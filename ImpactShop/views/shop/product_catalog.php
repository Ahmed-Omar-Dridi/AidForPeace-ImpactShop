<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactShop - Boutique</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="shop-view">
        <header>
            <div class="header-logo">AidForPeace Impact Shop</div>
            <div class="header-actions">
                <button id="lang-toggle" onclick="toggleLang()">FR</button>
                <a href="index.php" class="back-btn">← Accueil</a>
                <div class="cart-icon" onclick="showCart()">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-badge">0</span>
                </div>
            </div>
        </header>

        <main>
            <section id="shop-section">
                <h1 data-en="Shop Our Impact" data-fr="Notre Boutique d'Impact">Notre Boutique d'Impact</h1>
                <p data-en="Your purchase directly funds life-saving aid." data-fr="Votre achat finance directement une aide vitale.">
                    Votre achat finance directement une aide vitale.
                </p>
                
                <div class="impact-grid" id="impact-grid">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="impact-item-card">
                                <img 
                                    src="assets/images/<?php echo htmlspecialchars($product['img_name']); ?>" 
                                    alt="<?php echo htmlspecialchars($product['name_fr']); ?>"
                                    class="item-image"
                                    onerror="this.src='assets/images/placeholder.jpg'">
                                
                                <div class="item-info">
                                    <h3 class="item-name" 
                                        data-en="<?php echo htmlspecialchars($product['name_en']); ?>"
                                        data-fr="<?php echo htmlspecialchars($product['name_fr']); ?>">
                                        <?php echo htmlspecialchars($product['name_fr']); ?>
                                    </h3>
                                    
                                    <p class="item-description"
                                       data-en="<?php echo htmlspecialchars($product['description_en']); ?>"
                                       data-fr="<?php echo htmlspecialchars($product['description_fr']); ?>">
                                        <?php echo htmlspecialchars($product['description_fr']); ?>
                                    </p>
                                    
                                    <div class="item-price">$<?php echo number_format($product['price'], 2); ?></div>
                                    
                                    <button 
                                        class="btn add-to-cart" 
                                        onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name_fr']); ?>', <?php echo $product['price']; ?>, '<?php echo htmlspecialchars($product['img_name']); ?>')"
                                        data-en="Add to Cart"
                                        data-fr="Ajouter au Panier">
                                        Ajouter au Panier
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 60px;">
                            <h3>Aucun produit disponible pour le moment</h3>
                            <p>Revenez plus tard !</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Section Panier (initialement cachée) -->
            <section id="checkout-section" class="hidden">
                <h1 data-en="Your Cart" data-fr="Votre Panier">Votre Panier</h1>
                <div class="cart-items" id="cart-items"></div>
                <div class="checkout-summary">
                    <div class="checkout-total">
                        <span data-en="Total:" data-fr="Total :">Total :</span> 
                        <strong>$<span id="total-amount">0.00</span></strong>
                    </div>
                    <button class="btn btn-primary" onclick="proceedToCheckout()">
                        Procéder au Paiement
                    </button>
                    <button class="btn btn-secondary" onclick="showShop()">
                        Retour à la Boutique
                    </button>
                </div>
            </section>
        </main>
    </div>

    <script src="assets/js/shop.js"></script>
</body>
</html>