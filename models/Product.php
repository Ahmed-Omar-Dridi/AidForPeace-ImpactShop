<?php
/**
 * Modèle Product - Gestion des produits
 * Respect des principes POO et utilisation obligatoire de PDO
 */

class Product {
    private $conn;
    private $table_name = "products";

    // Propriétés de l'entité Product
    public $id;
    public $name_en;
    public $name_fr;
    public $description_en;
    public $description_fr;
    public $price;
    public $img_name;
    public $created_at;
    public $updated_at;

    /**
     * Constructeur
     * @param PDO $db Connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * CREATE - Ajouter un nouveau produit
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name_en, name_fr, description_en, description_fr, price, img_name, created_at) 
                  VALUES 
                  (:name_en, :name_fr, :description_en, :description_fr, :price, :img_name, NOW())";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->name_en = htmlspecialchars(strip_tags($this->name_en));
        $this->name_fr = htmlspecialchars(strip_tags($this->name_fr));
        $this->description_en = htmlspecialchars(strip_tags($this->description_en));
        $this->description_fr = htmlspecialchars(strip_tags($this->description_fr));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->img_name = htmlspecialchars(strip_tags($this->img_name));

        // Liaison des paramètres
        $stmt->bindParam(":name_en", $this->name_en);
        $stmt->bindParam(":name_fr", $this->name_fr);
        $stmt->bindParam(":description_en", $this->description_en);
        $stmt->bindParam(":description_fr", $this->description_fr);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":img_name", $this->img_name);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * READ - Récupérer tous les produits
     * @return PDOStatement
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * READ - Récupérer un produit par ID
     * @return void
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name_en = $row['name_en'];
            $this->name_fr = $row['name_fr'];
            $this->description_en = $row['description_en'];
            $this->description_fr = $row['description_fr'];
            $this->price = $row['price'];
            $this->img_name = $row['img_name'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        }
    }

    /**
     * UPDATE - Modifier un produit existant
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET 
                      name_en = :name_en,
                      name_fr = :name_fr,
                      description_en = :description_en,
                      description_fr = :description_fr,
                      price = :price,
                      img_name = :img_name,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->name_en = htmlspecialchars(strip_tags($this->name_en));
        $this->name_fr = htmlspecialchars(strip_tags($this->name_fr));
        $this->description_en = htmlspecialchars(strip_tags($this->description_en));
        $this->description_fr = htmlspecialchars(strip_tags($this->description_fr));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->img_name = htmlspecialchars(strip_tags($this->img_name));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Liaison des paramètres
        $stmt->bindParam(":name_en", $this->name_en);
        $stmt->bindParam(":name_fr", $this->name_fr);
        $stmt->bindParam(":description_en", $this->description_en);
        $stmt->bindParam(":description_fr", $this->description_fr);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":img_name", $this->img_name);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * DELETE - Supprimer un produit
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Validation des données du produit
     * @return array Tableau des erreurs (vide si pas d'erreur)
     */
    public function validate() {
        $errors = [];

        // Validation nom anglais
        if (empty($this->name_en) || strlen(trim($this->name_en)) < 3) {
            $errors[] = "Le nom en anglais doit contenir au moins 3 caractères.";
        }

        // Validation nom français
        if (empty($this->name_fr) || strlen(trim($this->name_fr)) < 3) {
            $errors[] = "Le nom en français doit contenir au moins 3 caractères.";
        }

        // Validation description anglaise
        if (empty($this->description_en) || strlen(trim($this->description_en)) < 10) {
            $errors[] = "La description en anglais doit contenir au moins 10 caractères.";
        }

        // Validation description française
        if (empty($this->description_fr) || strlen(trim($this->description_fr)) < 10) {
            $errors[] = "La description en français doit contenir au moins 10 caractères.";
        }

        // Validation prix
        if (empty($this->price) || !is_numeric($this->price) || $this->price <= 0) {
            $errors[] = "Le prix doit être un nombre positif.";
        }

        // Validation nom de l'image
        if (empty($this->img_name) || strlen(trim($this->img_name)) < 3) {
            $errors[] = "Le nom de l'image est obligatoire.";
        }

        return $errors;
    }
}
?>