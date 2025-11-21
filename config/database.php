<?php
// config/database.php
class Database {
    private $host = 'mysql.rackhost.hu';
    private $db_name = 'c88384bhe';
    private $username = 'c88384eszti';
    private $password = 'Eszter2009';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";

            error_log("Attempting database connection to: " . $this->host . " / " . $this->db_name);

            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            error_log("Database connection successful");
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            error_log("Connection details - Host: " . $this->host . ", DB: " . $this->db_name . ", User: " . $this->username);
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>