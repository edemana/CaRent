<?php
session_start();
require_once '../config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['name']) || !isset($data['email']) || !isset($data['role'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Split name into first and last name
$name_parts = explode(' ', $data['name'], 2);
$fname = $name_parts[0];
$lname = isset($name_parts[1]) ? $name_parts[1] : '';

$sql = "UPDATE users 
        SET Fname = ?, 
            Lname = ?, 
            Email = ?, 
            Phone = ?, 
            Role = ? 
        WHERE User_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", 
    $fname,
    $lname,
    $data['email'],
    $data['phone'],
    $data['role'],
    $data['id']
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update user']);
}
?>
