<?php
// models/Product.php
require_once __DIR__ . "/../config/database.php";
class Product {
    public static function findById($id)
    {
        $db = Database::getConnexion();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findAll(): array
    {
        $db = Database::getConnexion();
        $stmt = $db->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listProducts(): array {
        try {
            return Product::findAll();
        } catch (Exception $e) {
            // En cas d'erreur renvoyer tableau vide et logger
            error_log('ProductController::listProducts error: ' . $e->getMessage());
            return [];
        }
    }

    public function showProductById(int $id) {
        $product = Product::findById($id);
        if ($product) {
            echo "<h2>Informations du produit :</h2>";
            $product->show();
        } else {
            echo "<p>Produit introuvable (id=".htmlspecialchars($id).").</p>";
        }
    }

    public function addProduct(array $data) {
        $db = Database::getConnexion();
        try {
            $sql = "INSERT INTO products (name_en, name_fr, description_en, description_fr, price, img_name, stock, created_at)
                    VALUES (:name_en, :name_fr, :description_en, :description_fr, :price, :img_name, :stock, NOW())";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'name_en' => $data['name_en'] ?? '',
                'name_fr' => $data['name_fr'] ?? '',
                'description_en' => $data['description_en'] ?? null,
                'description_fr' => $data['description_fr'] ?? null,
                'price' => floatval($data['price'] ?? 0),
                'img_name' => $data['img_name'] ?? null,
                'stock' => intval($data['stock'] ?? 0)
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            error_log('ProductController::addProduct error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProduct(array $data, int $id): bool {
        $db = Database::getConnexion();
        try {
            $sql = "UPDATE products SET 
                        name_en = :name_en,
                        name_fr = :name_fr,
                        description_en = :description_en,
                        description_fr = :description_fr,
                        price = :price,
                        img_name = :img_name,
                        stock = :stock,
                        updated_at = NOW()
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'name_en' => $data['name_en'] ?? '',
                'name_fr' => $data['name_fr'] ?? '',
                'description_en' => $data['description_en'] ?? null,
                'description_fr' => $data['description_fr'] ?? null,
                'price' => floatval($data['price'] ?? 0),
                'img_name' => $data['img_name'] ?? null,
                'stock' => intval($data['stock'] ?? 0)
            ]);
            return true;
        } catch (Exception $e) {
            error_log('ProductController::updateProduct error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id): bool {
        $db = Database::getConnexion();
        try {
            $sql = "DELETE FROM products WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log('ProductController::deleteProduct error: ' . $e->getMessage());
            return false;
        }
    }

    // helper to set stock (admin)
    public function setStock(int $id, int $newStock): bool {
        $db = Database::getConnexion();
        try {
            $sql = "UPDATE products SET stock = :stock, updated_at = NOW() WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['stock' => $newStock, 'id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log('ProductController::setStock error: ' . $e->getMessage());
            return false;
        }
    }

    // Get average rating for a product
    public static function getAverageRating($productId) {
        $db = Database::getConnexion();
        try {
            $sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews 
                    FROM reviews 
                    WHERE product_id = :product_id AND is_approved = 1";
            $stmt = $db->prepare($sql);
            $stmt->execute(['product_id' => $productId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'average' => $result['average_rating'] ? round($result['average_rating'], 1) : 0,
                'total' => $result['total_reviews'] ? intval($result['total_reviews']) : 0
            ];
        } catch (Exception $e) {
            error_log('Product::getAverageRating error: ' . $e->getMessage());
            return ['average' => 0, 'total' => 0];
        }
    }

    // Generate star HTML
    public static function generateStars($rating) {
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
        
        $html = '<div class="stars-rating">';
        
        // Full stars
        for ($i = 0; $i < $fullStars; $i++) {
            $html .= '<i class="fas fa-star" style="color: #FFA500;"></i>';
        }
        
        // Half star
        if ($hasHalfStar) {
            $html .= '<i class="fas fa-star-half-alt" style="color: #FFA500;"></i>';
        }
        
        // Empty stars
        for ($i = 0; $i < $emptyStars; $i++) {
            $html .= '<i class="far fa-star" style="color: #FFA500;"></i>';
        }
        
        $html .= '</div>';
        return $html;
    }
}
?>