<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class VeterinariansController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();

        // ✅ Try to initialize LavaLust database
        $this->db = $this->call->database();

        // 🔹 Fallback: try alternative paths if the first call fails
        if (!$this->db) {
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

            // ⚠️ Still not found — throw clear exception
            if (!$this->db) {
                throw new Exception("⚠️ Database core file not found in any known path.");
            }
        }
    }

   public function index()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . site_url('login'));
            exit;
        }

        // ✅ Fetch all veterinarians from DB
        $data['veterinarians'] = $this->db->table('veterinarians')->get_all();

        $this->call->view('veterinarians', $data);
    }
}
