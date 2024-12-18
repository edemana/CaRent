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

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id'])) {
        throw new Exception('Car ID is required');
    }
    
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
    $img_sql = "SELECT Img FROM car WHERE Vehicle_id = ?";
    $img_stmt = $conn->prepare($img_sql);
    $img_stmt->bind_param("i", $car_id);
    $img_stmt->execute();
    $result = $img_stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) {
        throw new Exception('Car not found');
    }
    
    $image_path = $row['Img'];

    // Delete car
    $sql = "DELETE FROM car WHERE Vehicle_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $car_id);

    if ($stmt->execute()) {
        // Delete image file if it exists and is a local file
        if (!empty($image_path) && !filter_var($image_path, FILTER_VALIDATE_URL)) {
            $full_path = "../../uploads/cars/" . $image_path;
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Car deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete car');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

//$stmt->close();
//$conn->close();
?>
