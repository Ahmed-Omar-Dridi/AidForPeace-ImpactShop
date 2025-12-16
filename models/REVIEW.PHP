<?php
require_once __DIR__ . "/../config/database.php";

class Review {
    private $id;
    private $product_id;
    private $customer_id;
    private $rating;
    private $title;
    private $comment;
    private $is_verified;
    private $is_approved;
    private $created_at;

    public function __construct($id, $product_id, $customer_id, $rating, $title = null, $comment = null, $is_verified = false, $is_approved = false, $created_at = null) {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->customer_id = $customer_id;
        $this->rating = max(1, min(5, $rating));
        $this->title = $title;
        $this->comment = $comment;
        $this->is_verified = $is_verified;
        $this->is_approved = $is_approved;
        $this->created_at = $created_at;
    }

    public static function findAll() {
        $db = Database::getConnexion();
        $sql = "SELECT r.*, c.first_name, c.last_name, p.name_fr as product_name FROM reviews r JOIN customers c ON r. customer_id = c.id JOIN products p ON r. product_id = p.id ORDER BY r.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM reviews WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new Review(
            intval($row["id"]),
            intval($row["product_id"]),
            intval($row["customer_id"]),
            intval($row["rating"]),
            $row["title"],
            $row["comment"],
            (bool)$row["is_verified"],
            (bool)$row["is_approved"],
            $row["created_at"]
        );
    }

    public static function findPendingApproval() {
        $db = Database::getConnexion();
        $sql = "SELECT r.*, c.first_name, c. last_name, p.name_fr as product_name FROM reviews r JOIN customers c ON r.customer_id = c.id JOIN products p ON r. product_id = p.id WHERE r. is_approved = 0 ORDER BY r. created_at ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAverageRating($productId) {
        $db = Database::getConnexion();
        $sql = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = :product_id AND is_approved = 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(["product_id" => $productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round(floatval($row["avg_rating"]), 1);
    }

    public static function getReviewCount($productId) {
        $db = Database::getConnexion();
        $sql = "SELECT COUNT(*) as cnt FROM reviews WHERE product_id = :product_id AND is_approved = 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(["product_id" => $productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($row["cnt"]);
    }

    public function approve() {
        $db = Database::getConnexion();
        $sql = "UPDATE reviews SET is_approved = 1 WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(["id" => $this->id]);
    }

    public function reject() {
        $db = Database::getConnexion();
        $sql = "DELETE FROM reviews WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(["id" => $this->id]);
    }

    public function getId() { return $this->id; }
    public function getProductId() { return $this->product_id; }
    public function getCustomerId() { return $this->customer_id; }
    public function getRating() { return $this->rating; }
    public function getTitle() { return $this->title; }
    public function getComment() { return $this->comment; }
    public function isApproved() { return $this->is_approved; }
}