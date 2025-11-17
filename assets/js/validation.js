/**
 * Validation côté client pour les formulaires
 * IMPORTANT: La validation côté serveur (PHP) reste OBLIGATOIRE
 * Ceci est juste pour améliorer l'expérience utilisateur
 */

document.addEventListener('DOMContentLoaded', function() {
    const productForm = document.getElementById('product-form');
    
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupération des valeurs
            const nameEn = document.getElementById('name-en').value.trim();
            const nameFr = document.getElementById('name-fr').value.trim();
            const price = document.getElementById('price').value.trim();
            const imgName = document.getElementById('img-name').value.trim();
            const descEn = document.getElementById('desc-en').value.trim();
            const descFr = document.getElementById('desc-fr').value.trim();
            
            let errors = [];
            
            // Validation du nom anglais
            if (nameEn.length < 3) {
                errors.push("Le nom en anglais doit contenir au moins 3 caractères.");
                highlightError('name-en');
            } else {
                removeHighlight('name-en');
            }
            
            // Validation du nom français
            if (nameFr.length < 3) {
                errors.push("Le nom en français doit contenir au moins 3 caractères.");
                highlightError('name-fr');
            } else {
                removeHighlight('name-fr');
            }
            
            // Validation du prix
            const priceNum = parseFloat(price);
            if (isNaN(priceNum) || priceNum <= 0) {
                errors.push("Le prix doit être un nombre positif.");
                highlightError('price');
            } else {
                removeHighlight('price');
            }
            
            // Validation du nom de l'image
            if (imgName.length < 3) {
                errors.push("Le nom de l'image doit contenir au moins 3 caractères.");
                highlightError('img-name');
            } else if (!imgName.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                errors.push("Le nom de l'image doit avoir une extension valide (jpg, png, gif, webp).");
                highlightError('img-name');
            } else {
                removeHighlight('img-name');
            }
            
            // Validation de la description anglaise
            if (descEn.length < 10) {
                errors.push("La description en anglais doit contenir au moins 10 caractères.");
                highlightError('desc-en');
            } else {
                removeHighlight('desc-en');
            }
            
            // Validation de la description française
            if (descFr.length < 10) {
                errors.push("La description en français doit contenir au moins 10 caractères.");
                highlightError('desc-fr');
            } else {
                removeHighlight('desc-fr');
            }
            
            // Affichage des erreurs ou soumission du formulaire
            if (errors.length > 0) {
                showValidationErrors(errors);
            } else {
                // Si tout est OK, soumettre le formulaire
                productForm.submit();
            }
        });
        
        // Validation en temps réel pour une meilleure UX
        const inputs = productForm.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                removeHighlight(this.id);
            });
        });
    }
});

/**
 * Valider un champ individuel
 */
function validateField(field) {
    const value = field.value.trim();
    const fieldId = field.id;
    
    switch(fieldId) {
        case 'name-en':
        case 'name-fr':
            if (value.length < 3) {
                highlightError(fieldId);
                return false;
            }
            break;
            
        case 'price':
            const priceNum = parseFloat(value);
            if (isNaN(priceNum) || priceNum <= 0) {
                highlightError(fieldId);
                return false;
            }
            break;
            
        case 'img-name':
            if (value.length < 3 || !value.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                highlightError(fieldId);
                return false;
            }
            break;
            
        case 'desc-en':
        case 'desc-fr':
            if (value.length < 10) {
                highlightError(fieldId);
                return false;
            }
            break;
    }
    
    removeHighlight(fieldId);
    return true;
}

/**
 * Mettre en évidence un champ avec erreur
 */
function highlightError(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.style.borderColor = '#e74c3c';
        field.style.backgroundColor = '#fee';
    }
}

/**
 * Retirer la mise en évidence d'erreur
 */
function removeHighlight(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.style.borderColor = '#e0e0e0';
        field.style.backgroundColor = '#fff';
    }
}

/**
 * Afficher les erreurs de validation
 */
function showValidationErrors(errors) {
    // Supprimer l'ancienne alerte si elle existe
    const oldAlert = document.querySelector('.alert-error-js');
    if (oldAlert) {
        oldAlert.remove();
    }
    
    // Créer une nouvelle alerte
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-error alert-error-js';
    alertDiv.style.marginBottom = '20px';
    alertDiv.style.padding = '15px';
    alertDiv.style.backgroundColor = '#fee';
    alertDiv.style.border = '1px solid #e74c3c';
    alertDiv.style.borderRadius = '8px';
    alertDiv.style.color = '#c0392b';
    
    let errorHTML = '<h4>Erreurs de validation :</h4><ul>';
    errors.forEach(error => {
        errorHTML += `<li>${error}</li>`;
    });
    errorHTML += '</ul>';
    
    alertDiv.innerHTML = errorHTML;
    
    // Insérer l'alerte au début du formulaire
    const form = document.getElementById('product-form');
    form.parentElement.insertBefore(alertDiv, form);
    
    // Scroll vers l'alerte
    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Supprimer l'alerte après 5 secondes
    setTimeout(() => {
        alertDiv.style.transition = 'opacity 0.5s';
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 500);
    }, 5000);
}