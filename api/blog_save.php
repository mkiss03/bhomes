<?php
// api/blog_save.php - UPDATED VERSION WITH BETTER ERROR HANDLING
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

try {
    include_once '../config/database.php';
    include_once '../models/Blog.php';

    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    $blog = new Blog($db);

    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['message' => 'Érvénytelen adatok.']);
        exit;
    }

    // Validate required fields
    if (empty($data['title']) || empty($data['content'])) {
        http_response_code(400);
        echo json_encode(['message' => 'A cím és tartalom megadása kötelező.']);
        exit;
    }

    // Set properties
    $blog->title = $data['title'];
    $blog->slug = isset($data['slug']) ? $data['slug'] : '';
    $blog->excerpt = isset($data['excerpt']) ? $data['excerpt'] : '';
    $blog->content = $data['content'];
    $blog->cover_image = isset($data['cover_image']) ? $data['cover_image'] : null;
    $blog->status = isset($data['status']) ? $data['status'] : 'draft';
    $blog->publish_at = isset($data['publish_at']) ? $data['publish_at'] : date('Y-m-d H:i:s');
    $blog->seo_title = isset($data['seo_title']) ? $data['seo_title'] : '';
    $blog->seo_description = isset($data['seo_description']) ? $data['seo_description'] : '';

    if ($method === 'POST') {
        // Create new post
        $postId = $blog->create();
        
        if ($postId) {
            http_response_code(201);
            echo json_encode(['message' => 'Blogbejegyzés sikeresen létrehozva.', 'id' => $postId]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Hiba a blogbejegyzés létrehozásakor.']);
        }
        
    } elseif ($method === 'PUT') {
        // Update existing post
        $blog->id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($blog->id <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'Érvénytelen post ID.']);
            exit;
        }
        
        if ($blog->update()) {
            http_response_code(200);
            echo json_encode(['message' => 'Blogbejegyzés sikeresen frissítve.']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Hiba a blogbejegyzés frissítésekor.']);
        }
    }
    
} catch (Exception $e) {
    error_log('Blog save error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Váratlan hiba történt: ' . $e->getMessage()]);
}
?>