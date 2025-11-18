<?php
/**
 * Contrôleur OrderController
 * Gestion des commandes et du processus de paiement
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController {
    private $db;
    private $order;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->order = new Order($this->db);
    }

    /**
     * Afficher le formulaire de paiement
     */
    public function checkout() {
        include __DIR__ . '/../views/shop/checkout_form.php';
    }

    /**
     * Traiter le paiement et créer la commande
     */
    public function processPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=product&action=shop");
            exit();
        }

        // Récupérer les données du formulaire
        $customerData = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? ''
        ];

        // Nettoyer les données
        $customerData = array_map(function($value) {
            return htmlspecialchars(strip_tags(trim($value)));
        }, $customerData);

        // Récupérer les items du panier (envoyés en JSON)
        $cartItems = json_decode($_POST['cart_items'] ?? '[]', true);

        if (empty($cartItems)) {
            $_SESSION['error'] = "Votre panier est vide.";
            header("Location: index.php?controller=product&action=shop");
            exit();
        }

        // Validation des données client
        $errors = $this->order->validateCustomerData($customerData);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['customer_data'] = $customerData;
            header("Location: index.php?controller=order&action=checkout");
            exit();
        }

        // Créer la commande
        $this->order->payment_method = 'paypal';
        $order_id = $this->order->createOrder($customerData, $cartItems);

        if ($order_id) {
            // Succès - Rediriger vers la page de confirmation
            $_SESSION['success'] = "Commande créée avec succès !";
            $_SESSION['order_id'] = $order_id;
            header("Location: index.php?controller=order&action=confirmation&id=" . $order_id);
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la création de la commande. Veuillez réessayer.";
            header("Location: index.php?controller=order&action=checkout");
            exit();
        }
    }

    /**
     * Page de confirmation de commande
     */
    public function confirmation() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=product&action=shop");
            exit();
        }

        $order_id = $_GET['id'];
        $orderDetails = $this->order->getOrderDetails($order_id);

        if (!$orderDetails) {
            $_SESSION['error'] = "Commande introuvable.";
            header("Location: index.php?controller=product&action=shop");
            exit();
        }

        include __DIR__ . '/../views/shop/order_confirmation.php';
    }

    /**
     * Liste des commandes (BackOffice)
     */
    public function index() {
        $stmt = $this->order->getAllOrders();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../views/admin/order_list.php';
    }

    /**
     * Voir les détails d'une commande (BackOffice)
     */
    public function view() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=order&action=index");
            exit();
        }

        $order_id = $_GET['id'];
        $orderDetails = $this->order->getOrderDetails($order_id);

        if (!$orderDetails) {
            $_SESSION['error'] = "Commande introuvable.";
            header("Location: index.php?controller=order&action=index");
            exit();
        }

        include __DIR__ . '/../views/admin/order_details.php';
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateStatus() {
        if (!isset($_GET['id']) || !isset($_GET['status'])) {
            header("Location: index.php?controller=order&action=index");
            exit();
        }

        $order_id = $_GET['id'];
        $status = $_GET['status'];

        $allowed_statuses = ['pending', 'paid', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            $_SESSION['error'] = "Statut invalide.";
            header("Location: index.php?controller=order&action=index");
            exit();
        }

        if ($this->order->updateStatus($order_id, $status)) {
            $_SESSION['success'] = "Statut de la commande mis à jour.";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour du statut.";
        }

        header("Location: index.php?controller=order&action=view&id=" . $order_id);
        exit();
    }
}
?>