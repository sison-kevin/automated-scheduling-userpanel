<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class SettingsController extends Controller
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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . site_url('login'));
            exit;
        }

        // Fetch current user data
        $user = $this->db->table('users')
                         ->where('id', $_SESSION['user_id'])
                         ->get();

        $data['user'] = $user;
        $this->call->view('settings', $data);
    }

    public function update_profile()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . site_url('login'));
            exit;
        }

        // Get form data
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        // Validate
        if (empty($name) || empty($email)) {
            $_SESSION['error'] = 'Name and email are required.';
            header('Location: ' . site_url('settings'));
            exit;
        }

        // Check if email is already taken by another user
        $existingUser = $this->db->table('users')
                                 ->where('email', $email)
                                 ->get();

        if ($existingUser && $existingUser['id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Email is already taken by another user.';
            header('Location: ' . site_url('settings'));
            exit;
        }

        // Update user profile
        $updated = $this->db->table('users')
                           ->where('id', $_SESSION['user_id'])
                           ->update([
                               'name' => $name,
                               'email' => $email
                           ]);

        if ($updated) {
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['success'] = 'Profile updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update profile.';
        }

        header('Location: ' . site_url('settings'));
        exit;
    }

    public function change_password()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . site_url('login'));
            exit;
        }

        // Get form data
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = 'All password fields are required.';
            header('Location: ' . site_url('settings'));
            exit;
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = 'New passwords do not match.';
            header('Location: ' . site_url('settings'));
            exit;
        }

        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'New password must be at least 6 characters.';
            header('Location: ' . site_url('settings'));
            exit;
        }

        // Get current user
        $user = $this->db->table('users')
                        ->where('id', $_SESSION['user_id'])
                        ->get();

        // Verify current password (plain text comparison)
        if ($current_password !== $user['password']) {
            $_SESSION['error'] = 'Current password is incorrect.';
            header('Location: ' . site_url('settings'));
            exit;
        }

        // Store new password as plain text (matching your current system)
        $hashed_password = $new_password;
        $updated = $this->db->table('users')
                           ->where('id', $_SESSION['user_id'])
                           ->update([
                               'password' => $hashed_password
                           ]);

        if ($updated) {
            $_SESSION['success'] = 'Password changed successfully!';
        } else {
            $_SESSION['error'] = 'Failed to change password.';
        }

        header('Location: ' . site_url('settings'));
        exit;
    }
}
