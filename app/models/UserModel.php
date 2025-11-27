<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

require_once __DIR__ . '/../../scheme/kernel/Model.php';

class UserModel extends Model
{
    protected $table = 'users';
    protected $primary_key = 'id';

    public function __construct() {
        parent::__construct();
        $this->call->database();
    }

    // Get user by email safely
    public function getUserByEmail($email)
    {
        $result = $this->db->table($this->table)
                           ->where('email', $email)
                           ->get();

        if (is_object($result) && method_exists($result, 'getRowArray')) {
            $row = $result->getRowArray();
            return is_array($row) ? $row : null;
        }

        if (is_array($result) && !empty($result)) {
            $rows = array_values($result);
            return $rows[0] ?? null;
        }

        return null;
    }

    // Insert user
    public function insertUser($data)
    {
        return $this->db->table($this->table)->insert($data);
    }

    // Verify user
    public function verifyUser($email, $code)
    {
        $affectedRows = $this->db->table($this->table)
                                 ->where('email', $email)
                                 ->where('verification_code', $code)
                                 ->update(['is_verified' => 1, 'verification_code' => null]);
        return ($affectedRows > 0);
    }

 public function checkCredentials($email, $password)
    {
        $this->call->database();

        // Fetch user by email
       $user = $this->db->table($this->table)
                 ->where('email', $email)
                 ->get(); // returns array directly


        // If no user found
        if (!$user) {
            return false;
        }

        // Compare password (plain for now)
        if ($user['password'] === $password) {
            return $user; // ✅ Login success
        }

        // Password mismatch
        return false;
    }

}
