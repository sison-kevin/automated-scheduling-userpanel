<?php
// Simple test to call sendVerificationEmail()
require_once __DIR__ . '/../app/helpers/email_helper.php';

$to = $argv[1] ?? null;
if (! $to) {
    echo "Usage: php tools/send_test_email.php you@example.com\n";
    exit(1);
}

$code = rand(100000, 999999);
$ok = sendVerificationEmail($to, $code);
if ($ok) {
    echo "sendVerificationEmail returned true — check inbox.\n";
} else {
    echo "sendVerificationEmail returned false — check logs (runtime/logs or PHP error log).\n";
}
