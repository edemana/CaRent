<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarRent - Modern Car Rental Service</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
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
            <?php if (!isset($_SESSION['user_id'])) { ?>
                <button id="loginBtn">Login</button>
                <button id="registerBtn">Register</button>
            <?php } else { ?> 
                <div class="user-dropdown">
                    <button class="dropdown-toggle">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'customer') { ?>
                            <a href="user/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                My Dashboard
                            </a>
                            <a href="user/profile.php">
                                <i class="fas fa-user-edit"></i>
                                Edit Profile
                            </a>
                            <a href="user/booking.php">
                                <i class="fas fa-calendar-alt"></i>
                                My Bookings
                            </a>
                            <div class="divider"></div>
                            <a href="php/logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        <?php } else { ?>
                            <a href="admin/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                            <div class="divider"></div>
                            <a href="php/logout.php">
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
            <div class="car-grid" id="carGrid">
                <!-- Cars will be loaded dynamically -->
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
                    <p>Don't have an account? <a href="#" class="switch-modal" data-target="registerModal">Sign Up</a></p>
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

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About CarRent</h3>
                <p>Your trusted partner in car rental services.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="cars.php">Available Cars</a>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: info@carrent.com</p>
                <p>Phone: 054-155-1234</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 CarRent. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script src="js/dropdown.js"></script>
    <script>
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
