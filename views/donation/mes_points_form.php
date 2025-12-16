<?php
$pageTitle = "Mes points | Plateforme de Dons";
$mainClass = 'pt-0';
$activePage = 'points';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Consultez vos points</h2>
    <p>Entrez votre email pour voir votre progression et vos récompenses :</p>

    <form method="POST" action="mes_points.php" class="row g-3">
        <div class="col-md-6">
            <label for="donor_email" class="form-label">Email</label>
            <input type="email" id="donor_email" name="donor_email" class="form-control" placeholder="votre@email.com" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-amber mt-3">Voir mes points</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
