<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class PetsController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();

        // ✅ Try to initialize LavaLust database
        $this->db = $this->call->database();

        // 🔹 Fallback: try alternative paths
        if (!$this->db) {
            // Check all known LavaLust v4+ core DB paths
            $possiblePaths = [
                __DIR__ . '/../../scheme/database/Database.php',
                __DIR__ . '/../../core/Database.php',
                __DIR__ . '/../../system/core/Database.php'
            ];

            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    $this->db = new Database();
                    break;
                }
            }

            // If still not found
            if (!$this->db) {
                throw new Exception("⚠️ Database core file not found in any known path.");
            }
        }
    }

    // ✅ Display user's pets
    public function index()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . site_url('login'));
            exit;
        }

        // ✅ Fetch all pets for the logged-in user
        $data['pets'] = $this->db->table('pets')
                                 ->where('user_id', $_SESSION['user_id'])
                                 ->get_all();

        $this->call->view('user_pets', $data);
    }

    // ✅ Add new pet
    public function add()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . site_url('login'));
        exit;
    }

    // Handle photo upload
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../app/uploads/pets/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Sanitize original filename
            $originalName = pathinfo($_FILES['photo']['name'], PATHINFO_FILENAME);
            $originalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
            $fileName = time() . '_' . substr($originalName, 0, 20) . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $photoPath = 'app/uploads/pets/' . $fileName;
                error_log("Photo uploaded successfully: $photoPath");
            } else {
                error_log("Failed to move uploaded file to: $targetPath");
            }
        } else {
            error_log("Invalid file extension: $fileExtension");
        }
    } else {
        if (isset($_FILES['photo'])) {
            error_log("File upload error: " . $_FILES['photo']['error']);
        }
    }

    // Calculate age automatically
    $birthdate = $_POST['birthdate'] ?? null;
    $age = 0;
    
    if (!empty($birthdate)) {
        try {
            $birthDateObj = new DateTime($birthdate);
            $today = new DateTime();
            $interval = $today->diff($birthDateObj);
            
            // Format age nicely: "X years Y months" or just months if under 1 year
            if ($interval->y > 0) {
                $age = $interval->y . ' year' . ($interval->y > 1 ? 's' : '');
                if ($interval->m > 0) {
                    $age .= ' ' . $interval->m . ' month' . ($interval->m > 1 ? 's' : '');
                }
            } else if ($interval->m > 0) {
                $age = $interval->m . ' month' . ($interval->m > 1 ? 's' : '');
            } else {
                $age = $interval->d . ' day' . ($interval->d > 1 ? 's' : '');
            }
        } catch (Exception $e) {
            $age = 'Unknown';
        }
    } else {
        $age = 'Unknown';
    }

    $petData = [
        'user_id'         => $_SESSION['user_id'],
        'name'            => $_POST['name'] ?? '',
        'species'         => $_POST['species'] ?? '',
        'breed'           => $_POST['breed'] ?? '',
        'birthdate'       => $birthdate,
        'age'             => $age,
        'vaccinated'      => isset($_POST['vaccinated']) ? 1 : 0,
        'medical_history' => $_POST['medical_history'] ?? '',
        'photo'           => $photoPath,
        'created_at'      => date('Y-m-d H:i:s')
    ];

    error_log("DEBUG: About to insert pet data: " . print_r($petData, true));
    $result = $this->db->table('pets')->insert($petData);
    error_log("DEBUG: Insert result: " . print_r($result, true));

   header('Location: ' . site_url('pets'));
    exit;

}

  public function update()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (!isset($_SESSION['user_id'])) {
       header('Location: ' . site_url('login'));
        exit;
    }

    $id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    // Get existing pet data
    $existingPet = $this->db->table('pets')
                            ->where('id', $id)
                            ->where('user_id', $user_id)
                            ->get();

    // Handle photo upload
    $photoPath = $existingPet['photo'] ?? null; // Keep existing photo by default
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../app/uploads/pets/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Sanitize original filename
            $originalName = pathinfo($_FILES['photo']['name'], PATHINFO_FILENAME);
            $originalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
            $fileName = time() . '_' . substr($originalName, 0, 20) . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                // Delete old photo if exists
                if ($photoPath && file_exists(__DIR__ . '/../../' . $photoPath)) {
                    unlink(__DIR__ . '/../../' . $photoPath);
                }
                $photoPath = 'app/uploads/pets/' . $fileName;
                error_log("Photo updated successfully: $photoPath");
            } else {
                error_log("Failed to move uploaded file to: $targetPath");
            }
        } else {
            error_log("Invalid file extension: $fileExtension");
        }
    } else {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            error_log("File upload error: " . $_FILES['photo']['error']);
        }
    }

    // Recalculate age based on birthdate
    $birthdate = $_POST['birthdate'] ?? null;
    $age = 0;
    
    if (!empty($birthdate)) {
        try {
            $birthDateObj = new DateTime($birthdate);
            $today = new DateTime();
            $interval = $today->diff($birthDateObj);
            
            // Format age nicely: "X years Y months" or just months if under 1 year
            if ($interval->y > 0) {
                $age = $interval->y . ' year' . ($interval->y > 1 ? 's' : '');
                if ($interval->m > 0) {
                    $age .= ' ' . $interval->m . ' month' . ($interval->m > 1 ? 's' : '');
                }
            } else if ($interval->m > 0) {
                $age = $interval->m . ' month' . ($interval->m > 1 ? 's' : '');
            } else {
                $age = $interval->d . ' day' . ($interval->d > 1 ? 's' : '');
            }
        } catch (Exception $e) {
            $age = 'Unknown';
        }
    } else {
        $age = 'Unknown';
    }

    // ✅ Update pet record
    $this->db->table('pets')
             ->where('id', $id)
             ->where('user_id', $user_id)
             ->update([
                 'name'            => $_POST['name'],
                 'species'         => $_POST['species'] ?? '',
                 'breed'           => $_POST['breed'],
                 'birthdate'       => $birthdate,
                 'age'             => $age,
                 'vaccinated'      => isset($_POST['vaccinated']) ? 1 : 0,
                 'medical_history' => $_POST['medical_history'] ?? '',
                 'photo'           => $photoPath,
                 'updated_at'      => date('Y-m-d H:i:s')
             ]);

     header('Location: ' . site_url('pets'));
    exit;

}

