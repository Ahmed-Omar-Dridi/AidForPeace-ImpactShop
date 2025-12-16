<?php
require_once __DIR__ . "/../models/Product.php";
require_once __DIR__ . "/../config/DATABASE.PHP";

class ProductController {
    private $lastError = null;

    public function getLastError() {
        return $this->lastError;
    }

    public function listProducts() {
        try {
            return Product::findAll();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function showProductById($id) {
        $product = Product::findById($id);
        if ($product) {
            $product->show();
        } else {
            echo "<p>Produit introuvable.</p>";
        }
    }

    public function addProduct($data) {
        $db = Database::getConnexion();
        try {
            $sql = "INSERT INTO products (name_en, name_fr, description_en, description_fr, price, img_name, stock, category_id, created_at) VALUES (:name_en, :name_fr, :description_en, :description_fr, :price, :img_name, :stock, :category_id, NOW())";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                "name_en" => $data["name_en"],
                "name_fr" => $data["name_fr"],
                "description_en" => $data["description_en"],
                "description_fr" => $data["description_fr"],
                "price" => floatval($data["price"]),
                "img_name" => $data["img_name"],
                "stock" => intval($data["stock"]),
                "category_id" => isset($data["category_id"]) ? $data["category_id"] : null
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function updateProduct($data, $id) {
        $db = Database::getConnexion();
        try {
            $sql = "UPDATE products SET name_en = :name_en, name_fr = :name_fr, description_en = :description_en, description_fr = :description_fr, price = :price, img_name = :img_name, stock = :stock, category_id = :category_id, updated_at = NOW() WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                "id" => $id,
                "name_en" => $data["name_en"],
                "name_fr" => $data["name_fr"],
                "description_en" => $data["description_en"],
                "description_fr" => $data["description_fr"],
                "price" => floatval($data["price"]),
                "img_name" => $data["img_name"],
                "stock" => intval($data["stock"]),
                "category_id" => isset($data["category_id"]) ? $data["category_id"] : null
            ]);
            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function deleteProduct($id) {
        $db = Database::getConnexion();
        try {
            $sql = "DELETE FROM products WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(["id" => $id]);
            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}