<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ffb600;
            --secondary: #1e3149;
            --text-dark: #5a6570;
            --success: #27ae60;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }
        .navbar {
            background: var(--secondary);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo a {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 800;
        }
        .navbar .logo span { color: var(--primary); }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .navbar a:hover { color: var(--primary); }
        
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .contact-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .contact-header h1 {
            color: var(--secondary);
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .contact-header h1 i { color: var(--primary); }
        .contact-header p { color: var(--text-dark); }
        
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary);
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: var(--secondary);
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        .btn-submit:hover {
            background: #e6a500;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .contact-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #eee;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }
        .contact-info-item i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 10px;
        }
        .contact-info-item h4 {
            color: var(--secondary);
            margin-bottom: 5px;
        }
        .contact-info-item p {
            color: var(--text-dark);
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .contact-info {
                grid-template-columns: 1fr;
            }
            .contact-card {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php"><span>Impact</span>Shop</a>
        </div>
        <div>
            <a href="index.php?controller=product&action=shop"><i class="fas fa-store"></i> Boutique</a>
            <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="contact-card">
            <div class="contact-header">
                <h1><i class="fas fa-envelope"></i> Contactez-nous</h1>
                <p>Nous sommes la pour vous aider. Envoyez-nous un message!</p>
            </div>

            <form method="POST" action="index.php?controller=contact&action=send">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nom complet *</label>
                        <input type="text" id="name" name="name" required placeholder="Votre nom">
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required placeholder="votre@email.com">
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Sujet *</label>
                    <input type="text" id="subject" name="subject" required placeholder="Sujet de votre message">
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" required placeholder="Ecrivez votre message ici..."></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Envoyer le message
                </button>
            </form>

            <div class="contact-info">
                <div class="contact-info-item">
                    <i class="fas fa-phone"></i>
                    <h4>Telephone</h4>
                    <p>+216 XX XXX XXX</p>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-envelope"></i>
                    <h4>Email</h4>
                    <p>contact@impactshop.tn</p>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <h4>Adresse</h4>
                    <p>Tunis, Tunisie</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Widget -->
    <?php include __DIR__ . '/partials/chatbot.php'; ?>
</body>
</html>
