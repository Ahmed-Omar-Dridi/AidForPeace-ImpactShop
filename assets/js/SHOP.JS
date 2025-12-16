/**
 * ImpactShop - Shop JavaScript
 * Date: 2025-11-23 17:40:00
 * Author: dridi10331
 */

console.log('=== SHOP.JS LOADED - VERSION 2.0 ===');
console.log('Timestamp:', new Date().toISOString());

// Initialize cart
let cart = [];

// Load cart from localStorage
try {
    const savedCart = localStorage.getItem('cart');
    console.log('Saved cart from localStorage:', savedCart);
    
    if (savedCart) {
        cart = JSON.parse(savedCart);
        console.log('Cart parsed:', cart);
    }
} catch (error) {
    console.error('Error loading cart:', error);
}

/**
 * Add product to cart
 */
function addToCart(id, name, price, image) {
    console.log('=== ADD TO CART CALLED ===');
    console.log('Parameters:', { id, name, price, image });
    
    // Find existing item
    const existingIndex = cart.findIndex(item => item.id === id);
    
    if (existingIndex !== -1) {
        cart[existingIndex].quantity++;
        console.log('Increased quantity to:', cart[existingIndex].quantity);
    } else {
        const newItem = {
            id: id,
            name: name,
            price: parseFloat(price),
            image: image,
            quantity: 1
        };
        cart.push(newItem);
        console.log('Added new item:', newItem);
    }
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    console.log('Cart saved:', cart);
    
    // Update badge
    updateCartBadge();
    
    // Show notification
    showNotification('✅ Produit ajouté au panier!');
    
    return true;
}

/**
 * Update cart badge
 */
function updateCartBadge() {
    console.log('Updating badge...');
    const badge = document.getElementById('cart-badge');
    
    if (!badge) {
        console.error('Badge not found!');
        return;
    }
    
    const total = cart.reduce((sum, item) => sum + item.quantity, 0);
    console.log('Total items:', total);
    
    badge.textContent = total;
    badge.style.display = total > 0 ? 'inline-block' : 'none';
}

/**
 * Show cart
 */
function showCart() {
    console.log('=== SHOW CART ===');
    
    const shopSection = document.getElementById('shop-section');
    const checkoutSection = document.getElementById('checkout-section');
    
    if (!shopSection || !checkoutSection) {
        console.error('Sections not found!');
        return;
    }
    
    shopSection.classList.add('hidden');
    checkoutSection.classList.remove('hidden');
    
    displayCartItems();
}

/**
 * Show shop
 */
function showShop() {
    console.log('=== SHOW SHOP ===');
    
    const shopSection = document.getElementById('shop-section');
    const checkoutSection = document.getElementById('checkout-section');
    
    if (!shopSection || !checkoutSection) {
        console.error('Sections not found!');
        return;
    }
    
    checkoutSection.classList.add('hidden');
    shopSection.classList.remove('hidden');
}

/**
 * Display cart items
 */
function displayCartItems() {
    console.log('=== DISPLAY CART ITEMS ===');
    console.log('Cart:', cart);
    
    const container = document.getElementById('cart-items');
    const totalElement = document.getElementById('total-amount');
    
    if (!container) {
        console.error('Cart items container not found!');
        return;
    }
    
    // Reload from localStorage
    try {
        const savedCart = localStorage.getItem('cart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
        }
    } catch (error) {
        console.error('Error reloading cart:', error);
    }
    
    if (cart.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 60px;">
                <i class="fas fa-shopping-cart" style="font-size: 5rem; color: #ddd;"></i>
                <h3 style="color: #999; margin-top: 20px;">Votre panier est vide</h3>
                <p style="color: #bbb;">Ajoutez des produits depuis la boutique</p>
            </div>
        `;
        if (totalElement) totalElement.textContent = '0.00';
        return;
    }
    
    let html = '';
    let total = 0;
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        html += `
            <div style="display: flex; align-items: center; gap: 20px; padding: 20px; border-bottom: 1px solid #ebebeb;">
                <img src="assets/images/${item.image}" 
                     alt="${item.name}"
                     style="width: 100px; height: 100px; object-fit: cover;"
                     onerror="this.src='https://via.placeholder.com/100x100/1e3149/ffb600?text=P'">
                <div style="flex-grow: 1;">
                    <h4 style="margin: 0 0 10px 0; font-weight: 700;">${item.name}</h4>
                    <p style="margin: 0; color: var(--primary-color); font-weight: 700;">$${item.price.toFixed(2)}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button onclick="decreaseQuantity(${index})" style="background: var(--secondary-color); color: white; border: none; width: 30px; height: 30px; cursor: pointer;">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span style="font-weight: 700; font-size: 1.2rem;">${item.quantity}</span>
                    <button onclick="increaseQuantity(${index})" style="background: var(--secondary-color); color: white; border: none; width: 30px; height: 30px; cursor: pointer;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div style="font-weight: 800; font-size: 1.3rem; color: var(--primary-color);">
                    $${itemTotal.toFixed(2)}
                </div>
                <button onclick="removeFromCart(${index})" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; cursor: pointer;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    });
    
    container.innerHTML = html;
    if (totalElement) totalElement.textContent = total.toFixed(2);
}

/**
 * Increase quantity
 */
function increaseQuantity(index) {
    if (cart[index]) {
        cart[index].quantity++;
        localStorage.setItem('cart', JSON.stringify(cart));
        displayCartItems();
        updateCartBadge();
    }
}

/**
 * Decrease quantity
 */
function decreaseQuantity(index) {
    if (cart[index]) {
        if (cart[index].quantity > 1) {
            cart[index].quantity--;
        } else {
            cart.splice(index, 1);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        displayCartItems();
        updateCartBadge();
    }
}

/**
 * Remove from cart
 */
function removeFromCart(index) {
    if (confirm('Supprimer cet article ?')) {
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        displayCartItems();
        updateCartBadge();
        showNotification('❌ Produit retiré');
    }
}

/**
 * Proceed to checkout
 */
function proceedToCheckout() {
    if (cart.length === 0) {
        alert('Votre panier est vide!');
        return;
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    window.location.href = 'index.php?controller=order&action=checkout';
}

/**
 * Show notification
 */
function showNotification(message) {
    const notif = document.createElement('div');
    notif.textContent = message;
    notif.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ffb600;
        color: #1e3149;
        padding: 15px 25px;
        border-radius: 5px;
        font-weight: 700;
        z-index: 10000;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    `;
    
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.remove();
    }, 3000);
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    updateCartBadge();
});

// Export functions
window.addToCart = addToCart;
window.showCart = showCart;
window.showShop = showShop;
window.increaseQuantity = increaseQuantity;
window.decreaseQuantity = decreaseQuantity;
window.removeFromCart = removeFromCart;
window.proceedToCheckout = proceedToCheckout;

console.log('=== SHOP.JS FULLY INITIALIZED ===');
console.log('Functions exported:', {
    addToCart: typeof addToCart,
    showCart: typeof showCart,
    proceedToCheckout: typeof proceedToCheckout
});