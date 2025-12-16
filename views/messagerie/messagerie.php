<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie - AidForPeace</title>
    <link rel="stylesheet" href="../../assets/css/STYLE.CSS">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        .hero {
            background: linear-gradient(135deg, #1e3149 0%, #15202e 50%, #0a0a0a 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 3rem;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .features-grid {
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

        .feature-card i {
            font-size: 3rem;
            color: #ffb600;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            color: #1e3149;
            margin-bottom: 1rem;
        }

        .btn-custom {
            display: inline-block;
            background-color: #ffb600;
            color: #1e3149;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #e6a500;
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
    <main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
        <section class="hero">
            <h1>Messagerie AidForPeace</h1>
            <p>Connectez-vous avec notre communauté et nos partenaires</p>
            <a href="index.php?controller=messagerie&action=inbox" class="btn-custom">
                <i class="fas fa-comments"></i> Accéder à mes conversations
            </a>
        </section>

        <section class="features-grid">
            <div class="feature-card">
                <i class="fas fa-comments"></i>
                <h3>Conversations</h3>
                <p>Discutez en temps réel avec les membres de la communauté</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-robot"></i>
                <h3>ChatBot Assistant</h3>
                <p>Obtenez de l'aide instantanée avec notre assistant IA</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-bell"></i>
                <h3>Notifications</h3>
                <p>Restez informé des nouveaux messages</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Sécurisé</h3>
                <p>Vos conversations sont privées et protégées</p>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer style="text-align: center; padding: 2rem; background-color: #1e3149; color: white; margin-top: 3rem;">
        <p>&copy; 2025 AidForPeace. All rights reserved.</p>
    </footer>
</body>
</html>
