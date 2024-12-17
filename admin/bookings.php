<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get all bookings with car and user details
$sql = "SELECT 
            b.id,
            CONCAT(u.Fname, ' ', u.Lname) as customer_name,
            CONCAT(c.Make, ' ', c.Model) as car_name,
            b.pickup_date,
            b.return_date,
            b.pickup_location,
            b.status,
            b.created_at
        FROM bookings b
        JOIN users u ON b.user_id = u.User_id
        JOIN car c ON b.car_id = c.Vehicle_id
        ORDER BY b.created_at DESC";

$result = $conn->query($sql);
$bookings = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<style>
    ::-webkit-scrollbar {
            display: none;
        }
        .table-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin: 1rem 0;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .data-table thead th {
            background: #f8f9fa;
            color: #2d3436;
            font-weight: 600;
            padding: 1rem;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
            text-align: left;
        }

        .data-table tbody tr {
            transition: all 0.2s ease-in-out;
            border-bottom: 1px solid #f1f3f5;
        }

        .data-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.04);
        }

        .data-table td {
            padding: 1rem;
            vertical-align: middle;
            color: #4a4a4a;
            font-size: 0.95rem;
        }

        .data-table td:first-child {
            font-weight: 600;
            color: #2d3436;
        }

        .role-badge {
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            display: inline-block;
            text-align: center;
        }

        .role-badge.admin {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(238, 82, 83, 0.2);
        }

        .role-badge.user {
            background: linear-gradient(135deg, #26de81 0%, #20bf6b 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(32, 191, 107, 0.2);
        }

        .action-btn.small {
            width: 35px;
            height: 35px;
            padding: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.3rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f8f9fa;
        }

        .action-btn.small i {
            font-size: 0.9rem;
            color: #2d3436;
        }

        .action-btn.small:hover {
            transform: translateY(-2px);
        }

        .action-btn.small:hover i {
            color: #0984e3;
        }

        .action-btn.danger:hover {
            background: #fff5f5;
        }

        .action-btn.danger:hover i {
            color: #ff6b6b;
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            align-items: center;
        }

        .filters select,
        .filters input {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
            min-width: 200px;
            background: white;
        }

        .filters select:focus,
        .filters input:focus {
            outline: none;
            border-color: #0984e3;
            box-shadow: 0 0 0 2px rgba(9, 132, 227, 0.1);
        }

        @media (max-width: 768px) {
            .data-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
            
            .data-table thead th {
                padding: 0.8rem;
                font-size: 0.8rem;
            }
            
            .data-table td {
                padding: 0.8rem;
                font-size: 0.9rem;
            }
            
            .role-badge {
                padding: 0.3rem 0.8rem;
                font-size: 0.75rem;
            }

            .filters {
                flex-direction: column;
            }
            
            .filters select,
            .filters input {
                width: 100%;
            }
        }

        .data-table tbody:empty::after {
            content: "No users found";
            display: block;
            text-align: center;
            padding: 2rem;
            color: #a0a0a0;
            font-style: italic;
        }
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
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Bookings</h1>
                <div class="admin-profile">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="../php/logout.php" class="logout-btn">Logout</a>
                </div>
            </header>

            <div class="content-section">
                <div class="filters">
                    <select id="statusFilter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="text" id="searchInput" placeholder="Search by customer or car...">
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Car</th>
                                <th>Pickup Date</th>
                                <th>Return Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($booking['id']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['car_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['pickup_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['return_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['pickup_location']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($booking['status']); ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="updateStatus(<?php echo $booking['id']; ?>)" class="action-btn small">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="viewDetails(<?php echo $booking['id']; ?>)" class="action-btn small">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Filter bookings by status
        document.getElementById('statusFilter').addEventListener('change', function() {
            filterBookings();
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            filterBookings();
        });

        function filterBookings() {
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const search = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');

            rows.forEach(row => {
                const statusCell = row.querySelector('.status-badge').textContent.toLowerCase();
                const customerCell = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const carCell = row.querySelector('td:nth-child(3)').textContent.toLowerCase();

                const statusMatch = !status || statusCell.includes(status);
                const searchMatch = !search || 
                    customerCell.includes(search) || 
                    carCell.includes(search);

                row.style.display = statusMatch && searchMatch ? '' : 'none';
            });
        }

        function updateStatus(bookingId) {
            // Implement status update functionality
            const newStatus = prompt('Enter new status (pending/active/completed/cancelled):');
            if (newStatus) {
                // Make API call to update status
                fetch('../php/admin/update_booking_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        booking_id: bookingId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }

        function viewDetails(bookingId) {
            // Redirect to booking details page
            window.location.href = `booking-details.php?id=${bookingId}`;
        }
    </script>
</body>
</html>
