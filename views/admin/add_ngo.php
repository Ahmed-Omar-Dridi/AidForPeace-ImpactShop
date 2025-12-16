<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/NGOController.php';

$controller = new NGOController();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $country = trim($_POST['country']);
    $address = trim($_POST['address']);
    $history = trim($_POST['history']);

    $errorsArr = [];

    // Validation PHP
    if ($name === '' || strlen($name) < 2) $errorsArr[] = "Le nom de l'ONG est requis (au moins 2 caractères).";
    if ($country === '' || !preg_match('/^[a-zA-Z\s]+$/', $country)) $errorsArr[] = "Le pays est requis et doit contenir uniquement des lettres.";
    if (!filter_var($address, FILTER_VALIDATE_EMAIL)) $errorsArr[] = "Adresse email invalide.";
    if ($history === '' || strlen($history) < 10) $errorsArr[] = "L'historique est requis (minimum 10 caractères).";
    if (empty($_FILES['image']['name'])) $errorsArr[] = "L'image est obligatoire.";

    if (empty($errorsArr)) {
        $imgName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetPath = "../../assets/images/" . $imgName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            $newNgo = new Ngo(null, $name, $country, $address, $history, $imgName);
            $newNgoId = $controller->createNGO($newNgo); // Retourne l'ID

            if ($newNgoId) {
                // Envoi email
                sendNgoAddedEmail($address, $name);

                $success = "ONG ajoutée avec succès ! Un email de confirmation a été envoyé.";
            } else {
                $error = "Erreur lors de l'ajout de l'ONG. Réessayez.";
            }
        } else {
            $error = "Erreur lors de l'upload de l'image.";
        }
    } else {
        $error = implode('<br>', $errorsArr);
    }
}

$pageTitle = 'Ajouter une ONG';
$activePage = 'backoffice';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero py-4">
  <div class="container py-3">
    <div class="row align-items-center gy-3">
      <div class="col-lg-8">
        <div class="hero-badge mb-3"><i class="fas fa-hand-holding-heart"></i><span>Backoffice</span></div>
        <h1 class="fw-bold display-6 mb-1">Ajouter une ONG</h1>
        <p class="text-white-50 mb-0">Centralisez les partenaires et leurs visuels depuis une interface unifiée.</p>
        <p class="hero-badge mb-3">With AidForPeace, we create a better world</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="../../public/index.php" class="btn btn-outline-light px-4 py-3">
            <i class="fas fa-arrow-left me-2"></i>Retour accueil
        </a>
      </div>
    </div>
  </div>
</section>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card-lift p-4">

        <?php if($success): ?>
            <div class="alert alert-success mb-3"><?= $success ?></div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-danger mb-3"><?= $error ?></div>
        <?php endif; ?>

        <form id="addNGOForm" method="POST" enctype="multipart/form-data" class="form-modern">
            
            <div class="mb-3">
              <label class="form-label">Nom de l'ONG</label>
              <input type="text" name="name" id="name" class="form-control">
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Pays</label>
                <input type="text" name="country" id="country" class="form-control">
              </div>

              <div class="col-md-6">
                <label class="form-label">Adresse email</label>
                <input type="text" name="address" id="address" class="form-control">
              </div>
            </div>

            <div class="mb-3 mt-3">
              <label class="form-label">Historique</label>
              <textarea name="history" id="history" rows="5" class="form-control"></textarea>
            </div>

            <div class="mb-4">
              <label class="form-label">Image</label>
              <input type="file" name="image" id="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-amber w-100 py-3">Ajouter</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
document.getElementById('addNGOForm').addEventListener('submit', function(e) {
    let errors = [];

    const name = document.getElementById('name').value.trim();
    const country = document.getElementById('country').value.trim();
    const address = document.getElementById('address').value.trim();
    const history = document.getElementById('history').value.trim();
    const image = document.getElementById('image').value.trim();

    if (name === "" || name.length < 2) errors.push("Le nom de l'ONG est requis (au moins 2 caractères).");
    if (country === "" || !/^[a-zA-Z\s]+$/.test(country)) errors.push("Le pays est requis et doit contenir uniquement des lettres.");
    const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    if (!emailPattern.test(address)) errors.push("Adresse email invalide.");
    if (history === "" || history.length < 10) errors.push("L'historique est requis (minimum 10 caractères).");
    if (image === "") errors.push("L'image est obligatoire.");
    else if (!/\.(jpg|jpeg|png|gif)$/i.test(image)) errors.push("L'image doit être .jpg, .jpeg, .png ou .gif.");

    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join("\n"));
    }
});
</script>
