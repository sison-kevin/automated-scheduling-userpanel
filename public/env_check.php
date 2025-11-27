<?php
// One-time diagnostic page to check Apache-visible environment variables.
// Do NOT paste any secret values into public chat. This page reports only presence and lengths.
header('Content-Type: text/plain');

function present($k) {
    $v = getenv($k);
    if ($v === false || $v === '') return '<empty>';
    return '<present> length=' . strlen($v);
}

echo "MAILERSEND_SMTP_USER: " . present('MAILERSEND_SMTP_USER') . PHP_EOL;
echo "MAILERSEND_SMTP_PASS: " . present('MAILERSEND_SMTP_PASS') . PHP_EOL;
echo "EMAIL_USER: " . present('EMAIL_USER') . PHP_EOL;
echo "EMAIL_PASS: " . present('EMAIL_PASS') . PHP_EOL;
echo "MAILERSEND_SMTP_HOST: " . (getenv('MAILERSEND_SMTP_HOST') ?: '<empty>') . PHP_EOL;
echo "MAILERSEND_SMTP_PORT: " . (getenv('MAILERSEND_SMTP_PORT') ?: '<empty>') . PHP_EOL;
echo "MAILERSEND_SMTP_EMAIL_FROM_NAME: " . (getenv('MAILERSEND_SMTP_EMAIL_FROM_NAME') ?: '<empty>') . PHP_EOL;
echo "SAPI: " . php_sapi_name() . PHP_EOL;
echo "PHP_VERSION: " . PHP_VERSION . PHP_EOL;

echo PHP_EOL . "NOTE: Remove this file after use to avoid exposing diagnostic endpoints." . PHP_EOL;
