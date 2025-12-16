<?php
require_once __DIR__ . "/../config/database.php";

class Order {
    private $id;
    private $customer_id;
    private $total_amount;
    private $status;
    private $payment_method;
    private $created_at;
    private $updated_at;
    private $items = [];

    public function __construct($id, $customer_id, $total_amount, $status, $payment_method, $created_at = null, $updated_at = null) {
        $this->id = $id;
        $this->customer_id = $customer_id;
        $this->total_amount = $total_amount;
        $this->status = $status;
        $this->payment_method = $payment_method;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public static function findAll() {
        $db = Database::getConnexion();
        $sql = "SELECT o.*, c.first_name, c. last_name, c.email FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::getConnexion();
        $sql = "SELECT o.*, c.first_name, c.last_name, c.email, c.phone, c.address, c. city, c.postal_code, c. country FROM orders o LEFT JOIN customers c ON o.customer_id = c. id WHERE o.id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return $row;
    }

    public function show() {
        echo "<h3>Commande #" . htmlspecialchars($this->id) .  "</h3>";
        echo "<p>Montant: " . number_format($this->total_amount, 2) . " TND</p>";
        echo "<p>Statut: " .  htmlspecialchars($this->status) . "</p>";
    }

    public function getId() { return $this->id; }
    public function getCustomerId() { return $this->customer_id; }
    public function getTotalAmount() { return $this->total_amount; }
    public function getStatus() { return $this->status; }
    public function getPaymentMethod() { return $this->payment_method; }
    public function getItems() { return $this->items; }
}