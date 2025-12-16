<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/DonationController.php';
require_once __DIR__ . '/../../controllers/NGOController.php';

$donationController = new DonationController();
$ngoController = new NGOController();

if(!isset($_GET['id'])) {
    header('Location: index.php?controller=admin&action=donations');
    exit;
}
$donation_id = intval($_GET['id']);
$donation = $donationController->getDonationById($donation_id);
if(!$donation) {
    header('Location: index.php?controller=admin&action=donations');
    exit;
}

$ngo = $ngoController->getNgo($donation['ngo_id']);
$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'donor_name' => $_POST['donor_name'],
        'donor_email' => $_POST['donor_email'],
        'country' => $_POST['country'],
        'type' => $_POST['type'],
        'amount' => $_POST['amount'],
        'message' => $_POST['message'],
        'status' => $_POST['status']
    ];

    $donationNew = new Donation(
        null,
        $data['donor_name'],
        $data['donor_email'],
        $data['country'],
        $data['type'],
        $data['amount'],
        $data['message'],
        $data['status'],
        null
    );
    if($donationController->updateDonation($donationNew, $donation_id)) {
        $success = "Don mis a jour avec succes !";
    } else {
        $error = "Erreur lors de la mise a jour.";
    }
}

$pageTitle = 'Modifier don - ' . htmlspecialchars($ngo['name']);
$activePage = 'backoffice';
$mainClass = 'pt-0';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero py-4">
  <div class="container py-3">
    <div class="row align-items-center gy-2">
      <div class="col">
        <div class="hero-badge mb-3"><i class="fas fa-edit"></i><span>Backoffice</span></div>
        <h1 class="fw-bold display-6 mb-1">Modifier le don - <?= htmlspecialchars($ngo['name']) ?></h1>
        <p class="text-white-50 mb-0">Mettez a jour les informations du donateur, du montant et le statut.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="index.php?controller=admin&action=manage_donations" class="btn btn-outline-light px-4 py-3"><i class="fas fa-arrow-left me-2"></i>Retour</a>
      </div>
    </div>
  </div>
</section>

<div class="container my-5">
  <div class="card-lift p-4">
    <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST" class="form-modern">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nom et prenom</label>
          <input type="text" name="donor_name" class="form-control" value="<?= htmlspecialchars($donation['donor_name']) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="donor_email" class="form-control" value="<?= htmlspecialchars($donation['donor_email']) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Pays</label>
          <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($donation['country']) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Type de don</label>
          <select name="type" class="form-select">
            <option value="Nourriture" <?= $donation['type']==='Nourriture'?'selected':'' ?>>Nourriture</option>
            <option value="Vetement" <?= $donation['type']==='Vetement'?'selected':'' ?>>Vetement</option>
            <option value="Medicament" <?= $donation['type']==='Medicament'?'selected':'' ?>>Medicament</option>
            <option value="Autre" <?= $donation['type']==='Autre'?'selected':'' ?>>Autre</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Montant</label>
          <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($donation['amount']) ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="4"><?= htmlspecialchars($donation['message']) ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Statut</label>
          <select name="status" class="form-select">
            <option value="attente" <?= $donation['status']==='attente'?'selected':'' ?>>En attente</option>
            <option value="accepter" <?= $donation['status']==='accepter'?'selected':'' ?>>Accepte</option>
            <option value="refuse" <?= $donation['status']==='refuse'?'selected':'' ?>>Refuse</option>
          </select>
        </div>
      </div>
      <button class="btn btn-amber w-100 mt-4" type="submit">Mettre a jour</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
