<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laisser un Avis - ImpactShop</title>
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
            max-width: 700px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .review-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .review-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .review-header h1 {
            color: var(--secondary);
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .review-header h1 i { color: var(--primary); }
        .review-header p { color: var(--text-dark); }
        
        .product-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .product-info img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
        .product-info h3 {
            color: var(--secondary);
            margin-bottom: 5px;
        }
        .product-info p {
            color: var(--primary);
            font-weight: 700;
        }
        
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        /* Star Rating */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 5px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            cursor: pointer;
            font-size: 2rem;
            color: #ddd;
            transition: color 0.2s;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: var(--primary);
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
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: var(--secondary);
            text-decoration: none;
        }
        .back-link a:hover {
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .review-card {
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

        <div class="review-card">
            <div class="review-header">
                <h1><i class="fas fa-star"></i> Laisser un Avis</h1>
                <p>Partagez votre experience avec ce produit</p>
            </div>

            <?php if (isset($product) && $product): ?>
            <div class="product-info">
                <img src="../../assets/images/<?php echo htmlspecialchars($product['img_name'] ?? ''); ?>" 
                     alt="<?php echo htmlspecialchars($product['name_fr'] ?? ''); ?>"
                     onerror="this.src='https://via.placeholder.com/80x80?text=Produit'">
                <div>
                    <h3><?php echo htmlspecialchars($product['name_fr'] ?? ''); ?></h3>
                    <p><?php echo number_format($product['price'] ?? 0, 2); ?> TND</p>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" action="index.php?controller=review&action=store">
                <?php if (isset($product) && $product): ?>
                    <input type="hidden" name="product_id" value="<?php echo $product['id'] ?? 0; ?>">
                <?php else: ?>
                    <div class="form-group">
                        <label for="product_id">Produit *</label>
                        <select id="product_id" name="product_id" required>
                            <option value="">-- Selectionnez un produit --</option>
                            <?php
                            require_once __DIR__ . '/../../models/Product.php';
                            $allProducts = Product::findAll();
                            foreach ($allProducts as $p):
                            ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name_fr']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Prenom *</label>
                        <input type="text" id="first_name" name="first_name" required placeholder="Votre prenom">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Nom *</label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Votre nom">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required placeholder="votre@email.com">
                </div>

                <div class="form-group">
                    <label>Note *</label>
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5" required>
                        <label for="star5"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star4" name="rating" value="4">
                        <label for="star4"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star3" name="rating" value="3">
                        <label for="star3"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star2" name="rating" value="2">
                        <label for="star2"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star1" name="rating" value="1">
                        <label for="star1"><i class="fas fa-star"></i></label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title">Titre de l'avis *</label>
                    <input type="text" id="title" name="title" required placeholder="Ex: Excellent produit! ">
                </div>

                <div class="form-group">
                    <label for="comment">Votre commentaire *</label>
                    <textarea id="comment" name="comment" required placeholder="Decrivez votre experience avec ce produit..."></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Envoyer mon avis
                </button>
            </form>

            <div class="back-link">
                <a href="index.php?controller=product&action=shop"><i class="fas fa-arrow-left"></i> Retour a la boutique</a>
            </div>
        </div>
    </div>
</body>
</html>