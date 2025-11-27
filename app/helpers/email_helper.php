<?php
/**
 * Central email helper — MailerSend-only implementation.
 * Preference order:
 * 1. MailerSend HTTP API (MAILERSEND_API_KEY)
 * 2. MailerSend SMTP helper (MAILERSEND_SMTP_USER / MAILERSEND_SMTP_PASS)
 * Returns true on success, false on failure and logs details to error_log().
 */

function sendVerificationEmail($email, $verificationCode)
{
    // 1) HTTP API
    if (getenv('MAILERSEND_API_KEY')) {
        $api = __DIR__ . '/mailersend_api.php';
        if (file_exists($api)) {
            require_once $api;
            if (function_exists('sendVerificationEmailMailerSendAPI')) {
                $ok = sendVerificationEmailMailerSendAPI($email, $verificationCode);
                if ($ok) return true;
                error_log('[email_helper] MailerSend API helper failed');
            } else {
                error_log('[email_helper] sendVerificationEmailMailerSendAPI not found in ' . $api);
            }
        } else {
            error_log('[email_helper] MailerSend API helper not found at ' . $api);
        }
    }

    // 2) SMTP helper
    if (getenv('MAILERSEND_SMTP_USER')) {
        $ms = __DIR__ . '/mailersend_smtp.php';
        if (file_exists($ms)) {
            require_once $ms;
            if (function_exists('sendVerificationEmailMailerSend')) {
                $ok = sendVerificationEmailMailerSend($email, $verificationCode);
                if ($ok) return true;
                error_log('[email_helper] MailerSend SMTP helper failed');
            } else {
                error_log('[email_helper] sendVerificationEmailMailerSend not found in ' . $ms);
            }
        } else {
            error_log('[email_helper] MailerSend SMTP helper not found at ' . $ms);
        }
    }

    error_log('[email_helper] No MailerSend configuration available (set MAILERSEND_API_KEY or MAILERSEND_SMTP_USER)');
    return false;
}
