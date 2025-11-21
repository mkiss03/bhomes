<?php
// api/upload_temp.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if(!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_dir = '../uploads/';
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $uploaded_files = [];
    $errors = [];

    if(isset($_FILES['images'])) {
        $files = $_FILES['images'];
        $file_count = count($files['name']);
        
        for($i = 0; $i < $file_count; $i++) {
            if($files['error'][$i] == 0) {
                $file_extension = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                $max_file_size = 50 * 1024 * 1024; // 50MB
                
                // Validate file extension
                if(!in_array($file_extension, $allowed_extensions)) {
                    $errors[] = "Invalid file type: " . $files['name'][$i];
                    continue;
                }
                
                // Validate file size
                if($files['size'][$i] > $max_file_size) {
                    $errors[] = "File too large: " . $files['name'][$i];
                    continue;
                }
                
                // Generate unique filename
                $filename = uniqid() . '_' . time() . '.' . $file_extension;
                $filepath = $upload_dir . $filename;
                
                if(move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                    $relative_path = 'uploads/' . $filename;
                    $uploaded_files[] = $relative_path;
                } else {
                    $errors[] = "Failed to upload: " . $files['name'][$i];
                }
            } else {
                $errors[] = "Upload error for: " . $files['name'][$i];
            }
        }
    }

    if(count($uploaded_files) > 0) {
        http_response_code(200);
        echo json_encode(array(
            "message" => "Images uploaded successfully.",
            "files" => $uploaded_files,
            "errors" => $errors
        ));
    } else {
        http_response_code(400);
        echo json_encode(array(
            "message" => "No images uploaded.",
            "errors" => $errors
        ));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>