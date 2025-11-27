<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AppointmentsController extends Controller
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

    // ✅ Display user appointments + pets + vets
   public function index()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . site_url('login'));
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // ✅ Fetch all pets for dropdown
    $pets = $this->db->table('pets')
                     ->where('user_id', $user_id)
                     ->get_all();

    // ✅ Fetch only active veterinarians
    $vets = $this->db->table('veterinarians')
                     ->where('is_active', 1)
                     ->get_all();

    // ✅ Fetch all services
    $services = $this->db->table('services')->get_all();

    // ✅ Fetch UPCOMING appointments (not cancelled, future or today's date)
    $upcomingAppointments = $this->db->table('appointments a')
        ->select('
            a.id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.remarks,
            p.name AS pet_name,
            v.name AS vet_name,
            s.service_name AS service_name
        ')
        ->join('veterinarians v', 'v.id = a.vet_id')
        ->join('services s', 's.id = a.service_id')
        ->join('pets p', 'p.id = a.pet_id')
        ->where('a.user_id', $user_id)
        ->where('a.status', '!=', 'Cancelled')
        ->where('a.appointment_date', '>=', date('Y-m-d'))
        ->order_by('a.appointment_date', 'ASC')
        ->get_all();

    // ✅ Fetch ALL past/cancelled appointments for history
    $allAppointments = $this->db->table('appointments a')
        ->select('
            a.id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.remarks,
            p.name AS pet_name,
            v.name AS vet_name,
            s.service_name AS service_name
        ')
        ->join('veterinarians v', 'v.id = a.vet_id')
        ->join('services s', 's.id = a.service_id')
        ->join('pets p', 'p.id = a.pet_id')
        ->where('a.user_id', $user_id)
        ->order_by('a.appointment_date', 'DESC')
        ->get_all();

    // Filter history appointments in PHP (cancelled OR past dates)
    $historyAppointments = [];
    $today = date('Y-m-d');
    if (is_array($allAppointments)) {
        foreach ($allAppointments as $apt) {
            if ($apt['status'] === 'Cancelled' || $apt['appointment_date'] < $today) {
                $historyAppointments[] = $apt;
            }
        }
    }

    // ✅ Load view
    $this->call->view('appointments', [
        'upcomingAppointments' => $upcomingAppointments,
        'historyAppointments' => $historyAppointments,
        'pets' => $pets,
        'vets' => $vets,
        'services' => $services
    ]);
}


    // ✅ Add new appointment
    public function book()
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . site_url('login'));
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $pet_id = $_POST['pet_id'] ?? null;
        $veterinarian_id = $_POST['veterinarian_id'] ?? null;
        $service = $_POST['service'] ?? '';
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';

        // DEBUG: Log what we received
        error_log("Booking appointment - Date: $date, Time: $time");

        // ✅ Validate appointment is not in the past
        $appointmentDateTime = strtotime($date . ' ' . $time);
        $currentDateTime = time();

        if ($appointmentDateTime < $currentDateTime) {
            $_SESSION['error'] = 'Cannot book appointments in the past. Please select a future date and time.';
            header('Location: ' . site_url('appointments'));
            exit;
        }

        // ✅ Validate time is in 30-minute intervals
        $timeParts = explode(':', $time);
        $minutes = isset($timeParts[1]) ? (int)$timeParts[1] : 0;
        if ($minutes !== 0 && $minutes !== 30) {
            $_SESSION['error'] = 'Appointments must be booked in 30-minute intervals (e.g., 8:00, 8:30).';
            header('Location: ' . site_url('appointments'));
            exit;
        }

        // ✅ Format time to include seconds for database storage
        $formattedTime = $time . ':00';
        
        // DEBUG: Log formatted time
        error_log("Formatted time for database: $formattedTime");

        // ✅ Check for conflicting appointments (same vet, date, time)
        $existingAppointment = $this->db->table('appointments')
            ->where('vet_id', $veterinarian_id)
            ->where('appointment_date', $date)
            ->where('appointment_time', $formattedTime)
            ->where('status', '!=', 'Cancelled')
            ->get();

        if ($existingAppointment) {
            $_SESSION['error'] = 'This time slot is already booked. Please select another time.';
            header('Location: ' . site_url('appointments'));
            exit;
        }

        // ✅ Format time to include seconds (HH:MM:SS)
        $formattedTime = $time . ':00';
        
        // DEBUG: Check what was actually inserted
        error_log("About to insert - Date: $date, Time: $formattedTime");

        // ✅ Insert appointment
        $insertData = [
            'user_id' => $_SESSION['user_id'],
            'pet_id' => $_POST['pet_id'],
            'vet_id' => $_POST['veterinarian_id'],
            'service_id' => $_POST['service'],
            'appointment_date' => $_POST['date'],
            'appointment_time' => $formattedTime,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        error_log("Insert data: " . json_encode($insertData));
        
        $this->db->table('appointments')->insert($insertData);

        $_SESSION['success'] = 'Appointment booked successfully!';
        header('Location: ' . site_url('appointments'));
        exit;
    }

    // ✅ Cancel appointment
    public function cancel($id)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . site_url('login'));
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Check if appointment exists and belongs to user
        $appointment = $this->db->table('appointments')
                                ->where('id', $id)
                                ->where('user_id', $user_id)
                                ->get();

        if (!$appointment) {
            $_SESSION['error'] = 'Appointment not found or access denied.';
            header('Location: ' . site_url('appointments'));
            exit;
        }

        // Check if appointment is already cancelled
        if ($appointment['status'] === 'Cancelled') {
            $_SESSION['error'] = 'This appointment is already cancelled.';
            header('Location: ' . site_url('appointments'));
            exit;
        }

        // Update appointment status to Cancelled
        $updated = $this->db->table('appointments')
                           ->where('id', $id)
                           ->where('user_id', $user_id)
                           ->update([
                               'status' => 'Cancelled'
                           ]);

        if ($updated) {
            $_SESSION['success'] = 'Appointment cancelled successfully.';
        } else {
            $_SESSION['error'] = 'Failed to cancel appointment.';
        }

        header('Location: ' . site_url('appointments'));
        exit;
    }

    // ✅ AJAX endpoint: Get booked time slots for a veterinarian on a specific date
    public function getBookedSlots()
    {
        header('Content-Type: application/json');
        
        $date = $_GET['date'] ?? '';
        $vet_id = $_GET['vet_id'] ?? '';

        error_log("getBookedSlots called - Date: $date, Vet ID: $vet_id");

        if (!$date || !$vet_id) {
            echo json_encode(['bookedSlots' => []]);
            exit;
        }

        // Fetch all booked appointments for this vet on this date (excluding cancelled)
        $appointments = $this->db->table('appointments')
            ->select('appointment_time')
            ->where('vet_id', $vet_id)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'Cancelled')
            ->get_all();

        error_log("Found " . count($appointments) . " appointments");

        $bookedSlots = [];
        if (is_array($appointments)) {
            foreach ($appointments as $apt) {
                // Ensure time format is HH:MM (strip seconds if present)
                $time = $apt['appointment_time'];
                if (strlen($time) > 5) {
                    $time = substr($time, 0, 5);
                }
                error_log("Booking slot: $time");
                $bookedSlots[] = $time;
                
                // Don't block the next slot - each appointment is 30 mins
                // so if 8:00 is booked (8:00-8:30), 8:30 is still available
            }
        }

        // Remove duplicates
        $bookedSlots = array_unique($bookedSlots);

        error_log("Returning booked slots: " . json_encode(array_values($bookedSlots)));
        echo json_encode(['bookedSlots' => array_values($bookedSlots)]);
        exit;
    }
}
