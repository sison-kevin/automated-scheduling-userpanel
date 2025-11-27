<?php
/**
 * email_helper.php
 *
 * Safe email helper that prefers the reusable MailService when available.
 * - Avoids hardcoding credentials; reads from environment variables via getenv().
 * - Attempts to load Composer's autoload.php safely and produces a clear error
 *   message if dependencies (PHPMailer) are not installed.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($email, $verificationCode)
{
    // Attempt to load Composer autoload from likely locations. This is permissive
    // so the helper works whether executed from CLI, Apache, or other entrypoints.
    $projectRoot = dirname(__DIR__, 2);
    $candidates = [
        $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
        __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
        __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
    ];

    $loaded = false;
    foreach ($candidates as $autoload) {
        if (file_exists($autoload)) {
            require_once $autoload;
            $loaded = true;
            break;
        }
    }

    if (! $loaded) {
        error_log('Composer autoload not found in expected locations: ' . implode(', ', $candidates));
        trigger_error('Email dependencies are not installed. Run composer install.', E_USER_WARNING);
        return false;
    }

    // Check whether PHPMailer is available after autoload. If it's missing
    // we'll still attempt to send using PHP's native mail() function as a
    // last-resort fallback so the app doesn't fatal out in production.
    $phpmailerAvailable = class_exists('\PHPMailer\\PHPMailer\\PHPMailer');
    if (! $phpmailerAvailable) {
        error_log('PHPMailer classes not found after requiring vendor/autoload.php; falling back to mail()');
    }

    // If the reusable MailService exists, prefer it (keeps code DRY)
    if (class_exists('\App\\Helpers\\MailService')) {
        try {
            $mailer = new \App\Helpers\MailService();
            $subject = 'Your Verification Code';
            $html = '<p>Your verification code is: <strong>' . htmlentities((string)$verificationCode) . '</strong></p>';
            $text = 'Your verification code is: ' . $verificationCode;
            return $mailer->sendEmail($email, $subject, $html, $text, null, 'User Verification');
        } catch (\Throwable $t) {
            error_log('MailService error: ' . $t->getMessage());
            // fall through to PHPMailer fallback
        }
    }

    // If PHPMailer is available, use it. Otherwise, fall back to PHP's
    // built-in mail() function (best-effort; less reliable than SMTP).
    if ($phpmailerAvailable) {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();

            $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USER') ?: '';
            $mail->Password = getenv('SMTP_PASS') ?: '';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int)(getenv('SMTP_PORT') ?: 587);

            $from = getenv('SMTP_USER') ?: $mail->Username;
            $mail->setFrom($from ?: 'no-reply@example.com', 'User Verification');

            $mail->addAddress($email);
            $mail->Subject = 'Your Verification Code';
            $mail->Body = 'Your verification code is: ' . $verificationCode;
            $mail->AltBody = 'Your verification code is: ' . $verificationCode;

            $mail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('Email failed (PHPMailer): ' . ($mail->ErrorInfo ?? $e->getMessage()));
            // fall through to attempt mail() below
        } catch (\Throwable $t) {
            error_log('Email failed (PHPMailer unexpected): ' . $t->getMessage());
            // fall through to attempt mail() below
        }
    }

    // Final fallback: use PHP's mail(). This requires a working MTA on the
    // server (sendmail/postfix). It's not as reliable as authenticated SMTP,
    // but it prevents a hard failure when PHPMailer isn't installed.
    $subject = 'Your Verification Code';
    $body = 'Your verification code is: ' . $verificationCode;
    $fromHeader = getenv('SMTP_USER') ?: 'no-reply@example.com';
    $headers = [];
    $headers[] = 'From: ' . $fromHeader;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/plain; charset=UTF-8';

    $ok = @mail($email, $subject, $body, implode("\r\n", $headers));
    if (! $ok) {
        error_log('mail() fallback failed for: ' . $email);
        return false;
    }

    return true;
}
