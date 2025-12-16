<?php
// receipt.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/DonationController.php';
require_once __DIR__ . '/../../controllers/NGOController.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../services/EmailService.php';

// Instanciation des controllers
$donationController = new DonationController();
$ngoController = new NGOController();

if (!isset($_GET['donation_id'])) {
    header('Location: index.php?controller=donation&action=ngos');
    exit;
}

$donation_id = intval($_GET['donation_id']);
$donation = $donationController->getDonationById($donation_id);

if (!$donation) {
    header('Location: index.php?controller=donation&action=ngos');
    exit;
}

// Récupérer les infos ONG
$ngo_id = $donation['ngo_id'] ?? ($_GET['ngo_id'] ?? null);
$ngo = $ngo_id ? $ngoController->getNgo($ngo_id) : null;
$ngo_name = $ngo ? $ngo['name'] : 'ONG inconnue';

// Variables pour le template
$donation_type = $donation['type'] ?? $donation['payment_method'] ?? '-';
$donation_date = $donation['donation_date'] ?? $donation['created_at'] ?? date('Y-m-d H:i:s');
$pdf_available = false;
$pdf_file = null;

// Génération du PDF (optionnel - seulement si FPDF est installé)
$fpdf_path = __DIR__ . '/../../pdf/fpdf.php';
if (file_exists($fpdf_path)) {
    require_once $fpdf_path;
    
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Recu de don', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Nom du donateur :', 0, 0);
    $pdf->Cell(0, 10, $donation['donor_name'], 0, 1);

    $pdf->Cell(50, 10, 'Email :', 0, 0);
    $pdf->Cell(0, 10, $donation['donor_email'], 0, 1);

    $pdf->Cell(50, 10, 'ONG :', 0, 0);
    $pdf->Cell(0, 10, $ngo_name, 0, 1);

    $pdf->Cell(50, 10, 'Type de don :', 0, 0);
    $pdf->Cell(0, 10, $donation_type, 0, 1);

    $pdf->Cell(50, 10, 'Montant :', 0, 0);
    $pdf->Cell(0, 10, number_format($donation['amount'], 2) . ' EUR', 0, 1);

    $pdf->Cell(50, 10, 'Message :', 0, 0);
    $pdf->MultiCell(0, 10, $donation['message'] ?? '');

    $pdf->Cell(50, 10, 'Date du don :', 0, 0);
    $pdf->Cell(0, 10, date('d/m/Y H:i', strtotime($donation_date)), 0, 1);

    // Enregistrer le PDF
    $pdf_file = __DIR__ . "/receipt_{$donation_id}.pdf";
    $pdf->Output('F', $pdf_file);
    $pdf_available = true;
}

// Calcul des points et niveaux
$points = $donationController->getDonationsCountByEmail($donation['donor_email']);
$levels = [
    1 => "🎁 + 5$ cadeau",
    5 => "🎁 + 10$ cadeau",
    10 => "🎁 + 20$ cadeau",
    20 => "🎁 + 30$ cadeau",
    100 => "⭐ Devenir membre"
];
$currentLevel = 0;
foreach ($levels as $levelPoint => $reward) {
    if ($points >= $levelPoint) $currentLevel = $levelPoint;
}
$levelName = $levels[$currentLevel] ?? 'Débutant';

// Envoyer l'email de remerciement
EmailService::sendThankYouEmail(
    $donation['donor_email'],
    $donation['donor_name'],
    $ngo_name,
    number_format($donation['amount'], 2),
    $donation_type
);

// Affichage du reçu
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container my-5">
    <div class="card p-4 text-center">
        <h2 class="mb-3">Merci pour votre don !</h2>
        <p>Votre contribution à <strong><?= htmlspecialchars($ngo_name) ?></strong> a été enregistrée.</p>
        <p>Montant : <strong><?= number_format($donation['amount'], 2) ?> €</strong></p>
        <p>Date : <strong><?= date('d/m/Y H:i', strtotime($donation_date)) ?></strong></p>

        <?php if ($pdf_available): ?>
        <a href="receipt_<?= $donation_id ?>.pdf" class="btn btn-success mt-3" download>
            Télécharger le reçu PDF
        </a>
        <?php endif; ?>
        
        <a href="<?= BASE_URL ?>index.php?controller=donation&action=ngos" class="btn btn-primary mt-3">
            <i class="fas fa-arrow-left me-2"></i>Retour aux ONG
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
