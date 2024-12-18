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

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No user ID provided']);
    exit;
}

// Prevent deleting self
if ($data['id'] == $_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
    exit;
}

// Check if user exists and is not an admin
$check_user = "SELECT Role FROM users WHERE User_id = ?";
$stmt = $conn->prepare($check_user);
$stmt->bind_param("i", $data['id']);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

if ($result['Role'] === 'admin') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cannot delete admin users']);
    exit;
}

// Check if user has any active bookings
$check_sql = "SELECT COUNT(*) as count FROM bookings WHERE user_id = ? AND status IN ('pending', 'active')";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $data['id']);
$check_stmt->execute();
$result = $check_stmt->get_result()->fetch_assoc();

if ($result['count'] > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cannot delete user with active bookings']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Delete user's bookings first
    $delete_bookings = "DELETE FROM bookings WHERE user_id = ?";
    $stmt = $conn->prepare($delete_bookings);
    $stmt->bind_param("i", $data['id']);
    $stmt->execute();

    // Then delete the user
    $delete_user = "DELETE FROM users WHERE User_id = ?";
    $stmt = $conn->prepare($delete_user);
    $stmt->bind_param("i", $data['id']);
    $stmt->execute();

    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
}

$conn->close();
?>
