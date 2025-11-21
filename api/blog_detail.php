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
    
    // Get slug from URL parameter
    $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
    
    if (empty($slug)) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Missing slug parameter',
            'message' => 'Nincs megadva blogbejegyzés azonosító.'
        ]);
        exit;
    }
    
    // Get the blog post by slug
    $sql = "SELECT id, title, slug, excerpt, content, cover_image, publish_at, created_at, seo_title, seo_description
            FROM blog_posts 
            WHERE slug = ? 
            AND status = 'published' 
            AND publish_at <= NOW()
            LIMIT 1";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        http_response_code(404);
        echo json_encode([
            'error' => 'Post not found',
            'message' => 'A blogbejegyzés nem található.'
        ]);
        exit;
    }
    
    // Format the post
    if ($post['publish_at']) {
        $publishDate = new DateTime($post['publish_at']);
        $post['publish_date_formatted'] = $publishDate->format('Y. m. d.');
    } else {
        $post['publish_date_formatted'] = '';
    }
    
    // Ensure excerpt exists
    if (empty($post['excerpt'])) {
        $post['excerpt'] = 'Részletek a teljes cikkben...';
    }
    
    // Ensure cover_image has a default if empty
    if (empty($post['cover_image'])) {
        $post['cover_image'] = '/images/main1.jpg';
    }
    
    // Return the post data directly (not wrapped in 'post' key to match your HTML)
    echo json_encode($post, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => 'Adatbázis hiba történt.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error', 
        'message' => 'Szerver hiba történt.'
    ]);
}
?>