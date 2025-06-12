<?php
/*
Fungsi: Merepresentasikan entitas pengguna dalam aplikasi Anda. Bertanggung jawab untuk semua operasi yang berkaitan dengan data pengguna.

Tugas Spesifik:
    Registrasi pengguna baru (menyimpan data ke tabel users).
    Login pengguna (memverifikasi kredensial dengan data di tabel users).
    Mencari pengguna berdasarkan username atau email.
    Mengambil data pengguna berdasarkan ID.
    Memperbarui informasi profil pengguna.
    Memperbarui password pengguna.
    Mengelola peran (role) pengguna (misalnya, menjadikan pengguna sebagai admin).
    Mengambil semua pengguna atau mencari pengguna (biasanya untuk panel admin).

Ketergantungan: Menggunakan Database.php untuk berinteraksi langsung dengan database.
*/

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Register user
    public function register($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $this->db->query("
            INSERT INTO users (username, password, email, full_name, phone, address)
            VALUES (:username, :password, :email, :full_name, :phone, :address)
        ");
        
        // Bind values
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        
        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
      // Login user
    public function login($username, $password) {
        $this->db->query("
            SELECT user_id, username, password, email, full_name, role, phone, address
            FROM users
            WHERE username = :username OR email = :email
        ");
        
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $username);
        
        $row = $this->db->single();
        
        if($row) {
            $hashedPassword = $row->password;
            
            if(password_verify($password, $hashedPassword)) {
                // Return all user data including role
                return $row;
            }
        }
        
        return false;
    }
    
    // Find user by username
    public function findUserByUsername($username) {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(':username', $username);
        
        $row = $this->db->single();
        
        return ($this->db->rowCount() > 0);
    }
    
    // Find user by email
    // Find user by email (modifikasi agar mengembalikan user atau false)
    public function findUserByEmail($email) { // Ini akan digunakan untuk validasi awal
        $this->db->query("SELECT user_id FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        $this->db->single();
        return ($this->db->rowCount() > 0);
    }

    // Metode BARU atau dimodifikasi dari findUserByEmail: Get user by email
    public function getUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single(); // Mengembalikan objek user atau false
    }
    
    // Get user by ID
    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE user_id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Update user
    public function updateUser($data) {
        // Bangun query secara dinamis berdasarkan data yang ada
        $setClauses = [
            "full_name = :full_name",
            "phone = :phone",
            "address = :address",
            "updated_at = CURRENT_TIMESTAMP"
        ];
        $params = [
            ':full_name' => $data['full_name'],
            ':phone' => $data['phone'],
            ':address' => $data['address'],
            ':user_id' => $data['user_id']
        ];

        if (isset($data['avatar']) && !empty($data['avatar'])) {
            $setClauses[] = "avatar = :avatar";
            $params[':avatar'] = $data['avatar'];
        }
        
        $sql = "UPDATE users SET " . implode(", ", $setClauses) . " WHERE user_id = :user_id";
        
        $this->db->query($sql);
        
        // Bind values
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        // Execute
        return $this->db->execute();
    }
    
    // Update password
    public function updatePassword($userId, $newPassword) {
        $this->db->query("
            UPDATE users
            SET password = :password,
                updated_at = CURRENT_TIMESTAMP
            WHERE user_id = :user_id
        ");
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Bind values
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':user_id', $userId);
        
        // Execute
        return $this->db->execute();
    }
    
    // Update user role (admin panel)
    public function updateUserRole($userId, $role) {
        $this->db->query("UPDATE users SET role = :role WHERE user_id = :user_id");
        $this->db->bind(':role', $role);
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }
    
    // Update user role
    public function updateRole($userId, $role) {
        $this->db->query("
            UPDATE users 
            SET role = :role,
                updated_at = CURRENT_TIMESTAMP
            WHERE user_id = :user_id
        ");
        
        $this->db->bind(':role', $role);
        $this->db->bind(':user_id', $userId);
        
        return $this->db->execute();
    }
    
    // Make user as admin
    public function makeAdmin($userId) {
        try {
            error_log("Making user $userId an admin");
            
            // First verify the user exists and isn't already an admin
            $this->db->query("SELECT user_id, role FROM users WHERE user_id = :user_id");
            $this->db->bind(':user_id', $userId);
            $user = $this->db->single();
            
            if (!$user) {
                error_log("User $userId not found");
                return false;
            }
            
            error_log("Current user role: " . ($user->role ?? 'null'));
            
            // Update to admin role
            $this->db->query("
                UPDATE users 
                SET role = 'admin'
                WHERE user_id = :user_id
            ");
            
            $this->db->bind(':user_id', $userId);
            $success = $this->db->execute();
            
            if ($success) {
                error_log("Successfully updated user $userId to admin");
                // Verify the update
                $this->db->query("SELECT role FROM users WHERE user_id = :user_id");
                $this->db->bind(':user_id', $userId);
                $updated = $this->db->single();
                error_log("New role after update: " . ($updated->role ?? 'null'));
                return true;
            } else {
                error_log("Failed to update user $userId to admin");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error in makeAdmin: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    // Get user's current role
    public function getUserRole($userId) {
        $this->db->query("SELECT role FROM users WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return $result ? $result->role : null;
    }
    
    // Get total number of users
    public function getTotalUsers() {
        $this->db->query("SELECT COUNT(*) as total FROM users");
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }
    
    // Get all users (for admin panel)
    // public function getAllUsers() {
    //     $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
    //     return $this->db->resultSet();
    // }
    
    // // Search users by name or email (for admin panel)
    // public function searchUsers($search) {
    //     $this->db->query("SELECT * FROM users WHERE full_name LIKE :search OR email LIKE :search ORDER BY created_at DESC");
    //     $this->db->bind(':search', "%$search%");
    //     return $this->db->resultSet();
    // }

    /**
     * Mengambil daftar pengguna dengan filter dan pagination.
     * Juga mengambil jumlah total booking per user.
     *
     * @param array $filters Filter seperti ['search' => 'keyword', 'role' => 'user']
     * @param int|null $limit Jumlah item per halaman
     * @param int|null $offset Offset untuk query
     * @return array Daftar pengguna
     */
    public function getUsers($filters = [], $limit = null, $offset = null) {
        // Bagian SELECT diubah untuk menyertakan total_booking
        $sql = "SELECT u.*, (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.user_id) as total_booking 
                FROM users u";
        $params = [];
        $whereClauses = [];

        if (!empty($filters['search'])) {
            $whereClauses[] = "(u.full_name LIKE :search OR u.email LIKE :search)";
            $params[':search'] = "%" . $filters['search'] . "%";
        }

        if (!empty($filters['role'])) {
            $whereClauses[] = "u.role = :role";
            $params[':role'] = $filters['role'];
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $sql .= " ORDER BY u.created_at DESC";

        if ($limit !== null && $offset !== null) {
            // Pastikan limit dan offset adalah integer
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = (int)$limit;
            $params[':offset'] = (int)$offset;
        }
        
        $this->db->query($sql);

        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                $this->db->bind($key, $value, PDO::PARAM_INT);
            } else {
                $this->db->bind($key, $value);
            }
        }
        
        return $this->db->resultSet();
    }

    /**
     * Menghitung total pengguna berdasarkan filter.
     *
     * @param array $filters Filter seperti ['search' => 'keyword', 'role' => 'user']
     * @return int Jumlah total pengguna
     */
    public function countUsers($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM users u"; // Alias tabel u
        $params = [];
        $whereClauses = [];

        if (!empty($filters['search'])) {
            $whereClauses[] = "(u.full_name LIKE :search OR u.email LIKE :search)";
            $params[':search'] = "%" . $filters['search'] . "%";
        }

        if (!empty($filters['role'])) {
            $whereClauses[] = "u.role = :role";
            $params[':role'] = $filters['role'];
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $this->db->query($sql);

        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }

    // Metode untuk mendapatkan avatar lama sebelum update, berguna untuk menghapus file lama
    public function getAvatarFilename($userId) {
        $this->db->query("SELECT avatar FROM users WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $row = $this->db->single();
        return $row ? $row->avatar : null;
    }

}