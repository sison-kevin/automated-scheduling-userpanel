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
    // Attempt to load Composer autoload (safe): vendor/autoload.php should be
    // at project_root/vendor/autoload.php when this file is at app/helpers.
    $projectRoot = dirname(__DIR__, 2);
    $autoload = $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    if (file_exists($autoload)) {
        require_once $autoload;
    } else {
        error_log('Composer autoload not found: ' . $autoload);
        trigger_error('Email dependencies are not installed. Run composer install.', E_USER_WARNING);
        return false;
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

    // PHPMailer fallback: configure PHPMailer using environment variables
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();

        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USER') ?: '';
        $mail->Password = getenv('SMTP_PASS') ?: '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int)(getenv('SMTP_PORT') ?: 587);

        $from = getenv('SMTP_USER') ?: $mail->Username;
        $mail->setFrom($from ?: 'no-reply@example.com', 'User Verification');

        $mail->addAddress($email);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = 'Your verification code is: ' . $verificationCode;
        $mail->AltBody = 'Your verification code is: ' . $verificationCode;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email failed: ' . ($mail->ErrorInfo ?? $e->getMessage()));
        return false;
    }
}
