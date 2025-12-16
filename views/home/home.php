<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidForPeace - Plateforme Humanitaire</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ffd700;
            --primary-dark: #e6c200;
            --primary-light: #ffdf33;
            --secondary: #07112b;
            --secondary-dark: #050d1f;
            --secondary-light: #1a3a5c;
            --accent: #00bcd4;
            --success: #4caf50;
            --danger: #f44336;
            --white: #ffffff;
            --light-bg: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --glass-bg: rgba(7, 17, 43, 0.95);
            --glass-border: rgba(255, 215, 0, 0.2);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #07112b, #1a3a5c, #07102b, #0d2240);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: var(--white);
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animated Stars */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,0.4), transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.3), transparent),
                radial-gradient(1px 1px at 90px 40px, rgba(255,255,255,0.5), transparent),
                radial-gradient(2px 2px at 130px 80px, rgba(255,255,255,0.3), transparent),
                radial-gradient(1px 1px at 160px 120px, rgba(255,255,255,0.4), transparent);
            background-size: 250px 250px;
            animation: twinkle 5s ease-in-out infinite;
            pointer-events: none;
            z-index: -1;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        /* Animated Top Line */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
            z-index: 9999;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* NAVBAR */
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 0 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 75px;
            position: sticky;
            top: 3px;
            z-index: 1000;
            border-bottom: 1px solid var(--glass-border);
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-100%); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .navbar-logo i { 
            color: var(--primary); 
            font-size: 1.6rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .navbar-logo .aid { color: var(--white); }
        .navbar-logo .peace { color: var(--primary); }
        
        .navbar-menu {
            display: flex;
            list-style: none;
            gap: 5px;
        }
        .navbar-menu a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 12px;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .navbar-menu a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.4s ease;
            z-index: -1;
            border-radius: 12px;
        }

        .navbar-menu a:hover, .navbar-menu a.active {
            color: var(--secondary);
            transform: translateY(-2px);
        }

        .navbar-menu a:hover::before, .navbar-menu a.active::before {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .navbar-right { display: flex; align-items: center; gap: 15px; }
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            padding: 12px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover { 
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 215, 0, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        /* HERO SECTION */
        .hero {
            background: transparent;
            padding: 100px 50px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(255,215,0,0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--white);
            line-height: 1.2;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-content h1 span { 
            color: var(--primary);
            text-shadow: 0 0 30px rgba(255, 215, 0, 0.5);
        }
        .hero-content p {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 30px;
            max-width: 500px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }
        .hero-buttons { 
            display: flex; 
            gap: 15px; 
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }
        .btn-hero {
            padding: 16px 38px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-hero:hover::before {
            left: 100%;
        }

        .btn-primary-hero {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            box-shadow: 0 8px 30px rgba(255,215,0,0.4);
        }
        .btn-primary-hero:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(255,215,0,0.5);
        }
        .btn-outline-hero {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
        }
        .btn-outline-hero:hover {
            background: rgba(255,255,255,0.1);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
        }
        
        /* Hero Stats */
        .hero-stats {
            display: flex;
            gap: 40px;
            margin-top: 40px;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }
        .stat-item { 
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }
        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 215, 0, 0.1);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }
        .stat-label {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
        }
        
        /* Hero Image/Visual */
        .hero-visual {
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }
        .hero-image-container {
            position: relative;
            width: 100%;
            max-width: 500px;
        }
        .hero-main-image {
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
            border: 1px solid var(--glass-border);
        }
        .floating-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            padding: 18px 24px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: floatCard 3s ease-in-out infinite;
        }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .floating-card.card-1 { top: 20px; left: -30px; animation-delay: 0s; }
        .floating-card.card-2 { bottom: 40px; right: -30px; animation-delay: 1.5s; }
        .floating-card i { font-size: 1.5rem; color: var(--primary); }
        .floating-card .card-text { font-size: 0.85rem; color: var(--secondary); }
        .floating-card .card-text strong { display: block; color: var(--secondary); }

        /* IMPACT SECTION */
        .impact-section {
            padding: 100px 50px;
            background: rgba(7, 17, 43, 0.5);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .impact-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }
        .section-header h2 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 15px;
        }
        .section-header h2 span {
            color: var(--primary);
        }
        .section-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }
        
        .impact-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .impact-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 40px 25px;
            border-radius: 20px;
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
        }

        .impact-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .impact-card:hover {
            transform: translateY(-15px);
            background: rgba(255, 215, 0, 0.1);
            box-shadow: 0 25px 50px rgba(255,215,0,0.2);
        }

        .impact-card:hover::before {
            transform: scaleX(1);
        }

        .impact-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
            transition: var(--transition);
        }

        .impact-card:hover .impact-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .impact-icon i { font-size: 2rem; color: var(--secondary); }
        .impact-card h3 {
            font-size: 1.3rem;
            color: var(--white);
            margin-bottom: 12px;
        }
        .impact-card p { color: rgba(255, 255, 255, 0.7); font-size: 0.95rem; }

        /* CAUSES SECTION */
        .causes-section {
            padding: 100px 50px;
            background: transparent;
        }
        .causes-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .cause-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            transition: var(--transition);
            position: relative;
        }

        .cause-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .cause-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        }

        .cause-card:hover::before {
            opacity: 1;
        }

        .cause-image {
            height: 220px;
            background-size: cover;
            background-position: center;
            position: relative;
            transition: var(--transition);
        }

        .cause-card:hover .cause-image {
            transform: scale(1.05);
        }

        .cause-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }
        .cause-content { padding: 28px; }
        .cause-content h3 {
            font-size: 1.3rem;
            color: var(--secondary);
            margin-bottom: 12px;
        }
        .cause-content p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .progress-bar {
            height: 10px;
            background: #e2e8f0;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            100% { left: 100%; }
        }

        .cause-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }
        .cause-stats span { color: var(--text-muted); }
        .cause-stats strong { color: var(--secondary); }

        /* CTA SECTION */
        .cta-section {
            padding: 100px 50px;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(0, 188, 212, 0.1));
            backdrop-filter: blur(10px);
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            text-align: center;
        }
        .cta-content {
            max-width: 800px;
            margin: 0 auto;
        }
        .cta-content h2 {
            font-size: 2.5rem;
            color: var(--white);
            margin-bottom: 20px;
        }
        .cta-content p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .cta-buttons { display: flex; gap: 20px; justify-content: center; }

        /* FEATURES SECTION */
        .features-section {
            padding: 80px 50px;
            background: var(--white);
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .feature-item {
            text-align: center;
            padding: 30px;
        }
        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--light-bg);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            transition: all 0.3s;
        }
        .feature-item:hover .feature-icon {
            background: var(--primary);
        }
        .feature-icon i { font-size: 2rem; color: var(--primary); transition: all 0.3s; }
        .feature-item:hover .feature-icon i { color: var(--secondary); }
        .feature-item h3 { color: var(--secondary); margin-bottom: 10px; }
        .feature-item p { color: var(--text-muted); }

        /* FOOTER */
        .footer {
            background: linear-gradient(135deg, #050d1f 0%, #07112b 100%);
            color: var(--white);
            padding: 80px 50px 40px;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }

        .footer::after {
            content: '';
            position: absolute;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 200px;
            background: radial-gradient(ellipse, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 50px;
            max-width: 1200px;
            margin: 0 auto 50px;
            position: relative;
            z-index: 1;
        }
        .footer-brand h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .footer-brand h3 span { 
            color: var(--primary);
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }
        .footer-brand p { 
            color: rgba(255,255,255,0.7); 
            font-size: 1rem;
            line-height: 1.7;
        }
        .footer-links h4 {
            color: var(--primary);
            margin-bottom: 25px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .footer-links a {
            display: block;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            margin-bottom: 14px;
            transition: var(--transition);
            position: relative;
            padding-left: 0;
        }
        .footer-links a::before {
            content: '→';
            position: absolute;
            left: -20px;
            opacity: 0;
            transition: var(--transition);
            color: var(--primary);
        }
        .footer-links a:hover { 
            color: var(--primary); 
            padding-left: 25px;
        }
        .footer-links a:hover::before {
            opacity: 1;
            left: 0;
        }
        .footer-bottom {
            text-align: center;
            padding-top: 40px;
            border-top: 1px solid var(--glass-border);
            color: rgba(255,255,255,0.5);
            position: relative;
            z-index: 1;
        }
        .social-links { 
            display: flex; 
            gap: 15px; 
            margin-top: 25px;
        }
        .social-links a {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            transition: var(--transition);
            font-size: 1.2rem;
        }
        .social-links a:hover { 
            background: var(--primary); 
            color: var(--secondary);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .hero-container { grid-template-columns: 1fr; text-align: center; }
            .hero-content p { margin: 0 auto 30px; }
            .hero-buttons { justify-content: center; }
            .hero-stats { justify-content: center; }
            .hero-visual { display: none; }
            .impact-grid { grid-template-columns: repeat(2, 1fr); }
            .causes-grid { grid-template-columns: repeat(2, 1fr); }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; flex-wrap: wrap; height: auto; padding: 15px 20px; }
            .navbar-menu { display: none; }
            .hero { padding: 50px 20px; }
            .hero-content h1 { font-size: 2.2rem; }
            .impact-grid, .causes-grid, .features-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .cta-buttons { flex-direction: column; align-items: center; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="navbar-logo">
            <i class="fas fa-hand-holding-heart"></i>
            <span class="aid">Aid</span><span class="peace">ForPeace</span>
        </a>
        
        <ul class="navbar-menu">
            <li><a href="index.php" class="active">Accueil</a></li>
            <li><a href="index.php?controller=user&action=index">Utilisateurs</a></li>
            <li><a href="index.php?controller=map&action=index">Map Mondiale</a></li>
            <li><a href="index.php?controller=messagerie&action=inbox&user=1">Messagerie</a></li>
            <li><a href="index.php?page=testimonials">Feedback</a></li>
            <li><a href="index.php?controller=product&action=shop">Boutique</a></li>
            <li><a href="index.php?controller=donation&action=index">Faire un Don</a></li>
        </ul>
        
        <div class="navbar-right">
            <?php if ($is_logged_in): ?>
                <a href="index.php?controller=page&action=profile" class="btn-login">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($user_name) ?>
                </a>
            <?php else: ?>
                <a href="index.php?controller=user&action=login" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Connexion
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Ensemble, créons un <span>impact positif</span> dans le monde</h1>
                <p>Rejoignez notre communauté de bénévoles et donateurs pour aider les personnes dans le besoin à travers le monde.</p>
                
                <div class="hero-buttons">
                    <a href="index.php?controller=home&action=donation" class="btn-hero btn-primary-hero">
                        <i class="fas fa-heart"></i> Faire un Don
                    </a>
                    <a href="index.php?controller=map&action=index" class="btn-hero btn-outline-hero">
                        <i class="fas fa-globe-africa"></i> Explorer la Map
                    </a>
                </div>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">50K+</div>
                        <div class="stat-label">Donateurs</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">120+</div>
                        <div class="stat-label">Pays aidés</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">€2M+</div>
                        <div class="stat-label">Collectés</div>
                    </div>
                </div>
            </div>
            
            <div class="hero-visual">
                <div class="hero-image-container">
                    <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600&h=400&fit=crop" 
                         alt="Aide humanitaire" class="hero-main-image">
                    <div class="floating-card card-1">
                        <i class="fas fa-hand-holding-heart"></i>
                        <div class="card-text">
                            <strong>+1,234</strong>
                            <span>Dons ce mois</span>
                        </div>
                    </div>
                    <div class="floating-card card-2">
                        <i class="fas fa-users"></i>
                        <div class="card-text">
                            <strong>+500</strong>
                            <span>Nouveaux bénévoles</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- IMPACT SECTION -->
    <section class="impact-section">
        <div class="section-header">
            <h2>Notre Impact Global</h2>
            <p>Découvrez comment nous faisons la différence chaque jour dans la vie de milliers de personnes</p>
        </div>
        
        <div class="impact-grid">
            <div class="impact-card">
                <div class="impact-icon"><i class="fas fa-utensils"></i></div>
                <h3>Aide Alimentaire</h3>
                <p>Distribution de repas et aide alimentaire d'urgence aux populations vulnérables</p>
            </div>
            <div class="impact-card">
                <div class="impact-icon"><i class="fas fa-heartbeat"></i></div>
                <h3>Santé</h3>
                <p>Accès aux soins médicaux et fournitures sanitaires essentielles</p>
            </div>
            <div class="impact-card">
                <div class="impact-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3>Éducation</h3>
                <p>Programmes éducatifs et fournitures scolaires pour les enfants</p>
            </div>
            <div class="impact-card">
                <div class="impact-icon"><i class="fas fa-home"></i></div>
                <h3>Logement</h3>
                <p>Construction d'abris et aide au logement pour les réfugiés</p>
            </div>
        </div>
    </section>

    <!-- CAUSES SECTION -->
    <section class="causes-section">
        <div class="section-header">
            <h2>Causes Urgentes</h2>
            <p>Ces projets ont besoin de votre soutien immédiat</p>
        </div>
        
        <div class="causes-grid">
            <div class="cause-card">
                <div class="cause-image" style="background-image: url('https://images.unsplash.com/photo-1594708767771-a7502f7c0b07?w=400&h=200&fit=crop')">
                    <span class="cause-badge">Urgent</span>
                </div>
                <div class="cause-content">
                    <h3>Aide aux réfugiés syriens</h3>
                    <p>Fournir nourriture, abri et soins médicaux aux familles déplacées</p>
                    <div class="progress-bar"><div class="progress-fill" style="width: 75%"></div></div>
                    <div class="cause-stats">
                        <span><strong>€75,000</strong> collectés</span>
                        <span>Objectif: <strong>€100,000</strong></span>
                    </div>
                </div>
            </div>
            
            <div class="cause-card">
                <div class="cause-image" style="background-image: url('https://images.unsplash.com/photo-1509099836639-18ba1795216d?w=400&h=200&fit=crop')">
                    <span class="cause-badge">Critique</span>
                </div>
                <div class="cause-content">
                    <h3>Eau potable au Yémen</h3>
                    <p>Installation de puits et systèmes de purification d'eau</p>
                    <div class="progress-bar"><div class="progress-fill" style="width: 45%"></div></div>
                    <div class="cause-stats">
                        <span><strong>€45,000</strong> collectés</span>
                        <span>Objectif: <strong>€100,000</strong></span>
                    </div>
                </div>
            </div>
            
            <div class="cause-card">
                <div class="cause-image" style="background-image: url('https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=400&h=200&fit=crop')">
                    <span class="cause-badge">En cours</span>
                </div>
                <div class="cause-content">
                    <h3>Écoles en Afghanistan</h3>
                    <p>Construction et équipement d'écoles pour les enfants</p>
                    <div class="progress-bar"><div class="progress-fill" style="width: 60%"></div></div>
                    <div class="cause-stats">
                        <span><strong>€30,000</strong> collectés</span>
                        <span>Objectif: <strong>€50,000</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Prêt à faire la différence ?</h2>
            <p>Chaque don, peu importe sa taille, contribue à changer des vies. Rejoignez notre communauté de donateurs et bénévoles.</p>
            <div class="cta-buttons">
                <a href="index.php?controller=home&action=donation" class="btn-hero btn-primary-hero">
                    <i class="fas fa-heart"></i> Donner Maintenant
                </a>
                <a href="index.php?controller=user&action=register" class="btn-hero btn-outline-hero">
                    <i class="fas fa-user-plus"></i> Devenir Bénévole
                </a>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="features-section">
        <div class="section-header">
            <h2>Pourquoi AidForPeace ?</h2>
            <p>Une plateforme transparente et efficace pour maximiser votre impact</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>100% Sécurisé</h3>
                <p>Vos dons sont protégés et acheminés directement aux bénéficiaires</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Transparence Totale</h3>
                <p>Suivez l'utilisation de vos dons en temps réel sur notre plateforme</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-globe"></i></div>
                <h3>Impact Global</h3>
                <p>Nous intervenons dans plus de 120 pays à travers le monde</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>Aid<span>ForPeace</span></h3>
                <p>Ensemble, nous pouvons créer un monde meilleur. Chaque action compte, chaque don fait la différence.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="footer-links">
                <h4>Navigation</h4>
                <a href="index.php">Accueil</a>
                <a href="index.php?controller=map&action=index">Map Mondiale</a>
                <a href="index.php?controller=messagerie&action=index">Messagerie</a>
                <a href="index.php?controller=product&action=shop">Boutique</a>
            </div>
            
            <div class="footer-links">
                <h4>Compte</h4>
                <a href="index.php?controller=user&action=login">Connexion</a>
                <a href="index.php?controller=user&action=register">Inscription</a>
                <a href="index.php?controller=user&action=profile">Mon Profil</a>
            </div>
            
            <div class="footer-links">
                <h4>Support</h4>
                <a href="#">Centre d'aide</a>
                <a href="#">Contact</a>
                <a href="#">FAQ</a>
                <a href="#">Mentions légales</a>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 AidForPeace. Tous droits réservés. Fait avec ❤️ pour un monde meilleur.</p>
        </div>
    </footer>

</body>
</html>
