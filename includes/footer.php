<style>
    footer {
            background: #2d3436;
            color: white;
            padding: 4rem 2rem 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-section h3 {
            color: #0984e3;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .footer-section a {
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 0.8rem;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .footer-section a:hover {
            opacity: 1;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
</style>
<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>About CarRent</h3>
            <p>Your trusted partner in car rental services.</p>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <a href="/mycarent/cars.php">Available Cars</a>
            <a href="/mycarent/about.php">About Us</a>
            <a href="/mycarent/contact.php">Contact</a>
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
