document.addEventListener('DOMContentLoaded', () => {
    // Modal functionality
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const closeBtns = document.querySelectorAll('.close');

    // Show modals
    if (loginBtn) {
        loginBtn.addEventListener('click', () => {
            loginModal.style.display = 'block';
        });
    }

    if (registerBtn) {
        registerBtn.addEventListener('click', () => {
            registerModal.style.display = 'block';
        });
    }

    // Close modals
    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (loginModal) loginModal.style.display = 'none';
            if (registerModal) registerModal.style.display = 'none';
        });
    });

    window.addEventListener('click', (e) => {
        if (loginModal && e.target === loginModal) loginModal.style.display = 'none';
        if (registerModal && e.target === registerModal) registerModal.style.display = 'none';
    });

    // Form submissions
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            
            try {
                const response = await fetch('php/login.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    if (data.role === 'admin') {
                        window.location.href = 'admin/dashboard.php';
                    } else {
                        window.location.href = 'user/dashboard.php';
                    }
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            
            // Validate password match
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }
            
            // Remove confirm password from form data
            formData.delete('confirm_password');
            
            try {
                const response = await fetch('php/register.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Registration successful! Please login.');
                    registerModal.style.display = 'none';
                    loginModal.style.display = 'block';
                    registerForm.reset();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    }

    // Check authentication status
    const checkAuth = async () => {
        try {
            const response = await fetch('php/check_auth.php');
            const data = await response.json();
            
            const authButtons = document.querySelector('.auth-buttons');
            const userDropdown = document.querySelector('.user-dropdown');
            
            if (data.authenticated) {
                if (authButtons) authButtons.style.display = 'none';
                if (userDropdown) {
                    userDropdown.style.display = 'block';
                    const userNameElement = userDropdown.querySelector('.user-name');
                    if (userNameElement) {
                        userNameElement.textContent = data.user_name;
                    }
                }
            } else {
                if (authButtons) authButtons.style.display = 'flex';
                if (userDropdown) userDropdown.style.display = 'none';
            }
        } catch (error) {
            console.error('Error checking auth:', error);
        }
    };

    checkAuth();

    // Load featured cars
    const loadFeaturedCars = async () => {
        try {
            // Get the base URL by checking if we're in a subdirectory
            const pathParts = window.location.pathname.split('/');
            const isInSubdir = pathParts[pathParts.length - 2] === 'user' || pathParts[pathParts.length - 2] === 'admin';
            const baseUrl = isInSubdir ? '../' : '';
            
            const response = await fetch(baseUrl + 'php/get_featured_cars.php');
            const cars = await response.json();
            const carGrid = document.getElementById('carGrid');
            
            if (!carGrid) return; // Exit if carGrid doesn't exist
            
            carGrid.innerHTML = ''; // Clear existing cars
            
            cars.forEach(car => {
                const carCard = document.createElement('div');
                carCard.className = 'car-card';
                carCard.innerHTML = `
                    <img src="${baseUrl + car.image}" alt="${car.name}" class="car-image">
                    <div class="car-details">
                        <h3>${car.name}</h3>
                        <p>${car.description}</p>
                        <div class="car-price">$${car.price}/day</div>
                        <button onclick="bookCar(${car.id})" class="book-btn">Book Now</button>
                    </div>
                `;
                carGrid.appendChild(carCard);
            });
        } catch (error) {
            console.error('Error loading cars:', error);
        }
    };

    loadFeaturedCars();

    // Search functionality
    const searchBtn = document.getElementById('searchBtn');
    if (searchBtn) {
        searchBtn.addEventListener('click', async () => {
            const location = document.getElementById('location').value;
            const pickupDate = document.getElementById('pickupDate').value;
            const returnDate = document.getElementById('returnDate').value;

            if (!location || !pickupDate || !returnDate) {
                alert('Please fill in all search fields');
                return;
            }

            try {
                const response = await fetch('php/search_cars.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        location,
                        pickupDate,
                        returnDate
                    })
                });
                const cars = await response.json();
                updateCarGrid(cars);
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while searching. Please try again.');
            }
        });
    }

    // Function to update car grid with search results
    function updateCarGrid(cars) {
        const carGrid = document.getElementById('carGrid');
        if (!carGrid) return;
        
        carGrid.innerHTML = '';
        
        if (cars.length === 0) {
            carGrid.innerHTML = '<p>No cars found matching your criteria.</p>';
            return;
        }

        cars.forEach(car => {
            const carCard = document.createElement('div');
            carCard.className = 'car-card';
            carCard.innerHTML = `
                <img src="${car.image}" alt="${car.name}" class="car-image">
                <div class="car-details">
                    <h3>${car.name}</h3>
                    <p>${car.description}</p>
                    <div class="car-price">$${car.price}/day</div>
                    <button onclick="bookCar(${car.id})" class="book-btn">Book Now</button>
                </div>
            `;
            carGrid.appendChild(carCard);
        });
    }

    // Function to handle car booking
    window.bookCar = async (carId) => {
        try {
            const response = await fetch('php/check_auth.php');
            const data = await response.json();
            
            if (!data.authenticated) {
                if (loginModal) {
                    loginModal.style.display = 'block';
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
    };

    // Handle booking form submission
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(bookingForm);
            
            try {
                const response = await fetch('php/create_booking.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Booking successful!');
                    window.location.href = 'user/bookings.php';
                } else {
                    alert(data.message || 'An error occurred');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    }
});
