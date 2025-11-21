<?php 
// api/properties.php - Complete version with new build support
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            // Get single property
            $property_id = $_GET['id'];
            
            $query = "SELECT p.*, 
                        GROUP_CONCAT(DISTINCT pi.image_url ORDER BY pi.is_primary DESC, pi.image_id LIMIT 3) as images
                      FROM properties p 
                      LEFT JOIN property_images pi ON p.id = pi.property_id 
                      WHERE p.id = ?
                      GROUP BY p.id";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$property_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result) {
                // Process images
                if ($result['images']) {
                    $result['images'] = explode(',', $result['images']);
                } else {
                    $result['images'] = [];
                }
                
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Property not found."));
            }
        } else {
            // Get properties with filters - CUSTOM LOGIC FOR NEW BUILD
            try {
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 9;
                $offset = ($page - 1) * $limit;
                
                // Build the main query
                $query = "SELECT p.*, 
                            GROUP_CONCAT(DISTINCT pi.image_url ORDER BY pi.is_primary DESC, pi.image_id LIMIT 3) as images
                          FROM properties p 
                          LEFT JOIN property_images pi ON p.id = pi.property_id 
                          WHERE 1=1";
                
                $countQuery = "SELECT COUNT(DISTINCT p.id) as total FROM properties p WHERE 1=1";
                $params = [];
                
                // Apply filters
                if (!empty($_GET['status'])) {
                    $query .= " AND p.status = :status";
                    $countQuery .= " AND p.status = :status";
                    $params[':status'] = $_GET['status'];
                }
                
                if (!empty($_GET['building_type'])) {
                    $query .= " AND p.building_type = :building_type";
                    $countQuery .= " AND p.building_type = :building_type";
                    $params[':building_type'] = $_GET['building_type'];
                }
                
                // IMPORTANT: Handle new_build filter
                if (!empty($_GET['new_build'])) {
                    $query .= " AND p.new_build = :new_build";
                    $countQuery .= " AND p.new_build = :new_build";
                    $params[':new_build'] = $_GET['new_build'];
                }
                
                if (!empty($_GET['city'])) {
                    $query .= " AND p.city = :city";
                    $countQuery .= " AND p.city = :city";
                    $params[':city'] = $_GET['city'];
                }
                
                if (!empty($_GET['rooms'])) {
                    $query .= " AND p.rooms = :rooms";
                    $countQuery .= " AND p.rooms = :rooms";
                    $params[':rooms'] = $_GET['rooms'];
                }
                
                // Price range filters
                if (!empty($_GET['price_min'])) {
                    $query .= " AND p.price >= :price_min";
                    $countQuery .= " AND p.price >= :price_min";
                    $params[':price_min'] = $_GET['price_min'];
                }
                
                if (!empty($_GET['price_max'])) {
                    $query .= " AND p.price <= :price_max";
                    $countQuery .= " AND p.price <= :price_max";
                    $params[':price_max'] = $_GET['price_max'];
                }
                
                // Additional filters
                if (!empty($_GET['property_id_code'])) {
                    $query .= " AND p.property_id_code LIKE :property_id_code";
                    $countQuery .= " AND p.property_id_code LIKE :property_id_code";
                    $params[':property_id_code'] = '%' . $_GET['property_id_code'] . '%';
                }
                
                if (!empty($_GET['size_min'])) {
                    $query .= " AND p.size_ownership_doc >= :size_min";
                    $countQuery .= " AND p.size_ownership_doc >= :size_min";
                    $params[':size_min'] = $_GET['size_min'];
                }
                
                if (!empty($_GET['overall_condition'])) {
                    $query .= " AND p.overall_condition = :overall_condition";
                    $countQuery .= " AND p.overall_condition = :overall_condition";
                    $params[':overall_condition'] = $_GET['overall_condition'];
                }
                
                // Boolean filters
                if (isset($_GET['furnished']) && $_GET['furnished'] === 'true') {
                    $query .= " AND p.furnished = 1";
                    $countQuery .= " AND p.furnished = 1";
                }
                
                if (isset($_GET['garden']) && $_GET['garden'] === 'true') {
                    $query .= " AND p.garden = 1";
                    $countQuery .= " AND p.garden = 1";
                }
                
                if (isset($_GET['terrace']) && $_GET['terrace'] === 'true') {
                    $query .= " AND p.terrace = 1";
                    $countQuery .= " AND p.terrace = 1";
                }
                
                // Complete the main query
                $query .= " GROUP BY p.id ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
                
                // Execute count query first
                $countStmt = $db->prepare($countQuery);
                foreach ($params as $key => $value) {
                    $countStmt->bindValue($key, $value);
                }
                $countStmt->execute();
                $total = $countStmt->fetch()['total'];
                
                // Execute main query
                $stmt = $db->prepare($query);
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                
                $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Process images for each property
                foreach ($properties as &$property) {
                    if ($property['images']) {
                        $property['images'] = explode(',', $property['images']);
                    } else {
                        $property['images'] = [];
                    }
                    
                    // Add computed fields for display
                    $property['is_new_build'] = (bool)$property['new_build'];
                    $property['formatted_price'] = '€' . number_format($property['price'], 0, ',', '.');
                }
                
                $response = [
                    'properties' => $properties,
                    'has_more' => ($offset + $limit) < $total,
                    'total' => (int)$total,
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit)
                ];
                
                http_response_code(200);
                echo json_encode($response);
                
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'error' => 'Database error: ' . $e->getMessage(),
                    'properties' => [],
                    'has_more' => false,
                    'total' => 0
                ]);
            }
        }
        break;

    case 'POST':
        // Authentication required for admin operations
        session_start();
        if(!isset($_SESSION['admin_id'])) {
            http_response_code(401);
            echo json_encode(array("message" => "Unauthorized - Please login to admin panel"));
            break;
        }
        
        include_once '../models/Property.php';
        $property = new Property($db);
        
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->title) && !empty($data->price)) {
            $property->title = $data->title;
            $property->description = $data->description ?? '';
            $property->price = $data->price;
            $property->status = $data->status ?? 'for_sale';
            $property->new_build = $data->new_build ?? 0;
            $property->property_id_code = $data->property_id_code ?? '';
            $property->size_ownership_doc = $data->size_ownership_doc ?? null;
            $property->rooms = isset($data->rooms) && $data->rooms !== '' ? (int)$data->rooms : null;
            $property->building_type = $data->building_type ?? '';
            $property->city = $data->city ?? '';
            $property->overall_condition = $data->overall_condition ?? '';
            $property->accessibility = $data->accessibility ?? '';
            $property->building_material = $data->building_material ?? '';
            $property->furnished = $data->furnished ?? 0;
            $property->airbnb_suitable = $data->airbnb_suitable ?? 0;
            $property->insulation = $data->insulation ?? 0;
            $property->special_offers = $data->special_offers ?? '';
            $property->view = $data->view ?? '';
            $property->orientation = $data->orientation ?? '';
            $property->noise_level = $data->noise_level ?? '';
            $property->floor_level = $data->floor_level ?? '';
            $property->garden = $data->garden ?? 0;
            $property->terrace = $data->terrace ?? 0;
            $property->parking = $data->parking ?? 0;
            $property->utilities = $data->utilities ?? 0;
            $property->wheelchair_access = $data->wheelchair_access ?? 0;

            $property_id = $property->create();

            if($property_id) {
                http_response_code(201);
                echo json_encode(array("message" => "Property created.", "id" => $property_id));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create property."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create property. Data is incomplete."));
        }
        break;

    case 'PUT':
        // Authentication required for admin operations
        session_start();
        if(!isset($_SESSION['admin_id'])) {
            http_response_code(401);
            echo json_encode(array("message" => "Unauthorized - Please login to admin panel"));
            break;
        }
        
        include_once '../models/Property.php';
        $property = new Property($db);
        
        $data = json_decode(file_get_contents("php://input"));
        $property->id = $_GET['id'] ?? $data->id;

        if(!empty($property->id) && !empty($data->title) && !empty($data->price)) {
            $property->title = $data->title;
            $property->description = $data->description ?? '';
            $property->price = $data->price;
            $property->status = $data->status ?? 'for_sale';
            $property->new_build = $data->new_build ?? 0;
            $property->property_id_code = $data->property_id_code ?? '';
            $property->size_ownership_doc = $data->size_ownership_doc ?? null;
            $property->rooms = isset($data->rooms) && $data->rooms !== '' ? (int)$data->rooms : null;
            $property->building_type = $data->building_type ?? '';
            $property->city = $data->city ?? '';
            $property->overall_condition = $data->overall_condition ?? '';
            $property->accessibility = $data->accessibility ?? '';
            $property->building_material = $data->building_material ?? '';
            $property->furnished = $data->furnished ?? 0;
            $property->airbnb_suitable = $data->airbnb_suitable ?? 0;
            $property->insulation = $data->insulation ?? 0;
            $property->special_offers = $data->special_offers ?? '';
            $property->view = $data->view ?? '';
            $property->orientation = $data->orientation ?? '';
            $property->noise_level = $data->noise_level ?? '';
            $property->floor_level = $data->floor_level ?? '';
            $property->garden = $data->garden ?? 0;
            $property->terrace = $data->terrace ?? 0;
            $property->parking = $data->parking ?? 0;
            $property->utilities = $data->utilities ?? 0;
            $property->wheelchair_access = $data->wheelchair_access ?? 0;

            if($property->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Property updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update property."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update property. Data is incomplete."));
        }
        break;

    case 'DELETE':
        // Authentication required for admin operations
        session_start();
        if(!isset($_SESSION['admin_id'])) {
            http_response_code(401);
            echo json_encode(array("message" => "Unauthorized - Please login to admin panel"));
            break;
        }
        
        include_once '../models/Property.php';
        $property = new Property($db);
        
        $property->id = $_GET['id'] ?? null;

        if(!empty($property->id)) {
            if($property->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Property deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete property."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to delete property. ID is required."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>