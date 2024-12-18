<?php
session_start();
require_once '../php/config.php';

// Debug logging
error_log('Session data: ' . print_r($_SESSION, true));
error_log('User type: ' . (isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'not set'));

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    error_log('Access denied: user_id=' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set') . 
              ', user_type=' . (isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'not set'));
    header('Location: ../index.php');
    exit;
}

// Get statistics
$stats = [
    'total_cars' => 0,
    'active_bookings' => 0,
    'total_users' => 0,
    'monthly_revenue' => 0
];

// Get total cars
$sql = "SELECT COUNT(*) as count FROM car";
$result = $conn->query($sql);
if ($result) {
    $stats['total_cars'] = $result->fetch_assoc()['count'];
}

// Get active bookings
$sql = "SELECT COUNT(*) as count FROM availability WHERE Available_start <= CURRENT_DATE AND Available_end >= CURRENT_DATE";
$result = $conn->query($sql);
if ($result) {
    $stats['active_bookings'] = $result->fetch_assoc()['count'];
}

// Get total users
$sql = "SELECT COUNT(*) as count FROM users WHERE Role = 'customer'";
$result = $conn->query($sql);
if ($result) {
    $stats['total_users'] = $result->fetch_assoc()['count'];
}

// Get monthly revenue
$sql = "SELECT SUM(RentalPrice) as revenue FROM car c 
        JOIN availability a ON c.Vehicle_id = a.Vehicle_id 
        WHERE MONTH(a.Available_start) = MONTH(CURRENT_DATE())";
$result = $conn->query($sql);
if ($result) {
    $stats['monthly_revenue'] = $result->fetch_assoc()['revenue'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CarRent</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<style>
    .logout-btn {
    padding: 0.5rem 1rem;
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
}
</style>
<body>
        
        <?php include 'includes/sidebar.php'; ?>

        <div class="admin-content">
            <main class="admin-main">
                <header class="admin-header">
                    <h1>Dashboard</h1>
                    <div class="admin-profile">
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="../php/logout.php" class="logout-btn">Logout</a>
                    </div>
                </header>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <i class="fas fa-car"></i>
                        <div class="stat-info">
                            <h3>Total Cars</h3>
                            <p><?php echo $stats['total_cars']; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-calendar-check"></i>
                        <div class="stat-info">
                            <h3>Active Bookings</h3>
                            <p><?php echo $stats['active_bookings']; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <div class="stat-info">
                            <h3>Total Users</h3>
                            <p><?php echo $stats['total_users']; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-dollar-sign"></i>
                        <div class="stat-info">
                            <h3>Monthly Revenue</h3>
                            <p>$<?php echo number_format($stats['monthly_revenue'], 2); ?></p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-content">
                    <div class="recent-bookings">
                        <h2>Recent Bookings</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>User</th>
                                    <th>Car</th>
                                    <th>Dates</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="recentBookings">
                                <!-- Bookings will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div class="quick-actions">
                        <h2>Quick Actions</h2>
                        <div class="action-buttons">
                            <a href="cars.php" class="action-btn">
                                <i class="fas fa-car"></i>
                                Manage Cars
                            </a>
                            <a href="bookings.php" class="action-btn">
                                <i class="fas fa-calendar"></i>
                                Manage Bookings
                            </a>
                            <a href="users.php" class="action-btn">
                                <i class="fas fa-users"></i>
                                Manage Users
                            </a>
                            <a href="reports.php" class="action-btn">
                                <i class="fas fa-chart-bar"></i>
                                View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadRecentBookings();

            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.admin-sidebar');
            
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        });

        async function loadRecentBookings() {
            try {
                const response = await fetch('../php/admin/get_recent_bookings.php');
                const bookings = await response.json();
                const tbody = document.getElementById('recentBookings');
                
                tbody.innerHTML = '';
                bookings.forEach(booking => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>#${booking.id}</td>
                        <td>${booking.user_name}</td>
                        <td>${booking.car_name}</td>
                        <td>${booking.pickup_date} to ${booking.return_date}</td>
                        <td><span class="status-${booking.status.toLowerCase()}">${booking.status}</span></td>
                        <td>
                            <button onclick="updateBooking(${booking.id})" class="action-btn small">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function updateBooking(bookingId) {
            window.location.href = `booking-details.php?id=${bookingId}`;
        }
    </script>
</body>
</html>
