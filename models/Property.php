<?php
// models/Property.php - Updated version with new fields and filters

const BUILDING_TYPES = [
    'apartment' => 'Lakás / Apartman',
    'house' => 'Villa / Ház / Ikerház'
];

// Status Types (updated)
const STATUS_TYPES = [
    'for_sale' => 'Eladó',
    'new_build' => 'Új építésű'
];

// Cities (including new Torrevieja)
const CITIES = [
    'Alicante' => 'Alicante',
    'Benidorm' => 'Benidorm',
    'Denia' => 'Denia',
    'Calpe' => 'Calpe',
    'Altea' => 'Altea',
    'Torrevieja' => 'Torrevieja',
    'Finestrat' => 'Finestrat'
];

// Validation functions
function validateBuildingType($type) {
    return array_key_exists($type, BUILDING_TYPES);
}

function validateStatus($status) {
    return array_key_exists($status, STATUS_TYPES);
}

function validateCity($city) {
    return array_key_exists($city, CITIES);
}

function validateRooms($rooms) {
    return is_numeric($rooms) && $rooms >= 0 && $rooms <= 20;
}

function validatePrice($price) {
    return is_numeric($price) && $price >= 0;
}

class Property {
    private $conn;
    private $table_name = "properties";

    // Add the new properties
    public $id;
    public $title;
    public $description;
    public $price;
    public $status;
    public $property_id_code;
    public $size_ownership_doc;
    public $rooms;  // NEW FIELD
    public $building_type;
    public $city;  // RENAMED FROM neighborhood
    public $overall_condition;
    public $accessibility;
    public $building_material;
    public $furnished;
    public $airbnb_suitable;
    public $insulation;
    public $special_offers;
    public $view;
    public $orientation;
    public $noise_level;
    public $floor_level;
    public $garden;
    public $terrace;
    public $parking;
    public $utilities;
    public $wheelchair_access;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($filters = [], $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT p.*, GROUP_CONCAT(i.image_path ORDER BY i.image_order ASC) as images 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN images i ON p.id = i.property_id 
                  WHERE 1=1";
        
        $params = [];
        
        // Map old parameter names to new ones for backward compatibility
        if (!empty($filters['neighborhood'])) {
            $filters['city'] = $filters['neighborhood'];
        }
        
        // Apply filters with new field names
        if (!empty($filters['status'])) {
            $query .= " AND p.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['building_type']) || !empty($filters['type'])) {
            $type = $filters['building_type'] ?? $filters['type'];
            $query .= " AND p.building_type = :building_type";
            $params[':building_type'] = $type;
        }
        
        if (!empty($filters['city'])) {
            $query .= " AND p.city = :city";
            $params[':city'] = $filters['city'];
        }
        
        if (!empty($filters['rooms'])) {
            $query .= " AND p.rooms = :rooms";
            $params[':rooms'] = $filters['rooms'];
        }
        
        // Price range filters
        if (!empty($filters['price_min'])) {
            $query .= " AND p.price >= :price_min";
            $params[':price_min'] = $filters['price_min'];
        }
        
        if (!empty($filters['price_max'])) {
            $query .= " AND p.price <= :price_max";
            $params[':price_max'] = $filters['price_max'];
        }
        
        // Existing filters
        if (!empty($filters['property_id_code'])) {
            $query .= " AND p.property_id_code LIKE :property_id_code";
            $params[':property_id_code'] = '%' . $filters['property_id_code'] . '%';
        }
        
        if (!empty($filters['size_min'])) {
            $query .= " AND p.size_ownership_doc >= :size_min";
            $params[':size_min'] = $filters['size_min'];
        }
        
        if (!empty($filters['overall_condition'])) {
            $query .= " AND p.overall_condition = :overall_condition";
            $params[':overall_condition'] = $filters['overall_condition'];
        }
        
        
        if (isset($filters['furnished']) && $filters['furnished'] === 'true') {
            $query .= " AND p.furnished = 1";
        }
        
        if (isset($filters['garden']) && $filters['garden'] === 'true') {
            $query .= " AND p.garden = 1";
        }
        
        if (isset($filters['terrace']) && $filters['terrace'] === 'true') {
            $query .= " AND p.terrace = 1";
        }
        
        $query .= " GROUP BY p.id ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $properties = $stmt->fetchAll();
        
        // Format images for each property
        foreach ($properties as &$property) {
            if ($property['images']) {
                $property['images'] = explode(',', $property['images']);
            } else {
                $property['images'] = [];
            }
        }
        
        // Count query with same filters for pagination
        $countQuery = "SELECT COUNT(DISTINCT p.id) as total FROM " . $this->table_name . " p WHERE 1=1";
        
        // Apply same filters to count query
        if (!empty($filters['status'])) {
            $countQuery .= " AND p.status = :status";
        }
        if (!empty($filters['building_type']) || !empty($filters['type'])) {
            $countQuery .= " AND p.building_type = :building_type";
        }
        if (!empty($filters['city'])) {
            $countQuery .= " AND p.city = :city";
        }
        if (!empty($filters['rooms'])) {
            $countQuery .= " AND p.rooms = :rooms";
        }
        if (!empty($filters['price_min'])) {
            $countQuery .= " AND p.price >= :price_min";
        }
        if (!empty($filters['price_max'])) {
            $countQuery .= " AND p.price <= :price_max";
        }
        if (!empty($filters['property_id_code'])) {
            $countQuery .= " AND p.property_id_code LIKE :property_id_code";
        }
        if (!empty($filters['size_min'])) {
            $countQuery .= " AND p.size_ownership_doc >= :size_min";
        }
        if (!empty($filters['overall_condition'])) {
            $countQuery .= " AND p.overall_condition = :overall_condition";
        }
        if (isset($filters['furnished']) && $filters['furnished'] === 'true') {
            $countQuery .= " AND p.furnished = 1";
        }
        if (isset($filters['garden']) && $filters['garden'] === 'true') {
            $countQuery .= " AND p.garden = 1";
        }
        if (isset($filters['terrace']) && $filters['terrace'] === 'true') {
            $countQuery .= " AND p.terrace = 1";
        }
        
        $countStmt = $this->conn->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        return [
            'properties' => $properties,
            'has_more' => ($offset + $limit) < $total,
            'total' => $total
        ];
    }

