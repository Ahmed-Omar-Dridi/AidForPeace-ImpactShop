<?php

require_once 'models/AdminManager.php';
require_once 'models/user.php';

class AdminAdvancedController {
    private $adminManager;

    public function __construct() {
        $this->adminManager = new AdminManager();
    }

    // =============================================
    // BANNISSEMENT
    // =============================================

    public function banUser(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $userId = intval($_POST['user_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');

        if ($userId <= 0) {
            return ['success' => false, 'error' => 'ID utilisateur invalide'];
        }

        if (empty($reason)) {
            return ['success' => false, 'error' => 'Raison requise'];
        }

        $result = $this->adminManager->banUser($userId, $reason, $_SESSION['user_id']);

        return [
            'success' => $result,
            'message' => $result ? 'Utilisateur banni avec succès' : 'Erreur lors du bannissement'
        ];
    }

    public function unbanUser(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $userId = intval($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return ['success' => false, 'error' => 'ID utilisateur invalide'];
        }

        $result = $this->adminManager->unbanUser($userId, $_SESSION['user_id']);

        return [
            'success' => $result,
            'message' => $result ? 'Utilisateur débanni avec succès' : 'Erreur lors du débannissement'
        ];
    }

    // =============================================
    // SUSPENSION
    // =============================================

    public function suspendUser(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $userId = intval($_POST['user_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        $endDate = $_POST['end_date'] ?? '';

        if ($userId <= 0) {
            return ['success' => false, 'error' => 'ID utilisateur invalide'];
        }

        if (empty($reason) || empty($endDate)) {
            return ['success' => false, 'error' => 'Raison et date de fin requises'];
        }

        $result = $this->adminManager->suspendUser($userId, $reason, $endDate, $_SESSION['user_id']);

        return [
            'success' => $result,
            'message' => $result ? 'Utilisateur suspendu avec succès' : 'Erreur lors de la suspension'
        ];
    }

    public function unsuspendUser(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $userId = intval($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return ['success' => false, 'error' => 'ID utilisateur invalide'];
        }

        $result = $this->adminManager->unsuspendUser($userId, $_SESSION['user_id']);

        return [
            'success' => $result,
            'message' => $result ? 'Suspension levée avec succès' : 'Erreur'
        ];
    }

    // =============================================
    // BADGES
    // =============================================

    public function awardBadge(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $userId = intval($_POST['user_id'] ?? 0);
        $badgeName = trim($_POST['badge_name'] ?? '');
        $icon = trim($_POST['badge_icon'] ?? '🏆');
        $color = trim($_POST['badge_color'] ?? '#f59e0b');
        $description = trim($_POST['description'] ?? '');

        if ($userId <= 0 || empty($badgeName)) {
            return ['success' => false, 'error' => 'Données invalides'];
        }

        $result = $this->adminManager->awardBadge($userId, $badgeName, $icon, $color, $description, $_SESSION['user_id']);

        return [
            'success' => $result,
            'message' => $result ? 'Badge attribué avec succès' : 'Erreur lors de l\'attribution'
        ];
    }

    public function removeBadge(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $badgeId = intval($_POST['badge_id'] ?? 0);

        if ($badgeId <= 0) {
            return ['success' => false, 'error' => 'ID badge invalide'];
        }

        $result = $this->adminManager->removeBadge($badgeId, $_SESSION['user_id']);

        return [
            'success' => $result,
            'message' => $result ? 'Badge retiré avec succès' : 'Erreur'
        ];
    }

    public function getUserBadges(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $userId = intval($_GET['user_id'] ?? 0);

        if ($userId <= 0) {
            return ['success' => false, 'error' => 'ID utilisateur invalide'];
        }

        $badges = $this->adminManager->getUserBadges($userId);

        return [
            'success' => true,
            'badges' => $badges
        ];
    }

    // =============================================
    // RÔLES
    // =============================================

    public function changeRole(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $userId = intval($_POST['user_id'] ?? 0);
        $newRole = trim($_POST['role'] ?? '');

        if ($userId <= 0 || !in_array($newRole, ['user', 'admin', 'moderator'])) {
            return ['success' => false, 'error' => 'Données invalides'];
        }

        // Empêcher de se retirer soi-même les droits admin
        if ($userId === $_SESSION['user_id'] && $newRole !== 'admin') {
            return ['success' => false, 'error' => 'Vous ne pouvez pas modifier votre propre rôle'];
        }

        $result = $this->adminManager->changeUserRole($userId, $newRole, $_SESSION['user_id']);

        return [
            'success' => $result,
            'message' => $result ? 'Rôle modifié avec succès' : 'Erreur lors de la modification'
        ];
    }

    // =============================================
    // EMAILS
    // =============================================

    public function sendEmail(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $recipientType = $_POST['recipient_type'] ?? 'single';
        $recipientIds = json_decode($_POST['recipient_ids'] ?? '[]', true);
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($recipientIds) || empty($subject) || empty($message)) {
            return ['success' => false, 'error' => 'Tous les champs sont requis'];
        }

        $result = $this->adminManager->sendEmail($_SESSION['user_id'], $recipientType, $recipientIds, $subject, $message);

        return [
            'success' => $result,
            'message' => $result ? 'Email envoyé avec succès' : 'Erreur lors de l\'envoi'
        ];
    }

    public function getEmailHistory(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $emails = $this->adminManager->getEmailHistory();

        return [
            'success' => true,
            'emails' => $emails
        ];
    }

    // =============================================
    // LOGS ET HISTORIQUE
    // =============================================

    public function getAdminLogs(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $logs = $this->adminManager->getAdminLogs();

        return [
            'success' => true,
            'logs' => $logs
        ];
    }

    public function getUserHistory(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $userId = intval($_GET['user_id'] ?? 0);

        if ($userId <= 0) {
            return ['success' => false, 'error' => 'ID utilisateur invalide'];
        }

        $history = $this->adminManager->getUserHistory($userId);
        $stats = $this->adminManager->getUserStats($userId);

        return [
            'success' => true,
            'history' => $history,
            'stats' => $stats
        ];
    }

    // =============================================
    // STATISTIQUES AVANCÉES
    // =============================================

    public function getActiveUsers(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $users = $this->adminManager->getActiveUsers(10);

        return [
            'success' => true,
            'users' => $users
        ];
    }

    public function getPopularSkills(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $skills = $this->adminManager->getPopularSkills(10);

        return [
            'success' => true,
            'skills' => $skills
        ];
    }

    public function getRetentionRate(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $stats = $this->adminManager->getRetentionRate();

        return [
            'success' => true,
            'stats' => $stats
        ];
    }

    public function getGeoAnalysis(): array {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'error' => 'Accès non autorisé'];
        }

        $locations = $this->adminManager->getGeoAnalysis();

        return [
            'success' => true,
            'locations' => $locations
        ];
    }
}
