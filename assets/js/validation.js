/**
 * Validation côté client pour les formulaires
 * Date: 2025-11-25
 * Author: dridi10331
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Validation.js chargé');
    
    const productForm = document.getElementById('product-form');
    
    if (productForm) {
        console.log('✅ Formulaire produit trouvé');
        
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('📋 Validation du formulaire...');
            
            let errors = [];
            
            // Récupération SÉCURISÉE des valeurs par NAME
            const nameEn = productForm.querySelector('[name="name_en"]');
            const nameFr = productForm.querySelector('[name="name_fr"]');
            const price = productForm.querySelector('[name="price"]');
            const imgName = productForm.querySelector('[name="img_name"]');
            const descEn = productForm.querySelector('[name="description_en"]');
            const descFr = productForm.querySelector('[name="description_fr"]');
            
            // Validation du nom anglais
            if (nameEn && nameEn.value.trim().length < 3) {
                errors.push("Le nom en anglais doit contenir au moins 3 caractères.");
                highlightError(nameEn);
            } else if (nameEn) {
                removeHighlight(nameEn);
            }
            
            // Validation du nom français
            if (nameFr && nameFr.value.trim().length < 3) {
                errors.push("Le nom en français doit contenir au moins 3 caractères.");
                highlightError(nameFr);
            } else if (nameFr) {
                removeHighlight(nameFr);
            }
            
            // Validation du prix
            if (price) {
                const priceNum = parseFloat(price.value);
                if (isNaN(priceNum) || priceNum <= 0) {
                    errors.push("Le prix doit être un nombre positif.");
                    highlightError(price);
                } else {
                    removeHighlight(price);
                }
            }
            
            // Validation du nom de l'image
            if (imgName && imgName.value.trim().length < 3) {
                errors.push("Le nom de l'image doit contenir au moins 3 caractères.");
                highlightError(imgName);
            } else if (imgName && !imgName.value.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                errors.push("Le nom de l'image doit avoir une extension valide (jpg, jpeg, png, gif, webp).");
                highlightError(imgName);
            } else if (imgName) {
                removeHighlight(imgName);
            }
            
            // Validation de la description anglaise
            if (descEn && descEn.value.trim().length < 10) {
                errors.push("La description en anglais doit contenir au moins 10 caractères.");
                highlightError(descEn);
            } else if (descEn) {
                removeHighlight(descEn);
            }
            
            // Validation de la description française
            if (descFr && descFr.value.trim().length < 10) {
                errors.push("La description en français doit contenir au moins 10 caractères.");
                highlightError(descFr);
            } else if (descFr) {
                removeHighlight(descFr);
            }
            
            // Affichage des erreurs ou soumission du formulaire
            if (errors.length > 0) {
                console.log('❌ Erreurs de validation:', errors);
                showValidationErrors(errors);
            } else {
                console.log('✅ Validation réussie, soumission du formulaire');
                productForm.submit();
            }
        });
        
        // Validation en temps réel
        const inputs = productForm.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                removeHighlight(this);
            });
        });
        
        console.log('✅ Événements de validation attachés');
    } else {
        console.log('ℹ️ Formulaire produit non trouvé sur cette page');
    }
});

/**
 * Valider un champ individuel
 */
function validateField(field) {
    if (!field || !field.name) return;
    
    const value = field.value.trim();
    const fieldName = field.name;
    
    switch(fieldName) {
        case 'name_en':
        case 'name_fr':
            if (value.length < 3) {
                highlightError(field);
                return false;
            }
            break;
            
        case 'price':
            const priceNum = parseFloat(value);
            if (isNaN(priceNum) || priceNum <= 0) {
                highlightError(field);
                return false;
            }
            break;
            
        case 'img_name':
            if (value.length < 3 || !value.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                highlightError(field);
                return false;
            }
            break;
            
        case 'description_en':
        case 'description_fr':
            if (value.length < 10) {
                highlightError(field);
                return false;
            }
            break;
    }
    
    removeHighlight(field);
    return true;
}

/**
 * Mettre en évidence un champ avec erreur
 */
function highlightError(field) {
    if (field) {
        field.style.borderColor = '#e74c3c';
        field.style.backgroundColor = '#fee';
    }
}

/**
 * Retirer la mise en évidence d'erreur
 */
function removeHighlight(field) {
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
    
    let errorHTML = '<h4><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</h4><ul>';
    errors.forEach(error => {
        errorHTML += `<li>${error}</li>`;
    });
    errorHTML += '</ul>';
    
    alertDiv.innerHTML = errorHTML;
    
    // Insérer l'alerte au début du formulaire
    const form = document.getElementById('product-form');
    if (form && form.parentElement) {
        form.parentElement.insertBefore(alertDiv, form);
        
        // Scroll vers l'alerte
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Supprimer l'alerte après 5 secondes
    setTimeout(() => {
        alertDiv.style.transition = 'opacity 0.5s';
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 500);
    }, 5000);
}

console.log('✅ validation.js chargé complètement');