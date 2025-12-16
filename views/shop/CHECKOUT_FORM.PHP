<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - ImpactShop</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #ff6b35;
            --primary-dark: #e55a2b;
            --primary-light: #ff8c5a;
            --secondary: #1a1a2e;
            --secondary-light: #16213e;
            --accent: #00d9ff;
            --accent-dark: #00b8d9;
            --success: #00c853;
            --warning: #ffc107;
            --danger: #ff1744;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --heading-color: #1a1a2e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--text-dark);
            line-height: 1.8;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        }
        
        /* Top Bar */
        .top-bar {
            background: var(--secondary);
            color: white;
            padding: 10px 0;
            font-size: 13px;
        }
        
        .top-bar a {
            color: white;
            text-decoration: none;
        }
        
        .top-bar i {
            color: var(--primary);
            margin-right: 5px;
        }
        
        /* Header */
        .site-header {
            background: white;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
            padding: 20px 0;
        }
        
        .site-logo h1 {
            color: var(--secondary);
            font-weight: 800;
            font-size: 2rem;
            margin: 0;
        }
        
        .site-logo span {
            color: var(--primary);
        }
        
        /* Navigation */
        .main-nav {
            background: var(--secondary);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .main-nav .navbar {
            padding: 0;
        }
        
        .main-nav .nav-link {
            color: white !important;
            padding: 18px 25px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            transition: all 0.3s;
        }
        
        .main-nav .nav-link:hover {
            background: var(--primary);
            color: var(--secondary) !important;
        }
        
        /* Banner */
        .banner-area {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            padding: 80px 0;
            text-align: center;
            color: white;
        }
        
        .banner-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        
        .breadcrumb {
            background: rgba(255,255,255,0.1);
            border-radius: 50px;
            padding: 10px 25px;
            display: inline-flex;
            margin: 0;
        }
        
        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: white;
        }
        
        /* Main Section */
        .main-section {
            padding: 80px 0;
        }
        
        /* Cart Items */
        .cart-section {
            background: white;
            padding: 30px;
            border-radius: 3px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .cart-section h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            border: 1px solid #ebebeb;
            border-radius: 3px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }
        
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 3px;
            border: 2px solid var(--primary);
        }
        
        .cart-item-info {
            flex-grow: 1;
        }
        
        .cart-item-name {
            font-weight: 700;
            color: var(--heading-color);
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 1rem;
        }
        
        .cart-item-total {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }
        
        /* Form */
        .checkout-form {
            background: white;
            padding: 40px;
            border-radius: 3px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .form-section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 25px;
            text-transform: uppercase;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--heading-color);
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 1px solid #ebebeb;
            border-radius: 3px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 182, 0, 0.1);
        }
        
        /* Summary */
        .order-summary {
            background: white;
            padding: 30px;
            border-radius: 3px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 20px;
        }
        
        .summary-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #ebebeb;
            font-size: 15px;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 2rem;
            font-weight: 800;
            color: var(--heading-color);
            padding-top: 20px;
            margin-top: 20px;
            border-top: 3px solid var(--secondary);
        }
        
        .summary-total span:last-child {
            color: var(--primary);
        }
        
        /* Payment */
        .payment-section {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 3px;
            margin: 30px 0;
            border: 1px solid #ebebeb;
        }
        
        .payment-option {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: white;
            border-radius: 3px;
            border: 2px solid #0070ba;
        }
        
        .paypal-logo {
            font-size: 2rem;
            color: #0070ba;
            font-weight: 700;
        }
        
        /* Buttons */
        .btn-checkout {
            width: 100%;
            background: var(--primary);
            color: var(--secondary);
            border: 2px solid var(--primary);
            padding: 18px;
            font-size: 1.2rem;
            font-weight: 800;
            text-transform: uppercase;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-checkout:hover {
            background: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }
        
        .btn-back {
            width: 100%;
            background: white;
            color: var(--secondary);
            border: 2px solid var(--secondary);
            padding: 15px;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 3px;
            margin-top: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: var(--secondary);
            color: white;
        }
        
        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 3px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .empty-cart i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-cart h3 {
            color: #999;
            margin-bottom: 10px;
        }
        
        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 0 20px;
            margin-top: 80px;
        }
        
        .copyright {
            background: #111;
            padding: 20px 0;
            text-align: center;
            color: #999;
            margin-top: 30px;
        }
        
        /* Alerts */
        .alert {
            border-radius: 3px;
            padding: 15px 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .banner-title {
                font-size: 2rem;
            }
            
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            
            .order-summary {
                position: static;
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <i class="fas fa-phone"></i> +216 XX XXX XXX
                    <span style="margin-left: 20px;"><i class="fas fa-envelope"></i> contact@impactshop.tn</span>
                </div>
                <div class="col-md-6 text-right">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="margin-left: 15px;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="margin-left: 15px;"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="site-logo">
                        <a href="index.php" style="text-decoration: none;">
                            <h1><span>Impact</span>Shop</h1>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <div class="main-nav">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home"></i> Accueil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=home&action=donation">
                                <i class="fas fa-heart"></i> Faire un don
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=product&action=shop">
                                <i class="fas fa-store"></i> Boutique
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=dashboard&action=index">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <!-- Banner -->
    <section class="banner-area">
        <div class="container">
            <h1 class="banner-title">Finaliser la Commande</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="index.php?controller=product&action=shop">Boutique</a></li>
                    <li class="breadcrumb-item active">Paiement</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Main Content -->
    <section class="main-section">
        <div class="container">

            <!-- Alerts -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Erreur!</strong> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</h5>
                    <ul style="margin: 10px 0 0 20px;">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <!-- Loading Indicator -->
            <div id="loading-indicator" style="text-align: center; padding: 60px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: var(--primary);"></i>
                <p style="margin-top: 20px; color: var(--text-dark);">Chargement de votre panier...</p>
            </div>

            <!-- Main Content (Hidden initially) -->
            <div id="checkout-content" style="display: none;">
                <div class="row">
                    
                    <!-- Left Column - Cart & Form -->
                    <div class="col-lg-8">
                        
                        <!-- Cart Items -->
                        <div class="cart-section">
                            <h3>
                                <i class="fas fa-shopping-cart"></i> Votre Panier (<span id="cart-count">0</span> article(s))
                            </h3>
                            <div id="checkout-items-display"></div>
                        </div>

                        <!-- Customer Form -->
                        <div class="checkout-form">
                            <h3 class="form-section-title">
                                <i class="fas fa-user"></i> Vos Informations
                            </h3>
                            
                            <form action="index.php?controller=order&action=create" method="post" id="checkout-form">
                                <input type="hidden" name="cart_data" id="cart-data-input">
                                <input type="hidden" name="total_amount" id="total-amount-input">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Prénom *</label>
                                            <input type="text" 
                                                   name="first_name" 
                                                   class="form-control" 
                                                   placeholder="Votre prénom"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nom *</label>
                                            <input type="text" 
                                                   name="last_name" 
                                                   class="form-control" 
                                                   placeholder="Votre nom"
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control" 
                                           placeholder="votre@email.com"
                                           required>
                                </div>

                                <div class="form-group">
                                    <label>Téléphone *</label>
                                    <input type="tel" 
                                           name="phone" 
                                           class="form-control" 
                                           placeholder="+216 XX XXX XXX"
                                           required>
                                </div>

                                <!-- Delivery Address -->
                                <h3 class="form-section-title" style="margin-top: 40px;">
                                    <i class="fas fa-truck"></i> Adresse de Livraison
                                </h3>

                                <div class="form-group">
                                    <label>Adresse *</label>
                                    <input type="text" 
                                           name="address" 
                                           class="form-control" 
                                           placeholder="Rue, numéro, appartement..."
                                           required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Ville *</label>
                                            <select name="city" class="form-control" id="citySelect" required onchange="updateDeliveryCost()">
                                                <option value="">-- Sélectionnez --</option>
                                                <optgroup label="Grand Tunis">
                                                    <option value="Tunis">Tunis</option>
                                                    <option value="Ariana">Ariana</option>
                                                    <option value="Ben Arous">Ben Arous</option>
                                                    <option value="Manouba">Manouba</option>
                                                    <option value="La Marsa">La Marsa</option>
                                                </optgroup>
                                                <optgroup label="Sahel">
                                                    <option value="Sousse">Sousse</option>
                                                    <option value="Monastir">Monastir</option>
                                                    <option value="Mahdia">Mahdia</option>
                                                    <option value="Sfax">Sfax</option>
                                                </optgroup>
                                                <optgroup label="Nord">
                                                    <option value="Bizerte">Bizerte</option>
                                                    <option value="Béja">Béja</option>
                                                    <option value="Nabeul">Nabeul</option>
                                                    <option value="Hammamet">Hammamet</option>
                                                </optgroup>
                                                <optgroup label="Centre & Sud">
                                                    <option value="Kairouan">Kairouan</option>
                                                    <option value="Gabès">Gabès</option>
                                                    <option value="Gafsa">Gafsa</option>
                                                    <option value="Tozeur">Tozeur</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Code Postal</label>
                                            <input type="text" 
                                                   name="postal_code" 
                                                   class="form-control" 
                                                   placeholder="1000">
                                        </div>
                                    </div>
                                </div>

                                <div id="deliveryInfo" style="background: #d4edda; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: none;">
                                    <i class="fas fa-truck" style="color: #155724;"></i>
                                    <span id="deliveryText"></span>
                                </div>

                                <!-- Payment Method -->
                                <h3 class="form-section-title" style="margin-top: 40px;">
                                    <i class="fas fa-credit-card"></i> Méthode de Paiement
                                </h3>

                                <div class="payment-section">
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="paypal" checked style="width: 20px; height: 20px;">
                                        <div style="flex-grow: 1;">
                                            <div class="paypal-logo">
                                                <i class="fab fa-paypal"></i> PayPal
                                            </div>
                                            <p style="margin: 5px 0 0 0; color: #666;">Paiement sécurisé via PayPal</p>
                                        </div>
                                    </label>
                                </div>

                                <button type="submit" class="btn-checkout" id="submit-btn">
                                    <i class="fas fa-lock"></i> Confirmer et Payer
                                </button>
                            </form>
                            
                            <script>
                                // Validate form before submission
                                document.getElementById('checkout-form').addEventListener('submit', function(e) {
                                    const cartData = document.getElementById('cart-data-input').value;
                                    console.log('Form submitted, cart_data:', cartData);
                                    
                                    if (!cartData || cartData === '[]' || cartData === '') {
                                        e.preventDefault();
                                        alert('Erreur: Votre panier est vide. Veuillez ajouter des produits.');
                                        window.location.href = 'index.php?controller=product&action=shop';
                                        return false;
                                    }
                                    
                                    try {
                                        const parsed = JSON.parse(cartData);
                                        if (!Array.isArray(parsed) || parsed.length === 0) {
                                            e.preventDefault();
                                            alert('Erreur: Votre panier est vide.');
                                            return false;
                                        }
                                    } catch (err) {
                                        e.preventDefault();
                                        alert('Erreur: Données du panier invalides.');
                                        return false;
                                    }
                                    
                                    // Disable submit button to prevent double submission
                                    document.getElementById('submit-btn').disabled = true;
                                    document.getElementById('submit-btn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
                                });
                            </script>
                        </div>

                    </div>

                    <!-- Right Column - Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h3 class="summary-title">
                                <i class="fas fa-file-invoice-dollar"></i> Récapitulatif
                            </h3>
                            
                            <div id="summary-items"></div>
                            
                            <div class="summary-total">
                                <span>Total:</span>
                                <span>$<span id="summary-total-amount">0.00</span></span>
                            </div>

                            <button type="button" class="btn-back" onclick="window.location.href='index.php?controller=product&action=shop'">
                                <i class="fas fa-arrow-left"></i> Retour à la Boutique
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3 style="color: white;">ImpactShop</h3>
                    <p>Boutique solidaire d'aide humanitaire. Chaque achat compte!</p>
                </div>
            </div>
        </div>
        <div class="copyright">
            &copy; 2025 ImpactShop. Développé par dridi10331. Tous droits réservés.
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        console.log('=== CHECKOUT PAGE LOADED ===');
        
        // Load cart from localStorage (try both keys for compatibility)
        let cart = JSON.parse(localStorage.getItem('impactshop_cart')) || JSON.parse(localStorage.getItem('cart')) || [];
        console.log('Cart loaded from localStorage:', cart);

        // Display cart items
        function displayCheckoutItems() {
            console.log('displayCheckoutItems called, cart length:', cart.length);
            
            const loadingIndicator = document.getElementById('loading-indicator');
            const checkoutContent = document.getElementById('checkout-content');
            const itemsDisplay = document.getElementById('checkout-items-display');
            const summaryItems = document.getElementById('summary-items');
            const cartCountEl = document.getElementById('cart-count');
            
            // Hide loading
            loadingIndicator.style.display = 'none';
            
            // Check if cart is empty
            if (cart.length === 0) {
                console.log('Cart is empty, redirecting to shop...');
                itemsDisplay.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Votre panier est vide</h3>
                        <p>Ajoutez des produits depuis la boutique</p>
                        <a href="index.php?controller=product&action=shop" class="btn btn-primary" style="margin-top: 20px; padding: 15px 30px; background: var(--primary); color: var(--secondary); text-decoration: none; border-radius: 3px; display: inline-block; font-weight: 700;">
                            <i class="fas fa-store"></i> Aller à la Boutique
                        </a>
                    </div>
                `;
                checkoutContent.style.display = 'block';
                return;
            }

            // Show checkout content
            checkoutContent.style.display = 'block';

            let itemsHTML = '';
            let summaryHTML = '';
            let total = 0;

            cart.forEach((item, index) => {
                console.log(`Item ${index}:`, item);
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                itemsHTML += `
                    <div class="cart-item">
                        <img src="assets/images/${item.image}" 
                             alt="${item.name}" 
                             onerror="this.src='https://via.placeholder.com/100x100/1e3149/ffb600?text=${encodeURIComponent(item.name.substring(0,2))}'">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">
                                <i class="fas fa-dollar-sign" style="color: var(--primary);"></i>
                                ${item.price.toFixed(2)} x ${item.quantity}
                            </div>
                        </div>
                        <div class="cart-item-total">
                            $${itemTotal.toFixed(2)}
                        </div>
                    </div>
                `;

                summaryHTML += `
                    <div class="summary-item">
                        <span>${item.name} (x${item.quantity})</span>
                        <span style="font-weight: 700; color: var(--primary);">$${itemTotal.toFixed(2)}</span>
                    </div>
                `;
            });

            console.log('Total calculated:', total);

            itemsDisplay.innerHTML = itemsHTML;
            summaryItems.innerHTML = summaryHTML;
            document.getElementById('summary-total-amount').textContent = total.toFixed(2);
            cartCountEl.textContent = cart.length;

            // Set hidden form fields
            document.getElementById('cart-data-input').value = JSON.stringify(cart);
            document.getElementById('total-amount-input').value = total.toFixed(2);
            
            console.log('Cart data set in hidden field');
        }

        // Update delivery cost based on city
        function updateDeliveryCost() {
            const city = document.getElementById('citySelect').value;
            const deliveryInfo = document.getElementById('deliveryInfo');
            const deliveryText = document.getElementById('deliveryText');
            
            if (!city) {
                deliveryInfo.style.display = 'none';
                return;
            }
            
            // Calculate total from cart
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            fetch(`index.php?controller=shipping&action=calculateCost&city=${encodeURIComponent(city)}&total=${total}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        deliveryInfo.style.display = 'block';
                        if (data.free_shipping) {
                            deliveryInfo.style.background = '#d4edda';
                            deliveryText.innerHTML = `<strong>Livraison GRATUITE!</strong> (${data.zone} - ${data.delay})`;
                        } else {
                            deliveryInfo.style.background = '#fff3cd';
                            deliveryText.innerHTML = `Livraison: <strong>${data.cost.toFixed(2)} TND</strong> (${data.zone} - ${data.delay})`;
                        }
                    }
                });
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            displayCheckoutItems();
        });

        // Also try to initialize immediately
        displayCheckoutItems();
    </script>

    <!-- Chatbot Widget -->
    <?php include __DIR__ . '/../partials/chatbot.php'; ?>
</body>

</html>