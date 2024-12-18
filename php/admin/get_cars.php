<?php
session_start();
require_once '../config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// Get search parameters
$data = json_decode(file_get_contents('php://input'), true);
$search = isset($data['search']) ? $data['search'] : '';
$type = isset($data['type']) ? $data['type'] : '';
$status = isset($data['status']) ? $data['status'] : '';

try {
    // Base query
    $sql = "SELECT 
                c.*,
                CASE 
                    WHEN NOT EXISTS (
                        SELECT 1 FROM bookings b 
                        WHERE b.car_id = c.Vehicle_id 
                        AND b.status IN ('pending', 'confirmed')
                        AND b.return_date >= CURDATE()
                    ) THEN 1
                    ELSE 0
                END as Status
            FROM car c 
            WHERE 1=1";

    $params = array();
    $types = "";

    // Add search conditions
    if (!empty($search)) {
        $sql .= " AND (c.Make LIKE ? OR c.Model LIKE ? OR c.Year LIKE ?)";
        $search = "%{$search}%";
        $types .= "sss";
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }

    // Add type filter
    if (!empty($type)) {
        $sql .= " AND c.Type = ?";
        $types .= "s";
        $params[] = $type;
    }

    // Add status filter
    if ($status !== '') {
        if ($status == '1') {
            $sql .= " AND NOT EXISTS (
                SELECT 1 FROM bookings b 
                WHERE b.car_id = c.Vehicle_id 
                AND b.status IN ('pending', 'confirmed')
                AND b.return_date >= CURDATE()
            )";
        } else {
            $sql .= " AND EXISTS (
                SELECT 1 FROM bookings b 
                WHERE b.car_id = c.Vehicle_id 
                AND b.status IN ('pending', 'confirmed')
                AND b.return_date >= CURDATE()
            )";
        }
    }

    $sql .= " ORDER BY c.Vehicle_id DESC";

    $stmt = $conn->prepare($sql);

    // Bind parameters if any
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    // Execute and get results
    $stmt->execute();
    $result = $stmt->get_result();
    $cars = array();

    while ($row = $result->fetch_assoc()) {
        try {
            // Handle image path
            $row['image'] = !empty($row['Img']) 
                ? (filter_var($row['Img'], FILTER_VALIDATE_URL) ? $row['Img'] : "../uploads/cars/" . $row['Img']) 
                : "../images/car-placeholder.jpg";
            
            // Safely decode JSON fields
            $row['Accessories'] = !empty($row['Accessories']) ? json_decode($row['Accessories'], true) : [];
            if (json_last_error() !== JSON_ERROR_NONE) {
                $row['Accessories'] = [];
            }
            
            $row['Functionalities'] = !empty($row['Functionalities']) ? json_decode($row['Functionalities'], true) : [];
            if (json_last_error() !== JSON_ERROR_NONE) {
                $row['Functionalities'] = [];
            }
            
            $cars[] = $row;
        } catch (Exception $e) {
            // Log the error but continue processing other cars
            error_log("Error processing car {$row['Vehicle_id']}: " . $e->getMessage());
            continue;
        }
    }

    echo json_encode($cars);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Failed to encode cars data: " . json_last_error_msg());
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 
// finally {
//     if (isset($stmt)) {
//         $stmt->close();
//     }
//     if (isset($conn)) {
//         $conn->close();
//     }
// }
