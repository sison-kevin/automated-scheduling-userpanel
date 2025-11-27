<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/email_helper.php';


class AuthController extends Controller
{
    protected $userModel;

    public function __construct() {
        parent::__construct();
         $this->userModel = new UserModel();
         
    }

    // Registration
    public function register()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        $name  = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Check if email exists
        $existingUser = $this->userModel->getUserByEmail($email);
        if ($existingUser) {
            $_SESSION['error'] = 'Email already registered! Please login instead.';
            $_SESSION['form_type'] = 'register';
            header('Location: ' . site_url('/'));
            exit;
        }

        // Generate verification code
        $code = rand(100000, 999999);

        // Insert user
        $this->userModel->insertUser([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'verification_code' => $code,
            'is_verified' => 0
        ]);

        // Send verification email
        if (sendVerificationEmail($email, $code)) {
            $_SESSION['success'] = 'Registration successful! Please check your email to verify your account.';
            $this->call->view('verify', ['email' => $email]);
        } else {
            $_SESSION['error'] = 'Registration successful but failed to send email. Contact support.';
            $_SESSION['form_type'] = 'register';
            header('Location: ' . site_url('/'));
            exit;
        }
    }

    // Email verification
   public function verify()
{
    // Use POST if form submitted, otherwise GET from URL
    $email = $_POST['email'] ?? $_GET['email'] ?? null;
    $code  = $_POST['code'] ?? $_GET['code'] ?? null;

    if (!$email || !$code) {
        echo "Invalid verification link.";
        return;
    }

    $success = $this->userModel->verifyUser($email, $code);
    if ($success) {
       echo "<script>
    alert('Email verified successfully! You can now login.');
    window.location.href='" . site_url('/') . "';
</script>";

    } else {
       echo "<script>
    alert('Invalid or expired verification code.');
    window.history.back();
</script>";

    }
}


    // Login
public function login()
{
    // Ensure session is started
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$email || !$password) {
        $_SESSION['error'] = 'Email and password are required.';
        $_SESSION['form_type'] = 'login';
        header('Location: ' . site_url('/'));
        exit;
    }

    // Check credentials
    $user = $this->userModel->checkCredentials($email, $password);

    if ($user) {
        // Check if user is verified
        if (isset($user['is_verified']) && $user['is_verified'] == 0) {
            $_SESSION['error'] = 'Please verify your email address before logging in.';
            $_SESSION['form_type'] = 'login';
            header('Location: ' . site_url('/'));
            exit;
        }

        // Save user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // Redirect to landing/dashboard page
        header('Location: ' . site_url('landing'));
        exit;
    } else {
        $_SESSION['error'] = 'Invalid email or password. Please try again.';
        $_SESSION['form_type'] = 'login';
        header('Location: ' . site_url('/'));
        exit;
    }
}

public function showVerify()
{
    $this->call->view('verify');
}

public function showRegister()
{
    $this->call->view('register');
}

public function logout()
{
    // Ensure session is started before destroying
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }
    
    session_destroy();
    header('Location: ' . site_url('/'));
    exit;
}

public function landing()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . site_url('login'));
        exit;
    }

    // Initialize database
    $db = $this->call->database();
    if (!$db) {
        $possiblePaths = [
            __DIR__ . '/../../scheme/database/Database.php',
            __DIR__ . '/../../core/Database.php',
            __DIR__ . '/../../system/core/Database.php'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                require_once $path;
                $db = new Database();
                break;
            }
        }
    }

    $user_id = $_SESSION['user_id'];

    // Fetch real data
    // 1. Count registered pets
    $petsResult = $db->table('pets')
                     ->where('user_id', $user_id)
                     ->get_all();
    $totalPets = is_array($petsResult) ? count($petsResult) : 0;

    // 2. Count upcoming appointments (future dates, NOT cancelled)
    $appointmentsResult = $db->table('appointments')
                             ->where('user_id', $user_id)
                             ->where('status', '!=', 'Cancelled')
                             ->where('appointment_date', '>=', date('Y-m-d'))
                             ->get_all();
    $upcomingAppointments = is_array($appointmentsResult) ? count($appointmentsResult) : 0;

    // 3. Count completed visits (past dates that are NOT cancelled)
    $completedResult = $db->table('appointments')
                          ->where('user_id', $user_id)
                          ->where('status', '!=', 'Cancelled')
                          ->where('appointment_date', '<', date('Y-m-d'))
                          ->get_all();
    $completedVisits = is_array($completedResult) ? count($completedResult) : 0;

    // 4. Get upcoming appointment details (limit 2)
    $upcomingDetails = [];
    if (is_array($appointmentsResult) && count($appointmentsResult) > 0) {
        foreach (array_slice($appointmentsResult, 0, 2) as $apt) {
            // Get pet name
            $pet = $db->table('pets')->where('id', $apt['pet_id'] ?? 0)->get();
            $petName = $pet ? $pet['name'] : 'Unknown';

            // Get vet name (using vet_id not veterinarian_id)
            $vet = $db->table('veterinarians')->where('id', $apt['vet_id'] ?? 0)->get();
            $vetName = $vet ? $vet['name'] : 'Unknown';

            // Get service name
            $service = $db->table('services')->where('id', $apt['service_id'] ?? 0)->get();
            $serviceName = $service ? $service['service_name'] : 'General Checkup';

            $upcomingDetails[] = [
                'pet_name' => $petName,
                'service' => $serviceName,
                'date' => date('M d, Y', strtotime($apt['appointment_date'])),
                'vet_name' => $vetName
            ];
        }
    }

    // Pass data to view
    $data = [
        'total_pets' => $totalPets,
        'upcoming_appointments' => $upcomingAppointments,
        'completed_visits' => $completedVisits,
        'upcoming_details' => $upcomingDetails
    ];

    $this->call->view('landing', $data);
}
}
