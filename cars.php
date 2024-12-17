<?php
session_start();
require_once 'php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Cars - CarRent</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .cars-page {
            background-color: #f8f9fa;
        }

        .cars-hero {
            height: 60vh;
            background: linear-gradient(rgba(9, 132, 227, 0.8), rgba(45, 52, 54, 0.9)), url('images/cars-hero.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 8rem 2rem;
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cars-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2);
            pointer-events: none;
        }

        .cars-hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .cars-hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            font-weight: 700;
            letter-spacing: 1px;
            animation: fadeInUp 1.2s ease;
        }

        .cars-hero p {
            font-size: 1.4rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1.4s ease;
        }

        .filter-section {
            max-width: 1200px;
            margin: -6rem auto 4rem;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            color: #2d3436;
            font-weight: 500;
            font-size: 1rem;
        }

        .filter-group select {
            padding: 0.8rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #0984e3;
        }

        .filter-form button {
            background: #0984e3;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            align-self: flex-end;
        }

        .filter-form button:hover {
            background: #0770c2;
            transform: translateY(-2px);
        }

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .car-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            animation: fadeIn 0.5s ease-out;
        }

        .car-card:hover {
            transform: translateY(-10px);
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
            padding: 1.5rem;
        }

        .car-details h3 {
            font-size: 1.5rem;
            color: #2d3436;
            margin-bottom: 0.5rem;
        }

        .car-type {
            color: #0984e3;
            font-weight: 500;
            margin-bottom: 1rem;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .car-features {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
            flex-wrap: wrap;
        }

        .car-features span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #636e72;
            font-size: 0.9rem;
        }

        .car-features i {
            color: #0984e3;
        }

        .car-price {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0984e3;
            margin: 1rem 0;
        }

        .book-btn {
            width: 100%;
            background: #0984e3;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .book-btn:hover {
            background: #0770c2;
            transform: translateY(-2px);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 3rem 0;
            padding-bottom: 3rem;
        }

        .pagination button {
            padding: 0.8rem 1.2rem;
            border: none;
            background: white;
            color: #0984e3;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .pagination button.active {
            background: #0984e3;
            color: white;
        }

        .pagination button:hover:not(.active) {
            background: #f1f2f6;
            transform: translateY(-2px);
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 4rem;
        }

        .loading-spinner i {
            font-size: 2.5rem;
            color: #0984e3;
            animation: spin 1s linear infinite;
        }

        .no-cars-message {
            text-align: center;
            padding: 4rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            max-width: 600px;
        }

        .no-cars-message i {
            display: block;
            margin-bottom: 1rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .cars-hero {
                height: 50vh;
                padding: 6rem 1.5rem;
            }

            .cars-hero h1 {
                font-size: 2.5rem;
            }

            .cars-hero p {
                font-size: 1.1rem;
            }

            .filter-section {
                margin: -4rem 1rem 2rem;
                padding: 1.5rem;
            }

            .cars-grid {
                padding: 1rem;
            }

            .car-features {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="cars-page">
        <section class="cars-hero">
            <div class="cars-hero-content">
                <h1>Our Fleet</h1>
                <p>Choose from our wide selection of premium vehicles</p>
            </div>
        </section>

        <section class="filter-section">
            <form id="filterForm" class="filter-form">
                <div class="filter-group">
                    <label for="carType">Car Type</label>
                    <select id="carType" name="carType">
                        <option value="">All Types</option>
                        <option value="sedan">Sedan</option>
                        <option value="suv">SUV</option>
                        <option value="luxury">Luxury</option>
                        <option value="sports">Sports</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="priceRange">Price Range</label>
                    <select id="priceRange" name="priceRange">
                        <option value="">All Prices</option>
                        <option value="0-50">$0 - $50/day</option>
                        <option value="51-100">$51 - $100/day</option>
                        <option value="101-200">$101 - $200/day</option>
                        <option value="201+">$201+/day</option>
                    </select>
                </div>
                <button type="submit">Find Cars</button>
            </form>
        </section>

        <div class="loading-spinner" id="loadingSpinner">
            <i class="fas fa-spinner"></i>
        </div>

        <div class="cars-grid" id="carsGrid">
            <!-- Cars will be loaded dynamically -->
        </div>

        <div class="pagination" id="pagination">
            <!-- Pagination will be added dynamically -->
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterForm = document.getElementById('filterForm');
            const carsGrid = document.getElementById('carsGrid');
            const pagination = document.getElementById('pagination');
            const loadingSpinner = document.getElementById('loadingSpinner');

            async function loadCars() {
                loadingSpinner.style.display = 'block';
                carsGrid.style.display = 'none';

                const formData = new FormData(filterForm);

                try {
                    const response = await fetch('php/get_featured_cars.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    carsGrid.innerHTML = '';

                    if (data.length === 0) {
                        carsGrid.innerHTML = `
                            <div class="no-cars-message">
                                <i class="fas fa-car" style="font-size: 3rem; color: #0984e3;"></i>
                                <p>No cars found matching your criteria. Please try different filters.</p>
                            </div>
                        `;
                    } else {
                        data.forEach(car => {
                            const carCard = document.createElement('div');
                            carCard.className = 'car-card';
                            carCard.innerHTML = `
                                <img src="${car.image}" alt="${car.name}" class="car-image">
                                <div class="car-details">
                                    <h3>${car.name}</h3>
                                    <p class="car-type">${car.type || 'Sedan'}</p>
                                    <p>${car.description}</p>
                                    <div class="car-features">
                                        <span><i class="fas fa-users"></i> 5 seats</span>
                                        <span><i class="fas fa-gas-pump"></i> Gasoline</span>
                                        <span><i class="fas fa-cog"></i> Automatic</span>
                                    </div>
                                    <div class="car-price">$${car.price}/day</div>
                                    <button onclick="bookCar(${car.id})" class="book-btn">Book Now</button>
                                </div>
                            `;
                            carsGrid.appendChild(carCard);
                        });
                    }

                    // Don't need pagination since we're showing all cars
                    pagination.innerHTML = '';
                } catch (error) {
                    console.error('Error:', error);
                    carsGrid.innerHTML = `
                        <div class="no-cars-message">
                            <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #e74c3c;"></i>
                            <p>An error occurred while loading cars. Please try again later.</p>
                        </div>
                    `;
                } finally {
                    loadingSpinner.style.display = 'none';
                    carsGrid.style.display = 'grid';
                }
            }

            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                loadCars();
            });

            // Initial load
            loadCars();
        });

        async function bookCar(carId) {
            try {
                const response = await fetch('php/check_auth.php');
                const data = await response.json();
                
                if (!data.authenticated) {
                    const modal = document.querySelector('#loginModal');
                    if (modal) {
                        modal.style.display = 'block';
                    } else {
                        alert('Please login to book a car');
                        window.location.href = 'index.php#loginModal';
                    }
                    return;
                }
                
                // User is logged in, proceed to booking page
                window.location.href = `booking.php?car_id=${carId}`;
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        }
    </script>
</body>
</html>
