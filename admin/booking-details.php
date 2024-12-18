<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    header('Location: bookings.php');
    exit;
}

$booking_id = $_GET['id'];

// Get booking details with user and car information
$stmt = $conn->prepare("
    SELECT 
        b.*,
        u.Fname,
        u.Lname,
        u.Email,
        u.Phone,
        c.Make,
        c.Model,
        c.Type,
        c.FuelConsumption,
        c.EngineSize,
        c.Img,
        c.RentalPrice
    FROM bookings b
    JOIN users u ON b.user_id = u.User_id
    JOIN car c ON b.car_id = c.Vehicle_id
    WHERE b.id = ?
");

$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: bookings.php');
    exit;
}

$booking = $result->fetch_assoc();
$stmt->close();

// Calculate total days and amount
$pickup_date = new DateTime($booking['pickup_date']);
$return_date = new DateTime($booking['return_date']);
$total_days = $pickup_date->diff($return_date)->days + 1;
$total_amount = $total_days * $booking['RentalPrice'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - CarRent Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .admin-main {
            flex: 1;
            margin-left: 250px;
            background: #f4f6f9;
            min-height: 100vh;
            padding: 20px;
        }

        .admin-header {
            background: white;
            padding: 1rem 2rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .booking-details {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .section {
            margin-bottom: 2.5rem;
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .section:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .section h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #3498db;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .info-item {
            background: white;
            padding: 1.2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .info-item label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .info-item label i {
            color: #3498db;
        }

        .info-item span {
            font-size: 1.1rem;
            color: #2c3e50;
            font-weight: 500;
            display: block;
            margin-top: 0.3rem;
        }

        .car-image-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto 2rem;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .car-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .status-badge {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-weight: 600;
            text-transform: capitalize;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status-badge.confirmed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-badge.cancelled {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-badge.completed {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }

        .status-actions {
            margin-top: 2rem;
            padding: 1.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .status-actions h3 {
            margin-bottom: 1rem;
            color: #2c3e50;
            font-size: 1.3rem;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .admin-main {
                margin-left: 0;
                padding: 10px;
            }

            .booking-details {
                padding: 1rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Booking Details</h1>
                <a href="../php/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </header>

            <div class="booking-details">
                <div class="section">
                    <h2><i class="fas fa-car"></i> Car Information</h2>
                    <div class="car-image-container">
                        <img src="<?php echo htmlspecialchars($booking['Img']); ?>" 
                             alt="<?php echo htmlspecialchars($booking['Make'] . ' ' . $booking['Model']); ?>" 
                             class="car-image"
                             onerror="this.src='../images/car-placeholder.jpg'">
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <label><i class="fas fa-car"></i> Make & Model</label>
                            <span><?php echo htmlspecialchars($booking['Make'] . ' ' . $booking['Model']); ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-tag"></i> Type</label>
                            <span><?php echo htmlspecialchars($booking['Type']); ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-gas-pump"></i> Fuel Consumption</label>
                            <span><?php echo htmlspecialchars($booking['FuelConsumption']); ?> L/100km</span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-engine"></i> Engine Size</label>
                            <span><?php echo htmlspecialchars($booking['EngineSize']); ?> L</span>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h2><i class="fas fa-user"></i> Customer Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label><i class="fas fa-user"></i> Full Name</label>
                            <span><?php echo htmlspecialchars($booking['Fname'] . ' ' . $booking['Lname']); ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <span><?php echo htmlspecialchars($booking['Email']); ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-phone"></i> Phone</label>
                            <span><?php echo htmlspecialchars($booking['Phone']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h2><i class="fas fa-calendar-alt"></i> Booking Details</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label><i class="fas fa-calendar-plus"></i> Pickup Date</label>
                            <span><?php echo date('F j, Y', strtotime($booking['pickup_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-calendar-minus"></i> Return Date</label>
                            <span><?php echo date('F j, Y', strtotime($booking['return_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-clock"></i> Duration</label>
                            <span><?php echo $total_days; ?> days</span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-money-bill-wave"></i> Total Amount</label>
                            <span>$<?php echo number_format($total_amount, 2); ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="fas fa-info-circle"></i> Status</label>
                            <span class="status-badge <?php echo strtolower($booking['status']); ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="status-actions">
                    <h3><i class="fas fa-edit"></i> Update Status</h3>
                    <div class="button-group">
                        <?php if ($booking['status'] !== 'confirmed'): ?>
                            <button onclick="updateStatus(<?php echo $booking['id']; ?>, 'confirmed')" 
                                    class="btn btn-success">
                                <i class="fas fa-check"></i> Confirm
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($booking['status'] !== 'completed'): ?>
                            <button onclick="updateStatus(<?php echo $booking['id']; ?>, 'completed')" 
                                    class="btn btn-primary">
                                <i class="fas fa-flag-checkered"></i> Complete
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($booking['status'] !== 'cancelled'): ?>
                            <button onclick="updateStatus(<?php echo $booking['id']; ?>, 'cancelled')" 
                                    class="btn btn-danger">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        async function updateStatus(bookingId, status) {
            if (confirm(`Are you sure you want to ${status} this booking?`)) {
                try {
                    const response = await fetch('../php/admin/update_booking_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            booking_id: bookingId,
                            status: status
                        })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        const successAlert = document.createElement('div');
                        successAlert.className = 'alert alert-success';
                        successAlert.style.cssText = `
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            padding: 15px 25px;
                            background: #d4edda;
                            color: #155724;
                            border-radius: 4px;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                            z-index: 1000;
                        `;
                        successAlert.textContent = `Booking ${status} successfully`;
                        document.body.appendChild(successAlert);

                        setTimeout(() => {
                            successAlert.remove();
                            location.reload();
                        }, 2000);
                    } else {
                        throw new Error(result.error || `Failed to ${status} booking`);
                    }
                } catch (error) {
                    const errorAlert = document.createElement('div');
                    errorAlert.className = 'alert alert-danger';
                    errorAlert.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 15px 25px;
                        background: #f8d7da;
                        color: #721c24;
                        border-radius: 4px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                        z-index: 1000;
                    `;
                    errorAlert.textContent = error.message || 'An error occurred while updating the booking';
                    document.body.appendChild(errorAlert);

                    setTimeout(() => {
                        errorAlert.remove();
                    }, 5000);

                    console.error('Error:', error);
                }
            }
        }
    </script>
</body>
</html>
