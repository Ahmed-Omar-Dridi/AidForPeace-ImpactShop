<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/DonationController.php';
require_once __DIR__ . '/../../controllers/NGOController.php';

$donationController = new DonationController();
$ngoController = new NGOController();

$searchResults = [];
$searchTerm = '';
$searchCountry = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchTerm = trim($_POST['search'] ?? '');
    $searchCountry = trim($_POST['country'] ?? '');
    $searchResults = $donationController->searchDonations($searchTerm, $searchCountry);
}

$pageTitle = 'Recherche des dons';
$activePage = 'search';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container my-5">
    <h2>Rechercher des dons</h2>

    <form method="POST" class="mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Nom, Email ou ONG" value="<?= htmlspecialchars($searchTerm) ?>">
            </div>
            <div class="col-md-4">
                <input type="text" name="country" class="form-control" placeholder="Pays" value="<?= htmlspecialchars($searchCountry) ?>">
            </div>
            <div class="d-flex">
                <button type="submit" class="btn btn-warning" style="flex:1;">
    Rechercher
</button>


            </div>
        </div>
    </form>
<a href="http://localhost/projet_dons2/view/backoffice/dashboard.php" class="btn btn-warning">
    Retour au Dashboard
</a>

    <?php if($searchResults): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>ONG</th>
                    <th>Pays</th>
                    <th>Montant</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($searchResults as $donation): 
                    $ngo = $ngoController->getNgo($donation['ngo_id']);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($donation['donor_name']) ?></td>
                        <td><?= htmlspecialchars($donation['donor_email']) ?></td>
                        <td><?= $ngo ? htmlspecialchars($ngo['name']) : 'N/A' ?></td>
                        <td><?= htmlspecialchars($donation['country'] ?? '') ?></td>
                        <td><?= number_format($donation['amount'],2) ?> $</td>
                        <td><?= date('d/m/Y H:i', strtotime($donation['donation_date'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    <?php elseif($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>Aucun résultat trouvé pour votre recherche.</p>
    <?php endif; ?>
</div>
<style>
/* Arrière-plan animé jaune → bleu marine */
body {
    min-height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(-45deg, #FFD700, #1E3A8A, #FFD700, #1E3A8A); /* jaune et bleu marine */
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
}

/* Animation du gradient */
@keyframes gradientBG {
    0% {background-position: 0% 50%;}
    50% {background-position: 100% 50%;}
    100% {background-position: 0% 50%;}
}

/* Formulaire et tableau centré */
.container {
    background: rgba(0, 27, 69, 0.9);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    max-width: 900px;
    margin: 50px auto;
}
</style>
