<?php
session_start();
require_once 'php/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Get car details
$car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
$car = null;

if ($car_id > 0) {
    $sql = "SELECT Vehicle_id as id, 
            CONCAT(Make, ' ', Model) as name,
            Description as description,
            RentalPrice as price,
            Img as image,
            Type as type,
            FuelConsumption as fuel_consumption,
            EngineSize as engine_size,
            Mileage as mileage
            FROM car 
            WHERE Vehicle_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
}

if (!$car) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Car - CarRent</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .booking-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .car-summary {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .car-summary img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
        .car-info {
            flex: 1;
        }
        .car-info h3 {
            margin: 0 0 1rem 0;
            color: var(--primary-color);
        }
        .car-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 1rem 0;
        }
        .booking-form {
            display: grid;
            gap: 1.5rem;
            padding: 1rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .form-group label {
            font-weight: 500;
            color: #333;
        }
        .form-group input {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            text-align: right;
        }
        .book-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .book-btn:hover {
            background-color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="booking-container">
        <h2>Book Your Car</h2>
        
        <div class="car-summary">
            <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
            <div class="car-info">
                <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                <p><?php echo htmlspecialchars($car['description']); ?></p>
                <p class="car-price">$<?php echo htmlspecialchars($car['price']); ?>/day</p>
                <div class="car-features">
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($car['type']); ?></p>
                    <p><strong>Fuel Consumption:</strong> <?php echo htmlspecialchars($car['fuel_consumption']); ?></p>
                    <p><strong>Engine Size:</strong> <?php echo htmlspecialchars($car['engine_size']); ?></p>
                    <p><strong>Mileage:</strong> <?php echo htmlspecialchars($car['mileage']); ?> km</p>
                </div>
            </div>
        </div>

        <form id="bookingForm" class="booking-form">
            <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
            <div class="form-group">
                <label for="pickup_date">Pickup Date</label>
                <input type="date" id="pickup_date" name="pickup_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="return_date">Return Date</label>
                <input type="date" id="return_date" name="return_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="pickup_location">Pickup Location</label>
                <input type="text" id="pickup_location" name="pickup_location" required placeholder="Enter pickup location">
            </div>
            <div class="total-price">
                Total: $<span id="totalPrice">0</span>
            </div>
            <button type="submit" class="book-btn">Confirm Booking</button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('bookingForm');
            const pickupDate = document.getElementById('pickup_date');
            const returnDate = document.getElementById('return_date');
            const totalPrice = document.getElementById('totalPrice');
            const pricePerDay = <?php echo $car['price']; ?>;

            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            pickupDate.min = today;
            returnDate.min = today;

            // Calculate total price
            function calculateTotal() {
                if (pickupDate.value && returnDate.value) {
                    const start = new Date(pickupDate.value);
                    const end = new Date(returnDate.value);
                    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                    if (days > 0) {
                        totalPrice.textContent = (days * pricePerDay).toFixed(2);
                    }
                }
            }

            pickupDate.addEventListener('change', calculateTotal);
            returnDate.addEventListener('change', calculateTotal);

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);

                try {
                    const response = await fetch('php/process_booking.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('Booking successful!');
                        window.location.href = 'user/dashboard.php';
                    } else {
                        alert(data.message || 'Booking failed. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });
    </script>
</body>
</html>
