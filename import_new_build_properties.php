<?php
// import_new_build_properties.php - Fixed version
error_reporting(E_ALL);
ini_set('display_errors', 1);

function importNewBuildPropertiesFromXML($xmlFile) {
    try {
        // Load XML file
        if (!file_exists($xmlFile)) {
            throw new Exception("XML file not found: $xmlFile");
        }
        
        $xml = simplexml_load_file($xmlFile);
        if (!$xml) {
            throw new Exception("Failed to parse XML file");
        }
        
        // Database connection - FIXED: Added proper quotes
        $pdo = new PDO("mysql:host=mysql.rackhost.hu;dbname=c88384bhe", "c88384eszti", "Eszter2009");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "Database connected successfully!<br>";
        echo "Found " . count($xml->property) . " properties in XML<br><br>";
        
        // First, let's add the missing columns we need for new build properties
        try {
            $pdo->exec("ALTER TABLE properties ADD COLUMN new_build TINYINT(1) DEFAULT 0");
            echo "Added new_build column<br>";
        } catch (Exception $e) {
            echo "new_build column already exists<br>";
        }
        
        try {
            $pdo->exec("ALTER TABLE properties ADD COLUMN ref_code VARCHAR(50)");
            echo "Added ref_code column<br>";
        } catch (Exception $e) {
            echo "ref_code column already exists<br>";
        }
        
        try {
            $pdo->exec("ALTER TABLE properties ADD COLUMN virtual_tour_url TEXT");
            echo "Added virtual_tour_url column<br>";
        } catch (Exception $e) {
            echo "virtual_tour_url column already exists<br>";
        }
        
        try {
            $pdo->exec("ALTER TABLE properties ADD COLUMN latitude DECIMAL(10,8)");
            echo "Added latitude column<br>";
        } catch (Exception $e) {
            echo "latitude column already exists<br>";
        }
        
        try {
            $pdo->exec("ALTER TABLE properties ADD COLUMN longitude DECIMAL(11,8)");
            echo "Added longitude column<br>";
        } catch (Exception $e) {
            echo "longitude column already exists<br>";
        }
        
        try {
            $pdo->exec("ALTER TABLE properties ADD COLUMN currency VARCHAR(10) DEFAULT 'EUR'");
            echo "Added currency column<br>";
        } catch (Exception $e) {
            echo "currency column already exists<br>";
        }
        
        // Create additional tables for features and images
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS property_features (
                id INT AUTO_INCREMENT PRIMARY KEY,
                property_id INT,
                feature VARCHAR(255),
                INDEX idx_property_id (property_id)
            )
        ");
        echo "Created property_features table<br>";
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS property_images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                property_id INT,
                image_id INT,
                image_url TEXT,
                is_primary TINYINT(1) DEFAULT 0,
                INDEX idx_property_id (property_id)
            )
        ");
        echo "Created property_images table<br><br>";
        
        $importCount = 0;
        
        foreach ($xml->property as $property) {
            $propertyId = (int)$property->id;
            
            echo "Processing property ID: $propertyId<br>";
            
            // Check if property already exists
            $checkStmt = $pdo->prepare("SELECT id FROM properties WHERE id = ?");
            $checkStmt->execute([$propertyId]);
            
            // Map XML data to your database structure
            $title = "New Build " . (string)$property->type . " in " . (string)$property->town;
            $description = (string)$property->desc->en; // Using English description
            $status = 'for_sale'; // Since all properties in XML are for sale
            $building_type = strtolower((string)$property->type); // Villa or Apartment
            $city = (string)$property->town;
            
            if ($checkStmt->fetch()) {
                // Update existing property
                $stmt = $pdo->prepare("
                    UPDATE properties SET 
                        title = ?, description = ?, price = ?, status = ?, ref_code = ?,
                        rooms = ?, building_type = ?, city = ?, furnished = ?, garden = ?,
                        terrace = ?, parking = ?, new_build = 1, latitude = ?, longitude = ?,
                        currency = ?, virtual_tour_url = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $title,
                    $description,
                    (float)$property->price,
                    $status,
                    (string)$property->ref,
                    (int)$property->beds,
                    $building_type,
                    $city,
                    1, // Assuming furnished
                    1, // Has garden (most ground floor properties do)
                    1, // Has terrace
                    1, // Has parking
                    (float)$property->location->latitude,
                    (float)$property->location->longitude,
                    (string)$property->currency,
                    (string)$property->virtual_tour_url,
                    $propertyId
                ]);
                echo "Updated property $propertyId<br>";
            } else {
                // Insert new property
                $stmt = $pdo->prepare("
                    INSERT INTO properties (
                        id, title, description, price, status, ref_code, rooms, building_type, 
                        city, furnished, garden, terrace, parking, new_build, latitude, longitude,
                        currency, virtual_tour_url, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ");
                
                $stmt->execute([
                    $propertyId,
                    $title,
                    $description,
                    (float)$property->price,
                    $status,
                    (string)$property->ref,
                    (int)$property->beds,
                    $building_type,
                    $city,
                    1, // Assuming furnished
                    1, // Has garden
                    1, // Has terrace
                    1, // Has parking
                    (float)$property->location->latitude,
                    (float)$property->location->longitude,
                    (string)$property->currency,
                    (string)$property->virtual_tour_url
                ]);
                echo "Inserted property $propertyId<br>";
            }
            
            // Clear existing features and images for this property
            $pdo->prepare("DELETE FROM property_features WHERE property_id = ?")->execute([$propertyId]);
            $pdo->prepare("DELETE FROM property_images WHERE property_id = ?")->execute([$propertyId]);
            
            // Insert features
            if (isset($property->features->feature)) {
                $featureStmt = $pdo->prepare("INSERT INTO property_features (property_id, feature) VALUES (?, ?)");
                foreach ($property->features->feature as $feature) {
                    $featureStmt->execute([$propertyId, (string)$feature]);
                }
                echo "Added features for property $propertyId<br>";
            }
            
            // Insert images
            if (isset($property->images->image)) {
                $imageStmt = $pdo->prepare("INSERT INTO property_images (property_id, image_id, image_url, is_primary) VALUES (?, ?, ?, ?)");
                $imageCount = 0;
                foreach ($property->images->image as $image) {
                    $isPrimary = ($imageCount === 0) ? 1 : 0; // First image is primary
                    $imageStmt->execute([
                        $propertyId, 
                        (int)$image['id'], 
                        (string)$image->url,
                        $isPrimary
                    ]);
                    $imageCount++;
                }
                echo "Added $imageCount images for property $propertyId<br>";
            }
            
            $importCount++;
            echo "<br>";
        }
        
        return $importCount;
        
    } catch (Exception $e) {
        throw new Exception("Import failed: " . $e->getMessage());
    }
}

// Run the import
try {
    $xmlFile = 'new_properties.xml'; // Place your XML file in the root directory
    $imported = importNewBuildPropertiesFromXML($xmlFile);
    echo "<strong>Successfully imported $imported new build properties!</strong><br>";
    echo "<a href='new-build-properties.php'>View New Build Properties</a>";
} catch (Exception $e) {
    echo "<strong>Error:</strong> " . $e->getMessage();
}
?>