public function qr($id)
{
    // Enable error display for debugging
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    // Log the request
    $logFile = __DIR__ . '/../../writable/qr_debug.log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "QR requested for ID: $id\n", FILE_APPEND);
    
    // Clean any output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    try {
        // Include composer autoload
        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            throw new \Exception("Autoload not found at: " . $autoloadPath);
        }
        require_once $autoloadPath;
        file_put_contents($logFile, "Autoload loaded\n", FILE_APPEND);

        // Check if class exists
        if (!class_exists('\chillerlan\QRCode\QRCode')) {
            throw new \Exception("QRCode class not found");
        }
        file_put_contents($logFile, "QRCode class exists\n", FILE_APPEND);

        // Load URL helper
        $this->call->helper('url');
        
        // Generate URL to pet view page
        $petUrl = site_url("pets/view/" . intval($id));
        file_put_contents($logFile, "Pet URL: $petUrl\n", FILE_APPEND);

        // Generate QR code. Prefer PNG (GD) when available, otherwise fall
        // back to SVG so the QR still renders on environments without ext-gd.
        $gdAvailable = extension_loaded('gd') || function_exists('imagecreatetruecolor');

        if ($gdAvailable) {
            $options = new \chillerlan\QRCode\QROptions([
                'version'      => 5,
                'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_L,
                'scale'        => 10,
                'imageBase64'  => false,
            ]);

            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $qrData = $qrcode->render($petUrl);

            file_put_contents($logFile, "QR generated (PNG): " . strlen($qrData) . " bytes\n", FILE_APPEND);

            header('Content-Type: image/png');
            header('Content-Length: ' . strlen($qrData));
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            echo $qrData;
            exit();
        }

        // Fallback to SVG markup (does not require GD)
        $options = new \chillerlan\QRCode\QROptions([
            'version'      => 5,
            'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_L,
            'scale'        => 4,
            'imageBase64'  => false,
        ]);

        $qrcode = new \chillerlan\QRCode\QRCode($options);
        $svg = $qrcode->render($petUrl);
        file_put_contents($logFile, "QR generated (SVG): " . strlen($svg) . " bytes\n", FILE_APPEND);

        header('Content-Type: image/svg+xml');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        echo $svg;
        exit();

    } catch (\Throwable $e) {
        file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
        
        // Create a simple error image using GD
        header('Content-Type: image/png');
        $im = imagecreatetruecolor(200, 200);
        $bg = imagecolorallocate($im, 240, 240, 240);
        $text = imagecolorallocate($im, 100, 100, 100);
        imagefill($im, 0, 0, $bg);
        imagestring($im, 3, 50, 90, 'QR Error', $text);
        $msg = substr($e->getMessage(), 0, 25);
        imagestring($im, 2, 20, 110, $msg, $text);
        imagepng($im);
        imagedestroy($im);
        exit();
    }
}

public function view($id)
{
    $this->call->helper('url');

    // ✅ Fetch pet record from database
    $pet = $this->db->table('pets')->where('id', intval($id))->get();

    if (!$pet) {
        echo "<h2>Pet not found.</h2>";
        return;
    }

    // ✅ Load a view to display the pet details
    $data['pet'] = $pet;
    $this->call->view('pet_view', $data);
}

public function delete($id)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . site_url('login'));
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // ✅ Delete pet record
    $this->db->table('pets')
             ->where('id', $id)
             ->where('user_id', $user_id)
             ->delete();

    header('Location: ' . site_url('pets'));
    exit;
}

public function downloadQr($id)
{
    // Clean any output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    try {
        // Include composer autoload
        require_once __DIR__ . '/../../vendor/autoload.php';

        // Load URL helper
        $this->call->helper('url');
        
        // Generate URL to pet view page
        $petUrl = site_url("pets/view/" . intval($id));

        // Generate QR code for download. Prefer PNG (GD) when available,
        // otherwise provide an SVG download.
        $gdAvailable = extension_loaded('gd') || function_exists('imagecreatetruecolor');

        if ($gdAvailable) {
            $options = new \chillerlan\QRCode\QROptions([
                'version'      => 5,
                'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_L,
                'scale'        => 10,
                'imageBase64'  => false,
            ]);

            $qrcode = new \chillerlan\QRCode\QRCode($options);

            header('Content-Type: image/png');
            header('Content-Disposition: attachment; filename="pet_' . $id . '_qr.png"');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            echo $qrcode->render($petUrl);
            exit();
        }

        // Fallback: SVG download
        $options = new \chillerlan\QRCode\QROptions([
            'version'      => 5,
            'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_L,
            'scale'        => 4,
            'imageBase64'  => false,
        ]);

        $qrcode = new \chillerlan\QRCode\QRCode($options);
        $svg = $qrcode->render($petUrl);

        header('Content-Type: image/svg+xml');
        header('Content-Disposition: attachment; filename="pet_' . $id . '_qr.svg"');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        echo $svg;
        exit();

    } catch (\Throwable $e) {
        header('Content-Type: text/plain');
        http_response_code(500);
        echo "Download Error: " . $e->getMessage();
        exit();
    }
}

}
