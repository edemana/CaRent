<?php
require_once 'config.php';

$sql = "CREATE TABLE IF NOT EXISTS booking_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (admin_id) REFERENCES users(User_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Booking logs table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
