<?php
declare(strict_types=1);

// Simple test script to send an email using the MailService class.
// Usage (browser): http://localhost/user/public/send_test_email.php?to=recipient@example.com

// Important: ensure Composer autoload is available and PHPMailer is installed via Composer.
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/helpers/email_helper.php';

header('Content-Type: application/json; charset=utf-8');

$to = isset($_GET['to']) && filter_var($_GET['to'], FILTER_VALIDATE_EMAIL) ? $_GET['to'] : 'test@example.com';

// Use the helper which will prefer MailService when available and fall back
// to PHPMailer configured via env vars if needed.
$ok = sendVerificationEmail($to, '123456');

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Email sent successfully']);
    exit(0);
}

echo json_encode(['success' => false, 'error' => 'Sending failed. Check server logs for details.']);
