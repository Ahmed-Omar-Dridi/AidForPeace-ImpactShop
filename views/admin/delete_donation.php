<?php
// Charger la configuration et le contrôleur avec les bons chemins
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/DonationController.php';

// Instanciation du contrôleur
$donationController = new DonationController();

// Vérifier que l'ID du don est passé en paramètre
if (!isset($_GET['id'])) {
    header('Location: index.php?controller=admin&action=donations&error=not_found');
    exit;
}

$donation_id = intval($_GET['id']);

// Supprimer le don
if ($donationController->deleteDonation($donation_id)) {
    // Redirection vers la page de gestion des dons
    header("Location: index.php?controller=admin&action=donations&success=deleted");
    exit;
} else {
    header('Location: index.php?controller=admin&action=donations&error=delete_failed');
    exit;
}

?>
