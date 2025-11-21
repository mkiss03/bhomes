<?php
// api/blog_upload.php - UPDATED VERSION WITH BETTER ERROR HANDLING
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

try {
    $upload_dir = '../uploads/blog/';

    // Create blog directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Cannot create upload directory');
        }
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Nincs feltöltött kép vagy hiba történt. Error code: ' . ($_FILES['image']['error'] ?? 'no file')]);
        exit;
    }

    $file = $_FILES['image'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Validate file type
    if (!in_array($file_extension, $allowed_extensions)) {
        http_response_code(400);
        echo json_encode(['message' => 'Nem támogatott fájlformátum. Csak JPG, PNG, GIF és WebP engedélyezett.']);
        exit;
    }

    // Validate file size (max 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['message' => 'A fájl túl nagy. Maximum 10MB engedélyezett.']);
        exit;
    }

    // Validate that it's actually an image
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        http_response_code(400);
        echo json_encode(['message' => 'A feltöltött fájl nem érvényes kép.']);
        exit;
    }

    // Generate unique filename
    $filename = 'blog_' . uniqid() . '_' . time() . '.' . $file_extension;
    $filepath = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $relative_path = 'uploads/blog/' . $filename;
        
        http_response_code(200);
        echo json_encode([
            'message' => 'Kép sikeresen feltöltve.',
            'file_path' => $relative_path,
            'url' => '/' . $relative_path
        ]);
    } else {
        throw new Exception('Cannot move uploaded file');
    }

} catch (Exception $e) {
    error_log('Blog upload error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Váratlan hiba történt a feltöltés során: ' . $e->getMessage()]);
}
?>