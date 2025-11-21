<?php 
// api/contact.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Contact.php';

$database = new Database();
$db = $database->getConnection();
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
            echo json_encode(array("message" => "Unable to send message."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to send message. Data is incomplete."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}

?>