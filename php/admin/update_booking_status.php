<?php
session_start();
require_once '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['booking_id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$booking_id = $data['booking_id'];
$status = $data['status'];

// Validate status
$allowed_statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
if (!in_array($status, $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit;
}

try {
    // Update booking status
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    
    if ($stmt->execute()) {
        // Log the status change
        $admin_id = $_SESSION['user_id'];
        $log_stmt = $conn->prepare("INSERT INTO booking_logs (booking_id, admin_id, action, created_at) VALUES (?, ?, ?, NOW())");
        $action = "Status changed to " . $status;
        $log_stmt->bind_param("iis", $booking_id, $admin_id, $action);
        $log_stmt->execute();
        
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Failed to update booking status");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
