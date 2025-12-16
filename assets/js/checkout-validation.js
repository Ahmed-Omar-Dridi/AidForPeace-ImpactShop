/**
 * Validation Complete du Formulaire de Paiement
 * Controles de saisie js
 */

// Recuperer le panier depuis localStorage
const cart = JSON.parse(localStorage.getItem('impactshop_cart')) || [];

// Verifier que le panier n'est pas vide
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
document.getElementById('total-btn').textContent = total.toFixed(2);

// ===== VALIDATION EN TEMPS RÉEL =====

const form = document.getElementById('customer-form');
const fields = form.querySelectorAll('input[data-validation]');

// Règles de validation
const validationRules = {
    text: (value, min, max) => {
        const length = value.trim().length;
        return length >= (min || 2) && length <= (max || 100);
    },
    
    email: (value) => {
        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return regex.test(value.trim());
    },
    
    phone: (value, min) => {
        // Enlever tous les caractères non-numériques
        const numbers = value.replace(/\D/g, '');
        return numbers.length >= (min || 8) && numbers.length <= 20;
    },
    
    postal: (value, min, max) => {
        const cleaned = value.trim().replace(/\s/g, '');
        return cleaned.length >= (min || 4) && cleaned.length <= (max || 10);
    },
    
    checkbox: (element) => {
        return element.checked;
    }
};

// Fonction de validation d'un champ
function validateField(field) {
    const validationType = field.dataset.validation;
    const min = parseInt(field.dataset.min) || null;
    const max = parseInt(field.dataset.max) || null;
    const errorElement = document.getElementById(`error-${field.id}`);
    
    let isValid = false;
    
    if (validationType === 'checkbox') {
        isValid = validationRules.checkbox(field);
    } else {
        const value = field.value;
        isValid = validationRules[validationType](value, min, max);
    }
    
    // Afficher/masquer l'erreur
    if (isValid) {
        field.classList.remove('error-field');
        if (errorElement) {
            errorElement.classList.remove('show');
        }
        
        // Ajouter un check vert si le champ est valide
        removeCheckMark(field);
        if (field.value.trim() !== '') {
            addCheckMark(field);
        }
    } else {
        field.classList.add('error-field');
        if (errorElement) {
            errorElement.classList.add('show');
        }
        removeCheckMark(field);
    }
    
    return isValid;
}

// Ajouter une coche verte
function addCheckMark(field) {
    const check = document.createElement('i');
    check.className = 'fas fa-check-circle success-check';
    check.style.cssText = 'position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 1.2rem;';
    
    const parent = field.parentElement;
    parent.style.position = 'relative';
    parent.appendChild(check);
}

// Retirer la coche verte
function removeCheckMark(field) {
    const parent = field.parentElement;
    const existingCheck = parent.querySelector('.success-check');
    if (existingCheck) {
        existingCheck.remove();
    }
}

// Validation en temps réel
fields.forEach(field => {
    // Validation à la perte de focus
    field.addEventListener('blur', function() {
        validateField(this);
    });
    
    // Retirer l'erreur lors de la saisie
    field.addEventListener('input', function() {
        if (this.classList.contains('error-field')) {
            validateField(this);
        }
    });
    
    // Pour les checkboxes
    if (field.type === 'checkbox') {
        field.addEventListener('change', function() {
            validateField(this);
        });
    }
});

// Validation à la soumission du formulaire
form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    let isFormValid = true;
    const errors = [];
    
    // Valider tous les champs
    fields.forEach(field => {
        const isValid = validateField(field);
        if (!isValid) {
            isFormValid = false;
            
            // Récupérer le message d'erreur
            const errorElement = document.getElementById(`error-${field.id}`);
            if (errorElement) {
                errors.push(errorElement.textContent);
            }
        }
    });
    
    // Si des erreurs existent
    if (!isFormValid) {
        // Scroll vers le premier champ en erreur
        const firstError = form.querySelector('.error-field');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
        
        // Afficher une alerte avec toutes les erreurs
        alert('Veuillez corriger les erreurs suivantes :\n\n• ' + errors.join('\n• '));
        
        return false;
    }
    
    // Si tout est valide, soumettre le formulaire
    // Afficher un loader sur le bouton
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';
    
    // Soumettre le formulaire
    form.submit();
});

// Validation spécifique pour le téléphone (formater automatiquement)
const phoneField = document.getElementById('phone');
if (phoneField) {
    phoneField.addEventListener('input', function(e) {
        // Garder seulement les chiffres et le +
        let value = e.target.value.replace(/[^\d+\s]/g, '');
        e.target.value = value;
    });
}

// Validation spécifique pour le code postal (formater automatiquement)
const postalField = document.getElementById('postal-code');
if (postalField) {
    postalField.addEventListener('input', function(e) {
        // Garder seulement les chiffres
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
    });
}

// Message de confirmation avant de quitter la page
window.addEventListener('beforeunload', function(e) {
    // Seulement si le formulaire a été modifié
    const hasModified = Array.from(fields).some(field => {
        return field.value.trim() !== '';
    });
    
    if (hasModified) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});