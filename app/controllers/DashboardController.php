<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class DashboardController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();

        $this->db = $this->call->database();

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

            if (!$this->db) {
                throw new Exception("⚠️ Database core file not found in any known path.");
            }
        }
    }

   public function index()
{
    // Ensure session is started
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . site_url('login'));
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // ✅ Fetch upcoming appointments (NOT cancelled, future/today dates only)
    $stmt = $this->db->raw("
        SELECT 
            a.id,
            a.appointment_date,
            a.appointment_time,
            p.name AS pet_name,
            s.service_name AS service,
            v.name AS veterinarian,
            a.status
        FROM appointments AS a
        LEFT JOIN pets AS p ON a.pet_id = p.id
        LEFT JOIN services AS s ON a.service_id = s.id
        LEFT JOIN veterinarians AS v ON a.vet_id = v.id
        WHERE a.user_id = ?
        AND a.status != 'Cancelled'
        AND a.appointment_date >= CURDATE()
        ORDER BY a.appointment_date ASC
        LIMIT 5
    ", [$user_id]);

    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Count pets
    $stmt2 = $this->db->raw("SELECT COUNT(*) AS total FROM pets WHERE user_id = ?", [$user_id]);
    $data = $stmt2->fetch(PDO::FETCH_ASSOC);
    $petsCount = $data['total'] ?? 0;

    // ✅ Render dashboard view
    $this->call->view('user_dashboard', [
        'appointments' => $appointments,
        'petsCount'    => $petsCount
    ]);
}


}
