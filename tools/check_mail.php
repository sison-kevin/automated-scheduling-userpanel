<?php
// Simple diagnostic for mail setup (does NOT send email)
echo "Checking Composer autoload...\n";
$autoload = file_exists(__DIR__ . '/../vendor/autoload.php') ? 'yes' : 'no';
echo "vendor/autoload.php exists: $autoload\n";
if ($autoload === 'yes') {
    require_once __DIR__ . '/../vendor/autoload.php';
}
$phpexists = class_exists('PHPMailer\\PHPMailer\\PHPMailer') ? 'yes' : 'no';
echo "PHPMailer class available: $phpexists\n";
$emailUser = getenv('EMAIL_USER') ?: '(not set)';
$emailPass = getenv('EMAIL_PASS') ? '***SET***' : '(not set)';
$from = getenv('EMAIL_FROM') ?: '(not set)';
$fromName = getenv('EMAIL_FROM_NAME') ?: '(not set)';
echo "EMAIL_USER: $emailUser\n";
echo "EMAIL_PASS: $emailPass\n";
echo "EMAIL_FROM: $from\n";
echo "EMAIL_FROM_NAME: $fromName\n";
if ($autoload === 'yes' && $phpexists === 'yes' && getenv('EMAIL_USER') && getenv('EMAIL_PASS')) {
    echo "Quick check: configuration looks present. To fully verify SMTP you need to run a send test (will attempt to send an email).\n";
} else {
    echo "Quick check: missing items detected (see above).\n";
}
