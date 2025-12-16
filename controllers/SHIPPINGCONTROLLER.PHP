<?php
require_once __DIR__ . '/../models/Shipping.php';
require_once __DIR__ . '/../models/DeliveryZone.php';
require_once __DIR__ . '/../config/DATABASE.PHP';
require_once __DIR__ . '/../services/EmailService.php';

class ShippingController {
    private $lastError = null;

    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Afficher les zones de livraison
     */
    public function zones() {
        $zones = DeliveryZone::getAll();
        require __DIR__ . '/../views/shop/delivery_zones.php';
    }

    /**
     * Calculer les frais de livraison (AJAX)
     */
    public function calculateCost() {
        header('Content-Type: application/json');
        
        $city = trim($_GET['city'] ?? '');
        $total = floatval($_GET['total'] ?? 0);
        
        if (empty($city)) {
            echo json_encode(['success' => false, 'error' => 'Ville requise']);
            return;
        }
        
        $zone = DeliveryZone::findZoneByCity($city);
        $cost = DeliveryZone::calculateShippingCost($city, $total);
        
        echo json_encode([
            'success' => true,
            'zone' => $zone['name'],
            'delay' => $zone['delay'],
            'cost' => $cost,
            'free_shipping' => $cost === 0
        ]);
    }

    /**
     * Envoyer le code de suivi par email
     */
    public function sendTrackingEmail($shippingId) {
        try {
            $shipping = Shipping::findById($shippingId);
            if (!$shipping) {
                return false;
            }
            
            $db = Database::getConnexion();
            $stmt = $db->prepare("
                SELECT c.email, c.first_name, c.last_name, o.id as order_id
                FROM shippings s
                JOIN orders o ON s.order_id = o.id
                JOIN customers c ON o.customer_id = c.id
                WHERE s.id = :id
            ");
            $stmt->execute(['id' => $shippingId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                $customerName = $data['first_name'] . ' ' . $data['last_name'];
                $orderId = str_pad($data['order_id'], 6, '0', STR_PAD_LEFT);
                
                return EmailService::sendTrackingCode(
                    $data['email'],
                    $customerName,
                    $orderId,
                    $shipping->getTrackingNumber()
                );
            }
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
        }
        return false;
    }

    public function listShippings() {
        try {
            return Shipping::findAll();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function listPendingShippings() {
        try {
            return Shipping::findPending();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function updateShippingStatus($shippingId, $newStatus) {
        try {
            $shipping = Shipping::findById($shippingId);
            if (!$shipping) {
                throw new Exception("Livraison introuvable.");
            }
            return $shipping->updateStatus($newStatus);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function track() {
        $trackingNumber = $_GET['tracking'] ?? '';
        
        if ($trackingNumber) {
            $shipping = Shipping::findByTrackingNumber($trackingNumber);
            
            if ($shipping) {
                require __DIR__ . '/../views/shop/tracking_result.php';
            } else {
                $_SESSION['error'] = "Numéro de suivi non trouvé";
                require __DIR__ . '/../views/shop/tracking_form.php';
            }
        } else {
            require __DIR__ . '/../views/shop/tracking_form.php';
        }
    }
    
    public function index() {
        $shippings = Shipping::findAll();
        require __DIR__ . '/../views/admin/shipping_list.php';
    }
}