<?php
// models/Contact.php
class Contact {
    private $conn;
    private $table_name = "contacts";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $message;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new contact message
    public function create() {
        try {
            // Verify connection exists
            if (!$this->conn) {
                error_log("Contact create: No database connection");
                return false;
            }

            $query = "INSERT INTO " . $this->table_name . "
                        SET name = :name,
                            email = :email,
                            phone = :phone,
                            message = :message";

            // Log the query for debugging
            error_log("Contact create query: " . $query);

            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                error_log("Contact create: Failed to prepare statement");
                error_log("PDO Error: " . print_r($this->conn->errorInfo(), true));
                return false;
            }

            // Bind values (already sanitized in the API endpoint)
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':message', $this->message);

            // Log the bound values (for debugging)
            error_log("Inserting contact - Name: " . $this->name . ", Email: " . $this->email);

            if($stmt->execute()) {
                error_log("Contact created successfully for: " . $this->email);
                return true;
            }

            // Log SQL error
            $errorInfo = $stmt->errorInfo();
            error_log("Contact create failed - SQLSTATE: " . $errorInfo[0] .
                     ", Error Code: " . $errorInfo[1] .
                     ", Message: " . $errorInfo[2]);
            return false;
        } catch (PDOException $e) {
            error_log("Contact create PDOException: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        } catch (Exception $e) {
            error_log("Contact create Exception: " . $e->getMessage());
            return false;
        }
    }

    // Read all contacts (for admin)
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . "
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Read single contact
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->message = $row['message'];
            $this->created_at = $row['created_at'];

            return $row;
        }

        return false;
    }

    // Delete contact
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Count total contacts
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
