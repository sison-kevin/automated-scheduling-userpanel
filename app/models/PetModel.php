<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class PetModel extends Model
{
    protected $table = 'pets';
    protected $primary_key = 'id';
    protected $db;

    public function __construct()
    {
        parent::__construct();

        // ✅ Safely initialize database connection
        $lava = lava_instance();

        if (isset($lava->call) && method_exists($lava->call, 'database')) {
            $this->db = $lava->call->database();
        }

        // 🔹 Fallback: try the global helper if available
        if (!$this->db && function_exists('database')) {
            $this->db = database();
        }

        // 🔹 Final sanity check
        if (!$this->db) {
            throw new RuntimeException('❌ Database connection could not be initialized in PetModel.');
        }
    }

    public function getPetsByUser($user_id)
    {
        return $this->db
                    ->table($this->table)
                    ->where('user_id', $user_id)
                    ->get_all();
    }

    public function addPet($data)
    {
        return $this->db->table($this->table)->insert($data);
    }
}
