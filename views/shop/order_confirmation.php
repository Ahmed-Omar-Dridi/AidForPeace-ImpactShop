<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 60px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
        }

        .success-icon {
            font-size: 5rem;
            color: var(--success);
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .confirmation-container h1 {
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        .order-number {
            font-size: 1.5rem;
            color: var(--accent);
            font-weight: 800;
            margin: 20px 0;
        }

        .order-details {
            text-align: left;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .order-items {
            margin-top: 30px;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .order-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-details {
            flex-grow: 1;
            text-align: left;
        }

        .item-name {
            font-weight: 700;
            color: var(--primary-dark);
        }

        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed #ddd;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="shop-view">
        <header>
            <div class="header-logo">AidForPeace Impact Shop</div>
            <div class="header-actions">
                <a href="index.php?controller=product&action=shop" class="back-btn">← Retour à la Boutique</a>
            </div>
        </header>

        <main>
            <div class="confirmation-container">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>

                <h1>Commande Confirmée !</h1>
                <p style="font-size: 1.1rem; color: #666; margin-bottom: 20px;">
                    Merci pour votre achat. Votre contribution aide directement ceux qui en ont besoin.
                </p>

                <div class="order-number">
                    Commande #<?php echo str_pad($orderDetails['id'], 6, '0', STR_PAD_LEFT); ?>
                </div>

                <div class="order-details">
                    <h3 style="margin-bottom: 15px; color: var(--primary-dark);">Détails de la Commande</h3>
                    
                    <div class="detail-row">
                        <span><strong>Client:</strong></span>
                        <span><?php echo htmlspecialchars($orderDetails['first_name'] . ' ' . $orderDetails['last_name']); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span><strong>Email:</strong></span>
                        <span><?php echo htmlspecialchars($orderDetails['email']); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span><strong>Téléphone:</strong></span>
                        <span><?php echo htmlspecialchars($orderDetails['phone']); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span><strong>Date:</strong></span>
                        <span><?php echo date('d/m/Y H:i', strtotime($orderDetails['created_at'])); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span><strong>Paiement:</strong></span>
                        <span><?php echo strtoupper($orderDetails['payment_method']); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span><strong>Statut:</strong></span>
                        <span style="color: var(--accent); font-weight: 700;">
                            <?php 
                            $statusLabels = [
                                'pending' => 'En attente',
                                'paid' => 'Payé',
                                'cancelled' => 'Annulé'
                            ];
                            echo $statusLabels[$orderDetails['status']] ?? $orderDetails['status'];
                            ?>
                        </span>
                    </div>
                </div>

                <div class="order-items">
                    <h3 style="margin-bottom: 15px; color: var(--primary-dark); text-align: left;">Articles Commandés</h3>
                    
                    <?php foreach ($orderDetails['items'] as $item): ?>
                        <div class="order-item">
                            <img src="assets/images/<?php echo htmlspecialchars($item['img_name']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name_fr']); ?>"
                                 onerror="this.src='assets/images/placeholder.jpg'">
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['name_fr']); ?></div>
                                <div style="color: #666; font-size: 0.9rem;">
                                    Quantité: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['unit_price'], 2); ?>
                                </div>
                            </div>
                            <div style="font-weight: 700; font-size: 1.1rem;">
                                $<?php echo number_format($item['subtotal'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="total-section">
                        Total: $<?php echo number_format($orderDetails['total_amount'], 2); ?>
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <p style="color: #666; margin-bottom: 20px;">
                        Un email de confirmation a été envoyé à <strong><?php echo htmlspecialchars($orderDetails['email']); ?></strong>
                    </p>
                    
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <a href="index.php?controller=product&action=shop" class="btn btn-primary">
                            Continuer mes Achats
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            Retour à l'Accueil
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Vider le panier après confirmation
        localStorage.removeItem('impactshop_cart');

        // Animation confetti
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });

        setTimeout(() => {
            confetti({
                particleCount: 50,
                angle: 60,
                spread: 55,
                origin: { x: 0 }
            });
            confetti({
                particleCount: 50,
                angle: 120,
                spread: 55,
                origin: { x: 1 }
            });
        }, 250);
    </script>
</body>
</html>