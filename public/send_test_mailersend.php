<?php
header('Content-Type: text/plain; charset=utf-8');

// Use the centralized email helper which prefers API, then SMTP, then PHPMailer
$helper = __DIR__ . '/../app/helpers/email_helper.php';
if (! file_exists($helper)) {
    echo "email_helper not found at $helper\n";
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
echo "MAILERSEND_API_KEY: " . (getenv('MAILERSEND_API_KEY') ? '(present)' : '(not set)') . "\n";
echo "MAILERSEND_SMTP_USER: " . (getenv('MAILERSEND_SMTP_USER') ?: '(not set)') . "\n";
echo "EMAIL_FROM: " . (getenv('EMAIL_FROM') ?: '(not set)') . "\n";

$code = bin2hex(random_bytes(3));
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
    echo "Result: FAILURE - check server logs for details\n";
}

echo "Done.\n";

?>
