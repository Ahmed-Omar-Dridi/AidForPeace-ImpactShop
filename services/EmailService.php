<?php
/**
 * EmailService - Service d'envoi d'emails
 * Envoie des emails de confirmation de commande avec code de suivi
 */

class EmailService
{
    private static $fromEmail = 'noreply@impactshop.tn';
    private static $fromName = 'ImpactShop';

    /**
     * Envoyer un email de confirmation de commande avec code de suivi
     */
    public static function sendOrderConfirmation($orderDetails, $trackingNumber = null)
    {
        $to = $orderDetails['email'];
        $customerName = ($orderDetails['first_name'] ?? '') . ' ' . ($orderDetails['last_name'] ?? '');
        $orderId = str_pad($orderDetails['id'], 6, '0', STR_PAD_LEFT);

        $subject = "ImpactShop - Confirmation de votre commande #$orderId";

        $html = self::getOrderConfirmationTemplate($orderDetails, $trackingNumber, $customerName, $orderId);

        return self::sendEmail($to, $subject, $html);
    }

    /**
     * Envoyer un email avec le code de suivi
     */
    public static function sendTrackingCode($email, $customerName, $orderId, $trackingNumber)
    {
        $subject = "ImpactShop - Votre code de suivi pour la commande #$orderId";

        $html = self::getTrackingEmailTemplate($customerName, $orderId, $trackingNumber);

        return self::sendEmail($email, $subject, $html);
    }

    /**
     * Envoyer un email de bienvenue au programme de fidélité
     */
    public static function sendLoyaltyWelcome($email, $customerName, $points)
    {
        $subject = "ImpactShop - Bienvenue au programme de fidélité!";

        $html = self::getLoyaltyWelcomeTemplate($customerName, $points);

        return self::sendEmail($email, $subject, $html);
    }

