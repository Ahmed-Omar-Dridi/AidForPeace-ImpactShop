<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de Commande - ImpactShop</title>
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
            <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
            <a href="index.php?controller=home&action=donation"><i class="fas fa-heart"></i> Faire un don</a>
            <a href="index.php?controller=product&action=shop"><i class="fas fa-store"></i> Boutique</a>
        </div>
    </nav>

    <div class="tracking-container">
        <div class="tracking-card">
            <div class="tracking-header">
                <i class="fas fa-truck"></i>
                <h1>Suivi de Commande</h1>
                <p>Entrez votre numero de suivi pour localiser votre colis</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="GET" class="tracking-form">
                <input type="hidden" name="controller" value="shipping">
                <input type="hidden" name="action" value="track">
                <div class="input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" name="tracking" placeholder="Ex: IMP-ABC123-XY01" required>
                </div>
                <button type="submit">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </form>

            <div class="tracking-help">
                <h3><i class="fas fa-question-circle"></i> Ou trouver mon numero de suivi? </h3>
                <ul>
                    <li><i class="fas fa-envelope"></i> Dans l'email de confirmation de commande</li>
                    <li><i class="fas fa-file-alt"></i> Sur votre facture</li>
                    <li><i class="fas fa-sms"></i> Dans le SMS de notification</li>
                </ul>
            </div>

            <div class="tracking-example">
                <p>Format du numero: <strong>IMP-XXXXXX-XXXX</strong></p>
            </div>
        </div>
    </div>

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
        .tracking-container {
            max-width: 650px;
            margin: 50px auto;
            padding: 0 20px;
        }
        
        .tracking-card {
            background: white;
            border-radius: 24px;
            padding: 55px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
            border-top: 5px solid var(--primary);
        }
        
        .tracking-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .tracking-header i {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 15px rgba(255,107,53,0.4));
            display: block;
        }
        
        .tracking-header h1 {
            color: var(--secondary);
            font-size: 2.2rem;
            margin-bottom: 12px;
            font-weight: 900;
        }
        
        .tracking-header p {
            color: #636e72;
            font-size: 1.05rem;
        }
        
        .tracking-form {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
        }
        
        .input-group {
            flex: 1;
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            font-size: 1.1rem;
        }
        
        .input-group input {
            width: 100%;
            padding: 18px 18px 18px 50px;
            border: 2px solid #e8ecef;
            border-radius: 14px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(0,217,255,0.15);
        }
        
        .tracking-form button {
            padding: 18px 35px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(255,107,53,0.35);
        }
        
        .tracking-form button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255,107,53,0.45);
        }
        
        .tracking-help {
            background: linear-gradient(135deg, #f8fafc, #e8ecef);
            border-radius: 18px;
            padding: 28px;
            margin-bottom: 20px;
            border-left: 4px solid var(--accent);
        }
        
        .tracking-help h3 {
            color: var(--secondary);
            font-size: 1rem;
            margin-bottom: 18px;
            font-weight: 800;
        }
        
        .tracking-help h3 i {
            color: var(--accent);
            margin-right: 10px;
        }
        
        .tracking-help ul {
            list-style: none;
            padding: 0;
        }
        
        .tracking-help li {
            padding: 12px 0;
            color: #636e72;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        . tracking-help li i {
            color: #1e3149;
            width: 20px;
        }
        
        . tracking-example {
            text-align: center;
            color: #999;
        }
        
        .tracking-example strong {
            color: #1e3149;
            font-family: monospace;
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        . alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .tracking-card {
                padding: 30px 20px;
            }
            
            . tracking-form {
                flex-direction: column;
            }
            
            .tracking-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>

    <!-- Chatbot Widget -->
    <?php include __DIR__ . '/../partials/chatbot.php'; ?>
</body>
</html>
