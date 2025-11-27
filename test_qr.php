<?php
// Simple QR code test
require_once __DIR__ . '/vendor/autoload.php';

try {
    $options = new \chillerlan\QRCode\QROptions([
        'version'      => 5,
        'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_L,
        'scale'        => 8,
    ]);

    $qrcode = new \chillerlan\QRCode\QRCode($options);
    
    header('Content-Type: image/png');
    echo $qrcode->render('https://example.com/test');
    
} catch (Exception $e) {
    header('Content-Type: text/plain');
    echo "Error: " . $e->getMessage();
}
