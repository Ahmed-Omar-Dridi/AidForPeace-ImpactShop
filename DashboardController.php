<?php
/**
 * Contrôleur DashboardController
 * Gestion du tableau de bord administrateur
 */

require_once __DIR__ . '/../config/database.php';

class DashboardController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Afficher le dashboard principal
     */
    public function index() {
        // Récupérer les statistiques
        $stats = $this->getStatistics();
        
        // Récupérer les dernières commandes
        $recentOrders = $this->getRecentOrders(10);
        
        // Récupérer les produits les plus vendus
        $topProducts = $this->getTopProducts(5);
        
        // Récupérer les statistiques mensuelles
        $monthlyStats = $this->getMonthlyStats();
        
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Récupérer les statistiques générales
     */
    private function getStatistics() {
        $stats = [];

        // Total des revenus
        $query = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE status = 'paid'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_revenue'] = $result['total_revenue'] ?? 0;

        // Revenus du mois en cours
        $query = "SELECT SUM(total_amount) as monthly_revenue 
                  FROM orders 
                  WHERE status = 'paid' 
                  AND MONTH(created_at) = MONTH(CURRENT_DATE())
                  AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['monthly_revenue'] = $result['monthly_revenue'] ?? 0;

        // Nombre total de commandes
        $query = "SELECT COUNT(*) as total_orders FROM orders";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_orders'] = $result['total_orders'] ?? 0;

        // Commandes en attente
        $query = "SELECT COUNT(*) as pending_orders FROM orders WHERE status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['pending_orders'] = $result['pending_orders'] ?? 0;

        // Nombre total de clients
        $query = "SELECT COUNT(*) as total_customers FROM customers";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_customers'] = $result['total_customers'] ?? 0;

        // Nombre total de produits
        $query = "SELECT COUNT(*) as total_products FROM products";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_products'] = $result['total_products'] ?? 0;

        // Montant moyen par commande
        $stats['average_order'] = $stats['total_orders'] > 0 
            ? $stats['total_revenue'] / $stats['total_orders'] 
            : 0;

        return $stats;
    }

    /**
     * Récupérer les dernières commandes
     */
    private function getRecentOrders($limit = 10) {
        $query = "SELECT o.*, 
                         CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                         c.email as customer_email
                  FROM orders o
                  JOIN customers c ON o.customer_id = c.id
                  ORDER BY o.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les produits les plus vendus
     */
    private function getTopProducts($limit = 5) {
        $query = "SELECT p.id, p.name_fr, p.price, p.img_name,
                         SUM(oi.quantity) as total_sold,
                         SUM(oi.subtotal) as total_revenue
                  FROM products p
                  JOIN order_items oi ON p.id = oi.product_id
                  JOIN orders o ON oi.order_id = o.id
                  WHERE o.status = 'paid'
                  GROUP BY p.id
                  ORDER BY total_sold DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les statistiques mensuelles (12 derniers mois)
     */
    private function getMonthlyStats() {
        $query = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as order_count,
                    SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as revenue
                  FROM orders
                  WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                  ORDER BY month DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les statistiques par statut de paiement
     */
    private function getPaymentStatusStats() {
        $query = "SELECT 
                    status,
                    COUNT(*) as count,
                    SUM(total_amount) as total
                  FROM orders
                  GROUP BY status";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>