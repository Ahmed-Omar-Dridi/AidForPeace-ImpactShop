<?php
/**
 * Modèle Programme de Fidélité - Points
 * Date: 2025-12-05
 * Author: dridi10331
 */

require_once __DIR__ . '/../config/DATABASE.PHP';

class LoyaltyPoint {
    
    // Règle: 1 TND dépensé = 1 point
    const POINTS_PER_TND = 1;
    
    // Niveaux de fidélité
    const LEVELS = [
        'bronze' => ['min' => 0, 'max' => 499, 'discount' => 0, 'name' => 'Bronze', 'icon' => '🥉'],
        'silver' => ['min' => 500, 'max' => 1499, 'discount' => 5, 'name' => 'Argent', 'icon' => '🥈'],
        'gold' => ['min' => 1500, 'max' => 4999, 'discount' => 10, 'name' => 'Or', 'icon' => '🥇'],
        'platinum' => ['min' => 5000, 'max' => PHP_INT_MAX, 'discount' => 15, 'name' => 'Platine', 'icon' => '💎']
    ];

    /**
     * Obtenir le solde de points d'un client
     */
    public static function getBalance($customerId) {
        $db = Database::getConnexion();
        $sql = "SELECT COALESCE(SUM(
                    CASE WHEN type = 'earned' OR type = 'bonus' THEN points 
                         WHEN type = 'redeemed' OR type = 'expired' THEN -points 
                         ELSE 0 END
                ), 0) as balance
                FROM loyalty_points 
                WHERE customer_id = :customer_id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['customer_id' => $customerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return max(0, intval($row['balance']));
    }

    /**
     * Obtenir le total des points gagnés (pour niveau)
     */
    public static function getTotalEarned($customerId) {
        $db = Database::getConnexion();
        $sql = "SELECT COALESCE(SUM(points), 0) as total
                FROM loyalty_points 
                WHERE customer_id = :customer_id AND (type = 'earned' OR type = 'bonus')";
        $stmt = $db->prepare($sql);
        $stmt->execute(['customer_id' => $customerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($row['total']);
    }

    /**
     * Obtenir le niveau de fidélité d'un client
     */
    public static function getLevel($customerId) {
        $totalEarned = self::getTotalEarned($customerId);
        
        foreach (self::LEVELS as $key => $level) {
            if ($totalEarned >= $level['min'] && $totalEarned <= $level['max']) {
                return array_merge(['key' => $key], $level);
            }
        }
        
        return array_merge(['key' => 'bronze'], self::LEVELS['bronze']);
    }

    /**
     * Ajouter des points (après un achat)
     */
    public static function earnPoints($customerId, $orderId, $amount) {
        $db = Database::getConnexion();
        $points = floor($amount * self::POINTS_PER_TND);
        
        if ($points <= 0) return false;
        
        $sql = "INSERT INTO loyalty_points (customer_id, order_id, points, type, description, created_at)
                VALUES (:customer_id, :order_id, :points, 'earned', :description, NOW())";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'points' => $points,
            'description' => "Points gagnés pour la commande #" . str_pad($orderId, 6, '0', STR_PAD_LEFT)
        ]);
    }

    /**
     * Ajouter des points bonus
     */
    public static function addBonus($customerId, $points, $description = 'Points bonus') {
        $db = Database::getConnexion();
        $sql = "INSERT INTO loyalty_points (customer_id, points, type, description, created_at)
                VALUES (:customer_id, :points, 'bonus', :description, NOW())";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'customer_id' => $customerId,
            'points' => $points,
            'description' => $description
        ]);
    }

    /**
     * Utiliser des points
     */
    public static function redeemPoints($customerId, $points, $orderId = null, $description = '') {
        $db = Database::getConnexion();
        
        $balance = self::getBalance($customerId);
        if ($balance < $points) {
            return false;
        }
        
        $sql = "INSERT INTO loyalty_points (customer_id, order_id, points, type, description, created_at)
                VALUES (:customer_id, :order_id, :points, 'redeemed', :description, NOW())";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'points' => $points,
            'description' => $description ?: "Points utilisés"
        ]);
    }

    /**
     * Calculer la réduction en TND (100 points = 1 TND)
     */
    public static function pointsToDiscount($points) {
        return $points / 100;
    }

    /**
     * Historique des points d'un client
     */
    public static function getHistory($customerId, $limit = 20) {
        $db = Database::getConnexion();
        $sql = "SELECT lp.*, o.id as order_number
                FROM loyalty_points lp
                LEFT JOIN orders o ON lp.order_id = o.id
                WHERE lp.customer_id = :customer_id
                ORDER BY lp.created_at DESC
                LIMIT :lim";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Points à expirer dans X jours
     */
    public static function getExpiringPoints($customerId, $days = 30) {
        $db = Database::getConnexion();
        $sql = "SELECT COALESCE(SUM(points), 0) as expiring
                FROM loyalty_points 
                WHERE customer_id = :customer_id 
                AND (type = 'earned' OR type = 'bonus')
                AND created_at < DATE_SUB(NOW(), INTERVAL (365 - :days) DAY)
                AND created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
        $stmt = $db->prepare($sql);
        $stmt->execute(['customer_id' => $customerId, 'days' => $days]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($row['expiring']);
    }

    /**
     * Statistiques globales (admin)
     */
    public static function getGlobalStats() {
        $db = Database::getConnexion();
        $stats = [];
        
        $stmt = $db->query("SELECT COALESCE(SUM(points), 0) as total FROM loyalty_points WHERE type IN ('earned', 'bonus')");
        $stats['total_earned'] = intval($stmt->fetch(PDO::FETCH_ASSOC)['total']);
        
        $stmt = $db->query("SELECT COALESCE(SUM(points), 0) as total FROM loyalty_points WHERE type = 'redeemed'");
        $stats['total_redeemed'] = intval($stmt->fetch(PDO::FETCH_ASSOC)['total']);
        
        $stats['in_circulation'] = $stats['total_earned'] - $stats['total_redeemed'];
        
        $stmt = $db->query("SELECT COUNT(DISTINCT customer_id) as count FROM loyalty_points");
        $stats['total_members'] = intval($stmt->fetch(PDO::FETCH_ASSOC)['count']);
        
        return $stats;
    }

    /**
     * Top clients fidèles
     */
    public static function getTopMembers($limit = 10) {
        $db = Database::getConnexion();
        $sql = "SELECT 
                    c.id, c.first_name, c. last_name, c.email,
                    COALESCE(SUM(CASE WHEN lp.type IN ('earned', 'bonus') THEN lp.points ELSE 0 END), 0) as total_earned,
                    COALESCE(SUM(CASE WHEN lp.type IN ('earned', 'bonus') THEN lp.points 
                                      WHEN lp. type = 'redeemed' THEN -lp.points ELSE 0 END), 0) as balance
                FROM customers c
                LEFT JOIN loyalty_points lp ON c.id = lp.customer_id
                GROUP BY c.id
                HAVING total_earned > 0
                ORDER BY total_earned DESC
                LIMIT :lim";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>