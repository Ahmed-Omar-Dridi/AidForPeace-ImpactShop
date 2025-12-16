<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faire un Don - ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/3d-effects.css">
    <style>
        :root {
            --primary: #ff6b35;
            --primary-dark: #e55a2b;
            --primary-light: #ff8c5a;
            --secondary: #1a1a2e;
            --secondary-light: #16213e;
            --accent: #00d9ff;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --success: #00c853;
            --danger: #ff1744;
            --light-bg: #f8f9fa;
            --white: #ffffff;
        }
        
        /* Navbar */
        .shop-navbar {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            height: 75px;
            border-bottom: 3px solid var(--primary);
        }
        .shop-navbar .logo a {
            text-decoration: none;
            font-size: 1.9rem;
            font-weight: 900;
            display: flex;
            align-items: center;
        }
        .shop-navbar .logo .impact {
            color: white;
            text-shadow: 0 2px 10px rgba(255,255,255,0.2);
        }
        .shop-navbar .logo .shop {
            color: var(--primary);
            text-shadow: 0 2px 15px rgba(255,107,53,0.5);
        }
        .shop-navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .shop-navbar .nav-links a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 12px 20px;
            border-radius: 12px;
            background: rgba(255,255,255,0.08);
            border: 2px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(10px);
        }
        .shop-navbar .nav-links a:hover,
        .shop-navbar .nav-links a.active {
            color: white;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255,107,53,0.4);
        }
        .shop-navbar .nav-links a i {
            color: var(--accent);
        }
        .shop-navbar .nav-links a:hover i,
        .shop-navbar .nav-links a.active i {
            color: white;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }
        
        .donation-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .donation-header {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            border-radius: 24px;
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(26,26,46,0.3);
        }
        
        .donation-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,182,0,0.1) 0%, transparent 70%);
            animation: rotate-3d 20s linear infinite;
            pointer-events: none;
        }
        
        .donation-header h1 {
            font-size: 3rem;
            color: var(--white);
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            animation: float-3d 6s ease-in-out infinite;
        }
        
        .donation-header .heart-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-right: 15px;
            animation: pulse-3d 2s ease-in-out infinite;
        }
        
        .donation-header p {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
            position: relative;
            z-index: 1;
        }
        
        /* Donation Options */
        .donation-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }
        
        .donation-card {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
        }
        
        .donation-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,182,0,0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }
        
        .donation-card:hover {
            transform: translateY(-15px) rotateX(5deg) rotateZ(2deg);
            box-shadow: 
                0 15px 40px rgba(0,0,0,0.15),
                0 0 30px rgba(255,182,0,0.2);
            border-color: var(--primary);
        }
        
        .donation-card:hover::before {
            opacity: 1;
        }
        
        .donation-card.selected {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 15px 40px rgba(255,107,53,0.4);
        }
        
        .donation-amount {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--primary);
            margin: 15px 0;
        }
        
        .donation-card.selected .donation-amount {
            color: var(--secondary);
        }
        
        .donation-description {
            font-size: 0.9rem;
            color: var(--text-dark);
            margin-top: 10px;
        }
        
        .donation-card.selected .donation-description {
            color: var(--secondary);
        }
        
        /* Custom Amount */
        .custom-amount-section {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 50px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .custom-amount-section h3 {
            color: var(--secondary);
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .input-group input {
            flex: 1;
            padding: 15px;
            border: 2px solid var(--primary);
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .input-group input:focus {
            outline: none;
            box-shadow: 0 0 20px rgba(255,182,0,0.3);
        }
        
        .currency-label {
            font-weight: 700;
            color: var(--secondary);
            font-size: 1.1rem;
        }
        
        /* Impact Section */
        .impact-section {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 50px;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .impact-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,182,0,0.1) 0%, transparent 70%);
            animation: rotate-3d 20s linear infinite reverse;
            pointer-events: none;
        }
        
        .impact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            position: relative;
            z-index: 1;
        }
        
        .impact-item {
            text-align: center;
        }
        
        .impact-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
            animation: bounce-3d 1s ease-in-out infinite;
        }
        
        .impact-number {
            font-size: 2rem;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .impact-text {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.9);
        }
        
        /* Donation Form */
        .donation-form-section {
            background: var(--white);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 50px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--secondary);
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(255,182,0,0.2);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        /* Donation Button */
        .donate-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.2rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 35px rgba(255,107,53,0.4);
            position: relative;
            overflow: hidden;
        }
        
        .donate-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .donate-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 45px rgba(255,107,53,0.5);
        }
        
        .donate-btn:hover::before {
            left: 100%;
        }
        
        .donate-btn:active {
            transform: translateY(-1px);
        }
        
        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .payment-method {
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--white);
        }
        
        .payment-method:hover {
            border-color: var(--primary);
            background: rgba(255,182,0,0.05);
        }
        
        .payment-method.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, rgba(255,182,0,0.1) 0%, rgba(255,182,0,0.05) 100%);
            box-shadow: 0 0 20px rgba(255,182,0,0.2);
        }
        
        .payment-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .payment-name {
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.9rem;
        }
        
        /* Testimonials */
        .testimonials-section {
            margin-bottom: 50px;
        }
        
        .section-title {
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 30px;
            text-align: center;
            font-weight: 800;
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .testimonial-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary);
            position: relative;
            overflow: hidden;
        }
        
        .testimonial-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,182,0,0.05) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }
        
        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .testimonial-card:hover::before {
            opacity: 1;
        }
        
        .testimonial-content {
            position: relative;
            z-index: 1;
        }
        
        .testimonial-text {
            color: var(--text-dark);
            margin-bottom: 15px;
            font-style: italic;
            line-height: 1.6;
        }
        
        .testimonial-author {
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 5px;
        }
        
        .testimonial-role {
            font-size: 0.85rem;
            color: var(--primary);
        }
        
        .stars {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        /* Footer */
        .donation-footer {
            text-align: center;
            padding: 30px;
            background: var(--secondary);
            color: var(--white);
            border-radius: 15px;
            margin-top: 50px;
        }
        
        .donation-footer p {
            margin-bottom: 10px;
        }
        
        .donation-footer i {
            color: var(--primary);
            margin-right: 8px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .donation-header h1 {
                font-size: 2rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .donation-options {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .impact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="shop-navbar">
        <div class="logo">
            <a href="index.php"><span class="impact">Impact</span><span class="shop">Shop</span></a>
        </div>
        <div class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
            <a href="index.php?controller=home&action=donation" class="active"><i class="fas fa-heart"></i> Faire un don</a>
            <a href="index.php?controller=product&action=shop"><i class="fas fa-store"></i> Boutique</a>
            <a href="index.php?controller=map&action=index"><i class="fas fa-globe-africa"></i> Map</a>
            <a href="index.php?controller=messagerie&action=index"><i class="fas fa-comments"></i> Messagerie</a>
        </div>
    </nav>

    <div class="donation-container">
        
        <!-- Header -->
        <div class="donation-header">
            <h1><i class="fas fa-heart heart-icon"></i>Faire un Don</h1>
            <p>Votre générosité change des vies - Chaque don compte!</p>
        </div>
        
        <!-- Quick Donation Options -->
        <h2 class="section-title"><i class="fas fa-hand-holding-heart"></i> Montants Suggérés</h2>
        <div class="donation-options">
            <div class="donation-card" onclick="selectDonation(10)">
                <div class="donation-amount">10 TND</div>
                <div class="donation-description">Aide un enfant</div>
            </div>
            <div class="donation-card" onclick="selectDonation(25)">
                <div class="donation-amount">25 TND</div>
                <div class="donation-description">Repas pour 5</div>
            </div>
            <div class="donation-card" onclick="selectDonation(50)">
                <div class="donation-amount">50 TND</div>
                <div class="donation-description">Fournitures scolaires</div>
            </div>
            <div class="donation-card" onclick="selectDonation(100)">
                <div class="donation-amount">100 TND</div>
                <div class="donation-description">Soins médicaux</div>
            </div>
            <div class="donation-card" onclick="selectDonation(250)">
                <div class="donation-amount">250 TND</div>
                <div class="donation-description">Formation professionnelle</div>
            </div>
            <div class="donation-card" onclick="selectDonation(500)">
                <div class="donation-amount">500 TND</div>
                <div class="donation-description">Projet communautaire</div>
            </div>
        </div>
        
        <!-- Custom Amount -->
        <div class="custom-amount-section">
            <h3><i class="fas fa-edit"></i> Montant Personnalisé</h3>
            <div class="input-group">
                <input type="number" id="customAmount" placeholder="Entrez votre montant" min="1">
                <span class="currency-label">TND</span>
                <button class="donate-btn" style="width: auto; padding: 15px 30px;" onclick="selectCustomDonation()">
                    Sélectionner
                </button>
            </div>
        </div>
        
        <!-- Impact Section -->
        <div class="impact-section">
            <h2 style="text-align: center; margin-bottom: 40px; font-size: 2rem;">
                <i class="fas fa-star" style="color: var(--primary); margin-right: 10px;"></i>Votre Impact
            </h2>
            <div class="impact-grid">
                <div class="impact-item">
                    <div class="impact-icon"><i class="fas fa-child"></i></div>
                    <div class="impact-number">5,000+</div>
                    <div class="impact-text">Enfants aidés</div>
                </div>
                <div class="impact-item">
                    <div class="impact-icon"><i class="fas fa-book"></i></div>
                    <div class="impact-number">1,200+</div>
                    <div class="impact-text">Bourses d'études</div>
                </div>
                <div class="impact-item">
                    <div class="impact-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="impact-number">800+</div>
                    <div class="impact-text">Soins médicaux</div>
                </div>
                <div class="impact-item">
                    <div class="impact-icon"><i class="fas fa-home"></i></div>
                    <div class="impact-number">300+</div>
                    <div class="impact-text">Familles soutenues</div>
                </div>
            </div>
        </div>
        
        <!-- Donation Form -->
        <div class="donation-form-section">
            <h2 class="section-title"><i class="fas fa-credit-card"></i> Informations de Donation</h2>
            
            <form id="donationForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Prénom *</label>
                        <input type="text" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="lastName" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="phone">
                </div>
                
                <div class="form-group">
                    <label>Montant à Donner *</label>
                    <input type="number" id="donationAmount" name="amount" placeholder="0.00" min="1" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Méthode de Paiement *</label>
                    <div class="payment-methods">
                        <div class="payment-method selected" onclick="selectPayment(this, 'paypal')">
                            <div class="payment-icon"><i class="fab fa-paypal"></i></div>
                            <div class="payment-name">PayPal</div>
                        </div>
                        <div class="payment-method" onclick="selectPayment(this, 'card')">
                            <div class="payment-icon"><i class="fas fa-credit-card"></i></div>
                            <div class="payment-name">Carte Bancaire</div>
                        </div>
                        <div class="payment-method" onclick="selectPayment(this, 'bank')">
                            <div class="payment-icon"><i class="fas fa-university"></i></div>
                            <div class="payment-name">Virement</div>
                        </div>
                        <div class="payment-method" onclick="selectPayment(this, 'mobile')">
                            <div class="payment-icon"><i class="fas fa-mobile-alt"></i></div>
                            <div class="payment-name">Mobile Money</div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="newsletter" checked>
                        <span style="margin-left: 8px; color: var(--text-dark);">Recevoir nos mises à jour</span>
                    </label>
                </div>
                
                <button type="submit" class="donate-btn">
                    <i class="fas fa-heart"></i> Faire un Don Maintenant
                </button>
            </form>
        </div>
        
        <!-- Testimonials -->
        <div class="testimonials-section">
            <h2 class="section-title"><i class="fas fa-quote-left"></i> Témoignages</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="testimonial-text">
                            "Grâce à votre aide, mon fils a pu continuer ses études. Merci infiniment!"
                        </div>
                        <div class="testimonial-author">Fatima Ben Ali</div>
                        <div class="testimonial-role">Mère de famille</div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="testimonial-text">
                            "Votre organisation a changé ma vie. J'ai reçu une formation et maintenant j'ai un emploi!"
                        </div>
                        <div class="testimonial-author">Mohamed Saïd</div>
                        <div class="testimonial-role">Bénéficiaire</div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="testimonial-text">
                            "Je suis fier de soutenir une cause qui fait vraiment la différence dans notre communauté."
                        </div>
                        <div class="testimonial-author">Ahmed Khaled</div>
                        <div class="testimonial-role">Donateur régulier</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="donation-footer">
            <p><i class="fas fa-heart"></i> Merci de votre générosité!</p>
            <p>Chaque don est sécurisé et transparent - 100% de votre contribution aide nos bénéficiaires</p>
            <p style="margin-top: 20px; font-size: 0.9rem; opacity: 0.8;">
                ImpactShop © 2025 - Organisme d'aide humanitaire
            </p>
        </div>
        
    </div>
    
    <!-- 3D Effects Script -->
    <script src="assets/js/3d-effects.js"></script>
    <script src="assets/js/donation-widget.js"></script>
    
    <script>
        let selectedAmount = 0;
        let selectedPayment = 'paypal';
        
        function selectDonation(amount) {
            selectedAmount = amount;
            document.getElementById('donationAmount').value = amount;
            
            // Update UI
            document.querySelectorAll('.donation-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.target.closest('.donation-card').classList.add('selected');
            
            // Scroll to form
            document.querySelector('.donation-form-section').scrollIntoView({ behavior: 'smooth' });
        }
        
        function selectCustomDonation() {
            const amount = document.getElementById('customAmount').value;
            if (amount && amount > 0) {
                selectedAmount = parseFloat(amount);
                document.getElementById('donationAmount').value = amount;
                document.querySelector('.donation-form-section').scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        function selectPayment(element, method) {
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('selected');
            });
            element.classList.add('selected');
            selectedPayment = method;
        }
        
        document.getElementById('donationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                firstName: this.firstName.value,
                lastName: this.lastName.value,
                email: this.email.value,
                phone: this.phone.value,
                amount: this.amount.value,
                paymentMethod: selectedPayment,
                newsletter: this.newsletter.checked
            };
            
            console.log('Donation submitted:', formData);
            alert('Merci pour votre don de ' + formData.amount + ' TND! Vous serez redirigé vers le paiement.');
            // Here you would redirect to payment gateway
        });
    </script>
</body>
</html>
