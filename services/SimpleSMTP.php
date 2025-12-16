<?php
/**
 * SimpleSMTP - Classe simple pour envoyer des emails via SMTP Gmail
 * Sans dépendances externes (pas besoin de Composer)
 */

class SimpleSMTP
{
    private $host = 'smtp.gmail.com';
    private $port = 587;
    private $username;
    private $password;
    private $from_email;
    private $from_name;

    public function __construct($username, $password, $from_email, $from_name)
    {
        $this->username = $username;
        $this->password = $password;
        $this->from_email = $from_email;
        $this->from_name = $from_name;
    }

    public function send($to, $subject, $html_body)
    {
        try {
            // Créer une connexion socket
            $socket = fsockopen($this->host, $this->port, $errno, $errstr, 30);
            
            if (!$socket) {
                error_log("❌ Erreur de connexion SMTP: $errstr ($errno)");
                return false;
            }

            // Lire la réponse du serveur
            $response = fgets($socket, 1024);
            if (strpos($response, '220') === false) {
                error_log("❌ Erreur SMTP: " . trim($response));
                fclose($socket);
                return false;
            }

            // Envoyer EHLO
            fputs($socket, "EHLO localhost\r\n");
            $response = fgets($socket, 1024);

            // Démarrer TLS
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 1024);
            
            if (strpos($response, '220') === false) {
                error_log("❌ Erreur STARTTLS: " . trim($response));
                fclose($socket);
                return false;
            }

            // Activer le chiffrement SSL
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            // Envoyer EHLO à nouveau après TLS
            fputs($socket, "EHLO localhost\r\n");
            $response = fgets($socket, 1024);

            // Authentification
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 1024);

            // Envoyer le username en base64
            fputs($socket, base64_encode($this->username) . "\r\n");
            $response = fgets($socket, 1024);

            // Envoyer le password en base64
            fputs($socket, base64_encode($this->password) . "\r\n");
            $response = fgets($socket, 1024);

            if (strpos($response, '235') === false) {
                error_log("❌ Erreur d'authentification: " . trim($response));
                fclose($socket);
                return false;
            }

            // Envoyer le mail
            fputs($socket, "MAIL FROM: <" . $this->from_email . ">\r\n");
            $response = fgets($socket, 1024);

            fputs($socket, "RCPT TO: <$to>\r\n");
            $response = fgets($socket, 1024);

            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 1024);

            // Construire le message
            $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";
            $headers .= "\r\n";

            $message = $headers . $html_body . "\r\n.\r\n";

            fputs($socket, $message);
            $response = fgets($socket, 1024);

            if (strpos($response, '250') === false) {
                error_log("❌ Erreur d'envoi: " . trim($response));
                fclose($socket);
                return false;
            }

            // Fermer la connexion
            fputs($socket, "QUIT\r\n");
            fclose($socket);

            error_log("✅ Email envoyé avec succès à: $to");
            return true;

        } catch (Exception $e) {
            error_log("❌ Erreur SimpleSMTP: " . $e->getMessage());
            return false;
        }
    }
}
?>
