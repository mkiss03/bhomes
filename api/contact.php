<?php
// api/contact.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    // Determine the base path dynamically
    $base_path = dirname(__DIR__);

    // Load config with absolute path
    $config_path = $base_path . '/config/database.php';
    $model_path = $base_path . '/models/Contact.php';

    if (!file_exists($config_path)) {
        throw new Exception("Config file not found at: " . $config_path);
    }

    if (!file_exists($model_path)) {
        throw new Exception("Contact model not found at: " . $model_path);
    }

    include_once $config_path;
    include_once $model_path;

    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception("Database connection failed");
    }

    $contact = new Contact($db);

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->name) && !empty($data->email) && !empty($data->message)) {
            $contact->name = htmlspecialchars(strip_tags($data->name));
            $contact->email = htmlspecialchars(strip_tags($data->email));
            $contact->phone = htmlspecialchars(strip_tags($data->phone ?? ''));
            $contact->message = htmlspecialchars(strip_tags($data->message));

            if($contact->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Contact message sent successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to send message.", "debug" => "create() returned false"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to send message. Data is incomplete."));
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