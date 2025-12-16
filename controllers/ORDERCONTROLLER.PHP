<?php
require_once __DIR__ . '/../config/DATABASE.PHP';
require_once __DIR__ . '/../models/ORDER.PHP';

/**
 * OrderController - gestion des commandes
 */
class OrderController {
    private ?string $lastError = null;

    public function getLastError(): ?string {
        return $this->lastError;
    }

    // Retourne la liste des commandes (associatif)
    public function listOrders(): array {
        $db = Database::getConnexion();
        $sql = "SELECT 
                    o.*,
                    c.first_name,
                    c.last_name,
                    c.email,
                    c.phone
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.id
                ORDER BY o.created_at DESC";
        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log('OrderController::listOrders error: ' . $e->getMessage());
            return [];
        }
    }

    public function showOrder(Order $order) {
        echo "<h2>Informations de la commande :</h2>";
        $order->show();
    }

    public function showById(int $id) {
        $order = Order::findById($id);
        if ($order) {
            $this->showOrder($order);
        } else {
            echo "<p>Commande introuvable (id=".htmlspecialchars($id).").</p>";
        }
    }

    public function deleteOrder($id): bool {
        $db = Database::getConnexion();
        try {
            $db->beginTransaction();
            $sqlItems = "DELETE FROM order_items WHERE order_id = :id";
            $reqItems = $db->prepare($sqlItems);
            $reqItems->execute(['id' => $id]);

            $sql = "DELETE FROM orders WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db && $db->inTransaction()) $db->rollBack();
            $this->lastError = $e->getMessage();
            error_log('OrderController::deleteOrder error: ' . $e->getMessage());
            return false;
        }
    }

    public function addOrder(Order $order) {
        $sql = "INSERT INTO orders VALUES (NULL, :customer_id, :total_amount, :status, :payment_method, NOW(), NULL)";
        $db = Database::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'customer_id' => $order->getCustomerId(),
                'total_amount' => $order->getTotalAmount(),
                'status' => $order->getStatus(),
                'payment_method' => $order->getPaymentMethod()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log('OrderController::addOrder error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateOrder(Order $order, $id): bool {
        try {
            $db = Database::getConnexion();
            $query = $db->prepare(
                'UPDATE orders SET 
                    customer_id = :customer_id,
                    total_amount = :total_amount,
                    status = :status,
                    payment_method = :payment_method,
                    updated_at = NOW()
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'customer_id' => $order->getCustomerId(),
                'total_amount' => $order->getTotalAmount(),
                'status' => $order->getStatus(),
                'payment_method' => $order->getPaymentMethod()
            ]);
            return true;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log('OrderController::updateOrder error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crée une commande avec vérification & décrémentation du stock.
     * Retourne orderId (int) si ok, false sinon (getLastError fournit le détail).
     */
    public function createOrderWithStock(array $customerData, ?array $cartData = null, $paymentMethod = 'paypal') {
        $db = Database::getConnexion();
        $this->lastError = null;

        try {
            if (empty($customerData['first_name']) || empty($customerData['last_name'])) {
                throw new Exception('Le prénom et nom du client sont requis.');
            }
            if (empty($customerData['email']) || !filter_var($customerData['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email client invalide.');
            }
            if (!is_array($cartData) || count($cartData) === 0) {
                throw new Exception('Panier vide. Veuillez ajouter des produits avant de commander.');
            }

            $db->beginTransaction();

            // Rechercher ou créer client
            $stmtFind = $db->prepare("SELECT id FROM customers WHERE email = :email LIMIT 1");
            $stmtFind->execute(['email' => $customerData['email']]);
            $existing = $stmtFind->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                $customerId = $existing['id'];
            } else {
                $sqlCustomer = "INSERT INTO customers (first_name, last_name, email, phone, created_at) 
                               VALUES (:first_name, :last_name, :email, :phone, NOW())";
                $stmtCustomer = $db->prepare($sqlCustomer);
                $stmtCustomer->execute([
                    'first_name' => $customerData['first_name'],
                    'last_name' => $customerData['last_name'],
                    'email' => $customerData['email'],
                    'phone' => $customerData['phone'] ?? ''
                ]);
                $customerId = $db->lastInsertId();
            }

            // Créer commande provisoire
            $sqlOrder = "INSERT INTO orders (customer_id, total_amount, status, payment_method, created_at)
                         VALUES (:customer_id, 0, 'pending', :payment_method, NOW())";
            $stmtOrder = $db->prepare($sqlOrder);
            $stmtOrder->execute([
                'customer_id' => $customerId,
                'payment_method' => $paymentMethod
            ]);
            $orderId = $db->lastInsertId();

            // Statements pour verrouillage, insertion items et mise à jour stock
            $stmtCheckProduct = $db->prepare("SELECT id, price, stock FROM products WHERE id = :id FOR UPDATE");
            $stmtInsertItem = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                                           VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal)");
            $stmtUpdateStock = $db->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id");

            $calculatedTotal = 0.0;
            foreach ($cartData as $idx => $item) {
                $productId = intval($item['id'] ?? 0);
                $quantity = intval($item['quantity'] ?? 0);
                if ($productId <= 0) throw new Exception("Article #{$idx} : product_id invalide.");
                if ($quantity <= 0) throw new Exception("Article #{$idx} : quantité invalide.");

                $stmtCheckProduct->execute(['id' => $productId]);
                $prod = $stmtCheckProduct->fetch(PDO::FETCH_ASSOC);
                if (!$prod) throw new Exception("Produit introuvable (id={$productId}).");
                if (!array_key_exists('stock', $prod)) throw new Exception("Colonne 'stock' manquante pour le produit {$productId}.");
                if (intval($prod['stock']) < $quantity) throw new Exception("Stock insuffisant pour le produit id={$productId} (disponible: {$prod['stock']}, demandé: {$quantity}).");

                $unitPrice = floatval($prod['price']);
                $subtotal = $unitPrice * $quantity;
                $calculatedTotal += $subtotal;

                $stmtInsertItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal
                ]);
                $stmtUpdateStock->execute(['qty' => $quantity, 'id' => $productId]);
            }

            // Update total
            $stmtUpdateTotal = $db->prepare("UPDATE orders SET total_amount = :total WHERE id = :id");
            $stmtUpdateTotal->execute(['total' => $calculatedTotal, 'id' => $orderId]);

            $db->commit();
            return intval($orderId);
        } catch (Exception $e) {
            if ($db && $db->inTransaction()) $db->rollBack();
            $this->lastError = $e->getMessage();
            error_log('OrderController::createOrderWithStock error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Change order status with business rules and restore stock on cancellation.
     */
    public function changeOrderStatus($orderId, $newStatus, $changedBy = null): bool {
        $allowedTransitions = [
            'pending' => ['paid', 'cancelled'],
            'paid' => ['processing','cancelled'],
            'processing' => ['shipped','cancelled'],
            'shipped' => ['completed'],
            'completed' => [],
            'cancelled' => []
        ];

        $db = Database::getConnexion();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT status FROM orders WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$order) throw new Exception("Order not found");

            $current = $order['status'];
            if (!isset($allowedTransitions[$current])) throw new Exception("Invalid current status");
            if (!in_array($newStatus, $allowedTransitions[$current])) {
                throw new Exception("Transition $current -> $newStatus not allowed");
            }

            if ($newStatus === 'cancelled' && $current !== 'cancelled') {
                $stmtItems = $db->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :order_id");
                $stmtItems->execute(['order_id' => $orderId]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                $stmtRestock = $db->prepare("UPDATE products SET stock = stock + :qty WHERE id = :id");
                foreach ($items as $it) {
                    $stmtRestock->execute(['qty' => $it['quantity'], 'id' => $it['product_id']]);
                }
            }

            $stmtUpdate = $db->prepare("UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id");
            $stmtUpdate->execute(['status' => $newStatus, 'id' => $orderId]);

            try {
                $stmtHist = $db->prepare("INSERT INTO order_status_history (order_id, old_status, new_status, changed_by)
                                          VALUES (:order_id, :old_status, :new_status, :changed_by)");
                $stmtHist->execute([
                    'order_id' => $orderId,
                    'old_status' => $current,
                    'new_status' => $newStatus,
                    'changed_by' => $changedBy
                ]);
            } catch (Exception $ignore) {
                // not critical
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db && $db->inTransaction()) $db->rollBack();
            $this->lastError = $e->getMessage();
            error_log('OrderController::changeOrderStatus error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère une commande complète : commande + client + items (avec infos produits).
     * Retourne tableau associatif ou null si introuvable.
     */
    public function getOrderWithItems(int $orderId): ?array {
        $db = Database::getConnexion();
        try {
            // 1) récupérer la commande + infos client
            $sqlOrder = "SELECT o.*, c.first_name, c.last_name, c.email, c.phone
                         FROM orders o
                         LEFT JOIN customers c ON o.customer_id = c.id
                         WHERE o.id = :id";
            $stmt = $db->prepare($sqlOrder);
            $stmt->execute(['id' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$order) return null;

            // 2) récupérer les items avec infos produit
            $sqlItems = "SELECT oi.order_id, oi.product_id, oi.quantity, oi.unit_price, oi.subtotal,
                                p.name_fr, p.name_en, p.img_name
                         FROM order_items oi
                         JOIN products p ON oi.product_id = p.id
                         WHERE oi.order_id = :order_id";
            $stmtItems = $db->prepare($sqlItems);
            $stmtItems->execute(['order_id' => $orderId]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $order['items'] = $items;
            return $order;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log('OrderController::getOrderWithItems error: ' . $e->getMessage());
            return null;
        }
    }
}
?>