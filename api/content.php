<?php
// api/content.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    $content = new Content($db);
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method) {
        case 'GET':
            if(isset($_GET['section_key'])) {
                // Get single content section by key
                $result = $content->readByKey($_GET['section_key']);
                
                if($result) {
                    http_response_code(200);
                    echo json_encode($result);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "Content section not found."));
                }
            } else if(isset($_GET['id'])) {
                // Get single content section by ID (admin only)
                if(!isset($_SESSION['admin_id'])) {
                    http_response_code(401);
                    echo json_encode(array("message" => "Unauthorized"));
                    break;
                }
                
                $content->id = $_GET['id'];
                $result = $content->readOne();
                
                if($result) {
                    http_response_code(200);
                    echo json_encode($result);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "Content section not found."));
                }
            } else if(isset($_GET['page'])) {
                // Get content sections for a specific page
                $page = $_GET['page'];
                $section_group = $_GET['section_group'] ?? null;
                $grouped = isset($_GET['grouped']) && $_GET['grouped'] === 'true';
                
                if($grouped) {
                    $result = $content->getContentByPageGrouped($page);
                } else {
                    $result = $content->readAll($page, $section_group);
                }
                
                http_response_code(200);
                echo json_encode($result);
            } else if(isset($_GET['pages'])) {
                // Get all pages (admin only)
                if(!isset($_SESSION['admin_id'])) {
                    http_response_code(401);
                    echo json_encode(array("message" => "Unauthorized"));
                    break;
                }
                
                $pages = $content->getPages();
                http_response_code(200);
                echo json_encode($pages);
            } else if(isset($_GET['groups']) && isset($_GET['for_page'])) {
                // Get section groups for a page (admin only)
                if(!isset($_SESSION['admin_id'])) {
                    http_response_code(401);
                    echo json_encode(array("message" => "Unauthorized"));
                    break;
                }
                
                $groups = $content->getSectionGroups($_GET['for_page']);
                http_response_code(200);
                echo json_encode($groups);
            } else {
                // Get all content sections (admin only)
                if(!isset($_SESSION['admin_id'])) {
                    http_response_code(401);
                    echo json_encode(array("message" => "Unauthorized"));
                    break;
                }
                
                $active_only = !isset($_GET['include_inactive']);
                $result = $content->readAll(null, null, $active_only);
                
                http_response_code(200);
                echo json_encode($result);
            }
            break;

        case 'POST':
            // Admin only for creating content
            if(!isset($_SESSION['admin_id'])) {
                http_response_code(401);
                echo json_encode(array("message" => "Unauthorized"));
                break;
            }

            $data = json_decode(file_get_contents("php://input"));

            if(isset($data->batch_update) && $data->batch_update === true) {
                // Batch update multiple content sections
                if(!empty($data->updates)) {
                    if($content->batchUpdate($data->updates)) {
                        http_response_code(200);
                        echo json_encode(array("message" => "Content updated successfully."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("message" => "Unable to update content."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "Updates data is required."));
                }
            } else {
                // Create new content section
                if(!empty($data->section_key) && !empty($data->section_name) && !empty($data->content_value)) {
                    $content->section_key = $data->section_key;
                    $content->section_name = $data->section_name;
                    $content->content_type = $data->content_type ?? 'text';
                    $content->content_value = $data->content_value;
                    $content->page = $data->page ?? 'home';
                    $content->section_group = $data->section_group ?? null;
                    $content->display_order = $data->display_order ?? 0;
                    $content->is_active = $data->is_active ?? 1;

                    $content_id = $content->create();

                    if($content_id) {
                        http_response_code(201);
                        echo json_encode(array("message" => "Content section created.", "id" => $content_id));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("message" => "Unable to create content section."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "Unable to create content section. Data is incomplete."));
                }
            }
            break;

        case 'PUT':
            // Admin only for updating content
            if(!isset($_SESSION['admin_id'])) {
                http_response_code(401);
                echo json_encode(array("message" => "Unauthorized"));
                break;
            }

            $data = json_decode(file_get_contents("php://input"));

            if(isset($_GET['section_key'])) {
                // Quick update by section key (only content value)
                if(!empty($data->content_value)) {
                    $content->section_key = $_GET['section_key'];
                    $content->content_value = $data->content_value;
                    $content->content_type = $data->content_type ?? 'text';

                    if($content->updateContent()) {
                        http_response_code(200);
                        echo json_encode(array("message" => "Content updated successfully."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("message" => "Unable to update content."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "Content value is required."));
                }
            } else {
                // Full update by ID
                $content->id = $_GET['id'] ?? $data->id;

                if(!empty($content->id) && !empty($data->section_name) && !empty($data->content_value)) {
                    $content->section_name = $data->section_name;
                    $content->content_type = $data->content_type ?? 'text';
                    $content->content_value = $data->content_value;
                    $content->page = $data->page ?? 'home';
                    $content->section_group = $data->section_group ?? null;
                    $content->display_order = $data->display_order ?? 0;
                    $content->is_active = $data->is_active ?? 1;

                    if($content->update()) {
                        http_response_code(200);
                        echo json_encode(array("message" => "Content section updated."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("message" => "Unable to update content section."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "Unable to update content section. Data is incomplete."));
                }
            }
            break;

        case 'DELETE':
            // Admin only for deleting content
            if(!isset($_SESSION['admin_id'])) {
                http_response_code(401);
                echo json_encode(array("message" => "Unauthorized"));
                break;
            }

            $content->id = $_GET['id'] ?? null;

            if(!empty($content->id)) {
                if($content->delete()) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Content section deleted."));
                } else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Unable to delete content section."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to delete content section. ID is required."));
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(array("message" => "Method not allowed."));
            break;
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