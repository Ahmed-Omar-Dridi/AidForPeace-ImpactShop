<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zones de Livraison - ImpactShop</title>
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
        .zones-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .zones-header { text-align: center; margin-bottom: 40px; }
        .zones-header h1 { color: var(--secondary); font-size: 2.5rem; margin-bottom: 10px; font-weight: 900; }
        .zones-header h1 i { color: var(--primary); filter: drop-shadow(0 2px 10px rgba(255,107,53,0.4)); }
        .zones-header p { color: #636e72; font-size: 1.1rem; }
        
        .free-shipping-banner { background: linear-gradient(135deg, var(--success), #00e676); color: white; padding: 25px; border-radius: 20px; text-align: center; margin-bottom: 40px; box-shadow: 0 10px 40px rgba(0,200,83,0.3); }
        .free-shipping-banner h3 { margin: 0; font-size: 1.5rem; font-weight: 800; }
        .free-shipping-banner p { margin: 10px 0 0 0; opacity: 0.95; }
        
        .zones-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-bottom: 40px; }
        
        .zone-card { background: white; border-radius: 20px; padding: 28px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-top: 4px solid var(--primary); }
        .zone-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(0,0,0,0.12); }
        .zone-card-header { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f1f3f4; }
        .zone-icon { width: 55px; height: 55px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; box-shadow: 0 8px 20px rgba(255,107,53,0.3); }
        .zone-card-header h3 { margin: 0; color: var(--secondary); font-weight: 800; }
        
        .zone-info { margin-bottom: 15px; }
        .zone-info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f3f4; }
        .zone-info-row:last-child { border-bottom: none; }
        .zone-info-label { color: #636e72; }
        .zone-info-value { font-weight: 700; color: var(--secondary); }
        .zone-price { color: var(--primary); font-size: 1.4rem; font-weight: 900; }
        
        .zone-cities { margin-top: 15px; }
        .zone-cities h4 { color: var(--secondary); margin-bottom: 10px; font-size: 0.9rem; font-weight: 700; }
        .cities-list { display: flex; flex-wrap: wrap; gap: 8px; }
        .city-tag { background: #f8fafc; padding: 6px 14px; border-radius: 25px; font-size: 0.85rem; color: #636e72; transition: all 0.3s; }
        .city-tag:hover { background: var(--primary); color: white; }
        
        .calculator-section { background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); margin-bottom: 40px; border-top: 4px solid var(--accent); }
        .calculator-section h3 { color: var(--secondary); margin-bottom: 25px; font-weight: 800; }
        .calculator-section h3 i { color: var(--accent); }
        .calc-form { display: flex; gap: 15px; flex-wrap: wrap; }
        .calc-form select, .calc-form input { flex: 1; min-width: 200px; padding: 16px 20px; border: 2px solid #e8ecef; border-radius: 14px; font-size: 1rem; transition: all 0.3s; }
        .calc-form select:focus, .calc-form input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(0,217,255,0.15); }
        .calc-form button { padding: 16px 35px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; border: none; border-radius: 14px; font-weight: 800; cursor: pointer; transition: all 0.3s; box-shadow: 0 8px 25px rgba(255,107,53,0.35); }
        .calc-form button:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(255,107,53,0.45); }
        .calc-result { margin-top: 20px; padding: 25px; background: linear-gradient(135deg, #f8fafc, #e8ecef); border-radius: 14px; display: none; }
        
        .faq-section { background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
        .faq-section h3 { color: var(--secondary); margin-bottom: 25px; font-weight: 800; }
        .faq-item { padding: 18px 0; border-bottom: 1px solid #f1f3f4; }
        .faq-item:last-child { border-bottom: none; }
        .faq-question { font-weight: 800; color: var(--secondary); margin-bottom: 8px; }
        .faq-answer { color: #636e72; line-height: 1.7; }
    </style>
</head>
<body>
    <nav class="shop-navbar">
        <div class="logo"><a href="index.php"><span class="impact">Impact</span><span class="shop">Shop</span></a></div>
        <div class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
            <a href="index.php?controller=home&action=donation"><i class="fas fa-heart"></i> Faire un don</a>
            <a href="index.php?controller=product&action=shop"><i class="fas fa-store"></i> Boutique</a>
            <a href="index.php?controller=shipping&action=track"><i class="fas fa-truck"></i> Suivi</a>
        </div>
    </nav>

    <div class="zones-container">
        <div class="zones-header">
            <h1><i class="fas fa-map-marked-alt"></i> Zones de Livraison</h1>
            <p>Nous livrons dans toute la Tunisie! Découvrez les délais et tarifs par région.</p>
        </div>

        <!-- Bannière livraison gratuite -->
        <div class="free-shipping-banner">
            <h3><i class="fas fa-gift"></i> Livraison GRATUITE dès 100 TND d'achat!</h3>
            <p>Profitez de la livraison offerte sur toutes vos commandes de 100 TND ou plus.</p>
        </div>

        <!-- Calculateur -->
        <div class="calculator-section">
            <h3><i class="fas fa-calculator"></i> Calculer mes frais de livraison</h3>
            <form class="calc-form" id="calcForm">
                <select id="calcCity" required>
                    <option value="">-- Sélectionnez votre ville --</option>
                    <?php foreach ($zones as $key => $zone): ?>
                        <optgroup label="<?php echo $zone['name']; ?>">
                            <?php foreach ($zone['cities'] as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <input type="number" id="calcTotal" placeholder="Montant de la commande (TND)" min="0" step="0.01">
                <button type="submit"><i class="fas fa-search"></i> Calculer</button>
            </form>
            <div class="calc-result" id="calcResult"></div>
        </div>

        <!-- Grille des zones -->
        <div class="zones-grid">
            <?php foreach ($zones as $key => $zone): ?>
            <div class="zone-card">
                <div class="zone-card-header">
                    <div class="zone-icon"><i class="fas <?php echo $zone['icon']; ?>"></i></div>
                    <h3><?php echo $zone['name']; ?></h3>
                </div>
                <div class="zone-info">
                    <div class="zone-info-row">
                        <span class="zone-info-label"><i class="fas fa-clock"></i> Délai</span>
                        <span class="zone-info-value"><?php echo $zone['delay']; ?></span>
                    </div>
                    <div class="zone-info-row">
                        <span class="zone-info-label"><i class="fas fa-tag"></i> Tarif</span>
                        <span class="zone-info-value zone-price"><?php echo number_format($zone['cost'], 2); ?> TND</span>
                    </div>
                </div>
                <div class="zone-cities">
                    <h4><i class="fas fa-map-pin"></i> Villes couvertes:</h4>
                    <div class="cities-list">
                        <?php foreach ($zone['cities'] as $city): ?>
                            <span class="city-tag"><?php echo htmlspecialchars($city); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- FAQ -->
        <div class="faq-section">
            <h3><i class="fas fa-question-circle"></i> Questions Fréquentes</h3>
            
            <div class="faq-item">
                <div class="faq-question">Comment suivre ma livraison?</div>
                <div class="faq-answer">Vous recevrez un code de suivi par email après l'expédition. Utilisez-le sur notre <a href="index.php?controller=shipping&action=track">page de suivi</a>.</div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Puis-je modifier mon adresse de livraison?</div>
                <div class="faq-answer">Contactez-nous rapidement via le <a href="index.php?controller=contact&action=index">formulaire de contact</a> si votre commande n'a pas encore été expédiée.</div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Que faire si je ne suis pas disponible à la livraison?</div>
                <div class="faq-answer">Le livreur vous contactera pour convenir d'un nouveau créneau. Vous pouvez aussi indiquer un voisin ou gardien.</div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">Livrez-vous à l'international?</div>
                <div class="faq-answer">Actuellement, nous livrons uniquement en Tunisie. La livraison internationale sera bientôt disponible!</div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('calcForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const city = document.getElementById('calcCity').value;
        const total = document.getElementById('calcTotal').value || 0;
        const resultDiv = document.getElementById('calcResult');
        
        fetch(`index.php?controller=shipping&action=calculateCost&city=${encodeURIComponent(city)}&total=${total}`)
            .then(r => r.json())
            .then(data => {
                resultDiv.style.display = 'block';
                if (data.success) {
                    if (data.free_shipping) {
                        resultDiv.innerHTML = `
                            <div style="text-align: center;">
                                <i class="fas fa-gift" style="font-size: 3rem; color: #27ae60;"></i>
                                <h3 style="color: #27ae60; margin: 15px 0;">Livraison GRATUITE!</h3>
                                <p>Votre commande dépasse 100 TND, la livraison est offerte!</p>
                                <p><strong>Zone:</strong> ${data.zone} | <strong>Délai:</strong> ${data.delay}</p>
                            </div>
                        `;
                    } else {
                        resultDiv.innerHTML = `
                            <div style="text-align: center;">
                                <i class="fas fa-truck" style="font-size: 3rem; color: #ffb600;"></i>
                                <h3 style="color: #1e3149; margin: 15px 0;">Frais de livraison: <span style="color: #ffb600;">${data.cost.toFixed(2)} TND</span></h3>
                                <p><strong>Zone:</strong> ${data.zone} | <strong>Délai:</strong> ${data.delay}</p>
                                <p style="color: #666; margin-top: 10px;">💡 Ajoutez ${(100 - total).toFixed(2)} TND pour bénéficier de la livraison gratuite!</p>
                            </div>
                        `;
                    }
                }
            });
    });
    </script>
</body>
</html>
