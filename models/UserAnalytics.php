<?php
require_once(__DIR__ . '/../config/config.php');

class UserAnalytics {
    public int $id_analytics;
    public int $user_id;
    public string $activity_type;
    public string $activity_date;
    public int $activity_count;
    public ?string $metadata;
    public string $created_at;

    // Types d'activités
    const TYPE_LOGIN = 'login';
    const TYPE_PROFILE_UPDATE = 'profile_update';
    const TYPE_SKILL_ADD = 'skill_add';
    const TYPE_PHOTO_UPLOAD = 'photo_upload';
    const TYPE_SEARCH = 'search';
    const TYPE_CONNECTION = 'connection';

    public function __construct(
        int $user_id = 0,
        string $activity_type = '',
        string $activity_date = '',
        int $activity_count = 1,
        ?string $metadata = null
    ) {
        $this->id_analytics = 0;
        $this->user_id = $user_id;
        $this->activity_type = $activity_type;
        $this->activity_date = $activity_date ?: date('Y-m-d');
        $this->activity_count = $activity_count;
        $this->metadata = $metadata;
        $this->created_at = date('Y-m-d H:i:s');
    }

    /**
     * Enregistrer une activité
     */
    public static function logActivity(int $userId, string $activityType, ?array $metadata = null): bool {
        try {
            $pdo = config::getConnexion();
            
            $metadataJson = $metadata ? json_encode($metadata) : null;
            $today = date('Y-m-d');
            
            $sql = "INSERT INTO user_analytics (user_id, activity_type, activity_date, activity_count, metadata) 
                    VALUES (:user_id, :activity_type, :activity_date, 1, :metadata)
                    ON DUPLICATE KEY UPDATE 
                    activity_count = activity_count + 1,
                    metadata = :metadata";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'activity_type' => $activityType,
                'activity_date' => $today,
                'metadata' => $metadataJson
            ]);
        } catch (Exception $e) {
            error_log("Erreur log activité: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les statistiques d'un utilisateur
     */
    public static function getUserStats(int $userId, int $days = 30): array {
        try {
            $pdo = config::getConnexion();
            
            $startDate = date('Y-m-d', strtotime("-$days days"));
            
            $stmt = $pdo->prepare("
                SELECT 
                    activity_type,
                    activity_date,
                    activity_count
                FROM user_analytics
                WHERE user_id = :user_id 
                AND activity_date >= :start_date
                ORDER BY activity_date DESC
            ");
            
            $stmt->execute([
                'user_id' => $userId,
                'start_date' => $startDate
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération stats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer le résumé des statistiques
     */
    public static function getStatsSummary(int $userId): array {
        try {
            $pdo = config::getConnexion();
            
            $stmt = $pdo->prepare("
                SELECT 
                    activity_type,
                    SUM(activity_count) as total_count,
                    MAX(activity_date) as last_activity
                FROM user_analytics
                WHERE user_id = :user_id
                GROUP BY activity_type
            ");
            
            $stmt->execute(['user_id' => $userId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $summary = [];
            foreach ($results as $row) {
                $summary[$row['activity_type']] = [
                    'count' => $row['total_count'],
                    'last' => $row['last_activity']
                ];
            }
            
            return $summary;
        } catch (Exception $e) {
            error_log("Erreur résumé stats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les données pour graphique
     */
    public static function getChartData(int $userId, string $activityType, int $days = 30): array {
        try {
            $pdo = config::getConnexion();
            
            $startDate = date('Y-m-d', strtotime("-$days days"));
            
            $stmt = $pdo->prepare("
                SELECT 
                    activity_date as date,
                    activity_count as count
                FROM user_analytics
                WHERE user_id = :user_id 
                AND activity_type = :activity_type
                AND activity_date >= :start_date
                ORDER BY activity_date ASC
            ");
            
            $stmt->execute([
                'user_id' => $userId,
                'activity_type' => $activityType,
                'start_date' => $startDate
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur données graphique: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les statistiques globales de l'utilisateur
     */
    public static function getGlobalStats(int $userId): array {
        try {
            $pdo = config::getConnexion();
            
            $stmt = $pdo->prepare("
                SELECT * FROM user_stats_summary WHERE id_user = :user_id
            ");
            
            $stmt->execute(['user_id' => $userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $stats ?: [];
        } catch (Exception $e) {
            error_log("Erreur stats globales: " . $e->getMessage());
            return [];
        }
    }
}
?>
