<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - AidForPeace</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        :root {
            --primary: #ffb600;
            --primary-dark: #e6a500;
            --secondary: #1e3149;
            --secondary-dark: #15202e;
            --text-dark: #5a6570;
            --light-bg: #f8f9fa;
            --white: #ffffff;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            min-height: 100vh;
            color: var(--text-dark);
            padding: 0;
            margin: 0;
        }
        
        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            padding: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            height: 80px;
        }
        
        .navbar-logo {
            display: flex;
            align-items: center;
            padding: 0 40px;
            text-decoration: none;
            font-size: 1.8rem;
            font-weight: 900;
            gap: 10px;
        }
        
        .navbar-logo .impact {
            color: var(--white);
        }
        
        .navbar-logo .shop {
            color: var(--primary);
        }
        
        .navbar-logo i {
            color: var(--primary);
            font-size: 2rem;
        }
        
        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 0;
            list-style: none;
            flex: 1;
            justify-content: center;
        }
        
        .navbar-menu li {
            position: relative;
        }
        
        .navbar-menu a {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--white);
            text-decoration: none;
            padding: 30px 25px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            height: 80px;
        }
        
        .navbar-menu a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #ffc933);
            transition: width 0.3s ease;
        }
        
        .navbar-menu a:hover {
            background: rgba(255, 182, 0, 0.15);
            color: var(--primary);
            transform: translateY(-2px);
        }
        
        .navbar-menu a:hover::before {
            width: 100%;
        }
        
        .navbar-menu a i {
            font-size: 1.2rem;
        }
        
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 0 40px;
        }
        
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--white);
            padding: 12px 20px;
            background: rgba(255, 182, 0, 0.2);
            border-radius: 25px;
            border: 2px solid rgba(255, 182, 0, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: 600;
        }
        
        .navbar-user:hover {
            background: rgba(255, 182, 0, 0.3);
            border-color: var(--primary);
            transform: scale(1.05);
        }
        
        .navbar-user i {
            font-size: 1.3rem;
            color: var(--primary);
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .empty-state i {
            font-size: 5rem;
            color: var(--primary);
            margin-bottom: 20px;
            opacity: 0.8;
        }
        
        .empty-state h1 {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 15px;
            font-weight: 800;
        }
        
        .empty-state p {
            font-size: 1.1rem;
            color: var(--text-dark);
            margin-bottom: 30px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="index.php" class="navbar-logo">
            <i class="fas fa-shopping-bag"></i>
            <span><span class="impact">Impact</span><span class="shop">Shop</span></span>
        </a>
        
        <ul class="navbar-menu">
            <li>
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Accueil</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=product&action=shop">
                    <i class="fas fa-store"></i>
                    <span>Boutique</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=map&action=index">
                    <i class="fas fa-map"></i>
                    <span>Map</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=messagerie&action=index">
                    <i class="fas fa-envelope"></i>
                    <span>Messagerie</span>
                </a>
            </li>
        </ul>
        
        <div class="navbar-right">
            <a href="index.php?controller=dashboard&action=index" class="navbar-user">
                <i class="fas fa-user-circle"></i>
                <span>Admin</span>
            </a>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="empty-state">
            <i class="fas fa-home"></i>
            <h1>Accueil</h1>
            <p>Page d'accueil - À compléter</p>
        </div>
    </div>
</body>
</html>
