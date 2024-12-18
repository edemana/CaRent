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
            b.pickup_location,
            b.status,
            CONCAT(c.Make, ' ', c.Model) as car_name,
            c.Img as car_image,
            c.RentalPrice as price_per_day,
            DATEDIFF(b.return_date, b.pickup_date) as total_days,
            (DATEDIFF(b.return_date, b.pickup_date) * c.RentalPrice) as total_price
        FROM bookings b
        JOIN car c ON b.car_id = c.Vehicle_id
        WHERE b.user_id = ?
        ORDER BY b.pickup_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - CarRent</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .bookings-container {
            max-width: 1200px;
            margin: 100px auto;
            padding: 2rem;
        }

        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .booking-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-5px);
        }

        .booking-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .booking-details {
            padding: 1.5rem;
        }

        .car-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .booking-info {
            display: grid;
            gap: 0.5rem;
        }

        .booking-info p {
            display: flex;
            justify-content: space-between;
            margin: 0;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .booking-info p:last-child {
            border-bottom: none;
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
            text-align: center;
        }

        .status-pending {
            background-color: #ffeaa7;
            color: #fdcb6e;
        }

        .status-confirmed {
            background-color: #55efc4;
            color: #00b894;
        }

        .status-completed {
            background-color: #81ecec;
            color: #00cec9;
        }

        .status-cancelled {
            background-color: #ff7675;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .empty-state h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 2rem;
        }

        .browse-cars-btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: background-color 0.3s ease;
        }

        .browse-cars-btn:hover {
            background-color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="bookings-container">
        <h2>My Bookings</h2>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <h3>No Bookings Yet</h3>
                <p>You haven't made any car bookings yet. Start exploring our collection of cars!</p>
                <a href="../cars.php" class="browse-cars-btn">Browse Cars</a>
            </div>
        <?php else: ?>
            <div class="bookings-grid">
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card">
                        <img src="<?php echo htmlspecialchars($booking['car_image']); ?>" alt="<?php echo htmlspecialchars($booking['car_name']); ?>" class="booking-image">
                        <div class="booking-details">
                            <h3 class="car-name"><?php echo htmlspecialchars($booking['car_name']); ?></h3>
                            <div class="booking-info">
                                <p>
                                    <span>Pickup Date:</span>
                                    <span><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></span>
                                </p>
                                <p>
                                    <span>Return Date:</span>
                                    <span><?php echo date('M d, Y', strtotime($booking['return_date'])); ?></span>
                                </p>
                                <p>
                                    <span>Location:</span>
                                    <span><?php echo htmlspecialchars($booking['pickup_location']); ?></span>
                                </p>
                                <p>
                                    <span>Duration:</span>
                                    <span><?php echo $booking['total_days']; ?> days</span>
                                </p>
                                <p>
                                    <span>Total Price:</span>
                                    <span>$<?php echo number_format($booking['total_price'], 2); ?></span>
                                </p>
                                <p>
                                    <span>Status:</span>
                                    <span class="status status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
