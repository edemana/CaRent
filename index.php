<!-- <?php
session_start();
?> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarRent - Modern Car Rental Service</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="css/favicon.ico" type="image/x-icon">
</head>
<style>
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

    /* Featured Cars Section */
    #featured-cars {
        padding: 4rem 2rem;
        background: #f8f9fa;
    }

    #featured-cars h2 {
        text-align: center;
        margin-bottom: 3rem;
        color: #2d3436;
        font-size: 2.5rem;
        position: relative;
    }

    #featured-cars h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: #0984e3;
        border-radius: 2px;
    }

    .cars-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .car-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .car-card:hover {
        transform: translateY(-5px);
    }

    .car-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .car-details {
        padding: 1.5rem;
    }

    .car-details h3 {
        margin: 0 0 0.5rem 0;
        color: #2d3436;
        font-size: 1.4rem;
    }

    .car-type {
        color: #0984e3;
        font-size: 0.9rem;
        margin-bottom: 1rem;
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
        font-size: 0.9rem;
        color: #636e72;
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
        padding: 0.8rem;
        background: #0984e3;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
        transition: background 0.3s ease;
        text-decoration: none;
        display: block;
        text-align: center;
    }

    .book-btn:hover {
        background: #0773c5;
    }

    .loading-spinner {
        text-align: center;
        padding: 4rem;
    }

    .loading-spinner i {
        font-size: 3rem;
        color: #0984e3;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .no-cars-message {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .no-cars-message i {
        margin-bottom: 1rem;
    }

    .no-cars-message p {
        color: #636e72;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        #featured-cars {
            padding: 3rem 1rem;
        }

        .cars-grid {
            grid-template-columns: 1fr;
        }

        .car-features {
            justify-content: space-between;
        }
    }
