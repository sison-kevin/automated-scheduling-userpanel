<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Try to include Composer's autoloader from a few likely locations.
$autoloadPaths = [
    (defined('ROOT_DIR') ? rtrim(ROOT_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : __DIR__ . '/../../') . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
];

$autoloadFound = false;
foreach ($autoloadPaths as $p) {
    if (file_exists($p)) {
        require_once $p;
        $autoloadFound = true;
        break;
    }
}

if (! $autoloadFound) {
    error_log('[email_helper] Composer autoload.php not found. Run `composer install` in project root.');
}

function sendVerificationEmail($email, $verificationCode)
{

    // If MailerSend SMTP env is provided prefer the MailerSend SMTP helper
    if (getenv('MAILERSEND_SMTP_USER')) {
        $ms = __DIR__ . '/mailersend_smtp.php';
        if (file_exists($ms)) {
            require_once $ms;
            if (function_exists('sendVerificationEmailMailerSend')) {
                $ok = sendVerificationEmailMailerSend($email, $verificationCode);
                if ($ok) {
                    return true;
                }
                // Log and fall back to PHPMailer using the same MailerSend SMTP settings
                error_log('[email_helper] MailerSend SMTP helper failed; falling back to PHPMailer with MailerSend SMTP settings.');
            }
        } else {
            error_log('[email_helper] MailerSend helper not found at ' . $ms);
        }
        // If helper not available or failed, continue to PHPMailer path below
    }

    // Ensure PHPMailer is available
    if (! class_exists(PHPMailer::class)) {
        error_log('[email_helper] PHPMailer class not available. Ensure dependencies are installed.');
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        // Prefer explicit MailerSend SMTP settings when available, otherwise default to gmail
        $mail->Host = getenv('MAILERSEND_SMTP_HOST') ?: getenv('EMAIL_SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        // Credentials must be provided via environment variables
        // Allow both EMAIL_* and MAILERSEND_* credentials
        $emailUser = getenv('EMAIL_USER') ?: getenv('MAILERSEND_SMTP_USER');
        $emailPass = getenv('EMAIL_PASS') ?: getenv('MAILERSEND_SMTP_PASS');
        if (empty($emailUser) || empty($emailPass)) {
            error_log('[email_helper] SMTP credentials missing. Set EMAIL_USER/EMAIL_PASS or MAILERSEND_SMTP_USER/MAILERSEND_SMTP_PASS.');
            return false;
        }
        $mail->Username = $emailUser;
        $mail->Password = $emailPass;
        $mail->SMTPSecure = 'tls';
        $mail->Port = getenv('MAILERSEND_SMTP_PORT') ?: getenv('EMAIL_SMTP_PORT') ?: 587;

        $from = getenv('EMAIL_FROM') ?: $emailUser;
        $fromName = getenv('EMAIL_FROM_NAME') ?: 'User Verification';
        $mail->setFrom($from, $fromName);
        $mail->addAddress($email);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = "Your verification code is: $verificationCode";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('[email_helper] Email failed: ' . ($mail->ErrorInfo ?? $e->getMessage()));
        return false;
    }
}
