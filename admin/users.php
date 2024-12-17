<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get all users except current admin
$sql = "SELECT 
            User_id,
            CONCAT(Fname, ' ', Lname) as full_name,
            Email,
            Phone,
            Role,
            (SELECT COUNT(*) FROM bookings WHERE user_id = users.User_id) as total_bookings
        FROM users 
        WHERE User_id != ?
        ORDER BY User_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0984e3;
            --secondary-color: #2d3436;
            --accent-color: #00b894;
            --background-color: #f5f6fa;
            --dark-color: #2d3436;
            --light-color: #ffffff;
            --gradient-start: #e8f4f8;
            --gradient-end: #f5f6fa;
        }

        body {
            background: var(--gradient-start);
            background-image: 
                linear-gradient(120deg, var(--gradient-start), var(--gradient-end)),
                url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z' fill='%230984e3' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--dark-color);
        }

        .admin-container {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 249, 250, 0.9));
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            
        }

        .card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin: 15px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .table-container {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 249, 250, 0.95));
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .table {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary-color), #0770c2);
            color: white;
            border: none;
            padding: 15px;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(248, 249, 250, 0.5);
        }

        .table tbody tr:hover {
            background: rgba(9, 132, 227, 0.05);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #0770c2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0770c2, #065a9e);
            transform: translateY(-2px);
        }

        .modal-content {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .form-control {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(9, 132, 227, 0.1);
            outline: none;
        }

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

        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            overflow-y: auto;
            padding: 20px;
        }

        .modal-content {
            background-color: #fff;
            max-width: 700px;
            width: 90%;
            margin: 30px auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: relative;
            max-height: calc(100vh - 60px);
            overflow-y: auto;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #000;
        }

        #modalTitle {
            margin-top: 0;
            margin-bottom: 20px;
            padding-right: 30px;
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
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Users</h1>
                <div class="admin-profile">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="../php/logout.php" class="logout-btn">Logout</a>
                </div>
            </header>

            <div class="content-section">
                <div class="filters">
                    <select id="roleFilter">
                        <option value="">All Roles</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <input type="text" id="searchInput" placeholder="Search by name or email...">
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Total Bookings</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($user['User_id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['Email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['Phone'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="role-badge <?php echo strtolower($user['Role']); ?>">
                                            <?php echo ucfirst($user['Role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $user['total_bookings']; ?></td>
                                    <td>
                                        <button onclick="editUser(<?php echo $user['User_id']; ?>)" class="action-btn small">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($user['Role'] !== 'admin'): ?>
                                            <button onclick="deleteUser(<?php echo $user['User_id']; ?>)" class="action-btn small danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit User</h2>
            <form id="editUserForm">
                <input type="hidden" id="editUserId">
                <div class="form-group">
                    <label for="editName">Name</label>
                    <input type="text" id="editName" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" required>
                </div>
                <div class="form-group">
                    <label for="editPhone">Phone</label>
                    <input type="tel" id="editPhone">
                </div>
                <div class="form-group">
                    <label for="editRole">Role</label>
                    <select id="editRole">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeModal()">Cancel</button>
                    <button type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Filter users by role
        document.getElementById('roleFilter').addEventListener('change', function() {
            filterUsers();
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            filterUsers();
        });

        function filterUsers() {
            const role = document.getElementById('roleFilter').value.toLowerCase();
            const search = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');

            rows.forEach(row => {
                const roleCell = row.querySelector('.role-badge').textContent.toLowerCase();
                const nameCell = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const emailCell = row.querySelector('td:nth-child(3)').textContent.toLowerCase();

                const roleMatch = !role || roleCell.includes(role);
                const searchMatch = !search || 
                    nameCell.includes(search) || 
                    emailCell.includes(search);

                row.style.display = roleMatch && searchMatch ? '' : 'none';
            });
        }

        function editUser(userId) {
            // Fetch user details and populate modal
            fetch(`../php/admin/get_user.php?id=${userId}`)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('editUserId').value = user.User_id;
                    document.getElementById('editName').value = `${user.Fname} ${user.Lname}`.trim();
                    document.getElementById('editEmail').value = user.Email;
                    document.getElementById('editPhone').value = user.Phone || '';
                    document.getElementById('editRole').value = user.Role.toLowerCase();
                    
                    document.getElementById('editModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load user details');
                });
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const data = {
                id: document.getElementById('editUserId').value,
                name: document.getElementById('editName').value,
                email: document.getElementById('editEmail').value,
                phone: document.getElementById('editPhone').value,
                role: document.getElementById('editRole').value
            };

            console.log('Sending data:', data); // Debug log

            fetch('../php/admin/update_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('User updated successfully!');
                    location.reload();
                } else {
                    alert(result.message || 'Failed to update user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update user. Check console for details.');
            });
        });

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch('../php/admin/delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: userId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to delete user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }
    </script>
</body>
</html>
