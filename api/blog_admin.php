<?php
// api/blog_admin.php - UPDATED VERSION WITH BETTER ERROR HANDLING
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get single post
                $blog->id = (int)$_GET['id'];
                $post = $blog->readOne();
                
                if ($post) {
                    echo json_encode($post);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Blogbejegyzés nem található.']);
                }
            } else {
                // Get all posts with filters
                $filters = [
                    'status' => $_GET['status'] ?? '',
                    'search' => $_GET['search'] ?? ''
                ];
                
                $posts = $blog->getAllPosts($filters);
                echo json_encode($posts);
            }
            break;
            
        case 'DELETE':
            $blog->id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($blog->id <= 0) {
                http_response_code(400);
                echo json_encode(['message' => 'Érvénytelen post ID.']);
                exit;
            }
            
            // Load post to get cover image path before deletion
            $blog->readOne();
            
            if ($blog->delete()) {
                http_response_code(200);
                echo json_encode(['message' => 'Blogbejegyzés sikeresen törölve.']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Hiba a blogbejegyzés törlésekor.']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    error_log('Blog admin error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Váratlan hiba történt: ' . $e->getMessage()]);
}
?>