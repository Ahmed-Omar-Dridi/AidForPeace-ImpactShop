<?php
// Récupérer les infos utilisateur de la session
$current_user_name = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';
$current_user_email = $_SESSION['user_email'] ?? '';
$current_user_id = $_SESSION['user_id'] ?? 1;
$current_user_role = $_SESSION['user_role'] ?? 'user';
?>

<!-- NAVBAR AIDFORPEACE -->
<nav class="main-navbar">
    <a href="index.php" class="navbar-logo">
        <i class="fas fa-hand-holding-heart"></i>
        <span class="aid">Aid</span><span class="peace">ForPeace</span>
    </a>
    
    <ul class="navbar-menu">
        <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
        <li><a href="index.php?controller=user&action=index"><i class="fas fa-users"></i> Utilisateurs</a></li>
        <li><a href="index.php?controller=map&action=index"><i class="fas fa-globe-africa"></i> Map</a></li>
        <li><a href="index.php?controller=messagerie&action=index" class="active"><i class="fas fa-comments"></i> Messagerie</a></li>
        <li><a href="index.php?controller=product&action=shop"><i class="fas fa-store"></i> Boutique</a></li>
    </ul>
    
    <div class="navbar-right">
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-dropdown">
            <button class="user-btn" onclick="toggleUserMenu()">
                <div class="user-avatar"><?= strtoupper(substr($current_user_name, 0, 1)) ?></div>
                <span><?= htmlspecialchars($current_user_name) ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu" id="userDropdownMenu">
                <a href="index.php?controller=user&action=profile"><i class="fas fa-user"></i> Mon Profil</a>
                <a href="index.php?controller=user&action=edit_profile"><i class="fas fa-cog"></i> Paramètres</a>
                <?php if ($current_user_role === 'admin'): ?>
                <a href="index.php?controller=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <?php endif; ?>
                <hr>
                <a href="index.php?controller=user&action=logout" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
        <?php else: ?>
        <a href="index.php?controller=user&action=login" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Connexion
        </a>
        <?php endif; ?>
    </div>
</nav>

<style>
.main-navbar {
    background: linear-gradient(135deg, #1e3149 0%, #15202e 100%);
    padding: 0 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 65px;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.navbar-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    font-size: 1.4rem;
    font-weight: 700;
}
.navbar-logo i { color: #ffb600; font-size: 1.5rem; }
.navbar-logo .aid { color: #ffffff; }
.navbar-logo .peace { color: #ffb600; }

.navbar-menu {
    display: flex;
    list-style: none;
    gap: 5px;
    margin: 0;
    padding: 0;
}
.navbar-menu li a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    padding: 12px 18px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}
.navbar-menu li a:hover,
.navbar-menu li a.active {
    background: rgba(255,182,0,0.15);
    color: #ffb600;
}
.navbar-menu li a i { font-size: 1rem; }

.navbar-right { display: flex; align-items: center; gap: 15px; }

.btn-login {
    background: #ffb600;
    color: #1e3149;
    padding: 10px 22px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}
.btn-login:hover {
    background: #ffc933;
    transform: translateY(-2px);
}

.user-dropdown { position: relative; }
.user-btn {
    background: rgba(255,182,0,0.15);
    border: 2px solid rgba(255,182,0,0.3);
    color: #fff;
    padding: 8px 15px;
    border-radius: 25px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    transition: all 0.3s;
}
.user-btn:hover {
    background: rgba(255,182,0,0.25);
    border-color: #ffb600;
}
.user-avatar {
    width: 32px;
    height: 32px;
    background: #ffb600;
    color: #1e3149;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
}
.user-btn i.fa-chevron-down { font-size: 0.7rem; opacity: 0.7; }

.dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    min-width: 220px;
    display: none;
    overflow: hidden;
    z-index: 1001;
}
.dropdown-menu.show { display: block; animation: fadeIn 0.2s ease; }
.dropdown-menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    color: #1e3149;
    text-decoration: none;
    transition: all 0.2s;
    font-size: 0.95rem;
}
.dropdown-menu a:hover { background: #f8f9fa; }
.dropdown-menu a i { color: #64748b; width: 18px; }
.dropdown-menu a.logout { color: #ef4444; }
.dropdown-menu a.logout i { color: #ef4444; }
.dropdown-menu hr { margin: 5px 0; border: none; border-top: 1px solid #e2e8f0; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 992px) {
    .main-navbar { padding: 0 20px; flex-wrap: wrap; height: auto; padding: 15px 20px; }
    .navbar-menu { display: none; }
}
</style>

<script>
function toggleUserMenu() {
    const menu = document.getElementById('userDropdownMenu');
    menu.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.querySelector('.user-dropdown');
    const menu = document.getElementById('userDropdownMenu');
    if (dropdown && menu && !dropdown.contains(e.target)) {
        menu.classList.remove('show');
    }
});
</script>
