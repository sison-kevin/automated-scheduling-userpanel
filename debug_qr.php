<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing QR Code Generation...<br><br>";

// Test 1: Check if vendor autoload exists
$autoloadPath = __DIR__ . '/vendor/autoload.php';
echo "1. Autoload path: " . $autoloadPath . "<br>";
echo "   Exists: " . (file_exists($autoloadPath) ? "YES" : "NO") . "<br><br>";

if (!file_exists($autoloadPath)) {
    die("Autoload not found!");
}

require_once $autoloadPath;

// Test 2: Check if class exists
$classExists = class_exists('chillerlan\QRCode\QRCode');
echo "2. QRCode class exists: " . ($classExists ? "YES" : "NO") . "<br><br>";

if (!$classExists) {
    die("QRCode class not found!");
}

// Test 3: Try to generate QR code
try {
    echo "3. Attempting to generate QR code...<br>";
    
    $options = new \chillerlan\QRCode\QROptions([
        'version'      => 5,
        'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_L,
        'scale'        => 10,
    ]);

    $qrcode = new \chillerlan\QRCode\QRCode($options);
    $qrData = $qrcode->render('https://example.com/test');
    
    echo "   QR Generated successfully!<br>";
    echo "   Data length: " . strlen($qrData) . " bytes<br><br>";
    
    echo "4. Displaying QR Code:<br>";
    echo '<img src="data:image/png;base64,' . base64_encode($qrData) . '" alt="Test QR">';
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
