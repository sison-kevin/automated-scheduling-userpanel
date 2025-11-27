<?php
/**
 * MailerSend HTTP API helper
 * Requires: MAILERSEND_API_KEY env var
 * Provides: sendVerificationEmailMailerSendAPI($to, $verificationCode)
 */

function sendVerificationEmailMailerSendAPI($to, $verificationCode)
{
    $apiKey = getenv('MAILERSEND_API_KEY');
    if (! $apiKey) {
        error_log('[mailersend_api] MAILERSEND_API_KEY not set');
        return false;
    }

    $fromEmail = getenv('EMAIL_FROM') ?: (getenv('MAILERSEND_SMTP_USER') ?: null);
    $fromName = getenv('EMAIL_FROM_NAME') ?: 'User Verification';
    if (! $fromEmail) {
        error_log('[mailersend_api] From address not set (EMAIL_FROM or MAILERSEND_SMTP_USER)');
        return false;
    }

    $payload = [
        'from' => ['email' => $fromEmail, 'name' => $fromName],
        'to' => [['email' => $to]],
        'subject' => 'Your Verification Code',
        'text' => "Your verification code is: {$verificationCode}"
    ];

    error_log('[mailersend_api] Sending email via API: from=' . $fromEmail . ' to=' . $to . ' subject=' . $payload['subject']);

    $ch = curl_init('https://api.mailersend.com/v1/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false) {
        error_log('[mailersend_api] curl error: ' . $err);
        return false;
    }

    if ($code >= 200 && $code < 300) {
        error_log('[mailersend_api] API send succeeded HTTP ' . $code);
        return true;
    }

    // Log response body for debugging (do not log API key)
    error_log('[mailersend_api] API responded with HTTP ' . $code . ': ' . $resp);
    return false;
}
