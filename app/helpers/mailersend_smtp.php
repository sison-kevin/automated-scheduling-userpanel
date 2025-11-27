<?php
/**
 * Lightweight SMTP client for MailerSend (STARTTLS + AUTH LOGIN)
 *
 * Environment variables used (preferred):
 * - MAILERSEND_SMTP_HOST (default: smtp.mailersend.net)
 * - MAILERSEND_SMTP_PORT (default: 587)
 * - MAILERSEND_SMTP_USER
 * - MAILERSEND_SMTP_PASS
 * - EMAIL_FROM, EMAIL_FROM_NAME (optional)
 *
 * This file provides `sendVerificationEmailMailerSend($to, $code)` which
 * returns true on success or false on failure and logs errors via error_log().
 */

function smtp_read_response($fp)
{
    $data = '';
    while ($line = fgets($fp, 515)) {
        $data .= $line;
        // RFC: multi-line responses have '-' as 4th char, final line has space
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }
    return $data;
}

function smtp_send_command($fp, $cmd)
{
    fwrite($fp, $cmd);
    return smtp_read_response($fp);
}

function sendVerificationEmailMailerSend($to, $verificationCode)
{
    $host = getenv('MAILERSEND_SMTP_HOST') ?: 'smtp.mailersend.net';
    $port = getenv('MAILERSEND_SMTP_PORT') ?: 587;
    $user = getenv('MAILERSEND_SMTP_USER') ?: getenv('EMAIL_USER');
    $pass = getenv('MAILERSEND_SMTP_PASS') ?: getenv('EMAIL_PASS');

    if (! $user || ! $pass) {
        error_log('[mailersend_smtp] SMTP credentials missing. Set MAILERSEND_SMTP_USER and MAILERSEND_SMTP_PASS (or EMAIL_USER/EMAIL_PASS).');
        return false;
    }

    $from = getenv('EMAIL_FROM') ?: $user;
    $fromName = getenv('EMAIL_FROM_NAME') ?: 'User Verification';

    $subject = 'Your Verification Code';
    $body = "Your verification code is: $verificationCode";

    $socketAddress = "tcp://{$host}:{$port}";
    $fp = @stream_socket_client($socketAddress, $errno, $errstr, 30);
    if (! $fp) {
        error_log("[mailersend_smtp] Unable to connect to SMTP {$socketAddress}: {$errno} {$errstr}");
        // If MAILERSEND_API_KEY is available, try the HTTP API as a fallback
        $apiKey = getenv('MAILERSEND_API_KEY');
        if ($apiKey) {
            $api = __DIR__ . '/mailersend_api.php';
            if (file_exists($api)) {
                require_once $api;
                if (function_exists('sendVerificationEmailMailerSendAPI')) {
                    error_log('[mailersend_smtp] Falling back to MailerSend HTTP API due to SMTP connect failure');
                    return sendVerificationEmailMailerSendAPI($to, $verificationCode);
                }
            } else {
                error_log('[mailersend_smtp] MailerSend API helper not found at ' . $api);
            }
        }
        return false;
    }

    // Read banner
    $resp = smtp_read_response($fp);
    if (stripos($resp, '220') !== 0) {
        error_log('[mailersend_smtp] SMTP banner error: ' . trim($resp));
        fclose($fp);
        return false;
    }

    $localhost = 'localhost';
    $resp = smtp_send_command($fp, "EHLO {$localhost}\r\n");

    // Request STARTTLS
    $resp = smtp_send_command($fp, "STARTTLS\r\n");
    if (stripos($resp, '220') !== 0) {
        error_log('[mailersend_smtp] STARTTLS not supported/failed: ' . trim($resp));
        fclose($fp);
        return false;
    }

    // Enable crypto (TLS)
    $ok = stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    if (! $ok) {
        error_log('[mailersend_smtp] Failed enabling TLS via stream_socket_enable_crypto');
        fclose($fp);
        return false;
    }

    // EHLO again after TLS
    $resp = smtp_send_command($fp, "EHLO {$localhost}\r\n");

    // AUTH LOGIN
    $resp = smtp_send_command($fp, "AUTH LOGIN\r\n");
    if (stripos($resp, '334') !== 0) {
        error_log('[mailersend_smtp] AUTH LOGIN not accepted: ' . trim($resp));
        fclose($fp);
        return false;
    }
    $resp = smtp_send_command($fp, base64_encode($user) . "\r\n");
    $resp = smtp_send_command($fp, base64_encode($pass) . "\r\n");
    if (stripos($resp, '235') === false) {
        error_log('[mailersend_smtp] Authentication failed: ' . trim($resp));
        fclose($fp);
        return false;
    }

    // MAIL FROM
    $resp = smtp_send_command($fp, "MAIL FROM:<{$from}>\r\n");
    if (stripos($resp, '250') !== 0) {
        error_log('[mailersend_smtp] MAIL FROM rejected: ' . trim($resp));
        fclose($fp);
        return false;
    }

    // RCPT TO
    $resp = smtp_send_command($fp, "RCPT TO:<{$to}>\r\n");
    if (stripos($resp, '250') !== 0 && stripos($resp, '251') !== 0) {
        error_log('[mailersend_smtp] RCPT TO rejected: ' . trim($resp));
        fclose($fp);
        return false;
    }

    // DATA
    $resp = smtp_send_command($fp, "DATA\r\n");
    if (stripos($resp, '354') !== 0) {
        error_log('[mailersend_smtp] DATA command not accepted: ' . trim($resp));
        fclose($fp);
        return false;
    }

    // Build headers
    $headers = [];
    $headers[] = 'From: ' . ($fromName ? "{$fromName} <{$from}>" : $from);
    $headers[] = 'To: ' . $to;
    $headers[] = 'Subject: ' . $subject;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'Content-Transfer-Encoding: 7bit';

    $data = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";
    fwrite($fp, $data);
    $resp = smtp_read_response($fp);
    if (stripos($resp, '250') !== 0) {
        error_log('[mailersend_smtp] Message not accepted: ' . trim($resp));
        fclose($fp);
        return false;
    }

    // QUIT
    fwrite($fp, "QUIT\r\n");
    fclose($fp);

    return true;
}
