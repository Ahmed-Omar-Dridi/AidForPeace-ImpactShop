<?php
// Unified Navigation Bar for AidForPeace
if (session_status() === PHP_SESSION_NONE) session_start();

$user_name = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';
$user_email = $_SESSION['user_email'] ?? '';
$user_role = $_SESSION['user_role'] ?? 'user';
$is_logged_in = isset($_SESSION['user_id']);
$current_page = $_GET['controller'] ?? 'home';
?>
<nav class="navbar">
    <a href="index.php" class="navbar-logo">
        <i class="fas fa-hand-holding-heart"></i>
        <span><span class="impact">Aid</span><span class="shop">ForPeace</span></span>
    </a>
    
    <ul class="navbar-menu">
        <li>
            <a href="index.php" class="<?= $current_page === 'home' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Accueil</span>
            </a>
        </li>
        <li>
            <a href="index.php?controller=user&action=index" class="<?= $current_page === 'user' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Utilisateurs</span>
            </a>
        </li>
        <li>
            <a href="index.php?controller=map&action=index" class="<?= $current_page === 'map' ? 'active' : '' ?>">
                <i class="fas fa-globe-africa"></i>
                <span>Map</span>
            </a>
        </li>
        <li>
            <a href="index.php?controller=messagerie&action=index" class="<?= $current_page === 'messagerie' ? 'active' : '' ?>">
                <i class="fas fa-comments"></i>
                <span>Messagerie</span>
            </a>
        </li>
        <li>
            <a href="index.php?controller=product&action=shop" class="<?= $current_page === 'product' ? 'active' : '' ?>">
                <i class="fas fa-store"></i>
                <span>Boutique</span>
            </a>
        </li>
    </ul>
    
    <div class="navbar-right">
        <?php if ($is_logged_in): ?>
        <a href="index.php?controller=user&action=profile" class="navbar-user">
            <i class="fas fa-user-circle"></i>
            <span><?= htmlspecialchars($user_name) ?></span>
        </a>
        <a href="index.php?controller=user&action=logout" class="navbar-btn-logout" title="Déconnexion">
            <i class="fas fa-sign-out-alt"></i>
        </a>
        <?php else: ?>
        <a href="index.php?controller=user&action=login" class="navbar-user">
            <i class="fas fa-sign-in-alt"></i>
            <span>Connexion</span>
        </a>
        <?php endif; ?>
    </div>
</nav>
