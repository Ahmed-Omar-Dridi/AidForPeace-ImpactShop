<?php
require_once(__DIR__ . '/../config/config.php');

class FacialRecognitionSkill {
    
    public static function getByUserId(int $userId) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM facial_recognition_skills WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Erreur récupération compétence: " . $e->getMessage());
            return null;
        }
    }
    
    public function __construct(private int $userId) {}
    
    public function create(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                INSERT INTO facial_recognition_skills (user_id, skill_level, projects_count, accuracy, last_trained) 
                VALUES (:user_id, 'beginner', 0, 0.00, CURDATE())
            ");
            return $stmt->execute(['user_id' => $this->userId]);
        } catch (Exception $e) {
            error_log("Erreur création compétence: " . $e->getMessage());
            return false;
        }
    }
    
    public function addProject(float $accuracy): bool {
        try {
            $pdo = config::getConnexion();
            
            // Récupérer les données actuelles
            $current = self::getByUserId($this->userId);
            if (!$current) return false;
            
            // Calculer la nouvelle précision moyenne
            $newProjectsCount = $current['projects_count'] + 1;
            $newAccuracy = (($current['accuracy'] * $current['projects_count']) + $accuracy) / $newProjectsCount;
            
            // Déterminer le niveau
            $newLevel = 'beginner';
            if ($newAccuracy >= 80) $newLevel = 'advanced';
            elseif ($newAccuracy >= 60) $newLevel = 'intermediate';
            
            // Mettre à jour
            $stmt = $pdo->prepare("
                UPDATE facial_recognition_skills 
                SET projects_count = :count, 
                    accuracy = :accuracy, 
                    skill_level = :level,
                    last_trained = CURDATE()
                WHERE user_id = :user_id
            ");
            
            return $stmt->execute([
                'count' => $newProjectsCount,
                'accuracy' => round($newAccuracy, 2),
                'level' => $newLevel,
                'user_id' => $this->userId
            ]);
        } catch (Exception $e) {
            error_log("Erreur ajout projet: " . $e->getMessage());
            return false;
        }
    }
}
?>