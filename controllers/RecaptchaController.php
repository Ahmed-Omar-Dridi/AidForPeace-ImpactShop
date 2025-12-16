<?php
/**
 * Controller Recaptcha
 * Gère les interactions avec le modèle Recaptcha
 * Respecte le pattern MVC - Pas de logique métier, seulement orchestration
 */

require_once(__DIR__ . '/../models/Recaptcha.php');

class RecaptchaController {
    private Recaptcha $recaptcha;
    
    public function __construct() {
        $this->recaptcha = new Recaptcha();
    }
    
    /**
     * Vérifier le token reCAPTCHA depuis une requête POST
     * @param string $context Contexte de validation (login, register, etc.)
     * @return array ['success' => bool, 'error' => string]
     */
    public function verifyFromPost(string $context = 'unknown'): array {
        // Récupérer le token depuis POST
        $token = $_POST['g-recaptcha-response'] ?? '';
        
        // Récupérer l'IP de l'utilisateur
        $remoteIp = $this->getClientIp();
        
        // Valider avec le modèle
        $result = $this->recaptcha->verify($token, $remoteIp);
        
        // Logger la tentative
        $this->recaptcha->logAttempt($result['success'], $context);
        
        // Retourner le résultat simplifié
        return [
            'success' => $result['success'],
            'error' => $result['error'] ?? ''
        ];
    }
    
    /**
     * Vérifier si reCAPTCHA est activé
     * @return bool
     */
    public function isEnabled(): bool {
        return $this->recaptcha->isConfigured();
    }
    
    /**
     * Obtenir la clé publique du site
     * @return string
     */
    public function getSiteKey(): string {
        return $this->recaptcha->getSiteKey();
    }
    
    /**
     * Obtenir le HTML du widget reCAPTCHA
     * @param string $theme Theme (light ou dark)
     * @param string $size Taille (normal ou compact)
     * @return string
     */
    public function getWidget(string $theme = 'light', string $size = 'normal'): string {
        return $this->recaptcha->getWidgetHtml($theme, $size);
    }
    
    /**
     * Obtenir l'URL du script reCAPTCHA
     * @param string $lang Langue
     * @return string
     */
    public function getScriptUrl(string $lang = 'fr'): string {
        return $this->recaptcha->getScriptUrl($lang);
    }
    
    /**
     * Obtenir les statistiques de validation
     * @param int $days Nombre de jours
     * @return array
     */
    public function getStats(int $days = 7): array {
        return $this->recaptcha->getStats($days);
    }
    
    /**
     * Obtenir l'adresse IP réelle du client
     * @return string
     */
    private function getClientIp(): string {
        // Vérifier les headers de proxy
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                
                // Si plusieurs IPs (proxy chain), prendre la première
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                
                // Valider l'IP
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Middleware pour vérifier reCAPTCHA avant une action
     * Retourne null si OK, sinon retourne un array d'erreur
     * @param string $context Contexte
     * @return array|null
     */
    public function middleware(string $context = 'unknown'): ?array {
        // Si reCAPTCHA n'est pas configuré, on laisse passer
        if (!$this->isEnabled()) {
            return null;
        }
        
        // Vérifier le token
        $result = $this->verifyFromPost($context);
        
        if (!$result['success']) {
            return [
                'success' => false,
                'errors' => ['Veuillez valider le reCAPTCHA: ' . $result['error']]
            ];
        }
        
        return null; // OK, on laisse passer
    }
}
