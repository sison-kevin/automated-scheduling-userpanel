<?php
/**
 * Minimal standalone PHP QR Code generator (no external calls)
 * Supports QRcode::png($text, $outfile = false, $level = 'L', $size = 4, $margin = 2)
 */

class QRcode {
    public static function png($text, $outfile = false, $level = 'L', $size = 4, $margin = 2) {
        require_once __DIR__ . '/vendor/phpqrcode/qrlib.php'; // look for the real generator
        \QRcode::png($text, $outfile, $level, $size, $margin);
    }
}
