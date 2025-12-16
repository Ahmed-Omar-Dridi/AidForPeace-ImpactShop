<?php
require_once __DIR__ . '/../config/database.php';

class Shipping {
    private $id;
    private $order_id;
    private $carrier;
    private $tracking_number;
    private $status;
    private $address;
    private $city;
    private $postal_code;
    private $country;
    private $shipping_cost;
    private $estimated_delivery;
    private $shipped_at;
    private $delivered_at;
    private $created_at;



    public function __construct($id, $order_id, $carrier, $tracking_number, $status, $address, $city, $postal_code, $country, $shipping_cost = 0, $estimated_delivery = null, $shipped_at = null, $delivered_at = null, $created_at = null) {
        $this->id = $id;
        $this->order_id = $order_id;
        $this->carrier = $carrier;
        $this->tracking_number = $tracking_number;
        $this->status = $status;
        $this->address = $address;
        $this->city = $city;
        $this->postal_code = $postal_code;
        $this->country = $country;
        $this->shipping_cost = $shipping_cost;
        $this->estimated_delivery = $estimated_delivery;
        $this->shipped_at = $shipped_at;
        $this->delivered_at = $delivered_at;
        $this->created_at = $created_at;
    }

    public static function findAll() {
        $db = Database::getConnexion();
        $sql = "SELECT s.*, c.first_name, c.last_name, c.email 
                FROM shippings s 
                LEFT JOIN orders o ON s.order_id = o.id 
                LEFT JOIN customers c ON o.customer_id = c. id 
                ORDER BY s.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM shippings WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! $row) return null;
        return new Shipping(
            intval($row["id"]),
            intval($row["order_id"]),
            $row["carrier"],
            $row["tracking_number"],
            $row["status"],
            $row["address"],
            $row["city"],
            $row["postal_code"],
            $row["country"],
            floatval($row["shipping_cost"]),
            $row["estimated_delivery"],
            $row["shipped_at"],
            $row["delivered_at"],
            $row["created_at"]
        );
    }

    public static function findByTrackingNumber($trackingNumber) {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM shippings WHERE tracking_number = :tracking_number LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(["tracking_number" => $trackingNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new Shipping(
            intval($row["id"]),
            intval($row["order_id"]),
            $row["carrier"],
            $row["tracking_number"],
            $row["status"],
            $row["address"],
            $row["city"],
            $row["postal_code"],
            $row["country"],
            floatval($row["shipping_cost"]),
            $row["estimated_delivery"],
            $row["shipped_at"],
            $row["delivered_at"],
            $row["created_at"]
        );
    }

    public static function findPending() {
        $db = Database::getConnexion();
        $sql = "SELECT s.*, c.first_name, c. last_name, c.email 
                FROM shippings s 
                LEFT JOIN orders o ON s.order_id = o.id 
                LEFT JOIN customers c ON o.customer_id = c. id 
                WHERE s.status IN ('processing', 'shipped', 'in_transit') 
                ORDER BY s. created_at ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function generateTrackingNumber() {
        return "IMP-" . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6)) . "-" . strtoupper(substr(md5(time()), 0, 4));
    }

    public static function create($data) {
        $db = Database::getConnexion();
        $trackingNumber = isset($data["tracking_number"]) ?  $data["tracking_number"] : self::generateTrackingNumber();
        $estimatedDelivery = isset($data["estimated_delivery"]) ?  $data["estimated_delivery"] : date("Y-m-d", strtotime("+7 days"));
        
        $sql = "INSERT INTO shippings (order_id, carrier, tracking_number, status, address, city, postal_code, country, shipping_cost, estimated_delivery, created_at) 
                VALUES (:order_id, :carrier, :tracking_number, 'processing', :address, :city, :postal_code, :country, :shipping_cost, :estimated_delivery, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            "order_id" => $data["order_id"],
            "carrier" => isset($data["carrier"]) ? $data["carrier"] : "ImpactShop Express",
            "tracking_number" => $trackingNumber,
            "address" => isset($data["address"]) ?  $data["address"] : "",
            "city" => isset($data["city"]) ? $data["city"] : "",
            "postal_code" => isset($data["postal_code"]) ? $data["postal_code"] : "",
            "country" => isset($data["country"]) ? $data["country"] : "Tunisia",
            "shipping_cost" => isset($data["shipping_cost"]) ? $data["shipping_cost"] : 0,
            "estimated_delivery" => $estimatedDelivery
        ]);
        return intval($db->lastInsertId());
    }

    public function updateStatus($newStatus) {
        $validStatuses = ["processing", "shipped", "in_transit", "delivered"];
        if (! in_array($newStatus, $validStatuses)) return false;
        
        $db = Database::getConnexion();
        $sql = "UPDATE shippings SET status = :status";
        if ($newStatus === "shipped") {
            $sql .= ", shipped_at = NOW()";
        } elseif ($newStatus === "delivered") {
            $sql .= ", delivered_at = NOW()";
        }
        $sql .= " WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            "status" => $newStatus,
            "id" => $this->id
        ]);
        $this->status = $newStatus;
        return true;
    }

    public function getId() { return $this->id; }
    public function getOrderId() { return $this->order_id; }
    public function getCarrier() { return $this->carrier; }
    public function getTrackingNumber() { return $this->tracking_number; }
    public function getStatus() { return $this->status; }
    public function getAddress() { return $this->address; }
    public function getCity() { return $this->city; }
    public function getPostalCode() { return $this->postal_code; }
    public function getCountry() { return $this->country; }
    public function getShippingCost() { return $this->shipping_cost; }
    public function getEstimatedDelivery() { return $this->estimated_delivery; }
    public function getShippedAt() { return $this->shipped_at; }
    public function getDeliveredAt() { return $this->delivered_at; }
    public function getCreatedAt() { return $this->created_at; }
    public function isDelivered() { return $this->status === "delivered"; }
}
