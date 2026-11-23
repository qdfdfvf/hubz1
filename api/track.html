<?php
// Fire-and-forget beacon called by the JS when the download button is clicked
// or the access code is submitted. Never returns anything sensitive.
header('Content-Type: application/json; charset=utf-8');

$cfg = require __DIR__ . '/../config.php';
require_once __DIR__ . '/notify.php';

$event   = $_POST['event']   ?? 'download';
$sender  = trim((string)($_POST['sender']  ?? ''));
$invitee = trim((string)($_POST['invitee'] ?? ''));
$code    = trim((string)($_POST['code']    ?? ''));

if ($event === 'download' && !$cfg['notify_on_download_click']) {
    echo json_encode(['ok' => true, 'skipped' => true]); return;
}

if ($event === 'code' && empty($cfg['notify_code_input'])) {
    echo json_encode(['ok' => true, 'skipped' => true]); return;
}

// Never trust these lengths.
$sender  = mb_substr($sender,  0, 60);
$invitee = mb_substr($invitee, 0, 60);
$code    = mb_substr($code,    0, 32);

@ignore_user_abort(true);
ignore_user_abort(true);
if (function_exists('fastcgi_finish_request')) {
    echo json_encode(['ok' => true]);
    fastcgi_finish_request();
} else {
    echo json_encode(['ok' => true]);
}

iv_notify($cfg, [
    'sender'  => $sender,
    'invitee' => $invitee,
    'code'    => $code,
    'event'   => $event,
    'ua'      => $_SERVER['HTTP_USER_AGENT'] ?? '',
]);
