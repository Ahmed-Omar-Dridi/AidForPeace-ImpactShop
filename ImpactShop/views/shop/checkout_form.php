<?php
$customerData = $_SESSION['customer_data'] ?? [];
unset($_SESSION['customer_data']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .error-field {
            border-color: #e74c3c !important;
            background-color: #fee !important;
        }
        .error-message {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        .success-check {
            color: #52c41a;
            margin-left: 5px;
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
            <section id="customer-form-section">
                <h1>Informations de Paiement</h1>

                <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                    <div class="alert alert-error">
                        <h4><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</h4>
                        <ul>
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-times-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form id="customer-form" method="POST" action="index.php?controller=order&action=processPayment" novalidate>
                    <input type="hidden" name="cart_items" id="cart_items_hidden">

                    <!-- Informations Personnelles -->
                    <h3 style="color: var(--primary-dark); margin-bottom: 20px;">
                        <i class="fas fa-user"></i> Informations Personnelles
                    </h3>

                    <div class="form-group">
                        <label for="first-name">Prénom *</label>
                        <input 
                            type="text" 
                            id="first-name" 
                            name="first_name" 
                            value="<?php echo htmlspecialchars($customerData['first_name'] ?? ''); ?>"
                            placeholder="Jean"
                            data-validation="text"
                            data-min="2"
                            data-max="50">
                        <span class="error-message" id="error-first-name">Le prénom doit contenir entre 2 et 50 caractères</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="last-name">Nom *</label>
                        <input 
                            type="text" 
                            id="last-name" 
                            name="last_name" 
                            value="<?php echo htmlspecialchars($customerData['last_name'] ?? ''); ?>"
                            placeholder="Dupont"
                            data-validation="text"
                            data-min="2"
                            data-max="50">
                        <span class="error-message" id="error-last-name">Le nom doit contenir entre 2 et 50 caractères</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Adresse Email *</label>
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            value="<?php echo htmlspecialchars($customerData['email'] ?? ''); ?>"
                            placeholder="jean.dupont@example.com"
                            data-validation="email">
                        <small class="form-help">Nous vous enverrons une confirmation à cette adresse</small>
                        <span class="error-message" id="error-email">Veuillez entrer une adresse email valide</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Numéro de Téléphone *</label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            value="<?php echo htmlspecialchars($customerData['phone'] ?? ''); ?>"
                            placeholder="+216 XX XXX XXX"
                            data-validation="phone"
                            data-min="8"
                            data-max="20">
                        <small class="form-help">Format: +216 12 345 678 ou 12345678</small>
                        <span class="error-message" id="error-phone">Le numéro de téléphone doit contenir au moins 8 chiffres</span>
                    </div>

                    <!-- Adresse de Livraison -->
                    <h3 style="color: var(--primary-dark); margin: 30px 0 20px;">
                        <i class="fas fa-map-marker-alt"></i> Adresse de Livraison
                    </h3>

                    <div class="form-group">
                        <label for="address">Adresse Complète *</label>
                        <input 
                            type="text" 
                            id="address" 
                            name="address" 
                            value="<?php echo htmlspecialchars($customerData['address'] ?? ''); ?>"
                            placeholder="123 Rue de la Paix"
                            data-validation="text"
                            data-min="10"
                            data-max="200">
                        <span class="error-message" id="error-address">L'adresse doit contenir entre 10 et 200 caractères</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="city">Ville *</label>
                            <input 
                                type="text" 
                                id="city" 
                                name="city" 
                                value="<?php echo htmlspecialchars($customerData['city'] ?? ''); ?>"
                                placeholder="Tunis"
                                data-validation="text"
                                data-min="2"
                                data-max="50">
                            <span class="error-message" id="error-city">La ville doit contenir entre 2 et 50 caractères</span>
                        </div>

                        <div class="form-group">
                            <label for="postal-code">Code Postal *</label>
                            <input 
                                type="text" 
                                id="postal-code" 
                                name="postal_code" 
                                value="<?php echo htmlspecialchars($customerData['postal_code'] ?? ''); ?>"
                                placeholder="1000"
                                data-validation="postal"
                                data-min="4"
                                data-max="10">
                            <span class="error-message" id="error-postal-code">Le code postal doit contenir entre 4 et 10 caractères</span>
                        </div>
                    </div>

                    <!-- Résumé du panier -->
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 30px 0;">
                        <h3 style="margin-bottom: 15px; color: var(--primary-dark);">
                            <i class="fas fa-shopping-cart"></i> Résumé de votre commande
                        </h3>
                        <div id="order-summary"></div>
                        <div style="border-top: 2px dashed #ddd; margin-top: 15px; padding-top: 15px; display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: 700;">
                            <span>Total:</span>
                            <span style="color: var(--accent);">$<span id="total-display">0.00</span></span>
                        </div>
                    </div>
                    
                    <!-- Méthode de Paiement -->
                    <div class="payment-section">
                        <h3><i class="fas fa-credit-card"></i> Méthode de Paiement</h3>
                        <div class="paypal-option">
                            <div class="paypal-logo">
                                <i class="fab fa-paypal"></i>
                                <span>PayPal</span>
                            </div>
                            <div>
                                <p style="margin: 0; font-weight: 600;">Paiement sécurisé avec PayPal</p>
                                <small style="color: #666;">Vos données sont protégées avec un cryptage SSL</small>
                            </div>
                        </div>
                    </div>

                    <!-- Conditions d'utilisation -->
                    <div class="form-group" style="margin-top: 20px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input 
                                type="checkbox" 
                                id="terms" 
                                name="terms"
                                style="width: auto; margin-right: 10px;"
                                data-validation="checkbox">
                            <span>J'accepte les <a href="#" style="color: var(--primary);">conditions d'utilisation</a> et la <a href="#" style="color: var(--primary);">politique de confidentialité</a> *</span>
                        </label>
                        <span class="error-message" id="error-terms">Vous devez accepter les conditions d'utilisation</span>
                    </div>
                    
                    <div class="form-actions">
                        <a href="index.php?controller=product&action=shop" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la Boutique
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="fab fa-paypal"></i> Confirmer et Payer ($<span id="total-btn">0.00</span>)
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script src="assets/js/checkout-validation.js"></script>
</body>
</html>