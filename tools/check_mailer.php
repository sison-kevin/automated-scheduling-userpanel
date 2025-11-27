<?php
// tools/check_mailer.php
// Quick script to verify Composer autoload and the MailService class without shell quoting issues.

require __DIR__ . '/../vendor/autoload.php';

echo "PHP: " . PHP_VERSION . "\n";

echo "Composer autoload loaded from: ";
$autoload = realpath(__DIR__ . '/../vendor/autoload.php');
var_export($autoload);
echo "\n";

$exists = class_exists('\App\\Helpers\\MailService');
echo "App\\Helpers\\MailService exists: ";
var_export($exists);
echo "\n";

// If the class exists, show that we can instantiate it (but don't send mail).
if ($exists) {
    try {
        $m = new \App\Helpers\MailService();
        echo "MailService instantiated: yes\n";
        echo "Has sendEmail method: ";
        var_export(method_exists($m, 'sendEmail'));
        echo "\n";
    } catch (Throwable $t) {
        echo "MailService instantiation failed: " . $t->getMessage() . "\n";
    }
} else {
    echo "MailService not found — check PSR-4 mapping and files.\n";
}
