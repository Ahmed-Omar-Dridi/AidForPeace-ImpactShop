<?php
/**
 * Configuration Google reCAPTCHA v2
 * 
 * INSTRUCTIONS:
 * 1. Allez sur https://www.google.com/recaptcha/admin
 * 2. Créez un nouveau site avec reCAPTCHA v2 "Je ne suis pas un robot"
 * 3. Copiez vos clés ci-dessous
 * 4. Renommez ce fichier en supprimant .example si nécessaire
 */

// =============================================
// CLÉ PUBLIQUE (Site Key)
// =============================================
// Cette clé est visible dans le code HTML
// Elle est utilisée pour afficher le widget reCAPTCHA
define('RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'); // CLÉ DE TEST

// =============================================
// CLÉ SECRÈTE (Secret Key)
// =============================================
// Cette clé est utilisée côté serveur pour valider les réponses
// NE JAMAIS la partager ou la mettre dans le code client
define('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'); // CLÉ DE TEST

// =============================================
// CONFIGURATION
// =============================================

// Activer/désactiver reCAPTCHA globalement
define('RECAPTCHA_ENABLED', true); // ENABLED

// Pages où reCAPTCHA est requis
define('RECAPTCHA_PAGES', [
    'login' => true,        // Page de connexion
    'register' => true,     // Page d'inscription
    'forgot_password' => true, // Mot de passe oublié
    'contact' => false,     // Formulaire de contact (désactivé par défaut)
]);

// Langue du widget (fr, en, es, etc.)
define('RECAPTCHA_LANG', 'fr');

// Thème du widget (light ou dark)
define('RECAPTCHA_THEME', 'light');

// Taille du widget (normal ou compact)
define('RECAPTCHA_SIZE', 'normal');

// =============================================
// NOTES IMPORTANTES
// =============================================

/*
 * CLÉS DE TEST GOOGLE:
 * Les clés ci-dessus sont des clés de test fournies par Google.
 * Elles acceptent TOUJOURS les validations.
 * 
 * Site Key (test): 6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI
 * Secret Key (test): 6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
 * 
 * POUR LA PRODUCTION:
 * 1. Allez sur https://www.google.com/recaptcha/admin
 * 2. Connectez-vous avec votre compte Google
 * 3. Cliquez sur "+" pour créer un nouveau site
 * 4. Choisissez:
 *    - Type: reCAPTCHA v2
 *    - Sous-type: "Je ne suis pas un robot" (Checkbox)
 *    - Domaines: localhost, votre-domaine.com
 * 5. Copiez vos clés et remplacez-les ci-dessus
 * 
 * SÉCURITÉ:
 * - Ne commitez JAMAIS ce fichier avec vos vraies clés dans Git
 * - Ajoutez config_recaptcha.php dans .gitignore
 * - Utilisez des variables d'environnement en production
 */

// =============================================
// VÉRIFICATION
// =============================================

// Vérifier que les clés sont définies
if (!defined('RECAPTCHA_SITE_KEY') || !defined('RECAPTCHA_SECRET_KEY')) {
    trigger_error('Les clés reCAPTCHA ne sont pas configurées', E_USER_WARNING);
}

// Vérifier si on utilise les clés de test
if (RECAPTCHA_SITE_KEY === '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI') {
    // Clés de test détectées
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
        trigger_error('ATTENTION: Vous utilisez les clés de TEST en PRODUCTION!', E_USER_WARNING);
    }
}
