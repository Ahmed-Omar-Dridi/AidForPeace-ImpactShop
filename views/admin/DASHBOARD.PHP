<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/DonationController.php';
require_once __DIR__ . '/../../controllers/NGOController.php';

$donationController = new DonationController();
$ngoController = new NGOController();

// Récupérer les stats
$donationsByCountry = $donationController->getDonationsCountByCountry();
$totalDonations = $donationController->getTotalDonations();
$totalNGOs = $ngoController->getTotalNGOs();

// Exemple de données pour les courbes (remplacer par des vraies stats si possible)
$donationsTrend = [100, 150, 200, 180, 220, 250, 300]; // Dons par jour/semaine
$ngosTrend = [5,6,6,7,7,8,9]; // Nombre d'ONG ajoutées
$countriesTrend = array_map(fn($d) => $d['total'], $donationsByCountry);

$pageTitle = 'Backoffice - Dashboard';
$activePage = 'backoffice';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="dashboard">

  <!-- Sidebar -->
  <aside class="sidebar">
    
    <nav class="sidebar-nav">
      <a href="index.php?controller=admin&action=dashboard" class="sidebar-nav-item <?php echo ($activePage == 'backoffice') ? 'active' : ''; ?>">
        <i class="fas fa-tachometer-alt"></i> Dashboard
      </a>
      <a href="index.php?controller=admin&action=manage_donations" class="sidebar-nav-item">
        <i class="fas fa-clipboard-check"></i> Gestion des dons
      </a>
      <a href="index.php?controller=admin&action=pending_donations" class="sidebar-nav-item">
        <i class="fas fa-inbox"></i> Dons reçus
      </a>
      <a href="index.php?controller=admin&action=manage_ngos" class="sidebar-nav-item">
        <i class="fas fa-handshake"></i> ONG
      </a>
      <a href="index.php" class="sidebar-nav-item">
        <i class="fas fa-home"></i> Accueil
      </a>
      <a href="index.php?controller=admin&action=search_donations" class="sidebar-nav-item">
  <i class="fas fa-search"></i> Rechercher
</a>

    </nav>
  </aside>

  <!-- Main Content -->
  <div class="main-wrapper" style="flex:1; display:flex; flex-direction:column;">

    <header class="header">
      <h1 class="page-title">Gestion des dons et partenaires</h1>
      <p class="text-muted">Pilotez les dons reçus, les statuts et les informations ONG depuis une interface unique.</p>
    </header>

    <!-- Statistiques modernes -->
    <div class="dashboard-container" style="display:flex; flex-wrap:wrap; gap:30px; padding:30px; justify-content:center;">

      <!-- Card 1 : Total Donations -->
      <div class="stat-card" style="background: linear-gradient(135deg,#6e48aa,#9d50bb); color:white;">
          <div class="stat-icon"><i class="fas fa-donate fa-2x"></i></div>
          <div class="stat-title">Total Donations</div>
          <div class="stat-value" id="totalDonations">0</div>
          <canvas id="donationsChart" style="width:100%; height:50px;"></canvas>
      </div>

      <!-- Card 2 : Total NGOs -->
      <div class="stat-card" style="background: linear-gradient(135deg,#42e695,#3bb2b8); color:white;">
          <div class="stat-icon"><i class="fas fa-hand-holding-heart fa-2x"></i></div>
          <div class="stat-title">Total NGOs</div>
          <div class="stat-value" id="totalNGOs">0</div>
          <canvas id="ngosChart" style="width:100%; height:50px;"></canvas>
      </div>

      <!-- Card 3 : Countries with Donations -->
      <div class="stat-card" style="background: linear-gradient(135deg,#f6d365,#fda085); color:white;">
          <div class="stat-icon"><i class="fas fa-globe fa-2x"></i></div>
          <div class="stat-title">Countries</div>
          <div class="stat-value" id="totalCountries">0</div>
          <canvas id="countriesChart" style="width:100%; height:50px;"></canvas>
      </div>
    </div>

    <!-- Gestion rapide -->
    <main class="main" style="padding:30px; display:flex; gap:20px; flex-wrap:wrap; justify-content:center;">
        <div class="stat-card">
          <div class="stat-card-title"><i class="fas fa-clipboard-check"></i> Dons</div>
          <div class="stat-card-value">Gestion des dons</div>
          <div class="stat-card-trend">Modifier ou supprimer les dons en cours.</div>
          <a href="index.php?controller=admin&action=manage_donations" class="btn-primary mt-3">Ouvrir</a>
        </div>

        <div class="stat-card">
          <div class="stat-card-title"><i class="fas fa-inbox"></i> Dons reçus</div>
          <div class="stat-card-value">Dons reçus</div>
          <div class="stat-card-trend">Valider ou refuser les dons en attente.</div>
          <a href="index.php?controller=admin&action=pending_donations" class="btn-primary mt-3">Afficher</a>
        </div>

        <div class="stat-card">
          <div class="stat-card-title"><i class="fas fa-handshake"></i> ONG</div>
          <div class="stat-card-value">Gérer les ONG</div>
          <div class="stat-card-trend">Ajouter ou mettre à jour les partenaires.</div>
          <a href="index.php?controller=admin&action=manage_ngos" class="btn-primary mt-3">Gérer</a>
        </div>
    </main>
  </div>
</div>

<style>
.stat-card {
    flex:1 1 200px;
    padding:20px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 6px 15px rgba(0,0,0,0.2);
}
.stat-icon { margin-bottom:10px; }
.stat-title { font-size:16px; font-weight:600; margin-bottom:5px; }
.stat-value { font-size:28px; font-weight:700; margin-bottom:5px; }
.stat-trend { font-size:12px; opacity:0.8; }
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Animation des chiffres
function animateValue(id, start, end, duration) {
    let obj = document.getElementById(id);
    let range = end - start;
    let startTime = null;

    function step(timestamp) {
        if(!startTime) startTime = timestamp;
        let progress = timestamp - startTime;
        let value = Math.min(Math.floor(start + (range*progress/duration)), end);
        obj.textContent = value.toLocaleString();
        if(progress < duration) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

animateValue("totalDonations", 0, <?php echo $totalDonations; ?>, 1500);
animateValue("totalNGOs", 0, <?php echo $totalNGOs; ?>, 1500);
animateValue("totalCountries", 0, <?php echo count($donationsByCountry); ?>, 1500);

// Courbes avec Chart.js
function createSparkline(id, data, color){
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type:'line',
        data:{ labels:data.map((_,i)=>i+1), datasets:[{ data:data, borderColor:color, borderWidth:2, fill:false, tension:0.4, pointRadius:0 }]},
        options:{ responsive:true, plugins:{ legend:{ display:false } }, scales:{ x:{ display:false }, y:{ display:false } } }
    });
}

// Remplacer par des données réelles si tu veux
createSparkline("donationsChart", <?php echo json_encode($donationsTrend); ?>, "#ffffff");
createSparkline("ngosChart", <?php echo json_encode($ngosTrend); ?>, "#ffffff");
createSparkline("countriesChart", <?php echo json_encode($countriesTrend); ?>, "#ffffff");

</script>


