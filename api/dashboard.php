<?php 
// api/dashboard.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if(!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // Get dashboard statistics
        $stats = array();

        // Total properties
        $query = "SELECT COUNT(*) as total FROM properties";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_properties'] = $result['total'] ?? 0;

        // Active listings (for sale/rent)
        $query = "SELECT COUNT(*) as total FROM properties WHERE status IN ('for_sale', 'for_rent')";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['active_listings'] = $result['total'] ?? 0;

        // Recently added (last 30 days)
        $query = "SELECT COUNT(*) as total FROM properties WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['recently_added'] = $result['total'] ?? 0;

        // Contact messages - check if contacts table exists
        try {
            $query = "SELECT COUNT(*) as total FROM contacts";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['contact_messages'] = $result['total'] ?? 0;
        } catch (Exception $e) {
            // If contacts table doesn't exist, set to 0
            $stats['contact_messages'] = 0;
        }

        // Recent properties
        try {
            $query = "SELECT id, title, price, status, created_at FROM properties ORDER BY created_at DESC LIMIT 5";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stats['recent_properties'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $stats['recent_properties'] = array();
        }

        http_response_code(200);
        echo json_encode($stats);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error loading dashboard: " . $e->getMessage()));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>