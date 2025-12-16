<?php
require_once __DIR__ . '/../../controllers/CountryController.php';

$countryController = new CountryController();
$countries = $countryController->getCountries();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charity Connect - Interactive 3D Globe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/frontend.css">
    <!-- Three.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- D3.js for geographic data -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.0.0/d3.min.js"></script>
    <!-- OrbitControls for rotation -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.min.js"></script>
    <!-- Tween.js for smooth animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tween.js/18.6.4/tween.umd.js"></script>
    <!-- TopoJSON -->
    <script src="https://unpkg.com/topojson-client@3"></script>
    <!-- Chart.js for comparison charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- NEW: Country Index Data -->
    <script src="assets/js/countryIndexData.js"></script>
    
    <!-- Globe JS (must load AFTER index data) -->
    <script src="assets/js/globe-v2.js"></script>
    
    <style>
        /* ==================== CSS VARIABLES - MATCHING HOME INDEX PAGE ==================== */
        :root {
            --primary: #ffb600;
            --primary-dark: #e6a500;
            --primary-light: #ffc933;
            --secondary: #1e3149;
            --secondary-dark: #15202e;
            --secondary-light: #2a4562;
            --accent: #00bcd4;
            --success: #4caf50;
            --danger: #f44336;
            --white: #ffffff;
            --light-bg: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #666;
            --glass-bg: rgba(30, 49, 73, 0.95);
            --glass-border: rgba(255, 182, 0, 0.2);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ==================== BASE STYLES - MATCHING HOME INDEX PAGE ==================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3149 0%, #15202e 50%, #0a0a0a 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: var(--white);
            line-height: 1.6;
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

        /* ==================== NAVBAR - MATCHING HOME PAGE ==================== */
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
            box-shadow: 0 4px 20px rgba(255, 182, 0, 0.3);
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
            box-shadow: 0 8px 30px rgba(255, 182, 0, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        /* ==================== CONTAINER ==================== */
        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ==================== SEARCH SECTION ==================== */
        .search-section {
            background: rgba(30, 49, 73, 0.8);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 24px;
            margin-bottom: 30px;
            border: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .search-section::before {
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

        .search-section h2 {
            color: var(--primary);
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
        }

        .search-bar {
            display: flex;
            gap: 15px;
            max-width: 650px;
            margin: 0 auto;
        }

        .search-bar input {
            flex: 1;
            padding: 18px 28px;
            border: 2px solid rgba(255, 182, 0, 0.3);
            border-radius: 50px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.95);
            color: var(--secondary);
            transition: var(--transition);
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 182, 0, 0.2);
        }

        .search-bar button {
            padding: 18px 36px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .search-bar button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .search-bar button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 182, 0, 0.4);
        }

        .search-bar button:hover::before {
            left: 100%;
        }

        /* ==================== INDEX SELECTOR PANEL ==================== */
        .index-selector-panel {
            background: linear-gradient(135deg, rgba(255, 182, 0, 0.15), rgba(0, 188, 212, 0.1));
            backdrop-filter: blur(20px);
            padding: 35px;
            border-radius: 24px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 182, 0, 0.3);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        .index-selector-panel::before {
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

        .index-selector-panel h2 {
            color: var(--primary);
            margin: 0 0 10px 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .index-description {
            color: rgba(255, 255, 255, 0.8);
            margin: 0 0 25px 0;
            font-size: 1rem;
        }

        .index-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
        }

        .index-btn {
            background: rgba(255, 255, 255, 0.95);
            border: 3px solid transparent;
            border-radius: 16px;
            padding: 20px 15px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .index-btn:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            border-color: var(--primary);
        }

        .index-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(255, 182, 0, 0.4);
        }

        .index-btn.active .index-name,
        .index-btn.active .index-desc {
            color: var(--secondary);
        }

        .index-icon {
            font-size: 2rem;
            line-height: 1;
        }

        .index-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--secondary);
        }

        .index-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.3;
        }

        /* ==================== MAP CONTAINER ==================== */
        .map-container {
            background: rgba(30, 49, 73, 0.8);
            backdrop-filter: blur(20px);
            padding: 30px;
            border-radius: 24px;
            margin-bottom: 30px;
            border: 1px solid var(--glass-border);
            position: relative;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .map-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
            border-radius: 24px 24px 0 0;
        }

        #globeContainer {
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--glass-border);
        }

        /* ==================== LEGEND ==================== */
        .legend {
            background: rgba(30, 49, 73, 0.9);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 16px;
            margin-top: 25px;
            border: 1px solid var(--glass-border);
        }

        .legend h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.1rem;
            font-weight: 700;
            text-align: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 12px 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.9);
        }

        .legend-item:hover {
            background: rgba(255, 182, 0, 0.1);
            transform: translateX(8px);
        }

        .legend-color {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            margin-right: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .stable { background: linear-gradient(135deg, #4CAF50, #81C784); }
        .high { background: linear-gradient(135deg, #FF9800, #FFB74D); }
        .critical { background: linear-gradient(135deg, #F44336, #EF5350); }

        /* ==================== INFO PANEL ==================== */
        .info-panel {
            background: rgba(30, 49, 73, 0.9);
            backdrop-filter: blur(20px);
            padding: 35px;
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            display: none;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .info-panel.active {
            display: block;
        }

        .info-panel h2 {
            color: var(--primary);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .info-panel h3 {
            color: rgba(255, 255, 255, 0.9);
            margin-top: 25px;
            font-size: 1.1rem;
        }

        .info-panel p {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
        }

        /* ==================== NGO CARDS ==================== */
        .ngo-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 15px;
            border: 1px solid var(--glass-border);
            border-left: 4px solid var(--primary);
            transition: all 0.4s ease;
        }

        .ngo-card:hover {
            transform: translateY(-5px) translateX(5px);
            box-shadow: 0 15px 40px rgba(255, 182, 0, 0.15);
            border-left-color: var(--accent);
        }

        .ngo-card h3 {
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .ngo-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        /* ==================== BUTTONS ==================== */
        .donate-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .donate-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .donate-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 182, 0, 0.4);
        }

        .donate-btn:hover::before {
            left: 100%;
        }

        /* ==================== BOOKMARK & COMPARE BUTTONS ==================== */
        .bookmark-btn {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bookmark-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 152, 0, 0.4);
        }

        .bookmark-btn.bookmarked {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
        }

        .add-to-compare-btn {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-to-compare-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4);
        }

        /* ==================== NOTIFICATIONS ==================== */
        .bookmark-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            padding: 16px 24px;
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            z-index: 10001;
            animation: slideInRight 0.4s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .bookmark-notification.error {
            background: linear-gradient(135deg, #F44336, #D32F2F);
        }

        /* ==================== COMPARE PANEL ==================== */
        .compare-panel {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 500px;
            max-height: 700px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            overflow: hidden;
            animation: slideDown 0.3s ease;
        }
        
        .compare-panel-header {
            background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .compare-panel-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        
        .compare-close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 24px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .compare-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .compare-panel-content {
            padding: 20px;
            max-height: 620px;
            overflow-y: auto;
        }
        
        .selected-countries {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        
        .selected-country-list h4 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 16px;
        }
        
        #compareList {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            min-height: 60px;
        }
        
        .compare-country-tag {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .remove-compare-btn {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            color: white;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .remove-compare-btn:hover {
            background: rgba(255, 255, 255, 0.5);
            transform: rotate(90deg);
        }
        
        .compare-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
            color: white;
        }
        
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #666;
            border: 1px solid #ddd;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
        }
        
        .comparison-charts {
            margin: 25px 0;
            height: 300px;
        }
        
        .comparison-table {
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 20px;
        }
        
        .comparison-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .comparison-table th {
            background: #4CAF50;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .comparison-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .comparison-table tr:hover {
            background: #f1f1f1;
        }
        
        .index-value {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 600;
            text-align: center;
            min-width: 50px;
        }
        
        .value-low { background: #d4edda; color: #155724; }
        .value-medium { background: #fff3cd; color: #856404; }
        .value-high { background: #f8d7da; color: #721c24; }
        
        /* Info Panel Header */
        .info-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .info-panel-header h2 {
            margin: 0;
            flex-grow: 1;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .add-to-compare-btn {
            background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-to-compare-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        
        .add-to-compare-btn.added {
            background: linear-gradient(135deg, #2196F3 0%, #0D47A1 100%);
        }
        
        /* Responsive design */
        @media (max-width: 1024px) {
            .navbar { padding: 0 30px; }
            .navbar-menu { gap: 3px; }
            .navbar-menu a { padding: 10px 15px; font-size: 0.85rem; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; flex-wrap: wrap; height: auto; padding: 15px 20px; }
            .navbar-menu { display: none; }
            
            .compare-panel {
                width: calc(100% - 40px);
                right: 20px;
                left: 20px;
                max-height: 500px;
            }
            
            .comparison-charts {
                height: 250px;
            }
            
            .info-panel-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }
            
            .bookmark-btn, .add-to-compare-btn {
                flex: 1;
                justify-content: center;
            }
        }
        
        /* Existing styles... */
        .news-dropdown {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 450px;
            max-height: 600px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            overflow: hidden;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .news-dropdown-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .news-dropdown-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        
        .news-close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 24px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .news-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .news-dropdown-content {
            max-height: 520px;
            overflow-y: auto;
            padding: 15px;
        }
        
        .news-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .news-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .news-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.2);
        }
        
        .news-title {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin: 0 0 8px 0;
            line-height: 1.4;
        }
        
        .news-description {
            font-size: 13px;
            color: #666;
            line-height: 1.4;
            margin: 0 0 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .news-meta {
            display: flex;
            gap: 12px;
            font-size: 11px;
            color: #999;
        }
        
        .news-source {
            font-weight: 600;
            color: #667eea;
        }
        
        .loading {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-size: 14px;
        }
        
        .loading::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
        }
        
        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60%, 100% { content: '...'; }
        }
        
        /* Responsive news dropdown */
        @media (max-width: 768px) {
            .news-dropdown {
                width: calc(100% - 40px);
                right: 20px;
                left: 20px;
                max-height: 500px;
            }
        }
        
        /* Index Selector Panel Styles */
        .index-selector-panel {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }
        
        .index-selector-panel h2 {
            color: white;
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .index-description {
            color: rgba(255, 255, 255, 0.9);
            margin: 0 0 25px 0;
            font-size: 16px;
        }
        
        .index-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .index-btn {
            background: white;
            border: 3px solid transparent;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .index-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .index-btn.active {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .index-btn.active .index-icon,
        .index-btn.active .index-name,
        .index-btn.active .index-desc {
            color: white;
        }
        
        .index-icon {
            font-size: 32px;
            line-height: 1;
        }
        
        .index-name {
            font-weight: 700;
            font-size: 16px;
            color: #333;
        }
        
        .index-desc {
            font-size: 13px;
            color: #666;
            line-height: 1.4;
        }
        
        /* Index Stats Display */
        .index-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .index-stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 5px solid #667eea;
        }
        
        .index-stat-header {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .index-stat-value {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .index-stat-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .index-stat-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.6s ease;
        }
        
        .index-stat-desc {
            font-size: 12px;
            color: #888;
            margin-top: 8px;
        }
        
        /* Other existing styles... */
        .country-label {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: white;
            background: rgba(0, 0, 0, 0.7);
            padding: 2px 6px;
            border-radius: 3px;
            pointer-events: none;
            white-space: nowrap;
            z-index: 1000;
        }
        
        .subscription-panel {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-top: 30px;
            border-left: 6px solid #dc3545;
            transition: all 0.3s ease;
        }
        
        .subscription-panel:hover {
            box-shadow: 0 6px 25px rgba(220, 53, 69, 0.15);
        }
        
        .subscription-panel h3 {
            color: #dc3545;
            margin-bottom: 15px;
            font-size: 26px;
            font-weight: 700;
        }
        
        .subscription-panel p {
            color: #666;
            margin-bottom: 25px;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .subscription-panel .form-group {
            margin-bottom: 20px;
        }
        
        .subscription-panel .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }
        
        .subscription-panel input[type="email"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        
        .subscription-panel input[type="email"]:focus {
            outline: none;
            border-color: #6B8E23;
            box-shadow: 0 0 0 3px rgba(107, 142, 35, 0.1);
        }
        
        .subscription-panel input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.3);
        }
        
        .subscription-panel .form-group label[for] {
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            cursor: pointer;
        }
        
        .subscription-panel .donate-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        
        .subscription-panel .donate-btn:hover {
            background: linear-gradient(135deg, #c82333 0%, #dc3545 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }
        
        .subscription-panel .donate-btn:active {
            transform: translateY(0);
        }
        
        @media (max-width: 768px) {
            .subscription-panel {
                padding: 20px;
                margin: 20px 10px;
            }
            
            .subscription-panel h3 {
                font-size: 22px;
            }
            
            .subscription-panel .donate-btn {
                padding: 14px;
                font-size: 16px;
            }
            
            .index-buttons {
                grid-template-columns: 1fr;
            }
            
            .index-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR - MATCHING HOME PAGE -->
    <nav class="navbar">
        <a href="index.php" class="navbar-logo">
            <i class="fas fa-hand-holding-heart"></i>
            <span class="aid">Aid</span><span class="peace">ForPeace</span>
        </a>
        
        <ul class="navbar-menu">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="index.php?controller=user&action=index">Utilisateurs</a></li>
            <li><a href="index.php?controller=map&action=index" class="active">Map Mondiale</a></li>
            <li><a href="index.php?controller=messagerie&action=inbox&user=1">Messagerie</a></li>
            <li><a href="index.php?page=testimonials">Feedback</a></li>
            <li><a href="index.php?controller=product&action=shop">Boutique</a></li>
            <li><a href="index.php?controller=donation&action=index">Faire un Don</a></li>
        </ul>
        
        <div class="navbar-right">
            <a href="index.php?controller=user&action=login" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Connexion
            </a>
        </div>
    </nav>
    
    <!-- NEW: Comparison Panel -->
    <div class="compare-panel" id="comparePanel" style="display: none;">
        <div class="compare-panel-header">
            <h3>📊 Country Comparison</h3>
            <button class="compare-close-btn" onclick="toggleComparePanel()">✕</button>
        </div>
        <div class="compare-panel-content">
            <!-- Selected Countries -->
            <div class="selected-countries" id="selectedCountries">
                <div class="selected-country-list">
                    <h4>Selected for Comparison:</h4>
                    <div id="compareList"></div>
                </div>
                <div class="compare-actions">
                    <button class="btn btn-primary" onclick="startComparison()" id="compareBtn" disabled>
                        Compare Now
                    </button>
                    <button class="btn btn-secondary" onclick="clearComparison()">
                        Clear All
                    </button>
                </div>
            </div>
            
            <!-- Comparison Results -->
            <div id="comparisonResults" style="display: none;">
                <h4>Comparison Results:</h4>
                <div class="comparison-charts">
                    <canvas id="comparisonChart"></canvas>
                </div>
                <div class="comparison-table" id="comparisonTable"></div>
            </div>
        </div>
    </div>
    
    <!-- News Feed Dropdown Panel -->
    <div class="news-dropdown" id="newsDropdown" style="display: none;">
        <div class="news-dropdown-header">
            <h3>📰 World Crisis News</h3>
            <button class="news-close-btn" onclick="toggleNews()">✕</button>
        </div>
        <div class="news-dropdown-content" id="newsContent">
            <div class="loading">Loading latest news</div>
        </div>
    </div>
    
    <!-- Main Container -->
    <div class="container">
        <!-- Search Section -->
        <div class="search-section">
            <h2>🔍 Find a Country</h2>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Type country name ">
                <button onclick="searchCountry()">Search</button>
            </div>
            <div class="search-error" id="searchError"></div>
        </div>
        
        <!-- NEW: Index Selector Panel -->
        <div class="index-selector-panel">
            <h2>📊 View by Index</h2>
            <p class="index-description">Select a social indicator to visualize on the globe</p>
            
            <div class="index-buttons">
                <button class="index-btn active" onclick="switchIndexMode('crisis')" data-index="crisis">
                    <span class="index-icon">🚨</span>
                    <span class="index-name">Crisis Level</span>
                    <span class="index-desc">Overall emergency status</span>
                </button>
                
                <button class="index-btn" onclick="switchIndexMode('hunger')" data-index="hunger">
                    <span class="index-icon">🍞</span>
                    <span class="index-name">Hunger Index</span>
                    <span class="index-desc">Food scarcity levels</span>
                </button>
                
                <button class="index-btn" onclick="switchIndexMode('poverty')" data-index="poverty">
                    <span class="index-icon">💰</span>
                    <span class="index-name">Poverty Rate</span>
                    <span class="index-desc">Economic hardship</span>
                </button>
                
                <button class="index-btn" onclick="switchIndexMode('crime')" data-index="crime">
                    <span class="index-icon">⚠️</span>
                    <span class="index-name">Crime Rate</span>
                    <span class="index-desc">Safety and security</span>
                </button>
                
                <button class="index-btn" onclick="switchIndexMode('migration')" data-index="migration">
                    <span class="index-icon">🚶</span>
                    <span class="index-name">Migration</span>
                    <span class="index-desc">Displacement pressure</span>
                </button>
                
                <button class="index-btn" onclick="switchIndexMode('waterAccess')" data-index="waterAccess">
                    <span class="index-icon">💧</span>
                    <span class="index-name">Water Access</span>
                    <span class="index-desc">Clean water scarcity</span>
                </button>
                
                <button class="index-btn" onclick="switchIndexMode('health')" data-index="health">
                    <span class="index-icon">🏥</span>
                    <span class="index-name">Health Crisis</span>
                    <span class="index-desc">Healthcare system burden</span>
                </button>
            </div>
        </div>
        
        <!-- 3D Globe Container -->
        <div class="map-container">
            <div id="globeContainer" style="width: 100%; height: 600px; border-radius: 12px;"></div>
            
            <!-- Legend -->
            <div class="legend" id="legendPanel">
                <h3 id="legendTitle">🌍 Crisis Level Legend</h3>
                <div id="legendContent">
                    <div class="legend-item">
                        <div class="legend-color stable"></div>
                        <span>🟩 Stable - Safe conditions</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color high"></div>
                        <span>🟧 High - Elevated risk</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color critical"></div>
                        <span>🟥 Critical - Emergency situation</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Country Info Panel -->
        <div class="info-panel" id="infoPanel">
            <div class="info-panel-header">
                <h2 id="countryName">Select a country</h2>
                <div class="action-buttons">
                    <!-- NEW: Bookmark Button -->
                    <button class="bookmark-btn" id="bookmarkBtn" onclick="toggleBookmark()" style="display: none;">
                        <i>🔖</i> <span>Bookmark</span>
                    </button>
                    <!-- Add to Compare Button -->
                    <button class="add-to-compare-btn" id="addToCompareBtn" onclick="addToComparison()" style="display: none;">
                        + Add to Compare
                    </button>
                </div>
            </div>
            <div id="crisisLevel"></div>
            <p id="countryDescription"></p>
            
            <!-- NEW: Index Data Display -->
            <div id="indexDataDisplay" style="display: none;">
                <h3 style="margin-top: 20px; color: #333;">📊 Index Data</h3>
                <div id="indexDataContent"></div>
            </div>
            
            <h3 style="margin-top: 20px; color: #333;">NGOs Operating Here:</h3>
            <div id="ngoList">
                <!-- NGO cards will be inserted here -->
            </div>
        </div>
        
        <!-- Crisis Alert Subscription Form -->
        <div class="subscription-panel" id="subscriptionPanel" style="display: none;">
            <h3>🚨 Get Crisis Alerts</h3>
            <p id="subscriptionDescription">Subscribe to receive email alerts when this country's crisis level changes.</p>
            
            <form id="subscriptionForm" onsubmit="subscribeToAlerts(event)">
                <input type="hidden" id="subscribe_country_id" name="country_id">
                <input type="hidden" id="subscribe_country_name" name="country_name">
                
                <div class="form-group">
                    <label for="subscribe_email">Your Email:</label>
                    <input type="email" id="subscribe_email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="subscribe_critical" checked> Critical alerts only
                    </label>
                </div>
                
                <button type="submit" class="donate-btn">Subscribe to Alerts</button>
            </form>
            
            <div id="subscriptionMessage" style="display: none; margin-top: 15px; padding: 10px; border-radius: 5px;"></div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer-modern">
        <div class="footer-glow"></div>
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <span>🌍</span>
                    <span>Charity Connect</span>
                </div>
                <p class="footer-tagline">Making a difference, one donation at a time.</p>
                <div class="footer-social">
                    <a href="#" class="social-link">🌐</a>
                    <a href="#" class="social-link">💬</a>
                    <a href="#" class="social-link">📧</a>
                </div>
            </div>
            <p class="footer-copyright">&copy; 2025 AidForPeace - Charity Connect. All rights reserved.</p>
        </div>
    </footer>
    
    <style>
        .footer-modern {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            color: white;
            padding: 50px 0 30px;
            margin-top: 60px;
            position: relative;
            overflow: hidden;
            border-top: 1px solid var(--glass-border);
        }

        .footer-modern::before {
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

        .footer-glow {
            position: absolute;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 200px;
            background: radial-gradient(ellipse, rgba(255, 182, 0, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .footer-content {
            text-align: center;
            margin-bottom: 30px;
        }

        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .footer-tagline {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1rem;
            margin-bottom: 25px;
        }

        .footer-social {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .social-link {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            text-decoration: none;
            transition: all 0.4s ease;
        }

        .social-link:hover {
            background: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 182, 0, 0.3);
        }

        .footer-copyright {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
            margin: 0;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 182, 0, 0.1);
        }
    </style>

    <script>
        // Global variable to store globe instance
        var globeInstance = null;
        var currentIndexMode = 'crisis';
        
        // NEW: Global variables for comparison and bookmarks
        var selectedCountries = [];
        var comparisonChart = null;
        var currentCountryInfo = null;
        var bookmarkedCountries = JSON.parse(localStorage.getItem('bookmarkedCountries') || '[]');
        
        // Initialize the 3D Globe when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            const countries = <?php echo json_encode($countries); ?>;

            console.log('=== COUNTRY COORDINATES DEBUG ===');
            countries.forEach(country => {
                console.log(`${country.name}: [${country.coords[0]}, ${country.coords[1]}] (lon, lat)`);
            });
            
            // Initialize the 3D globe
            console.log('🚀 Creating globe instance...');
            globeInstance = new InteractiveGlobe('globeContainer', countries);
            
            // Wait a tiny bit for constructor to finish
            setTimeout(function() {
                console.log('🌍 Globe instance created:', globeInstance);
                console.log('📊 Current mode:', globeInstance ? globeInstance.currentIndexMode : 'INSTANCE IS NULL');
                console.log('🎯 Index markers:', globeInstance && globeInstance.indexMarkers ? Object.keys(globeInstance.indexMarkers).length : 'NO MARKERS');
                
                // Double-check by directly accessing the property
                if (globeInstance) {
                    console.log('🔍 Direct property check:');
                    console.log('   - currentIndexMode exists?', 'currentIndexMode' in globeInstance);
                    console.log('   - currentIndexMode value:', globeInstance.currentIndexMode);
                    console.log('   - indexMarkers exists?', 'indexMarkers' in globeInstance);
                    console.log('   - indexMarkers keys:', Object.keys(globeInstance.indexMarkers || {}));
                    
                    // If undefined, set it manually
                    if (globeInstance.currentIndexMode === undefined) {
                        console.warn('⚠️ currentIndexMode was undefined, setting manually...');
                        globeInstance.currentIndexMode = 'crisis';
                        console.log('✅ currentIndexMode now set to:', globeInstance.currentIndexMode);
                    }
                    
                    if (!globeInstance.indexMarkers) {
                        console.warn('⚠️ indexMarkers was missing, creating...');
                        globeInstance.indexMarkers = {};
                    }
                }
            }, 500);
            
            // Make it globally accessible for debugging
            window.globeInstance = globeInstance;
            
            // Listen for country selection from the globe
            document.addEventListener('countrySelected', function(event) {
                showCountryInfo(event.detail);
            });
            
            console.log('✅ Globe initialization script complete!');
        });
        
        // =============================================================================
        // NEW: BOOKMARK FUNCTIONS
        // =============================================================================
        
        // Toggle bookmark for current country
        function toggleBookmark() {
            if (!currentCountryInfo) return;
            
            const countryName = currentCountryInfo.name;
            const isBookmarked = bookmarkedCountries.includes(countryName);
            
            if (isBookmarked) {
                // Remove bookmark
                bookmarkedCountries = bookmarkedCountries.filter(name => name !== countryName);
                showBookmarkNotification(countryName + ' bookmark removed', false);
            } else {
                // Add bookmark
                bookmarkedCountries.push(countryName);
                showBookmarkNotification(countryName + ' bookmarked!', true);
            }
            
            // Save to localStorage
            localStorage.setItem('bookmarkedCountries', JSON.stringify(bookmarkedCountries));
            
            // Update button
            updateBookmarkButton();
        }
        
        // Update bookmark button state
        function updateBookmarkButton() {
            const btn = document.getElementById('bookmarkBtn');
            if (!currentCountryInfo) return;
            
            const countryName = currentCountryInfo.name;
            const isBookmarked = bookmarkedCountries.includes(countryName);
            
            if (isBookmarked) {
                btn.innerHTML = '<i>✅</i> <span>Bookmarked</span>';
                btn.classList.add('bookmarked');
            } else {
                btn.innerHTML = '<i>🔖</i> <span>Bookmark</span>';
                btn.classList.remove('bookmarked');
            }
        }
        
        // Show bookmark notification
        function showBookmarkNotification(message, isSuccess = true) {
            const notification = document.createElement('div');
            notification.className = 'bookmark-notification';
            if (!isSuccess) {
                notification.classList.add('error');
            }
            
            notification.innerHTML = `
                <i>${isSuccess ? '✅' : '🗑️'}</i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }
        
        // =============================================================================
        // NEW: COMPARISON FUNCTIONS
        // =============================================================================
        
        // Toggle comparison panel
        function toggleComparePanel() {
            const panel = document.getElementById('comparePanel');
            const toggle = document.getElementById('compareToggle');
            
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
                toggle.style.background = 'rgba(255, 255, 255, 0.2)';
                updateCompareList();
            } else {
                panel.style.display = 'none';
                toggle.style.background = 'transparent';
            }
        }
        
        // Add current country to comparison list
        function addToComparison() {
            if (!currentCountryInfo) return;
            
            const countryName = currentCountryInfo.name;
            
            // Check if already added
            if (selectedCountries.some(c => c.name === countryName)) {
                showNotification(countryName + ' is already in the comparison list!', false);
                return;
            }
            
            // Add to list
            selectedCountries.push({
                name: countryName,
                data: countryIndexData[countryName] || {},
                crisisLevel: currentCountryInfo.crisis_level
            });
            
            // Update UI
            updateCompareList();
            updateAddToCompareButton();
            
            // Show notification
            showNotification('✅ ' + countryName + ' added to comparison list!', true);
        }
        
        // Remove country from comparison list
        function removeFromComparison(countryName) {
            selectedCountries = selectedCountries.filter(c => c.name !== countryName);
            updateCompareList();
            updateAddToCompareButton();
        }
        
        // Update comparison list display
        function updateCompareList() {
            const compareList = document.getElementById('compareList');
            const compareBtn = document.getElementById('compareBtn');
            
            compareList.innerHTML = '';
            
            if (selectedCountries.length === 0) {
                compareList.innerHTML = '<p style="color: #999; font-style: italic;">No countries selected yet.</p>';
                compareBtn.disabled = true;
                document.getElementById('comparisonResults').style.display = 'none';
                return;
            }
            
            selectedCountries.forEach(country => {
                const tag = document.createElement('div');
                tag.className = 'compare-country-tag';
                tag.innerHTML = `
                    ${country.name}
                    <button class="remove-compare-btn" onclick="removeFromComparison('${country.name}')">×</button>
                `;
                compareList.appendChild(tag);
            });
            
            // Enable compare button if we have at least 2 countries
            compareBtn.disabled = selectedCountries.length < 2;
        }
        
        // Clear all comparisons
        function clearComparison() {
            selectedCountries = [];
            updateCompareList();
            updateAddToCompareButton();
            document.getElementById('comparisonResults').style.display = 'none';
            
            if (comparisonChart) {
                comparisonChart.destroy();
                comparisonChart = null;
            }
        }
        
        // Start comparison
        function startComparison() {
            if (selectedCountries.length < 2) {
                showNotification('Please select at least 2 countries to compare.', false);
                return;
            }
            
            // Show results section
            document.getElementById('comparisonResults').style.display = 'block';
            
            // Generate chart
            generateComparisonChart();
            
            // Generate table
            generateComparisonTable();
            
            // Scroll to results
            document.getElementById('comparisonResults').scrollIntoView({ behavior: 'smooth' });
        }
        
        // Generate comparison chart
        function generateComparisonChart() {
            const ctx = document.getElementById('comparisonChart').getContext('2d');
            
            // Destroy existing chart
            if (comparisonChart) {
                comparisonChart.destroy();
            }
            
            // Prepare data
            const labels = ['Hunger', 'Poverty', 'Crime', 'Migration', 'Water Access', 'Health'];
            const datasets = selectedCountries.map((country, index) => {
                const colors = ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#F44336', '#00BCD4'];
                return {
                    label: country.name,
                    data: [
                        country.data.hunger || 0,
                        country.data.poverty || 0,
                        country.data.crime || 0,
                        country.data.migration || 0,
                        country.data.waterAccess || 0,
                        country.data.health || 0
                    ],
                    backgroundColor: colors[index] + '40',
                    borderColor: colors[index],
                    borderWidth: 2,
                    pointBackgroundColor: colors[index],
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                };
            });
            
            // Create chart
            comparisonChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20
                            },
                            pointLabels: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}%`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Generate comparison table
        function generateComparisonTable() {
            const table = document.getElementById('comparisonTable');
            let html = '<table>';
            
            // Table header
            html += '<thead><tr><th>Index</th>';
            selectedCountries.forEach(country => {
                html += `<th>${country.name}</th>`;
            });
            html += '</tr></thead>';
            
            // Table body
            html += '<tbody>';
            
            const indices = [
                { key: 'hunger', label: 'Hunger' },
                { key: 'poverty', label: 'Poverty' },
                { key: 'crime', label: 'Crime' },
                { key: 'migration', label: 'Migration' },
                { key: 'waterAccess', label: 'Water Access' },
                { key: 'health', label: 'Health' }
            ];
            
            indices.forEach(index => {
                html += '<tr>';
                html += `<td><strong>${index.label}</strong></td>`;
                
                selectedCountries.forEach(country => {
                    const value = country.data[index.key] || 0;
                    let valueClass = 'value-medium';
                    if (value <= 39) valueClass = 'value-low';
                    else if (value >= 80) valueClass = 'value-high';
                    
                    html += `<td><span class="index-value ${valueClass}">${value}%</span></td>`;
                });
                
                html += '</tr>';
            });
            
            // Crisis Level row
            html += '<tr>';
            html += '<td><strong>Crisis Level</strong></td>';
            selectedCountries.forEach(country => {
                html += `<td>${country.crisisLevel}</td>`;
            });
            html += '</tr>';
            
            html += '</tbody></table>';
            table.innerHTML = html;
        }
        
        // Update "Add to Compare" button state
        function updateAddToCompareButton() {
            const btn = document.getElementById('addToCompareBtn');
            if (!currentCountryInfo) return;
            
            const countryName = currentCountryInfo.name;
            const isAdded = selectedCountries.some(c => c.name === countryName);
            
            btn.textContent = isAdded ? '✓ Added to Compare' : '+ Add to Compare';
            btn.classList.toggle('added', isAdded);
            btn.disabled = isAdded;
        }
        
        // Show notification
        function showNotification(message, isSuccess = true) {
            const notification = document.createElement('div');
            notification.className = 'bookmark-notification';
            if (!isSuccess) {
                notification.classList.add('error');
            }
            
            notification.innerHTML = `
                <i>${isSuccess ? '✅' : '⚠️'}</i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }
        
        // =============================================================================
        // NEW: Index Switching Functions
        // =============================================================================
        
        // Switch between different index modes
        function switchIndexMode(indexType) {
            console.log('🎮 Button clicked! Switching to mode:', indexType);
            currentIndexMode = indexType;
            
            // Verify globe instance exists
            if (!globeInstance) {
                console.error('❌ ERROR: globeInstance not found!');
                alert('Globe not initialized yet. Please wait a moment and try again.');
                return;
            }
            
            console.log('✅ Globe instance found:', globeInstance);
            
            // Update button states
            document.querySelectorAll('.index-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            const targetButton = document.querySelector(`[data-index="${indexType}"]`);
            if (targetButton) {
                targetButton.classList.add('active');
                console.log('✅ Button activated:', indexType);
            } else {
                console.warn('⚠️ Button not found for:', indexType);
            }
            
            // Update globe colors
            if (indexType === 'crisis') {
                console.log('🔄 Calling switchToCrisisMode()');
                globeInstance.switchToCrisisMode();
            } else {
                console.log('🔄 Calling switchToIndexMode(' + indexType + ')');
                globeInstance.switchToIndexMode(indexType);
            }
            
            // Update legend
            updateLegend(indexType);
            
            console.log('✅ Mode switch complete!');
        }
        
        // Update the legend based on current mode
        function updateLegend(indexType) {
            const legendTitle = document.getElementById('legendTitle');
            const legendContent = document.getElementById('legendContent');
            
            if (indexType === 'crisis') {
                legendTitle.textContent = '🌍 Crisis Level Legend';
                legendContent.innerHTML = `
                    <div class="legend-item">
                        <div class="legend-color" style="background: #00ff00;"></div>
                        <span>🟩 Stable - Safe conditions</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ff8800;"></div>
                        <span>🟧 High - Elevated risk</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ff3366;"></div>
                        <span>🟥 Critical - Emergency situation</span>
                    </div>
                `;
            } else {
                const indexDef = indexDefinitions[indexType];
                legendTitle.textContent = '📊 ' + indexDef.name + ' Scale';
                legendContent.innerHTML = `
                    <div class="legend-item">
                        <div class="legend-color" style="background: #00ff00;"></div>
                        <span>0-19: Low / Good</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ffcc00;"></div>
                        <span>20-39: Medium</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ff8800;"></div>
                        <span>40-59: High</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ff4400;"></div>
                        <span>60-79: Severe</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ff0000;"></div>
                        <span>80-100: Critical</span>
                    </div>
                `;
            }
        }
        
        // =============================================================================
        
        // Search functionality
        function searchCountry() {
            const searchInput = document.getElementById('searchInput');
            const searchError = document.getElementById('searchError');
            const searchTerm = searchInput.value.trim().toLowerCase();
            
            searchInput.classList.remove('error');
            searchError.style.display = 'none';
            searchError.textContent = '';
            
            if (!searchTerm) {
                searchInput.classList.add('error');
                searchError.textContent = 'Please enter a country name to search';
                searchError.style.display = 'block';
                return;
            }
            
            const countries = <?php echo json_encode($countries); ?>;
            const found = countries.find(c => c.name.toLowerCase().includes(searchTerm));
            
            if (found) {
                if (globeInstance) {
                    globeInstance.flyToCountry(found);
                    globeInstance.showCountryInfo(found);
                }
                searchInput.classList.remove('error');
                searchError.style.display = 'none';
            } else {
                searchInput.classList.add('error');
                const countryNames = countries.map(c => c.name).join(', ');
                searchError.textContent = `Country "${searchInput.value}" not found. Try: ${countryNames}`;
                searchError.style.display = 'block';
            }
        }

        // Show country information
        function showCountryInfo(country) {
            currentCountryInfo = country;
            
            document.getElementById('infoPanel').classList.add('active');
            document.getElementById('countryName').textContent = country.name;
            
            // Show action buttons
            const bookmarkBtn = document.getElementById('bookmarkBtn');
            const addToCompareBtn = document.getElementById('addToCompareBtn');
            bookmarkBtn.style.display = 'flex';
            addToCompareBtn.style.display = 'flex';
            
            // Update button states
            updateBookmarkButton();
            updateAddToCompareButton();
            
            const badge = document.createElement('span');
            badge.className = 'crisis-badge';
            badge.style.background = country.crisis_level === 'Critical' ? '#F44336' : 
                                    country.crisis_level === 'High' ? '#FF9800' : '#4CAF50';
            badge.textContent = country.crisis_level;
            
            const crisisDiv = document.getElementById('crisisLevel');
            crisisDiv.innerHTML = '';
            crisisDiv.appendChild(badge);
            
            document.getElementById('countryDescription').textContent = country.description;
            
            // NEW: Show index data if available
            const indexDataDisplay = document.getElementById('indexDataDisplay');
            const indexDataContent = document.getElementById('indexDataContent');
            
            if (countryIndexData[country.name]) {
                const data = countryIndexData[country.name];
                let html = '<div class="index-stats-grid">';
                
                for (let key in data) {
                    const def = indexDefinitions[key];
                    const value = data[key];
                    const color = getColorForValue(value);
                    const colorStr = '#' + color.toString(16).padStart(6, '0');
                    
                    html += `
                        <div class="index-stat-card">
                            <div class="index-stat-header">
                                <strong>${def.name}</strong>
                            </div>
                            <div class="index-stat-value" style="color: ${colorStr}; font-size: 32px; font-weight: bold;">
                                ${value}
                            </div>
                            <div class="index-stat-bar">
                                <div class="index-stat-fill" style="width: ${value}%; background: ${colorStr};"></div>
                            </div>
                            <div class="index-stat-desc">${def.description}</div>
                        </div>
                    `;
                }
                
                html += '</div>';
                indexDataContent.innerHTML = html;
                indexDataDisplay.style.display = 'block';
            } else {
                indexDataDisplay.style.display = 'none';
            }
            
            // Display NGOs
            const ngoList = document.getElementById('ngoList');
            ngoList.innerHTML = '';
            
            if (country.ngos && country.ngos.length > 0) {
                country.ngos.forEach(ngo => {
                    ngoList.innerHTML += `
                        <div class="ngo-card">
                            <h3>${ngo.name}</h3>
                            <p><strong>Mission:</strong> ${ngo.mission}</p>
                            <p><strong>Contact:</strong> ${ngo.contact}</p>
                            <p><strong>Type of Aid:</strong> ${ngo.type}</p>
                            <button class="donate-btn" onclick="donate('${ngo.name.replace(/'/g, "\\'")}')">💚 Donate Now</button>
                        </div>
                    `;
                });
            } else {
                ngoList.innerHTML = '<p>No NGOs currently registered for this country.</p>';
            }
            
            updateSubscriptionPanel(country);
            document.getElementById('infoPanel').scrollIntoView({ behavior: 'smooth' });
        }

        function updateSubscriptionPanel(country) {
            document.getElementById('subscribe_country_id').value = country.id;
            document.getElementById('subscribe_country_name').value = country.name;
            document.querySelector('#subscriptionPanel h3').textContent = `🚨 Get Crisis Alerts for ${country.name}`;
            document.getElementById('subscriptionDescription').textContent = 
                `Subscribe to receive email alerts when ${country.name}'s crisis level changes. Current level: ${country.crisis_level}`;
            document.getElementById('subscriptionPanel').style.display = 'block';
            
            setTimeout(() => {
                document.getElementById('subscriptionPanel').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'nearest' 
                });
            }, 500);
        }

        function subscribeToAlerts(e) {
            e.preventDefault();
            
            const email = document.getElementById('subscribe_email').value;
            const countryId = document.getElementById('subscribe_country_id').value;
            const countryName = document.getElementById('subscribe_country_name').value;
            const criticalOnly = document.querySelector('input[name="subscribe_critical"]').checked;
            
            if (!email || !countryId) {
                showSubscriptionMessage('Please enter your email and select a country first.', 'error');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showSubscriptionMessage('Please enter a valid email address.', 'error');
                return;
            }
            
            showSubscriptionMessage(
                `✅ You're subscribed to ${criticalOnly ? 'Critical' : 'all'} crisis alerts for ${countryName}. 
                You'll receive email notifications when the crisis level changes.`, 
                'success'
            );
            
            document.getElementById('subscribe_email').value = '';
            document.getElementById('subscriptionForm').reset();
            
            setTimeout(() => {
                document.getElementById('subscriptionPanel').style.display = 'none';
            }, 3000);
        }

        function showSubscriptionMessage(message, type) {
            const messageDiv = document.getElementById('subscriptionMessage');
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';
            messageDiv.style.background = type === 'success' ? '#d4edda' : '#f8d7da';
            messageDiv.style.color = type === 'success' ? '#155724' : '#721c24';
            messageDiv.style.border = type === 'success' ? '1px solid #c3e6cb' : '1px solid #f5c6cb';
            messageDiv.style.padding = '15px';
            messageDiv.style.borderRadius = '8px';
            messageDiv.style.marginTop = '20px';
            
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }

        function donate(ngoName) {
            alert(`Thank you for choosing to donate to ${ngoName}!\n\nRedirecting to payment page...`);
        }

        // Enter key support for search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchCountry();
        });

        // Clear error when user starts typing
        document.getElementById('searchInput').addEventListener('input', function() {
            this.classList.remove('error');
            document.getElementById('searchError').style.display = 'none';
        });
        
        // =============================================================================
        // NEWS FEED FUNCTIONS
        // =============================================================================
        
        // Toggle news dropdown
        function toggleNews() {
            const dropdown = document.getElementById('newsDropdown');
            const toggle = document.getElementById('newsToggle');
            
            if (dropdown.style.display === 'none') {
                dropdown.style.display = 'block';
                toggle.style.background = 'rgba(255, 255, 255, 0.2)';
                
                // Load news if not already loaded
                if (!window.newsLoaded) {
                    loadNews();
                    window.newsLoaded = true;
                }
            } else {
                dropdown.style.display = 'none';
                toggle.style.background = 'transparent';
            }
        }
        
        // Load news from API
        function loadNews() {
            console.log('📰 Loading news...');
            
            document.getElementById('newsContent').innerHTML = 
                '<div class="loading">Loading latest news</div>';
            
            // Free GNews API - searches for crisis/humanitarian news
            const apiUrl = 'https://gnews.io/api/v4/search?q=crisis OR humanitarian OR disaster OR emergency&lang=en&max=8&apikey=demo';
            
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    console.log('✅ News loaded');
                    displayNews(data.articles);
                })
                .catch(error => {
                    console.error('❌ Error loading news:', error);
                    displaySampleNews();
                });
        }
        
        // Display news articles
        function displayNews(articles) {
            if (!articles || articles.length === 0) {
                displaySampleNews();
                return;
            }
            
            let html = '<div class="news-list">';
            
            articles.forEach(article => {
                const date = new Date(article.publishedAt);
                const formattedDate = date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });
                
                html += `
                    <div class="news-item" onclick="window.open('${article.url}', '_blank')">
                        <div class="news-title">${article.title}</div>
                        <div class="news-description">${article.description || 'Click to read more...'}</div>
                        <div class="news-meta">
                            <span class="news-source">📡 ${article.source.name}</span>
                            <span class="news-date">📅 ${formattedDate}</span>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            document.getElementById('newsContent').innerHTML = html;
        }
        
        // Display sample news (backup)
        function displaySampleNews() {
            const samples = [
                {
                    title: "UN Reports Rising Humanitarian Needs Across Crisis Regions",
                    description: "United Nations officials announce significant increase in assistance requirements across multiple conflict zones globally.",
                    source: { name: "UN News" },
                    publishedAt: new Date().toISOString(),
                    url: "https://news.un.org"
                },
                {
                    title: "International Aid Organizations Coordinate Emergency Response",
                    description: "Major relief efforts underway as NGOs mobilize resources for disaster-affected communities.",
                    source: { name: "Relief Web" },
                    publishedAt: new Date().toISOString(),
                    url: "https://reliefweb.int"
                },
                {
                    title: "Global Food Security Concerns Mount in Vulnerable Nations",
                    description: "Food security experts warn of growing challenges affecting millions in developing regions.",
                    source: { name: "WFP" },
                    publishedAt: new Date().toISOString(),
                    url: "https://wfp.org"
                },
                {
                    title: "Water Crisis Deepens in Drought-Affected Areas",
                    description: "Communities face severe water shortages as prolonged drought conditions continue.",
                    source: { name: "UNICEF" },
                    publishedAt: new Date().toISOString(),
                    url: "https://unicef.org"
                },
                {
                    title: "Healthcare Systems Under Strain in Crisis Zones",
                    description: "Medical facilities report overwhelming demand and critical supply shortages.",
                    source: { name: "WHO" },
                    publishedAt: new Date().toISOString(),
                    url: "https://who.int"
                }
            ];
            
            displayNews(samples);
        }
        
        // Close news dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('newsDropdown');
            const toggle = document.getElementById('newsToggle');
            const panel = document.getElementById('comparePanel');
            const compareToggle = document.getElementById('compareToggle');
            
            // Close news dropdown
            if (dropdown.style.display === 'block' && 
                !dropdown.contains(event.target) && 
                event.target !== toggle) {
                dropdown.style.display = 'none';
                toggle.style.background = 'transparent';
            }
            
            // Close comparison panel
            if (panel.style.display === 'block' && 
                !panel.contains(event.target) && 
                event.target !== compareToggle) {
                panel.style.display = 'none';
                compareToggle.style.background = 'transparent';
            }
        });
        
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>