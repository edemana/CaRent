<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - CarRent</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .about-hero {
            height: 60vh;
            background: linear-gradient(rgba(9, 132, 227, 0.8), rgba(45, 52, 54, 0.9)), url('images/about-hero.jpg');
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

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2);
            pointer-events: none;
        }

        .about-hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .about-hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            font-weight: 700;
            letter-spacing: 1px;
            animation: fadeInUp 1.2s ease;
        }

        .about-hero p {
            font-size: 1.4rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1.4s ease;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .about-card:hover {
            transform: translateY(-10px);
        }

        .about-card i {
            font-size: 2.5rem;
            color: #0984e3;
            margin-bottom: 1.5rem;
        }

        .about-card h3 {
            color: #2d3436;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .about-card p {
            color: #636e72;
            line-height: 1.6;
        }

        .our-story {
            background: #f8f9fa;
            padding: 6rem 2rem;
            margin: 4rem 0;
        }

        .story-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .story-content h2 {
            color: #2d3436;
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .story-content p {
            color: #636e72;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .team-section {
            padding: 4rem 2rem;
            background: white;
        }

        .team-section h2 {
            text-align: center;
            color: #2d3436;
            font-size: 2.5rem;
            margin-bottom: 3rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .team-member {
            text-align: center;
        }

        .team-member img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1.5rem;
            border: 5px solid #f8f9fa;
            transition: transform 0.3s ease;
        }

        .team-member:hover img {
            transform: scale(1.05);
        }

        .team-member h3 {
            color: #2d3436;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .team-member p {
            color: #0984e3;
            font-weight: 500;
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
            .about-hero {
                height: 50vh;
                padding: 6rem 1.5rem;
            }

            .about-hero h1 {
                font-size: 2.5rem;
            }

            .about-hero p {
                font-size: 1.1rem;
            }

            .about-grid {
                padding: 1rem;
            }

            .team-grid {
                gap: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="about-page">
        <section class="about-hero">
            <div class="about-hero-content">
                <h1>About CarRent</h1>
                <p>Your trusted partner in car rental services since 2024</p>
            </div>
        </section>

        <section class="about-info">
            <div class="about-grid">
                <div class="about-card">
                    <i class="fas fa-car"></i>
                    <h3>Premium Fleet</h3>
                    <p>Choose from our wide selection of well-maintained vehicles, from economy to luxury.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Safe & Reliable</h3>
                    <p>All our vehicles undergo regular maintenance and safety checks.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-dollar-sign"></i>
                    <h3>Best Rates</h3>
                    <p>Competitive pricing with no hidden fees. Get the best value for your money.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Our customer service team is always ready to assist you.</p>
                </div>
            </div>
        </section>

        <section class="our-story">
            <div class="story-content">
                <h2>Our Story</h2>
                <p>Founded in 2024, CarRent has grown from a small local car rental service to a trusted name in the industry. Our mission is to provide convenient, reliable, and affordable car rental solutions to our customers.</p>
                <p>We take pride in our commitment to customer satisfaction and our dedication to maintaining a modern fleet of vehicles that cater to all needs and preferences.</p>
            </div>
        </section>

        <section class="team-section">
            <h2>Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="images/team/ceo.jpg" alt="CEO">
                    <h3>Edem K. Anagbah</h3>
                    <p>CEO & Founder</p>
                </div>
                <div class="team-member">
                    <img src="images/team/operations.jpg" alt="Operations Manager">
                    <h3>Joseph Quartey</h3>
                    <p>Operations Manager</p>
                </div>
                <div class="team-member">
                    <img src="images/team/customer-service.jpg" alt="Customer Service Head">
                    <h3>Mike Johnson</h3>
                    <p>Customer Service Head</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
