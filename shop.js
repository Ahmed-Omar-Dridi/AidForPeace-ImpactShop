/**
 * JavaScript pour la gestion de la boutique (FrontOffice)
 * Version ultra-optimisée - Pas de lag
 */

let cart = [];
let currentLang = 'fr';
let cartCache = null;

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Charger le panier depuis localStorage UNE SEULE FOIS
    cart = JSON.parse(localStorage.getItem('impactshop_cart')) || [];
    updateCartBadge();
    
    // PAS d'animation au chargement pour éviter le lag
    // Les cartes sont déjà visibles dans le CSS
});

/**
 * Ajouter un produit au panier
 */
function addToCart(id, name, price, imgName) {
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            imgName: imgName,
            quantity: 1
        });
    }
    
    saveCart();
    updateCartBadge();
    showNotification(`"${name}" ajouté au panier !`);
}

/**
 * Retirer un produit du panier
 */
function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    saveCart();
    updateCartBadge();
    displayCart();
}

/**
 * Mettre à jour la quantité d'un produit
 */
function updateQuantity(id, change) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            saveCart();
            displayCart();
        }
    }
}

/**
 * Sauvegarder le panier dans localStorage
 */
function saveCart() {
    localStorage.setItem('impactshop_cart', JSON.stringify(cart));
}

/**
 * Mettre à jour le badge du panier (OPTIMISÉ)
 */
function updateCartBadge() {
    const badge = document.getElementById('cart-badge');
    if (badge) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        badge.textContent = totalItems;
    }
}

/**
 * Afficher le panier
 */
function showCart() {
    const shopSection = document.getElementById('shop-section');
    const checkoutSection = document.getElementById('checkout-section');
    
    if (shopSection) shopSection.classList.add('hidden');
    if (checkoutSection) checkoutSection.classList.remove('hidden');
    
    displayCart();
}

/**
 * Retour à la boutique
 */
function showShop() {
    const shopSection = document.getElementById('shop-section');
    const checkoutSection = document.getElementById('checkout-section');
    
    if (checkoutSection) checkoutSection.classList.add('hidden');
    if (shopSection) shopSection.classList.remove('hidden');
}

/**
 * Afficher le contenu du panier
 */
function displayCart() {
    const cartItemsContainer = document.getElementById('cart-items');
    const totalAmountElement = document.getElementById('total-amount');
    
    if (!cartItemsContainer || !totalAmountElement) return;
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc;"></i>
                <p style="margin-top: 20px; color: #666;">Votre panier est vide</p>
            </div>
        `;
        totalAmountElement.textContent = '0.00';
        return;
    }
    
    let html = '';
    let total = 0;
    
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        html += `
            <div class="checkout-item">
                <img src="assets/images/${item.imgName}" 
                     alt="${item.name}" 
                     loading="lazy"
                     onerror="this.src='https://via.placeholder.com/80x80/2d6a4f/ffffff?text=Image'">
                <div class="checkout-item-info">
                    <div class="checkout-item-name">${item.name}</div>
                    <div style="color: #666;">${item.price.toFixed(2)} x ${item.quantity}</div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button onclick="updateQuantity(${item.id}, -1)" class="btn btn-secondary" style="padding: 5px 10px;">-</button>
                    <span style="font-weight: 700; min-width: 30px; text-align: center;">${item.quantity}</span>
                    <button onclick="updateQuantity(${item.id}, 1)" class="btn btn-secondary" style="padding: 5px 10px;">+</button>
                    <button onclick="removeFromCart(${item.id})" class="btn btn-danger" style="padding: 5px 10px; margin-left: 10px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div style="font-weight: 700; color: var(--accent);">${itemTotal.toFixed(2)}</div>
            </div>
        `;
    });
    
    cartItemsContainer.innerHTML = html;
    totalAmountElement.textContent = total.toFixed(2);
}

/**
 * Procéder au paiement
 */
function proceedToCheckout() {
    if (cart.length === 0) {
        alert('Votre panier est vide !');
        return;
    }
    
    // Rediriger vers le formulaire de paiement
    window.location.href = 'index.php?controller=order&action=checkout';
}

/**
 * Changer la langue (OPTIMISÉ - pas d'animation)
 */
function toggleLang() {
    currentLang = currentLang === 'fr' ? 'en' : 'fr';
    const langButton = document.getElementById('lang-toggle');
    if (langButton) {
        langButton.textContent = currentLang === 'fr' ? 'EN' : 'FR';
    }
    
    // Mettre à jour tous les éléments avec data-en et data-fr
    const elements = document.querySelectorAll('[data-en][data-fr]');
    elements.forEach(el => {
        const text = currentLang === 'en' ? el.getAttribute('data-en') : el.getAttribute('data-fr');
        if (text) el.textContent = text;
    });
}

/**
 * Afficher une notification (VERSION LÉGÈRE)
 */
function showNotification(message) {
    // Supprimer l'ancienne notification si elle existe
    const oldNotif = document.querySelector('.notification');
    if (oldNotif) oldNotif.remove();
    
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #2d6a4f;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 9999;
        opacity: 0;
        transform: translateX(400px);
        transition: all 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Forcer le reflow pour déclencher l'animation
    notification.offsetHeight;
    
    // Afficher
    notification.style.opacity = '1';
    notification.style.transform = 'translateX(0)';
    
    // Masquer après 2 secondes
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}