<?php
// models/Blog.php
class Blog {
    private $conn;
    private $table_name = "blog_posts";

    public $id;
    public $title;
    public $slug;
    public $excerpt;
    public $content;
    public $cover_image;
    public $status;
    public $publish_at;
    public $seo_title;
    public $seo_description;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Generate URL-friendly slug
    public function generateSlug($title, $id = null) {
        // Convert Hungarian characters
        $hungarian = ['á', 'é', 'í', 'ó', 'ö', 'ő', 'ú', 'ü', 'ű', 'Á', 'É', 'Í', 'Ó', 'Ö', 'Ő', 'Ú', 'Ü', 'Ű'];
        $latin = ['a', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'u', 'A', 'E', 'I', 'O', 'O', 'O', 'U', 'U', 'U'];
        $title = str_replace($hungarian, $latin, $title);
        
        // Create basic slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        
        // Check for duplicates and append number if needed
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $id)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    // Check if slug exists
    private function slugExists($slug, $excludeId = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE slug = :slug";
        if ($excludeId) {
            $query .= " AND id != :id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId);
        }
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Get published posts with pagination
    public function getPublishedPosts($page = 1, $limit = 9, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE status = 'published' AND publish_at <= NOW()";
        
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (title LIKE :search OR excerpt LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $query .= " ORDER BY publish_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $posts = $stmt->fetchAll();
        
        // Count total for pagination
        $countQuery = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                       WHERE status = 'published' AND publish_at <= NOW()";
        
        if (!empty($search)) {
            $countQuery .= " AND (title LIKE :search OR excerpt LIKE :search)";
        }
        
        $countStmt = $this->conn->prepare($countQuery);
        
        if (!empty($search)) {
            $countStmt->bindValue(':search', '%' . $search . '%');
        }
        
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        return [
            'posts' => $posts,
            'has_more' => ($offset + $limit) < $total,
            'total' => $total
        ];
    }

    // Get post by slug
    public function findBySlug($slug) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE slug = :slug AND status = 'published' AND publish_at <= NOW() 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if ($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->slug = $row['slug'];
            $this->excerpt = $row['excerpt'];
            $this->content = $row['content'];
            $this->cover_image = $row['cover_image'];
            $this->status = $row['status'];
            $this->publish_at = $row['publish_at'];
            $this->seo_title = $row['seo_title'];
            $this->seo_description = $row['seo_description'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return $row;
        }
        
        return false;
    }

    // Get all posts (for admin)
    public function getAllPosts($filters = []) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $query .= " AND (title LIKE :search OR excerpt LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get single post by ID (for admin)
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if ($row) {
            $this->title = $row['title'];
            $this->slug = $row['slug'];
            $this->excerpt = $row['excerpt'];
            $this->content = $row['content'];
            $this->cover_image = $row['cover_image'];
            $this->status = $row['status'];
            $this->publish_at = $row['publish_at'];
            $this->seo_title = $row['seo_title'];
            $this->seo_description = $row['seo_description'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return $row;
        }
        
        return false;
    }

    // Create new post
    public function create() {
        // Generate slug if not provided
        if (empty($this->slug)) {
            $this->slug = $this->generateSlug($this->title);
        } else {
            $this->slug = $this->generateSlug($this->slug);
        }
        
        $query = "INSERT INTO " . $this->table_name . "
                    SET title = :title, slug = :slug, excerpt = :excerpt, content = :content,
                        cover_image = :cover_image, status = :status, publish_at = :publish_at,
                        seo_title = :seo_title, seo_description = :seo_description";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->excerpt = htmlspecialchars(strip_tags($this->excerpt));
        $this->seo_title = htmlspecialchars(strip_tags($this->seo_title));
        $this->seo_description = htmlspecialchars(strip_tags($this->seo_description));

        // Bind values
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':excerpt', $this->excerpt);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':cover_image', $this->cover_image);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':publish_at', $this->publish_at);
        $stmt->bindParam(':seo_title', $this->seo_title);
        $stmt->bindParam(':seo_description', $this->seo_description);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Update post
    public function update() {
        // Regenerate slug if title changed (but keep existing slug if manually set)
        if (empty($this->slug)) {
            $this->slug = $this->generateSlug($this->title, $this->id);
        }
        
        $query = "UPDATE " . $this->table_name . "
                    SET title = :title, slug = :slug, excerpt = :excerpt, content = :content,
                        cover_image = :cover_image, status = :status, publish_at = :publish_at,
                        seo_title = :seo_title, seo_description = :seo_description,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->excerpt = htmlspecialchars(strip_tags($this->excerpt));
        $this->seo_title = htmlspecialchars(strip_tags($this->seo_title));
        $this->seo_description = htmlspecialchars(strip_tags($this->seo_description));

        // Bind values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':excerpt', $this->excerpt);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':cover_image', $this->cover_image);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':publish_at', $this->publish_at);
        $stmt->bindParam(':seo_title', $this->seo_title);
        $stmt->bindParam(':seo_description', $this->seo_description);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete post
    public function delete() {
        // Delete cover image if exists
        if ($this->cover_image && file_exists('../' . $this->cover_image)) {
            unlink('../' . $this->cover_image);
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get recent posts for RSS
    public function getRecentPosts($limit = 10) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE status = 'published' AND publish_at <= NOW() 
                  ORDER BY publish_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Sanitize content for display (basic security)
    public function sanitizeContent($content) {
        // Allow basic HTML tags, remove dangerous ones
        $allowedTags = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote>';
        return strip_tags($content, $allowedTags);
    }
}
?>