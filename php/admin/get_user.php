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

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

$userId = $_GET['id'];

// Prevent accessing own user data through this endpoint
if ($userId == $_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cannot edit own user data through this endpoint']);
    exit;
}

$sql = "SELECT User_id, Fname, Lname, Email, Phone, Role FROM users WHERE User_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'data' => $user
    ]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

$conn->close();
?>
