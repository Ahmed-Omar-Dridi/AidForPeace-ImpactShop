<?php
require_once __DIR__ . "/../models/CATEGORY.PHP";
require_once __DIR__ . "/../config/DATABASE.PHP";

class CategoryController {
    private $lastError = null;

    public function getLastError() {
        return $this->lastError;
    }

    public function listCategories() {
        try {
            return Category::findAll();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function listActiveCategories() {
        try {
            return Category::findActive();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function showById($id) {
        $category = Category::findById($id);
        if ($category) {
            $category->show();
        } else {
            echo "<p>Categorie introuvable. </p>";
        }
    }

    public function addCategory($data) {
        $db = Database::getConnexion();
        try {
            $sql = "INSERT INTO categories (name_en, name_fr, description_en, description_fr, parent_id, img_name, is_active, created_at) VALUES (:name_en, :name_fr, :description_en, :description_fr, :parent_id, :img_name, 1, NOW())";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                "name_en" => $data["name_en"],
                "name_fr" => $data["name_fr"],
                "description_en" => isset($data["description_en"]) ? $data["description_en"] : null,
                "description_fr" => isset($data["description_fr"]) ? $data["description_fr"] : null,
                "parent_id" => isset($data["parent_id"]) ? $data["parent_id"] : null,
                "img_name" => isset($data["img_name"]) ? $data["img_name"] : null
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function updateCategory($data, $id) {
        $db = Database::getConnexion();
        try {
            $sql = "UPDATE categories SET name_en = :name_en, name_fr = :name_fr, description_en = :description_en, description_fr = :description_fr, parent_id = :parent_id, img_name = :img_name, is_active = :is_active WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                "id" => $id,
                "name_en" => $data["name_en"],
                "name_fr" => $data["name_fr"],
                "description_en" => isset($data["description_en"]) ? $data["description_en"] : null,
                "description_fr" => isset($data["description_fr"]) ? $data["description_fr"] : null,
                "parent_id" => isset($data["parent_id"]) ? $data["parent_id"] : null,
                "img_name" => isset($data["img_name"]) ?  $data["img_name"] : null,
                "is_active" => isset($data["is_active"]) ? 1 : 0
            ]);
            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function deleteCategory($id) {
        $db = Database::getConnexion();
        try {
            $db->prepare("UPDATE products SET category_id = NULL WHERE category_id = :id")->execute(["id" => $id]);
            $db->prepare("UPDATE categories SET parent_id = NULL WHERE parent_id = :id")->execute(["id" => $id]);
            $db->prepare("DELETE FROM categories WHERE id = :id")->execute(["id" => $id]);
            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function toggleActive($id) {
        $db = Database::getConnexion();
        try {
            $sql = "UPDATE categories SET is_active = NOT is_active WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(["id" => $id]);
            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}