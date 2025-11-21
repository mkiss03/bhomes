<?php 
// api/upload.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if(!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

include_once '../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $property_id = $_POST['property_id'];
    $upload_dir = '../uploads/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $uploaded_files = [];

    if(isset($_FILES['images'])) {
        $files = $_FILES['images'];
        
        for($i = 0; $i < count($files['name']); $i++) {
            if($files['error'][$i] == 0) {
                $file_extension = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if(in_array($file_extension, $allowed_extensions)) {
                    $filename = uniqid() . '_' . time() . '.' . $file_extension;
                    $filepath = $upload_dir . $filename;
                    
                    if(move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                        // Get the highest order number for this property
                        $query = "SELECT COALESCE(MAX(image_order), 0) + 1 as next_order FROM images WHERE property_id = ?";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$property_id]);
                        $next_order = $stmt->fetch()['next_order'];
                        
                        // Save to database with proper order
                        $query = "INSERT INTO images (property_id, image_path, image_order) VALUES (?, ?, ?)";
                        $stmt = $db->prepare($query);
                        $relative_path = 'uploads/' . $filename;
                        $stmt->execute([$property_id, $relative_path, $next_order]);
                        
                        $uploaded_files[] = $relative_path;
                    }
                }
            }
        }
    }

    if(count($uploaded_files) > 0) {
        http_response_code(200);
        echo json_encode(array(
            "message" => "Images uploaded successfully.",
            "files" => $uploaded_files
        ));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "No images uploaded."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>