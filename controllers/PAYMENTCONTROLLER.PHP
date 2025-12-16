<?php
require_once __DIR__ . "/../models/Payment.php";
require_once __DIR__ . "/../models/Shipping.php";
require_once __DIR__ . "/../config/DATABASE.PHP";

class PaymentController {
    private $lastError = null;

    public function getLastError() {
        return $this->lastError;
    }

    public function index() {
        $payments = $this->listPayments();
        require_once __DIR__ . "/../views/payment/index.php";
    }

    public function listPayments() {
        try {
            return Payment::findAll();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function listPendingPayments() {
        try {
            return Payment::findPending();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function showById($id) {
        $payment = Payment::findById($id);
        if ($payment) {
            echo "<h3>Paiement #" . $payment->getId() . "</h3>";
            echo "<p>Montant: " . number_format($payment->getAmount(), 2) . " TND</p>";
            echo "<p>Statut: " . $payment->getStatus() . "</p>";
        } else {
            echo "<p>Paiement introuvable.</p>";
        }
    }

    public function createPayment($orderId, $amount, $method) {
        try {
            return Payment::create($orderId, $amount, $method);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function confirmPaymentWithEmail($paymentId, $transactionId = null) {
        $db = Database::getConnexion();
        $this->lastError = null;

        try {
            $db->beginTransaction();

            $payment = Payment::findById($paymentId);
            if (!$payment) {
                throw new Exception("Paiement introuvable.");
            }
            if ($payment->getStatus() !== "pending") {
                throw new Exception("Ce paiement n'est pas en attente.");
            }

            $payment->markAsCompleted($transactionId);

            $orderId = $payment->getOrderId();
            $sqlOrder = "SELECT o.*, c.first_name, c.last_name, c.email, c.address, c.city, c.postal_code, c.country FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = :id";
            $stmtOrder = $db->prepare($sqlOrder);
            $stmtOrder->execute(["id" => $orderId]);
            $orderDetails = $stmtOrder->fetch(PDO::FETCH_ASSOC);

            if ($orderDetails) {
                $shippingData = [
                    "order_id" => $orderId,
                    "carrier" => "ImpactShop Express",
                    "address" => isset($orderDetails["address"]) ? $orderDetails["address"] : "",
                    "city" => isset($orderDetails["city"]) ? $orderDetails["city"] : "",
                    "postal_code" => isset($orderDetails["postal_code"]) ? $orderDetails["postal_code"] : "",
                    "country" => isset($orderDetails["country"]) ? $orderDetails["country"] : "Tunisia"
                ];
                Shipping::create($shippingData);
            }

            $db->prepare("UPDATE orders SET status = 'paid', updated_at = NOW() WHERE id = :id")->execute(["id" => $orderId]);

            $db->commit();
            return true;

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function refundPayment($paymentId) {
        $db = Database::getConnexion();
        $this->lastError = null;

        try {
            $db->beginTransaction();

            $payment = Payment::findById($paymentId);
            if (!$payment) {
                throw new Exception("Paiement introuvable.");
            }

            $payment->refund();

            $orderId = $payment->getOrderId();
            $db->prepare("UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = :id")->execute(["id" => $orderId]);

            $sqlItems = "SELECT product_id, quantity FROM order_items WHERE order_id = :order_id";
            $stmtItems = $db->prepare($sqlItems);
            $stmtItems->execute(["order_id" => $orderId]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $stmtRestock = $db->prepare("UPDATE products SET stock = stock + :qty WHERE id = :id");
            foreach ($items as $item) {
                $stmtRestock->execute(["qty" => $item["quantity"], "id" => $item["product_id"]]);
            }

            $db->commit();
            return true;

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
