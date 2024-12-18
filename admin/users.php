<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get users list
$stmt = $conn->prepare("
        SELECT 
            u.User_id,
            CONCAT(u.Fname, ' ', u.Lname) as full_name,
            u.Email,
            u.Phone,
            u.Role,
            COUNT(b.id) as total_bookings
        FROM users u
        LEFT JOIN bookings b ON u.User_id = b.user_id
        WHERE u.User_id != ?
        GROUP BY u.User_id, u.Fname, u.Lname, u.Email, u.Phone, u.Role
        ORDER BY u.User_id DESC
    ");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

// Get total users count (excluding current admin)
$userCount = 0;
$sql = "SELECT COUNT(*) as count FROM users WHERE Role = 'customer' AND User_id != " . $_SESSION['user_id'];
$result = $conn->query($sql);
if ($result) {
    $userCount = $result->fetch_assoc()['count'];
}

// Get active users (users with active bookings)
$activeUsers = 0;
$sql = "SELECT COUNT(DISTINCT u.User_id) as count 
        FROM users u 
        INNER JOIN bookings b ON u.User_id = b.user_id 
        WHERE b.status = 'active'";
$result = $conn->query($sql);
if ($result) {
    $activeUsers = $result->fetch_assoc()['count'];
}

// Get new users this month
$newUsers = 0;
$firstDayOfMonth = date('Y-m-01');
$sql = "SELECT COUNT(*) as count 
        FROM users 
        WHERE Role = 'customer' 
        AND User_id != " . $_SESSION['user_id'] . "
        AND created_at >= '" . $firstDayOfMonth . "'";
$result = $conn->query($sql);
if ($result) {
    $newUsers = $result->fetch_assoc()['count'];
}

// For debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug information
$debug = [
    'total_users' => $userCount,
    'active_users' => $activeUsers,
    'new_users' => $newUsers,
    'total_rows' => count($users),
    'last_sql_error' => $conn->error,
    'session_user_id' => $_SESSION['user_id']
];

// Add debug output to the page
echo "<!-- Debug Info: " . json_encode($debug) . " -->";

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - CarRent Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .user-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            color: #666;
            font-size: 0.9rem;
        }

        .stat-card p {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .users-table {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: 600;
            color: #666;
        }

        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .role-badge.admin {
            background-color: #e3fcef;
            color: #00b894;
        }

        .role-badge.user {
            background-color: #fff3bf;
            color: #f39c12;
        }

        .action-btn {
            padding: 0.5rem;
            border: none;
            background: none;
            cursor: pointer;
            color: var(--primary-color);
            transition: transform 0.3s ease;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .action-btn.danger {
            color: #e74c3c;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        @media (max-width: 768px) {
            .user-stats {
                grid-template-columns: 1fr;
            }

            .users-table {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Users</h1>
                <a href="../php/logout.php" class="logout-btn">Logout</a>
            </header>

            <div class="user-stats">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p><?php echo $userCount; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Active Users</h3>
                    <p><?php echo $activeUsers; ?></p>
                </div>
                <div class="stat-card">
                    <h3>New Users This Month</h3>
                    <p><?php echo $newUsers; ?></p>
                </div>
            </div>

            <div class="users-table">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2>Users List</h2>
                    <button onclick="showAddUserModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Bookings</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
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

            <!-- Add User Modal -->
            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('addModal')">&times;</span>
                    <h2>Add New User</h2>
                    <form id="addUserForm" onsubmit="return addUser(event)">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone">
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" required>
                                <option value="customer">Customer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" required>
                        </div>
                        <div style="text-align: right;">
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('editModal')">&times;</span>
                    <h2>Edit User</h2>
                    <form id="editUserForm" onsubmit="return updateUser(event)">
                        <input type="hidden" id="editUserId">
                        <div class="form-group">
                            <label for="editName">Full Name</label>
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
                            <select id="editRole" required>
                                <option value="customer">Customer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div style="text-align: right;">
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function showAddUserModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        async function addUser(event) {
            event.preventDefault();
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                role: document.getElementById('role').value,
                password: document.getElementById('password').value
            };

            try {
                const response = await fetch('../php/admin/add_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                if (result.success) {
                    alert('User added successfully');
                    location.reload();
                } else {
                    alert(result.error || 'Failed to add user');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while adding the user');
            }
        }

        async function editUser(userId) {
            try {
                const response = await fetch(`../php/admin/get_user.php?id=${userId}`);
                
                if (!response.ok) {
                    throw new Error('Failed to fetch user data');
                }

                const result = await response.json();
                if (!result.success) {
                    throw new Error(result.message || 'Failed to load user data');
                }

                const user = result.data;
                document.getElementById('editUserId').value = user.User_id;
                document.getElementById('editName').value = `${user.Fname} ${user.Lname}`.trim();
                document.getElementById('editEmail').value = user.Email;
                document.getElementById('editPhone').value = user.Phone || '';
                document.getElementById('editRole').value = user.Role.toLowerCase();
                
                document.getElementById('editModal').style.display = 'block';
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to load user data');
            }
        }

        async function updateUser(event) {
            event.preventDefault();
            const userId = document.getElementById('editUserId').value;
            const [firstName, ...lastNameParts] = document.getElementById('editName').value.split(' ');
            const lastName = lastNameParts.join(' ');

            const formData = {
                firstName: firstName,
                lastName: lastName || '',
                email: document.getElementById('editEmail').value,
                phone: document.getElementById('editPhone').value,
                role: document.getElementById('editRole').value
            };

            try {
                const response = await fetch(`../php/admin/update_user.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: userId,
                        ...formData
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();
                if (result.success) {
                    alert('User updated successfully');
                    location.reload();
                } else {
                    throw new Error(result.message || 'Failed to update user');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'An error occurred while updating the user');
            }
        }

        async function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                try {
                    const response = await fetch(`../php/admin/delete_user.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id: userId })
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const result = await response.json();
                    if (result.success) {
                        alert('User deleted successfully');
                        location.reload();
                    } else {
                        alert(result.message || 'Failed to delete user');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the user');
                }
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
