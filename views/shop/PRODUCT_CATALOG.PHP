<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique - ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/global-theme.css">
    <link rel="stylesheet" href="assets/css/3d-effects.css">
    <style>
        /* Page-specific overrides */
        .cart-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white !important;
            padding: 14px 28px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 800;
            border: none;
            box-shadow: 0 8px 25px rgba(255,107,53,0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }
        .cart-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            border-radius: 50px;
            transition: left 0.5s;
        }
        .cart-btn:hover { 
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 35px rgba(255,107,53,0.5);
        }
        .cart-btn:hover::before {
            left: 100%;
        }
        .cart-btn i {
            color: white;
        }
        .cart-count {
            background: linear-gradient(135deg, var(--danger) 0%, #d50000 100%);
            color: white;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(255,23,68,0.5);
            min-width: 24px;
            text-align: center;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        /* Header */
        .shop-header {
            text-align: center;
            margin-bottom: 40px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            padding: 50px 30px;
            border-radius: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(26,26,46,0.3);
        }
        .shop-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,107,53,0.15) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        .shop-header::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(0,217,255,0.1) 0%, transparent 70%);
            animation: float 8s ease-in-out infinite reverse;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        .shop-header h1 {
            font-size: 3rem;
            color: white;
            margin-bottom: 15px;
            font-weight: 900;
            position: relative;
            z-index: 1;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .shop-header h1 i { 
            color: var(--primary);
            margin-right: 15px;
            filter: drop-shadow(0 4px 15px rgba(255,107,53,0.5));
        }
        .shop-header p {
            color: rgba(255,255,255,0.85);
            font-size: 1.15rem;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        /* Product Card */
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            position: relative;
            transform-style: preserve-3d;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--success));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 10;
        }
        
        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 
                0 25px 60px rgba(0,0,0,0.15),
                0 0 0 1px rgba(255,107,53,0.1);
        }
        
        .product-card:hover::before {
            opacity: 1;
        }
        
        .product-image {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }
        .stock-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 6px 14px;
            border-radius: 25px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            backdrop-filter: blur(10px);
            z-index: 5;
        }
        .stock-badge.in-stock {
            background: rgba(0,200,83,0.9);
            color: white;
            box-shadow: 0 4px 15px rgba(0,200,83,0.4);
        }
        .stock-badge.low-stock {
            background: rgba(255,193,7,0.95);
            color: #1a1a2e;
            box-shadow: 0 4px 15px rgba(255,193,7,0.4);
        }
        .stock-badge.out-stock {
            background: rgba(255,23,68,0.9);
            color: white;
            box-shadow: 0 4px 15px rgba(255,23,68,0.4);
        }
        .product-info {
            padding: 22px;
        }
        .product-info h3 {
            font-size: 1.1rem;
            color: var(--secondary);
            margin-bottom: 10px;
            font-weight: 800;
            line-height: 1.3;
        }
        .product-info .description {
            color: var(--text-light);
            font-size: 0.85rem;
            margin-bottom: 18px;
            line-height: 1.6;
            height: 42px;
            overflow: hidden;
        }
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .product-footer .price {
            font-size: 1.4rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ========== BOUTONS ACTION ========== */
        .product-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Bouton Ajouter au Panier */
        .add-to-cart-btn {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            box-shadow: 0 8px 25px rgba(255,107,53,0.35);
            position: relative;
            overflow: hidden;
        }
        .add-to-cart-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        .add-to-cart-btn:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 15px 35px rgba(255,107,53,0.45);
        }
        .add-to-cart-btn:hover::before {
            left: 100%;
        }
        .add-to-cart-btn:active {
            transform: translateY(-2px) scale(1.02);
        }
        .add-to-cart-btn:disabled {
            background: linear-gradient(135deg, #94a3b8 0%, #cbd5e1 100%);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Bouton Laisser un Avis */
        .review-btn {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 800;
            box-shadow: 0 8px 25px rgba(0,217,255,0.35);
            position: relative;
            overflow: hidden;
        }
        .review-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        .review-btn:hover {
            transform: translateY(-4px) scale(1.05);
            color: white;
            box-shadow: 0 15px 35px rgba(0,217,255,0.45);
        }
        .review-btn:hover::before {
            left: 100%;
        }
        .review-btn:active {
            transform: translateY(-2px) scale(1.02);
        }

        /* Cart Sidebar */
        .cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        .cart-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 30px rgba(0,0,0,0.2);
            z-index: 999;
            display: flex;
            flex-direction: column;
            transition: right 0.3s ease;
        }
        .cart-sidebar.active {
            right: 0;
        }
        .cart-header {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: white;
            padding: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            border-bottom: 3px solid var(--primary);
        }
        .cart-header h3 { 
            font-size: 1.25rem;
            font-weight: 800;
        }
        .cart-header h3 i { 
            color: var(--primary); 
            margin-right: 12px;
            filter: drop-shadow(0 2px 8px rgba(255,107,53,0.5));
        }
        .close-cart {
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        .close-cart:hover { 
            background: var(--primary);
            transform: rotate(90deg);
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .cart-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .cart-item-info { flex: 1; }
        .cart-item-info h4 {
            font-size: 0.9rem;
            color: var(--secondary);
            margin-bottom: 5px;
        }
        .cart-item-info .price {
            color: var(--primary);
            font-weight: 700;
        }
        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }
        .cart-item-quantity button {
            width: 25px;
            height: 25px;
            border: 2px solid var(--secondary);
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 700;
        }
        .cart-item-quantity button:hover {
            background: var(--secondary);
            color: white;
        }
        .remove-item {
            background: none;
            border: none;
            color: var(--danger);
            font-size: 1.2rem;
            cursor: pointer;
        }
        .cart-empty {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .cart-empty i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
        .cart-footer {
            padding: 20px;
            border-top: 2px solid #eee;
            background: #f8f9fa;
        }
        .cart-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--secondary);
        }
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            text-align: center;
            font-weight: 800;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            font-size: 1rem;
            box-shadow: 0 8px 25px rgba(255,107,53,0.35);
            position: relative;
            overflow: hidden;
        }
        .checkout-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255,107,53,0.45);
            color: white;
        }
        .checkout-btn:hover::before {
            left: 100%;
        }

        /* No Products */
        .no-products {
            text-align: center;
            padding: 60px;
            color: var(--text-dark);
            grid-column: 1/-1;
        }
        .no-products i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
            display: block;
        }
        .no-products h3 {
            color: var(--secondary);
            margin-bottom: 10px;
        }

        /* Shop Container with Sidebar */
        .shop-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        /* Filters Sidebar */
        .filters-sidebar {
            background: white;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            height: fit-content;
            position: sticky;
            top: 100px;
            border-top: 4px solid var(--primary);
        }

        .filter-title {
            font-size: 1.3rem;
            color: var(--secondary);
            margin-bottom: 25px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-title i {
            color: var(--primary);
            font-size: 1.4rem;
            filter: drop-shadow(0 2px 8px rgba(255,107,53,0.4));
        }

        .filter-group {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ebebeb;
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .filter-subtitle {
            font-size: 0.95rem;
            color: var(--secondary);
            margin-bottom: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .filter-option:hover {
            background: rgba(255,182,0,0.05);
        }

        .filter-option input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .filter-option span {
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.9rem;
        }

        /* Price Range */
        .price-range-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }

        .price-slider {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: #ebebeb;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .price-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255,107,53,0.4);
            border: 3px solid white;
        }

        .price-slider::-moz-range-thumb {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 4px 15px rgba(255,107,53,0.4);
        }

        .price-display {
            text-align: center;
            color: var(--secondary);
            font-weight: 700;
            font-size: 0.95rem;
        }

        .btn-reset-filters {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-reset-filters:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255,107,53,0.35);
        }

        /* Products Section */
        .products-section {
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 768px) {
            .navbar { padding: 15px; }
            .navbar .logo a { font-size: 1.4rem; }
            .cart-sidebar { width: 100%; right: -100%; }
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            .shop-container {
                grid-template-columns: 1fr;
            }
            .filters-sidebar {
                position: static;
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="index.php"><span class="impact">Impact</span><span class="shop">Shop</span></a>
        </div>
        <div class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
            <a href="index.php?controller=home&action=donation"><i class="fas fa-heart"></i> Faire un don</a>
            <a href="index.php?controller=loyalty&action=index"><i class="fas fa-gift"></i> Fidélité</a>
            <a href="index.php?controller=shipping&action=zones"><i class="fas fa-map-marked-alt"></i> Zones</a>
            <a href="index.php?controller=shipping&action=track"><i class="fas fa-truck"></i> Suivi</a>
            <a href="#" class="cart-btn" onclick="toggleCart()">
                <i class="fas fa-shopping-cart"></i>
                <span>Panier</span>
                <span class="cart-count" id="cartCount">0</span>
            </a>
        </div>
    </nav>

    <!-- Cart Overlay -->
    <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3><i class="fas fa-shopping-cart"></i> Mon Panier</h3>
            <button class="close-cart" onclick="toggleCart()"><i class="fas fa-times"></i></button>
        </div>
        <div class="cart-items" id="cartItems">
            <div class="cart-empty">
                <i class="fas fa-shopping-cart"></i>
                <p>Votre panier est vide</p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotal">0.00 TND</span>
            </div>
            <a href="index.php?controller=order&action=checkout" class="checkout-btn" id="checkoutBtn">
                <i class="fas fa-credit-card"></i> Passer la commande
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        
        <!-- Header -->
        <div class="shop-header">
            <h1><i class="fas fa-store"></i> Notre Boutique</h1>
            <p>Decouvrez nos produits solidaires et soutenez des causes humanitaires</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="shop-container">
            <!-- Sidebar Filters -->
            <aside class="filters-sidebar">
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-filter"></i> Filtres
                    </h3>
                    
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <h4 class="filter-subtitle">Catégories</h4>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" class="category-filter" value="" checked>
                                <span>Tous les produits</span>
                            </label>
                            <?php 
                            // Get unique categories from products
                            $categories = [];
                            foreach ($products as $product) {
                                if (!empty($product['category_id'])) {
                                    $categories[$product['category_id']] = $product['category_id'];
                                }
                            }
                            
                            // Get category names from database
                            if (!empty($categories)) {
                                require_once __DIR__ . '/../../models/Category.php';
                                foreach (array_keys($categories) as $catId) {
                                    $cat = Category::findById($catId);
                                    if ($cat) {
                                        echo '<label class="filter-option">';
                                        echo '<input type="checkbox" class="category-filter" value="' . $catId . '">';
                                        echo '<span>' . htmlspecialchars($cat->getNameFr()) . '</span>';
                                        echo '</label>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Price Filter -->
                    <div class="filter-group">
                        <h4 class="filter-subtitle">Prix</h4>
                        <div class="price-range-container">
                            <input type="range" id="priceMin" class="price-slider" min="0" max="500" value="0">
                            <input type="range" id="priceMax" class="price-slider" min="0" max="500" value="500">
                        </div>
                        <div class="price-display">
                            <span id="priceMinDisplay">0</span> TND - 
                            <span id="priceMaxDisplay">500</span> TND
                        </div>
                    </div>
                    
                    <!-- Stock Filter -->
                    <div class="filter-group">
                        <h4 class="filter-subtitle">Disponibilité</h4>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" class="stock-filter" value="all" checked>
                                <span>Tous</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" class="stock-filter" value="in-stock">
                                <span>En stock</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" class="stock-filter" value="low-stock">
                                <span>Stock faible</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Reset Button -->
                    <button id="reset-filters" class="btn-reset-filters">
                        <i class="fas fa-redo"></i> Réinitialiser les filtres
                    </button>
                </div>
            </aside>

            <!-- Products Section -->
            <main class="products-section">
                <!-- Products Grid -->
                <div class="products-grid">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h3>Aucun produit disponible</h3>
                    <p>Revenez bientot pour decouvrir nos nouveaux produits!</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-price="<?php echo $product['price']; ?>" data-stock="<?php echo $product['stock'] ?? 0; ?>" data-category="<?php echo $product['category_id'] ?? ''; ?>">
                        <div class="product-image">
                            <img src="assets/images/<?php echo htmlspecialchars($product['img_name'] ?? 'placeholder.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name_fr']); ?>"
                                 onerror="this.src='https://via.placeholder.com/300x200/1e3149/ffb600?text=<?php echo urlencode($product['name_fr']); ?>'">
                            
                            <?php 
                            $stock = intval($product['stock'] ?? 0);
                            if ($stock > 10): ?>
                                <span class="stock-badge in-stock">En Stock</span>
                            <?php elseif ($stock > 0): ?>
                                <span class="stock-badge low-stock">Stock Faible (<?php echo $stock; ?>)</span>
                            <?php else: ?>
                                <span class="stock-badge out-stock">Rupture</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name_fr']); ?></h3>
                            
                            <!-- Average Rating -->
                            <?php 
                                require_once __DIR__ . '/../../models/PRODUCT.PHP';
                                $rating = Product::getAverageRating($product['id']);
                            ?>
                            <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                <div class="stars-rating" style="display: flex; gap: 2px;">
                                    <?php 
                                        $fullStars = floor($rating['average']);
                                        $hasHalfStar = ($rating['average'] - $fullStars) >= 0.5;
                                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                        
                                        for ($i = 0; $i < $fullStars; $i++) {
                                            echo '<i class="fas fa-star" style="color: #FFA500; font-size: 0.9rem;"></i>';
                                        }
                                        if ($hasHalfStar) {
                                            echo '<i class="fas fa-star-half-alt" style="color: #FFA500; font-size: 0.9rem;"></i>';
                                        }
                                        for ($i = 0; $i < $emptyStars; $i++) {
                                            echo '<i class="far fa-star" style="color: #FFA500; font-size: 0.9rem;"></i>';
                                        }
                                    ?>
                                </div>
                                <span style="font-size: 0.85rem; color: var(--text-dark); font-weight: 600;">
                                    <?php echo $rating['average']; ?>/5 (<?php echo $rating['total']; ?> avis)
                                </span>
                            </div>
                            
                            <p class="description"><?php echo htmlspecialchars($product['description_fr'] ?? ''); ?></p>
                            
                            <div class="product-footer">
                                <span class="price"><?php echo number_format($product['price'], 2); ?> TND</span>
                                
                                <!-- ========== ICI: LES BOUTONS D'ACTION ========== -->
                                <div class="product-actions">
                                    
                                    <!-- Bouton Laisser un Avis -->
                                    <a href="index.php?controller=review&action=create&product_id=<?php echo $product['id']; ?>" 
                                       class="review-btn" 
                                       title="Laisser un avis">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    
                                    <!-- Bouton Ajouter au Panier -->
                                    <?php if ($stock > 0): ?>
                                        <button class="add-to-cart-btn" 
                                                onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name_fr'])); ?>', <?php echo $product['price']; ?>, '<?php echo htmlspecialchars($product['img_name'] ?? 'placeholder.jpg'); ?>')"
                                                title="Ajouter au panier">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="add-to-cart-btn" disabled title="Produit indisponible">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                </div>
                                <!-- ========== FIN BOUTONS D'ACTION ========== -->
                                
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        let cart = [];

        // Charger le panier depuis localStorage
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            updateCartUI();
        });

        function loadCart() {
            const savedCart = localStorage.getItem('impactshop_cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
            }
        }

        function saveCart() {
            localStorage.setItem('impactshop_cart', JSON.stringify(cart));
        }

        function addToCart(id, name, price, image) {
            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ id, name, price, image, quantity: 1 });
            }
            saveCart();
            updateCartUI();
            toggleCart();
            showNotification('Produit ajoute au panier!');
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            saveCart();
            updateCartUI();
        }

        function updateQuantity(id, change) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(id);
                } else {
                    saveCart();
                    updateCartUI();
                }
            }
        }

        function updateCartUI() {
            const cartCount = document.getElementById('cartCount');
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');
            
            // Update count
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = totalItems;
            
            // Update items
            if (cart.length === 0) {
                cartItems.innerHTML = '<div class="cart-empty"><i class="fas fa-shopping-cart"></i><p>Votre panier est vide</p></div>';
            } else {
                cartItems.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <img src="assets/images/${item.image}" alt="${item.name}" onerror="this.src='https://via.placeholder.com/60x60?text=P'">
                        <div class="cart-item-info">
                            <h4>${item.name}</h4>
                            <p class="price">${item.price.toFixed(2)} TND</p>
                            <div class="cart-item-quantity">
                                <button onclick="updateQuantity(${item.id}, -1)">-</button>
                                <span>${item.quantity}</span>
                                <button onclick="updateQuantity(${item.id}, 1)">+</button>
                            </div>
                        </div>
                        <button class="remove-item" onclick="removeFromCart(${item.id})"><i class="fas fa-trash"></i></button>
                    </div>
                `).join('');
            }
            
            // Update total
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            cartTotal.textContent = total.toFixed(2) + ' TND';
        }

        function toggleCart() {
            document.getElementById('cartSidebar').classList.toggle('active');
            document.getElementById('cartOverlay').classList.toggle('active');
        }

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = 'position:fixed;top:100px;right:20px;background:#27ae60;color:white;padding:15px 25px;border-radius:8px;font-weight:600;z-index:2000;animation:slideIn 0.3s ease';
            notification.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        // ========== FILTER FUNCTIONALITY ==========
        const allProducts = document.querySelectorAll('.product-card');
        const categoryFilters = document.querySelectorAll('.category-filter');
        const priceMinInput = document.getElementById('priceMin');
        const priceMaxInput = document.getElementById('priceMax');
        const priceMinDisplay = document.getElementById('priceMinDisplay');
        const priceMaxDisplay = document.getElementById('priceMaxDisplay');
        const stockFilters = document.querySelectorAll('.stock-filter');
        const resetBtn = document.getElementById('reset-filters');

        // Update price display
        priceMinInput.addEventListener('input', function() {
            if (parseInt(this.value) > parseInt(priceMaxInput.value)) {
                this.value = priceMaxInput.value;
            }
            priceMinDisplay.textContent = this.value;
            filterProducts();
        });

        priceMaxInput.addEventListener('input', function() {
            if (parseInt(this.value) < parseInt(priceMinInput.value)) {
                this.value = priceMinInput.value;
            }
            priceMaxDisplay.textContent = this.value;
            filterProducts();
        });

        // Category filter
        categoryFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                if (this.value === '') {
                    // If "All products" is checked, uncheck others
                    if (this.checked) {
                        categoryFilters.forEach(f => {
                            if (f.value !== '') f.checked = false;
                        });
                    }
                } else {
                    // If any category is checked, uncheck "All products"
                    categoryFilters.forEach(f => {
                        if (f.value === '') f.checked = false;
                    });
                }
                filterProducts();
            });
        });

        // Stock filter
        stockFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                if (this.value === 'all') {
                    if (this.checked) {
                        stockFilters.forEach(f => {
                            if (f.value !== 'all') f.checked = false;
                        });
                    }
                } else {
                    stockFilters.forEach(f => {
                        if (f.value === 'all') f.checked = false;
                    });
                }
                filterProducts();
            });
        });

        // Reset filters
        resetBtn.addEventListener('click', function() {
            categoryFilters.forEach(f => f.checked = f.value === '');
            stockFilters.forEach(f => f.checked = f.value === 'all');
            priceMinInput.value = 0;
            priceMaxInput.value = 500;
            priceMinDisplay.textContent = 0;
            priceMaxDisplay.textContent = 500;
            filterProducts();
        });

        // Main filter function
        function filterProducts() {
            const minPrice = parseInt(priceMinInput.value);
            const maxPrice = parseInt(priceMaxInput.value);
            
            // Get selected categories
            const selectedCategories = Array.from(categoryFilters)
                .filter(f => f.checked && f.value !== '')
                .map(f => f.value);
            
            // Get selected stock filters
            const selectedStockFilters = Array.from(stockFilters)
                .filter(f => f.checked && f.value !== 'all')
                .map(f => f.value);
            
            const showAll = selectedStockFilters.length === 0;

            let visibleCount = 0;

            allProducts.forEach(card => {
                let show = true;

                // Price filter
                const price = parseFloat(card.getAttribute('data-price'));
                if (price < minPrice || price > maxPrice) {
                    show = false;
                }

                // Category filter
                if (show && selectedCategories.length > 0) {
                    const category = card.getAttribute('data-category');
                    if (!selectedCategories.includes(category)) {
                        show = false;
                    }
                }

                // Stock filter
                if (show && !showAll) {
                    const stock = parseInt(card.getAttribute('data-stock'));
                    let stockMatch = false;
                    
                    if (selectedStockFilters.includes('in-stock') && stock > 10) {
                        stockMatch = true;
                    }
                    if (selectedStockFilters.includes('low-stock') && stock > 0 && stock <= 10) {
                        stockMatch = true;
                    }
                    
                    if (!stockMatch) {
                        show = false;
                    }
                }

                // Show or hide product
                if (show) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show "no products" message if needed
            const productsGrid = document.querySelector('.products-grid');
            let noProductsMsg = productsGrid.querySelector('.no-products-filtered');
            
            if (visibleCount === 0) {
                if (!noProductsMsg) {
                    noProductsMsg = document.createElement('div');
                    noProductsMsg.className = 'no-products-filtered';
                    noProductsMsg.style.cssText = 'grid-column: 1/-1; text-align: center; padding: 60px; color: #999;';
                    noProductsMsg.innerHTML = '<i class="fas fa-search" style="font-size: 3rem; margin-bottom: 20px; display: block;"></i><h3>Aucun produit ne correspond à vos filtres</h3><p>Essayez de modifier vos critères de recherche</p>';
                    productsGrid.appendChild(noProductsMsg);
                }
            } else if (noProductsMsg) {
                noProductsMsg.remove();
            }
        }
    </script>
    
    <!-- 3D Effects Script -->
    <script src="assets/js/3d-effects.js"></script>

    <!-- Chatbot Widget -->
    <?php include __DIR__ . '/../partials/chatbot.php'; ?>

</body>
</html>