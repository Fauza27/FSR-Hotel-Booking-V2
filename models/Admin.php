<?php
// models/Admin.php
class Admin {
    private $db;
    private $adminId;
    private $username;
    private $email;
    private $fullName;
    private $role;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Login admin
    public function login($username, $password) {
        $this->db->query('SELECT * FROM admins WHERE username = :username');
        $this->db->bind(':username', $username);
        
        $admin = $this->db->single();
        
        if ($admin && password_verify($password, $admin->password)) {
            $this->adminId = $admin->admin_id;
            $this->username = $admin->username;
            $this->email = $admin->email;
            $this->fullName = $admin->full_name;
            $this->role = $admin->role;
            return true;
        }
        
        return false;
    }
    
    // Get admin by ID
    public function getAdminById($id) {
        $this->db->query('SELECT * FROM admins WHERE admin_id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    // Update admin profile
    public function updateProfile($id, $data) {
        $sql = "UPDATE admins SET full_name = :full_name, email = :email";
        
        if (isset($data['password'])) {
            $sql .= ", password = :password";
        }
        
        $sql .= " WHERE admin_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        
        if (isset($data['password'])) {
            $this->db->bind(':password', $data['password']);
        }
        
        return $this->db->execute();
    }
    
    // Create new admin
    public function createAdmin($data) {
        $this->db->query('INSERT INTO admins (username, password, email, full_name, role) 
                         VALUES (:username, :password, :email, :full_name, :role)');
        
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':role', $data['role']);
        
        return $this->db->execute();
    }
    
    // Get all admins
    public function getAllAdmins() {
        $this->db->query('SELECT admin_id, username, email, full_name, role, created_at 
                         FROM admins ORDER BY created_at DESC');
        return $this->db->resultSet();
    }
    
    // Delete admin
    public function deleteAdmin($id) {
        $this->db->query('DELETE FROM admins WHERE admin_id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    // Check if username exists
    public function usernameExists($username, $excludeId = null) {
        $sql = 'SELECT admin_id FROM admins WHERE username = :username';
        if ($excludeId) {
            $sql .= ' AND admin_id != :exclude_id';
        }
        
        $this->db->query($sql);
        $this->db->bind(':username', $username);
        if ($excludeId) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        
        return $this->db->single() ? true : false;
    }
    
    // Check if email exists
    public function emailExists($email, $excludeId = null) {
        $sql = 'SELECT admin_id FROM admins WHERE email = :email';
        if ($excludeId) {
            $sql .= ' AND admin_id != :exclude_id';
        }
        
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        if ($excludeId) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        
        return $this->db->single() ? true : false;
    }
    
    // Getters
    public function getAdminId() {
        return $this->adminId;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getFullName() {
        return $this->fullName;
    }
    
    public function getRole() {
        return $this->role;
    }
}
?>