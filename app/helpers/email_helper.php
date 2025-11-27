<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($email, $verificationCode)
{
    require_once __DIR__ . '/../../vendor/autoload.php'; // ✅ Correct path for LavaLust

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kevinsison612@gmail.com'; // your Gmail
        $mail->Password = 'qhsi kvov iqxm fvzd';     // your App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('kevinsison612@gmail.com', 'User Verification');
        $mail->addAddress($email);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = "Your verification code is: $verificationCode";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email failed: ' . $mail->ErrorInfo);
        return false;
    }
}
