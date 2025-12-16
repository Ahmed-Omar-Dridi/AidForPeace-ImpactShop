<?php
require_once(__DIR__ . '/../config/config.php');

class UserSkill {
    public int $id_skill;
    public int $user_id;
    public string $skill_name;
    public int $skill_level; // 1-5
    public string $skill_category;
    public string $skill_description;
    public int $years_experience;
    public bool $is_certified;
    public string $created_at;
    public string $updated_at;

    public function __construct(
        int $user_id = 0,
        string $skill_name = '',
        int $skill_level = 1,
        string $skill_category = 'general',
        string $skill_description = '',
        int $years_experience = 0,
        bool $is_certified = false
    ) {
        $this->id_skill = 0;
        $this->user_id = $user_id;
        $this->skill_name = $skill_name;
        $this->skill_level = max(1, min(5, $skill_level)); // Entre 1 et 5
        $this->skill_category = $skill_category;
        $this->skill_description = $skill_description;
        $this->years_experience = max(0, $years_experience);
        $this->is_certified = $is_certified;
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    // CREATE - Ajouter une compétence
    public function create(): bool {
        try {
            $pdo = config::getConnexion();
            
            $sql = "INSERT INTO user_skills 
                    (user_id, skill_name, skill_level, skill_category, skill_description, years_experience, is_certified) 
                    VALUES (:user_id, :skill_name, :skill_level, :skill_category, :skill_description, :years_experience, :is_certified)";
            
            $stmt = $pdo->prepare($sql);
            
            $result = $stmt->execute([
                'user_id' => $this->user_id,
                'skill_name' => $this->skill_name,
                'skill_level' => $this->skill_level,
                'skill_category' => $this->skill_category,
                'skill_description' => $this->skill_description,
                'years_experience' => $this->years_experience,
                'is_certified' => $this->is_certified ? 1 : 0
            ]);

            if ($result) {
                $this->id_skill = $pdo->lastInsertId();
            }

            return $result;
        } catch (Exception $e) {
            error_log("Erreur création compétence: " . $e->getMessage());
            return false;
        }
    }

    // UPDATE - Modifier une compétence
    public function update(): bool {
        try {
            $pdo = config::getConnexion();
            
            $sql = "UPDATE user_skills SET 
                    skill_name = :skill_name,
                    skill_level = :skill_level,
                    skill_category = :skill_category,
                    skill_description = :skill_description,
                    years_experience = :years_experience,
                    is_certified = :is_certified,
                    updated_at = NOW()
                    WHERE id_skill = :id_skill";
            
            $stmt = $pdo->prepare($sql);
            
            return $stmt->execute([
                'skill_name' => $this->skill_name,
                'skill_level' => $this->skill_level,
                'skill_category' => $this->skill_category,
                'skill_description' => $this->skill_description,
                'years_experience' => $this->years_experience,
                'is_certified' => $this->is_certified ? 1 : 0,
                'id_skill' => $this->id_skill
            ]);
        } catch (Exception $e) {
            error_log("Erreur mise à jour compétence: " . $e->getMessage());
            return false;
        }
    }

    // DELETE - Supprimer une compétence
    public function delete(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM user_skills WHERE id_skill = :id_skill");
            return $stmt->execute(['id_skill' => $this->id_skill]);
        } catch (Exception $e) {
            error_log("Erreur suppression compétence: " . $e->getMessage());
            return false;
        }
    }

    // READ - Récupérer toutes les compétences d'un utilisateur
    public static function getUserSkills(int $user_id): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                SELECT * FROM user_skills 
                WHERE user_id = :user_id 
                ORDER BY skill_category, skill_level DESC, skill_name
            ");
            $stmt->execute(['user_id' => $user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération compétences: " . $e->getMessage());
            return [];
        }
    }

    // READ - Récupérer les compétences par catégorie
    public static function getUserSkillsByCategory(int $user_id): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                SELECT * FROM user_skills 
                WHERE user_id = :user_id 
                ORDER BY skill_category, skill_level DESC
            ");
            $stmt->execute(['user_id' => $user_id]);
            $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Grouper par catégorie
            $grouped = [];
            foreach ($skills as $skill) {
                $category = $skill['skill_category'];
                if (!isset($grouped[$category])) {
                    $grouped[$category] = [];
                }
                $grouped[$category][] = $skill;
            }
            
            return $grouped;
        } catch (Exception $e) {
            error_log("Erreur récupération compétences par catégorie: " . $e->getMessage());
            return [];
        }
    }

    // READ - Récupérer les catégories disponibles
    public static function getCategories(): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                SELECT * FROM skill_categories 
                ORDER BY display_order, category_name
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération catégories: " . $e->getMessage());
            return [];
        }
    }

    // READ - Récupérer les suggestions de compétences
    public static function getSuggestions(string $search = '', int $limit = 20): array {
        try {
            $pdo = config::getConnexion();
            
            if (!empty($search)) {
                $stmt = $pdo->prepare("
                    SELECT * FROM skill_suggestions 
                    WHERE skill_name LIKE :search 
                    ORDER BY popularity_count DESC, skill_name 
                    LIMIT :limit
                ");
                $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            } else {
                $stmt = $pdo->prepare("
                    SELECT * FROM skill_suggestions 
                    ORDER BY popularity_count DESC, skill_name 
                    LIMIT :limit
                ");
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération suggestions: " . $e->getMessage());
            return [];
        }
    }

    // Statistiques
    public static function getUserSkillsStats(int $user_id): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_skills,
                    AVG(skill_level) as avg_level,
                    MAX(skill_level) as max_level,
                    SUM(CASE WHEN is_certified = 1 THEN 1 ELSE 0 END) as certified_count
                FROM user_skills
                WHERE user_id = :user_id
            ");
            $stmt->execute(['user_id' => $user_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $stats ?: [
                'total_skills' => 0,
                'avg_level' => 0,
                'max_level' => 0,
                'certified_count' => 0
            ];
        } catch (Exception $e) {
            error_log("Erreur récupération stats compétences: " . $e->getMessage());
            return [
                'total_skills' => 0,
                'avg_level' => 0,
                'max_level' => 0,
                'certified_count' => 0
            ];
        }
    }

    // Méthode pour ajouter une compétence (utilisée par le contrôleur)
    public function addSkill(int $user_id, string $skill_name, int $skill_level, string $skill_category, int $years_experience, int $is_certified): bool {
        $this->user_id = $user_id;
        $this->skill_name = $skill_name;
        $this->skill_level = max(1, min(5, $skill_level));
        $this->skill_category = $skill_category;
        $this->years_experience = max(0, $years_experience);
        $this->is_certified = $is_certified == 1;
        
        return $this->create();
    }

    // Méthode pour supprimer une compétence (utilisée par le contrôleur)
    public function deleteSkill(int $skill_id, int $user_id): bool {
        try {
            $pdo = config::getConnexion();
            // Vérifier que la compétence appartient bien à l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM user_skills WHERE id_skill = :id_skill AND user_id = :user_id");
            return $stmt->execute([
                'id_skill' => $skill_id,
                'user_id' => $user_id
            ]);
        } catch (Exception $e) {
            error_log("Erreur suppression compétence: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour obtenir des suggestions de compétences
    public function getSkillSuggestions(string $search): array {
        return self::getSuggestions($search, 10);
    }
}
?>
