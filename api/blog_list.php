<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Use the same database connection as your other files
include_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get parameters
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 9;
    $search = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    $offset = ($page - 1) * $limit;
    
    // Build the base query
    if (!empty($search)) {
        // With search
        $sql = "SELECT id, title, slug, excerpt, cover_image, publish_at, created_at 
                FROM blog_posts 
                WHERE status = 'published' 
                AND publish_at <= NOW()
                AND (title LIKE ? OR excerpt LIKE ? OR content LIKE ?)
                ORDER BY publish_at DESC 
                LIMIT $limit OFFSET $offset";
        
        $countSql = "SELECT COUNT(*) FROM blog_posts 
                     WHERE status = 'published' 
                     AND publish_at <= NOW()
                     AND (title LIKE ? OR excerpt LIKE ? OR content LIKE ?)";
        
        $searchParam = "%$search%";
        $params = [$searchParam, $searchParam, $searchParam];
        
        // Get total count
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $totalPosts = (int)$countStmt->fetchColumn();
        
        // Get posts
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
    } else {
        // Without search
        $sql = "SELECT id, title, slug, excerpt, cover_image, publish_at, created_at 
                FROM blog_posts 
                WHERE status = 'published' 
                AND publish_at <= NOW()
                ORDER BY publish_at DESC 
                LIMIT $limit OFFSET $offset";
        
        $countSql = "SELECT COUNT(*) FROM blog_posts 
                     WHERE status = 'published' 
                     AND publish_at <= NOW()";
        
        // Get total count
        $countStmt = $db->prepare($countSql);
        $countStmt->execute();
        $totalPosts = (int)$countStmt->fetchColumn();
        
        // Get posts
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }
    
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the posts
    foreach ($posts as &$post) {
        // Format the publish date
        if ($post['publish_at']) {
            $publishDate = new DateTime($post['publish_at']);
            $post['publish_date_formatted'] = $publishDate->format('Y. m. d.');
        } else {
            $post['publish_date_formatted'] = date('Y. m. d.');
        }
        
        // Ensure excerpt exists
        if (empty($post['excerpt'])) {
            $post['excerpt'] = 'Részletek a teljes cikkben...';
        }
        
        // Ensure cover_image has a default if empty
        if (empty($post['cover_image'])) {
            $post['cover_image'] = '/images/main1.jpg';
        }
    }
    
    // Calculate if there are more posts
    $hasMore = ($offset + $limit) < $totalPosts;
    
    // Return the response
    $response = [
        'posts' => $posts,
        'has_more' => $hasMore,
        'total' => $totalPosts,
        'page' => $page,
        'limit' => $limit
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage(),
        'posts' => [],
        'has_more' => false,
        'total' => 0
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error', 
        'message' => $e->getMessage(),
        'posts' => [],
        'has_more' => false,
        'total' => 0
    ]);
}
?>