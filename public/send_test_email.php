<?php
header('Content-Type: text/plain; charset=utf-8');

// Include the project's email helper which loads Composer/autoload and PHPMailer
$helper = __DIR__ . '/../app/helpers/email_helper.php';
if (! file_exists($helper)) {
    echo "email_helper.php not found at $helper\n";
    exit(1);
}
require_once $helper;

$to = isset($_GET['to']) && filter_var($_GET['to'], FILTER_VALIDATE_EMAIL) ? $_GET['to'] : (getenv('EMAIL_FROM') ?: getenv('EMAIL_USER'));
if (! $to) {
    echo "No recipient specified. Use ?to=you@domain or set EMAIL_FROM/EMAIL_USER.\n";
    exit(1);
}

echo "Recipient: $to\n";
echo "Using environment (Apache) values:\n";
echo "EMAIL_USER: " . (getenv('EMAIL_USER') ?: '(not set)') . "\n";
echo "EMAIL_FROM: " . (getenv('EMAIL_FROM') ?: '(not set)') . "\n";

$code = bin2hex(random_bytes(3)); // 6 hex chars
echo "Verification code: $code\n";

echo "Calling sendVerificationEmail()...\n";
$ok = false;
try {
    $ok = sendVerificationEmail($to, $code);
} catch (Throwable $e) {
    echo "Exception thrown: " . $e->getMessage() . "\n";
}

if ($ok) {
    echo "Result: SUCCESS\n";
} else {
    echo "Result: FAILURE - check Apache error log: C:\\xampp\\apache\\logs\\error.log\n";
}

echo "Done.\n";

?>
