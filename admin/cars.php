<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get car statistics
$stats = [];

// Total cars
$sql = "SELECT COUNT(*) as total FROM car";
$result = $conn->query($sql);
$stats['total_cars'] = $result->fetch_assoc()['total'];

// Available cars (cars that are not currently booked)
$sql = "SELECT COUNT(DISTINCT c.Vehicle_id) as available 
        FROM car c 
        LEFT JOIN bookings b ON c.Vehicle_id = b.car_id 
        WHERE b.id IS NULL 
        OR b.status IN ('cancelled', 'completed')
        OR (b.status IN ('confirmed', 'pending') 
            AND (b.return_date < CURDATE() 
                OR b.pickup_date > CURDATE()))";
$result = $conn->query($sql);
$stats['available_cars'] = $result->fetch_assoc()['available'];

// Cars by type
$sql = "SELECT Type, COUNT(*) as count FROM car GROUP BY Type";
$result = $conn->query($sql);
$stats['cars_by_type'] = [];
while ($row = $result->fetch_assoc()) {
    $stats['cars_by_type'][$row['Type']] = $row['count'];
}

// Most rented cars
$sql = "SELECT 
            c.Vehicle_id,
            CONCAT(c.Make, ' ', c.Model) as name, 
            COUNT(b.id) as rental_count 
        FROM car c 
        LEFT JOIN bookings b ON c.Vehicle_id = b.car_id 
        WHERE b.status NOT IN ('cancelled')
        GROUP BY c.Vehicle_id, c.Make, c.Model 
        ORDER BY rental_count DESC 
        LIMIT 5";
$result = $conn->query($sql);
$stats['most_rented'] = [];
while ($row = $result->fetch_assoc()) {
    $stats['most_rented'][] = $row;
}

