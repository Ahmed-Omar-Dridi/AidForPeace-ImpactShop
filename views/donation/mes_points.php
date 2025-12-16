<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/DonationController.php';

$donationController = new DonationController();

$donor_email = $_POST['donor_email'] ?? $_GET['email'] ?? null;
if (!$donor_email) {
    header('Location: index.php?controller=donation&action=ngos&error=email_required');
    exit;
}

// Nombre de dons
$points = $donationController->getDonationsCountByEmail($donor_email);

// Définir les niveaux et les récompenses
$levels = [
    1 => "🎁 + 5$ cadeau",
    5 => "🎁 + 10$ cadeau",
    10 => "🎁 + 20$ cadeau",
    20 => "🎁 + 30$ cadeau",
    100 => "⭐ Devenir membre"
];

// Calcul pour la barre de progression
$maxPoints = max(array_keys($levels));
$percentage = min(100, ($points / $maxPoints) * 100);

// Déterminer niveau atteint
$currentLevel = 0;
foreach ($levels as $levelPoint => $reward) {
    if ($points >= $levelPoint) $currentLevel = $levelPoint;
}

$pageTitle = 'Mes points | Plateforme de Dons';
$mainClass = 'pt-0';
$activePage = 'points';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Bonjour, <?= htmlspecialchars($donor_email) ?></h2>
    <p>Nombre de dons effectués : <strong><?= $points ?></strong></p>

    <!-- Barre de progression -->
    <div class="progress" style="height: 35px; border-radius:20px; overflow:hidden;">
        <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%; background: linear-gradient(90deg, #667eea, #764ba2);" aria-valuenow="<?= $points ?>" aria-valuemin="0" aria-valuemax="<?= $maxPoints ?>">
            <?= $points ?> / <?= $maxPoints ?> points
        </div>
    </div>

    <!-- Afficher le niveau actuel et la surprise -->
    <div class="mt-4 p-3 border rounded bg-light">
        <h4>Niveau atteint : <?= $currentLevel ?> <?= $levels[$currentLevel] ?? "" ?></h4>
        <?php
        $nextLevel = null;
        foreach ($levels as $levelPoint => $reward) {
            if ($points < $levelPoint) {
                $nextLevel = $levelPoint;
                break;
            }
        }
        if ($nextLevel) {
            echo "<p>Prochain niveau : $nextLevel points → {$levels[$nextLevel]}</p>";
        } else {
            echo "<p>Vous avez atteint le niveau maximum ! 🎉</p>";
        }
        ?>
    </div>

    <!-- Objectif Donateur Étoile -->
    <div class="alert alert-info p-3 mb-4 rounded">
        <h5 class="mb-2">🎯 Objectif Donateur Étoile</h5>
        <p>
            Chaque don vous rapproche du prochain niveau ! Atteignez les différents paliers pour débloquer des récompenses exclusives.
            Faites des dons régulièrement pour devenir un <strong>Donateur Étoile ⭐</strong> et recevoir votre récompense ultime !
        </p>
    </div>

    <a href="list_ngos.php" class="btn btn-amber mt-4">Faire un nouveau don</a>
</div>

<!-- Animation cadeau grande mais pas plein écran -->
<div id="giftAnimation" style="display:none; position:fixed; top:5%; left:5%; width:90vw; height:90vh; z-index:9999;">
    <img src="../../assets/images/0.png" alt="Cadeau !" 
         style="width:100%; height:100%; object-fit:contain; animation: bounceGift 1s ease infinite;">
</div>

<style>
@keyframes bounceGift {
    0%, 100% { transform: translateY(0) scale(1); }
    25% { transform: translateY(-30px) scale(1.05); }
    50% { transform: translateY(-60px) scale(1.1); }
    75% { transform: translateY(-30px) scale(1.05); }
}
</style>



<script>
window.addEventListener('DOMContentLoaded', (event) => {
    const currentLevel = <?= $currentLevel ?>;
    const points = <?= $points ?>;

    // Liste des niveaux avec cadeau
    const levelsWithGift = [1,5, 10, 20, 100];

    // Si le niveau actuel est un palier de cadeau et qu'il vient juste d'être atteint
    if(levelsWithGift.includes(currentLevel) && points === currentLevel) {
        const gift = document.getElementById('giftAnimation');
        gift.style.display = 'block';
        
        // Masquer après 3 secondes
        setTimeout(() => {
            gift.style.display = 'none';
        }, 3000);
    }
});
</script>
<a href="receipt.php?donation_id=90" 
   style="
        display:inline-block;
        padding:12px 20px;
        background:#f59e0b;
        color:white;
        font-weight:600;
        border-radius:8px;
        text-decoration:none;
        margin-bottom:20px;
        font-size:16px;
   ">
    ⬅ Retour
</a>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
