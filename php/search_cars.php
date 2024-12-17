<?php
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$location = $conn->real_escape_string($data['location']);
$pickup_date = $conn->real_escape_string($data['pickupDate']);
$return_date = $conn->real_escape_string($data['returnDate']);

$sql = "SELECT c.Vehicle_id as id, 
        CONCAT(c.Make, ' ', c.Model) as name, 
        c.Description as description, 
        c.RentalPrice as price, 
        c.Img as image
        FROM car c
        JOIN availability a ON c.Vehicle_id = a.Vehicle_id
        WHERE c.Address LIKE ?
        AND a.Available_start <= ?
        AND a.Available_end >= ?";

if ($stmt = $conn->prepare($sql)) {
    $location = "%$location%";
    $stmt->bind_param("sss", $location, $return_date, $pickup_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cars = [];
    while ($row = $result->fetch_assoc()) {
        $cars[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'image' => $row['image']
        ];
    }
    
    echo json_encode($cars);
    
    $stmt->close();
}

//$conn->close();
?>
