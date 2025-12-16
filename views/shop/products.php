<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique - ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Votre CSS original inchangé */
        :root {
            --primary: #ffb600;
            --primary-dark: #e6a500;
            --secondary: #1e3149;
            --secondary-dark: #15202e;
            --text-dark: #5a6570;
            --success: #27ae60;
            --danger: #e74c3c;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: var(--secondary);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .navbar .logo a {
            text-decoration: none;
            font-size: 1.8rem;
            font-weight: 900;
        }
        .navbar .logo .impact { color: white; }
        .navbar .logo .shop { color: var(--primary); }
        .navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .navbar .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        .navbar .nav-links a:hover { color: var(--primary); }
        
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
        }
        .shop-header h1 {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }
        .shop-header h1 i { color: var(--primary); }
        .shop-header p {
            color: var(--text-dark);
            font-size: 1.1rem;
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
        
        /* Le reste de votre CSS original... */
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="<?php echo BASE_URL; ?>"><span class="impact">Impact</span><span class="shop">Shop</span></a>
        </div>
        <div class="nav-links">
            <a href="<?php echo BASE_URL; ?>"><i class="fas fa-home"></i> Accueil</a>
            <a href="<?php echo BASE_URL; ?>index.php?controller=contact&action=index"><i class="fas fa-envelope"></i> Contact</a>
            <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=view" class="cart-btn">
                <i class="fas fa-shopping-cart"></i>
                <span>Panier</span>
                <span class="cart-count" id="cartCount">0</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        
        <!-- Header -->
        <div class="shop-header">
            <h1><i class="fas fa-store"></i> Notre Boutique</h1>
            <p>Découvrez nos produits solidaires et soutenez des causes humanitaires</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php if (empty($products)): ?>
                <div class="no-products" style="grid-column: 1/-1; text-align: center; padding: 60px;">
                    <i class="fas fa-box-open" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                    <h3 style="color: var(--text-dark);">Aucun produit disponible</h3>
                    <p style="color: var(--text-dark);">Revenez bientôt pour découvrir nos nouveaux produits!</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                        <div class="product-image" style="position: relative; height: 200px; overflow: hidden;">
                            <img src="../../assets/images/<?php echo htmlspecialchars($product->getImgName()); ?>" 
                                 alt="<?php echo htmlspecialchars($product->getNameFr()); ?>"
                                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;"
                                 onerror="this.src='https://via.placeholder.com/300x200/1e3149/ffb600?text=<?php echo urlencode(substr($product->getNameFr(), 0, 2)); ?>'">
                            
                            <?php $stock = $product->getStock(); ?>
                            <?php if ($stock > 10): ?>
                                <span style="position: absolute; top: 10px; right: 10px; padding: 5px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; background: #d4edda; color: #155724;">En Stock</span>
                            <?php elseif ($stock > 0): ?>
                                <span style="position: absolute; top: 10px; right: 10px; padding: 5px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; background: #fff3cd; color: #856404;">Stock Faible (<?php echo $stock; ?>)</span>
                            <?php else: ?>
                                <span style="position: absolute; top: 10px; right: 10px; padding: 5px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; background: #f8d7da; color: #721c24;">Rupture</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info" style="padding: 20px;">
                            <h3 style="font-size: 1.1rem; color: var(--secondary); margin-bottom: 8px; font-weight: 700;">
                                <?php echo htmlspecialchars($product->getNameFr()); ?>
                            </h3>
                            
                            <!-- Average Rating -->
                            <?php 
                                require_once __DIR__ . '/../../models/PRODUCT.PHP';
                                $rating = Product::getAverageRating($product->getId());
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
                            
                            <p class="description" style="color: var(--text-dark); font-size: 0.85rem; margin-bottom: 15px; line-height: 1.5; height: 40px; overflow: hidden;">
                                <?php echo htmlspecialchars($product->getDescriptionFr() ?? ''); ?>
                            </p>
                            
                            <div class="product-footer" style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="price" style="font-size: 1.3rem; font-weight: 800; color: var(--primary);">
                                    <?php echo number_format($product->getPrice(), 2); ?> TND
                                </span>
                                
                                <div class="product-actions" style="display: flex; align-items: center; gap: 8px;">
                                    <a href="index.php?controller=review&action=create&product_id=<?php echo $product->getId(); ?>" 
                                       class="review-btn" 
                                       title="Laisser un avis"
                                       style="width: 40px; height: 40px; border: none; border-radius: 50%; background: #f39c12; color: white; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s;">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    
                                    <?php if ($stock > 0): ?>
                                        <button class="add-to-cart-btn" 
                                                onclick="addToCart(<?php echo $product->getId(); ?>)"
                                                title="Ajouter au panier"
                                                style="width: 45px; height: 45px; border: none; border-radius: 50%; background: var(--secondary); color: white; font-size: 1.1rem; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="add-to-cart-btn" disabled title="Produit indisponible"
                                                style="width: 45px; height: 45px; border: none; border-radius: 50%; background: #ccc; color: white; font-size: 1.1rem; cursor: not-allowed; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function addToCart(productId) {
            // Ajouter au panier via AJAX
            fetch('index.php?controller=cart&action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Produit ajouté au panier!');
                    // Mettre à jour le compteur du panier
                    document.getElementById('cartCount').textContent = data.cart_count;
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur réseau');
            });
        }
    </script>

</body>
</html>