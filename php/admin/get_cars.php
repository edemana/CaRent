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

$data = json_decode(file_get_contents('php://input'), true);

$search = $data['search'] ?? '';
$type = $data['type'] ?? '';
$status = $data['status'] ?? '';

$sql = "SELECT c.*, 
        (NOT EXISTS (
            SELECT 1 FROM bookings b 
            WHERE b.car_id = c.Vehicle_id 
            AND b.status IN ('pending', 'active')
        )) as Status 
        FROM car c WHERE 1=1";

if (!empty($search)) {
    $search = "%$search%";
    $sql .= " AND (c.Make LIKE ? OR c.Model LIKE ?)";
}

if (!empty($type)) {
    $sql .= " AND c.Type = ?";
}

if ($status !== '') {
    if ($status == '1') {
        $sql .= " AND NOT EXISTS (
            SELECT 1 FROM bookings b 
            WHERE b.car_id = c.Vehicle_id 
            AND b.status IN ('pending', 'active')
        )";
    } else {
        $sql .= " AND EXISTS (
            SELECT 1 FROM bookings b 
            WHERE b.car_id = c.Vehicle_id 
            AND b.status IN ('pending', 'active')
        )";
    }
}

$sql .= " ORDER BY c.Vehicle_id DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $types .= "ss";
        $params[] = $search;
        $params[] = $search;
    }
    
    if (!empty($type)) {
        $types .= "s";
        $params[] = $type;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $cars = [];
    
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
    
    echo json_encode($cars);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare statement']);
}

$stmt->close();
$conn->close();
?>
