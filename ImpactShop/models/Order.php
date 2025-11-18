<?php
/**
 * Modèle Order - Gestion des commandes
 */

class Order {
    private $conn;
    private $table_orders = "orders";
    private $table_order_items = "order_items";

    public $id;
    public $customer_id;
    public $total_amount;
    public $status;
    public $payment_method;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer une nouvelle commande avec ses items
     * @param array $customerData Données du client
     * @param array $cartItems Items du panier
     * @return int|false ID de la commande créée ou false
     */
    public function createOrder($customerData, $cartItems) {
        try {
            // Démarrer une transaction
            $this->conn->beginTransaction();

            // 1. Créer ou récupérer le client
            $customer_id = $this->getOrCreateCustomer($customerData);
            
            if (!$customer_id) {
                throw new Exception("Erreur lors de la création du client");
            }

            // 2. Calculer le total
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // 3. Créer la commande
            $query = "INSERT INTO " . $this->table_orders . " 
                      (customer_id, total_amount, status, payment_method, created_at) 
                      VALUES 
                      (:customer_id, :total_amount, 'pending', :payment_method, NOW())";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":customer_id", $customer_id);
            $stmt->bindParam(":total_amount", $total);
            $stmt->bindParam(":payment_method", $this->payment_method);
            $stmt->execute();

            $order_id = $this->conn->lastInsertId();

            // 4. Créer les items de la commande
            $query_items = "INSERT INTO " . $this->table_order_items . " 
                            (order_id, product_id, quantity, unit_price, subtotal) 
                            VALUES 
                            (:order_id, :product_id, :quantity, :unit_price, :subtotal)";

            $stmt_items = $this->conn->prepare($query_items);

            foreach ($cartItems as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                
                $stmt_items->bindParam(":order_id", $order_id);
                $stmt_items->bindParam(":product_id", $item['id']);
                $stmt_items->bindParam(":quantity", $item['quantity']);
                $stmt_items->bindParam(":unit_price", $item['price']);
                $stmt_items->bindParam(":subtotal", $subtotal);
                $stmt_items->execute();
            }

            // Valider la transaction
            $this->conn->commit();

            return $order_id;

        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->conn->rollBack();
            error_log("Erreur création commande: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer ou créer un client
     * @param array $customerData
     * @return int|false ID du client
     */
    private function getOrCreateCustomer($customerData) {
        // Vérifier si le client existe déjà
        $query = "SELECT id FROM customers WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $customerData['email']);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row['id'];
        }

        // Créer un nouveau client
        $query = "INSERT INTO customers (first_name, last_name, email, phone, created_at) 
                  VALUES (:first_name, :last_name, :email, :phone, NOW())";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":first_name", $customerData['first_name']);
        $stmt->bindParam(":last_name", $customerData['last_name']);
        $stmt->bindParam(":email", $customerData['email']);
        $stmt->bindParam(":phone", $customerData['phone']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Mettre à jour le statut d'une commande
     * @param int $order_id
     * @param string $status
     * @return bool
     */
    public function updateStatus($order_id, $status) {
        $query = "UPDATE " . $this->table_orders . " 
                  SET status = :status, updated_at = NOW() 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $order_id);

        return $stmt->execute();
    }

    /**
     * Récupérer une commande avec ses détails
     * @param int $order_id
     * @return array|false
     */
    public function getOrderDetails($order_id) {
        $query = "SELECT o.*, c.first_name, c.last_name, c.email, c.phone
                  FROM " . $this->table_orders . " o
                  INNER JOIN customers c ON o.customer_id = c.id
                  WHERE o.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $order_id);
        $stmt->execute();

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Récupérer les items
            $query_items = "SELECT oi.*, p.name_en, p.name_fr, p.img_name
                            FROM " . $this->table_order_items . " oi
                            INNER JOIN products p ON oi.product_id = p.id
                            WHERE oi.order_id = :order_id";

            $stmt_items = $this->conn->prepare($query_items);
            $stmt_items->bindParam(":order_id", $order_id);
            $stmt_items->execute();

            $order['items'] = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        }

        return $order;
    }

    /**
     * Récupérer toutes les commandes
     * @return PDOStatement
     */
    public function getAllOrders() {
        $query = "SELECT o.*, c.first_name, c.last_name, c.email
                  FROM " . $this->table_orders . " o
                  INNER JOIN customers c ON o.customer_id = c.id
                  ORDER BY o.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Validation des données de commande
     */
    public function validateCustomerData($data) {
        $errors = [];

        if (empty($data['first_name']) || strlen(trim($data['first_name'])) < 2) {
            $errors[] = "Le prénom doit contenir au moins 2 caractères.";
        }

        if (empty($data['last_name']) || strlen(trim($data['last_name'])) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères.";
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        }

        if (empty($data['phone']) || strlen(trim($data['phone'])) < 8) {
            $errors[] = "Le numéro de téléphone doit contenir au moins 8 caractères.";
        }

        return $errors;
    }
}
?>