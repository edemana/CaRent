<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = (int)$data['id'];
$user_id = $_SESSION['user_id'];

// Check if booking belongs to user and is pending
$check_sql = "SELECT id FROM bookings WHERE id = ? AND user_id = ? AND status = 'pending'";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $booking_id, $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking or cannot be cancelled'
    ]);
    exit;
}

// Cancel booking
$sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Booking cancelled successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to cancel booking'
    ]);
}

$stmt->close();
//$conn->close();
?>
