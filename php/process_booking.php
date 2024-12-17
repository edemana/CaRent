<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to book a car'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $car_id = (int)$_POST['car_id'];
    $pickup_date = $conn->real_escape_string($_POST['pickup_date']);
    $return_date = $conn->real_escape_string($_POST['return_date']);
    $pickup_location = $conn->real_escape_string($_POST['pickup_location']);

    // Check if car is available for the selected dates
    $check_sql = "SELECT id FROM bookings 
                  WHERE car_id = ? 
                  AND status != 'cancelled'
                  AND ((pickup_date BETWEEN ? AND ?) 
                  OR (return_date BETWEEN ? AND ?))";
    
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("issss", $car_id, $pickup_date, $return_date, $pickup_date, $return_date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Car is not available for the selected dates'
        ]);
        exit;
    }

    // Create booking
    $sql = "INSERT INTO bookings (user_id, car_id, pickup_date, return_date, pickup_location, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iisss", $user_id, $car_id, $pickup_date, $return_date, $pickup_location);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Booking successful'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Booking failed: ' . $conn->error
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Something went wrong: ' . $conn->error
        ]);
    }
}
?>