</style>
<body>
    <nav class="navbar">
        <div class="logo">CarRent</div>
        <div class="nav-links">
            <a href="index.php" class="active">Home</a>
            <a href="cars.php">Cars</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </div>
        <div class="auth-buttons">
            <?php 
            // Get the current directory depth relative to root
            $currentPath = $_SERVER['PHP_SELF'];
            $rootPath = "";
            if (strpos($currentPath, '/admin/') !== false || strpos($currentPath, '/user/') !== false) {
                $rootPath = "../";
            }
            
            if (!isset($_SESSION['user_type'])) { ?>
                <button id="loginBtn">Login</button>
                <button id="registerBtn">Register</button>
            <?php } else { ?>
                <div class="user-dropdown">
                    <button class="dropdown-toggle">
                        <i class="fas fa-user-circle"></i>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <?php if ($_SESSION['user_type'] === 'customer') { ?>
                            <a href="<?php echo $rootPath; ?>user/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                My Dashboard
                            </a>
                            <a href="<?php echo $rootPath; ?>user/profile.php">
                                <i class="fas fa-user-edit"></i>
                                Edit Profile
                            </a>
                            <a href="<?php echo $rootPath; ?>user/bookings.php" >
                            <i class="fas fa-calendar-alt"></i>
                                My Bookings
                            </a>
                            <div class="divider"></div>
                            <a href="<?php echo $rootPath; ?>php/logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        <?php } else { ?>
                            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                            <a href="<?php echo $rootPath; ?>admin/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                            <?php endif; ?>
                            <div class="divider"></div>
                            <a href="<?php echo $rootPath; ?>php/logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </nav>

    <main>
        <section id="hero">
            <div class="hero-content">
                <h1>Find Your Perfect Ride</h1>
                <p>Choose from our wide selection of premium vehicles</p>
                <div class="search-container">
                    <input type="text" id="location" placeholder="Pick-up Location">
                    <input type="date" id="pickupDate">
                    <input type="date" id="returnDate">
                    <button id="searchBtn">Search Cars</button>
                </div>
            </div>
        </section>

        <section id="featured-cars">
            <h2>Featured Cars</h2>
            <div class="loading-spinner" id="loadingSpinner">
            <i class="fas fa-spinner"></i>
        </div>

        <div class="cars-grid" id="carsGrid">
            <!-- Cars will be loaded dynamically -->
        </div>

        <div class="pagination" id="pagination">
            <!-- Pagination will be added dynamically -->
        </div>
        </section>
    </main>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2>Welcome Back!</h2>
                <p>Sign in to access your account and manage your rentals</p>
            </div>
            <form id="loginForm">
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Your Email Address" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Your Password" required>
                </div>
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>
                <button type="submit" class="btn-primary">Sign In</button>
                <div class="social-login">
                    <p>Or continue with</p>
                    <div class="social-buttons">
                        <button type="button" class="btn-social btn-google">
                            <i class="fab fa-google"></i>
                            <span>Google</span>
                        </button>
                        <button type="button" class="btn-social btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <p>Don't have an account? <a href=#registerModal class="switch-modal" data-target="registerModal">Sign Up</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2>Create Account</h2>
                <p>Join CarRent to start your premium car rental experience</p>
            </div>
            <form id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="fname" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="lname" placeholder="Last Name" required>
                    </div>
                </div>
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" placeholder="Phone Number (optional)">
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Create Password" required>
                    <small class="password-hint">Must be at least 8 characters with numbers and letters</small>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <div class="terms-privacy">
                    <label>
                        <input type="checkbox" required>
                        <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                    </label>
                </div>
                <button type="submit" class="btn-primary">Create Account</button>
                <div class="social-login">
                    <p>Or sign up with</p>
                    <div class="social-buttons">
                        <button type="button" class="btn-social btn-google">
                            <i class="fab fa-google"></i>
                            <span>Google</span>
                        </button>
                        <button type="button" class="btn-social btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <p>Already have an account? <a href="#" class="switch-modal" data-target="loginModal">Sign In</a></p>
                </div>
            </form>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>

    <!-- <script src="js/script.js"></script> -->
    <script src="js/dropdown.js"></script>
    <script>

        document.addEventListener('DOMContentLoaded', () => {
            const carsGrid = document.getElementById('carsGrid');
            const loadingSpinner = document.getElementById('loadingSpinner');

            async function loadCars() {
                loadingSpinner.style.display = 'block';
                carsGrid.style.display = 'none';

                try {
                    const response = await fetch('php/get_featured_cars.php');
                    const data = await response.json();
                    
                    carsGrid.innerHTML = '';

                    if (data.length === 0) {
                        carsGrid.innerHTML = `
                            <div class="no-cars-message">
                                <i class="fas fa-car" style="font-size: 3rem; color: #0984e3;"></i>
                                <p>No featured cars available at the moment.</p>
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
                                    <a href="booking.php?car_id=${car.id}" class="book-btn">Book Now</a>
                                </div>
                            `;
                            carsGrid.appendChild(carCard);
                        });
                    }
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

            // Initial load
            loadCars();
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Modal functionality
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        const closeBtns = document.getElementsByClassName('close');

        loginBtn.onclick = function() {
            loginModal.style.display = "block";
        }

        registerBtn.onclick = function() {
            registerModal.style.display = "block";
        }

        Array.from(closeBtns).forEach(btn => {
            btn.onclick = function() {
                loginModal.style.display = "none";
                registerModal.style.display = "none";
            }
        });

        window.onclick = function(event) {
            if (event.target == loginModal) {
                loginModal.style.display = "none";
            }
            if (event.target == registerModal) {
                registerModal.style.display = "none";
            }
        }

        // Set minimum date for date inputs
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('pickupDate').min = today;
        document.getElementById('returnDate').min = today;

        // Update return date min when pickup date changes
        document.getElementById('pickupDate').addEventListener('change', function() {
            document.getElementById('returnDate').min = this.value;
        });

        // Form submission handlers
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('php/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide the modal
                    loginModal.style.display = "none";
                    
                    // Redirect based on user role
                    if (data.role === 'admin') {
                        window.location.href = 'admin/dashboard.php';
                    } else if (data.role === 'customer') {
                        window.location.href = 'user/dashboard.php';
                    } else {
                        // Fallback to home page if role is undefined
                        window.location.reload();
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during login. Please try again.');
            });
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('php/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration successful! Please login.');
                    // Hide register modal and show login modal
                    registerModal.style.display = "none";
                    loginModal.style.display = "block";
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during registration. Please try again.');
            });
        });

        const userTrigger = document.querySelector('.user-trigger');
        if (userTrigger) {
            userTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelector('.user-dropdown').classList.toggle('active');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.user-dropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Prevent dropdown from closing when clicking inside
        const dropdownMenu = document.querySelector('.dropdown-menu');
        if (dropdownMenu) {
            dropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    </script>
</body>
</html>
