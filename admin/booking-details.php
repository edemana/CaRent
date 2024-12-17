<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Get booking details
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            a.Vehicle_id as id,
            a.Available_start as pickup_date,
            a.Available_end as return_date,
            CONCAT(c.Make, ' ', c.Model) as car_name,
            c.Img as car_image,
            c.RentalPrice as car_price,
            c.Type as car_type,
            c.FuelConsumption as fuel_consumption,
            c.EngineSize as engine_size,
            CASE 
                WHEN CURRENT_DATE BETWEEN a.Available_start AND a.Available_end THEN 'Active'
                WHEN CURRENT_DATE < a.Available_start THEN 'Pending'
                ELSE 'Completed'
            END as status
        FROM availability a
        JOIN car c ON a.Vehicle_id = c.Vehicle_id
        WHERE a.Vehicle_id = ? AND a.User_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - CarRent</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .booking-details-page {
            padding: 2rem 5%;
            margin-top: 60px;
        }

        .booking-details-container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .booking-header {
            padding: 2rem;
            background-color: var(--primary-color);
            color: white;
        }

        .booking-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 1rem;
            background-color: white;
        }

        .booking-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            padding: 2rem;
        }

        .car-image-container img {
            width: 100%;
            border-radius: 10px;
        }

        .booking-info {
            display: grid;
            gap: 1.5rem;
        }

        .info-group {
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }

        .info-group h3 {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .car-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .feature-item i {
            color: var(--primary-color);
        }

        .price-breakdown {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }

        .price-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .total-price {
            border-top: 2px solid #ddd;
            padding-top: 1rem;
            margin-top: 1rem;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .booking-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="booking-details-page">
        <div class="booking-details-container">
            <div class="booking-header">
                <h1>Booking #<?php echo $booking_id; ?></h1>
                <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                    <?php echo ucfirst($booking['status']); ?>
                </span>
            </div>

            <div class="booking-content">
                <div class="car-image-container">
                    <img src="../<?php echo htmlspecialchars($booking['car_image']); ?>" 
                         alt="<?php echo htmlspecialchars($booking['car_name']); ?>">
                </div>

                <div class="booking-info">
                    <div class="info-group">
                        <h3>Car Details</h3>
                        <h2><?php echo htmlspecialchars($booking['car_name']); ?></h2>
                        <p><?php echo ucfirst($booking['car_type']); ?></p>
                        <div class="car-features">
                            <div class="feature-item">
                                <i class="fas fa-gas-pump"></i>
                                <span><?php echo ucfirst($booking['fuel_consumption']); ?></span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-cog"></i>
                                <span><?php echo ucfirst($booking['engine_size']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="info-group">
                        <h3>Booking Details</h3>
                        <div class="feature-item">
                            <i class="fas fa-calendar"></i>
                            <span>Pickup: <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>Return: <?php echo date('M d, Y', strtotime($booking['return_date'])); ?></span>
                        </div>
                    </div>

                    <div class="info-group">
                        <h3>Price Breakdown</h3>
                        <div class="price-breakdown">
                            <?php
                            $start = new DateTime($booking['pickup_date']);
                            $end = new DateTime($booking['return_date']);
                            $days = $end->diff($start)->days;
                            $daily_rate = $booking['car_price'];
                            $subtotal = $days * $daily_rate;
                            $tax = $subtotal * 0.1; // 10% tax
                            $total = $subtotal + $tax;
                            ?>
                            <div class="price-item">
                                <span>Daily Rate:</span>
                                <span>$<?php echo number_format($daily_rate, 2); ?></span>
                            </div>
                            <div class="price-item">
                                <span>Number of Days:</span>
                                <span><?php echo $days; ?></span>
                            </div>
                            <div class="price-item">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="price-item">
                                <span>Tax (10%):</span>
                                <span>$<?php echo number_format($tax, 2); ?></span>
                            </div>
                            <div class="price-item total-price">
                                <span>Total:</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($booking['status'] == 'pending'): ?>
                        <button onclick="cancelBooking(<?php echo $booking_id; ?>)" class="btn-danger">
                            Cancel Booking
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        async function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                try {
                    const response = await fetch('../php/cancel_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: bookingId })
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        window.location.href = 'dashboard.php';
                    } else {
                        alert(result.message || 'Failed to cancel booking');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            }
        }
    </script>
</body>
</html>
