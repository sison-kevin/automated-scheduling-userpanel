<?php
// CLI diagnostic to test MailerSend SMTP STARTTLS + AUTH LOGIN
// Usage: php tools/test_mailersend_auth.php

$host = getenv('MAILERSEND_SMTP_HOST') ?: getenv('EMAIL_SMTP_HOST') ?: 'smtp.mailersend.net';
$port = getenv('MAILERSEND_SMTP_PORT') ?: 587;
$user = getenv('MAILERSEND_SMTP_USER') ?: getenv('EMAIL_USER');
$pass = getenv('MAILERSEND_SMTP_PASS') ?: getenv('EMAIL_PASS');

echo "MailerSend SMTP auth test\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Username present: " . ($user ? 'YES' : 'NO') . "\n";
echo "Password present: " . ($pass ? 'YES' : 'NO') . "\n";

if (!$user || !$pass) {
    echo "ERROR: Credentials missing. Set MAILERSEND_SMTP_USER and MAILERSEND_SMTP_PASS in Apache/CLI environment.\n";
    exit(2);
}

$timeout = 10;
$remote = sprintf('%s:%d', $host, $port);
echo "Connecting to $remote ...\n";

$ctx = stream_context_create();
$fp = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $ctx);
if (!$fp) {
    echo "Connection failed: $errno - $errstr\n";
    exit(3);
}

stream_set_timeout($fp, $timeout);

function getresp($fp) {
    $s = '';
    while (($line = fgets($fp, 515)) !== false) {
        $s .= rtrim($line, "\r\n") . "\n";
        // multiline responses have '-' after the code
        if (preg_match('/^[0-9]{3} /', $line)) break;
    }
    return $s;
}

echo "S: " . getresp($fp);

fwrite($fp, "EHLO localhost\r\n");
echo "C: EHLO localhost\n";
echo "S: " . getresp($fp);

// Try STARTTLS if offered
fwrite($fp, "STARTTLS\r\n");
echo "C: STARTTLS\n";
$resp = getresp($fp);
echo "S: $resp";

if (strpos($resp, '220') === 0) {
    echo "Enabling crypto (TLS)...\n";
    $ok = stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    if ($ok !== true) {
        echo "ERROR: Failed to enable TLS (stream_socket_enable_crypto returned: ".var_export($ok, true).")\n";
        fclose($fp);
        exit(4);
    }
    // EHLO again
    fwrite($fp, "EHLO localhost\r\n");
    echo "C: EHLO localhost\n";
    echo "S: " . getresp($fp);
}

// AUTH LOGIN
fwrite($fp, "AUTH LOGIN\r\n");
echo "C: AUTH LOGIN\n";
$resp = getresp($fp);
echo "S: $resp";

if (strpos($resp, '334') !== 0) {
    echo "ERROR: Server did not prompt for username/password (expected 334).\n";
    fclose($fp);
    exit(5);
}

fwrite($fp, base64_encode($user) . "\r\n");
echo "C: <base64 user>\n";
echo "S: " . getresp($fp);

fwrite($fp, base64_encode($pass) . "\r\n");
echo "C: <base64 pass>\n";
$resp = getresp($fp);
echo "S: $resp";

if (strpos($resp, '235') === 0) {
    echo "Authentication SUCCESS (235).\n";
    fclose($fp);
    exit(0);
} else {
    echo "Authentication FAILED — server response above. Check credentials, correct SMTP host/port, and account settings.\n";
    fclose($fp);
    exit(6);
}
