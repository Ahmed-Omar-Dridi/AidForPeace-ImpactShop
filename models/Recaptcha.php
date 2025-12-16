<?php
/**
 * Model Recaptcha
 * Gère la validation de Google reCAPTCHA v2
 * Respecte le pattern MVC - Logique métier uniquement
 */

class Recaptcha {
    private string $secretKey;
    private string $siteKey;
    private string $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    
    /**
     * Constructeur
     * @param string $siteKey Clé publique du site
     * @param string $secretKey Clé secrète
     */
    public function __construct(string $siteKey = '', string $secretKey = '') {
        // Utiliser les clés de configuration ou celles passées en paramètre
        $this->siteKey = $siteKey ?: $this->getConfigValue('RECAPTCHA_SITE_KEY', '');
        $this->secretKey = $secretKey ?: $this->getConfigValue('RECAPTCHA_SECRET_KEY', '');
    }
    
    /**
     * Récupérer une valeur de configuration
     * @param string $key Nom de la constante
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    private function getConfigValue(string $key, $default) {
        return defined($key) ? constant($key) : $default;
    }
    
    /**
     * Obtenir la clé publique du site
     * @return string
     */
    public function getSiteKey(): string {
        return $this->siteKey;
    }
    
    /**
     * Vérifier si reCAPTCHA est configuré ET activé
     * @return bool
     */
    public function isConfigured(): bool {
        // Vérifier d'abord si reCAPTCHA est activé dans la config
        if (defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED === false) {
            return false;
        }
        return !empty($this->siteKey) && !empty($this->secretKey);
    }
    
