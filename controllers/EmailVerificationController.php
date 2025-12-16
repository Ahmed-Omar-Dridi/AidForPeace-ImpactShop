<?php
/**
 * Controller EmailVerificationController
 * Gère les interactions avec les modèles EmailVerification et EmailSender
 * Respecte le pattern MVC - Orchestration uniquement
 */

require_once(__DIR__ . '/../models/EmailVerification.php');
require_once(__DIR__ . '/../models/EmailSender.php');

class EmailVerificationController {
    private EmailSender $emailSender;
    
    public function __construct() {
        $this->emailSender = new EmailSender();
    }
    
    /**
     * Créer et envoyer un code de vérification
     * @param int $user_id ID de l'utilisateur
     * @param string $email Email de l'utilisateur
     * @param string $name Nom de l'utilisateur
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendVerificationCode(int $user_id, string $email, string $name): array {
        // Générer le code et le token
        $code = EmailVerification::generateCode();
        $token = EmailVerification::generateToken();
        
        // Créer la vérification en base de données
        $verification = new EmailVerification($user_id, $email, $code, $token);
        
        if (!$verification->create()) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la création du code de vérification'
            ];
        }
        
        // Envoyer l'email
        $emailSent = $this->emailSender->sendVerificationEmail($email, $name, $code, $token);
        
        if (!$emailSent) {
            // En environnement local, l'email ne peut pas être envoyé sans SMTP
            // Mais le code est quand même généré et disponible
            return [
                'success' => true, // On considère comme succès car le code est créé
                'message' => 'Code de vérification créé. En environnement local, consultez: <a href="voir_dernier_code.php" target="_blank">voir_dernier_code.php</a> pour voir le code.'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Un code de vérification a été envoyé à votre adresse email.'
        ];
    }
    
    /**
     * Vérifier un code depuis un formulaire POST
     * @return array ['success' => bool, 'message' => string, 'user_id' => int]
     */
    public function verifyCodeFromPost(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Méthode non autorisée',
                'user_id' => 0
            ];
        }
        
        $email = trim($_POST['email'] ?? '');
        $code = trim($_POST['code'] ?? '');
        
        if (empty($email) || empty($code)) {
            return [
                'success' => false,
                'message' => 'Email et code requis',
                'user_id' => 0
            ];
        }
        
        return EmailVerification::verifyCode($email, $code);
    }
    
    /**
     * Vérifier un token depuis l'URL
     * @param string $token Token de vérification
     * @return array ['success' => bool, 'message' => string, 'user_id' => int]
     */
    public function verifyToken(string $token): array {
        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token manquant',
                'user_id' => 0
            ];
        }
        
        return EmailVerification::verifyToken($token);
    }
    
    /**
     * Renvoyer un code de vérification
     * @return array ['success' => bool, 'message' => string]
     */
    public function resendCode(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Méthode non autorisée'
            ];
        }
        
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            return [
                'success' => false,
                'message' => 'Email requis'
            ];
        }
        
        // Générer un nouveau code
        $result = EmailVerification::resendCode($email);
        
        if (!$result['success']) {
            return [
                'success' => false,
                'message' => $result['message']
            ];
        }
        
        // Envoyer l'email
        $emailSent = $this->emailSender->sendVerificationEmail(
            $email,
            'Utilisateur', // On ne connaît pas le nom ici
            $result['code'],
            $result['token']
        );
        
        if (!$emailSent) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Un nouveau code a été envoyé à votre adresse email'
        ];
    }
    
    /**
     * Vérifier si un email est déjà vérifié
     * @param string $email
     * @return bool
     */
    public function isEmailVerified(string $email): bool {
        return EmailVerification::isEmailVerified($email);
    }
    
    /**
     * Vérifier si l'envoi d'emails est configuré
     * @return bool
     */
    public function isEmailConfigured(): bool {
        return $this->emailSender->isConfigured();
    }
}
