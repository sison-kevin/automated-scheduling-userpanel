<?php
// Simple helper for Google reCAPTCHA verification
// Uses environment variables `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` if set,
// otherwise will attempt to read $GLOBALS['config'] entries.

function get_recaptcha_site_key() {
    $env = getenv('RECAPTCHA_SITE_KEY');
    if ($env !== false && $env !== '') return $env;
    // Prefer framework helper if available
    if (function_exists('config_item')) {
        $val = config_item('recaptcha_site_key');
        if ($val) return $val;
    }

    // Fallback: check global config array or object safely
    if (isset($GLOBALS['config'])) {
        if (is_array($GLOBALS['config']) && !empty($GLOBALS['config']['recaptcha_site_key'])) {
            return $GLOBALS['config']['recaptcha_site_key'];
        }
        if (is_object($GLOBALS['config']) && property_exists($GLOBALS['config'], 'recaptcha_site_key')) {
            return $GLOBALS['config']->recaptcha_site_key;
        }
    }
    return '';
}

function get_recaptcha_secret_key() {
    $env = getenv('RECAPTCHA_SECRET_KEY');
    if ($env !== false && $env !== '') return $env;
    if (function_exists('config_item')) {
        $val = config_item('recaptcha_secret_key');
        if ($val) return $val;
    }

    if (isset($GLOBALS['config'])) {
        if (is_array($GLOBALS['config']) && !empty($GLOBALS['config']['recaptcha_secret_key'])) {
            return $GLOBALS['config']['recaptcha_secret_key'];
        }
        if (is_object($GLOBALS['config']) && property_exists($GLOBALS['config'], 'recaptcha_secret_key')) {
            return $GLOBALS['config']->recaptcha_secret_key;
        }
    }
    return '';
}

function is_recaptcha_enabled() {
    return (get_recaptcha_site_key() !== '' && get_recaptcha_secret_key() !== '');
}

function verify_recaptcha($token) {
    if (!is_recaptcha_enabled()) {
        // If not configured, skip verification (avoid blocking development environments)
        return true;
    }

    if (empty($token)) return false;

    $secret = get_recaptcha_secret_key();
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = http_build_query([
        'secret' => $secret,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ]);

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $data,
            'timeout' => 5
        ]
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    if ($result === false) return false;

    $json = json_decode($result, true);
    return isset($json['success']) && $json['success'] === true;
}
