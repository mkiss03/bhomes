<?php
// STANDALONE Contact Form Handler - NO INCLUDES, NO DEPENDENCIES
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to user
ini_set('log_errors', 1);

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

try {
    // Get POST data
    $input = file_get_contents("php://input");
    $data = json_decode($input);

    // Validate JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg());
    }

    // Validate required fields
    if (empty($data->name) || empty($data->email) || empty($data->message)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Name, email, and message are required"
        ]);
        exit;
    }

    // Sanitize inputs
    $name = strip_tags(trim($data->name));
    $email = filter_var(trim($data->email), FILTER_SANITIZE_EMAIL);
    $phone = isset($data->phone) ? strip_tags(trim($data->phone)) : '';
    $message = strip_tags(trim($data->message));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid email address"
        ]);
        exit;
    }

    // Database connection - DIRECT, NO INCLUDES
    $db_host = 'mysql.rackhost.hu';
    $db_name = 'c88384bhe';
    $db_user = 'c88384eszti';
    $db_pass = 'Eszter2009';

    // Connect to database
    $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

    // Insert contact into database
    $sql = "INSERT INTO contacts (name, email, phone, message, created_at)
            VALUES (:name, :email, :phone, :message, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':message' => $message
    ]);

    // Success response
    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Message sent successfully!"
    ]);

} catch (PDOException $e) {
    // Database error
    error_log("Contact form DB error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);

} catch (Exception $e) {
    // General error
    error_log("Contact form error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error",
        "error" => $e->getMessage()
    ]);
}
?>
