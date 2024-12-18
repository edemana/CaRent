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

// Get and decode JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['role'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Split name into first and last name
$name_parts = explode(' ', $data['name'], 2);
$fname = $name_parts[0];
$lname = isset($name_parts[1]) ? $name_parts[1] : '';

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Check if email already exists
$check_email = "SELECT User_id FROM users WHERE Email = ?";
$stmt = $conn->prepare($check_email);
$stmt->bind_param("s", $data['email']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    exit;
}

// Validate role
$role = strtolower($data['role']);
if (!in_array($role, ['user', 'admin'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

// Hash password
$hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

// Insert new user
$sql = "INSERT INTO users (Fname, Lname, Email, Phone, Password, Role) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", 
    $fname,
    $lname,
    $data['email'],
    $data['phone'],
    $hashed_password,
    $role
);

try {
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'User added successfully',
            'userId' => $conn->insert_id
        ]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add user: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
