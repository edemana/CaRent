<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and has user role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}

// Get search parameters
$location = isset($_GET['location']) ? $_GET['location'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_asc';

// Build query
$sql = "SELECT 
            c.Vehicle_id,
            CONCAT(c.Make, ' ', c.Model) as name,
            c.Description,
            c.RentalPrice,
            c.Img as image,
            c.Type,
            c.FuelConsumption,
            c.EngineSize,
            c.Mileage
        FROM car c
        WHERE c.Vehicle_id NOT IN (
            SELECT Vehicle_id 
            FROM availability 
            WHERE (Available_start <= ? AND Available_end >= ?)
        )";

$params = [$end_date, $start_date];
$types = "ss";

if ($location) {
    $sql .= " AND c.Address LIKE ?";
    $params[] = "%$location%";
    $types .= "s";
}

if ($type) {
    $sql .= " AND c.Type = ?";
    $params[] = $type;
    $types .= "s";
}

// Add sorting
switch ($sort) {
    case 'price_desc':
        $sql .= " ORDER BY c.RentalPrice DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY name DESC";
        break;
    default: // price_asc
        $sql .= " ORDER BY c.RentalPrice ASC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - CarRent</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="results-page">
        <div class="container">
            <div class="search-filters">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                    
                    <div class="filter-group">
                        <label for="type">Car Type</label>
                        <select name="type" id="type" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="Sedan" <?php echo $type === 'Sedan' ? 'selected' : ''; ?>>Sedan</option>
                            <option value="SUV" <?php echo $type === 'SUV' ? 'selected' : ''; ?>>SUV</option>
                            <option value="Sports" <?php echo $type === 'Sports' ? 'selected' : ''; ?>>Sports</option>
                            <option value="Luxury" <?php echo $type === 'Luxury' ? 'selected' : ''; ?>>Luxury</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="sort">Sort By</label>
                        <select name="sort" id="sort" onchange="this.form.submit()">
                            <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                            <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="results-grid">
                <?php if (count($cars) > 0): ?>
                    <?php foreach ($cars as $car): ?>
                        <div class="car-card">
                            <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>" class="car-image">
                            <div class="car-info">
                                <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                                <p class="description"><?php echo htmlspecialchars($car['Description']); ?></p>
                                <div class="features">
                                    <span><i class="fas fa-car"></i> <?php echo htmlspecialchars($car['Type']); ?></span>
                                    <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($car['FuelConsumption']); ?></span>
                                    <span><i class="fas fa-cog"></i> <?php echo htmlspecialchars($car['EngineSize']); ?></span>
                                </div>
                                <div class="price">
                                    <span class="amount">$<?php echo number_format($car['RentalPrice'], 2); ?></span>
                                    <span class="period">per day</span>
                                </div>
                                <a href="booking.php?car_id=<?php echo $car['Vehicle_id']; ?>" class="btn-primary">Book Now</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <h2>No Cars Available</h2>
                        <p>No cars found matching your search criteria. Please try different dates or filters.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
