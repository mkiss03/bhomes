<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/functions.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Csak POST kérések engedélyezettek');
    }
    
    // Get search parameters
    $filters = [];
    $searchFields = [
        'location', 'status', 'rooms', 'building_type', 'furnished',
        'price_min', 'price_max', 'garden', 'terrace', 'parking', 'elevator'
    ];
    
    foreach ($searchFields as $field) {
        if (!empty($_POST[$field])) {
            $filters[$field] = sanitizeInput($_POST[$field]);
        }
    }
    
    // Create cache key
    $cacheKey = 'search_' . md5(serialize($filters));
    $cachedResults = getCached($cacheKey, 300); // 5 minutes cache
    
    if ($cachedResults !== false) {
        echo json_encode($cachedResults);
        exit;
    }
    
    // Perform search
    $properties = searchProperties($filters);
    
    // Format properties for JSON response
    $formattedProperties = [];
    foreach ($properties as $property) {
        $images = getPropertyImages($property['id']);
        $mainImage = !empty($images) ? $images[0] : 'assets/img/placeholder.jpg';
        
        $formattedProperties[] = [
            'id' => $property['id'],
            'reference_code' => $property['reference_code'],
            'location' => $property['location'],
            'price' => $property['price'],
            'status' => $property['status'],
            'rooms' => $property['rooms'],
            'size_lap_alap' => $property['size_lap_alap'],
            'plot_size' => $property['plot_size'],
            'building_type' => $property['building_type'],
            'description' => $property['description'],
            'furnished' => (bool)$property['furnished'],
            'garden' => (bool)$property['garden'],
            'terrace' => (bool)$property['terrace'],
            'parking' => (bool)$property['parking'],
            'elevator' => (bool)$property['elevator'],
            'image' => $mainImage,
            'created_at' => $property['created_at'],
            'features' => getPropertyFeatures($property)
        ];
    }
    
    $result = [
        'success' => true,
        'properties' => $formattedProperties,
        'count' => count($formattedProperties),
        'filters' => $filters
    ];
    
    // Cache results
    setCache($cacheKey, $result, 300);
    
    // Log search activity
    logActivity('PROPERTY_SEARCH', 'Filters: ' . json_encode($filters) . ', Results: ' . count($formattedProperties));
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>