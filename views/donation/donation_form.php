<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/NGOController.php';
require_once __DIR__ . '/../../controllers/DonationController.php';
require_once __DIR__ . '/../../models/Donation.php';
require_once __DIR__ . '/../../services/EmailService.php';

// Helper function for sending thank you emails
function sendThankYouEmail($email, $donorName, $ngoName, $amount, $type) {
    return EmailService::sendThankYouEmail($email, $donorName, $ngoName, $amount, $type);
}

$ngoController = new NGOController();
$donationController = new DonationController();

// Redirect to NGO list if no ID specified
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

$error = '';
$donationId = null;
$giftMessage = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor_name = trim($_POST['donor_name'] ?? '');
    $donor_email = trim($_POST['donor_email'] ?? '');
    $confirm_email = trim($_POST['confirm_email'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $errorsArr = [];

    if($donor_name === '' || strlen($donor_name) < 2) $errorsArr[] = "Le nom est requis (au moins 2 caractères).";
    if(!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/u', $donor_email)) $errorsArr[] = "Email invalide.";
    if($donor_email !== $confirm_email) $errorsArr[] = "Les deux emails ne correspondent pas.";
    if($country === '') $errorsArr[] = "Le pays est requis.";
    if($type === '') $errorsArr[] = "Veuillez choisir un type de don.";
    if($amount === '' || !preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $amount) || floatval($amount) <= 0)
        $errorsArr[] = "Le montant doit être un nombre positif (ex : 10 ou 10.50).";

    if(empty($errorsArr)) {
        $newDonation = new Donation($ngo_id, $donor_name, $donor_email, $country, $type, $amount, $message, null, null);
        $donationId = $donationController->createDonation($newDonation);

        if($donationId) {
            sendThankYouEmail($donor_email, $donor_name, $ngo['name'], $amount, $type);

            $points = $donationController->getDonationsCountByEmail($donor_email); 
            $threshold = 5; 
            if($points >= $threshold) {
                $giftMessage = "🎁 Félicitations ! Vous avez atteint $threshold dons et gagné un cadeau !";
            } else {
                $giftMessage = "Il vous reste " . ($threshold - $points) . " dons pour obtenir un cadeau.";
            }

            $_SESSION['giftMessage'] = $giftMessage;
            header("Location: " . BASE_URL . "index.php?controller=donation&action=receipt&donation_id=" . $donationId);
            exit;
        } else {
            $error = "Erreur lors de l'enregistrement du don. Réessayez.";
        }
    } else {
        $error = implode('<br>', $errorsArr);
    }
}

$pageTitle = 'Faire un don | ' . htmlspecialchars($ngo['name']);
$activePage = 'ngos';
$mainClass = 'pt-0';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero py-4">
  <div class="container py-4">
    <div class="row align-items-center gy-3">
      <div class="col-lg-8">
        <div class="hero-badge mb-3"><i class="fas fa-heartbeat"></i><span>Don pour <?= htmlspecialchars($ngo['name']) ?></span></div>
        <h1 class="fw-bold display-6 mb-2">Apportez un soutien immédiat</h1>
        <p class="text-white-50 mb-0">Vos informations sont sécurisées. Chaque contribution compte.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="<?= BASE_URL ?>index.php?controller=donation&action=ngos" class="btn btn-outline-light px-4 py-3"><i class="fas fa-arrow-left me-2"></i>Retour aux ONG</a>
      </div>
    </div>
  </div>
</section>

<div class="container my-5">
  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card-lift h-100 p-4">
        <?php if(!empty($ngo['image'])): ?>
          <img src="../../assets/images/<?= htmlspecialchars($ngo['image']) ?>" alt="<?= htmlspecialchars($ngo['name']) ?>" class="img-fluid rounded mb-3">
        <?php endif; ?>
        <h4 class="fw-bold mb-2"><?= htmlspecialchars($ngo['name']) ?></h4>
        <p class="text-muted mb-3"><?= nl2br(htmlspecialchars(substr($ngo['history'], 0, 240))) ?>...</p>
        <div class="d-flex flex-wrap gap-2">
          <span class="stat-pill"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($ngo['country']) ?></span>
          <span class="stat-pill"><i class="fas fa-envelope"></i> <?= htmlspecialchars($ngo['address']) ?></span>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card-lift h-100 p-4">
        <h4 class="fw-bold mb-3">Formulaire de don</h4>
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <form id="donationForm" method="POST" novalidate class="form-modern">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="donor_name">Nom et prénom</label>
              <input id="donor_name" name="donor_name" type="text" class="form-control" placeholder="Nom et prénom" value="<?= htmlspecialchars($_POST['donor_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="donor_email">Email</label>
              <input id="donor_email" name="donor_email" type="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($_POST['donor_email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="confirm_email">Confirmer l'email</label>
              <input id="confirm_email" name="confirm_email" type="email" class="form-control" placeholder="Confirmez l'email" value="<?= htmlspecialchars($_POST['confirm_email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="country">Pays</label>
              <input id="country" name="country" type="text" class="form-control" placeholder="Pays" value="<?= htmlspecialchars($_POST['country'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="type">Type de don</label>
              <select id="type" name="type" class="form-select">
                  <option value="">--Choisir--</option>
                  <option value="Nourriture">Nourriture</option>
                  <option value="Vetement">Vêtement</option>
                  <option value="Medicament">Médicament</option>
                  <option value="Autre">Autre</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="amount">Montant</label>
              <input id="amount" name="amount" type="text" class="form-control" placeholder="Montant (ex: 10.00)" value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>">
            </div>

            <div class="col-12">
              <label class="form-label" for="message">Message (facultatif)</label>
              <textarea id="message" name="message" class="form-control" rows="4" placeholder="Ajouter un mot pour l'ONG"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="d-flex align-items-center gap-3 mt-3">
            <a href="<?= BASE_URL ?>index.php?controller=donation&action=scan&ngo_id=<?= $ngo_id ?>" class="btn btn-primary">
              📷 Scanner vos dons
            </a>

            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="moneyOnly">
              <label class="form-check-label" for="moneyOnly">
                Faire un don uniquement en argent
              </label>
            </div>
          </div>

          <button id="submitBtn" type="submit" class="btn btn-amber w-100 mt-4">Valider le don</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Vérifie si scan effectué via sessionStorage
let scanned = sessionStorage.getItem('scanned') === 'true';

document.getElementById('donationForm').addEventListener('submit', function(e) {
    let errors = [];
    const donorName = document.getElementById('donor_name').value.trim();
    const donorEmail = document.getElementById('donor_email').value.trim();
    const confirmEmail = document.getElementById('confirm_email').value.trim();
    const country = document.getElementById('country').value.trim();
    const type = document.getElementById('type').value;
    const amount = document.getElementById('amount').value.trim();
    const moneyOnly = document.getElementById('moneyOnly').checked;

    if(donorName === "" || donorName.length < 2) errors.push("Le nom est requis (au moins 2 caractères).");
    const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    if(!emailPattern.test(donorEmail)) errors.push("Email invalide.");
    if(donorEmail !== confirmEmail) errors.push("Les emails ne correspondent pas.");
    if(country === "") errors.push("Le pays est requis.");
    if(type === "") errors.push("Veuillez choisir un type de don.");
    const amountPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
    if(amount === "" || !amountPattern.test(amount) || parseFloat(amount) <= 0)
        errors.push("Le montant doit être un nombre positif (ex : 10 ou 10.50).");

    if(!moneyOnly && !scanned) {
        errors.push("Veuillez scanner l'objet que vous souhaitez donner ou cochez 'Faire un don uniquement en argent'.");
    }

    if(errors.length > 0) {
        e.preventDefault();
        alert(errors.join("\n"));
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
