<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Database connection (adjust these settings to match your database)
    $host = 'localhost';
    $dbname = 'your_database_name';
    $username = 'your_db_username';
    $password = 'your_db_password';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        // Get query parameters
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        
        // Build the query
        $sql = "SELECT id, title, slug, excerpt, cover_image, publish_at, created_at 
                FROM blog_posts 
                WHERE status = 'published' AND publish_at <= NOW()";
        
        $params = [];
        
        // Add search functionality
        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR excerpt LIKE :search OR content LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        // Add ordering and pagination
        $sql .= " ORDER BY publish_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the posts
        foreach ($posts as &$post) {
            $post['excerpt'] = $post['excerpt'] ?: substr(strip_tags($post['content'] ?? ''), 0, 150) . '...';
            $post['publish_at'] = date('Y-m-d H:i:s', strtotime($post['publish_at']));
        }
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM blog_posts WHERE status = 'published' AND publish_at <= NOW()";
        if (!empty($search)) {
            $countSql .= " AND (title LIKE :search OR excerpt LIKE :search OR content LIKE :search)";
        }
        
        $countStmt = $pdo->prepare($countSql);
        if (!empty($search)) {
            $countStmt->bindValue(':search', "%$search%");
        }
        $countStmt->execute();
        $total = $countStmt->fetchColumn();
        
        echo json_encode([
            'posts' => $posts,
            'total' => (int)$total,
            'limit' => $limit,
            'offset' => $offset
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>