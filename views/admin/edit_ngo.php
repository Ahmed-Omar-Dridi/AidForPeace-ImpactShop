<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/NGOController.php';

$controller = new NGOController();

if(!isset($_GET['id'])) {
    header('Location: index.php?controller=admin&action=ngos');
    exit;
}
$id = intval($_GET['id']);
$ngo = $controller->getNgo($id);
if(!$ngo) {
    header('Location: index.php?controller=admin&action=ngos');
    exit;
}

$success = "";
$error = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $country = trim($_POST['country']);
    $address = trim($_POST['address']);
    $history = trim($_POST['history']);

    $imgName = $ngo['image'];
    if(!empty($_FILES['image']['name'])) {
        if(file_exists("../../assets/images/" . $ngo['image'])) unlink("../../assets/images/" . $ngo['image']);
        $imgName = time() . "_" . basename($_FILES['image']['name']);
        $targetPath = "../../assets/images/" . $imgName;
        if(!move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            $error = "Erreur lors de l'upload de l'image.";
        }
    }

    if(!$error) {
        $ngoObj = new Ngo(null, $name, $country, $address, $history, $imgName);
        $controller->updateNGO($ngoObj, $id);
        $success = "ONG modifiee avec succes !";
        $ngo = $controller->getNgo($id);
    }
}

$pageTitle = 'Modifier ONG';
$activePage = 'backoffice';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero py-4">
  <div class="container py-3">
    <div class="row align-items-center gy-2">
      <div class="col">
        <div class="hero-badge mb-3"><i class="fas fa-handshake"></i><span>Backoffice</span></div>
        <h1 class="fw-bold display-6 mb-1">Modifier ONG</h1>
        <p class="text-white-50 mb-0">Mettez a jour les informations de la structure et son visuel.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="index.php?controller=admin&action=manage_ngos" class="btn btn-outline-light px-4 py-3"><i class="fas fa-arrow-left me-2"></i>Retour</a>
      </div>
    </div>
  </div>
</section>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card-lift p-4">
        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST" enctype="multipart/form-data" class="form-modern">
            <div class="mb-3">
              <label class="form-label">Nom</label>
              <input type="text" name="name" value="<?= htmlspecialchars($ngo['name']) ?>" class="form-control" required>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Pays</label>
                <input type="text" name="country" value="<?= htmlspecialchars($ngo['country']) ?>" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Adresse email</label>
                <input type="text" name="address" value="<?= htmlspecialchars($ngo['address']) ?>" class="form-control" required>
              </div>
            </div>
            <div class="mt-3 mb-3">
              <label class="form-label">Historique</label>
              <textarea name="history" rows="5" class="form-control" required><?= htmlspecialchars($ngo['history']) ?></textarea>
            </div>
            <div class="mb-4">
              <label class="form-label">Image</label>
              <input type="file" name="image" class="form-control">
              <?php if(!empty($ngo['image'])): ?>
              <p class="mt-2 mb-0">Image actuelle :</p>
              <img src="../../assets/images/<?= $ngo['image'] ?>" width="120" class="rounded">
              <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-amber w-100 py-3">Mettre a jour</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
