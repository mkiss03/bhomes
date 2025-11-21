<?php
// models/Content.php
class Content {
    private $conn;
    private $table_name = "content_sections";

    public $id;
    public $section_key;
    public $section_name;
    public $content_type;
    public $content_value;
    public $page;
    public $section_group;
    public $display_order;
    public $is_active;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all content sections with optional filtering
    public function readAll($page = null, $section_group = null, $active_only = true) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if ($page !== null) {
            $query .= " AND page = :page";
            $params[':page'] = $page;
        }

        if ($section_group !== null) {
            $query .= " AND section_group = :section_group";
            $params[':section_group'] = $section_group;
        }

        if ($active_only) {
            $query .= " AND is_active = 1";
        }

        $query .= " ORDER BY display_order ASC, section_key ASC";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get content by section key
    public function readByKey($section_key) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE section_key = :section_key AND is_active = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':section_key', $section_key);
        $stmt->execute();

        $row = $stmt->fetch();
        
        if ($row) {
            $this->id = $row['id'];
            $this->section_key = $row['section_key'];
            $this->section_name = $row['section_name'];
            $this->content_type = $row['content_type'];
            $this->content_value = $row['content_value'];
            $this->page = $row['page'];
            $this->section_group = $row['section_group'];
            $this->display_order = $row['display_order'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return $row;
        }
        
        return false;
    }

    // Get content by ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch();
        
        if ($row) {
            $this->section_key = $row['section_key'];
            $this->section_name = $row['section_name'];
            $this->content_type = $row['content_type'];
            $this->content_value = $row['content_value'];
            $this->page = $row['page'];
            $this->section_group = $row['section_group'];
            $this->display_order = $row['display_order'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return $row;
        }
        
        return false;
    }

    // Create new content section
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                    SET section_key = :section_key,
                        section_name = :section_name,
                        content_type = :content_type,
                        content_value = :content_value,
                        page = :page,
                        section_group = :section_group,
                        display_order = :display_order,
                        is_active = :is_active";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->section_key = htmlspecialchars(strip_tags($this->section_key));
        $this->section_name = htmlspecialchars(strip_tags($this->section_name));
        $this->content_type = htmlspecialchars(strip_tags($this->content_type));
        $this->page = htmlspecialchars(strip_tags($this->page));
        $this->section_group = htmlspecialchars(strip_tags($this->section_group));

        // For content_value, only strip tags if content_type is 'text'
        if ($this->content_type === 'text') {
            $this->content_value = htmlspecialchars(strip_tags($this->content_value));
        } else {
            // For HTML content, allow basic HTML tags but sanitize
            $this->content_value = htmlspecialchars($this->content_value, ENT_NOQUOTES);
        }

        // Bind values
        $stmt->bindParam(':section_key', $this->section_key);
        $stmt->bindParam(':section_name', $this->section_name);
        $stmt->bindParam(':content_type', $this->content_type);
        $stmt->bindParam(':content_value', $this->content_value);
        $stmt->bindParam(':page', $this->page);
        $stmt->bindParam(':section_group', $this->section_group);
        $stmt->bindParam(':display_order', $this->display_order);
        $stmt->bindParam(':is_active', $this->is_active);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Update content section
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                    SET section_name = :section_name,
                        content_type = :content_type,
                        content_value = :content_value,
                        page = :page,
                        section_group = :section_group,
                        display_order = :display_order,
                        is_active = :is_active,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->section_name = htmlspecialchars(strip_tags($this->section_name));
        $this->content_type = htmlspecialchars(strip_tags($this->content_type));
        $this->page = htmlspecialchars(strip_tags($this->page));
        $this->section_group = htmlspecialchars(strip_tags($this->section_group));

        // For content_value, handle based on content_type
        if ($this->content_type === 'text') {
            $this->content_value = htmlspecialchars(strip_tags($this->content_value));
        }
        // For HTML content, we'll allow it but could add more sophisticated sanitization

        // Bind values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':section_name', $this->section_name);
        $stmt->bindParam(':content_type', $this->content_type);
        $stmt->bindParam(':content_value', $this->content_value);
        $stmt->bindParam(':page', $this->page);
        $stmt->bindParam(':section_group', $this->section_group);
        $stmt->bindParam(':display_order', $this->display_order);
        $stmt->bindParam(':is_active', $this->is_active);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update only content value (for quick edits)
    public function updateContent() {
        $query = "UPDATE " . $this->table_name . "
                    SET content_value = :content_value,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE section_key = :section_key";

        $stmt = $this->conn->prepare($query);

        // Handle content based on type
        if ($this->content_type === 'text') {
            $this->content_value = htmlspecialchars(strip_tags($this->content_value));
        }

        $stmt->bindParam(':content_value', $this->content_value);
        $stmt->bindParam(':section_key', $this->section_key);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete content section
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get content sections grouped by page and section_group
    public function getContentByPageGrouped($page) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE page = :page AND is_active = 1 
                  ORDER BY section_group ASC, display_order ASC, section_key ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':page', $page);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        $grouped = [];
        
        foreach ($results as $row) {
            $group = $row['section_group'] ?: 'default';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $row;
        }
        
        return $grouped;
    }

    // Get all pages that have content
    public function getPages() {
        $query = "SELECT DISTINCT page FROM " . $this->table_name . " WHERE is_active = 1 ORDER BY page";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Get all section groups for a page
    public function getSectionGroups($page) {
        $query = "SELECT DISTINCT section_group FROM " . $this->table_name . " 
                  WHERE page = :page AND is_active = 1 AND section_group IS NOT NULL 
                  ORDER BY section_group";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':page', $page);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Batch update content values
    public function batchUpdate($updates) {
        try {
            $this->conn->beginTransaction();
            
            foreach ($updates as $update) {
                $query = "UPDATE " . $this->table_name . "
                          SET content_value = :content_value,
                              updated_at = CURRENT_TIMESTAMP
                          WHERE section_key = :section_key";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':content_value', $update['content_value']);
                $stmt->bindParam(':section_key', $update['section_key']);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>