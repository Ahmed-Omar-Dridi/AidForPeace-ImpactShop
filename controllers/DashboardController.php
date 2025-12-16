<?php
/**
 * Contrôleur Dashboard - Statistiques et tableaux de bord
 * Date: 2025-11-25
 * Author: dridi10331
 */

require_once __DIR__ . '/../config/DATABASE.PHP';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';

class DashboardController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnexion();
    }
    
    /**
     * Afficher le dashboard principal
     */
    public function index() {
        // Récupérer toutes les commandes pour statistiques (returns array)
        $orders = Order::findAll();
        
        // Calculer les statistiques manuellement
        $stats = $this->calculateStatistics($orders);
        
        // Récupérer les 5 commandes récentes
        $recentOrders = array_slice($orders, 0, 5);
        
        // Récupérer les 5 produits les plus vendus
        $topProducts = $this->getTopProducts(5);
        
        // Récupérer les statistiques mensuelles
        $monthlyStats = $this->getMonthlyStats(12);
        
        // Récupérer les informations sur les admins, utilisateurs et messagerie
        $adminStats = $this->getAdminStats();
        $userStats = $this->getUserStats();
        $messagingStats = $this->getMessagingStats();
        $recentConversations = $this->getRecentConversations(5);
        $recentMessages = $this->getRecentMessages(5);
        
        // Afficher la vue
        require __DIR__ . '/../views/admin/dashboard.php';
    }
    
    /**
     * Calculer les statistiques
     */
    private function calculateStatistics($orders) {
        $stats = [
            'total_revenue' => 0,
            'monthly_revenue' => 0,
            'total_orders' => count($orders),
            'pending_orders' => 0,
            'total_customers' => 0,
            'average_order' => 0
        ];
        
        $currentMonth = date('Y-m');
        
        foreach ($orders as $order) {
            $status = $order['status'] ?? '';
            $totalAmount = floatval($order['total_amount'] ?? 0);
            $createdAt = $order['created_at'] ?? '';
            
            // Revenu total (seulement paid)
            if ($status === 'paid') {
                $stats['total_revenue'] += $totalAmount;
            }
            
            // Revenu du mois
            if ($status === 'paid' && strpos($createdAt, $currentMonth) === 0) {
                $stats['monthly_revenue'] += $totalAmount;
            }
            
            // Commandes en attente
            if ($status === 'pending') {
                $stats['pending_orders']++;
            }
        }
        
        // Total clients (requête séparée)
        try {
            $query = "SELECT COUNT(*) as total FROM customers";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_customers'] = $result['total'] ?? 0;
        } catch (Exception $e) {
            $stats['total_customers'] = 0;
        }
        
        // Panier moyen
        if ($stats['total_orders'] > 0) {
            $stats['average_order'] = $stats['total_revenue'] / $stats['total_orders'];
        }
        
        return $stats;
    }
    
    /**
     * Récupérer les produits les plus vendus
     */
    private function getTopProducts($limit = 5) {
        $query = "SELECT 
                    p.id,
                    p.name_fr,
                    p.name_en,
                    p.price,
                    p.img_name,
                    COALESCE(SUM(oi.quantity), 0) as total_sold,
                    COALESCE(SUM(oi.subtotal), 0) as total_revenue
                  FROM products p
                  LEFT JOIN order_items oi ON p.id = oi.product_id
                  GROUP BY p.id
                  ORDER BY total_sold DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupérer les statistiques mensuelles
     */
    private function getMonthlyStats($months = 12) {
        $query = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COALESCE(SUM(total_amount), 0) as revenue,
                    COUNT(*) as order_count
                  FROM orders
                  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
                  AND status = 'paid'
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                  ORDER BY month DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupérer les statistiques des administrateurs (depuis la base 'user')
     */
    private function getAdminStats() {
        try {
            $userDb = new PDO('mysql:host=localhost;dbname=user;charset=utf8mb4', 'root', '');
            $userDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $query = "SELECT 
                        COUNT(*) as total_admins,
                        SUM(CASE WHEN email_verified = 1 THEN 1 ELSE 0 END) as active_admins
                      FROM user
                      WHERE role = 'admin'";
            
            $stmt = $userDb->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_admins' => $result['total_admins'] ?? 0,
                'active_admins' => $result['active_admins'] ?? 0,
                'admins_with_login' => 0,
                'list' => $this->getAdminListFromUserDb()
            ];
        } catch (Exception $e) {
            error_log("Erreur getAdminStats: " . $e->getMessage());
            return ['total_admins' => 0, 'active_admins' => 0, 'admins_with_login' => 0, 'list' => []];
        }
    }
    
    /**
     * Récupérer la liste des administrateurs (depuis la base 'user')
     */
    private function getAdminListFromUserDb() {
        try {
            $userDb = new PDO('mysql:host=localhost;dbname=user;charset=utf8mb4', 'root', '');
            $userDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $query = "SELECT 
                        id_user as id,
                        CONCAT(Prenom, ' ', nom) as nom,
                        email,
                        email_verified as is_active,
                        created_at,
                        NULL as last_login
                      FROM user
                      WHERE role = 'admin'
                      ORDER BY created_at DESC";
            
            $stmt = $userDb->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAdminListFromUserDb: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer la liste des administrateurs (ancienne méthode)
     */
    private function getAdminList() {
        return $this->getAdminListFromUserDb();
    }
    
    /**
     * Récupérer les statistiques des utilisateurs (depuis la base 'user')
     */
    private function getUserStats() {
        try {
            // Connexion à la base 'user' séparée
            $userDb = new PDO('mysql:host=localhost;dbname=user;charset=utf8mb4', 'root', '');
            $userDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $query = "SELECT 
                        COUNT(*) as total_users,
                        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admins,
                        SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as regular_users,
                        SUM(CASE WHEN email_verified = 1 THEN 1 ELSE 0 END) as verified_users
                      FROM user";
            
            $stmt = $userDb->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_users' => $result['total_users'] ?? 0,
                'total_admins' => $result['total_admins'] ?? 0,
                'regular_users' => $result['regular_users'] ?? 0,
                'verified_users' => $result['verified_users'] ?? 0,
                'active_users' => $result['total_users'] ?? 0,
                'users_with_login' => 0,
                'active_last_7_days' => 0,
                'active_last_30_days' => 0,
                'list' => $this->getUserListFromUserDb(10)
            ];
        } catch (Exception $e) {
            error_log("Erreur getUserStats: " . $e->getMessage());
            return [
                'total_users' => 0,
                'active_users' => 0,
                'users_with_login' => 0,
                'active_last_7_days' => 0,
                'active_last_30_days' => 0,
                'list' => []
            ];
        }
    }
    
    /**
     * Récupérer la liste des utilisateurs (depuis la base 'user')
     */
    private function getUserListFromUserDb($limit = 10) {
        try {
            $userDb = new PDO('mysql:host=localhost;dbname=user;charset=utf8mb4', 'root', '');
            $userDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $query = "SELECT 
                        id_user as id,
                        CONCAT(Prenom, ' ', nom) as nom,
                        email,
                        role,
                        email_verified as is_active,
                        created_at
                      FROM user
                      ORDER BY created_at DESC
                      LIMIT :limit";
            
            $stmt = $userDb->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getUserListFromUserDb: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer la liste des utilisateurs (ancienne méthode - garde pour compatibilité)
     */
    private function getUserList($limit = 10) {
        return $this->getUserListFromUserDb($limit);
    }
    
    /**
     * Récupérer les statistiques de messagerie
     */
    private function getMessagingStats() {
        try {
            $conversationQuery = "SELECT 
                                    COUNT(*) as total_conversations,
                                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_conversations
                                  FROM conversations";
            
            $stmt = $this->db->prepare($conversationQuery);
            $stmt->execute();
            $convResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $messageQuery = "SELECT 
                                COUNT(*) as total_messages,
                                SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_messages
                              FROM messages";
            
            $stmt = $this->db->prepare($messageQuery);
            $stmt->execute();
            $msgResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_conversations' => $convResult['total_conversations'] ?? 0,
                'active_conversations' => $convResult['active_conversations'] ?? 0,
                'total_messages' => $msgResult['total_messages'] ?? 0,
                'unread_messages' => $msgResult['unread_messages'] ?? 0
            ];
        } catch (Exception $e) {
            error_log("Erreur getMessagingStats: " . $e->getMessage());
            return [
                'total_conversations' => 0,
                'active_conversations' => 0,
                'total_messages' => 0,
                'unread_messages' => 0
            ];
        }
    }
    
    /**
     * Récupérer les conversations récentes
     */
    private function getRecentConversations($limit = 5) {
        try {
            $query = "SELECT 
                        c.id,
                        c.subject,
                        c.created_at,
                        c.updated_at,
                        u1.nom as participant1_name,
                        u1.email as participant1_email,
                        u2.nom as participant2_name,
                        u2.email as participant2_email,
                        COUNT(m.id) as message_count,
                        SUM(CASE WHEN m.is_read = 0 THEN 1 ELSE 0 END) as unread_count,
                        MAX(m.content) as last_message,
                        MAX(m.created_at) as last_message_date
                      FROM conversations c
                      LEFT JOIN utilisateurs u1 ON c.participant1_id = u1.id
                      LEFT JOIN utilisateurs u2 ON c.participant2_id = u2.id
                      LEFT JOIN messages m ON c.id = m.conversation_id AND m.is_deleted = 0
                      WHERE c.is_active = 1
                      GROUP BY c.id
                      ORDER BY c.updated_at DESC
                      LIMIT :limit";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getRecentConversations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les messages récents
     */
    public function getRecentMessages($limit = 10) {
        try {
            $query = "SELECT 
                        m.id,
                        m.conversation_id,
                        m.content,
                        m.created_at,
                        m.is_read,
                        u1.nom as sender_name,
                        u1.email as sender_email,
                        u2.nom as receiver_name,
                        u2.email as receiver_email,
                        c.participant1_id,
                        c.participant2_id
                      FROM messages m
                      LEFT JOIN utilisateurs u1 ON m.sender_id = u1.id
                      LEFT JOIN utilisateurs u2 ON m.receiver_id = u2.id
                      LEFT JOIN conversations c ON m.conversation_id = c.id
                      WHERE m.is_deleted = 0
                      ORDER BY m.created_at DESC
                      LIMIT :limit";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getRecentMessages: " . $e->getMessage());
            return [];
        }
    }
}
?>