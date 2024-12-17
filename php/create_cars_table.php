<?php
require_once 'config.php';

try {
    // Create cars table
    $sql = "CREATE TABLE IF NOT EXISTS cars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        type VARCHAR(50) NOT NULL,
        description TEXT,
        seats INT NOT NULL,
        fuel_type VARCHAR(50) NOT NULL,
        transmission VARCHAR(50) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);

    // Insert sample cars if the table is empty
    $count = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
    
    if ($count == 0) {
        $sample_cars = [
            [
                'name' => 'Toyota Camry',
                'type' => 'sedan',
                'description' => 'Comfortable and reliable sedan perfect for family trips.',
                'seats' => 5,
                'fuel_type' => 'Gasoline',
                'transmission' => 'Automatic',
                'price' => 50.00,
                'image' => 'images/cars/camry.jpg'
            ],
            [
                'name' => 'Honda CR-V',
                'type' => 'suv',
                'description' => 'Spacious SUV with excellent safety features.',
                'seats' => 5,
                'fuel_type' => 'Gasoline',
                'transmission' => 'Automatic',
                'price' => 65.00,
                'image' => 'images/cars/crv.jpg'
            ],
            [
                'name' => 'BMW 5 Series',
                'type' => 'luxury',
                'description' => 'Luxury sedan with premium features and powerful performance.',
                'seats' => 5,
                'fuel_type' => 'Gasoline',
                'transmission' => 'Automatic',
                'price' => 120.00,
                'image' => 'images/cars/bmw5.jpg'
            ],
            [
                'name' => 'Ford Mustang',
                'type' => 'sports',
                'description' => 'Iconic sports car with thrilling performance.',
                'seats' => 4,
                'fuel_type' => 'Gasoline',
                'transmission' => 'Manual',
                'price' => 100.00,
                'image' => 'images/cars/mustang.jpg'
            ],
            [
                'name' => 'Mercedes-Benz S-Class',
                'type' => 'luxury',
                'description' => 'Ultimate luxury sedan with cutting-edge technology.',
                'seats' => 5,
                'fuel_type' => 'Gasoline',
                'transmission' => 'Automatic',
                'price' => 200.00,
                'image' => 'images/cars/sclass.jpg'
            ],
            [
                'name' => 'Tesla Model 3',
                'type' => 'sedan',
                'description' => 'All-electric sedan with impressive range and features.',
                'seats' => 5,
                'fuel_type' => 'Electric',
                'transmission' => 'Automatic',
                'price' => 85.00,
                'image' => 'images/cars/model3.jpg'
            ]
        ];

        $sql = "INSERT INTO cars (name, type, description, seats, fuel_type, transmission, price, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        foreach ($sample_cars as $car) {
            $stmt->execute([
                $car['name'],
                $car['type'],
                $car['description'],
                $car['seats'],
                $car['fuel_type'],
                $car['transmission'],
                $car['price'],
                $car['image']
            ]);
        }
    }

    echo "Cars table created successfully with sample data!";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
