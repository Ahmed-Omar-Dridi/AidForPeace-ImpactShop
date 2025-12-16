<?php
require_once __DIR__ . "/../config/database.php";

class Payment {
    private $id;
    private $order_id;
    private $amount;
    private $method;
    private $status;
    private $transaction_id;
    private $paid_at;
    private $created_at;

    public function __construct($id, $order_id, $amount, $method, $status = "pending", $transaction_id = null, $paid_at = null, $created_at = null) {
        $this->id = $id;
        $this->order_id = $order_id;
        $this->amount = $amount;
        $this->method = $method;
        $this->status = $status;
        $this->transaction_id = $transaction_id;
        $this->paid_at = $paid_at;
        $this->created_at = $created_at;
    }

    public static function findAll() {
        $db = Database::getConnexion();
        $sql = "SELECT p.*, c.first_name, c.last_name, c.email FROM payments p LEFT JOIN orders o ON p.order_id = o.id LEFT JOIN customers c ON o.customer_id = c.id ORDER BY p.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM payments WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new Payment(
            intval($row["id"]),
            intval($row["order_id"]),
            floatval($row["amount"]),
            $row["method"],
            $row["status"],
            $row["transaction_id"],
            $row["paid_at"],
            $row["created_at"]
        );
    }

    public static function findPending() {
        $db = Database::getConnexion();
        $sql = "SELECT p.*, c.first_name, c.last_name, c.email FROM payments p LEFT JOIN orders o ON p.order_id = o.id LEFT JOIN customers c ON o.customer_id = c.id WHERE p.status = 'pending' ORDER BY p.created_at ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($orderId, $amount, $method) {
        $db = Database::getConnexion();
        $sql = "INSERT INTO payments (order_id, amount, method, status, created_at) VALUES (:order_id, :amount, :method, 'pending', NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute(["order_id" => $orderId, "amount" => $amount, "method" => $method]);
        return intval($db->lastInsertId());
    }

    public function markAsCompleted($transactionId = null) {
        $db = Database::getConnexion();
        $sql = "UPDATE payments SET status = 'completed', transaction_id = :transaction_id, paid_at = NOW() WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(["transaction_id" => $transactionId, "id" => $this->id]);
    }

    public function refund() {
        $db = Database::getConnexion();
        $sql = "UPDATE payments SET status = 'refunded' WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(["id" => $this->id]);
    }

    public function getId() { return $this->id; }
    public function getOrderId() { return $this->order_id; }
    public function getAmount() { return $this->amount; }
    public function getMethod() { return $this->method; }
    public function getStatus() { return $this->status; }
    public function getTransactionId() { return $this->transaction_id; }
    public function getPaidAt() { return $this->paid_at; }
    public function getCreatedAt() { return $this->created_at; }
}