// Average rental price
$sql = "SELECT AVG(RentalPrice) as avg_price FROM car WHERE RentalPrice > 0";
$result = $conn->query($sql);
$stats['avg_price'] = number_format($result->fetch_assoc()['avg_price'], 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars - CarRent Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0984e3;
            --secondary-color: #2d3436;
            --accent-color: #00b894;
            --background-color: #f5f6fa;
            --dark-color: #2d3436;
            --light-color: #ffffff;
            --gradient-start: #e8f4f8;
            --gradient-end: #f5f6fa;
            --sidebar-width: 250px;
        }

        body {
            background: var(--gradient-start);
            background-image: 
                linear-gradient(120deg, var(--gradient-start), var(--gradient-end)),
                url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z' fill='%230984e3' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--dark-color);
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .admin-sidebar {
            width: var(--sidebar-width);
            background-color: var(--secondary-color);
            color: white;
            padding: 2rem;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .admin-main {
            flex: 1;
            padding: 2rem;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }

        .car-card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin: 15px;
        }

        .car-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .car-image-container {
            position: relative;
            overflow: hidden;
            border-radius: 15px 15px 0 0;
        }

        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .car-card:hover .car-image {
            transform: scale(1.05);
        }

        .car-details {
            padding: 20px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 249, 250, 0.95));
        }

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-top: 2rem;
            overflow-x: auto;
        }

        .table {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary-color), #0770c2);
            color: white;
            border: none;
            padding: 15px;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(248, 249, 250, 0.5);
        }

        .table tbody tr:hover {
            background: rgba(9, 132, 227, 0.05);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #0770c2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0770c2, #065a9e);
            transform: translateY(-2px);
        }

        .modal-content {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .form-control {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(9, 132, 227, 0.1);
            outline: none;
        }

        .section-title {
            text-align: center;
            margin: 40px 0;
            color: var(--dark-color);
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-color), #0770c2);
            margin: 10px auto;
            border-radius: 2px;
        }

        ::-webkit-scrollbar {
            display: none;
        }
        .analytics-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .analytics-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        }

        .analytics-card h3 {
            color: #2d3436;
            font-size: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .analytics-card .value {
            font-size: 2rem;
            font-weight: 600;
            color: #0984e3;
            margin-bottom: 0.5rem;
        }

        .analytics-card .subtext {
            color: #636e72;
            font-size: 0.9rem;
        }
    .logout-btn {
    padding: 0.5rem 1rem;
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
}

        .type-distribution {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .type-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .type-item:last-child {
            border-bottom: none;
        }

        .type-name {
            color: #2d3436;
            font-weight: 500;
        }

        .type-count {
            color: #0984e3;
            font-weight: 600;
        }

        .most-rented-list {
            list-style: none;
            padding: 0;
        }

        .most-rented-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .most-rented-item:last-child {
            border-bottom: none;
        }

        .most-rented-name {
            color: #2d3436;
        }

        .most-rented-count {
            background: #e1f5fe;
            color: #0984e3;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-top: 2rem;
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .admin-table th,
        .admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .admin-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2d3436;
        }

        .admin-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .car-image-small {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .car-status {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-available {
            background-color: #e3fcef;
            color: #00b894;
        }

        .status-unavailable {
            background-color: #fff3bf;
            color: #f39c12;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit,
        .btn-delete {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-edit {
            background-color: #e3f2fd;
            color: #0984e3;
        }

        .btn-delete {
            background-color: #ffe3e3;
            color: #e74c3c;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            overflow-y: auto;
            padding: 20px;
        }

        .modal-content {
            background-color: #fff;
            max-width: 700px;
            width: 90%;
            margin: 30px auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: relative;
            max-height: calc(100vh - 60px);
            overflow-y: auto;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #000;
        }

        #modalTitle {
            margin-top: 0;
            margin-bottom: 20px;
            padding-right: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-actions {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 15px auto;
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        <main class="admin-main">
            <header class="admin-header">

                <h1>Manage Cars</h1>
                <a href="../php/logout.php" class="logout-btn">Logout</a>
                <button onclick="showAddCarModal()" class="btn-primary">
                    <i class="fas fa-plus"></i> Add New Car
                </button>
            </header>

            <!-- Analytics Section -->
            <div class="analytics-container">
                <div class="analytics-card">
                    <h3><i class="fas fa-car"></i> Total Cars</h3>
                    <div class="value"><?php echo $stats['total_cars']; ?></div>
                    <div class="subtext">Vehicles in fleet</div>
                </div>

                <div class="analytics-card">
                    <h3><i class="fas fa-check-circle"></i> Available Cars</h3>
                    <div class="value"><?php echo $stats['available_cars']; ?></div>
                    <div class="subtext">Ready for rental</div>
                </div>

                <div class="analytics-card">
                    <h3><i class="fas fa-dollar-sign"></i> Average Price</h3>
                    <div class="value">$<?php echo $stats['avg_price']; ?></div>
                    <div class="subtext">Per day</div>
                </div>

                <div class="analytics-card">
                    <h3><i class="fas fa-chart-pie"></i> Car Types</h3>
                    <div class="type-distribution">
                        <?php foreach ($stats['cars_by_type'] as $type => $count): ?>
                            <div class="type-item">
                                <span class="type-name"><?php echo ucfirst($type); ?></span>
                                <span class="type-count"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="analytics-card">
                    <h3><i class="fas fa-star"></i> Most Rented Cars</h3>
                    <ul class="most-rented-list">
                        <?php foreach ($stats['most_rented'] as $car): ?>
                            <li class="most-rented-item">
                                <span class="most-rented-name"><?php echo $car['name']; ?></span>
                                <span class="most-rented-count"><?php echo $car['rental_count']; ?> rentals</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h2>Cars List</h2>
                    <div class="table-actions">
                        <input type="text" id="searchCar" placeholder="Search cars..." class="search-input">
                        <select id="filterType" class="filter-select">
                            <option value="">All Types</option>
                            <option value="sedan">Sedan</option>
                            <option value="suv">SUV</option>
                            <option value="luxury">Luxury</option>
                            <option value="sports">Sports</option>
                        </select>
                        <select id="filterStatus" class="filter-select">
                            <option value="">All Status</option>
                            <option value="1">Available</option>
                            <option value="0">Not Available</option>
                        </select>
                    </div>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Make & Model</th>
                            <th>Type</th>
                            <th>Rental Price</th>
                            <th>Transmission</th>
                            <th>Fuel Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="carsTableBody">
                        <!-- Cars will be loaded dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Add/Edit Car Modal -->
            <div id="carModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2 id="modalTitle">Add New Car</h2>
                    <form id="carForm" class="admin-form" enctype="multipart/form-data">
                        <input type="hidden" id="carId" name="carId">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Car Name (Make Model)</label>
                                <input type="text" id="name" name="name" placeholder="e.g., Toyota Camry" required>
                            </div>
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input type="number" id="year" name="year" min="1900" max="2024" value="2024" required>
                            </div>
                            <div class="form-group">
                                <label for="type">Car Type</label>
                                <select id="type" name="type" required>
                                    <option value="sedan">Sedan</option>
                                    <option value="suv">SUV</option>
                                    <option value="luxury">Luxury</option>
                                    <option value="sports">Sports</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="transmission">Transmission</label>
                                <select id="transmission" name="transmission" required>
                                    <option value="automatic">Automatic</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fuel_type">Fuel Type</label>
                                <select id="fuel_type" name="fuel_type" required>
                                    <option value="petrol">Petrol</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="hybrid">Hybrid</option>
                                    <option value="electric">Electric</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price">Price per Day ($)</label>
                                <input type="number" id="price" name="price" min="0" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="engine_size">Engine Size</label>
                                <input type="text" id="engine_size" name="engine_size" placeholder="e.g., 2.5L">
                            </div>
                            <div class="form-group">
                                <label for="fuel_consumption">Fuel Consumption (L/100km)</label>
                                <input type="number" id="fuel_consumption" name="fuel_consumption" min="0" step="0.1">
                            </div>
                            <div class="form-group">
                                <label for="mileage">Mileage</label>
                                <input type="number" id="mileage" name="mileage" min="0">
                            </div>
                            <div class="form-group">
                                <label for="rental_company">Rental Company</label>
                                <input type="text" id="rental_company" name="rental_company" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" required>
                            </div>
                            <div class="form-group">
                                <label for="accessories">Accessories</label>
                                <input type="text" id="accessories" name="accessories" placeholder="Comma-separated list">
                            </div>
                            <div class="form-group">
                                <label for="functionalities">Functionalities</label>
                                <input type="text" id="functionalities" name="functionalities" placeholder="Comma-separated list">
                            </div>
                            <div class="form-group">
                                <label for="rental_conditions">Rental Conditions</label>
                                <textarea id="rental_conditions" name="rental_conditions" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">Car Image</label>
                                <input type="file" id="image" name="image" accept="image/*">
                                <div id="currentImage"></div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" onclick="closeCarModal()" class="btn-secondary">Cancel</button>
                            <button type="submit" class="btn-primary">Save Car</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadCars();
            setupFilters();
        });

        function setupFilters() {
            const searchInput = document.getElementById('searchCar');
            const typeFilter = document.getElementById('filterType');
            const statusFilter = document.getElementById('filterStatus');

            searchInput.addEventListener('input', loadCars);
            typeFilter.addEventListener('change', loadCars);
            statusFilter.addEventListener('change', loadCars);
        }

        async function loadCars() {
            const search = document.getElementById('searchCar').value;
            const type = document.getElementById('filterType').value;
            const status = document.getElementById('filterStatus').value;

            try {
                const response = await fetch('../php/admin/get_cars.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ search, type, status })
                });
                const cars = await response.json();
                
                const tbody = document.getElementById('carsTableBody');
                tbody.innerHTML = '';
                
                cars.forEach(car => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><img src="${car.image}" alt="${car.Make} ${car.Model}" class="car-image-small"></td>
                        <td>${car.Make} ${car.Model}</td>
                        <td>${car.Type}</td>
                        <td>$${car.RentalPrice}/day</td>
                        <td>${car.Transmission}</td>
                        <td>${car.FuelType}</td>
                        <td>
                            <span class="car-status ${car.Status ? 'status-available' : 'status-unavailable'}">
                                ${car.Status ? 'Available' : 'Not Available'}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button onclick="editCar(${car.Vehicle_id})" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button onclick="deleteCar(${car.Vehicle_id})" class="btn-delete">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function showAddCarModal() {
            document.getElementById('modalTitle').textContent = 'Add New Car';
            document.getElementById('carId').value = '';
            document.getElementById('carForm').reset();
            document.getElementById('carModal').style.display = 'block';
        }

        function closeCarModal() {
            document.getElementById('carModal').style.display = 'none';
        }

        async function editCar(carId) {
            try {
                const response = await fetch(`../php/admin/get_car.php?id=${carId}`);
                const car = await response.json();
                
                document.getElementById('modalTitle').textContent = 'Edit Car';
                document.getElementById('carId').value = car.Vehicle_id;
                document.getElementById('name').value = `${car.Make} ${car.Model}`;
                document.getElementById('year').value = car.Year;
                document.getElementById('type').value = car.Type;
                document.getElementById('transmission').value = car.Transmission;
                document.getElementById('fuel_type').value = car.FuelType;
                document.getElementById('price').value = car.RentalPrice;
                document.getElementById('engine_size').value = car.EngineSize;
                document.getElementById('fuel_consumption').value = car.FuelConsumption;
                document.getElementById('mileage').value = car.Mileage;
                document.getElementById('rental_company').value = car.RentalCompany;
                document.getElementById('address').value = car.Address;
                document.getElementById('description').value = car.Description;
                document.getElementById('rental_conditions').value = car.RentalConditions;
                
                // Handle JSON arrays
                document.getElementById('accessories').value = car.Accessories ? JSON.parse(car.Accessories).join(', ') : '';
                document.getElementById('functionalities').value = car.Functionalities ? JSON.parse(car.Functionalities).join(', ') : '';
                
                // Show current image if exists
                const currentImageDiv = document.getElementById('currentImage');
                if (car.Img) {
                    currentImageDiv.innerHTML = `<img src="${car.Img}" alt="Current car image" style="max-width: 200px; margin-top: 10px;">`;
                } else {
                    currentImageDiv.innerHTML = '';
                }
                
                document.getElementById('carModal').style.display = 'block';
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load car details. Please try again.');
            }
        }

        async function deleteCar(carId) {
            if (confirm('Are you sure you want to delete this car?')) {
                try {
                    const response = await fetch('../php/admin/delete_car.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: carId })
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        loadCars();
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        }

        document.getElementById('carForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            try {
                const formData = new FormData(e.target);
                
                const response = await fetch('../php/admin/save_car.php', {
                    method: 'POST',
                    body: formData
                });
                
                let result;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    throw new Error('Server returned non-JSON response');
                }
                
                if (!response.ok) {
                    throw new Error(result.message || `Server returned ${response.status}`);
                }
                
                if (result.success) {
                    closeCarModal();
                    loadCars();
                    alert('Car saved successfully!');
                } else {
                    throw new Error(result.message || 'Failed to save car');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to save car. Please try again.');
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('carModal');
            if (event.target == modal) {
                closeCarModal();
            }
        }
    </script>
</body>
</html>
