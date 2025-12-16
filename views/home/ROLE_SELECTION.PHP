<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactShop - Selection du Role</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ffb600;
            --primary-dark: #e6a500;
            --secondary: #1e3149;
            --secondary-dark: #15202e;
            --text-dark: #5a6570;
            --heading-color: #282f39;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 50%, var(--primary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .role-container {
            width: 100%;
            max-width: 1000px;
        }
        
        .role-box {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .role-box h1 {
            text-align: center;
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }
        
        .role-box h1 span {
            color: var(--primary);
        }
        
        .role-box > p {
            text-align: center;
            color: var(--text-dark);
            font-size: 1.1rem;
            margin-bottom: 40px;
        }
        
        .choice-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .choice-card {
            background: white;
            border: 3px solid var(--secondary);
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .choice-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            border-color: var(--primary);
            background: var(--secondary);
        }
        
        .choice-card h2 {
            font-size: 1.8rem;
            color: var(--secondary);
            margin-bottom: 15px;
            transition: color 0.3s;
        }
        
        .choice-card:hover h2 {
            color: white;
        }
        
        .choice-card p {
            color: var(--text-dark);
            font-size: 1rem;
            transition: color 0.3s;
        }
        
        .choice-card:hover p {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .section-title {
            text-align: center;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--secondary);
            margin: 30px 0 20px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }
        
        .section-title i {
            color: var(--primary);
            margin-right: 10px;
        }
        
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .quick-link {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px 15px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .quick-link:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background: white;
        }
        
        .quick-link i {
            font-size: 1.8rem;
            color: var(--secondary);
            margin-bottom: 10px;
            display: block;
        }
        
        .quick-link:hover i {
            color: var(--primary);
        }
        
        .quick-link span {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--secondary);
            display: block;
        }
        
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }
        
        .admin-link {
            background: var(--secondary);
            border-radius: 10px;
            padding: 15px 10px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .admin-link:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .admin-link i {
            font-size: 1.3rem;
            color: white;
            margin-bottom: 8px;
            display: block;
        }
        
        .admin-link:hover i {
            color: var(--secondary);
        }
        
        .admin-link span {
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            display: block;
        }
        
        .admin-link:hover span {
            color: var(--secondary);
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            color: var(--text-dark);
            font-size: 0.9rem;
        }
        
        .footer i {
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .choice-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .admin-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .role-box {
                padding: 30px 20px;
            }
            
            .role-box h1 {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .quick-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .admin-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="role-container">
        <div class="role-box">
            <h1><span>Impact</span>Shop</h1>
            <p>Veuillez selectionner votre role pour continuer</p>

            <div class="choice-grid">
                <a href="index.php?controller=product&action=shop" class="choice-card buyer-choice">
                    <h2><i class="fas fa-shopping-cart"></i> Acheteur</h2>
                    <p>Parcourir et acheter des produits d'aide humanitaire</p>
                </a>

                <a href="index.php?controller=dashboard&action=index" class="choice-card admin-choice">
                    <h2><i class="fas fa-cog"></i> Administrateur</h2>
                    <p>Gerer le catalogue et voir le dashboard</p>
                </a>
            </div>

            <h3 class="section-title">
                <i class="fas fa-bolt"></i>
                Acces Rapide - Client
            </h3>
            
            <div class="quick-grid">
                <a href="index.php?controller=product&action=shop" class="quick-link">
                    <i class="fas fa-store"></i>
                    <span>Boutique</span>
                </a>
                
                <a href="index.php?controller=order&action=checkout" class="quick-link">
                    <i class="fas fa-shopping-basket"></i>
                    <span>Commander</span>
                </a>
                
                <a href="index.php?controller=shipping&action=track" class="quick-link">
                    <i class="fas fa-truck"></i>
                    <span>Suivi Colis</span>
                </a>
                
                <a href="index.php?controller=category&action=index" class="quick-link">
                    <i class="fas fa-folder-open"></i>
                    <span>Categories</span>
                </a>
            </div>

            <h3 class="section-title">
                <i class="fas fa-shield-alt"></i>
                Administration
            </h3>
            
            <div class="admin-grid">
                <a href="index.php?controller=dashboard&action=index" class="admin-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="index.php?controller=product&action=index" class="admin-link">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
                
                <a href="index.php?controller=order&action=index" class="admin-link">
                    <i class="fas fa-receipt"></i>
                    <span>Commandes</span>
                </a>
                
                <a href="index.php?controller=category&action=index" class="admin-link">
                    <i class="fas fa-folder"></i>
                    <span>Categories</span>
                </a>
                
                <a href="index.php?controller=payment&action=index" class="admin-link">
                    <i class="fas fa-credit-card"></i>
                    <span>Paiements</span>
                </a>
                
                <a href="index.php?controller=shipping&action=index" class="admin-link">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Livraisons</span>
                </a>
                
                <a href="index.php?controller=review&action=index" class="admin-link">
                    <i class="fas fa-star"></i>
                    <span>Avis</span>
                </a>
            </div>

            <div class="footer">
                <p><i class="fas fa-heart"></i> Chaque achat compte - <strong>ImpactShop 2025</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
