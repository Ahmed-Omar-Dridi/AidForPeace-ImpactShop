<?php
/**
 * Configuration pour l'envoi d'emails
 * 
 * INSTRUCTIONS:
 * 1. Configurez vos paramètres SMTP ci-dessous
 * 2. Ou utilisez la fonction mail() de PHP (par défaut)
 */

// =============================================
// CONFIGURATION DE BASE
// =============================================

// Email d'envoi
define('EMAIL_FROM', 'noreply@aidforpeace.org');
define('EMAIL_FROM_NAME', 'Aid for Peace');

// =============================================
// CONFIGURATION SMTP (Optionnel)
// =============================================

// Activer/désactiver SMTP
define('SMTP_ENABLED', true); // ACTIVÉ pour envoyer des emails

// Serveur SMTP - Configuration Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587); // 587 pour TLS
define('SMTP_USERNAME', 'omardridi123466@gmail.com');
define('SMTP_PASSWORD', 'mtpqkrgyzuqidbhnbsr');
define('SMTP_ENCRYPTION', 'tls');

// =============================================
// CONFIGURATION DE LA VÉRIFICATION EMAIL
// =============================================

// Activer/désactiver la vérification email
define('EMAIL_VERIFICATION_ENABLED', true); // ACTIVÉ - L'utilisateur reçoit un code par email

// Forcer la vérification avant connexion
define('EMAIL_VERIFICATION_REQUIRED', true); // true = l'utilisateur DOIT vérifier son email pour se connecter

// Durée de validité du code (en heures)
define('EMAIL_VERIFICATION_EXPIRY', 24);

// =============================================
// NOTES IMPORTANTES
// =============================================

/*
 * UTILISATION DE LA FONCTION mail() DE PHP:
 * 
 * Par défaut, le système utilise la fonction mail() de PHP.
 * 
 * WINDOWS (XAMPP):
 * 1. Ouvrir php.ini (C:\xampp\php\php.ini)
 * 2. Chercher [mail function]
 * 3. Configurer:
 *    SMTP = localhost
 *    smtp_port = 25
 *    sendmail_from = noreply@aidforpeace.org
 * 
 * LINUX:
 * La fonction mail() utilise sendmail par défaut.
 * Assurez-vous que sendmail est installé:
 * sudo apt-get install sendmail
 * 
 * POUR LES TESTS EN LOCAL:
 * Utilisez un service comme MailHog ou Mailtrap:
 * - MailHog: https://github.com/mailhog/MailHog
 * - Mailtrap: https://mailtrap.io/
 * 
 * POUR LA PRODUCTION:
 * Utilisez un service SMTP professionnel:
 * - Gmail SMTP (gratuit, limité)
 * - SendGrid (gratuit jusqu'à 100 emails/jour)
 * - Mailgun (gratuit jusqu'à 5000 emails/mois)
 * - Amazon SES (très bon marché)
 * 
 * CONFIGURATION GMAIL:
 * 1. Activer "Accès moins sécurisé" dans votre compte Google
 * 2. Ou créer un "Mot de passe d'application"
 * 3. Configurer:
 *    SMTP_HOST = smtp.gmail.com
 *    SMTP_PORT = 587
 *    SMTP_USERNAME = votre-email@gmail.com
 *    SMTP_PASSWORD = votre-mot-de-passe-application
 *    SMTP_ENCRYPTION = tls
 * 
 * SÉCURITÉ:
 * - Ne commitez JAMAIS ce fichier avec vos vrais identifiants dans Git
 * - Ajoutez config_email.php dans .gitignore
 * - Utilisez des variables d'environnement en production
 */

// =============================================
// VÉRIFICATION
// =============================================

// Vérifier que les constantes sont définies
if (!defined('EMAIL_FROM')) {
    trigger_error('EMAIL_FROM non défini', E_USER_WARNING);
}

// Avertir si SMTP est activé mais mal configuré
if (SMTP_ENABLED && (empty(SMTP_HOST) || empty(SMTP_USERNAME))) {
    trigger_error('SMTP activé mais mal configuré', E_USER_WARNING);
}
