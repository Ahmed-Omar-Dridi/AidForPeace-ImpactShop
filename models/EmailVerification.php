<?php
/**
 * Model EmailVerification
 * Gère la vérification des emails lors de l'inscription
 * Respecte le pattern MVC - Logique métier uniquement
 */

class EmailVerification {
    private int $id_verification;
    private int $user_id;
    private string $email;
    private string $verification_code;
    private string $verification_token;
    private bool $is_verified;
    private DateTime $created_at;
    private ?DateTime $verified_at;
    private DateTime $expires_at;
    
    /**
     * Constructeur
     */
    public function __construct(
        int $user_id = 0,
        string $email = '',
        string $verification_code = '',
        string $verification_token = ''
    ) {
        $this->user_id = $user_id;
        $this->email = $email;
        $this->verification_code = $verification_code;
        $this->verification_token = $verification_token;
        $this->is_verified = false;
        $this->created_at = new DateTime();
        $this->verified_at = null;
        $this->expires_at = new DateTime('+24 hours'); // Expire après 24h
    }
    
    /**
     * Générer un code de vérification à 6 chiffres
     * @return string
     */
    public static function generateCode(): string {
        return str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Générer un token de vérification unique
     * @return string
     */
    public static function generateToken(): string {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Créer une nouvelle vérification
     * @return bool
     */
    public function create(): bool {
        try {
            $pdo = config::getConnexion();
            
            // Créer la table si elle n'existe pas
            $this->createTableIfNotExists($pdo);
            
            $stmt = $pdo->prepare("
                INSERT INTO email_verifications 
                (user_id, email, verification_code, verification_token, is_verified, created_at, expires_at)
                VALUES (:user_id, :email, :code, :token, 0, NOW(), DATE_ADD(NOW(), INTERVAL 24 HOUR))
            ");
            
            $result = $stmt->execute([
                'user_id' => $this->user_id,
                'email' => $this->email,
                'code' => $this->verification_code,
                'token' => $this->verification_token
            ]);
            
            if ($result) {
                $this->id_verification = (int)$pdo->lastInsertId();
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Erreur création vérification email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifier un code de vérification
     * @param string $email Email de l'utilisateur
     * @param string $code Code à vérifier
     * @return array ['success' => bool, 'message' => string, 'user_id' => int]
     */
    public static function verifyCode(string $email, string $code): array {
        try {
            $pdo = config::getConnexion();
            
            // Récupérer la vérification
            $stmt = $pdo->prepare("
                SELECT * FROM email_verifications
                WHERE email = :email 
                AND verification_code = :code
                AND is_verified = 0
                AND expires_at > NOW()
                ORDER BY created_at DESC
                LIMIT 1
            ");
            
            $stmt->execute([
                'email' => $email,
                'code' => $code
            ]);
            
            $verification = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$verification) {
                return [
                    'success' => false,
                    'message' => 'Code invalide ou expiré',
                    'user_id' => 0
                ];
            }
            
            // Marquer comme vérifié
            $stmt = $pdo->prepare("
                UPDATE email_verifications
                SET is_verified = 1, verified_at = NOW()
                WHERE id_verification = :id
            ");
            
            $stmt->execute(['id' => $verification['id_verification']]);
            
            // Activer le compte utilisateur
            $stmt = $pdo->prepare("
                UPDATE user
                SET email_verified = 1, email_verified_at = NOW()
                WHERE id_user = :user_id
            ");
            
            $stmt->execute(['user_id' => $verification['user_id']]);
            
            return [
                'success' => true,
                'message' => 'Email vérifié avec succès!',
                'user_id' => (int)$verification['user_id']
            ];
        } catch (Exception $e) {
            error_log("Erreur vérification code: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la vérification',
                'user_id' => 0
            ];
        }
    }
    
    /**
     * Vérifier un token de vérification (lien dans l'email)
     * @param string $token Token à vérifier
     * @return array ['success' => bool, 'message' => string, 'user_id' => int]
     */
    public static function verifyToken(string $token): array {
        try {
            $pdo = config::getConnexion();
            
            // Récupérer la vérification
            $stmt = $pdo->prepare("
                SELECT * FROM email_verifications
                WHERE verification_token = :token
                AND is_verified = 0
                AND expires_at > NOW()
                ORDER BY created_at DESC
                LIMIT 1
            ");
            
            $stmt->execute(['token' => $token]);
            $verification = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$verification) {
                return [
                    'success' => false,
                    'message' => 'Lien invalide ou expiré',
                    'user_id' => 0
                ];
            }
            
            // Marquer comme vérifié
            $stmt = $pdo->prepare("
                UPDATE email_verifications
                SET is_verified = 1, verified_at = NOW()
                WHERE id_verification = :id
            ");
            
            $stmt->execute(['id' => $verification['id_verification']]);
            
            // Activer le compte utilisateur
            $stmt = $pdo->prepare("
                UPDATE user
                SET email_verified = 1, email_verified_at = NOW()
                WHERE id_user = :user_id
            ");
            
            $stmt->execute(['user_id' => $verification['user_id']]);
            
            return [
                'success' => true,
                'message' => 'Email vérifié avec succès!',
                'user_id' => (int)$verification['user_id']
            ];
        } catch (Exception $e) {
            error_log("Erreur vérification token: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la vérification',
                'user_id' => 0
            ];
        }
    }
    
    /**
     * Renvoyer un code de vérification
     * @param string $email Email de l'utilisateur
     * @return array ['success' => bool, 'message' => string, 'code' => string, 'token' => string]
     */
    public static function resendCode(string $email): array {
        try {
            $pdo = config::getConnexion();
            
            // Vérifier que l'email existe et n'est pas déjà vérifié
            $stmt = $pdo->prepare("
                SELECT u.id_user, u.email_verified
                FROM user u
                WHERE u.email = :email
            ");
            
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Email non trouvé',
                    'code' => '',
                    'token' => ''
                ];
            }
            
            if ($user['email_verified'] == 1) {
                return [
                    'success' => false,
                    'message' => 'Email déjà vérifié',
                    'code' => '',
                    'token' => ''
                ];
            }
            
            // Invalider les anciens codes
            $stmt = $pdo->prepare("
                UPDATE email_verifications
                SET is_verified = -1
                WHERE user_id = :user_id AND is_verified = 0
            ");
            
            $stmt->execute(['user_id' => $user['id_user']]);
            
            // Créer un nouveau code
            $code = self::generateCode();
            $token = self::generateToken();
            
            $verification = new EmailVerification(
                (int)$user['id_user'],
                $email,
                $code,
                $token
            );
            
            if ($verification->create()) {
                return [
                    'success' => true,
                    'message' => 'Nouveau code envoyé',
                    'code' => $code,
                    'token' => $token,
                    'user_id' => (int)$user['id_user']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création du code',
                    'code' => '',
                    'token' => ''
                ];
            }
        } catch (Exception $e) {
            error_log("Erreur renvoi code: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors du renvoi',
                'code' => '',
                'token' => ''
            ];
        }
    }
    
    /**
     * Vérifier si un email est déjà vérifié
     * @param string $email
     * @return bool
     */
    public static function isEmailVerified(string $email): bool {
        try {
            $pdo = config::getConnexion();
            
            $stmt = $pdo->prepare("
                SELECT email_verified FROM user WHERE email = :email
            ");
            
            $stmt->execute(['email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result && $result['email_verified'] == 1;
        } catch (Exception $e) {
            error_log("Erreur vérification email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Nettoyer les vérifications expirées
     * @param int $days Nombre de jours
     * @return int Nombre de lignes supprimées
     */
    public static function cleanExpired(int $days = 7): int {
        try {
            $pdo = config::getConnexion();
            
            $stmt = $pdo->prepare("
                DELETE FROM email_verifications
                WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)
            ");
            
            $stmt->execute(['days' => $days]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            error_log("Erreur nettoyage: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Créer la table si elle n'existe pas
     * @param PDO $pdo
     */
    private function createTableIfNotExists(PDO $pdo): void {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS email_verifications (
                id_verification INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                email VARCHAR(255) NOT NULL,
                verification_code VARCHAR(6) NOT NULL,
                verification_token VARCHAR(64) NOT NULL,
                is_verified TINYINT(1) DEFAULT 0 COMMENT '-1=invalidé, 0=en attente, 1=vérifié',
                created_at DATETIME NOT NULL,
                verified_at DATETIME NULL,
                expires_at DATETIME NOT NULL,
                
                INDEX idx_user_id (user_id),
                INDEX idx_email (email),
                INDEX idx_code (verification_code),
                INDEX idx_token (verification_token),
                INDEX idx_expires (expires_at),
                
                FOREIGN KEY (user_id) REFERENCES user(id_user) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Ajouter les colonnes à la table user si elles n'existent pas
        try {
            $pdo->exec("
                ALTER TABLE user 
                ADD COLUMN IF NOT EXISTS email_verified TINYINT(1) DEFAULT 0,
                ADD COLUMN IF NOT EXISTS email_verified_at DATETIME NULL
            ");
        } catch (Exception $e) {
            // Les colonnes existent déjà
        }
    }
    
    // Getters
    public function getId(): int { return $this->id_verification; }
    public function getUserId(): int { return $this->user_id; }
    public function getEmail(): string { return $this->email; }
    public function getCode(): string { return $this->verification_code; }
    public function getToken(): string { return $this->verification_token; }
    public function isVerified(): bool { return $this->is_verified; }
}