    public function readOne() {
        $query = "SELECT p.*, GROUP_CONCAT(i.image_path ORDER BY i.image_order ASC) as images 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN images i ON p.id = i.property_id 
                  WHERE p.id = ? 
                  GROUP BY p.id 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch();

        if ($row) {
            // Set all properties including new ones
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->status = $row['status'];
            $this->property_id_code = $row['property_id_code'];
            $this->size_ownership_doc = $row['size_ownership_doc'];
            $this->rooms = $row['rooms'];  // NEW
            $this->building_type = $row['building_type'];
            
            // Handle backward compatibility for city/neighborhood
            $this->city = $row['city'] ?? $row['neighborhood'] ?? null;
            
            $this->overall_condition = $row['overall_condition'];
            $this->accessibility = $row['accessibility'];
            $this->building_material = $row['building_material'];
            $this->furnished = $row['furnished'];
            $this->airbnb_suitable = $row['airbnb_suitable'];
            $this->insulation = $row['insulation'];
            $this->special_offers = $row['special_offers'];
            $this->view = $row['view'];
            $this->orientation = $row['orientation'];
            $this->noise_level = $row['noise_level'];
            $this->floor_level = $row['floor_level'];
            $this->garden = $row['garden'];
            $this->terrace = $row['terrace'];
            $this->parking = $row['parking'];
            $this->utilities = $row['utilities'];
            $this->wheelchair_access = $row['wheelchair_access'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            // Ensure backward compatibility in returned data
            if (isset($row['neighborhood']) && !isset($row['city'])) {
                $row['city'] = $row['neighborhood'];
            }
            
            if ($row['images']) {
                $row['images'] = explode(',', $row['images']);
            } else {
                $row['images'] = [];
            }
            
            return $row;
        }
        
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                    SET title=:title, description=:description, price=:price, status=:status,
                        property_id_code=:property_id_code, size_ownership_doc=:size_ownership_doc,
                        rooms=:rooms, building_type=:building_type, city=:city,
                        overall_condition=:overall_condition, accessibility=:accessibility,
                         building_material=:building_material,
                         furnished=:furnished,
                        airbnb_suitable=:airbnb_suitable, insulation=:insulation,
                        special_offers=:special_offers, view=:view, orientation=:orientation,
                        noise_level=:noise_level, floor_level=:floor_level, garden=:garden,
                        terrace=:terrace, parking=:parking, utilities=:utilities,
                        wheelchair_access=:wheelchair_access";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":property_id_code", $this->property_id_code);
        $stmt->bindParam(":size_ownership_doc", $this->size_ownership_doc);
        $stmt->bindParam(":rooms", $this->rooms);  // NEW
        $stmt->bindParam(":building_type", $this->building_type);
        $stmt->bindParam(":city", $this->city);  // RENAMED
        $stmt->bindParam(":overall_condition", $this->overall_condition);
        $stmt->bindParam(":accessibility", $this->accessibility);
        $stmt->bindParam(":building_material", $this->building_material);
        $stmt->bindParam(":furnished", $this->furnished);
        $stmt->bindParam(":airbnb_suitable", $this->airbnb_suitable);
        $stmt->bindParam(":insulation", $this->insulation);
        $stmt->bindParam(":special_offers", $this->special_offers);
        $stmt->bindParam(":view", $this->view);
        $stmt->bindParam(":orientation", $this->orientation);
        $stmt->bindParam(":noise_level", $this->noise_level);
        $stmt->bindParam(":floor_level", $this->floor_level);
        $stmt->bindParam(":garden", $this->garden);
        $stmt->bindParam(":terrace", $this->terrace);
        $stmt->bindParam(":parking", $this->parking);
        $stmt->bindParam(":utilities", $this->utilities);
        $stmt->bindParam(":wheelchair_access", $this->wheelchair_access);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                    SET title = :title, description = :description, price = :price, status = :status,
                        property_id_code = :property_id_code, size_ownership_doc = :size_ownership_doc,
                        rooms = :rooms, building_type = :building_type, city = :city,
                        overall_condition = :overall_condition, accessibility = :accessibility,
                         building_material = :building_material,
                         furnished = :furnished,
                        airbnb_suitable = :airbnb_suitable, insulation = :insulation,
                        special_offers = :special_offers, view = :view, orientation = :orientation,
                        noise_level = :noise_level, floor_level = :floor_level, garden = :garden,
                        terrace = :terrace, parking = :parking, utilities = :utilities,
                        wheelchair_access = :wheelchair_access, updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":property_id_code", $this->property_id_code);
        $stmt->bindParam(":size_ownership_doc", $this->size_ownership_doc);
        $stmt->bindParam(":rooms", $this->rooms);  // NEW
        $stmt->bindParam(":building_type", $this->building_type);
        $stmt->bindParam(":city", $this->city);  // RENAMED
        $stmt->bindParam(":overall_condition", $this->overall_condition);
        $stmt->bindParam(":accessibility", $this->accessibility);
        $stmt->bindParam(":building_material", $this->building_material);
        $stmt->bindParam(":furnished", $this->furnished);
        $stmt->bindParam(":airbnb_suitable", $this->airbnb_suitable);
        $stmt->bindParam(":insulation", $this->insulation);
        $stmt->bindParam(":special_offers", $this->special_offers);  // FIXED: was :special_offerings
        $stmt->bindParam(":view", $this->view);
        $stmt->bindParam(":orientation", $this->orientation);
        $stmt->bindParam(":noise_level", $this->noise_level);
        $stmt->bindParam(":floor_level", $this->floor_level);
        $stmt->bindParam(":garden", $this->garden);
        $stmt->bindParam(":terrace", $this->terrace);
        $stmt->bindParam(":parking", $this->parking);
        $stmt->bindParam(":utilities", $this->utilities);
        $stmt->bindParam(":wheelchair_access", $this->wheelchair_access);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete method remains the same
    public function delete() {
        // First delete all images associated with this property
        $imageQuery = "SELECT image_path FROM images WHERE property_id = ?";
        $imageStmt = $this->conn->prepare($imageQuery);
        $imageStmt->execute([$this->id]);
        $images = $imageStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Delete physical files
        foreach($images as $imagePath) {
            $fullPath = '../' . $imagePath;
            if(file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        
        // Delete image records
        $deleteImagesQuery = "DELETE FROM images WHERE property_id = ?";
        $deleteImagesStmt = $this->conn->prepare($deleteImagesQuery);
        $deleteImagesStmt->execute([$this->id]);
        
        // Delete property
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

     public static function getFilteredProperties($type = '', $newBuildOnly = false, $limit = null) {
        $pdo = Database::getInstance(); // Adjust according to your database connection method
        
        $query = "SELECT p.* FROM properties p WHERE p.status = 'for_sale'";
        $params = [];
        
        // Filter by new build status
        if ($newBuildOnly) {
            $query .= " AND p.new_build = 1";
        }
        
        // Filter by type
        if (!empty($type)) {
            switch ($type) {
                case 'Új építésű villa':
                    $query .= " AND p.new_build = 1 AND p.building_type = 'villa'";
                    break;
                case 'Új építésű apartman':
                    $query .= " AND p.new_build = 1 AND p.building_type = 'apartment'";
                    break;
                case 'Villa':
                case 'Villa / Ház / Ikerház':
                    $query .= " AND p.building_type = 'villa'";
                    break;
                case 'Lakás / Apartman':
                    $query .= " AND p.building_type = 'apartment'";
                    break;
            }
        }
        
        $query .= " ORDER BY p.price ASC";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get new build properties only
     */
    public static function getNewBuildProperties($type = null) {
        return self::getFilteredProperties($type, true);
    }
    
    /**
     * Get property images
     */
    public static function getPropertyImages($propertyId, $limit = null) {
        $pdo = Database::getInstance();
        
        $query = "SELECT * FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, image_id";
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$propertyId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get property features
     */
    public static function getPropertyFeatures($propertyId) {
        $pdo = Database::getInstance();
        
        $stmt = $pdo->prepare("SELECT feature FROM property_features WHERE property_id = ?");
        $stmt->execute([$propertyId]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Get first property image
     */
    public static function getFirstPropertyImage($propertyId) {
        $pdo = Database::getInstance();
        
        $stmt = $pdo->prepare("SELECT image_url FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, image_id LIMIT 1");
        $stmt->execute([$propertyId]);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Get property by ID with all details
     */
    public static function getPropertyWithDetails($propertyId) {
        $pdo = Database::getInstance();
        
        $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
        $stmt->execute([$propertyId]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($property) {
            $property['images'] = self::getPropertyImages($propertyId);
            $property['features'] = self::getPropertyFeatures($propertyId);
        }
        
        return $property;
    }
    
    /**
     * Check if property is new build
     */
    public static function isNewBuild($propertyId) {
        $pdo = Database::getInstance();
        
        $stmt = $pdo->prepare("SELECT new_build FROM properties WHERE id = ?");
        $stmt->execute([$propertyId]);
        
        return (bool)$stmt->fetchColumn();
    }
    
    /**
     * Get new build statistics
     */
    public static function getNewBuildStats() {
        $pdo = Database::getInstance();
        
        $stats = [];
        
        // Total new builds
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE new_build = 1 AND status = 'for_sale'");
        $stmt->execute();
        $stats['total'] = $stmt->fetchColumn();
        
        // New build villas
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE new_build = 1 AND building_type = 'villa' AND status = 'for_sale'");
        $stmt->execute();
        $stats['villas'] = $stmt->fetchColumn();
        
        // New build apartments
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE new_build = 1 AND building_type = 'apartment' AND status = 'for_sale'");
        $stmt->execute();
        $stats['apartments'] = $stmt->fetchColumn();
        
        // Average price
        $stmt = $pdo->prepare("SELECT AVG(price) FROM properties WHERE new_build = 1 AND status = 'for_sale'");
        $stmt->execute();
        $stats['avg_price'] = $stmt->fetchColumn();
        
        return $stats;
    }

}
?>