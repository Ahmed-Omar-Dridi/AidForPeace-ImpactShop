<?php
/**
 * Model EmailSender
 * Gère l'envoi d'emails
 * Respecte le pattern MVC - Logique métier uniquement
 */

class EmailSender {
    private string $from_email;
    private string $from_name;
    private string $smtp_host;
    private int $smtp_port;
    private string $smtp_username;
    private string $smtp_password;
    private bool $smtp_enabled;
    
    /**
     * Constructeur
     */
    public function __construct() {
        // Configuration depuis les constantes ou valeurs par défaut
        $this->from_email = defined('EMAIL_FROM') ? EMAIL_FROM : 'noreply@aidforpeace.org';
        $this->from_name = defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : 'Aid for Peace';
        $this->smtp_host = defined('SMTP_HOST') ? SMTP_HOST : '';
        $this->smtp_port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $this->smtp_username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
        $this->smtp_password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
        $this->smtp_enabled = defined('SMTP_ENABLED') ? SMTP_ENABLED : false;
    }
    
    /**
     * Envoyer un email de vérification
     * @param string $to_email Email du destinataire
     * @param string $to_name Nom du destinataire
     * @param string $verification_code Code de vérification
     * @param string $verification_token Token de vérification
     * @return bool
     */
    public function sendVerificationEmail(
        string $to_email,
        string $to_name,
        string $verification_code,
        string $verification_token
    ): bool {
        $subject = "Vérifiez votre adresse email - Aid for Peace";
        
        // Créer le lien de vérification
        $verification_link = $this->getBaseUrl() . "/index.php?controller=user&action=verify_email&token=" . urlencode($verification_token);
        
        // Corps de l'email en HTML
        $html_body = $this->getVerificationEmailTemplate(
            $to_name,
            $verification_code,
            $verification_link
        );
        
        // Corps de l'email en texte brut
        $text_body = $this->getVerificationEmailText(
            $to_name,
            $verification_code,
            $verification_link
        );
        
        return $this->send($to_email, $to_name, $subject, $html_body, $text_body);
    }
    
    /**
     * Envoyer un email
     * @param string $to_email
     * @param string $to_name
     * @param string $subject
     * @param string $html_body
     * @param string $text_body
     * @return bool
     */
    private function send(
        string $to_email,
        string $to_name,
        string $subject,
        string $html_body,
        string $text_body = ''
    ): bool {
        if ($this->smtp_enabled && !empty($this->smtp_host)) {
            return $this->sendWithSMTP($to_email, $to_name, $subject, $html_body, $text_body);
        } else {
            return $this->sendWithPHPMail($to_email, $to_name, $subject, $html_body, $text_body);
        }
    }
    
    /**
     * Envoyer avec SMTP (utilise fsockopen)
     * @return bool
     */
    private function sendWithSMTP(
        string $to_email,
        string $to_name,
        string $subject,
        string $html_body,
        string $text_body
    ): bool {
        try {
            // Connexion au serveur SMTP
            $smtp = fsockopen(
                $this->smtp_host,
                $this->smtp_port,
                $errno,
                $errstr,
                30
            );
            
            if (!$smtp) {
                error_log("SMTP Error: $errstr ($errno)");
                $this->logEmail($to_email, $subject, false);
                return false;
            }
            
            // Lire la réponse du serveur
            $response = fgets($smtp, 515);
            
            // EHLO
            fputs($smtp, "EHLO " . $this->smtp_host . "\r\n");
            $response = fgets($smtp, 515);
            
            // STARTTLS si nécessaire
            if (defined('SMTP_ENCRYPTION') && SMTP_ENCRYPTION === 'tls') {
                fputs($smtp, "STARTTLS\r\n");
                $response = fgets($smtp, 515);
                
                stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                
                // EHLO après STARTTLS
                fputs($smtp, "EHLO " . $this->smtp_host . "\r\n");
                $response = fgets($smtp, 515);
            }
            
            // AUTH LOGIN si nécessaire
            if (!empty($this->smtp_username) && !empty($this->smtp_password)) {
                fputs($smtp, "AUTH LOGIN\r\n");
                $response = fgets($smtp, 515);
                
                fputs($smtp, base64_encode($this->smtp_username) . "\r\n");
                $response = fgets($smtp, 515);
                
                fputs($smtp, base64_encode($this->smtp_password) . "\r\n");
                $response = fgets($smtp, 515);
                
                // Vérifier l'authentification
                if (strpos($response, '235') === false) {
                    error_log("SMTP Auth failed: " . $response);
                    fclose($smtp);
                    $this->logEmail($to_email, $subject, false);
                    return false;
                }
            }
            
            // MAIL FROM
            fputs($smtp, "MAIL FROM: <{$this->from_email}>\r\n");
            $response = fgets($smtp, 515);
            
            // RCPT TO
            fputs($smtp, "RCPT TO: <{$to_email}>\r\n");
            $response = fgets($smtp, 515);
            
            // DATA
            fputs($smtp, "DATA\r\n");
            $response = fgets($smtp, 515);
            
            // Headers
            $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
            $headers .= "To: {$to_name} <{$to_email}>\r\n";
            $headers .= "Subject: {$subject}\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";
            $headers .= "\r\n";
            
            // Envoyer le message
            fputs($smtp, $headers . $html_body . "\r\n.\r\n");
            $response = fgets($smtp, 515);
            
            // QUIT
            fputs($smtp, "QUIT\r\n");
            fclose($smtp);
            
            // Logger
            $success = strpos($response, '250') !== false;
            $this->logEmail($to_email, $subject, $success);
            
            return $success;
            
        } catch (Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            $this->logEmail($to_email, $subject, false);
            return false;
        }
    }
    
