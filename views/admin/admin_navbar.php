<?php
// Admin Sidebar Navigation - Réutilisable dans toutes les pages admin
// Affiche une barre latérale à gauche avec les onglets de navigation

$currentController = isset($_GET['controller']) ? $_GET['controller'] : '';
$currentAction = isset($_GET['action']) ? $_GET['action'] : '';

// Définir les onglets disponibles
$adminTabs = [
    [
        'label' => 'Dashboard',
        'icon' => 'fa-chart-line',
        'controller' => 'dashboard',
        'action' => 'index',
        'color' => '#ffb600'
    ],
    [
        'label' => 'Produits',
        'icon' => 'fa-box',
        'controller' => 'product',
        'action' => 'index',
        'color' => '#ffb600'
    ],
    [
        'label' => 'Commandes',
        'icon' => 'fa-receipt',
        'controller' => 'order',
        'action' => 'index',
        'color' => '#ffb600'
    ],
    [
        'label' => 'Catégories',
        'icon' => 'fa-folder',
        'controller' => 'category',
        'action' => 'index',
        'color' => '#27ae60'
    ],
    [
        'label' => 'Paiements',
        'icon' => 'fa-credit-card',
        'controller' => 'payment',
        'action' => 'index',
        'color' => '#27ae60'
    ],
    [
        'label' => 'Livraisons',
        'icon' => 'fa-shipping-fast',
        'controller' => 'shipping',
        'action' => 'index',
        'color' => '#27ae60'
    ],
    [
        'label' => 'Avis',
        'icon' => 'fa-star',
        'controller' => 'review',
        'action' => 'index',
        'color' => '#27ae60'
    ],
    [
        'label' => 'Tracking',
        'icon' => 'fa-search-location',
        'controller' => 'shipping',
        'action' => 'track',
        'color' => '#ffb600'
    ],
    [
        'label' => 'Fidélité',
        'icon' => 'fa-coins',
        'controller' => 'loyalty',
        'action' => 'adminIndex',
        'color' => '#27ae60'
    ]
];
?>

<style>
    /* Sidebar Container */
    .admin-sidebar-wrapper {
        display: flex;
        gap: 0;
        min-height: 100vh;
    }

    .admin-sidebar {
        width: 280px;
        background: linear-gradient(135deg, #1e3149 0%, #15202e 100%);
        padding: 30px 0;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        box-shadow: 5px 0 20px rgba(0,0,0,0.15);
        z-index: 1000;
    }

    .admin-sidebar-title {
        padding: 0 25px 30px 25px;
        border-bottom: 2px solid rgba(255,182,0,0.2);
        margin-bottom: 20px;
    }

    .admin-sidebar-title h2 {
        font-size: 1.4rem;
        font-weight: 800;
        color: white;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .admin-sidebar-title i {
        color: #ffb600;
        font-size: 1.3rem;
    }

    .admin-sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 0 15px;
    }

    .admin-sidebar-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 14px 18px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .admin-sidebar-item:hover {
        background: rgba(255,182,0,0.1);
        color: white;
        transform: translateX(5px);
    }

    .admin-sidebar-item.active {
        background: rgba(255,182,0,0.15);
        color: white;
        border-left-color: #ffb600;
    }

    .admin-sidebar-item i {
        font-size: 1.2rem;
        width: 24px;
        text-align: center;
    }

    /* Main Content Area */
    .admin-main-content {
        margin-left: 280px;
        flex: 1;
        width: calc(100% - 280px);
    }

    /* Scrollbar Styling */
    .admin-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .admin-sidebar::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.05);
    }

    .admin-sidebar::-webkit-scrollbar-thumb {
        background: rgba(255,182,0,0.3);
        border-radius: 3px;
    }

    .admin-sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255,182,0,0.5);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .admin-sidebar {
            width: 250px;
        }

        .admin-main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        .admin-sidebar-item {
            padding: 12px 15px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 768px) {
        .admin-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .admin-main-content {
            margin-left: 0;
            width: 100%;
        }

        .admin-sidebar-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            padding: 20px 15px;
        }

        .admin-sidebar-item {
            padding: 12px 14px;
            font-size: 0.85rem;
            flex-direction: column;
            text-align: center;
        }

        .admin-sidebar-item span {
            display: none;
        }
    }
</style>

<!-- Sidebar Navigation -->
<div class="admin-sidebar-wrapper">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">
            <h2>
                <i class="fas fa-cogs"></i>
                Admin
            </h2>
        </div>
        
        <nav class="admin-sidebar-menu">
            <?php foreach ($adminTabs as $tab): ?>
                <?php
                    $isActive = ($currentController === $tab['controller'] && 
                               ($currentAction === $tab['action'] || $currentAction === ''));
                    $activeClass = $isActive ? 'active' : '';
                    $url = "index.php?controller=" . $tab['controller'] . "&action=" . $tab['action'];
                ?>
                <a href="<?php echo $url; ?>" class="admin-sidebar-item <?php echo $activeClass; ?>" 
                   title="<?php echo $tab['label']; ?>">
                    <i class="fas <?php echo $tab['icon']; ?>" style="color: <?php echo $tab['color']; ?>;"></i>
                    <span><?php echo $tab['label']; ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="admin-main-content">
