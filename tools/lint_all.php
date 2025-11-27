<?php
// Recursively lint all PHP files under the project root (LavaLust-dev-v4)
$root = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$errors = [];
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    if (strtolower($file->getExtension()) !== 'php') continue;
    $path = $file->getPathname();
    // Run php -l on the file
    $output = [];
    $return = 0;
    exec('php -l "' . $path . '" 2>&1', $output, $return);
    if ($return !== 0) {
        $errors[] = ['file' => $path, 'output' => implode(PHP_EOL, $output)];
        echo "Syntax error in: $path" . PHP_EOL;
        echo implode(PHP_EOL, $output) . PHP_EOL . PHP_EOL;
    }
}
if (empty($errors)) {
    echo "No syntax errors found in project.\n";
    exit(0);
}
exit(1);
