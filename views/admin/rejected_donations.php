<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/DonationController.php';
require_once __DIR__ . '/../../controllers/NGOController.php';

$donationController = new DonationController();
$ngoController = new NGOController();
$rejectedDonations = $donationController->getDonationsByStatus('refusAc');

$pageTitle = 'Dons rejetes';
$activePage = 'backoffice';
$mainClass = 'pt-0';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero py-4">
  <div class="container py-3">
    <div class="row align-items-center">
      <div class="col">
        <div class="hero-badge mb-3"><i class="fas fa-ban"></i><span>Dons rejetes</span></div>
        <h1 class="fw-bold display-6 mb-1">Suivi des dons rejetes</h1>
        <p class="text-white-50 mb-0">Historique des dons refuses avec possibilite de nettoyage.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="dashboard.php" class="btn btn-outline-light px-4 py-3"><i class="fas fa-arrow-left me-2"></i>Dashboard</a>
      </div>
    </div>
  </div>
</section>

<div class="container my-5">
  <div class="table-modern">
    <?php if(empty($rejectedDonations)): ?>
      <div class="p-4 text-center text-muted">Aucun don rejete.</div>
    <?php else: ?>
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>ONG</th>
                <th>Donateur</th>
                <th>Email</th>
                <th>Pays</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Message</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rejectedDonations as $don):
                $ngo = $ngoController->getNgo($don['ngo_id']);
            ?>
            <tr>
                <td><?= $don['id'] ?></td>
                <td><?= htmlspecialchars($ngo['name']) ?></td>
                <td><?= htmlspecialchars($don['donor_name']) ?></td>
                <td><?= htmlspecialchars($don['donor_email']) ?></td>
                <td><?= htmlspecialchars($don['country']) ?></td>
                <td><?= htmlspecialchars($don['type']) ?></td>
                <td><?= htmlspecialchars($don['amount']) ?> &euro;</td>
                <td><?= htmlspecialchars($don['message']) ?></td>
                <td><?= htmlspecialchars($don['donation_date']) ?></td>
                <td>
                    <a class="btn btn-sm btn-outline-danger" href="delete_donation.php?id=<?= $don['id'] ?>" onclick="return confirm('Supprimer ce don ?')"><i class="fas fa-trash me-1"></i>Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
