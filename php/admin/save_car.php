<?php
// Start output buffering to catch any unwanted output
ob_start();

// Disable error display but keep logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once '../config.php';

// Clear any existing output
ob_clean();

// Set JSON header
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Debug POST data
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));

    if (!isset($conn)) {
        throw new Exception('Database connection failed');
    }

    // Get form data
    $vehicle_id = $_POST['carId'] ?? '';
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $rental_price = (float)($_POST['price'] ?? 0);
    $seats = (int)($_POST['seats'] ?? 0);
    $transmission = $_POST['transmission'] ?? '';
    $fuel_type = $_POST['fuel_type'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validate required fields
    if (empty($name) || empty($type) || $rental_price <= 0 || $seats <= 0) {
        throw new Exception('Please fill in all required fields');
    }

    // Split name into make and model
    $name_parts = explode(' ', $name, 2);
    $make = $name_parts[0];
    $model = $name_parts[1] ?? '';

    // Handle file upload
    $image_path = '';
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "../../uploads/cars/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }

        // Validate file type
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed');
        }

        // Generate unique filename
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Move uploaded file
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            throw new Exception('Failed to upload image');
        }
        
        $image_path = 'uploads/cars/' . $new_filename;
    }

    // Prepare SQL statement
    if (empty($vehicle_id)) {
        // Insert new car
        $sql = "INSERT INTO car (Make, Model, Type, Description, RentalPrice, Image, Seats, Transmission, FuelType) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        $stmt->bind_param("ssssdisss", 
            $make, $model, $type, $description, $rental_price, 
            $image_path, $seats, $transmission, $fuel_type
        );
    } else {
        // Update existing car
        if (!empty($image_path)) {
            $sql = "UPDATE car SET 
                    Make = ?, Model = ?, Type = ?, Description = ?, 
                    RentalPrice = ?, Image = ?, Seats = ?, 
                    Transmission = ?, FuelType = ? 
                    WHERE Vehicle_id = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Failed to prepare statement: ' . $conn->error);
            }
            
            $stmt->bind_param("ssssdssssi", 
                $make, $model, $type, $description, $rental_price, 
                $image_path, $seats, $transmission, $fuel_type, $vehicle_id
            );
        } else {
            $sql = "UPDATE car SET 
                    Make = ?, Model = ?, Type = ?, Description = ?, 
                    RentalPrice = ?, Seats = ?, 
                    Transmission = ?, FuelType = ? 
                    WHERE Vehicle_id = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Failed to prepare statement: ' . $conn->error);
            }
            
            $stmt->bind_param("ssssdsssi", 
                $make, $model, $type, $description, $rental_price, 
                $seats, $transmission, $fuel_type, $vehicle_id
            );
        }
    }

    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception('Failed to save car: ' . $stmt->error);
    }

    // Clear any output that might have been generated
    ob_clean();
    
    // Return success response
    echo json_encode(['success' => true, 'message' => 'Car saved successfully']);

} catch (Exception $e) {
    error_log("Error in save_car.php: " . $e->getMessage());
    
    // Clear any output that might have been generated
    ob_clean();
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    // Close database connections
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}

// End output buffering and flush
ob_end_flush();
