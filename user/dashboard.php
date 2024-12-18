<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and has customer role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../index.php');
    exit;
}

// Get user's bookings
$user_id = $_SESSION['user_id'];
$sql = "SELECT 
            b.id,
            b.pickup_date,
            b.return_date,
            CONCAT(c.Make, ' ', c.Model) as car_name,
            c.Img as car_image,
            c.RentalPrice as price,
            b.status,
            CASE 
                WHEN CURRENT_DATE BETWEEN b.pickup_date AND b.return_date THEN 'Active'
                WHEN CURRENT_DATE < b.pickup_date THEN 'Pending'
                ELSE 'Completed'
            END as booking_status
        FROM bookings b
        JOIN car c ON b.car_id = c.Vehicle_id
        WHERE b.user_id = ?
        ORDER BY b.pickup_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - CarRent</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f6fa;
            color: #2d3436;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .welcome-section {
            margin-top: 7rem;
            background: linear-gradient(rgba(9, 132, 227, 0.8), rgba(45, 52, 54, 0.9)), url('images/contact-hero.jpg');
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(9, 132, 227, 0.1);
        }

        .welcome-section h1 {
            font-size: 2rem;
            margin: 0 0 1rem 0;
            font-weight: 600;
        }

        .welcome-section p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            color: #2d3436;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-button:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .action-button i {
            color: #0984e3;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 1.1rem;
            color: #636e72;
            margin: 0 0 0.5rem 0;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #0984e3;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-description {
            font-size: 0.9rem;
            color: #636e72;
        }

        .recent-activity {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .recent-activity h2 {
            font-size: 1.5rem;
            margin: 0 0 1.5rem 0;
            color: #2d3436;
        }

        .activity-list {
            display: grid;
            gap: 1rem;
        }

        .activity-item {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .activity-item:hover {
            transform: translateY(-2px);
        }

        .activity-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-content h3 {
            margin: 0 0 0.5rem 0;
            color: #2d3436;
            font-size: 1.2rem;
        }

        .activity-content p {
            margin: 0.3rem 0;
            color: #636e72;
        }

        .section-title {
            font-size: 1.5rem;
            margin: 2rem 0 1rem;
            color: #2d3436;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #0984e3;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
                margin: 1rem auto;
            }

            .welcome-section {
                padding: 1.5rem;
            }

            .welcome-section h1 {
                font-size: 1.5rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-card .stat-value {
                font-size: 1.5rem;
            }

            .recent-activity {
                padding: 1.5rem;
            }

            .quick-actions {
                flex-direction: column;
            }

            .action-button {
                width: 100%;
                justify-content: center;
            }

            .activity-item {
                flex-direction: column;
                gap: 1rem;
            }

            .activity-item img {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <section class="welcome-section">
            <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?> !</h1>
            <p>Manage your car rentals and account settings from your personal dashboard.</p>
        </section>

        <div class="quick-actions">
            <a href="../cars.php" class="action-button">
                <i class="fas fa-car"></i>
                Book a Car
            </a>
            <a href="booking.php" class="action-button">
                <i class="fas fa-list"></i>
                View Bookings
            </a>
            <a href="profile.php" class="action-button">
                <i class="fas fa-user"></i>
                Edit Profile
            </a>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Active Rentals</h3>
                <div class="stat-value"><?php echo count(array_filter($bookings, function($booking) { return strtolower($booking['booking_status']) === 'active'; })); ?></div>
                <p class="stat-description">Currently rented vehicles</p>
            </div>
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="stat-value"><?php echo count($bookings); ?></div>
                <p class="stat-description">All-time reservations</p>
            </div>
            <div class="stat-card">
                <h3>Reward Points</h3>
                <div class="stat-value">350</div>
                <p class="stat-description">Available points to redeem</p>
            </div>
        </div>

        <section class="recent-activity">
            <h2 class="section-title">
                <i class="fas fa-clock"></i>
                Recent Activity
            </h2>
            <div class="activity-list">
                <?php foreach ($bookings as $booking): ?>
                    <div class="activity-item">
                        <img src="<?php echo htmlspecialchars($booking['car_image']); ?>" alt="<?php echo htmlspecialchars($booking['car_name']); ?>">
                        <div class="activity-content">
                            <h3><?php echo htmlspecialchars($booking['car_name']); ?></h3>
                            <p>Pickup: <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></p>
                            <p>Return: <?php echo date('M d, Y', strtotime($booking['return_date'])); ?></p>
                            <div class="activity-date"><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
