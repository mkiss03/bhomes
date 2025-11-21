<?php
// api/images.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if(!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['property_id'])) {
            // Get images for a specific property
            $property_id = $_GET['property_id'];
            $query = "SELECT * FROM images WHERE property_id = ? ORDER BY image_order ASC";
            $stmt = $db->prepare($query);
            $stmt->execute([$property_id]);
            $images = $stmt->fetchAll();
            
            http_response_code(200);
            echo json_encode($images);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Property ID is required."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->property_id) && !empty($data->images)) {
            $property_id = $data->property_id;
            $images = $data->images;
            
            try {
                $db->beginTransaction();
                
                foreach($images as $imageData) {
                    if($imageData->is_new) {
                        // Insert new image
                        $query = "INSERT INTO images (property_id, image_path, image_order) VALUES (?, ?, ?)";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$property_id, $imageData->image_path, $imageData->image_order]);
                    } else if($imageData->id) {
                        // Update existing image order
                        $query = "UPDATE images SET image_order = ? WHERE id = ? AND property_id = ?";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$imageData->image_order, $imageData->id, $property_id]);
                    }
                }
                
                $db->commit();
                http_response_code(200);
                echo json_encode(array("message" => "Images updated successfully."));
                
            } catch(Exception $e) {
                $db->rollback();
                http_response_code(500);
                echo json_encode(array("message" => "Error updating images: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Property ID and images data are required."));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->image_ids)) {
            try {
                $placeholders = str_repeat('?,', count($data->image_ids) - 1) . '?';
                
                // Get image paths before deleting from database
                $query = "SELECT image_path FROM images WHERE id IN ($placeholders)";
                $stmt = $db->prepare($query);
                $stmt->execute($data->image_ids);
                $imagePaths = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Delete from database
                $query = "DELETE FROM images WHERE id IN ($placeholders)";
                $stmt = $db->prepare($query);
                $stmt->execute($data->image_ids);
                
                // Delete physical files
                foreach($imagePaths as $imagePath) {
                    $fullPath = '../' . $imagePath;
                    if(file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
                
                http_response_code(200);
                echo json_encode(array("message" => "Images deleted successfully."));
                
            } catch(Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error deleting images: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Image IDs are required."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>