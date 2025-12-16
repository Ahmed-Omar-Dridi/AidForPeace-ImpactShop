<?php
/**
 * Point d'entrée principal de l'application
 * Routeur simple pour gérer les contrôleurs et actions
 */

session_start();

// Récupération du contrôleur et de l'action
$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Chargement du contrôleur approprié
switch ($controller) {
    case 'product':
        require_once __DIR__ . '/controllers/ProductController.php';
        $controllerInstance = new ProductController();
        break;
    
    case 'order':
        require_once __DIR__ . '/controllers/OrderController.php';
        $controllerInstance = new OrderController();
        break;
    
    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controllerInstance = new DashboardController();
        break;
    
    case 'home':
    default:
        require_once __DIR__ . '/controllers/HomeController.php';
        $controllerInstance = new HomeController();
        break;
}

// Exécution de l'action
if (method_exists($controllerInstance, $action)) {
    $controllerInstance->$action();
} else {
    die("Action '$action' non trouvée dans le contrôleur '$controller'");
}
?>