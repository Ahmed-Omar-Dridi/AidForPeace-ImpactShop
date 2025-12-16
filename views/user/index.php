<?php
// =============================================
// USER SYSTEM - Main Entry Point
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';

// Get the action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Route to appropriate view
switch ($action) {
    case 'login':
        // Traitement du formulaire de connexion
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Email et mot de passe requis.';
            } else {
                try {
                    $pdo = config::getConnexion();
                    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
                    $stmt->execute(['email' => $email]);
                    $userData = $stmt->fetch();
                    
                    if ($userData && password_verify($password, $userData['password'])) {
                        // Connexion réussie - Créer la session
                        $_SESSION['user_id'] = $userData['id_user'];
                        $_SESSION['user_email'] = $userData['email'];
                        $_SESSION['user_nom'] = $userData['nom'];
                        $_SESSION['user_prenom'] = $userData['Prenom'];
                        $_SESSION['user_role'] = $userData['role'];
                        $_SESSION['user_photo'] = $userData['photo'] ?? 'default.jpg';
                        
                        // Redirection selon le rôle
                        if ($userData['role'] === 'admin') {
                            header('Location: index.php?controller=admin&action=dashboard');
                        } else {
                            header('Location: index.php?controller=page&action=profile');
                        }
                        exit;
                    } else {
                        $error = 'Email ou mot de passe incorrect.';
                    }
                } catch (Exception $e) {
                    $error = 'Erreur lors de la connexion: ' . $e->getMessage();
                }
            }
        }
        require_once __DIR__ . '/login.php';
        break;
        
    case 'register':
        require_once __DIR__ . '/register.php';
        break;
        
    case 'profile':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        // Fetch user data for the profile view
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE id_user = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $user = $stmt->fetch();
        } catch (Exception $e) {
            $error = 'Erreur lors du chargement du profil.';
            $user = null;
        }
        require_once __DIR__ . '/profile.php';
        break;
        
    case 'edit_profile':
    case 'editProfile':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        // Fetch user data for the edit profile view
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE id_user = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $user = $stmt->fetch();
        } catch (Exception $e) {
            $error = 'Erreur lors du chargement du profil.';
            $user = null;
        }
        require_once __DIR__ . '/edit_profile.php';
        break;
        
    case 'forgot_password':
        require_once __DIR__ . '/forgot_password.php';
        break;
        
    case 'logout':
        session_destroy();
        header('Location: index.php');
        exit;
        break;
        
    default:
        require_once __DIR__ . '/login.php';
        break;
}
?>
