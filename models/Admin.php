<?php
// models/Admin.php
class Admin {
    private $conn;
    private $table_name = "admins";

    public $id;
    public $username;
    public $password;
    public $email;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login function
    public function login($username, $password) {
        // First check if admin table exists and create if needed
        $this->createAdminTableIfNotExists();
        
        $query = "SELECT id, username, password, email FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Check password - support both plain text and hashed for flexibility
            if (password_verify($password, $row['password']) || $row['password'] === $password) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                return true;
            }
        }
        
        // If no admin found and credentials are default, create default admin
        if ($username === 'admin' && $password === 'admin123') {
            $this->createDefaultAdmin();
            return $this->login($username, $password);
        }
        
        return false;
    }

    // Create admin table if it doesn't exist
    private function createAdminTableIfNotExists() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->conn->exec($query);
    }

    // Create default admin user
    private function createDefaultAdmin() {
        $query = "INSERT IGNORE INTO " . $this->table_name . " (username, password, email) VALUES ('admin', :password, 'admin@besthomesespana.com')";
        
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();
    }

    // Get admin by ID
    public function readOne() {
        $query = "SELECT id, username, email, created_at FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }

    // Update admin
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET username = :username, email = :email 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    // Update password
    public function updatePassword($new_password) {
        $query = "UPDATE " . $this->table_name . " 
                  SET password = :password 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
}
?>