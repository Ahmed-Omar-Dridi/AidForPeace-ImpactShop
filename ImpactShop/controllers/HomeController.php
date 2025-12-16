<?php
/**
 * Contrôleur HomeController
 * Gestion de la page d'accueil et navigation principale
 */

class HomeController {
    /**
     * Page d'accueil - Sélection du rôle
     */
    public function index() {
        include __DIR__ . '/../views/home/role_selection.php';
    }

    /**
     * Redirection vers l'espace Admin
     */
    public function admin() {
        header("Location: index.php?controller=product&action=index");
        exit();
    }

    /**
     * Redirection vers l'espace Buyer (Boutique)
     */
    public function buyer() {
        header("Location: index.php?controller=product&action=shop");
        exit();
    }
}
?>