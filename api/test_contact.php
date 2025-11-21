<?php
// api/test_contact.php - Diagnostic script for contact form
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");

$diagnostics = [];

// 1. Check PHP version
$diagnostics['php_version'] = phpversion();

// 2. Check file paths
$base_path = dirname(__DIR__);
$diagnostics['base_path'] = $base_path;
$diagnostics['current_dir'] = __DIR__;
$diagnostics['script_path'] = __FILE__;

// 3. Check if config file exists
$config_path = $base_path . '/config/database.php';
$diagnostics['config_path'] = $config_path;
$diagnostics['config_exists'] = file_exists($config_path);

// 4. Check if model file exists
$model_path = $base_path . '/models/Contact.php';
$diagnostics['model_path'] = $model_path;
$diagnostics['model_exists'] = file_exists($model_path);

// 5. Try to include and connect to database
if (file_exists($config_path)) {
    try {
        include_once $config_path;
        $diagnostics['database_class_loaded'] = class_exists('Database');

        if (class_exists('Database')) {
            $database = new Database();
            $db = $database->getConnection();
            $diagnostics['database_connected'] = ($db !== null);

            if ($db) {
                // Test query
                $query = "SELECT COUNT(*) as count FROM contacts";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $diagnostics['contacts_count'] = $result['count'];
            }
        }
    } catch (Exception $e) {
        $diagnostics['database_error'] = $e->getMessage();
    }
}

// 6. Check if Contact model loads
if (file_exists($model_path)) {
    try {
        include_once $model_path;
        $diagnostics['contact_class_loaded'] = class_exists('Contact');
    } catch (Exception $e) {
        $diagnostics['model_error'] = $e->getMessage();
    }
}

// 7. Check POST data handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents("php://input");
    $diagnostics['received_post'] = true;
    $diagnostics['raw_input_length'] = strlen($raw_input);

    $data = json_decode($raw_input);
    $diagnostics['json_decoded'] = ($data !== null);

    if ($data) {
        $diagnostics['has_name'] = !empty($data->name);
        $diagnostics['has_email'] = !empty($data->email);
        $diagnostics['has_message'] = !empty($data->message);
    }
}

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
?>
