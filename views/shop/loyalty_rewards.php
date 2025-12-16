<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récompenses Fidélité - ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/shop.css">
    <style>
        .rewards-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .rewards-header { text-align: center; margin-bottom: 40px; }
        .rewards-header h1 { color: #1e3149; font-size: 2.5rem; }
        .rewards-header h1 i { color: #ffb600; }
        
        .balance-banner { background: linear-gradient(135deg, #ffb600, #ff9500); padding: 25px; border-radius: 15px; text-align: center; margin-bottom: 40px; color: #1e3149; }
        .balance-banner h3 { margin: 0; font-size: 1.2rem; }
        .balance-banner .points { font-size: 3rem; font-weight: 900; margin: 10px 0; }
        
        .rewards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        
        .reward-card { background: white; border-radius: 20px; padding: 30px; text-align: center; box-shadow: 0 5px 25px rgba(0,0,0,0.1); transition: all 0.3s; position: relative; overflow: hidden; }
        .reward-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(0,0,0,0.15); }
        .reward-card.locked { opacity: 0.7; }
        .reward-card.locked::after { content: '🔒'; position: absolute; top: 15px; right: 15px; font-size: 1.5rem; }
        
        .reward-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #1e3149, #15202e); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .reward-icon i { font-size: 2rem; color: #ffb600; }
        
        .reward-card h3 { color: #1e3149; margin-bottom: 10px; }
        .reward-card p { color: #666; font-size: 0.9rem; margin-bottom: 20px; }
        .reward-points { font-size: 1.5rem; font-weight: 900; color: #ffb600; margin-bottom: 20px; }
        
        .redeem-btn { display: inline-block; padding: 12px 30px; background: #ffb600; color: #1e3149; border: none; border-radius: 25px; font-weight: 700; cursor: pointer; transition: all 0.3s; text-decoration: none; }
        .redeem-btn:hover { background: #1e3149; color: white; }
        .redeem-btn:disabled { background: #ccc; cursor: not-allowed; }
        
        .alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        
        .reward-code { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .reward-code h4 { color: #1e3149; margin-bottom: 10px; }
        .reward-code code { font-size: 1.5rem; font-weight: 700; color: #ffb600; background: white; padding: 10px 20px; border-radius: 5px; display: inline-block; }
    </style>
</head>
<body>
    <nav class="shop-navbar">
        <div class="logo"><a href="index.php"><span class="impact">Impact</span><span class="shop">Shop</span></a></div>
        <div class="nav-links">
            <a href="index.php?controller=loyalty&action=index"><i class="fas fa-arrow-left"></i> Retour</a>
            <a href="index.php?controller=product&action=shop"><i class="fas fa-store"></i> Boutique</a>
        </div>
    </nav>

    <div class="rewards-container">
        <div class="rewards-header">
            <h1><i class="fas fa-gift"></i> Récompenses Disponibles</h1>
            <p>Échangez vos points contre des réductions et avantages exclusifs!</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <?php if (isset($_SESSION['reward_code'])): ?>
                    <div class="reward-code">
                        <h4>Votre code de réduction:</h4>
                        <code><?php echo $_SESSION['reward_code']; unset($_SESSION['reward_code']); ?></code>
                        <p style="margin-top: 10px; font-size: 0.9rem;">Utilisez ce code lors de votre prochaine commande!</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="balance-banner">
            <h3>Votre solde actuel</h3>
            <div class="points"><?php echo $balance; ?></div>
            <p>points disponibles</p>
        </div>

        <div class="rewards-grid">
            <?php foreach ($rewards as $reward): ?>
                <?php $canRedeem = $balance >= $reward['points_required']; ?>
                <div class="reward-card <?php echo !$canRedeem ? 'locked' : ''; ?>">
                    <div class="reward-icon">
                        <i class="fas <?php echo htmlspecialchars($reward['icon'] ?? 'fa-gift'); ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($reward['name']); ?></h3>
                    <p><?php echo htmlspecialchars($reward['description'] ?? ''); ?></p>
                    <div class="reward-points"><?php echo $reward['points_required']; ?> points</div>
                    
                    <?php if ($canRedeem): ?>
                        <form method="POST" action="index.php?controller=loyalty&action=redeem" style="display: inline;">
                            <input type="hidden" name="reward_id" value="<?php echo $reward['id']; ?>">
                            <button type="submit" class="redeem-btn" onclick="return confirm('Échanger <?php echo $reward['points_required']; ?> points contre cette récompense?');">
                                <i class="fas fa-exchange-alt"></i> Échanger
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="redeem-btn" disabled>
                            <i class="fas fa-lock"></i> <?php echo $reward['points_required'] - $balance; ?> pts manquants
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="index.php?controller=product&action=shop" style="display: inline-block; padding: 15px 40px; background: #1e3149; color: white; text-decoration: none; border-radius: 25px; font-weight: 700;">
                <i class="fas fa-shopping-cart"></i> Gagner plus de points
            </a>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/chatbot.php'; ?>
</body>
</html>