    /**
     * Envoyer avec la fonction mail() de PHP
     * @return bool
     */
    private function sendWithPHPMail(
        string $to_email,
        string $to_name,
        string $subject,
        string $html_body,
        string $text_body
    ): bool {
        try {
            // Headers
            $headers = [];
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/html; charset=UTF-8";
            $headers[] = "From: {$this->from_name} <{$this->from_email}>";
            $headers[] = "Reply-To: {$this->from_email}";
            $headers[] = "X-Mailer: PHP/" . phpversion();
            
            // Envoyer l'email
            $result = mail(
                $to_email,
                $subject,
                $html_body,
                implode("\r\n", $headers)
            );
            
            // Logger l'envoi
            $this->logEmail($to_email, $subject, $result);
            
            return $result;
        } catch (Exception $e) {
            error_log("Erreur envoi email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Template HTML pour l'email de vérification
     * Style identique à la confirmation de commande ImpactShop
     * @return string
     */
    private function getVerificationEmailTemplate(
        string $name,
        string $code,
        string $link
    ): string {
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de votre email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5;">
    
    <!-- Header avec logo -->
    <div style="background: linear-gradient(135deg, #1e3149, #15202e); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;"><span style="color: #ffb600;">Aid</span>ForPeace</h1>
        <p style="color: rgba(255,255,255,0.8); margin: 10px 0 0 0; font-size: 14px;">Plateforme Humanitaire Solidaire</p>
    </div>
    
    <!-- Contenu principal -->
    <div style="background: white; padding: 30px; border: 1px solid #eee;">
        <h2 style="color: #1e3149; margin-top: 0; border-bottom: 3px solid #ffb600; padding-bottom: 10px;">
            ✅ Bienvenue sur AidForPeace!
        </h2>
        
        <p>Bonjour <strong>{$name}</strong>,</p>
        
        <p>Merci de vous être inscrit sur AidForPeace! Votre compte a été créé avec succès.</p>
        <p>Pour activer votre compte et accéder à toutes les fonctionnalités, veuillez vérifier votre adresse email.</p>
        
        <!-- Code de vérification -->
        <div style="background: #fff3cd; padding: 25px; border-radius: 8px; margin: 25px 0; text-align: center; border: 2px solid #ffb600;">
            <h3 style="color: #1e3149; margin: 0 0 10px 0;">🔐 Votre Code de Vérification</h3>
            <p style="font-size: 36px; font-weight: bold; color: #1e3149; margin: 15px 0; font-family: monospace; background: white; padding: 15px 25px; border-radius: 8px; letter-spacing: 8px; display: inline-block;">
                {$code}
            </p>
            <p style="color: #666; margin: 10px 0 0 0; font-size: 13px;">⏰ Ce code expire dans 24 heures</p>
        </div>
        
        <!-- Instructions -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin: 0 0 15px 0; color: #1e3149;">📋 Comment vérifier votre compte:</h4>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; vertical-align: top; width: 30px;"><strong style="color: #ffb600;">1.</strong></td>
                    <td style="padding: 8px 0;">Copiez le code ci-dessus</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; vertical-align: top;"><strong style="color: #ffb600;">2.</strong></td>
                    <td style="padding: 8px 0;">Collez-le sur la page de vérification</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; vertical-align: top;"><strong style="color: #ffb600;">3.</strong></td>
                    <td style="padding: 8px 0;">Ou cliquez directement sur le bouton ci-dessous</td>
                </tr>
            </table>
        </div>
        
        <!-- Bouton de vérification -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="{$link}" style="display: inline-block; background: #ffb600; color: #1e3149; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">
                ✉️ Vérifier mon Email
            </a>
        </div>
        
        <!-- Récapitulatif compte -->
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #ffb600;">
            <h4 style="margin: 0 0 10px 0; color: #1e3149;">📝 Récapitulatif de votre inscription</h4>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 5px 0; color: #666;">Nom:</td>
                    <td style="padding: 5px 0; font-weight: bold;">{$name}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666;">Statut:</td>
                    <td style="padding: 5px 0;"><span style="background: #fff3cd; color: #856404; padding: 3px 10px; border-radius: 4px; font-size: 12px;">⏳ En attente de vérification</span></td>
                </tr>
            </table>
        </div>
        
        <!-- Note de sécurité -->
        <p style="color: #999; font-size: 13px; margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
            🔒 Si vous n'avez pas créé de compte sur AidForPeace, vous pouvez ignorer cet email en toute sécurité.
        </p>
    </div>
    
    <!-- Footer -->
    <div style="background: #1e3149; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;">
        <p style="color: rgba(255,255,255,0.8); margin: 0; font-size: 14px;">❤️ Ensemble, créons un impact positif - AidForPeace 2025</p>
        <p style="color: rgba(255,255,255,0.5); margin: 10px 0 0 0; font-size: 12px;">Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
    
</body>
</html>
HTML;
    }
    
    /**
     * Template texte pour l'email de vérification
     * @return string
     */
    private function getVerificationEmailText(
        string $name,
        string $code,
        string $link
    ): string {
        return <<<TEXT
============================================
       AIDFORPEACE - BIENVENUE!
============================================

Bonjour {$name},

✅ Votre compte a été créé avec succès!

Merci de vous être inscrit sur AidForPeace, la plateforme humanitaire solidaire.

Pour activer votre compte et accéder à toutes les fonctionnalités, veuillez vérifier votre adresse email.

--------------------------------------------
🔐 VOTRE CODE DE VÉRIFICATION
--------------------------------------------

        {$code}

⏰ Ce code expire dans 24 heures.

--------------------------------------------
📋 COMMENT VÉRIFIER VOTRE COMPTE
--------------------------------------------

1. Copiez le code ci-dessus
2. Collez-le sur la page de vérification
3. Ou cliquez sur ce lien: {$link}

--------------------------------------------
📝 RÉCAPITULATIF
--------------------------------------------

Nom: {$name}
Statut: En attente de vérification

--------------------------------------------

🔒 Si vous n'avez pas créé de compte sur AidForPeace, vous pouvez ignorer cet email.

---
❤️ Ensemble, créons un impact positif
AidForPeace © 2025 - Tous droits réservés
TEXT;
    }
    
    /**
     * Logger l'envoi d'email
     * @param string $to_email
     * @param string $subject
     * @param bool $success
     */
    private function logEmail(string $to_email, string $subject, bool $success): void {
        try {
            $pdo = config::getConnexion();
            
            // Créer la table si elle n'existe pas
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS email_logs (
                    id_log INT AUTO_INCREMENT PRIMARY KEY,
                    to_email VARCHAR(255) NOT NULL,
                    subject VARCHAR(255) NOT NULL,
                    success TINYINT(1) NOT NULL,
                    sent_at DATETIME NOT NULL,
                    INDEX idx_sent_at (sent_at),
                    INDEX idx_to_email (to_email)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $stmt = $pdo->prepare("
                INSERT INTO email_logs (to_email, subject, success, sent_at)
                VALUES (:email, :subject, :success, NOW())
            ");
            
            $stmt->execute([
                'email' => $to_email,
                'subject' => $subject,
                'success' => $success ? 1 : 0
            ]);
        } catch (Exception $e) {
            error_log("Erreur log email: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir l'URL de base du site
     * @return string
     */
    private function getBaseUrl(): string {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script = dirname($_SERVER['SCRIPT_NAME']);
        
        return $protocol . '://' . $host . $script;
    }
    
    /**
     * Vérifier si l'envoi d'emails est configuré
     * @return bool
     */
    public function isConfigured(): bool {
        return !empty($this->from_email);
    }
}
