<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get total number of users
$stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM users WHERE role = 'customer'");
$stmt->execute();
$result = $stmt->get_result();
$totalUsers = $result->fetch_assoc()['total_users'];

// Get total number of cars
$stmt = $conn->prepare("SELECT COUNT(*) as total_cars FROM car");
$stmt->execute();
$result = $stmt->get_result();
$totalCars = $result->fetch_assoc()['total_cars'];

// Get total number of bookings
$stmt = $conn->prepare("SELECT COUNT(*) as total_bookings FROM bookings");
$stmt->execute();
$result = $stmt->get_result();
$totalBookings = $result->fetch_assoc()['total_bookings'];

// Get monthly revenue for the past 6 months
$stmt = $conn->prepare("
    SELECT 
        DATE_FORMAT(b.Pickup_date, '%Y-%m') as month,
        SUM(c.RentalPrice * DATEDIFF(b.Return_date, b.Pickup_date)) as revenue
    FROM bookings b
    JOIN car c ON b.car_id = c.Vehicle_id
    WHERE b.Pickup_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(b.Pickup_date, '%Y-%m')
    ORDER BY month ASC
");
$stmt->execute();
$monthlyRevenue = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get most popular cars
$stmt = $conn->prepare("
    SELECT 
        c.Make,
        c.Model,
        COUNT(b.id) as booking_count
    FROM car c
    LEFT JOIN bookings b ON c.Vehicle_id = b.id
    GROUP BY c.Vehicle_id
    ORDER BY booking_count DESC
    LIMIT 5
");
$stmt->execute();
$popularCars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get booking status distribution
$stmt = $conn->prepare("
    SELECT 
        CASE 
            WHEN CURRENT_DATE BETWEEN Pickup_date AND Return_date THEN 'Active'
            WHEN CURRENT_DATE < Pickup_date THEN 'Upcoming'
            ELSE 'Completed'
        END as status,
        COUNT(*) as count
    FROM bookings
    GROUP BY 
        CASE 
            WHEN CURRENT_DATE BETWEEN Pickup_date AND Return_date THEN 'Active'
            WHEN CURRENT_DATE < Pickup_date THEN 'Upcoming'
            ELSE 'Completed'
        END
");
$stmt->execute();
$bookingStatus = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        ::-webkit-scrollbar {
            display: none;
        }

        .reports-container {
            max-width: 1200px;
            margin: 80px auto;
            padding: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #2d3436;
            margin: 0;
            font-size: 1.1rem;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0984e3;
            margin: 10px 0;
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
        }

        .popular-cars {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .popular-cars h2 {
            color: #2d3436;
            margin-bottom: 20px;
        }

        .car-list {
            list-style: none;
            padding: 0;
        }

        .car-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .car-item:last-child {
            border-bottom: none;
        }

        .car-name {
            color: #2d3436;
            font-weight: 500;
        }

        .booking-count {
            color: #0984e3;
            font-weight: bold;
        }

        .section-title {
            color: #2d3436;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="reports-container">
        <h1 class="section-title">Reports & Analytics</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="number"><?php echo $totalUsers; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Cars</h3>
                <div class="number"><?php echo $totalCars; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="number"><?php echo $totalBookings; ?></div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-container">
                <h2>Monthly Revenue</h2>
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="chart-container">
                <h2>Booking Status Distribution</h2>
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="popular-cars">
            <h2>Most Popular Cars</h2>
            <ul class="car-list">
                <?php foreach ($popularCars as $car): ?>
                <li class="car-item">
                    <span class="car-name"><?php echo htmlspecialchars($car['Make'] . ' ' . $car['Model']); ?></span>
                    <span class="booking-count"><?php echo $car['booking_count']; ?> bookings</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthlyRevenue, 'month')); ?>,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: <?php echo json_encode(array_column($monthlyRevenue, 'revenue')); ?>,
                    borderColor: '#0984e3',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($bookingStatus, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($bookingStatus, 'count')); ?>,
                    backgroundColor: ['#0984e3', '#00b894', '#fdcb6e']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
