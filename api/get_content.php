<?php
// api/get_content.php
// Public endpoint for fetching content without authentication
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    // Try different path combinations
    $config_loaded = false;
    $model_loaded = false;
    
    // Try to load config
    if (file_exists('../config/database.php')) {
        include_once '../config/database.php';
        $config_loaded = true;
    } elseif (file_exists('config/database.php')) {
        include_once 'config/database.php';
        $config_loaded = true;
    } elseif (file_exists('./config/database.php')) {
        include_once './config/database.php';
        $config_loaded = true;
    }
    
    if (!$config_loaded) {
        throw new Exception("Could not load database config");
    }
    
    // Try to load Content model
    if (file_exists('../models/Content.php')) {
        include_once '../models/Content.php';
        $model_loaded = true;
    } elseif (file_exists('models/Content.php')) {
        include_once 'models/Content.php';
        $model_loaded = true;
    } elseif (file_exists('./models/Content.php')) {
        include_once './models/Content.php';
        $model_loaded = true;
    }
    
    if (!$model_loaded) {
        throw new Exception("Could not load Content model");
    }

    $database = new Database();
    $db = $database->getConnection();
    $content = new Content($db);

    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['section_key'])) {
            // Get single content section by key
            $result = $content->readByKey($_GET['section_key']);
            
            if($result) {
                // Return only the content value for public use
                http_response_code(200);
                echo json_encode(array(
                    'section_key' => $result['section_key'],
                    'content_value' => $result['content_value'],
                    'content_type' => $result['content_type']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Content not found."));
            }
        } else if(isset($_GET['page'])) {
            // Get all content for a specific page
            $page = $_GET['page'];
            $section_group = $_GET['section_group'] ?? null;
            $grouped = isset($_GET['grouped']) && $_GET['grouped'] === 'true';
            
            if($grouped) {
                $result = $content->getContentByPageGrouped($page);
            } else {
                $result = $content->readAll($page, $section_group, true);
            }
            
            // Format for public consumption
            $formatted_result = [];
            
            if($grouped) {
                foreach($result as $group_name => $sections) {
                    $formatted_result[$group_name] = [];
                    foreach($sections as $section) {
                        $formatted_result[$group_name][$section['section_key']] = [
                            'content_value' => $section['content_value'],
                            'content_type' => $section['content_type']
                        ];
                    }
                }
            } else {
                foreach($result as $section) {
                    $formatted_result[$section['section_key']] = [
                        'content_value' => $section['content_value'],
                        'content_type' => $section['content_type']
                    ];
                }
            }
            
            http_response_code(200);
            echo json_encode($formatted_result);
        } else if(isset($_GET['multiple'])) {
            // Get multiple content sections by keys
            $keys = explode(',', $_GET['multiple']);
            $result = [];
            
            foreach($keys as $key) {
                $key = trim($key);
                $section = $content->readByKey($key);
                if($section) {
                    $result[$key] = [
                        'content_value' => $section['content_value'],
                        'content_type' => $section['content_type']
                    ];
                }
            }
            
            http_response_code(200);
            echo json_encode($result);
        } else {
            // Return error for invalid request
            http_response_code(400);
            echo json_encode(array("message" => "Invalid request. Please specify 'section_key', 'page', or 'multiple' parameter."));
        }
    } else {
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "message" => "Internal server error",
        "error" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ));
}
?>