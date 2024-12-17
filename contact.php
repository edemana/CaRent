<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - CarRent</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .contact-hero {
            height: 60vh;
            background: linear-gradient(rgba(9, 132, 227, 0.8), rgba(45, 52, 54, 0.9)), url('images/contact-hero.jpg');
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

        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2);
            pointer-events: none;
        }

        .contact-hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .contact-hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            font-weight: 700;
            letter-spacing: 1px;
            animation: fadeInUp 1.2s ease;
        }

        .contact-hero p {
            font-size: 1.4rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1.4s ease;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-10px);
        }

        .contact-card i {
            font-size: 2.5rem;
            color: #0984e3;
            margin-bottom: 1.5rem;
        }

        .contact-card h3 {
            color: #2d3436;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .contact-card p {
            color: #636e72;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .contact-form-section {
            padding: 4rem 2rem;
            background: #f8f9fa;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            color: #2d3436;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .contact-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            color: #2d3436;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            padding: 0.8rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0984e3;
        }

        .contact-form button {
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

        .contact-form button:hover {
            background: #0770c2;
            transform: translateY(-2px);
        }

        .map-section {
            padding: 4rem 2rem;
        }

        .map-section h2 {
            text-align: center;
            color: #2d3436;
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        .map-container {
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: none;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: none;
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

        @media (max-width: 768px) {
            .contact-hero {
                height: 50vh;
                padding: 6rem 1.5rem;
            }

            .contact-hero h1 {
                font-size: 2.5rem;
            }

            .contact-hero p {
                font-size: 1.1rem;
            }

            .contact-grid {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="contact-page">
        <section class="contact-hero">
            <div class="contact-hero-content">
                <h1>Contact Us</h1>
                <p>We're here to help and answer any question you might have</p>
            </div>
        </section>

        <section class="contact-info">
            <div class="contact-grid">
                <div class="contact-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Address</h3>
                    <p>123 Car Street</p>
                    <p>New York, NY 10001</p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-phone"></i>
                    <h3>Phone</h3>
                    <p>+1 054-155-1234</p>
                    <p>Mon-Fri: 9:00 AM - 6:00 PM</p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>info@carrent.com</p>
                    <p>support@carrent.com</p>
                </div>
            </div>
        </section>

        <section class="contact-form-section">
            <div class="form-container">
                <h2>Send us a Message</h2>
                <div class="success-message" id="successMessage">
                    Your message has been sent successfully!
                </div>
                <div class="error-message" id="errorMessage">
                    There was an error sending your message. Please try again.
                </div>
                <form id="contactForm" class="contact-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit">Send Message</button>
                </form>
            </div>
        </section>

        <section class="map-section">
            <h2>Find Us</h2>
            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.30591910525!2d-74.25986790365704!3d40.69714941680757!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1696932122840!5m2!1sen!2s"
                    width="100%"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contactForm = document.getElementById('contactForm');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');

            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(contactForm);

                try {
                    const response = await fetch('php/process_contact.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        successMessage.style.display = 'block';
                        errorMessage.style.display = 'none';
                        contactForm.reset();
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 5000);
                    } else {
                        throw new Error(data.message || 'Error sending message');
                    }
                } catch (error) {
                    errorMessage.style.display = 'block';
                    successMessage.style.display = 'none';
                    setTimeout(() => {
                        errorMessage.style.display = 'none';
                    }, 5000);
                }
            });
        });
    </script>
</body>
</html>
