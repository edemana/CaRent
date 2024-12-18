<?php
session_start();
require_once 'config.php';
require_once 'helpers/Mailer.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

try {
    // Get booking data
    $vehicle_id = $_POST['vehicle_id'];
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    $total_amount = $_POST['total_amount'];
    $user_id = $_SESSION['user_id'];
    
    // Start transaction
    $conn->begin_transaction();

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO booking (Vehicle_id, User_id, Pickup_date, Return_date, Total_amount, Status) 
                           VALUES (?, ?, ?, ?, ?, 'Pending')");
    
    $stmt->bind_param("iissd", $vehicle_id, $user_id, $pickup_date, $return_date, $total_amount);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to save booking');
    }
    
    $booking_id = $conn->insert_id;
    
    // Get car details
    $stmt = $conn->prepare("SELECT * FROM car WHERE Vehicle_id = ?");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $car = $stmt->get_result()->fetch_assoc();
    
    // Get user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE User_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    // Prepare data for email
    $booking = [
        'booking_id' => $booking_id,
        'pickup_date' => $pickup_date,
        'return_date' => $return_date,
        'total_amount' => $total_amount
    ];
    
    $customer = [
        'name' => $user['Name'],
        'email' => $user['Email']
    ];
    
    // Send emails
    $mailer = Mailer::getInstance();
    $customerEmailSent = $mailer->sendBookingConfirmation($booking, $customer, $car);
    $adminEmailSent = $mailer->sendBookingNotificationToAdmin($booking, $customer, $car);
    
    if (!$customerEmailSent || !$adminEmailSent) {
        error_log("Failed to send some notifications for booking ID: $booking_id");
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Booking saved successfully! Check your email for confirmation.',
        'booking_id' => $booking_id
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollback();
    }
    
    error_log("Error in save_booking.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
