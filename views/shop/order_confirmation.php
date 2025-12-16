<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - ImpactShop</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #ffb600;
            --secondary: #1e3149;
            --text-dark: #5a6570;
            --heading-color: #282f39;
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
            background: #f9f9f9;
        }
        
        .top-bar {
            background: var(--secondary);
            color: white;
            padding: 10px 0;
            font-size: 13px;
        }
        
        .top-bar i {
            color: var(--primary);
            margin-right: 5px;
        }
        
        .site-header {
            background: white;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
            padding: 20px 0;
        }
        
        .site-logo h1 {
            color: var(--secondary);
            font-weight: 800;
            margin: 0;
            font-size: 2rem;
        }
        
        .site-logo span {
            color: var(--primary);
        }
        
        .main-section {
            padding: 80px 0;
        }
        
        .success-box {
            background: white;
            padding: 60px;
            border-radius: 3px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 40px;
        }
        
        .success-icon {
            width: 120px;
            height: 120px;
            background: #27ae60;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            margin: 0 auto 30px;
        }
        
        .success-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--heading-color);
            margin-bottom: 20px;
        }
        
        .order-number {
            font-size: 3rem;
            font-weight: 900;
            color: var(--primary);
            margin: 20px 0;
        }
        
        .details-box {
            background: white;
            padding: 40px;
            border-radius: 3px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .details-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--heading-color);
            margin-bottom: 30px;
            text-transform: uppercase;
            border-bottom: 3px solid var(--primary);
            padding-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #ebebeb;
        }
        
        .info-label {
            font-weight: 700;
            color: var(--heading-color);
        }
        
        .info-value {
            color: var(--text-dark);
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            border: 1px solid #ebebeb;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        
        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 3px;
        }
        
        .item-info {
            flex-grow: 1;
        }
        
        .item-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--heading-color);
        }
        
        .item-price {
            color: var(--primary);
            font-weight: 700;
        }
        
        .total-box {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 3px;
            margin-top: 30px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 2rem;
            font-weight: 900;
            color: var(--heading-color);
            padding-top: 20px;
            border-top: 3px solid var(--secondary);
        }
        
        .total-amount {
            color: var(--primary);
        }
        
        .btn-primary {
            background: var(--primary);
            color: var(--secondary);
            border: 2px solid var(--primary);
            padding: 15px 40px;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
            border-color: var(--secondary);
            color: white;
            text-decoration: none;
        }
        
        .badge {
            padding: 8px 15px;
            border-radius: 3px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-top: 80px;
        }
    </style>
    
    <script>
        // Vider le panier après confirmation (both keys for compatibility)
        localStorage.removeItem('cart');
        localStorage.removeItem('impactshop_cart');
        console.log('Cart cleared after order confirmation');
    </script>
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <i class="fas fa-phone"></i> +216 XX XXX XXX
                    <span style="margin-left: 20px;"><i class="fas fa-envelope"></i> contact@impactshop.tn</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <div class="site-logo">
                        <a href="index.php" style="text-decoration: none;">
                            <h1><span>Impact</span>Shop</h1>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Section -->
    <section class="main-section">
        <div class="container">
            
            <!-- Success Message -->
            <div class="success-box">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="success-title">Commande Confirmée!</h1>
                <p style="font-size: 1.2rem; color: var(--text-dark);">
                    Merci pour votre commande. Nous avons bien reçu votre paiement.
                </p>
                <div class="order-number">
                    #<?php echo str_pad($orderDetails['id'] ?? 0, 6, '0', STR_PAD_LEFT); ?>
                </div>
            </div>

            <div class="row">
                
                <!-- Order Details -->
                <div class="col-lg-6">
                    <div class="details-box">
                        <h2 class="details-title">
                            <i class="fas fa-info-circle"></i> Détails de la Commande
                        </h2>
                        
                        <div class="info-row">
                            <span class="info-label">Client:</span>
                            <span class="info-value">
                                <?php echo htmlspecialchars($orderDetails['first_name'] ?? '') . ' ' . htmlspecialchars($orderDetails['last_name'] ?? ''); ?>
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($orderDetails['email'] ?? ''); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Téléphone:</span>
                            <span class="info-value"><?php echo htmlspecialchars($orderDetails['phone'] ?? ''); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Date:</span>
                            <span class="info-value">
                                <?php echo date('d/m/Y H:i', strtotime($orderDetails['created_at'] ?? 'now')); ?>
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Paiement:</span>
                            <span class="info-value"><?php echo strtoupper($orderDetails['payment_method'] ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-row" style="border-bottom: none;">
                            <span class="info-label">Statut:</span>
                            <span class="info-value">
                                <?php
                                $status = $orderDetails['status'] ?? 'pending';
                                $badgeClass = $status === 'paid' ? 'success' : 'warning';
                                $statusLabel = $status === 'paid' ? 'Payé' : 'En attente';
                                ?>
                                <span class="badge badge-<?php echo $badgeClass; ?>">
                                    <?php echo $statusLabel; ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="col-lg-6">
                    <div class="details-box">
                        <h2 class="details-title">
                            <i class="fas fa-shopping-bag"></i> Articles Commandés
                        </h2>
                        
                        <?php if (!empty($orderDetails['items'])): ?>
                            <?php foreach ($orderDetails['items'] as $item): ?>
                                <div class="order-item">
                                    <img src="../../assets/images/<?php echo htmlspecialchars($item['img_name'] ?? 'placeholder.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name_fr'] ?? 'Produit'); ?>"
                                         onerror="this.src='https://via.placeholder.com/80x80/1e3149/ffb600?text=P'">
                                    <div class="item-info">
                                        <div class="item-name"><?php echo htmlspecialchars($item['name_fr'] ?? 'Produit'); ?></div>
                                        <div class="item-price">
                                            $<?php echo number_format($item['unit_price'] ?? 0, 2); ?> 
                                            x <?php echo $item['quantity'] ?? 1; ?>
                                        </div>
                                    </div>
                                    <div style="font-weight: 800; font-size: 1.2rem; color: var(--primary);">
                                        $<?php echo number_format($item['subtotal'] ?? 0, 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align: center; padding: 40px; color: #999;">Aucun article</p>
                        <?php endif; ?>
                        
                        <div class="total-box">
                            <div class="total-row">
                                <span>TOTAL:</span>
                                <span class="total-amount">$<?php echo number_format($orderDetails['total_amount'] ?? 0, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Actions -->
            <div class="text-center" style="margin-top: 50px;">
                <p style="font-size: 1.1rem; margin-bottom: 30px;">
                    <i class="fas fa-envelope"></i> 
                    Un email de confirmation a été envoyé à <strong><?php echo htmlspecialchars($orderDetails['email'] ?? ''); ?></strong>
                </p>
                
                <a href="index.php?controller=product&action=shop" class="btn-primary">
                    <i class="fas fa-shopping-bag"></i> CONTINUER MES ACHATS
                </a>
                
                <a href="index.php?controller=order&action=view&id=<?php echo $orderDetails['id'] ?? 0; ?>" class="btn-primary">
                    <i class="fas fa-file-alt"></i> VOIR MA COMMANDE
                </a>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <h3>ImpactShop</h3>
            <p>Merci de votre confiance et de votre générosité!</p>
            <p style="margin-top: 20px;">Copyright &copy; 2025 ImpactShop. Développé par dridi10331.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>