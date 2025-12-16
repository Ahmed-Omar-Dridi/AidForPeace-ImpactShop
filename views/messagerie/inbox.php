<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../controllers/MessagerieController.php';
$ctrl = new MessagerieController();

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (isset($_GET['user']) ? (int)$_GET['user'] : 1);
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';

$conversations = $ctrl->getUserConversations($user_id);
$stats = $ctrl->getStats($user_id);
$user = $ctrl->getUserById($user_id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie - AidForPeace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ffd700;
            --primary-dark: #e6c200;
            --primary-light: #ffdf33;
            --secondary: #07112b;
            --secondary-dark: #050d1f;
            --secondary-light: #1a3a5c;
            --accent: #00bcd4;
            --success: #4caf50;
            --danger: #f44336;
            --white: #ffffff;
            --glass-bg: rgba(7, 17, 43, 0.95);
            --glass-border: rgba(255, 215, 0, 0.2);
            --text-light: #ffffff;
            --text-muted: rgba(255,255,255,0.7);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #07112b, #1a3a5c, #07102b, #0d2240);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: var(--text-light);
            line-height: 1.6;
            min-height: 100vh;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animated Stars */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,0.4), transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.3), transparent),
                radial-gradient(1px 1px at 90px 40px, rgba(255,255,255,0.5), transparent),
                radial-gradient(2px 2px at 130px 80px, rgba(255,255,255,0.3), transparent);
            background-size: 250px 250px;
            animation: twinkle 5s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        /* Animated Top Line */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
            z-index: 9999;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* NAVBAR */
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            padding: 0 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            position: sticky;
            top: 3px;
            z-index: 1000;
            border-bottom: 1px solid var(--glass-border);
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-100%); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .navbar-logo i { 
            color: var(--primary); 
            font-size: 1.6rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .navbar-logo .aid { color: var(--white); }
        .navbar-logo .peace { color: var(--primary); }
        
        .navbar-menu {
            display: flex;
            list-style: none;
            gap: 5px;
        }
        .navbar-menu a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .navbar-menu a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.4s ease;
            z-index: -1;
            border-radius: 12px;
        }

        .navbar-menu a:hover, .navbar-menu a.active {
            color: var(--secondary);
        }

        .navbar-menu a:hover::before, .navbar-menu a.active::before {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .navbar-right { display: flex; align-items: center; gap: 15px; }
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            padding: 10px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        }
        .btn-login:hover { 
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 215, 0, 0.4);
        }

        /* MAIN LAYOUT */
        .main-layout {
            display: flex;
            min-height: calc(100vh - 73px);
            position: relative;
            z-index: 1;
        }

        /* SIDEBAR */
        .sidebar {
            width: 280px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            padding: 30px 20px;
            color: white;
            border-right: 1px solid var(--glass-border);
            animation: fadeInLeft 0.6s ease-out;
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .sidebar-header {
            text-align: center;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 25px;
        }

        .sidebar-header h3 {
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 8px;
            transition: var(--transition);
            font-weight: 500;
            border: 1px solid transparent;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(255, 215, 0, 0.15);
            color: var(--primary);
            border-color: var(--glass-border);
            transform: translateX(5px);
        }

        .sidebar-nav a i {
            width: 20px;
            text-align: center;
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            padding: 30px;
            background: transparent;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: var(--text-light);
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header h1 i {
            color: var(--primary);
        }

        /* USER SELECTOR */
        .user-selector {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            padding: 22px;
            border-radius: 16px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            border: 1px solid var(--glass-border);
        }

        .user-selector label {
            font-weight: 600;
            color: var(--text-light);
        }

        .user-selector select {
            padding: 12px 18px;
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            min-width: 200px;
            cursor: pointer;
            transition: var(--transition);
            background: rgba(255,255,255,0.95);
            color: var(--secondary);
        }

        .user-selector select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.15);
        }

        .btn-new {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            margin-left: auto;
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        }

        .btn-new:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 215, 0, 0.4);
        }

        /* STATS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            padding: 28px;
            border-radius: 18px;
            text-align: center;
            border: 1px solid var(--glass-border);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(255, 215, 0, 0.2);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card i {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .stat-card h3 {
            font-size: 2rem;
            color: var(--text-light);
            margin-bottom: 5px;
        }

        .stat-card p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* CONVERSATIONS */
        .conversations-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
        }

        .conversations-header {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(0, 188, 212, 0.1));
            padding: 22px 25px;
            color: white;
            border-bottom: 1px solid var(--glass-border);
        }

        .conversations-header h2 {
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary);
        }

        .conversations-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .conversation-item {
            padding: 22px 25px;
            border-bottom: 1px solid var(--glass-border);
            cursor: pointer;
            transition: var(--transition);
        }

        .conversation-item:hover {
            background: rgba(255, 215, 0, 0.08);
            padding-left: 30px;
        }

        .conversation-item:last-child {
            border-bottom: none;
        }

        .conv-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .conv-name {
            font-weight: 600;
            color: var(--text-light);
            font-size: 1rem;
        }

        .conv-badge {
            background: linear-gradient(135deg, var(--danger), #d32f2f);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(244, 67, 54, 0.3);
        }

        .conv-preview {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .conv-time {
            color: var(--text-muted);
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .conv-time i {
            color: var(--primary);
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 60px 40px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .empty-state h3 {
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            padding: 14px 28px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 35px rgba(255, 215, 0, 0.4);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        /* Custom Scrollbar */
        .conversations-list::-webkit-scrollbar {
            width: 8px;
        }

        .conversations-list::-webkit-scrollbar-track {
            background: rgba(7, 17, 43, 0.5);
        }

        .conversations-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 10px;
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .navbar { padding: 0 20px; }
            .navbar-menu { display: none; }
            .main-layout { flex-direction: column; }
            .sidebar { width: 100%; padding: 20px; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="navbar-logo">
            <i class="fas fa-hand-holding-heart"></i>
            <span><span class="aid">Aid</span> <span class="peace">ForPeace</span></span>
        </a>
        
        <ul class="navbar-menu">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="index.php?controller=user&action=index">Utilisateurs</a></li>
            <li><a href="index.php?controller=map&action=index">Map Mondiale</a></li>
            <li><a href="index.php?controller=messagerie&action=inbox&user=<?= $user_id ?>" class="active">Messagerie</a></li>
            <li><a href="index.php?page=testimonials">Feedback</a></li>
            <li><a href="index.php?controller=product&action=shop">Boutique</a></li>
        </ul>
        
        <div class="navbar-right">
            <?php if ($is_logged_in): ?>
                <a href="index.php?controller=page&action=profile" class="btn-login">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($user_name) ?>
                </a>
            <?php else: ?>
                <a href="index.php?controller=user&action=login" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Connexion
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- MAIN LAYOUT -->
    <div class="main-layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-comments"></i> Messagerie</h3>
                <p>Gérez vos conversations</p>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php?controller=messagerie&action=inbox&user=<?= $user_id ?>" class="active">
                    <i class="fas fa-inbox"></i> Boîte de réception
                </a>
                <a href="index.php?controller=messagerie&action=new_chat&user=<?= $user_id ?>">
                    <i class="fas fa-plus-circle"></i> Nouvelle conversation
                </a>
                <a href="index.php?controller=messagerie&action=chatbot">
                    <i class="fas fa-robot"></i> Chatbot IA
                </a>
                <a href="index.php">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-inbox"></i> Boîte de réception</h1>
            </div>

            <!-- USER SELECTOR -->
            <div class="user-selector">
                <label><i class="fas fa-user"></i> Utilisateur :</label>
                <select onchange="location.href='index.php?controller=messagerie&action=inbox&user='+this.value">
                    <?php $all_users = $ctrl->getUtilisateurs();
                    foreach($all_users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $u['id'] == $user_id ? 'selected' : '' ?>><?= htmlspecialchars($u['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
                <a href="index.php?controller=messagerie&action=new_chat&user=<?= $user_id ?>" class="btn-new">
                    <i class="fas fa-plus"></i> Nouvelle conversation
                </a>
            </div>

            <!-- STATS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-comments"></i>
                    <h3><?= $stats['total_conversations'] ?? 0 ?></h3>
                    <p>Conversations</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-envelope"></i>
                    <h3><?= $stats['unread_messages'] ?? 0 ?></h3>
                    <p>Messages non lus</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-paper-plane"></i>
                    <h3><?= $stats['total_messages_sent'] ?? 0 ?></h3>
                    <p>Messages envoyés</p>
                </div>
            </div>

            <!-- CONVERSATIONS -->
            <div class="conversations-card">
                <div class="conversations-header">
                    <h2><i class="fas fa-list"></i> Mes conversations</h2>
                </div>
                <div class="conversations-list">
                    <?php if (empty($conversations)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>Aucune conversation</h3>
                            <p>Commencez une nouvelle conversation pour échanger avec d'autres utilisateurs</p>
                            <a href="index.php?controller=messagerie&action=new_chat&user=<?= $user_id ?>" class="btn-primary">
                                <i class="fas fa-plus"></i> Démarrer une conversation
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach($conversations as $conv): ?>
                            <div class="conversation-item" onclick="location.href='index.php?controller=messagerie&action=chat&conv=<?= $conv['id'] ?>&user=<?= $user_id ?>'">
                                <div class="conv-header">
                                    <span class="conv-name"><?= htmlspecialchars($conv['other_user_nom'] ?? 'Utilisateur') ?></span>
                                    <?php if (($conv['unread_count'] ?? 0) > 0): ?>
                                        <span class="conv-badge"><?= $conv['unread_count'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="conv-preview">
                                    <?php if (!empty($conv['last_message'])): ?>
                                        <?php if (($conv['last_message_sender_id'] ?? null) == $user_id): ?>
                                            <strong>Vous:</strong>
                                        <?php endif; ?>
                                        <?= htmlspecialchars(substr($conv['last_message'], 0, 60)) ?>...
                                    <?php else: ?>
                                        <em>Aucun message</em>
                                    <?php endif; ?>
                                </p>
                                <span class="conv-time">
                                    <i class="fas fa-clock"></i> <?= $conv['last_message_date'] ?? 'N/A' ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
