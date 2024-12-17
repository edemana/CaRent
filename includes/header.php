<style>
    ::-webkit-scrollbar {
        display: none;
    }
    .navbar {
        font: normal 500 1rem 'Poppins', sans-serif;
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem 2rem;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }

    .navbar.scrolled {
        padding: 0.8rem 2rem;
        background: white;
    }

    .logo {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0984e3;
        text-decoration: none;
    }

    .nav-links {
        display: flex;
        gap: 2rem;
    }

    .nav-links a {
        color: #2d3436;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .nav-links a:hover {
        color: #0984e3;
    }

    .nav-links a.active {
        color: #0984e3;
    }

    .auth-buttons {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .auth-buttons button, .auth-buttons .btn-secondary, .auth-buttons .nav-link {
        padding: 0.5rem 1.5rem;
        border-radius: 25px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    #loginBtn, .btn-secondary {
        background: transparent;
        border: 2px solid #0984e3;
        color: #0984e3;
    }

    #loginBtn:hover, .btn-secondary:hover {
        background: #0984e3;
        color: white;
    }

    #registerBtn {
        background: #0984e3;
        border: none;
        color: white;
    }

    #registerBtn:hover {
        background: #0770c2;
        transform: translateY(-2px);
    }

    .nav-link {
        color: #2d3436;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .nav-link:hover {
        color: #0984e3;
    }

    @media (max-width: 768px) {
        .navbar {
            padding: 1rem;
        }

        .nav-links {
            display: none;
        }
    }
</style>
<nav class="navbar">
    <div class="logo">CarRent</div>
    <div class="nav-links">
        <a href="../index.php" class="active">Home</a>
        <a href="../cars.php">Cars</a>
        <a href="../about.php">About</a>
        <a href="../contact.php">Contact</a>
     </div>
    <div class="auth-buttons">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="../admin/dashboard.php" class="nav-link">Admin Dashboard</a>
            <?php else: ?>
                <a href="./dashboard.php" class="nav-link">My Dashboard</a>
            <?php endif; ?>
            <a href="../php/logout.php" class="btn-secondary">Logout</a>
        <?php else: ?>
            <button id="loginBtn">Login</button>
            <button id="registerBtn">Register</button>
        <?php endif; ?>
    </div>
</nav>

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
</script>
