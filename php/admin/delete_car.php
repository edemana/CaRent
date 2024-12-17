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
$car_id = (int)$data['id'];

// Check if car has active bookings
$check_sql = "SELECT COUNT(*) as count FROM bookings WHERE car_id = ? AND status IN ('pending', 'active')";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $car_id);
$check_stmt->execute();
$result = $check_stmt->get_result();
$active_bookings = $result->fetch_assoc()['count'];

if ($active_bookings > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Cannot delete car with active bookings'
    ]);
    exit;
}

// Get car image path
$img_sql = "SELECT image FROM cars WHERE id = ?";
$img_stmt = $conn->prepare($img_sql);
$img_stmt->bind_param("i", $car_id);
$img_stmt->execute();
$image_path = $img_stmt->get_result()->fetch_assoc()['image'];

// Delete car
$sql = "DELETE FROM cars WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $car_id);

if ($stmt->execute()) {
    // Delete image file if exists
    if (!empty($image_path)) {
        $full_path = "../../" . $image_path;
        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Car deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete car'
    ]);
}

$stmt->close();
//$conn->close();
?>