    /**
     * Envoyer un email avec PHPMailer + Gmail SMTP
     */
    private static function sendEmail($to, $subject, $htmlBody)
    {
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            // Configuration SMTP Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'omardridi123466@gmail.com';
            $mail->Password = 'triu iqgh qvmc dlyf';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataire et expéditeur
            $mail->setFrom('omardridi123466@gmail.com', 'ImpactShop');
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $htmlBody;
            $mail->CharSet = 'UTF-8';

            // Envoyer
            $result = $mail->send();
            
            error_log("✅ Email envoyé avec succès à: $to");
            return $result;

        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("❌ Erreur: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("❌ Erreur: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fallback: Envoyer un email avec PHP mail()
     */
    private static function sendEmailWithPHP($to, $subject, $htmlBody)
    {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . self::$fromName . ' <' . self::$fromEmail . '>',
            'Reply-To: ' . self::$fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];

        error_log("Email envoyé à: $to | Sujet: $subject");

        $result = mail($to, $subject, $htmlBody, implode("\r\n", $headers));
        
        if ($result) {
            error_log("✅ Email envoyé avec succès à: $to");
        } else {
            error_log("❌ Erreur lors de l'envoi de l'email à: $to");
        }
        
        return $result;
    }

    /**
     * Template email confirmation de commande
     */
    private static function getOrderConfirmationTemplate($order, $trackingNumber, $customerName, $orderId)
    {
        $trackingSection = '';
        if ($trackingNumber) {
            $trackingSection = "
            <div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;'>
                <h3 style='color: #856404; margin: 0 0 10px 0;'>📦 Votre Code de Suivi</h3>
                <p style='font-size: 24px; font-weight: bold; color: #1e3149; margin: 10px 0; font-family: monospace; background: white; padding: 15px; border-radius: 5px;'>
                    $trackingNumber
                </p>
                <p style='color: #666; margin: 0;'>Utilisez ce code pour suivre votre livraison sur notre site</p>
                <a href='index.php?controller=shipping&action=track&tracking=$trackingNumber' 
                   style='display: inline-block; background: #ffb600; color: #1e3149; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 15px;'>
                    Suivre ma commande
                </a>
            </div>";
        }

        $itemsHtml = '';
        if (!empty($order['items'])) {
            foreach ($order['items'] as $item) {
                $itemsHtml .= "
                <tr>
                    <td style='padding: 15px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($item['name_fr'] ?? 'Produit') . "</td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee; text-align: center;'>" . ($item['quantity'] ?? 1) . "</td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee; text-align: right;'>" . number_format($item['subtotal'] ?? 0, 2) . " TND</td>
                </tr>";
            }
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #1e3149, #15202e); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0;'><span style='color: #ffb600;'>Impact</span>Shop</h1>
                <p style='color: rgba(255,255,255,0.8); margin: 10px 0 0 0;'>Boutique Solidaire d'Aide Humanitaire</p>
            </div>
            
            <div style='background: white; padding: 30px; border: 1px solid #eee;'>
                <h2 style='color: #1e3149; margin-top: 0;'>✅ Commande Confirmée!</h2>
                
                <p>Bonjour <strong>$customerName</strong>,</p>
                
                <p>Merci pour votre commande! Nous avons bien reçu votre commande <strong>#$orderId</strong>.</p>
                
                $trackingSection
                
                <h3 style='color: #1e3149; border-bottom: 2px solid #ffb600; padding-bottom: 10px;'>Récapitulatif de votre commande</h3>
                
                <table style='width: 100%; border-collapse: collapse;'>
                    <thead>
                        <tr style='background: #f8f9fa;'>
                            <th style='padding: 15px; text-align: left;'>Produit</th>
                            <th style='padding: 15px; text-align: center;'>Qté</th>
                            <th style='padding: 15px; text-align: right;'>Prix</th>
                        </tr>
                    </thead>
                    <tbody>
                        $itemsHtml
                    </tbody>
                    <tfoot>
                        <tr style='background: #1e3149; color: white;'>
                            <td colspan='2' style='padding: 15px; font-weight: bold;'>TOTAL</td>
                            <td style='padding: 15px; text-align: right; font-weight: bold; color: #ffb600;'>" . number_format($order['total_amount'] ?? 0, 2) . " TND</td>
                        </tr>
                    </tfoot>
                </table>
                
                <div style='margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;'>
                    <h4 style='margin: 0 0 10px 0; color: #1e3149;'>Besoin d'aide?</h4>
                    <p style='margin: 0; color: #666;'>Notre équipe est là pour vous aider. Contactez-nous à contact@impactshop.tn</p>
                </div>
            </div>
            
            <div style='background: #1e3149; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;'>
                <p style='color: rgba(255,255,255,0.8); margin: 0;'>❤️ Merci de soutenir notre cause - ImpactShop 2025</p>
            </div>
        </body>
        </html>";
    }

    /**
     * Template email code de suivi
     */
    private static function getTrackingEmailTemplate($customerName, $orderId, $trackingNumber)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #1e3149, #15202e); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0;'><span style='color: #ffb600;'>Impact</span>Shop</h1>
            </div>
            
            <div style='background: white; padding: 30px; border: 1px solid #eee; text-align: center;'>
                <h2 style='color: #1e3149;'>📦 Votre Colis est en Route!</h2>
                
                <p>Bonjour <strong>$customerName</strong>,</p>
                
                <p>Votre commande <strong>#$orderId</strong> a été expédiée!</p>
                
                <div style='background: #d4edda; padding: 25px; border-radius: 10px; margin: 25px 0;'>
                    <p style='margin: 0 0 10px 0; color: #155724;'>Votre code de suivi:</p>
                    <p style='font-size: 28px; font-weight: bold; color: #1e3149; margin: 0; font-family: monospace; background: white; padding: 15px; border-radius: 5px;'>
                        $trackingNumber
                    </p>
                </div>
                
                <a href='index.php?controller=shipping&action=track&tracking=$trackingNumber' 
                   style='display: inline-block; background: #ffb600; color: #1e3149; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;'>
                    🔍 Suivre ma Livraison
                </a>
                
                <p style='margin-top: 30px; color: #666;'>Vous pouvez également entrer ce code sur notre page de suivi.</p>
            </div>
            
            <div style='background: #1e3149; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;'>
                <p style='color: rgba(255,255,255,0.8); margin: 0;'>❤️ ImpactShop 2025</p>
            </div>
        </body>
        </html>";
    }

    /**
     * Envoyer un email de remerciement pour un don
     */
    public static function sendThankYouEmail($email, $donorName, $ngoName, $amount, $type)
    {
        $subject = "AidForPeace - Merci pour votre don!";
        $html = self::getThankYouTemplate($donorName, $ngoName, $amount, $type);
        return self::sendEmail($email, $subject, $html);
    }

    /**
     * Template email remerciement don
     */
    private static function getThankYouTemplate($donorName, $ngoName, $amount, $type)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #1e3149, #15202e); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0;'><span style='color: #ffb600;'>Aid</span>ForPeace</h1>
            </div>
            
            <div style='background: white; padding: 30px; border: 1px solid #eee; text-align: center;'>
                <h2 style='color: #1e3149;'>❤️ Merci pour votre générosité!</h2>
                
                <p>Bonjour <strong>" . htmlspecialchars($donorName) . "</strong>,</p>
                
                <p>Nous avons bien reçu votre don pour <strong>" . htmlspecialchars($ngoName) . "</strong>.</p>
                
                <div style='background: #d4edda; padding: 25px; border-radius: 10px; margin: 25px 0;'>
                    <p style='margin: 0 0 10px 0; color: #155724;'>Détails de votre don:</p>
                    <p style='font-size: 28px; font-weight: bold; color: #1e3149; margin: 10px 0;'>" . htmlspecialchars($amount) . " €</p>
                    <p style='margin: 0; color: #155724;'>Type: " . htmlspecialchars($type) . "</p>
                </div>
                
                <p>Votre contribution fait une réelle différence dans la vie des personnes dans le besoin.</p>
                
                <p style='margin-top: 30px; color: #666;'>Merci de faire partie de notre communauté solidaire!</p>
            </div>
            
            <div style='background: #1e3149; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;'>
                <p style='color: rgba(255,255,255,0.8); margin: 0;'>❤️ AidForPeace 2025</p>
            </div>
        </body>
        </html>";
    }

