<?php
// api/login.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Admin.php';

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->username) && !empty($data->password)) {
        if($admin->login($data->username, $data->password)) {
            $_SESSION['admin_id'] = $admin->id;
            $_SESSION['admin_username'] = $admin->username;
            
            http_response_code(200);
            echo json_encode(array(
                "message" => "Login successful.",
                "admin" => array(
                    "id" => $admin->id,
                    "username" => $admin->username,
                    "email" => $admin->email
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Invalid credentials."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Username and password are required."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
    ?>