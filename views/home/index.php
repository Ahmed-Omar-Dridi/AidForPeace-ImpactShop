<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidForPeace - Home</title>
    <link rel="stylesheet" href="../../assets/css/STYLE.CSS">
    <style>
        .navbar-container {
            background-color: #1e3149;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .nav-menu a:hover {
            color: #ffb600;
            background-color: rgba(255, 182, 0, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, #1e3149 0%, #15202e 50%, #0a0a0a 100%);
            color: white;
            padding: 6rem 2rem;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 3rem;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .hero-section p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-btn {
            display: inline-block;
            padding: 1rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .cta-btn-primary {
            background-color: #ffb600;
            color: #1e3149;
        }

        .cta-btn-primary:hover {
            background-color: #e6a500;
            transform: translateY(-2px);
        }

        .cta-btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid #ffb600;
        }

        .cta-btn-secondary:hover {
            background-color: #ffb600;
            color: #1e3149;
            transform: translateY(-2px);
        }

        .features-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-card h3 {
            color: #1e3149;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar-container">
        <nav class="navbar">
            <a href="index.php" class="logo">AidForPeace</a>
            <ul class="nav-menu">
                <li><a href="index.php?controller=home&action=index">Accueil</a></li>
                <li><a href="index.php?controller=user&action=index">User</a></li>
                <li><a href="index.php?controller=product&action=shop">ImpactShop</a></li>
                <li><a href="index.php?controller=messagerie&action=index">Messagerie</a></li>
                <li><a href="index.php?controller=map&action=index">Map</a></li>
                <li><a href="index.php?controller=ngo&action=index">NGO</a></li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <h1>Welcome to AidForPeace</h1>
            <p>Making a difference, one action at a time</p>
            <div class="cta-buttons">
                <a href="index.php?controller=product&action=shop" class="cta-btn cta-btn-primary">Shop Now</a>
                <a href="index.php?controller=contact&action=index" class="cta-btn cta-btn-secondary">Get in Touch</a>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="feature-card">
                <div class="icon">🛍️</div>
                <h3>ImpactShop</h3>
                <p>Discover our curated collection of products that make a positive impact on communities worldwide.</p>
            </div>
            <div class="feature-card">
                <div class="icon">💬</div>
                <h3>Messagerie</h3>
                <p>Connect with our team and community members. Share your ideas and stay updated on our initiatives.</p>
            </div>
            <div class="feature-card">
                <div class="icon">🗺️</div>
                <h3>Map</h3>
                <p>Explore our projects and initiatives across different regions. See where we're making a difference.</p>
            </div>
            <div class="feature-card">
                <div class="icon">🤝</div>
                <h3>NGO Partners</h3>
                <p>Learn about our partner organizations and the incredible work they do for peace and development.</p>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 AidForPeace. All rights reserved.</p>
        <p>Making peace and development accessible to all.</p>
    </footer>
</body>
</html>
