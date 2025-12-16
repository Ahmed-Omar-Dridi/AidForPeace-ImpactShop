<?php
// Récupérer les infos utilisateur de la session
$current_user_name = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';
$current_user_email = $_SESSION['user_email'] ?? '';
$current_user_id = $_SESSION['user_id'] ?? 1;
$current_action = $_GET['action'] ?? 'index';
?>

<!-- SIDEBAR -->
<div class="col-md-3 msg-sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-comments"></i> Messagerie</h3>
        <p>Communication interne</p>
    </div>
    
    <!-- User Info -->
    <div class="sidebar-user">
        <div class="user-avatar-lg"><?= strtoupper(substr($current_user_name, 0, 1)) ?></div>
        <div class="user-info">
            <strong><?= htmlspecialchars($current_user_name) ?></strong>
            <small><?= htmlspecialchars($current_user_email) ?></small>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="sidebar-nav">
        <a href="index.php?controller=messagerie&action=index" class="<?= $current_action == 'index' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Accueil Messagerie
        </a>
        <a href="index.php?controller=messagerie&action=inbox&user=<?= $current_user_id ?>" class="<?= $current_action == 'inbox' ? 'active' : '' ?>">
            <i class="fas fa-inbox"></i> Mes Conversations
        </a>
        <a href="index.php?controller=messagerie&action=new_chat&user=<?= $current_user_id ?>" class="<?= $current_action == 'new_chat' ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i> Nouvelle Conversation
        </a>
        <a href="index.php?controller=messagerie&action=chatbot" class="<?= $current_action == 'chatbot' ? 'active' : '' ?>">
            <i class="fas fa-robot"></i> ChatBot Assistant
        </a>
    </nav>
    
    <hr class="sidebar-divider">
    
    <!-- Quick Links -->
    <nav class="sidebar-nav">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Retour Accueil</a>
        <a href="index.php?controller=user&action=profile"><i class="fas fa-user"></i> Mon Profil</a>
        <a href="index.php?controller=user&action=logout" class="logout-link"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </nav>
</div>

<style>
.msg-sidebar {
    background: linear-gradient(180deg, #1e3149 0%, #15202e 100%);
    min-height: calc(100vh - 65px);
    padding: 25px 20px;
    color: #fff;
}

.sidebar-header {
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.sidebar-header h3 {
    color: #fff;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 5px;
}
.sidebar-header h3 i { color: #ffb600; margin-right: 8px; }
.sidebar-header p {
    color: rgba(255,255,255,0.6);
    font-size: 0.85rem;
    margin: 0;
}

.sidebar-user {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255,255,255,0.08);
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 25px;
}
.user-avatar-lg {
    width: 45px;
    height: 45px;
    background: #ffb600;
    color: #1e3149;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
}
.user-info strong {
    display: block;
    color: #fff;
    font-size: 0.95rem;
}
.user-info small {
    color: rgba(255,255,255,0.6);
    font-size: 0.8rem;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s;
}
.sidebar-nav a:hover {
    background: rgba(255,182,0,0.15);
    color: #ffb600;
    transform: translateX(5px);
}
.sidebar-nav a.active {
    background: rgba(255,182,0,0.2);
    color: #ffb600;
    font-weight: 600;
}
.sidebar-nav a i {
    width: 20px;
    text-align: center;
    font-size: 1rem;
}
.sidebar-nav a.logout-link { color: #f87171; }
.sidebar-nav a.logout-link:hover { background: rgba(248,113,113,0.15); color: #f87171; }

.sidebar-divider {
    border: none;
    border-top: 1px solid rgba(255,255,255,0.1);
    margin: 20px 0;
}
</style>
