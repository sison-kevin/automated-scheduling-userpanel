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
    // Ensure PHPMailer is available
    if (! class_exists(PHPMailer::class)) {
        error_log('[email_helper] PHPMailer class not available. Ensure dependencies are installed.');
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        // Credentials: prefer environment variables, fall back to existing values
        $mail->Username = getenv('EMAIL_USER') ?: 'kevinsison612@gmail.com';
        $mail->Password = getenv('EMAIL_PASS') ?: 'qhsi kvov iqxm fvzd';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('kevinsison612@gmail.com', 'User Verification');
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
