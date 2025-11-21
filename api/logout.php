<?php 
// api/logout.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_destroy();
    http_response_code(200);
    echo json_encode(array("message" => "Logout successful."));
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}

?>