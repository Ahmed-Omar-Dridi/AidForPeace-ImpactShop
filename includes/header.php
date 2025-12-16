<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'AidForPeace' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ffd700;
            --primary-dark: #e6c200;
            --secondary: #07112b;
            --secondary-light: #1a3a5c;
            --accent: #00bcd4;
            --success: #4caf50;
            --danger: #f44336;
            --warning: #ff9800;
            --text-light: #ffffff;
            --text-muted: rgba(255,255,255,0.7);
            --glass-bg: rgba(7, 17, 43, 0.95);
            --glass-border: rgba(255, 215, 0, 0.2);
            --shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(-45deg, #07112b, #1a3a5c, #07102b, #0d2240);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            color: var(--text-light);
            overflow-x: hidden;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animated Stars Background */
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
                radial-gradient(1px 1px at 160px 120px, rgba(255,255,255,0.4), transparent),
                radial-gradient(1.5px 1.5px at 200px 150px, rgba(255,255,255,0.3), transparent);
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

        /* Page Hero Section */
        .page-hero {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: white;
            padding: 50px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid var(--glass-border);
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 60%;
            height: 200%;
            background: radial-gradient(ellipse, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 215, 0, 0.15);
            color: var(--primary);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid rgba(255, 215, 0, 0.3);
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.6s ease-out;
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

        /* Navbar Styles */
        .navbar {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 15px 0;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-100%);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar-brand {
            font-weight: 800 !important;
            font-size: 1.5rem !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand i {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .nav-link {
            position: relative;
            padding: 10px 20px !important;
            margin: 0 5px;
            border-radius: 10px;
            transition: var(--transition) !important;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
            transform: translateX(-50%);
        }

        .nav-link:hover::before {
            width: 80%;
        }

        .nav-link:hover {
            background: rgba(255, 215, 0, 0.1);
            color: var(--primary) !important;
            transform: translateY(-2px);
        }

        /* Buttons */
        .btn {
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            color: var(--secondary);
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        }

        .btn-primary:hover { 
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 215, 0, 0.4);
            color: var(--secondary);
        }

        .btn-outline-light {
            border: 2px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
        }

        .btn-outline-light:hover { 
            background: var(--primary);
            border-color: var(--primary);
            color: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 215, 0, 0.3);
        }

        .btn-amber {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            color: var(--secondary);
            font-weight: 700;
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        }

        .btn-amber:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 215, 0, 0.4);
            color: var(--secondary);
        }

        /* Cards */
        .card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: var(--transition);
            overflow: hidden;
        }

        .card::before {
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

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 215, 0, 0.4);
        }

        .card-lift {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .card-lift::before {
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

        .card-lift:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        }

        /* Form Controls */
        .form-control, .form-select {
            background: rgba(7, 17, 43, 0.6);
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            padding: 14px 18px;
            color: var(--text-light);
            transition: var(--transition);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-control:focus, .form-select:focus {
            background: rgba(7, 17, 43, 0.8);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.15);
            color: var(--text-light);
        }

        .form-label {
            color: var(--text-light);
            font-weight: 600;
            margin-bottom: 8px;
        }

        /* Tables */
        .table {
            color: var(--text-light);
            margin-bottom: 0;
        }

        .table th {
            background: rgba(255, 215, 0, 0.1);
            color: var(--primary);
            font-weight: 700;
            border-bottom: 2px solid var(--glass-border);
            padding: 15px;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid var(--glass-border);
            vertical-align: middle;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: rgba(255, 215, 0, 0.05);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.2);
            color: #81c784;
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: rgba(244, 67, 54, 0.2);
            color: #ef5350;
            border-left: 4px solid var(--danger);
        }

        /* Form Modern Style */
        .form-modern .form-control {
            background: rgba(7, 17, 43, 0.5);
            border: 2px solid var(--glass-border);
            color: var(--text-light);
        }

        .form-modern .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.1);
        }

        /* Animations for page elements */
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animate-slide-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Stagger delays */
        .stagger-1 { animation-delay: 0.1s; animation-fill-mode: both; }
        .stagger-2 { animation-delay: 0.2s; animation-fill-mode: both; }
        .stagger-3 { animation-delay: 0.3s; animation-fill-mode: both; }
        .stagger-4 { animation-delay: 0.4s; animation-fill-mode: both; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* Text colors */
        .text-white-50 {
            color: var(--text-muted) !important;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--text-light);
        }

        p {
            color: var(--text-muted);
        }

        /* Container animation */
        .container {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>
<body>
    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="<?= BASE_URL ?>index.php">
                <i class="fas fa-hand-holding-heart text-warning me-2"></i>AidForPeace
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="<?= BASE_URL ?>index.php?controller=admin&action=dashboard"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                <a class="nav-link text-white" href="<?= BASE_URL ?>index.php"><i class="fas fa-home me-1"></i> Site</a>
            </div>
        </div>
    </nav>
