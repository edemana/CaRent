<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and has user role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}

// Get car details if car_id is provided
$car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
$car = null;

if ($car_id > 0) {
    $sql = "SELECT 
            Vehicle_id,
            CONCAT(Make, ' ', Model) as name,
            Description,
            RentalPrice,
            Img as image,
            Type,
            FuelConsumption,
            EngineSize,
            Mileage
        FROM car 
        WHERE Vehicle_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $car = $stmt->get_result()->fetch_assoc();
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $car_id = $_POST['car_id'];
    $user_id = $_SESSION['user_id'];

    // Check if car is available for the selected dates
    $sql = "SELECT COUNT(*) as count 
            FROM availability 
            WHERE Vehicle_id = ? 
            AND (
                (Available_start BETWEEN ? AND ?) OR
                (Available_end BETWEEN ? AND ?) OR
                (Available_start <= ? AND Available_end >= ?)
            )";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $car_id, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['count'] == 0) {
        // Car is available, create booking
        $sql = "INSERT INTO availability (Vehicle_id, User_id, Available_start, Available_end) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $car_id, $user_id, $start_date, $end_date);
        
        if ($stmt->execute()) {
            header('Location: dashboard.php?success=1');
            exit;
        } else {
            $error = "Failed to create booking. Please try again.";
        }
    } else {
        $error = "Car is not available for the selected dates.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Car - CarRent</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .booking-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .car-summary {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .car-summary img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
        .car-info {
            flex: 1;
        }
        .car-info h3 {
            margin: 0 0 1rem 0;
            color: var(--primary-color);
        }
        .car-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 1rem 0;
        }
        .booking-form {
            display: grid;
            gap: 1.5rem;
            padding: 1rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .form-group label {
            font-weight: 500;
            color: #333;
        }
        .form-group input {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            text-align: right;
        }
        .book-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .book-btn:hover {
            background-color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="booking-page">
        <div class="container">
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($car): ?>
                <div class="car-details">
                    <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>" class="car-image">
                    <div class="car-info">
                        <h2><?php echo htmlspecialchars($car['name']); ?></h2>
                        <p class="description"><?php echo htmlspecialchars($car['Description']); ?></p>
                        <div class="features">
                            <div class="feature">
                                <i class="fas fa-car"></i>
                                <span>Type: <?php echo htmlspecialchars($car['Type']); ?></span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-gas-pump"></i>
                                <span>Fuel Consumption: <?php echo htmlspecialchars($car['FuelConsumption']); ?></span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-cog"></i>
                                <span>Engine: <?php echo htmlspecialchars($car['EngineSize']); ?></span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-road"></i>
                                <span>Mileage: <?php echo number_format($car['Mileage']); ?> km</span>
                            </div>
                        </div>
                        <div class="price">
                            <span class="amount">$<?php echo number_format($car['RentalPrice'], 2); ?></span>
                            <span class="period">per day</span>
                        </div>
                    </div>
                </div>

                <form method="POST" class="booking-form">
                    <input type="hidden" name="car_id" value="<?php echo $car['Vehicle_id']; ?>">
                    
                    <div class="form-group">
                        <label for="start_date">Pickup Date</label>
                        <input type="date" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="end_date">Return Date</label>
                        <input type="date" id="end_date" name="end_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <button type="submit" class="btn-primary">Book Now</button>
                </form>
            <?php else: ?>
                <div class="error-message">
                    <h2>Car Not Found</h2>
                    <p>The requested car could not be found. Please try again.</p>
                    <a href="../cars.php" class="btn-primary">View All Cars</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Validate dates
        document.getElementById('end_date').addEventListener('change', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = this.value;
            
            if (startDate && endDate && startDate > endDate) {
                alert('Return date must be after pickup date');
                this.value = '';
            }
        });

        document.getElementById('start_date').addEventListener('change', function() {
            const endDate = document.getElementById('end_date');
            endDate.min = this.value;
            
            if (endDate.value && endDate.value < this.value) {
                endDate.value = '';
            }
        });
    </script>
</body>
</html>
