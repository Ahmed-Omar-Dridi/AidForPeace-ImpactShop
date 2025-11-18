<?php
/**
 * Contrôleur ProductController
 * Gestion de la logique métier pour les produits
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private $db;
    private $product;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
    }

    /**
     * Afficher la liste de tous les produits (BackOffice)
     */
    public function index() {
        $stmt = $this->product->readAll();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../views/admin/product_list.php';
    }

    /**
     * Afficher le formulaire de création d'un produit
     */
    public function create() {
        include __DIR__ . '/../views/admin/product_form.php';
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $this->product->name_en = $_POST['name_en'] ?? '';
            $this->product->name_fr = $_POST['name_fr'] ?? '';
            $this->product->description_en = $_POST['description_en'] ?? '';
            $this->product->description_fr = $_POST['description_fr'] ?? '';
            $this->product->price = $_POST['price'] ?? '';
            $this->product->img_name = $_POST['img_name'] ?? '';

            // Validation côté serveur (obligatoire)
            $errors = $this->product->validate();

            if (empty($errors)) {
                if ($this->product->create()) {
                    $_SESSION['success'] = "Produit créé avec succès !";
                    header("Location: index.php?controller=product&action=index");
                    exit();
                } else {
                    $_SESSION['error'] = "Erreur lors de la création du produit.";
                }
            } else {
                $_SESSION['errors'] = $errors;
            }

            // Retour au formulaire avec les erreurs
            include __DIR__ . '/../views/admin/product_form.php';
        }
    }

    /**
     * Afficher le formulaire d'édition d'un produit
     */
    public function edit() {
        if (isset($_GET['id'])) {
            $this->product->id = $_GET['id'];
            $this->product->readOne();
            
            include __DIR__ . '/../views/admin/product_form.php';
        } else {
            $_SESSION['error'] = "ID du produit manquant.";
            header("Location: index.php?controller=product&action=index");
            exit();
        }
    }

    /**
     * Mettre à jour un produit existant
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->product->id = $_POST['id'] ?? '';
            $this->product->name_en = $_POST['name_en'] ?? '';
            $this->product->name_fr = $_POST['name_fr'] ?? '';
            $this->product->description_en = $_POST['description_en'] ?? '';
            $this->product->description_fr = $_POST['description_fr'] ?? '';
            $this->product->price = $_POST['price'] ?? '';
            $this->product->img_name = $_POST['img_name'] ?? '';

            // Validation côté serveur
            $errors = $this->product->validate();

            if (empty($errors)) {
                if ($this->product->update()) {
                    $_SESSION['success'] = "Produit mis à jour avec succès !";
                    header("Location: index.php?controller=product&action=index");
                    exit();
                } else {
                    $_SESSION['error'] = "Erreur lors de la mise à jour du produit.";
                }
            } else {
                $_SESSION['errors'] = $errors;
            }

            // Retour au formulaire avec les erreurs
            include __DIR__ . '/../views/admin/product_form.php';
        }
    }

    /**
     * Supprimer un produit
     */
    public function delete() {
        if (isset($_GET['id'])) {
            $this->product->id = $_GET['id'];
            
            if ($this->product->delete()) {
                $_SESSION['success'] = "Produit supprimé avec succès !";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression du produit.";
            }
            
            header("Location: index.php?controller=product&action=index");
            exit();
        }
    }

    /**
     * Afficher les produits dans le FrontOffice (boutique)
     */
    public function shop() {
        $stmt = $this->product->readAll();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../views/shop/product_catalog.php';
    }

    /**
     * API JSON pour récupérer tous les produits (pour JavaScript)
     */
    public function apiGetAll() {
        header('Content-Type: application/json');
        $stmt = $this->product->readAll();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
        exit();
    }

    /**
     * API JSON pour récupérer un produit par ID
     */
    public function apiGetOne() {
        header('Content-Type: application/json');
        if (isset($_GET['id'])) {
            $this->product->id = $_GET['id'];
            $this->product->readOne();
            
            $product_arr = array(
                "id" => $this->product->id,
                "name_en" => $this->product->name_en,
                "name_fr" => $this->product->name_fr,
                "description_en" => $this->product->description_en,
                "description_fr" => $this->product->description_fr,
                "price" => $this->product->price,
                "img_name" => $this->product->img_name
            );
            
            echo json_encode($product_arr);
        }
        exit();
    }
}
?>