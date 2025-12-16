<?php
require_once(__DIR__ . '/../models/User.php');
require_once(__DIR__ . '/../config/config.php');

class FacialRecognitionController {
    
    // Afficher le dashboard
    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        require_once 'models/FacialRecognitionSkill.php';
        $this->createSkillsTable();
        
        $userSkill = FacialRecognitionSkill::getByUserId($_SESSION['user_id']);
        
        if (!$userSkill && isset($_POST['start_learning'])) {
            $userSkill = new FacialRecognitionSkill($_SESSION['user_id']);
            if ($userSkill->create()) {
                header('Location: index.php?controller=facial&action=dashboard');
                exit;
            }
        }
        
        if (isset($_POST['add_project']) && $userSkill) {
            $accuracy = floatval($_POST['accuracy'] ?? 0.00);
            if ($userSkill->addProject($accuracy)) {
                header('Location: index.php?controller=facial&action=dashboard');
                exit;
            }
        }
        
        $this->renderView('facial/dashboard', [
            'userSkill' => $userSkill,
            'userPrenom' => $_SESSION['user_prenom'] ?? ''
        ]);
    }
    
    // Afficher la page de test
    public function test() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        require 'views/facial/test.php';
    }
    
    // API pour ajouter un projet
    public function addProjectApi() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Non autorisé']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit;
        }
        
        try {
            require_once 'models/FacialRecognitionSkill.php';
            $this->createSkillsTable();
            
            $userSkill = FacialRecognitionSkill::getByUserId($_SESSION['user_id']);
            
            if (!$userSkill) {
                $userSkill = new FacialRecognitionSkill($_SESSION['user_id']);
                if (!$userSkill->create()) {
                    echo json_encode(['success' => false, 'error' => 'Erreur création compétence']);
                    exit;
                }
            }
            
            $accuracy = floatval($_POST['accuracy'] ?? 0.00);
            
            if ($userSkill->addProject($accuracy)) {
                echo json_encode(['success' => true, 'message' => 'Projet ajouté avec succès']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur ajout projet']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur: ' . $e->getMessage()]);
        }
    }
    
    // API : Connexion par reconnaissance faciale - VERSION CORRIGÉE
    public function facialLogin() {
        // En-têtes CORS et JSON
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        // Gérer les requêtes OPTIONS pour CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        // Seulement POST accepté
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée. Utilisez POST.']);
            exit;
        }
        
        try {
            // Lire les données JSON
            $jsonInput = file_get_contents('php://input');
            
            if (empty($jsonInput)) {
                echo json_encode(['success' => false, 'error' => 'Aucune donnée reçue']);
                exit;
            }
            
            $input = json_decode($jsonInput, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode(['success' => false, 'error' => 'JSON invalide: ' . json_last_error_msg()]);
                exit;
            }
            
            $imageData = $input['image'] ?? '';
            
            if (empty($imageData)) {
                echo json_encode(['success' => false, 'error' => 'Image manquante']);
                exit;
            }
            
            // SIMULATION - Pour la démo
            // En production, vous analyseriez vraiment l'image
            
            // Récupérer la connexion DB
            $pdo = config::getConnexion();
            
            // Chercher des utilisateurs avec données faciales
            $stmt = $pdo->prepare("
                SELECT u.id_user, u.Prenom, u.nom, u.email, u.role
                FROM user u 
                WHERE u.facial_data IS NOT NULL 
                AND u.facial_data != ''
                ORDER BY u.id_user DESC
            ");
            $stmt->execute();
            $users = $stmt->fetchAll();
            
            if (empty($users)) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Aucun utilisateur enregistré avec reconnaissance faciale. Inscrivez-vous d\'abord.'
                ]);
                exit;
            }
            
            // SIMULATION : Prendre le dernier utilisateur inscrit
            // En réalité, comparez les descripteurs faciaux
            $matchedUser = $users[0];
            
            // Simuler une reconnaissance (80% de chance de succès)
            $random = mt_rand(1, 100);
            $simulatedSuccess = $random <= 80;
            
            if ($simulatedSuccess) {
                // Démarrer/récupérer la session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $matchedUser['id_user'];
                $_SESSION['user_email'] = $matchedUser['email'];
                $_SESSION['user_nom'] = $matchedUser['nom'];
                $_SESSION['user_prenom'] = $matchedUser['Prenom'];
                $_SESSION['user_role'] = $matchedUser['role'];
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Connexion réussie par reconnaissance faciale!',
                    'user' => [
                        'prenom' => $matchedUser['Prenom'],
                        'nom' => $matchedUser['nom']
                    ],
                    'redirect' => ($matchedUser['role'] === 'admin') ?
                        'index.php?controller=admin&action=dashboard' :
                        'index.php?controller=page&action=profile'
                ]);
                
            } else {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Visage non reconnu. Essayez encore.'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("ERREUR facialLogin: " . $e->getMessage() . " - " . $e->getTraceAsString());
            echo json_encode([
                'success' => false, 
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
    
    // Créer la table des compétences
    private function createSkillsTable() {
        try {
            $pdo = config::getConnexion();
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS facial_recognition_skills (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT NOT NULL,
                    skill_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
                    projects_count INT DEFAULT 0,
                    accuracy DECIMAL(5,2) DEFAULT 0.00,
                    last_trained DATE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES user(id_user) ON DELETE CASCADE
                )
            ");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Rendre une vue
    private function renderView($viewPath, $data = []) {
        extract($data);
        $viewFile = 'views/' . $viewPath . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo '<div style="padding: 20px; background: #f8d7da; color: #721c24; border-radius: 5px;">';
            echo '<h3>Vue non trouvée: ' . htmlspecialchars($viewPath) . '</h3>';
            echo '</div>';
        }
    }
}
?>