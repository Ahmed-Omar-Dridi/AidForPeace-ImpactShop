<?php
/**
 * LoyaltyController - Programme de fidélité
 */

require_once __DIR__ . '/../config/DATABASE.PHP';
require_once __DIR__ . '/../models/LoyaltyPoint.php';
require_once __DIR__ . '/../models/LoyaltyReward.php';

class LoyaltyController
{
    private $lastError = null;

    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Page principale du programme de fidélité
     */
    public function index()
    {
        $customerId = $this->getCustomerIdFromSession();
        $balance = 0;
        $level = LoyaltyPoint::LEVELS['bronze'];
        $level['key'] = 'bronze';
        $history = [];
        $rewards = LoyaltyReward::findActive();

        if ($customerId) {
            $balance = LoyaltyPoint::getBalance($customerId);
            $level = LoyaltyPoint::getLevel($customerId);
            $history = LoyaltyPoint::getHistory($customerId, 10);
        }

        require __DIR__ . '/../views/shop/loyalty.php';
    }

    /**
     * Page des récompenses disponibles
     */
    public function rewards()
    {
        $customerId = $this->getCustomerIdFromSession();
        $balance = 0;

        if ($customerId) {
            $balance = LoyaltyPoint::getBalance($customerId);
        }

        $rewards = LoyaltyReward::findActive();
        require __DIR__ . '/../views/shop/loyalty_rewards.php';
    }

    /**
     * Échanger une récompense
     */
    public function redeem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=loyalty&action=rewards');
            exit();
        }

        $customerId = $this->getCustomerIdFromSession();
        if (!$customerId) {
            $_SESSION['error'] = "Veuillez vous identifier pour échanger une récompense.";
            header('Location: index.php?controller=loyalty&action=index');
            exit();
        }

        $rewardId = intval($_POST['reward_id'] ?? 0);
        if (!$rewardId) {
            $_SESSION['error'] = "Récompense invalide.";
            header('Location: index.php?controller=loyalty&action=rewards');
            exit();
        }

        $result = LoyaltyReward::redeem($customerId, $rewardId);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            $_SESSION['reward_code'] = $result['code'] ?? null;
        } else {
            $_SESSION['error'] = $result['error'];
        }

        header('Location: index.php?controller=loyalty&action=rewards');
        exit();
    }

    /**
     * Vérifier les points d'un client (AJAX)
     */
    public function checkPoints()
    {
        header('Content-Type: application/json');

        $email = trim($_GET['email'] ?? '');
        if (empty($email)) {
            echo json_encode(['success' => false, 'error' => 'Email requis']);
            return;
        }

        try {
            $db = Database::getConnexion();
            $stmt = $db->prepare("SELECT id, first_name FROM customers WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$customer) {
                echo json_encode(['success' => false, 'error' => 'Client non trouvé']);
                return;
            }

            $balance = LoyaltyPoint::getBalance($customer['id']);
            $level = LoyaltyPoint::getLevel($customer['id']);

            echo json_encode([
                'success' => true,
                'name' => $customer['first_name'],
                'balance' => $balance,
                'level' => $level
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
        }
    }

    /**
     * Admin: Liste des membres fidélité
     */
    public function adminIndex()
    {
        $stats = LoyaltyPoint::getGlobalStats();
        $topMembers = LoyaltyPoint::getTopMembers(20);
        $rewards = LoyaltyReward::findAll();

        require __DIR__ . '/../views/admin/loyalty_list.php';
    }

    /**
     * Admin: Ajouter des points bonus
     */
    public function addBonus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=loyalty&action=adminIndex');
            exit();
        }

        $customerId = intval($_POST['customer_id'] ?? 0);
        $points = intval($_POST['points'] ?? 0);
        $description = trim($_POST['description'] ?? 'Points bonus');

        if ($customerId && $points > 0) {
            LoyaltyPoint::addBonus($customerId, $points, $description);
            $_SESSION['success'] = "$points points bonus ajoutés!";
        } else {
            $_SESSION['error'] = "Données invalides.";
        }

        header('Location: index.php?controller=loyalty&action=adminIndex');
        exit();
    }

    /**
     * Obtenir l'ID client depuis la session ou email
     */
    private function getCustomerIdFromSession()
    {
        if (isset($_SESSION['customer_id'])) {
            return $_SESSION['customer_id'];
        }

        // Permettre la vérification par email
        if (isset($_GET['email'])) {
            try {
                $db = Database::getConnexion();
                $stmt = $db->prepare("SELECT id FROM customers WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => trim($_GET['email'])]);
                $customer = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($customer) {
                    return $customer['id'];
                }
            } catch (Exception $e) {
                error_log("LoyaltyController error: " . $e->getMessage());
            }
        }

        return null;
    }
}
