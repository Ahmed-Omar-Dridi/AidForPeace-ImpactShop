<?php
require_once __DIR__ . '/../../controllers/MessagerieController.php';
$ctrl = new MessagerieController();
$user_id = isset($_GET['user']) ? (int)$_GET['user'] : 1;
$all_users = $ctrl->getUtilisateurs();
$current_user = $ctrl->getUserById($user_id);

if ($_POST && isset($_POST['start_chat'])) {
    $other_user_id = (int)($_POST['other_user_id'] ?? 0);
    if ($other_user_id <= 0 || $other_user_id == $user_id) {
        $error = "Sélectionnez un utilisateur valide";
    } else {
        $conversation_id = $ctrl->getOrCreateConversation($user_id, $other_user_id);
        header("Location: index.php?controller=messagerie&action=chat&conv=" . $conversation_id . "&user=" . $user_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle conversation - AidForPeace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #ffb600;
            --primary-dark: #e6a500;
            --primary-light: #ffc933;
            --accent: #00bcd4;
            --accent-light: #4dd0e1;
            --dark: #1e3149;
            --dark-light: #2a4562;
            --dark-darker: #15202e;
            --success: #10b981;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-50) 100%);
            min-height: 100vh;
            color: var(--dark);
        }

        /* Navbar Premium */
        .navbar-premium {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            padding: 1rem 2rem;
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-premium .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .navbar-premium .brand-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(255, 182, 0, 0.4);
        }

        .navbar-premium .brand-text {
            color: white;
        }

        .navbar-premium .brand-text h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .navbar-premium .brand-text small {
            color: var(--gray-400);
            font-size: 0.8rem;
        }

        .navbar-premium .nav-info {
            text-align: right;
            color: white;
        }

        .navbar-premium .nav-info h5 {
            margin: 0;
            font-weight: 600;
            color: var(--accent);
        }

        .navbar-premium .nav-info small {
            color: var(--gray-400);
        }

        /* Layout */
        .main-container {
            display: flex;
            min-height: calc(100vh - 77px);
        }

        /* Sidebar Premium */
        .sidebar-premium {
            width: 280px;
            background: linear-gradient(180deg, var(--dark) 0%, var(--dark-light) 50%, var(--dark) 100%);
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 77px;
            height: calc(100vh - 77px);
            overflow-y: auto;
        }

        .sidebar-header {
            text-align: center;
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1.5rem;
        }

        .sidebar-header h3 {
            color: white;
            font-weight: 800;
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
            background: linear-gradient(135deg, var(--white) 0%, var(--gray-300) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-header p {
            color: var(--gray-400);
            font-size: 0.875rem;
            margin: 0;
        }

        .sidebar-nav {
            padding: 0 1rem;
            flex-grow: 1;
        }

        .nav-link-premium {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .nav-link-premium::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 0;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 0 4px 4px 0;
            transition: height 0.3s ease;
        }

        .nav-link-premium:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateX(8px);
        }

        .nav-link-premium:hover::before {
            height: 60%;
        }

        .nav-link-premium.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--dark);
            box-shadow: 0 4px 15px rgba(255, 182, 0, 0.4);
        }

        .nav-link-premium.active::before {
            height: 70%;
            background: var(--accent);
        }

        .nav-link-premium i {
            width: 24px;
            font-size: 1.1rem;
            text-align: center;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            padding: 2.5rem;
            overflow-y: auto;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .page-header h1 i {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-header p {
            color: var(--gray-500);
            font-size: 1.1rem;
            margin: 0;
        }

        /* Main Card */
        .main-card {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            border: 1px solid var(--gray-200);
            position: relative;
        }

        .main-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 50%, var(--primary) 100%);
        }

        .card-body-premium {
            padding: 2.5rem;
        }

        /* Alert */
        .alert-premium {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            color: #dc2626;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Search Box */
        .search-box {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-box input {
            width: 100%;
            padding: 1rem 1.25rem 1rem 3.5rem;
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--gray-50);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(255, 182, 0, 0.15);
        }

        .search-box i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.1rem;
        }

        /* User Grid */
        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
            max-height: 450px;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .user-grid::-webkit-scrollbar {
            width: 8px;
        }

        .user-grid::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 10px;
        }

        .user-grid::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--dark) 0%, var(--dark-light) 100%);
            border-radius: 10px;
        }

        /* User Card */
        .user-card {
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            padding: 1.25rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .user-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .user-card:hover {
            border-color: var(--primary-light);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .user-card:hover::before {
            transform: scaleX(1);
        }

        .user-card.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, rgba(255, 182, 0, 0.05) 0%, rgba(255, 201, 51, 0.08) 100%);
            box-shadow: 0 8px 25px rgba(255, 182, 0, 0.2);
        }

        .user-card.selected::before {
            transform: scaleX(1);
            background: linear-gradient(90deg, var(--success) 0%, #34d399 100%);
        }

        .user-card.selected::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            animation: popIn 0.3s ease;
        }

        @keyframes popIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .user-card-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.5rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(30, 49, 73, 0.3);
            transition: all 0.3s ease;
        }

        .user-card:hover .user-avatar {
            transform: scale(1.1) rotate(5deg);
        }

        .user-card.selected .user-avatar {
            background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .user-info h5 {
            margin: 0 0 0.25rem;
            font-weight: 700;
            color: var(--dark);
            font-size: 1rem;
        }

        .user-info p {
            margin: 0;
            color: var(--gray-500);
            font-size: 0.85rem;
        }

        .user-status {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            margin-top: 0.5rem;
            padding: 0.25rem 0.75rem;
            background: var(--gray-100);
            border-radius: 20px;
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        .user-status .dot {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .btn-premium {
            padding: 1rem 2rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary-premium {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--dark);
            box-shadow: 0 4px 15px rgba(255, 182, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary-premium:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 182, 0, 0.5);
        }

        .btn-primary-premium:hover::before {
            left: 100%;
        }

        .btn-primary-premium:disabled {
            background: var(--gray-300);
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-secondary-premium {
            background: white;
            color: var(--gray-600);
            border: 2px solid var(--gray-200);
        }

        .btn-secondary-premium:hover {
            border-color: var(--primary);
            color: var(--dark);
            background: rgba(255, 182, 0, 0.1);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
        }

        .empty-state h4 {
            color: var(--gray-600);
            margin-bottom: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar-premium {
                display: none;
            }
            
            .content-area {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .user-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .page-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>

<!-- Premium Navbar -->
<nav class="navbar-premium">
    <div class="d-flex justify-content-between align-items-center w-100">
        <a href="index.php" class="brand">
            <div class="brand-icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="brand-text">
                <h4>AidForPeace</h4>
                <small>Messagerie Interne</small>
            </div>
        </a>
        <div class="nav-info">
            <h5>Bienvenue, <?= htmlspecialchars($current_user['nom'] ?? 'Utilisateur') ?></h5>
            <small><i class="fas fa-circle text-success me-1" style="font-size: 0.5rem;"></i> En ligne</small>
        </div>
    </div>
</nav>

<div class="main-container">
    <!-- Premium Sidebar -->
    <aside class="sidebar-premium">
        <div class="sidebar-header">
            <h3>💬 Messages</h3>
            <p>Restez connecté</p>
        </div>
        
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-link-premium">
                <i class="fas fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="index.php?controller=messagerie&action=inbox&user=<?= $user_id ?>" class="nav-link-premium">
                <i class="fas fa-inbox"></i>
                <span>Mes Conversations</span>
            </a>
            <a href="index.php?controller=messagerie&action=new_chat&user=<?= $user_id ?>" class="nav-link-premium active">
                <i class="fas fa-plus-circle"></i>
                <span>Nouvelle Conversation</span>
            </a>
            <a href="index.php?controller=messagerie&action=chatbot" class="nav-link-premium">
                <i class="fas fa-robot"></i>
                <span>Assistant IA</span>
            </a>
            <a href="index.php?controller=page&action=profile" class="nav-link-premium">
                <i class="fas fa-user"></i>
                <span>Mon Profil</span>
            </a>
        </nav>
    </aside>

    <!-- Content Area -->
    <main class="content-area">
        <div class="page-header">
            <h1><i class="fas fa-comments"></i> Nouvelle Conversation</h1>
            <p>Sélectionnez un utilisateur pour démarrer une discussion</p>
        </div>

        <div class="main-card">
            <div class="card-body-premium">
                <?php if (isset($error)): ?>
                    <div class="alert-premium">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Search Box -->
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchUser" placeholder="Rechercher un utilisateur..." onkeyup="filterUsers()">
                </div>

                <form method="post" id="chatForm">
                    <input type="hidden" name="start_chat" value="1">
                    <input type="hidden" name="other_user_id" id="other_user_id" value="">

                    <!-- User Grid -->
                    <div class="user-grid" id="userGrid">
                        <?php $hasUsers = false; ?>
                        <?php foreach($all_users as $u): ?>
                            <?php if ($u['id'] != $user_id): ?>
                                <?php $hasUsers = true; ?>
                                <div class="user-card" onclick="selectUser(<?= $u['id'] ?>, this)" data-name="<?= strtolower(htmlspecialchars($u['nom'])) ?>">
                                    <div class="user-card-content">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="user-info">
                                            <h5><?= htmlspecialchars($u['nom']) ?></h5>
                                            <p><?= htmlspecialchars($u['email'] ?? 'Membre AidForPeace') ?></p>
                                            <div class="user-status">
                                                <span class="dot"></span>
                                                Disponible
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <?php if (!$hasUsers): ?>
                            <div class="empty-state" style="grid-column: 1 / -1;">
                                <i class="fas fa-users-slash"></i>
                                <h4>Aucun utilisateur disponible</h4>
                                <p>Il n'y a pas d'autres utilisateurs pour le moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn-premium btn-primary-premium" id="startBtn" disabled>
                            <i class="fas fa-paper-plane"></i>
                            <span>Démarrer la conversation</span>
                        </button>
                        <a href="index.php?controller=messagerie&action=inbox&user=<?= $user_id ?>" class="btn-premium btn-secondary-premium">
                            <i class="fas fa-arrow-left"></i>
                            <span>Retour</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
let selectedCard = null;

function selectUser(userId, element) {
    // Remove selection from previous card
    if (selectedCard) {
        selectedCard.classList.remove('selected');
    }
    
    // Add selection to new card
    element.classList.add('selected');
    selectedCard = element;
    
    // Update hidden input and enable button
    document.getElementById('other_user_id').value = userId;
    document.getElementById('startBtn').disabled = false;
}

function filterUsers() {
    const searchValue = document.getElementById('searchUser').value.toLowerCase();
    const userCards = document.querySelectorAll('.user-card');
    
    userCards.forEach(card => {
        const name = card.getAttribute('data-name');
        if (name.includes(searchValue)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
</body>
</html>
