<?php
/**
 * Modèle pour l'authentification à deux facteurs (2FA)
 */
class TwoFactorAuth
{
    public int $userId;
    public bool $enabled;
    public string $secret;
    public array $recoveryCodes;
    public string $method; // 'app', 'sms', 'email'

    public function __construct(
        int $userId = 0,
        bool $enabled = false,
        string $secret = '',
        array $recoveryCodes = [],
        string $method = 'app'
    ) {
        $this->userId = $userId;
        $this->enabled = $enabled;
        $this->secret = $secret;
        $this->recoveryCodes = $recoveryCodes;
        $this->method = $method;
    }

    // Getters
    public function getUserId(): int { return $this->userId; }
    public function isEnabled(): bool { return $this->enabled; }
    public function getSecret(): string { return $this->secret; }
    public function getRecoveryCodes(): array { return $this->recoveryCodes; }
    public function getMethod(): string { return $this->method; }

    // Setters
    public function setEnabled(bool $enabled): void { $this->enabled = $enabled; }
    public function setSecret(string $secret): void { $this->secret = $secret; }
    public function setRecoveryCodes(array $codes): void { $this->recoveryCodes = $codes; }
    public function setMethod(string $method): void { $this->method = $method; }

    /**
     * Générer un secret pour TOTP (Time-based One-Time Password)
     */
    public static function generateSecret(int $length = 32): string {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $secret;
    }

    /**
     * Générer des codes de récupération
     */
    public static function generateRecoveryCodes(int $count = 10): array {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4))); // 8 caractères hex
        }
        return $codes;
    }

    /**
     * Vérifier un code TOTP
     * Implémentation simplifiée - en production, utilisez une bibliothèque comme google2fa
     */
    public function verifyCode(string $code): bool {
        if (empty($this->secret)) {
            return false;
        }

        // Vérifier si c'est un code de récupération
        if ($this->verifyRecoveryCode($code)) {
            return true;
        }

        // Pour une vraie implémentation, utilisez une bibliothèque TOTP
        // Ici, c'est une version simplifiée pour la démonstration
        $timeSlice = floor(time() / 30);
        $expectedCode = $this->generateTOTP($timeSlice);
        
        return hash_equals($expectedCode, $code);
    }

    /**
     * Vérifier un code de récupération
     */
    private function verifyRecoveryCode(string $code): bool {
        $code = strtoupper($code);
        $index = array_search($code, $this->recoveryCodes);
        
        if ($index !== false) {
            // Supprimer le code utilisé
            unset($this->recoveryCodes[$index]);
            $this->recoveryCodes = array_values($this->recoveryCodes);
            return true;
        }
        
        return false;
    }

    /**
     * Générer un code TOTP (simplifié)
     * En production, utilisez une vraie bibliothèque TOTP
     */
    private function generateTOTP(int $timeSlice): string {
        $hash = hash_hmac('sha1', pack('N*', 0, $timeSlice), $this->secret, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        
        return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Obtenir l'URL du QR Code pour Google Authenticator
     */
    public function getQRCodeUrl(string $email, string $appName = 'AidForPeace'): string {
        $encodedEmail = urlencode($email);
        $encodedApp = urlencode($appName);
        return "otpauth://totp/{$encodedApp}:{$encodedEmail}?secret={$this->secret}&issuer={$encodedApp}";
    }
}
?>
