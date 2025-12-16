<?php

require_once __DIR__ . '/../config/config.php';

class AdminManager {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // =============================================
    // BANNISSEMENT ET SUSPENSION
    // =============================================

    public function banUser(int $userId, string $reason, int $adminId): bool {
        try {
            $sql = "UPDATE user SET 
                    is_banned = TRUE,
                    ban_reason = :reason,
                    banned_at = NOW(),
                    banned_by = :admin_id
                    WHERE id_user = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                'reason' => $reason,
                'admin_id' => $adminId,
                'user_id' => $userId
            ]);

            if ($result) {
                $this->logAction($adminId, 'ban_user', $userId, "Utilisateur banni: $reason");
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur ban user: " . $e->getMessage());
            return false;
        }
    }

    public function unbanUser(int $userId, int $adminId): bool {
        try {
            $sql = "UPDATE user SET 
                    is_banned = FALSE,
                    ban_reason = NULL,
                    banned_at = NULL,
                    banned_by = NULL
                    WHERE id_user = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute(['user_id' => $userId]);

            if ($result) {
                $this->logAction($adminId, 'unban_user', $userId, "Utilisateur débanni");
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur unban user: " . $e->getMessage());
            return false;
        }
    }

    public function suspendUser(int $userId, string $reason, string $endDate, int $adminId): bool {
        try {
            $sql = "UPDATE user SET 
                    is_suspended = TRUE,
                    suspension_reason = :reason,
                    suspension_end = :end_date
                    WHERE id_user = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                'reason' => $reason,
                'end_date' => $endDate,
                'user_id' => $userId
            ]);

            if ($result) {
                $this->logAction($adminId, 'suspend_user', $userId, "Utilisateur suspendu jusqu'au $endDate: $reason");
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur suspend user: " . $e->getMessage());
            return false;
        }
    }

    public function unsuspendUser(int $userId, int $adminId): bool {
        try {
            $sql = "UPDATE user SET 
                    is_suspended = FALSE,
                    suspension_reason = NULL,
                    suspension_end = NULL
                    WHERE id_user = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute(['user_id' => $userId]);

            if ($result) {
                $this->logAction($adminId, 'unsuspend_user', $userId, "Suspension levée");
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur unsuspend user: " . $e->getMessage());
            return false;
        }
    }

    // =============================================
    // GESTION DES BADGES
    // =============================================

    public function awardBadge(int $userId, string $badgeName, string $icon, string $color, string $description, int $adminId): bool {
        try {
            $sql = "INSERT INTO user_badges (user_id, badge_name, badge_icon, badge_color, description, awarded_by)
                    VALUES (:user_id, :badge_name, :icon, :color, :description, :admin_id)";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                'user_id' => $userId,
                'badge_name' => $badgeName,
                'icon' => $icon,
                'color' => $color,
                'description' => $description,
                'admin_id' => $adminId
            ]);

            if ($result) {
                $this->logAction($adminId, 'award_badge', $userId, "Badge attribué: $badgeName");
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur award badge: " . $e->getMessage());
            return false;
        }
    }

    public function removeBadge(int $badgeId, int $adminId): bool {
        try {
            // Récupérer les infos du badge avant suppression
            $badge = $this->getBadgeById($badgeId);
            
            $sql = "DELETE FROM user_badges WHERE id = :badge_id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute(['badge_id' => $badgeId]);

            if ($result && $badge) {
                $this->logAction($adminId, 'remove_badge', $badge['user_id'], "Badge retiré: {$badge['badge_name']}");
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur remove badge: " . $e->getMessage());
            return false;
        }
    }

    public function getUserBadges(int $userId): array {
        try {
            $sql = "SELECT b.*, u.Prenom, u.nom 
                    FROM user_badges b
                    LEFT JOIN user u ON b.awarded_by = u.id_user
                    WHERE b.user_id = :user_id
                    ORDER BY b.awarded_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur get user badges: " . $e->getMessage());
            return [];
        }
    }

    private function getBadgeById(int $badgeId): ?array {
        try {
            $sql = "SELECT * FROM user_badges WHERE id = :badge_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['badge_id' => $badgeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // =============================================
    // GESTION DES RÔLES
    // =============================================

    public function changeUserRole(int $userId, string $newRole, int $adminId): bool {
        try {
            $sql = "UPDATE user SET role = :role WHERE id_user = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                'role' => $newRole,
                'user_id' => $userId
            ]);

            if ($result) {
                $this->logAction($adminId, 'change_role', $userId, "Rôle changé en: $newRole");
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur change role: " . $e->getMessage());
            return false;
        }
    }

    // =============================================
    // ENVOI D'EMAILS
    // =============================================

    public function sendEmail(int $adminId, string $recipientType, array $recipientIds, string $subject, string $message): bool {
        try {
            // Enregistrer l'email dans la base
            $sql = "INSERT INTO admin_emails (sent_by, recipient_type, recipient_ids, subject, message)
                    VALUES (:admin_id, :type, :recipients, :subject, :message)";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                'admin_id' => $adminId,
                'type' => $recipientType,
                'recipients' => json_encode($recipientIds),
                'subject' => $subject,
                'message' => $message
            ]);

            if ($result) {
                $count = count($recipientIds);
                $this->logAction($adminId, 'send_email', null, "Email envoyé à $count utilisateur(s): $subject");
            }

            // TODO: Implémenter l'envoi réel d'emails avec PHPMailer ou similaire

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur send email: " . $e->getMessage());
            return false;
        }
    }

    public function getEmailHistory(int $limit = 50): array {
        try {
            $sql = "SELECT e.*, u.Prenom, u.nom 
                    FROM admin_emails e
                    JOIN user u ON e.sent_by = u.id_user
                    ORDER BY e.sent_at DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur get email history: " . $e->getMessage());
            return [];
        }
    }

    // =============================================
    // LOGS D'ACTIONS
    // =============================================

    private function logAction(int $adminId, string $actionType, ?int $targetUserId, string $description): bool {
        try {
            $sql = "INSERT INTO admin_logs (admin_id, action_type, target_user_id, description, ip_address)
                    VALUES (:admin_id, :action_type, :target_user_id, :description, :ip)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'admin_id' => $adminId,
                'action_type' => $actionType,
                'target_user_id' => $targetUserId,
                'description' => $description,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        } catch (PDOException $e) {
            error_log("Erreur log action: " . $e->getMessage());
            return false;
        }
    }

    public function getAdminLogs(int $limit = 100): array {
        try {
            $sql = "SELECT l.*, 
                    u1.Prenom as admin_prenom, u1.nom as admin_nom,
                    u2.Prenom as target_prenom, u2.nom as target_nom
                    FROM admin_logs l
                    JOIN user u1 ON l.admin_id = u1.id_user
                    LEFT JOIN user u2 ON l.target_user_id = u2.id_user
                    ORDER BY l.created_at DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur get admin logs: " . $e->getMessage());
            return [];
        }
    }

    public function getUserHistory(int $userId): array {
        try {
            $sql = "SELECT * FROM admin_logs 
                    WHERE target_user_id = :user_id
                    ORDER BY created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur get user history: " . $e->getMessage());
            return [];
        }
    }

    // =============================================
    // STATISTIQUES
    // =============================================

    public function getUserStats(int $userId): array {
        try {
            $sql = "SELECT 
                    COUNT(DISTINCT ua.id) as total_albums,
                    COUNT(DISTINCT up.id) as total_photos,
                    COUNT(DISTINCT ub.id) as total_badges,
                    COUNT(DISTINCT us.id) as total_skills
                    FROM user u
                    LEFT JOIN user_albums ua ON u.id_user = ua.user_id
                    LEFT JOIN user_photos up ON u.id_user = up.user_id
                    LEFT JOIN user_badges ub ON u.id_user = ub.user_id
                    LEFT JOIN user_skills us ON u.id_user = us.user_id
                    WHERE u.id_user = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Erreur get user stats: " . $e->getMessage());
            return [];
        }
    }

    public function getActiveUsers(int $limit = 10): array {
        try {
            $sql = "SELECT u.id_user, u.Prenom, u.nom, u.email, u.last_activity,
                    COUNT(DISTINCT up.id) as photo_count,
                    COUNT(DISTINCT us.id) as skill_count
                    FROM user u
                    LEFT JOIN user_photos up ON u.id_user = up.user_id
                    LEFT JOIN user_skills us ON u.id_user = us.user_id
                    GROUP BY u.id_user
                    ORDER BY u.last_activity DESC, photo_count DESC, skill_count DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur get active users: " . $e->getMessage());
            return [];
        }
    }

    public function getPopularSkills(int $limit = 10): array {
        try {
            $sql = "SELECT skill_name, COUNT(*) as count
                    FROM user_skills
                    GROUP BY skill_name
                    ORDER BY count DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur get popular skills: " . $e->getMessage());
            return [];
        }
    }

    public function getRetentionRate(): array {
        try {
            $totalUsers = $this->pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
            
            $active7Days = $this->pdo->query(
                "SELECT COUNT(*) FROM user 
                WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
            )->fetchColumn();
            
            $active30Days = $this->pdo->query(
                "SELECT COUNT(*) FROM user 
                WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
            )->fetchColumn();
            
            $retentionRate = $totalUsers > 0 ? round(($active30Days / $totalUsers) * 100, 1) : 0;
            
            return [
                'total_users' => $totalUsers,
                'active_last_7_days' => $active7Days,
                'active_last_30_days' => $active30Days,
                'retention_rate' => $retentionRate
            ];
        } catch (PDOException $e) {
            error_log("Erreur get retention rate: " . $e->getMessage());
            return [
                'total_users' => 0,
                'active_last_7_days' => 0,
                'active_last_30_days' => 0,
                'retention_rate' => 0
            ];
        }
    }

    public function getGeoAnalysis(): array {
        try {
            $totalUsers = $this->pdo->query("SELECT COUNT(*) FROM user WHERE location_country IS NOT NULL")->fetchColumn();
            
            $sql = "SELECT 
                    location_country as country,
                    location_city as city,
                    COUNT(*) as user_count,
                    ROUND((COUNT(*) / :total * 100), 1) as percentage
                    FROM user
                    WHERE location_country IS NOT NULL
                    GROUP BY location_country, location_city
                    ORDER BY user_count DESC
                    LIMIT 20";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['total' => $totalUsers > 0 ? $totalUsers : 1]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur get geo analysis: " . $e->getMessage());
            return [];
        }
    }
}
