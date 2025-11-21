<?php
echo "<h3>Content Model Debug</h3>";

// Check if file exists
$content_path = '../models/Content.php';
echo "File exists: " . (file_exists($content_path) ? 'YES' : 'NO') . "<br>";
echo "File readable: " . (is_readable($content_path) ? 'YES' : 'NO') . "<br>";
echo "File size: " . filesize($content_path) . " bytes<br>";

// Try to read the file
echo "<h4>File contents (first 500 chars):</h4>";
echo "<pre>" . htmlspecialchars(substr(file_get_contents($content_path), 0, 500)) . "</pre>";

// Try to include it
echo "<h4>Include test:</h4>";
try {
    include_once $content_path;
    echo "✅ Include successful<br>";
    
    // Check if class exists
    if (class_exists('Content')) {
        echo "✅ Content class found<br>";
        
        // Try database connection
        include_once '../config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            echo "✅ Database connected<br>";
            
            // Try to create Content instance
            $content = new Content($db);
            echo "✅ Content object created successfully<br>";
        } else {
            echo "❌ Database connection failed<br>";
        }
    } else {
        echo "❌ Content class not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Include failed: " . $e->getMessage() . "<br>";
}
?>