    /**
     * Template email bienvenue fidélité
     */
    private static function getLoyaltyWelcomeTemplate($customerName, $points)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #1e3149, #15202e); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0;'><span style='color: #ffb600;'>Impact</span>Shop</h1>
            </div>
            
            <div style='background: white; padding: 30px; border: 1px solid #eee; text-align: center;'>
                <h2 style='color: #1e3149;'>🎁 Bienvenue au Programme de Fidélité!</h2>
                
                <p>Bonjour <strong>$customerName</strong>,</p>
                
                <p>Félicitations! Vous avez gagné vos premiers points de fidélité!</p>
                
                <div style='background: #ffb600; padding: 25px; border-radius: 10px; margin: 25px 0;'>
                    <p style='margin: 0; color: #1e3149; font-size: 18px;'>Votre solde actuel:</p>
                    <p style='font-size: 48px; font-weight: bold; color: #1e3149; margin: 10px 0;'>$points</p>
                    <p style='margin: 0; color: #1e3149;'>points</p>
                </div>
                
                <p>Continuez vos achats pour accumuler plus de points et débloquer des récompenses exclusives!</p>
                
                <a href='index.php?controller=loyalty&action=index' 
                   style='display: inline-block; background: #1e3149; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                    Voir mes Récompenses
                </a>
            </div>
            
            <div style='background: #1e3149; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;'>
                <p style='color: rgba(255,255,255,0.8); margin: 0;'>❤️ ImpactShop 2025</p>
            </div>
        </body>
        </html>";
    }
}
