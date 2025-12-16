<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultat Suivi - ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/shop.css">
</head>
<body>
    <nav class="shop-navbar">
        <div class="logo">
            <a href="index.php"><span class="impact">Impact</span><span class="shop">Shop</span></a>
        </div>
        <div class="nav-links">
            <a href="index.php?controller=product&action=shop"><i class="fas fa-store"></i> Boutique</a>
            <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
        </div>
    </nav>

    <div class="tracking-result-container">
        <div class="tracking-result-card">
            <div class="tracking-header">
                <h1><i class="fas fa-truck"></i> Suivi de Livraison</h1>
                <div class="tracking-number">
                    <?php echo htmlspecialchars($shipping->getTrackingNumber()); ?>
                </div>
            </div>

            <?php
            $status = $shipping->getStatus();
            $statusLabels = [
                'processing' => 'En preparation',
                'shipped' => 'Expedie',
                'in_transit' => 'En transit',
                'delivered' => 'Livre'
            ];
            $statusIcons = [
                'processing' => 'fa-box',
                'shipped' => 'fa-shipping-fast',
                'in_transit' => 'fa-truck',
                'delivered' => 'fa-check-circle'
            ];
            $currentLabel = $statusLabels[$status] ?? $status;
            $currentIcon = $statusIcons[$status] ?? 'fa-question';
            ?>

            <div class="current-status status-<?php echo $status; ?>">
                <i class="fas <?php echo $currentIcon; ?>"></i>
                <span><?php echo $currentLabel; ?></span>
            </div>

            <div class="tracking-timeline">
                <div class="timeline-item <?php echo in_array($status, ['processing', 'shipped', 'in_transit', 'delivered']) ? 'completed' : ''; ?> <?php echo $status === 'processing' ? 'active' : ''; ?>">
                    <div class="timeline-icon"><i class="fas fa-box"></i></div>
                    <div class="timeline-content">
                        <h4>Commande en preparation</h4>
                        <p>Votre commande est en cours de preparation</p>
                    </div>
                </div>

                <div class="timeline-item <?php echo in_array($status, ['shipped', 'in_transit', 'delivered']) ? 'completed' : ''; ?> <?php echo $status === 'shipped' ? 'active' : ''; ?>">
                    <div class="timeline-icon"><i class="fas fa-shipping-fast"></i></div>
                    <div class="timeline-content">
                        <h4>Commande expediee</h4>
                        <p>Votre colis a ete remis au transporteur</p>
                    </div>
                </div>

                <div class="timeline-item <?php echo in_array($status, ['in_transit', 'delivered']) ? 'completed' : ''; ?> <?php echo $status === 'in_transit' ? 'active' : ''; ?>">
                    <div class="timeline-icon"><i class="fas fa-truck"></i></div>
                    <div class="timeline-content">
                        <h4>En cours de livraison</h4>
                        <p>Votre colis est en route vers vous</p>
                    </div>
                </div>

                <div class="timeline-item <?php echo $status === 'delivered' ? 'completed active' : ''; ?>">
                    <div class="timeline-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="timeline-content">
                        <h4>Livre</h4>
                        <p>Votre colis a ete livre avec succes</p>
                    </div>
                </div>
            </div>

            <div class="shipping-details">
                <h3><i class="fas fa-info-circle"></i> Details de Livraison</h3>
                
                <div class="detail-row">
                    <span class="label">Transporteur:</span>
                    <span class="value"><?php echo htmlspecialchars($shipping->getCarrier()); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="label">Adresse:</span>
                    <span class="value">
                        <?php echo htmlspecialchars($shipping->getAddress()); ?>,
                        <?php echo htmlspecialchars($shipping->getCity()); ?>
                    </span>
                </div>
                
                <?php if ($shipping->getEstimatedDelivery()): ?>
                <div class="detail-row">
                    <span class="label">Livraison estimee:</span>
                    <span class="value"><?php echo date('d/m/Y', strtotime($shipping->getEstimatedDelivery())); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="tracking-actions">
                <a href="index.php?controller=shipping&action=track" class="btn-secondary">
                    <i class="fas fa-search"></i> Nouvelle recherche
                </a>
                <a href="index.php?controller=product&action=shop" class="btn-primary">
                    <i class="fas fa-shopping-cart"></i> Continuer vos achats
                </a>
            </div>
        </div>
    </div>

    <style>
        .tracking-result-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 0 20px;
        }
        
        .tracking-result-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .tracking-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .tracking-header h1 {
            color: #1e3149;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        
        .tracking-header h1 i {
            color: #ffb600;
        }
        
        .tracking-number {
            background: #f8f9fa;
            color: #1e3149;
            font-family: monospace;
            font-size: 1.3rem;
            font-weight: 700;
            padding: 10px 25px;
            border-radius: 8px;
            display: inline-block;
        }
        
        .current-status {
            text-align: center;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            font-size: 1.3rem;
            font-weight: 700;
        }
        
        .current-status i {
            font-size: 2rem;
        }
        
        .status-processing {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-shipped {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-in_transit {
            background: #d4edda;
            color: #155724;
        }
        
        .status-delivered {
            background: #d4edda;
            color: #155724;
        }
        
        .tracking-timeline {
            position: relative;
            padding-left: 50px;
            margin: 40px 0;
        }
        
        .tracking-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #e0e0e0;
            border-radius: 2px;
        }
        
        .timeline-item {
            position: relative;
            padding: 20px 0;
            opacity: 0.5;
        }
        
        .timeline-item.completed {
            opacity: 1;
        }
        
        .timeline-item.active .timeline-icon {
            background: #ffb600;
            box-shadow: 0 0 0 5px rgba(255, 182, 0, 0.3);
        }
        
        .timeline-item.completed .timeline-icon {
            background: #27ae60;
        }
        
        .timeline-icon {
            position: absolute;
            left: -42px;
            width: 44px;
            height: 44px;
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }
        
        .timeline-icon i {
            color: white;
            font-size: 1rem;
        }
        
        .timeline-content h4 {
            color: #1e3149;
            margin-bottom: 5px;
        }
        
        .timeline-content p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .shipping-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
        }
        
        .shipping-details h3 {
            color: #1e3149;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .shipping-details h3 i {
            color: #ffb600;
            margin-right: 10px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-row .label {
            color: #666;
        }
        
        .detail-row .value {
            color: #1e3149;
            font-weight: 600;
        }
        
        .tracking-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .tracking-actions a {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #ffb600;
            color: #1e3149;
        }
        
        .btn-primary:hover {
            background: #e6a500;
        }
        
        .btn-secondary {
            background: #1e3149;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #15202e;
        }
        
        @media (max-width: 768px) {
            .tracking-result-card {
                padding: 25px;
            }
            
            .tracking-timeline {
                padding-left: 40px;
            }
            
            .timeline-icon {
                left: -35px;
                width: 36px;
                height: 36px;
            }
            
            .tracking-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
