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
<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    overflow-y: auto;
    padding: 2rem 1rem;
}

.modal-content {
    background: white;
    max-width: 400px;
    padding: 2rem;
    border-radius: 15px;
    margin: 2rem auto;
    position: relative;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-height: 800px) {
    .modal {
        align-items: flex-start;
    }
    
    .modal-content {
        margin: 1rem auto;
    }
}

@media (max-width: 768px) {
    .modal {
        padding: 1rem;
    }
    
    .modal-content {
        margin: 1rem auto;
        padding: 1.5rem;
    }
}

.modal-header {
    text-align: center;
    margin-bottom: 2rem;
}

.modal-header h2 {
    color: #2d3436;
    margin-bottom: 0.5rem;
    font-size: 1.8rem;
}

.modal-header p {
    color: #636e72;
    font-size: 0.95rem;
}

.form-group {
    position: relative;
    margin-bottom: 1.5rem;
}

.form-group i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #0984e3;
}

.form-group input {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 2.8rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-group input:focus {
    border-color: #0984e3;
    box-shadow: 0 0 0 2px rgba(9, 132, 227, 0.1);
    outline: none;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.forgot-password {
    color: #0984e3;
    text-decoration: none;
}

.btn-primary {
    width: 100%;
    padding: 0.8rem;
    background: #0984e3;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #0770c2;
    transform: translateY(-1px);
}

.social-login {
    text-align: center;
    margin: 1.5rem 0;
}

.social-login p {
    color: #636e72;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    position: relative;
}

.social-login p::before,
.social-login p::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 30%;
    height: 1px;
    background: #e9ecef;
}

.social-login p::before {
    left: 0;
}

.social-login p::after {
    right: 0;
}

.social-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.btn-social {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.8rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-social:hover {
    background: #f5f6fa;
    transform: translateY(-1px);
}

.btn-google i {
    color: #ea4335;
}

.btn-facebook i {
    color: #1877f2;
}

.modal-footer {
    text-align: center;
    margin-top: 1.5rem;
    color: #636e72;
    font-size: 0.9rem;
}

.modal-footer a {
    color: #0984e3;
    text-decoration: none;
    font-weight: 500;
}

.password-hint {
    display: block;
    margin-top: 0.5rem;
    color: #636e72;
    font-size: 0.8rem;
}

.terms-privacy {
    margin: 1.5rem 0;
    font-size: 0.9rem;
    color: #636e72;
}

.terms-privacy a {
    color: #0984e3;
    text-decoration: none;
}

@media (max-width: 768px) {
    .modal-content {
        padding: 1.5rem;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const closeBtns = document.getElementsByClassName('close');
    const showRegister = document.getElementById('showRegister');
    const showLogin = document.getElementById('showLogin');

    // Open modals
    loginBtn?.addEventListener('click', () => loginModal.style.display = 'block');
    registerBtn?.addEventListener('click', () => registerModal.style.display = 'block');

    // Close modals
    Array.from(closeBtns).forEach(btn => {
        btn.addEventListener('click', () => {
            loginModal.style.display = 'none';
            registerModal.style.display = 'none';
        });
    });

    // Switch between modals
    showRegister?.addEventListener('click', (e) => {
        e.preventDefault();
        loginModal.style.display = 'none';
        registerModal.style.display = 'block';
    });

    showLogin?.addEventListener('click', (e) => {
        e.preventDefault();
        registerModal.style.display = 'none';
        loginModal.style.display = 'block';
    });

    // Close on outside click
    window.addEventListener('click', (e) => {
        if (e.target === loginModal) loginModal.style.display = 'none';
        if (e.target === registerModal) registerModal.style.display = 'none';
    });

    // Handle login form submission
    document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch('<?php echo $rootPath; ?>php/login.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                loginModal.style.display = 'none';
                if (data.role === 'admin') {
                    window.location.href = '<?php echo $rootPath; ?>admin/dashboard.php';
                } else {
                    window.location.href = '<?php echo $rootPath; ?>user/dashboard.php';
                }
            } else {
                alert(data.message || 'Login failed. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
    });

    // Handle registration form submission
    document.getElementById('registerForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch('<?php echo $rootPath; ?>php/register.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                alert('Registration successful! Please login.');
                registerModal.style.display = 'none';
                loginModal.style.display = 'block';
            } else {
                alert(data.message || 'Registration failed. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
    });
});
</script>
