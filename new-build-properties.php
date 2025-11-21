<?php
// new-build-properties.php - Simplified version without Property class
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection - update with your actual credentials
try {
    $pdo = new PDO("mysql:host=mysql.rackhost.hu;dbname=c88384bhe", "c88384eszti", "Eszter2009");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get filter parameters
$propertyType = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';

// Build query
$query = "SELECT * FROM properties WHERE new_build = 1 AND status = 'for_sale'";
$params = [];

// Filter by type
if (!empty($propertyType)) {
    switch ($propertyType) {
        case 'Új építésű villa':
            $query .= " AND building_type = 'villa'";
            break;
        case 'Új építésű apartman':
            $query .= " AND building_type = 'apartment'";
            break;
    }
}

$query .= " ORDER BY price ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN building_type = 'villa' THEN 1 ELSE 0 END) as villas,
    SUM(CASE WHEN building_type = 'apartment' THEN 1 ELSE 0 END) as apartments,
    AVG(price) as avg_price
    FROM properties WHERE new_build = 1 AND status = 'for_sale'";
$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute();
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Functions to get images and features
function getFirstPropertyImage($propertyId, $pdo) {
    $stmt = $pdo->prepare("SELECT image_url FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, image_id LIMIT 1");
    $stmt->execute([$propertyId]);
    return $stmt->fetchColumn();
}

function getPropertyFeatures($propertyId, $pdo) {
    $stmt = $pdo->prepare("SELECT feature FROM property_features WHERE property_id = ?");
    $stmt->execute([$propertyId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Új Építésű Ingatlanok - Best Homes España</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 2.5rem;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        
        .page-subtitle {
            font-size: 1.1rem;
            color: #666;
        }
        
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            min-width: 120px;
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c5aa0;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filter-row {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 200px;
        }
        
        .filter-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .filter-group select {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            transition: border-color 0.3s;
        }
        
        .filter-group select:focus {
            border-color: #2c5aa0;
            outline: none;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .results-count {
            font-size: 1.1rem;
            color: #666;
        }
        
        .property-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .property-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .property-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .property-badges {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 2;
        }
        
        .new-build-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .property-details {
            padding: 20px;
        }
        
        .property-price {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        
        .property-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .property-type {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        
        .property-info {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            color: #555;
        }
        
        .property-location {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        
        .property-features {
            margin-bottom: 15px;
        }
        
        .features-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .feature-tag {
            display: inline-block;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.8rem;
            margin: 2px;
            color: #555;
        }
        
        .property-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            text-align: center;
            flex: 1;
        }
        
        .btn-primary {
            background: #2c5aa0;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1a4480;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #2c5aa0;
            color: #2c5aa0;
        }
        
        .btn-outline:hover {
            background: #2c5aa0;
            color: white;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .no-results h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .no-results p {
            color: #999;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                min-width: auto;
            }
            
            .property-grid {
                grid-template-columns: 1fr;
            }
            
            .property-info {
                justify-content: space-between;
            }
            
            .property-actions {
                flex-direction: column;
            }
            
            .stats-bar {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Új Építésű Ingatlanok</h1>
            <p class="page-subtitle">Fedezze fel a legújabb építésű villa és apartman kínálatunkat</p>
            
            <div class="stats-bar">
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['total'] ?></div>
                    <div class="stat-label">Összes új építésű</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['villas'] ?></div>
                    <div class="stat-label">Villák</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['apartments'] ?></div>
                    <div class="stat-label">Apartmanok</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">€<?= number_format($stats['avg_price'], 0, ',', '.') ?></div>
                    <div class="stat-label">Átlagár</div>
                </div>
            </div>
        </div>

        <div class="filters">
            <form method="GET" id="filterForm">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="type">Ingatlantípus:</label>
                        <select name="type" id="type" onchange="this.form.submit()">
                            <option value="">Összes típus</option>
                            <option value="Új építésű villa" <?= ($propertyType === 'Új építésű villa') ? 'selected' : '' ?>>Új építésű villa</option>
                            <option value="Új építésű apartman" <?= ($propertyType === 'Új építésű apartman') ? 'selected' : '' ?>>Új építésű apartman</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="results-header">
            <div class="results-count">
                <?= count($properties) ?> ingatlan található
            </div>
        </div>

        <?php if (!empty($properties)): ?>
            <div class="property-grid">
                <?php foreach ($properties as $property): ?>
                    <?php
                    // Get first image and features for this property
                    $firstImage = getFirstPropertyImage($property['id'], $pdo);
                    $features = getPropertyFeatures($property['id'], $pdo);
                    ?>
                    
                    <div class="property-card">
                        <div style="position: relative;">
                            <?php if ($firstImage): ?>
                                <img src="<?= htmlspecialchars($firstImage) ?>" alt="Property Image" class="property-image">
                            <?php else: ?>
                                <div class="property-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                                    Finestrat Paradise Resort
                                </div>
                            <?php endif; ?>
                            
                            <div class="property-badges">
                                <?php if ($property['new_build']): ?>
                                    <span class="new-build-badge">Új Építésű</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="property-details">
                            <div class="property-title"><?= htmlspecialchars($property['title']) ?></div>
                            <div class="property-type"><?= htmlspecialchars(ucfirst($property['building_type'])) ?></div>
                            
                            <div class="property-price">
                                €<?= number_format($property['price'], 0, ',', '.') ?>
                            </div>
                            
                            <div class="property-info">
                                <div class="info-item">
                                    <span>🛏️</span>
                                    <span><?= $property['rooms'] ?> szoba</span>
                                </div>
                                <?php if ($property['garden']): ?>
                                    <div class="info-item">
                                        <span>🌳</span>
                                        <span>Kert</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($property['terrace']): ?>
                                    <div class="info-item">
                                        <span>🏠</span>
                                        <span>Terasz</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($property['parking']): ?>
                                    <div class="info-item">
                                        <span>🚗</span>
                                        <span>Parkoló</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="property-location">
                                📍 <?= htmlspecialchars($property['city']) ?>
                                <?php if ($property['ref_code']): ?>
                                    <span style="color: #999; font-size: 0.8rem;"> - Ref: <?= htmlspecialchars($property['ref_code']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($features)): ?>
                                <div class="property-features">
                                    <div class="features-title">Tulajdonságok:</div>
                                    <?php foreach (array_slice($features, 0, 6) as $feature): ?>
                                        <span class="feature-tag"><?= htmlspecialchars($feature) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($features) > 6): ?>
                                        <span class="feature-tag">+<?= count($features) - 6 ?> további</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="property-actions">
                                <a href="property-detail.php?id=<?= $property['id'] ?>" class="btn btn-primary">
                                    Részletek
                                </a>
                                <?php if ($property['virtual_tour_url']): ?>
                                    <a href="<?= htmlspecialchars($property['virtual_tour_url']) ?>" target="_blank" class="btn btn-outline">
                                        Virtuális túra
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <h3>Nincs találat</h3>
                <p>A megadott feltételeknek megfelelő ingatlan nem található.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>