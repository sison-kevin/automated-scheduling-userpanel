<?php
header('Content-Type: text/plain; charset=utf-8');
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

function show($name) {
    $v = getenv($name);
    $s = $v === false ? '(not set)' : $v;
    echo "$name: $s\n";
}

show('EMAIL_USER');
show('EMAIL_PASS');
show('EMAIL_FROM');
show('EMAIL_FROM_NAME');

echo "\n".$_SERVER['SERVER_NAME'] . " environment variables containing 'EMAIL':\n";
foreach (
    array_filter(array_keys($_SERVER), function($k){ return stripos($k,'EMAIL') !== false; })
    as $k
) {
    echo "$k=" . (isset($_SERVER[$k]) ? $_SERVER[$k] : '(not set)') . "\n";
}

if (function_exists('apache_getenv')) {
    echo "\napache_getenv() values:\n";
    echo "EMAIL_USER=" . (apache_getenv('EMAIL_USER') ?: '(not set)') . "\n";
}

echo "\nNotes:\n";
echo "- If values are (not set) here, Apache did not pass them to PHP.\n";
echo "- SetEnv affects the Apache process (not the CLI). Use php CLI only for CLI env checks.\n";
echo "\nRemove this file when done.\n";

?>
