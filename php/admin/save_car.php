<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once '../config.php';

ob_clean();
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
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
    $description = $_POST['description'] ?? '';
    $engine_size = $_POST['engine_size'] ?? '';
    $fuel_consumption = (float)($_POST['fuel_consumption'] ?? 0);
    $rental_company = $_POST['rental_company'] ?? '';
    $address = $_POST['address'] ?? '';
    $rental_conditions = $_POST['rental_conditions'] ?? '';
    $mileage = (int)($_POST['mileage'] ?? 0);
    $year = (int)($_POST['year'] ?? date('Y'));
    
    // Optional JSON fields
    $accessories = isset($_POST['accessories']) ? json_encode(explode(',', $_POST['accessories'])) : null;
    $functionalities = isset($_POST['functionalities']) ? json_encode(explode(',', $_POST['functionalities'])) : null;

    // Validate required fields
    if (empty($name) || empty($type) || $rental_price <= 0) {
        throw new Exception('Please fill in all required fields');
    }

    // Split name into make and model
    $name_parts = explode(' ', $name, 2);
    if (count($name_parts) < 2) {
        throw new Exception('Please enter both make and model (e.g., "Toyota Camry")');
    }
    $make = $name_parts[0];
    $model = $name_parts[1];

    // Handle file upload
    $image_filename = '';
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "../../uploads/cars/";
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }

        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed');
        }

        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            throw new Exception('Failed to upload image');
        }
        
        $image_filename = $new_filename;
    }

    // Prepare SQL statement
    if (empty($vehicle_id)) {
        // Insert new car
        $sql = "INSERT INTO car (Model, Year, Type, Make, Img, Description, 
                FuelConsumption, EngineSize, RentalPrice, RentalCompany, 
                Address, RentalConditions, Mileage, Accessories, Functionalities) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        $stmt->bind_param("sissssdsdssssss", 
            $model,
            $year,
            $type,
            $make,
            $image_filename,
            $description,
            $fuel_consumption,
            $engine_size,
            $rental_price,
            $rental_company,
            $address,
            $rental_conditions,
            $mileage,
            $accessories,
            $functionalities
        );
    } else {
        // Update existing car
        if (!empty($image_filename)) {
            $sql = "UPDATE car SET 
                    Model = ?, Year = ?, Type = ?, Make = ?, Img = ?, 
                    Description = ?, FuelConsumption = ?, EngineSize = ?, 
                    RentalPrice = ?, RentalCompany = ?, Address = ?, 
                    RentalConditions = ?, Mileage = ?, 
                    Accessories = ?, Functionalities = ?
                    WHERE Vehicle_id = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Failed to prepare statement: ' . $conn->error);
            }
            
            $stmt->bind_param("sissssdsdsssssi", 
                $model,
                $year,
                $type,
                $make,
                $image_filename,
                $description,
                $fuel_consumption,
                $engine_size,
                $rental_price,
                $rental_company,
                $address,
                $rental_conditions,
                $mileage,
                $accessories,
                $functionalities,
                $vehicle_id
            );
        } else {
            $sql = "UPDATE car SET 
                    Model = ?, Year = ?, Type = ?, Make = ?, 
                    Description = ?, FuelConsumption = ?, EngineSize = ?, 
                    RentalPrice = ?, RentalCompany = ?, Address = ?, 
                    RentalConditions = ?, Mileage = ?, 
                    Accessories = ?, Functionalities = ?
                    WHERE Vehicle_id = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Failed to prepare statement: ' . $conn->error);
            }
            
            $stmt->bind_param("sissdsdsssssssi", 
                $model,
                $year,
                $type,
                $make,
                $description,
                $fuel_consumption,
                $engine_size,
                $rental_price,
                $rental_company,
                $address,
                $rental_conditions,
                $mileage,
                $accessories,
                $functionalities,
                $vehicle_id
            );
        }
    }

    if (!$stmt->execute()) {
        throw new Exception('Failed to save car: ' . $stmt->error);
    }

    ob_clean();
    echo json_encode(['success' => true, 'message' => 'Car saved successfully']);

} catch (Exception $e) {
    error_log("Error in save_car.php: " . $e->getMessage());
    ob_clean();
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

ob_end_flush();
