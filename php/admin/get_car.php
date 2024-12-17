<?php
session_start();
require_once '../config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $car_id = (int)$_GET['id'];
    
    $sql = "SELECT * FROM cars WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($car = $result->fetch_assoc()) {
        echo json_encode($car);
    } else {
        echo json_encode(['error' => 'Car not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'No car ID provided']);
}

//$conn->close();
?>
