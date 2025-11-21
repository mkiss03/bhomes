<?php
// api/contacts.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if(!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

include_once '../config/database.php';
include_once '../models/Contact.php';

$database = new Database();
$db = $database->getConnection();
$contact = new Contact($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        try {
            $contacts = $contact->readAll();
            http_response_code(200);
            echo json_encode($contacts);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => "Error loading contacts: " . $e->getMessage()));
        }
        break;

    case 'DELETE':
        if(isset($_GET['id'])) {
            $contact->id = $_GET['id'];
            
            if($contact->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Contact deleted successfully."));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Unable to delete contact."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Contact ID is required."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>