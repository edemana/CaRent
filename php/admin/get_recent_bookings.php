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

$sql = "SELECT a.Vehicle_id as id, 
        CONCAT(u.Name) as user_name, 
        CONCAT(c.Make, ' ', c.Model) as car_name,
        a.Available_start as pickup_date,
        a.Available_end as return_date,
        CASE 
            WHEN CURRENT_DATE BETWEEN a.Available_start AND a.Available_end THEN 'Active'
            WHEN CURRENT_DATE < a.Available_start THEN 'Pending'
            ELSE 'Completed'
        END as status
        FROM availability a
        JOIN car c ON a.Vehicle_id = c.Vehicle_id
        JOIN bookings b ON b.id = a.Vehicle_id 
            AND b.Pickup_date = a.Available_start 
            AND b.Return_date = a.Available_end
        JOIN users u ON u.User_id = b.User_id
        ORDER BY a.Available_start DESC
        LIMIT 10";

$result = $conn->query($sql);
$bookings = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookings[] = [
            'id' => $row['id'],
            'user_name' => $row['user_name'],
            'car_name' => $row['car_name'],
            'pickup_date' => $row['pickup_date'],
            'return_date' => $row['return_date'],
            'status' => $row['status']
        ];
    }
}

echo json_encode($bookings);

//$conn->close();
?>
