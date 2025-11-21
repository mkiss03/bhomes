<?php
// api/contact.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Log errors to file for debugging
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/error_log.txt');

try {
    // Determine the base path dynamically (works in different environments)
    $base_path = dirname(__DIR__);

    // Alternative: If dirname(__DIR__) doesn't work, try $_SERVER['DOCUMENT_ROOT']
    if (!is_dir($base_path) || !file_exists($base_path . '/config')) {
        $base_path = $_SERVER['DOCUMENT_ROOT'];
    }

    // Load config with absolute path
    $config_path = $base_path . '/config/database.php';
    $model_path = $base_path . '/models/Contact.php';

    // Verify files exist
    if (!file_exists($config_path)) {
        error_log("Config not found. Tried: " . $config_path);
        throw new Exception("Config file not found. Base path: " . $base_path);
    }

    if (!file_exists($model_path)) {
        error_log("Model not found. Tried: " . $model_path);
        throw new Exception("Contact model not found. Base path: " . $base_path);
    }

    // Include required files
    require_once $config_path;
    require_once $model_path;

    // Verify classes are loaded
    if (!class_exists('Database')) {
        throw new Exception("Database class not loaded");
    }

    if (!class_exists('Contact')) {
        throw new Exception("Contact class not loaded");
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception("Database connection returned null");
    }

    // Create Contact instance
    $contact = new Contact($db);

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get raw POST data
        $raw_input = file_get_contents("php://input");

        if (empty($raw_input)) {
            throw new Exception("No POST data received");
        }

        // Decode JSON
        $data = json_decode($raw_input);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON decode error: " . json_last_error_msg());
        }

        // Validate required fields
        if(!empty($data->name) && !empty($data->email) && !empty($data->message)) {
            // Sanitize inputs
            $contact->name = htmlspecialchars(strip_tags($data->name));
            $contact->email = htmlspecialchars(strip_tags($data->email));
            $contact->phone = htmlspecialchars(strip_tags($data->phone ?? ''));
            $contact->message = htmlspecialchars(strip_tags($data->message));

            // Log the attempt
            error_log("Attempting to create contact for: " . $contact->email);

            // Try to create contact
            if($contact->create()) {
                http_response_code(201);
                echo json_encode(array(
                    "success" => true,
                    "message" => "Contact message sent successfully."
                ));
            } else {
                error_log("Contact create() returned false for: " . $contact->email);
                http_response_code(503);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Unable to send message. Database insert failed.",
                    "debug" => "create() returned false"
                ));
            }
        } else {
            // Missing required fields
            $missing = [];
            if (empty($data->name)) $missing[] = 'name';
            if (empty($data->email)) $missing[] = 'email';
            if (empty($data->message)) $missing[] = 'message';

            http_response_code(400);
            echo json_encode(array(
                "success" => false,
                "message" => "Unable to send message. Data is incomplete.",
                "missing_fields" => $missing
            ));
        }
    } else {
        http_response_code(405);
        echo json_encode(array(
            "success" => false,
            "message" => "Method not allowed. Only POST requests are accepted."
        ));
    }
} catch (Exception $e) {
    // Log the error
    error_log("Contact form error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    error_log("Stack trace: " . $e->getTraceAsString());

    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Internal server error",
        "error" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine(),
        "trace" => $e->getTraceAsString()
    ));
}
?>