    /**
     * Valider le token reCAPTCHA
     * @param string $token Token reCAPTCHA reçu du formulaire
     * @param string $remoteIp Adresse IP de l'utilisateur (optionnel)
     * @return array ['success' => bool, 'score' => float, 'error' => string]
     */
    public function verify(string $token, string $remoteIp = ''): array {
        // Vérifier que reCAPTCHA est configuré
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'score' => 0,
                'error' => 'reCAPTCHA non configuré'
            ];
        }
        
        // Vérifier que le token n'est pas vide
        if (empty($token)) {
            return [
                'success' => false,
                'score' => 0,
                'error' => 'Token reCAPTCHA manquant'
            ];
        }
        
        // Préparer les données pour la requête
        $data = [
            'secret' => $this->secretKey,
            'response' => $token
        ];
        
        // Ajouter l'IP si fournie
        if (!empty($remoteIp)) {
            $data['remoteip'] = $remoteIp;
        }
        
        // Effectuer la requête vers l'API Google
        try {
            $response = $this->makeRequest($data);
            return $this->parseResponse($response);
        } catch (Exception $e) {
            return [
                'success' => false,
                'score' => 0,
                'error' => 'Erreur de communication avec reCAPTCHA: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Effectuer la requête HTTP vers l'API Google
     * @param array $data Données à envoyer
     * @return string Réponse JSON
     * @throws Exception
     */
    private function makeRequest(array $data): string {
        // Utiliser cURL pour la requête
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->verifyUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        // Vérifier les erreurs
        if ($response === false) {
            throw new Exception("Erreur cURL: $error");
        }
        
        if ($httpCode !== 200) {
            throw new Exception("Code HTTP invalide: $httpCode");
        }
        
        return $response;
    }
    
    /**
     * Parser la réponse de l'API Google
     * @param string $jsonResponse Réponse JSON
     * @return array
     */
    private function parseResponse(string $jsonResponse): array {
        $data = json_decode($jsonResponse, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'score' => 0,
                'error' => 'Réponse JSON invalide'
            ];
        }
        
        // Extraire les informations
        $success = $data['success'] ?? false;
        $score = $data['score'] ?? 1.0; // v2 n'a pas de score, on met 1.0 par défaut
        $errorCodes = $data['error-codes'] ?? [];
        
        // Construire le message d'erreur si nécessaire
        $errorMessage = '';
        if (!$success && !empty($errorCodes)) {
            $errorMessage = $this->getErrorMessage($errorCodes);
        }
        
        return [
            'success' => $success,
            'score' => $score,
            'error' => $errorMessage,
            'challenge_ts' => $data['challenge_ts'] ?? '',
            'hostname' => $data['hostname'] ?? ''
        ];
    }
    
    /**
     * Obtenir un message d'erreur lisible
     * @param array $errorCodes Codes d'erreur de l'API
     * @return string
     */
    private function getErrorMessage(array $errorCodes): string {
        $messages = [
            'missing-input-secret' => 'Clé secrète manquante',
            'invalid-input-secret' => 'Clé secrète invalide',
            'missing-input-response' => 'Token reCAPTCHA manquant',
            'invalid-input-response' => 'Token reCAPTCHA invalide ou expiré',
            'bad-request' => 'Requête malformée',
            'timeout-or-duplicate' => 'Token expiré ou déjà utilisé'
        ];
        
        $errors = [];
        foreach ($errorCodes as $code) {
            $errors[] = $messages[$code] ?? $code;
        }
        
        return implode(', ', $errors);
    }
    
    /**
     * Générer le HTML du widget reCAPTCHA
     * @param string $theme Theme (light ou dark)
     * @param string $size Taille (normal ou compact)
     * @return string HTML du widget
     */
    public function getWidgetHtml(string $theme = 'light', string $size = 'normal'): string {
        if (!$this->isConfigured()) {
            return '<!-- reCAPTCHA non configuré -->';
        }
        
        return sprintf(
            '<div class="g-recaptcha" data-sitekey="%s" data-theme="%s" data-size="%s"></div>',
            htmlspecialchars($this->siteKey),
            htmlspecialchars($theme),
            htmlspecialchars($size)
        );
    }
    
    /**
     * Obtenir l'URL du script reCAPTCHA
     * @param string $lang Langue (fr, en, etc.)
     * @return string URL du script
     */
    public function getScriptUrl(string $lang = 'fr'): string {
        return "https://www.google.com/recaptcha/api.js?hl=" . urlencode($lang);
    }
    
    /**
     * Logger une tentative de validation (pour statistiques)
     * @param bool $success Succès ou échec
     * @param string $context Contexte (login, register, etc.)
     * @return bool
     */
    public function logAttempt(bool $success, string $context = 'unknown'): bool {
        try {
            $pdo = config::getConnexion();
            
            // Créer la table si elle n'existe pas
            $this->createLogTableIfNotExists($pdo);
            
            $stmt = $pdo->prepare("
                INSERT INTO recaptcha_logs (success, context, ip_address, user_agent, created_at)
                VALUES (:success, :context, :ip, :user_agent, NOW())
            ");
            
            return $stmt->execute([
                'success' => $success ? 1 : 0,
                'context' => $context,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log("Erreur log reCAPTCHA: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Créer la table de logs si elle n'existe pas
     * @param PDO $pdo
     */
    private function createLogTableIfNotExists(PDO $pdo): void {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS recaptcha_logs (
                id_log INT AUTO_INCREMENT PRIMARY KEY,
                success TINYINT(1) NOT NULL,
                context VARCHAR(50) NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT,
                created_at DATETIME NOT NULL,
                INDEX idx_created_at (created_at),
                INDEX idx_context (context)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    
    /**
     * Obtenir les statistiques de validation
     * @param int $days Nombre de jours
     * @return array
     */
    public function getStats(int $days = 7): array {
        try {
            $pdo = config::getConnexion();
            
            $stmt = $pdo->prepare("
                SELECT 
                    context,
                    COUNT(*) as total,
                    SUM(success) as successful,
                    COUNT(*) - SUM(success) as failed,
                    ROUND(SUM(success) * 100.0 / COUNT(*), 2) as success_rate
                FROM recaptcha_logs
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY context
                ORDER BY total DESC
            ");
            
            $stmt->execute(['days' => $days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur stats reCAPTCHA: " . $e->getMessage());
            return [];
        }
    }
}
