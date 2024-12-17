<?php
require_once 'config.php';

header('Content-Type: application/json');

$sql = "SELECT Vehicle_id as id, Model as name, Description as description, RentalPrice as price, Img as image 
        FROM car 
        WHERE Type_id = 1";
$result = $conn->query($sql);

$cars = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cars[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'image' => $row['image']
        ];
    }
}

echo json_encode($cars);

// //$conn->close();
?>
