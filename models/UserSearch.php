<?php
require_once(__DIR__ . '/../config/config.php');

class UserSearch {
    
    /**
     * Recherche avancée d'utilisateurs
     */
    public static function advancedSearch(array $criteria, int $limit = 50, int $offset = 0): array {
        try {
            $pdo = config::getConnexion();
            
            $sql = "SELECT DISTINCT u.id_user, 
                    u.Prenom as prenom, 
                    u.nom, 
                    u.email, 
                    u.photo, 
                    u.bio, 
                    u.points, 
                    u.niveau, 
                    u.rank, 
                    u.badges,
                    u.status, 
                    u.location_city as city, 
                    u.location_country as country,
                    u.is_online,
                    (SELECT COUNT(*) FROM user_skills WHERE user_id = u.id_user) as skills_count,
                    (SELECT COUNT(*) FROM user_photos WHERE user_id = u.id_user) as photos_count,
                    (SELECT GROUP_CONCAT(skill_name SEPARATOR ', ') FROM user_skills WHERE user_id = u.id_user LIMIT 10) as skills
                    FROM user u
                    LEFT JOIN user_skills us ON u.id_user = us.user_id
                    WHERE 1=1";
            
            $params = [];
            
            // Recherche par nom/prénom/email
            if (!empty($criteria['query'])) {
                $sql .= " AND (u.nom LIKE :query OR u.Prenom LIKE :query OR u.email LIKE :query)";
                $params['query'] = '%' . $criteria['query'] . '%';
            }
            
            // Filtre par localisation
            if (!empty($criteria['country'])) {
                $sql .= " AND u.location_country = :country";
                $params['country'] = $criteria['country'];
            }
            
            if (!empty($criteria['city'])) {
                $sql .= " AND u.location_city LIKE :city";
                $params['city'] = '%' . $criteria['city'] . '%';
            }
            
            // Filtre par statut
            if (!empty($criteria['status'])) {
                $sql .= " AND u.status = :status";
                $params['status'] = $criteria['status'];
            }
            
            // Filtre par niveau
            if (!empty($criteria['min_level'])) {
                $sql .= " AND u.niveau >= :min_level";
                $params['min_level'] = $criteria['min_level'];
            }
            
            if (!empty($criteria['max_level'])) {
                $sql .= " AND u.niveau <= :max_level";
                $params['max_level'] = $criteria['max_level'];
            }
            
            // Filtre par compétence
            if (!empty($criteria['skill'])) {
                $sql .= " AND us.skill_name LIKE :skill";
                $params['skill'] = '%' . $criteria['skill'] . '%';
            }
            
            // Filtre par catégorie de compétence
            if (!empty($criteria['skill_category'])) {
                $sql .= " AND us.skill_category = :skill_category";
                $params['skill_category'] = $criteria['skill_category'];
            }
            
            // Filtre utilisateurs en ligne
            if (isset($criteria['online_only']) && $criteria['online_only']) {
                $sql .= " AND u.is_online = 1";
            }
            
            // Tri
            $orderBy = " ORDER BY ";
            switch ($criteria['sort'] ?? 'relevance') {
                case 'name':
                    $orderBy .= "u.nom ASC, u.Prenom ASC";
                    break;
                case 'points':
                    $orderBy .= "u.points DESC";
                    break;
                case 'level':
                    $orderBy .= "u.niveau DESC";
                    break;
                case 'recent':
                    $orderBy .= "u.last_activity DESC";
                    break;
                default:
                    $orderBy .= "u.points DESC, u.niveau DESC";
            }
            
            $sql .= $orderBy;
            $sql .= " LIMIT :limit OFFSET :offset";
            
            $stmt = $pdo->prepare($sql);
            
            // Bind des paramètres
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur recherche avancée: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Compter les résultats de recherche
     */
    public static function countSearchResults(array $criteria): int {
        try {
            $pdo = config::getConnexion();
            
            $sql = "SELECT COUNT(DISTINCT u.id_user) as total
                    FROM user u
                    LEFT JOIN user_skills us ON u.id_user = us.user_id
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($criteria['query'])) {
                $sql .= " AND (u.nom LIKE :query OR u.Prenom LIKE :query OR u.email LIKE :query)";
                $params['query'] = '%' . $criteria['query'] . '%';
            }
            
            if (!empty($criteria['country'])) {
                $sql .= " AND u.location_country = :country";
                $params['country'] = $criteria['country'];
            }
            
            if (!empty($criteria['city'])) {
                $sql .= " AND u.location_city LIKE :city";
                $params['city'] = '%' . $criteria['city'] . '%';
            }
            
            if (!empty($criteria['status'])) {
                $sql .= " AND u.status = :status";
                $params['status'] = $criteria['status'];
            }
            
            if (!empty($criteria['min_level'])) {
                $sql .= " AND u.niveau >= :min_level";
                $params['min_level'] = $criteria['min_level'];
            }
            
            if (!empty($criteria['max_level'])) {
                $sql .= " AND u.niveau <= :max_level";
                $params['max_level'] = $criteria['max_level'];
            }
            
            if (!empty($criteria['skill'])) {
                $sql .= " AND us.skill_name LIKE :skill";
                $params['skill'] = '%' . $criteria['skill'] . '%';
            }
            
            if (!empty($criteria['skill_category'])) {
                $sql .= " AND us.skill_category = :skill_category";
                $params['skill_category'] = $criteria['skill_category'];
            }
            
            if (isset($criteria['online_only']) && $criteria['online_only']) {
                $sql .= " AND u.is_online = 1";
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (Exception $e) {
            error_log("Erreur comptage résultats: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Enregistrer une recherche
     */
    public static function saveSearch(int $userId, string $searchName, array $criteria): bool {
        try {
            $pdo = config::getConnexion();
            
            $sql = "INSERT INTO saved_searches (user_id, search_name, search_criteria) 
                    VALUES (:user_id, :search_name, :search_criteria)";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'search_name' => $searchName,
                'search_criteria' => json_encode($criteria)
            ]);
        } catch (Exception $e) {
            error_log("Erreur sauvegarde recherche: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les recherches sauvegardées
     */
    public static function getSavedSearches(int $userId): array {
        try {
            $pdo = config::getConnexion();
            
            $stmt = $pdo->prepare("
                SELECT * FROM saved_searches 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC
            ");
            
            $stmt->execute(['user_id' => $userId]);
            $searches = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Décoder le JSON pour chaque recherche
            foreach ($searches as &$search) {
                $search['search_criteria'] = json_decode($search['search_criteria'], true);
            }
            
            return $searches;
        } catch (Exception $e) {
            error_log("Erreur récupération recherches: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Enregistrer l'historique de recherche
     */
    public static function logSearch(int $userId, string $query, array $filters, int $resultsCount): bool {
        try {
            $pdo = config::getConnexion();
            
            $sql = "INSERT INTO search_history (user_id, search_query, search_filters, results_count) 
                    VALUES (:user_id, :search_query, :search_filters, :results_count)";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'search_query' => $query,
                'search_filters' => json_encode($filters),
                'results_count' => $resultsCount
            ]);
        } catch (Exception $e) {
            error_log("Erreur log recherche: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enregistrer une vue de profil
     */
    public static function logProfileView(int $profileUserId, ?int $viewerUserId = null, ?string $viewerIp = null): bool {
        try {
            $pdo = config::getConnexion();
            
            $sql = "INSERT INTO profile_views (profile_user_id, viewer_user_id, viewer_ip) 
                    VALUES (:profile_user_id, :viewer_user_id, :viewer_ip)";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                'profile_user_id' => $profileUserId,
                'viewer_user_id' => $viewerUserId,
                'viewer_ip' => $viewerIp
            ]);
        } catch (Exception $e) {
            error_log("Erreur log vue profil: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les vues de profil
     */
    public static function getProfileViews(int $userId, int $days = 30): array {
        try {
            $pdo = config::getConnexion();
            
            $startDate = date('Y-m-d', strtotime("-$days days"));
            
            $stmt = $pdo->prepare("
                SELECT 
                    pv.*,
                    u.Prenom,
                    u.nom,
                    u.photo
                FROM profile_views pv
                LEFT JOIN user u ON pv.viewer_user_id = u.id_user
                WHERE pv.profile_user_id = :user_id
                AND pv.viewed_at >= :start_date
                ORDER BY pv.viewed_at DESC
                LIMIT 100
            ");
            
            $stmt->execute([
                'user_id' => $userId,
                'start_date' => $startDate
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération vues: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Suggestions d'utilisateurs similaires
     */
    public static function getSimilarUsers(int $userId, int $limit = 10): array {
        try {
            $pdo = config::getConnexion();
            
            // Trouver des utilisateurs avec des compétences similaires
            $stmt = $pdo->prepare("
                SELECT DISTINCT u.id_user, u.Prenom, u.nom, u.photo, u.bio, u.points,
                       COUNT(DISTINCT us2.id_skill) as common_skills
                FROM user u
                INNER JOIN user_skills us2 ON u.id_user = us2.user_id
                WHERE us2.skill_name IN (
                    SELECT skill_name FROM user_skills WHERE user_id = :user_id
                )
                AND u.id_user != :user_id
                GROUP BY u.id_user
                ORDER BY common_skills DESC, u.points DESC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur suggestions utilisateurs: " . $e->getMessage());
            return [];
        }
    }
}
?>
