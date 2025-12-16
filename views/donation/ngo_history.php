<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/NGOController.php';
require_once __DIR__ . '/../../controllers/DonationController.php';

$ngoController = new NGOController();
$donationController = new DonationController();

if(!isset($_GET['id'])) {
    header('Location: index.php?controller=donation&action=ngos');
    exit;
}

$ngo_id = intval($_GET['id']);
$ngo = $ngoController->getNgo($ngo_id);
if(!$ngo) {
    header('Location: index.php?controller=donation&action=ngos');
    exit;
}

$donations = $donationController->getDonationsByNGOAndStatus($ngo_id, 'accepter') ?: [];


$pageTitle = 'Historique | ' . htmlspecialchars($ngo['name']);
$activePage = 'ngos';
$mainClass = 'pt-0';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero py-4">
  <div class="container py-4">
    <div class="row align-items-center gy-3">
      <div class="col-lg-8">
        <div class="hero-badge mb-3"><i class="fas fa-clipboard-list"></i><span>Historique des dons</span></div>
        <h1 class="fw-bold display-6 mb-2"><?= htmlspecialchars($ngo['name']) ?></h1>
        <p class="text-white-50 mb-0">Parcourez les informations generales de l'ONG et les contributions deja recues.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="<?= BASE_URL ?>index.php?controller=donation&action=ngos" class="btn btn-outline-light px-4 py-3"><i class="fas fa-arrow-left me-2"></i>Retour</a>
        <a href="<?= BASE_URL ?>index.php?controller=donation&action=donation_form&id=<?= $ngo_id ?>" class="btn btn-amber px-4 py-3 ms-lg-2 mt-2 mt-lg-0"><i class="fas fa-heart me-2"></i>Faire un don</a>
      </div>
    </div>
  </div>
</section>

<div class="container my-5">
  <div class="card-lift mb-4 p-4">
    <div class="row gy-3 align-items-center">
      <?php if(!empty($ngo['image'])): ?>
      <div class="col-md-4">
        <img src="../../assets/images/<?= htmlspecialchars($ngo['image']) ?>" alt="<?= htmlspecialchars($ngo['name']) ?>" class="img-fluid rounded">
      </div>
      <?php endif; ?>
      <div class="col">
        <h2 class="h4 fw-bold mb-3">Informations generales</h2>
        <p class="mb-2"><strong>Pays :</strong> <?= htmlspecialchars($ngo['country']) ?></p>
        <p class="mb-2"><strong>Adresse :</strong> <?= htmlspecialchars($ngo['address']) ?></p>
        <p class="mb-0"><strong>Historique :</strong> <?= nl2br(htmlspecialchars($ngo['history'])) ?></p>
      </div>
    </div>
  </div>

  <div class="table-modern">
    <?php if(count($donations) > 0): ?>
      <table class="table table-hover align-middle mb-0">
          <thead>
              <tr>
                  <th>Pays</th>
                  <th>Type</th>
                  <th>Montant</th>
                  <th>Message</th>
                  <th>Date</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($donations as $don): ?>
              <tr>
                  <td><?= htmlspecialchars($don['country'] ?? $ngo['country'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($don['type'] ?? $don['payment_method'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($don['amount'] ?? '0') ?> &euro;</td>
                  <td><?= htmlspecialchars($don['message'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($don['donation_date'] ?? $don['created_at'] ?? '-') ?></td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <div class="p-4 text-center text-muted">Aucun don recu pour le moment.</div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
