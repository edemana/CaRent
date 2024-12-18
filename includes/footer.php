<style>
    footer {
        background: linear-gradient(135deg, #2d3436 0%, #1e272e 100%);
        color: #f5f6fa;
        padding: 5rem 2rem 2rem;
        position: relative;
        overflow: hidden;
    }

    footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0984e3, #00b894, #0984e3);
        background-size: 200% 100%;
        animation: gradient 15s ease infinite;
    }

    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 4rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-section {
        position: relative;
    }

    .footer-section h3 {
        color: #0984e3;
        margin-bottom: 1.8rem;
        font-size: 1.4rem;
        font-weight: 600;
        position: relative;
        padding-bottom: 0.8rem;
    }

    .footer-section h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: #00b894;
        border-radius: 2px;
    }

    .footer-section p {
        line-height: 1.6;
        margin-bottom: 1rem;
        color: rgba(245, 246, 250, 0.8);
    }

    .footer-section a {
        color: #f5f6fa;
        text-decoration: none;
        display: block;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
        padding-left: 20px;
    }

    .footer-section a::before {
        content: '→';
        position: absolute;
        left: 0;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .footer-section a:hover {
        color: #0984e3;
        padding-left: 25px;
    }

    .footer-section a:hover::before {
        opacity: 1;
    }

    .contact-info {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .contact-info i {
        margin-right: 10px;
        color: #0984e3;
    }

    .footer-bottom {
        text-align: center;
        margin-top: 4rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-bottom p {
        color: rgba(245, 246, 250, 0.6);
        font-size: 0.9rem;
    }

    .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: #f5f6fa;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background: #0984e3;
        transform: translateY(-3px);
    }

    @media (max-width: 768px) {
        footer {
            padding: 4rem 1.5rem 1.5rem;
        }
        
        .footer-content {
            gap: 2rem;
        }
    }
</style>

<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>About CarRent</h3>
            <p>Your premier destination for quality car rentals. We provide reliable, comfortable, and affordable vehicles for all your transportation needs.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <a href="/mycarent/cars.php">Browse Cars</a>
            <a href="/mycarent/about.php">About Us</a>
            <a href="/mycarent/contact.php">Contact Us</a>
            <a href="/mycarent/terms.php">Terms & Conditions</a>
            <a href="/mycarent/privacy.php">Privacy Policy</a>
        </div>
        <div class="footer-section">
            <h3>Contact Us</h3>
            <div class="contact-info">
                <i class="fas fa-envelope"></i>
                <p>info@carrent.com</p>
            </div>
            <div class="contact-info">
                <i class="fas fa-phone"></i>
                <p>054-155-1234</p>
            </div>
            <div class="contact-info">
                <i class="fas fa-map-marker-alt"></i>
                <p>123 Main Street, Accra, Ghana</p>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> CarRent. All rights reserved. | Designed with <i class="fas fa-heart" style="color: #e74c3c;"></i> by CarRent Team</p>
    </div>
</footer>
