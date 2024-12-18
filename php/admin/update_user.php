<?php
session_start();
require_once '../config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['id']) || !isset($data['firstName']) || !isset($data['email']) || !isset($data['role'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Prevent updating own role
if ($data['id'] == $_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cannot update your own user data']);
    exit;
}

// Check if user exists and get current role
$check_sql = "SELECT Role FROM users WHERE User_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("i", $data['id']);
$stmt->execute();
$result = $stmt->get_result();
$current_user = $result->fetch_assoc();

if (!$current_user) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Prevent changing admin role if there's only one admin
if ($current_user['Role'] === 'admin' && $data['role'] !== 'admin') {
    $admin_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE Role = 'admin'")->fetch_assoc()['count'];
    if ($admin_count <= 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cannot change role of the last admin user']);
        exit;
    }
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Check if email is already taken by another user
$email_check = "SELECT User_id FROM users WHERE Email = ? AND User_id != ?";
$stmt = $conn->prepare($email_check);
$stmt->bind_param("si", $data['email'], $data['id']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email is already taken']);
    exit;
}

// Update user
$sql = "UPDATE users 
        SET Fname = ?, 
            Lname = ?, 
            Email = ?, 
            Phone = ?, 
            Role = ? 
        WHERE User_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", 
    $data['firstName'],
    $data['lastName'],
    $data['email'],
    $data['phone'],
    $data['role'],
    $data['id']
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update user: ' . $stmt->error]);
}

$conn->close();
?>
