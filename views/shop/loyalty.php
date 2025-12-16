<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programme de Fidélité - ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/shop.css">
    <style>
        :root {
            --primary: #ff6b35;
            --primary-light: #ff8c5a;
            --secondary: #1a1a2e;
            --secondary-light: #16213e;
            --accent: #00d9ff;
            --success: #00c853;
        }
        body { background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%); min-height: 100vh; }
        .loyalty-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .loyalty-header { text-align: center; margin-bottom: 40px; }
        .loyalty-header h1 { color: var(--secondary); font-size: 2.5rem; margin-bottom: 10px; font-weight: 900; }
        .loyalty-header h1 i { color: var(--primary); filter: drop-shadow(0 2px 10px rgba(255,107,53,0.4)); }
        .loyalty-header p { color: #636e72; font-size: 1.1rem; }
        
        .points-card { background: linear-gradient(135deg, var(--secondary), var(--secondary-light)); border-radius: 24px; padding: 45px; color: white; text-align: center; margin-bottom: 30px; box-shadow: 0 15px 50px rgba(26,26,46,0.3); position: relative; overflow: hidden; }
        .points-card::before { content: ''; position: absolute; top: -50%; right: -50%; width: 100%; height: 200%; background: radial-gradient(circle, rgba(255,107,53,0.15) 0%, transparent 70%); }
        .points-balance { font-size: 4rem; font-weight: 900; color: var(--primary); margin: 20px 0; text-shadow: 0 4px 20px rgba(255,107,53,0.4); position: relative; z-index: 1; }
        .level-badge { display: inline-block; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; padding: 12px 30px; border-radius: 50px; font-weight: 800; margin-top: 15px; box-shadow: 0 8px 25px rgba(255,107,53,0.4); position: relative; z-index: 1; }
        
        .level-progress { background: white; border-radius: 20px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); border-top: 4px solid var(--primary); }
        .progress-bar { height: 12px; background: #e8ecef; border-radius: 10px; overflow: hidden; margin: 20px 0; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, var(--primary), var(--accent)); border-radius: 10px; transition: width 0.5s; }
        
        .levels-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 20px; }
        .level-item { text-align: center; padding: 18px; border-radius: 14px; background: #f8fafc; transition: all 0.3s; }
        .level-item:hover { transform: translateY(-3px); }
        .level-item.active { background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; box-shadow: 0 8px 25px rgba(255,107,53,0.35); }
        .level-item .icon { font-size: 2rem; margin-bottom: 10px; }
        
        .check-points { background: white; border-radius: 20px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); border-top: 4px solid var(--accent); }
        .check-points h3 { color: var(--secondary); margin-bottom: 20px; font-weight: 800; }
        .check-points h3 i { color: var(--accent); }
        .check-form { display: flex; gap: 15px; }
        .check-form input { flex: 1; padding: 16px 20px; border: 2px solid #e8ecef; border-radius: 14px; font-size: 1rem; transition: all 0.3s; }
        .check-form input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(0,217,255,0.15); }
        .check-form button { padding: 16px 35px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; border: none; border-radius: 14px; font-weight: 800; cursor: pointer; transition: all 0.3s; box-shadow: 0 8px 25px rgba(255,107,53,0.35); }
        .check-form button:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(255,107,53,0.45); }
        
        .history-section { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
        .history-item { display: flex; justify-content: space-between; padding: 18px 0; border-bottom: 1px solid #f1f3f4; }
        .history-item:last-child { border-bottom: none; }
        .points-earned { color: var(--success); font-weight: 800; }
        .points-spent { color: #ff1744; font-weight: 800; }
        
        .rewards-preview { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px; }
        .reward-card { background: white; border-radius: 18px; padding: 25px; text-align: center; transition: all 0.3s; box-shadow: 0 5px 20px rgba(0,0,0,0.06); }
        .reward-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); }
        .reward-card i { font-size: 2.5rem; color: var(--primary); margin-bottom: 15px; filter: drop-shadow(0 2px 8px rgba(255,107,53,0.3)); }
        .reward-points { font-weight: 800; color: var(--secondary); }
        
        @media (max-width: 768px) {
            .levels-grid { grid-template-columns: repeat(2, 1fr); }
            .check-form { flex-direction: column; }
        }
    </style>
</head>
<body>
    <nav class="shop-navbar">
        <div class="logo"><a href="index.php"><span class="impact">Impact</span><span class="shop">Shop</span></a></div>
        <div class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
            <a href="index.php?controller=home&action=donation"><i class="fas fa-heart"></i> Faire un don</a>
            <a href="index.php?controller=product&action=shop"><i class="fas fa-store"></i> Boutique</a>
        </div>
    </nav>

    <div class="loyalty-container">
        <div class="loyalty-header">
            <h1><i class="fas fa-gift"></i> Programme de Fidélité</h1>
            <p>Gagnez des points à chaque achat et échangez-les contre des récompenses!</p>
        </div>

        <!-- Vérifier ses points -->
        <div class="check-points">
            <h3><i class="fas fa-search"></i> Vérifier mes points</h3>
            <form class="check-form" id="checkPointsForm">
                <input type="email" id="checkEmail" placeholder="Entrez votre email" required>
                <button type="submit"><i class="fas fa-check"></i> Vérifier</button>
            </form>
            <div id="pointsResult" style="margin-top: 20px; display: none;"></div>
        </div>

        <!-- Carte des points -->
        <div class="points-card">
            <h2>Votre Solde</h2>
            <div class="points-balance" id="displayBalance"><?php echo $balance; ?></div>
            <p>points disponibles</p>
            <div class="level-badge">
                <?php echo $level['icon'] ?? '🥉'; ?> Niveau <?php echo $level['name'] ?? 'Bronze'; ?>
            </div>
        </div>

        <!-- Progression des niveaux -->
        <div class="level-progress">
            <h3><i class="fas fa-chart-line"></i> Progression</h3>
            <div class="progress-bar">
                <?php 
                $totalEarned = isset($customerId) ? LoyaltyPoint::getTotalEarned($customerId) : 0;
                $nextLevel = 500;
                if ($totalEarned >= 500) $nextLevel = 1500;
                if ($totalEarned >= 1500) $nextLevel = 5000;
                if ($totalEarned >= 5000) $nextLevel = 5000;
                $progress = min(100, ($totalEarned / $nextLevel) * 100);
                ?>
                <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
            </div>
            <p>Encore <?php echo max(0, $nextLevel - $totalEarned); ?> points pour le niveau suivant!</p>
            
            <div class="levels-grid">
                <div class="level-item <?php echo ($level['key'] ?? '') === 'bronze' ? 'active' : ''; ?>">
                    <div class="icon">🥉</div>
                    <strong>Bronze</strong>
                    <p>0 pts</p>
                </div>
                <div class="level-item <?php echo ($level['key'] ?? '') === 'silver' ? 'active' : ''; ?>">
                    <div class="icon">🥈</div>
                    <strong>Argent</strong>
                    <p>500 pts</p>
                </div>
                <div class="level-item <?php echo ($level['key'] ?? '') === 'gold' ? 'active' : ''; ?>">
                    <div class="icon">🥇</div>
                    <strong>Or</strong>
                    <p>1500 pts</p>
                </div>
                <div class="level-item <?php echo ($level['key'] ?? '') === 'platinum' ? 'active' : ''; ?>">
                    <div class="icon">💎</div>
                    <strong>Platine</strong>
                    <p>5000 pts</p>
                </div>
            </div>
        </div>

        <!-- Récompenses disponibles -->
        <div class="history-section">
            <h3><i class="fas fa-gift"></i> Récompenses Disponibles</h3>
            <div class="rewards-preview">
                <?php foreach ($rewards as $reward): ?>
                <div class="reward-card">
                    <i class="fas <?php echo htmlspecialchars($reward['icon'] ?? 'fa-gift'); ?>"></i>
                    <h4><?php echo htmlspecialchars($reward['name']); ?></h4>
                    <p class="reward-points"><?php echo $reward['points_required']; ?> points</p>
                    <?php if ($balance >= $reward['points_required']): ?>
                        <a href="index.php?controller=loyalty&action=rewards" style="display: inline-block; margin-top: 10px; background: #ffb600; color: #1e3149; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: 600;">Échanger</a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Historique -->
        <?php if (!empty($history)): ?>
        <div class="history-section" style="margin-top: 30px;">
            <h3><i class="fas fa-history"></i> Historique des Points</h3>
            <?php foreach ($history as $item): ?>
            <div class="history-item">
                <div>
                    <strong><?php echo htmlspecialchars($item['description']); ?></strong>
                    <p style="color: #666; font-size: 0.9rem;"><?php echo date('d/m/Y', strtotime($item['created_at'])); ?></p>
                </div>
                <div class="<?php echo in_array($item['type'], ['earned', 'bonus']) ? 'points-earned' : 'points-spent'; ?>">
                    <?php echo in_array($item['type'], ['earned', 'bonus']) ? '+' : '-'; ?><?php echo $item['points']; ?> pts
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Comment ça marche -->
        <div class="history-section" style="margin-top: 30px;">
            <h3><i class="fas fa-question-circle"></i> Comment ça marche?</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-shopping-cart" style="font-size: 2rem; color: #ffb600; margin-bottom: 10px;"></i>
                    <h4>1. Achetez</h4>
                    <p style="color: #666;">Faites vos achats sur ImpactShop</p>
                </div>
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-coins" style="font-size: 2rem; color: #ffb600; margin-bottom: 10px;"></i>
                    <h4>2. Gagnez</h4>
                    <p style="color: #666;">1 TND = 1 point de fidélité</p>
                </div>
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-gift" style="font-size: 2rem; color: #ffb600; margin-bottom: 10px;"></i>
                    <h4>3. Échangez</h4>
                    <p style="color: #666;">Convertissez vos points en réductions</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('checkPointsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('checkEmail').value;
        const resultDiv = document.getElementById('pointsResult');
        
        fetch('index.php?controller=loyalty&action=checkPoints&email=' + encodeURIComponent(email))
            .then(r => r.json())
            .then(data => {
                resultDiv.style.display = 'block';
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div style="background: #d4edda; padding: 20px; border-radius: 10px;">
                            <h4 style="color: #155724; margin: 0;">Bonjour ${data.name}!</h4>
                            <p style="margin: 10px 0 0 0;">Vous avez <strong style="font-size: 1.5rem; color: #ffb600;">${data.balance}</strong> points</p>
                            <p style="margin: 5px 0 0 0;">Niveau: ${data.level.icon} ${data.level.name}</p>
                        </div>
                    `;
                    document.getElementById('displayBalance').textContent = data.balance;
                } else {
                    resultDiv.innerHTML = `<div style="background: #f8d7da; padding: 15px; border-radius: 10px; color: #721c24;">${data.error}</div>`;
                }
            });
    });
    </script>
</body>
</html>
