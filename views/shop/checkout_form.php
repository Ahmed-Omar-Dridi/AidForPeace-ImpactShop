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
                        <h4>Erreurs de validation :</h4>
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
                        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form id="customer-form" method="POST" action="index.php?controller=order&action=processPayment">
                    <input type="hidden" name="cart_items" id="cart_items_hidden">

                    <div class="form-group">
                        <label for="first-name">Prénom *</label>
                        <input 
                            type="text" 
                            id="first-name" 
                            name="first_name" 
                            value="<?php echo htmlspecialchars($customerData['first_name'] ?? ''); ?>"
                            placeholder="Jean">
                    </div>
                    
                    <div class="form-group">
                        <label for="last-name">Nom *</label>
                        <input 
                            type="text" 
                            id="last-name" 
                            name="last_name" 
                            value="<?php echo htmlspecialchars($customerData['last_name'] ?? ''); ?>"
                            placeholder="Dupont">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Adresse Email *</label>
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            value="<?php echo htmlspecialchars($customerData['email'] ?? ''); ?>"
                            placeholder="jean.dupont@example.com">
                        <small class="form-help">Nous vous enverrons une confirmation à cette adresse</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Numéro de Téléphone *</label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            value="<?php echo htmlspecialchars($customerData['phone'] ?? ''); ?>"
                            placeholder="+216 XX XXX XXX">
                    </div>

                    <!-- Résumé du panier -->
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                        <h3 style="margin-bottom: 15px;">Résumé de votre commande</h3>
                        <div id="order-summary"></div>
                        <div style="border-top: 2px dashed #ddd; margin-top: 15px; padding-top: 15px; display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: 700;">
                            <span>Total:</span>
                            <span style="color: var(--accent);">$<span id="total-display">0.00</span></span>
                        </div>
                    </div>
                    
                    <div class="payment-section">
                        <h3>Méthode de Paiement</h3>
                        <div class="paypal-option">
                            <div class="paypal-logo">
                                <i class="fab fa-paypal"></i>
                                <span>PayPal</span>
                            </div>
                            <p>Paiement sécurisé avec PayPal</p>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="index.php?controller=product&action=shop" class="btn btn-secondary">Retour à la Boutique</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fab fa-paypal"></i> Payer avec PayPal
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script>
        // Récupérer le panier depuis localStorage
        const cart = JSON.parse(localStorage.getItem('impactshop_cart')) || [];

        // Vérifier que le panier n'est pas vide
        if (cart.length === 0) {
            alert('Votre panier est vide !');
            window.location.href = 'index.php?controller=product&action=shop';
        }

        // Remplir le champ caché avec les items du panier
        document.getElementById('cart_items_hidden').value = JSON.stringify(cart);

        // Afficher le résumé de la commande
        let summaryHTML = '';
        let total = 0;

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            
            summaryHTML += `
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">
                    <div>
                        <strong>${item.name}</strong><br>
                        <small style="color: #666;">Quantité: ${item.quantity} × $${item.price.toFixed(2)}</small>
                    </div>
                    <div style="font-weight: 700;">$${subtotal.toFixed(2)}</div>
                </div>
            `;
        });

        document.getElementById('order-summary').innerHTML = summaryHTML;
        document.getElementById('total-display').textContent = total.toFixed(2);

        // Validation du formulaire
        document.getElementById('customer-form').addEventListener('submit', function(e) {
            const firstName = document.getElementById('first-name').value.trim();
            const lastName = document.getElementById('last-name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();

            let errors = [];

            if (firstName.length < 2) {
                errors.push('Le prénom doit contenir au moins 2 caractères.');
            }

            if (lastName.length < 2) {
                errors.push('Le nom doit contenir au moins 2 caractères.');
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                errors.push('L\'adresse email n\'est pas valide.');
            }

            if (phone.length < 8) {
                errors.push('Le numéro de téléphone doit contenir au moins 8 caractères.');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Erreurs:\n- ' + errors.join('\n- '));
                return false;
            }
        });
    </script>
</body>
</html>