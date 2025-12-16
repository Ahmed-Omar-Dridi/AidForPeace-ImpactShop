<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';

// Récupérer la photo de l'utilisateur depuis la base de données
$user_photo = 'default.jpg';
if (isset($_SESSION['user_id'])) {
    try {
        require_once __DIR__ . '/../../config/config.php';
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT photo FROM user WHERE id_user = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && !empty($result['photo'])) {
            $user_photo = $result['photo'];
        }
    } catch (Exception $e) {
        // Utiliser la photo par défaut en cas d'erreur
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - AidForPeace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/global-theme.css">
    <style>
        /* Profile page specific styles */
        .navbar-right { display: flex; align-items: center; gap: 15px; }
        .btn-user {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            padding: 12px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        }
        .btn-user:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 8px 30px rgba(255, 215, 0, 0.4);
        }

        .btn-logout-nav {
            background: rgba(244, 67, 54, 0.15);
            color: #ef5350;
            border: 1px solid rgba(244, 67, 54, 0.3);
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-logout-nav:hover { 
            background: var(--danger); 
            color: white;
            transform: translateY(-2px); 
        }

        /* MAIN CONTAINER */
        .main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 50px 20px;
            position: relative;
            z-index: 1;
        }

        /* PROFILE CARD */
        .profile-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 0;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            border: 1px solid var(--glass-border);
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }

        .profile-header {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(0, 188, 212, 0.05));
            padding: 45px;
            display: flex;
            align-items: center;
            gap: 30px;
            position: relative;
            border-bottom: 1px solid var(--glass-border);
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .profile-avatar {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--secondary);
            box-shadow: 0 15px 40px rgba(255, 215, 0, 0.4);
            position: relative;
            z-index: 1;
            border: 4px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            position: relative;
            z-index: 1;
        }

        .profile-info h2 {
            color: var(--white);
            margin-bottom: 10px;
            font-size: 2rem;
            font-weight: 700;
        }

        .profile-info p {
            color: rgba(255,255,255,0.8);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-info p i {
            color: var(--primary);
        }

        .role-badge {
            display: inline-block;
            background: var(--primary);
            color: var(--secondary);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 10px;
        }

        /* PROFILE BODY */
        .profile-body {
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
        }

        .profile-section {
            margin-bottom: 35px;
        }

        .profile-section:last-child {
            margin-bottom: 0;
        }

        .profile-section h3 {
            color: var(--text-light);
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--glass-border);
        }

        .profile-section h3 i {
            color: var(--primary);
            font-size: 1.3rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 22px;
            border-radius: 14px;
            transition: var(--transition);
            border: 1px solid var(--glass-border);
        }

        .info-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.15);
            background: rgba(255, 215, 0, 0.08);
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .info-value {
            font-size: 1.1rem;
            color: var(--text-light);
            font-weight: 600;
        }

        /* BUTTONS */
        .btn-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 30px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255,182,0,0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #dc2626);
            color: white;
            box-shadow: 0 8px 25px rgba(239,68,68,0.3);
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(239,68,68,0.4);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--secondary);
            color: var(--secondary);
        }

        .btn-outline:hover {
            background: var(--secondary);
            color: white;
        }

        /* FOOTER */
        footer {
            background: var(--secondary);
            color: rgba(255,255,255,0.7);
            text-align: center;
            padding: 30px;
            margin-top: 50px;
        }

        footer p {
            font-size: 0.9rem;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px;
                flex-wrap: wrap;
                height: auto;
                padding: 15px 20px;
            }
            
            .navbar-menu {
                display: none;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
                padding: 30px 20px;
            }

            .profile-info h2 {
                font-size: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .profile-body {
                padding: 25px;
            }

            .btn-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="index.php" class="navbar-logo">
            <i class="fas fa-hand-holding-heart"></i>
            <span><span class="aid">Aid</span> <span class="peace">ForPeace</span></span>
        </a>
        
        <ul class="navbar-menu">
            <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
            <li><a href="index.php?controller=user&action=index"><i class="fas fa-users"></i> Utilisateurs</a></li>
            <li><a href="index.php?controller=map&action=index"><i class="fas fa-map-marked-alt"></i> Map Mondiale</a></li>
            <li><a href="index.php?controller=messagerie&action=index"><i class="fas fa-comments"></i> Messagerie</a></li>
            <li><a href="index.php?controller=product&action=shop"><i class="fas fa-store"></i> Boutique</a></li>
        </ul>
        
        <div class="navbar-right">
            <?php if ($is_logged_in): ?>
                <a href="index.php?controller=page&action=profile" class="btn-user">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($user_name) ?>
                </a>
                <a href="index.php?controller=user&action=logout" class="btn-logout-nav">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            <?php else: ?>
                <a href="index.php?controller=user&action=login" class="btn-user">
                    <i class="fas fa-sign-in-alt"></i> Connexion
                </a>
            <?php endif; ?>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if ($user_photo && $user_photo !== 'default.jpg' && file_exists(__DIR__ . '/../views/user/user/uploads/' . $user_photo)): ?>
                        <img src="views/user/user/uploads/<?= htmlspecialchars($user_photo) ?>" alt="Photo de profil">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Utilisateur') ?> <?= htmlspecialchars($_SESSION['user_nom'] ?? '') ?></h2>
                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($_SESSION['user_email'] ?? 'email@example.com') ?></p>
                    <span class="role-badge">
                        <i class="fas fa-shield-alt"></i> <?= ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'user')) ?>
                    </span>
                </div>
            </div>
            
            <div class="profile-body">
                <div class="profile-section">
                    <h3><i class="fas fa-id-card"></i> Informations du Profil</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Prénom</div>
                            <div class="info-value"><?= htmlspecialchars($_SESSION['user_prenom'] ?? 'N/A') ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Nom</div>
                            <div class="info-value"><?= htmlspecialchars($_SESSION['user_nom'] ?? 'N/A') ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= htmlspecialchars($_SESSION['user_email'] ?? 'N/A') ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Rôle</div>
                            <div class="info-value"><?= ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'user')) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="profile-section">
                    <h3><i class="fas fa-cogs"></i> Actions</h3>
                    <div class="btn-actions">
                        <a href="index.php?controller=user&action=edit_profile" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier le Profil
                        </a>
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-home"></i> Retour à l'Accueil
                        </a>
                        <a href="index.php?controller=user&action=logout" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 AidForPeace. Tous droits réservés.</p>
    </footer>
</body>
</html>
