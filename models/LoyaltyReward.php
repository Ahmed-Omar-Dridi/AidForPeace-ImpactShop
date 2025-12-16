<?php
/**
 * Modèle LoyaltyReward - Récompenses Fidélité
 * Date: 2025-12-05
 * Author: dridi10331
 */

require_once __DIR__ . '/../config/DATABASE.PHP';

class LoyaltyReward {
    
    private $id;
    private $name;
    private $description;
    private $points_required;
    private $reward_type;
    private $value;
    private $icon;
    private $is_active;
    private $created_at;

    // Types de récompenses disponibles
    const TYPES = [
        'discount_percent' => 'Réduction en pourcentage',
        'discount_fixed' => 'Réduction fixe (TND)',
        'free_shipping' => 'Livraison gratuite',
        'free_product' => 'Produit offert',
        'bonus_points' => 'Points bonus',
        'exclusive_access' => 'Accès exclusif'
    ];

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->points_required = $data['points_required'] ?? 0;
        $this->reward_type = $data['reward_type'] ?? 'discount_percent';
        $this->value = $data['value'] ??  0;
        $this->icon = $data['icon'] ??  'fa-gift';
        $this->is_active = $data['is_active'] ?? 1;
        $this->created_at = $data['created_at'] ?? null;
    }

    // ==================== CRUD ====================

    /**
     * Récupérer toutes les récompenses
     */
    public static function findAll() {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM loyalty_rewards ORDER BY points_required ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les récompenses actives
     */
    public static function findActive() {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM loyalty_rewards WHERE is_active = 1 ORDER BY points_required ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer une récompense par ID
     */
    public static function findById($id) {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM loyalty_rewards WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new LoyaltyReward($row) : null;
    }

    /**
     * Récupérer les récompenses accessibles pour un client
     */
    public static function findAvailableForCustomer($customerId) {
        require_once __DIR__ . '/LoyaltyPoint.php';
        $balance = LoyaltyPoint::getBalance($customerId);
        
        $db = Database::getConnexion();
        $sql = "SELECT * FROM loyalty_rewards 
                WHERE is_active = 1 AND points_required <= :balance 
                ORDER BY points_required DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['balance' => $balance]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Créer une nouvelle récompense
     */
    public static function create($data) {
        $db = Database::getConnexion();
        $sql = "INSERT INTO loyalty_rewards (name, description, points_required, reward_type, value, icon, is_active, created_at) 
                VALUES (:name, :description, :points_required, :reward_type, :value, :icon, :is_active, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'points_required' => intval($data['points_required']),
            'reward_type' => $data['reward_type'] ?? 'discount_percent',
            'value' => floatval($data['value'] ?? 0),
            'icon' => $data['icon'] ??  'fa-gift',
            'is_active' => isset($data['is_active']) ?  1 : 1
        ]);
        return $db->lastInsertId();
    }

    /**
     * Mettre à jour une récompense
     */
    public static function update($id, $data) {
        $db = Database::getConnexion();
        $sql = "UPDATE loyalty_rewards SET 
                    name = :name, 
                    description = :description, 
                    points_required = :points_required, 
                    reward_type = :reward_type, 
                    value = :value, 
                    icon = :icon, 
                    is_active = :is_active 
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'points_required' => intval($data['points_required']),
            'reward_type' => $data['reward_type'] ?? 'discount_percent',
            'value' => floatval($data['value'] ?? 0),
            'icon' => $data['icon'] ?? 'fa-gift',
            'is_active' => isset($data['is_active']) ? 1 : 0
        ]);
    }

    /**
     * Supprimer une récompense
     */
    public static function delete($id) {
        $db = Database::getConnexion();
        $sql = "DELETE FROM loyalty_rewards WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Activer/Désactiver une récompense
     */
    public static function toggleActive($id) {
        $db = Database::getConnexion();
        $sql = "UPDATE loyalty_rewards SET is_active = NOT is_active WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // ==================== Redemption ====================

    /**
     * Échanger une récompense
     */
    public static function redeem($customerId, $rewardId) {
        $reward = self::findById($rewardId);
        if (!$reward) {
            return ['success' => false, 'error' => 'Récompense introuvable'];
        }

        require_once __DIR__ . '/LoyaltyPoint.php';
        $balance = LoyaltyPoint::getBalance($customerId);

        if ($balance < $reward->getPointsRequired()) {
            return ['success' => false, 'error' => 'Solde de points insuffisant'];
        }

        // Déduire les points
        $deducted = LoyaltyPoint::redeemPoints(
            $customerId, 
            $reward->getPointsRequired(), 
            null, 
            'Échange: ' . $reward->getName()
        );

        if (! $deducted) {
            return ['success' => false, 'error' => 'Erreur lors de la déduction des points'];
        }

        // Enregistrer l'échange
        $db = Database::getConnexion();
        $sql = "INSERT INTO loyalty_redemptions (customer_id, reward_id, points_used, status, created_at) 
                VALUES (:customer_id, :reward_id, :points_used, 'active', NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'customer_id' => $customerId,
            'reward_id' => $rewardId,
            'points_used' => $reward->getPointsRequired()
        ]);

        $redemptionId = $db->lastInsertId();

        // Générer un code si nécessaire
        $code = self::generateRedemptionCode($redemptionId);

        return [
            'success' => true,
            'redemption_id' => $redemptionId,
            'code' => $code,
            'reward' => $reward,
            'message' => 'Récompense échangée avec succès!'
        ];
    }

    /**
     * Générer un code de rédemption unique
     */
    private static function generateRedemptionCode($redemptionId) {
        $prefix = 'RWD';
        $random = strtoupper(substr(md5(uniqid() . $redemptionId), 0, 8));
        return $prefix . '-' . $random;
    }

    /**
     * Vérifier et appliquer un code de récompense
     */
    public static function applyRedemptionCode($code, $orderId) {
        $db = Database::getConnexion();
        
        // Trouver la rédemption
        $sql = "SELECT r.*, lr.reward_type, lr.value, lr.name as reward_name
                FROM loyalty_redemptions r
                JOIN loyalty_rewards lr ON r.reward_id = lr.id
                WHERE r.code = :code AND r.status = 'active'
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(['code' => $code]);
        $redemption = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$redemption) {
            return ['success' => false, 'error' => 'Code invalide ou déjà utilisé'];
        }

        // Marquer comme utilisé
        $sql = "UPDATE loyalty_redemptions SET status = 'used', used_at = NOW(), order_id = :order_id WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $redemption['id'], 'order_id' => $orderId]);

        return [
            'success' => true,
            'reward_type' => $redemption['reward_type'],
            'value' => $redemption['value'],
            'reward_name' => $redemption['reward_name']
        ];
    }

    /**
     * Récupérer l'historique des échanges d'un client
     */
    public static function getCustomerRedemptions($customerId) {
        $db = Database::getConnexion();
        $sql = "SELECT r.*, lr.name as reward_name, lr.icon, lr.reward_type, lr.value
                FROM loyalty_redemptions r
                JOIN loyalty_rewards lr ON r.reward_id = lr.id
                WHERE r.customer_id = :customer_id
                ORDER BY r.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['customer_id' => $customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== Getters ====================

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function getPointsRequired() { return $this->points_required; }
    public function getRewardType() { return $this->reward_type; }
    public function getValue() { return $this->value; }
    public function getIcon() { return $this->icon; }
    public function isActive() { return $this->is_active; }
    public function getCreatedAt() { return $this->created_at; }

    /**
     * Obtenir le libellé du type
     */
    public function getTypeLabel() {
        return self::TYPES[$this->reward_type] ?? $this->reward_type;
    }

    /**
     * Calculer la valeur de la récompense pour une commande
     */
    public function calculateDiscount($orderTotal) {
        switch ($this->reward_type) {
            case 'discount_percent':
                return $orderTotal * ($this->value / 100);
            case 'discount_fixed':
                return min($this->value, $orderTotal);
            case 'free_shipping':
                return 0; // Géré séparément
            default:
                return 0;
        }
    }
}
?>