<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/NGOController.php';

$controller = new NGOController();
$ngos = $controller->listNGOs();
$pageTitle = 'Liste des ONG';
$activePage = 'ngos';
$mainClass = 'pt-0';

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero py-4">
  <div class="container py-4">
    <div class="row align-items-center gy-3">
      <div class="col-lg-8">
        <div class="hero-badge mb-3">
          <i class="fas fa-globe-europe"></i><span>Communaut&eacute; d'ONG partenaires</span>
        </div>
        <h1 class="fw-bold display-6 mb-2">Choisissez une cause, nous assurons le reste</h1>
        <p class="text-white-50 mb-0">Trouvez l’ONG qui correspond à vos valeurs, explorez son histoire, ses missions et l’impact réel de ses actions, puis contribuez facilement grâce à un don en seulement quelques secondes.</p>
        
        
        <p class="hero-badge mb-3"> With AidForPeace, we create a better world</p>
    
      </div>

      <div class="col-lg-4 text-lg-end">
        <a class="btn btn-outline-light px-4 py-3" href="<?= BASE_URL ?>index.php?controller=admin&action=add_ngo"><i class="fas fa-plus-circle me-2"></i>Ajouter une ONG</a>
      </div>
    </div>
  </div>
</section>

<div class="container">
  <div class="search-container shadow-lg">
      <input id="searchInput" placeholder="Rechercher une ONG (nom, pays)...">
      <button id="searchBtn">Rechercher</button>
  </div>

  <div id="noResult" class="text-center fw-bold text-danger mt-3" style="display:none;">Aucune ONG trouvee</div>
</div>

<div class="ngo-cards" id="ngoCards">
    <?php foreach($ngos as $ngo): ?>
    <div class="card card-lift h-100">
        <img src="assets/images/<?= htmlspecialchars($ngo['image'] ?? 'image1.jpg') ?>" alt="<?= htmlspecialchars($ngo['name'] ?? '') ?>">
        <div class="card-content">
            <h2 class="fw-bold mb-1"><?= htmlspecialchars($ngo['name'] ?? '') ?></h2>
            <p class="mb-2 text-muted"><?= substr(htmlspecialchars($ngo['history'] ?? ''), 0, 120) ?><?= !empty($ngo['history']) ? '...' : '' ?></p>
            <div class="d-flex flex-wrap gap-2 mb-3">
              <span class="stat-pill"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($ngo['country'] ?? 'Non spécifié') ?></span>
            </div>
            <a href="<?= BASE_URL ?>index.php?controller=donation&action=ngo_history&id=<?= $ngo['id'] ?>" class="btn">Historique</a>
            <a href="<?= BASE_URL ?>index.php?controller=donation&action=donation_form&id=<?= $ngo['id'] ?>" class="btn">Faire un don</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
const ngoCards = Array.from(document.querySelectorAll('#ngoCards .card'));
const noResult = document.getElementById('noResult');

function searchNgo() {
    const q = searchInput.value.trim().toLowerCase();
    let found = false;
    ngoCards.forEach(card => {
        const name = card.querySelector('h2').textContent.toLowerCase();
        const country = card.querySelector('.stat-pill').textContent.toLowerCase();
        if(name.includes(q) || country.includes(q)) { card.style.display='block'; found = true; }
        else { card.style.display='none'; }
    });
    noResult.style.display = found ? 'none' : 'block';
}
searchBtn.addEventListener('click', searchNgo);
searchInput.addEventListener('keypress', e => { if(e.key === 'Enter') searchNgo(); });
</script>
<style>
/* Search Container - Enhanced */
.search-container {
    display: flex;
    max-width: 650px;
    margin: -30px auto 40px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 60px;
    overflow: hidden;
    position: relative;
    z-index: 10;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 215, 0, 0.3);
    animation: slideUp 0.8s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.search-container input {
    flex: 1;
    border: none;
    padding: 20px 30px;
    font-size: 1rem;
    outline: none;
    background: transparent;
    color: #07112b;
}

.search-container input::placeholder {
    color: #888;
}

.search-container button {
    background: linear-gradient(135deg, #ffd700, #ffb800);
    border: none;
    padding: 20px 40px;
    color: #07112b;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.search-container button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s ease;
}

.search-container button:hover {
    background: linear-gradient(135deg, #ffb800, #ff9500);
    box-shadow: 0 5px 20px rgba(255, 183, 0, 0.4);
}

.search-container button:hover::before {
    left: 100%;
}

/* NGO Cards Grid - Enhanced */
.ngo-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
    padding: 30px 40px 80px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Card Styles - Enhanced */
.ngo-cards .card {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    position: relative;
    animation: fadeInCard 0.6s ease-out both;
}

@keyframes fadeInCard {
    from {
        opacity: 0;
        transform: translateY(40px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.ngo-cards .card:nth-child(1) { animation-delay: 0.1s; }
.ngo-cards .card:nth-child(2) { animation-delay: 0.2s; }
.ngo-cards .card:nth-child(3) { animation-delay: 0.3s; }
.ngo-cards .card:nth-child(4) { animation-delay: 0.4s; }
.ngo-cards .card:nth-child(5) { animation-delay: 0.5s; }
.ngo-cards .card:nth-child(6) { animation-delay: 0.6s; }

.ngo-cards .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ffd700, #00bcd4, #ffd700);
    background-size: 200% 100%;
    animation: gradientMove 3s ease infinite;
    opacity: 0;
    transition: opacity 0.3s ease;
}

@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.ngo-cards .card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
}

.ngo-cards .card:hover::before {
    opacity: 1;
}

.ngo-cards .card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.ngo-cards .card:hover img {
    transform: scale(1.08);
}

.card-content {
    padding: 25px;
    position: relative;
    overflow: hidden;
}

.card-content::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.ngo-cards .card:hover .card-content::before {
    opacity: 1;
}

.card-content h2 {
    font-size: 1.3rem;
    color: #07112b;
    margin-bottom: 10px;
    font-weight: 700;
    position: relative;
}

.card-content p {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.6;
    position: relative;
}

/* Stat Pill - Enhanced */
.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, rgba(7, 17, 43, 0.08), rgba(7, 17, 43, 0.12));
    color: #07112b;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.stat-pill:hover {
    background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.3));
    transform: scale(1.05);
}

.stat-pill i {
    color: #ffd700;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

/* Buttons - Enhanced */
.card-content .btn {
    display: inline-block;
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    margin-right: 10px;
    margin-top: 10px;
    position: relative;
    overflow: hidden;
}

.card-content .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s ease;
}

.card-content .btn:hover::before {
    left: 100%;
}

.card-content .btn:first-of-type {
    background: transparent;
    border: 2px solid #07112b;
    color: #07112b;
}

.card-content .btn:first-of-type:hover {
    background: #07112b;
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(7, 17, 43, 0.3);
}

.card-content .btn:last-of-type {
    background: linear-gradient(135deg, #ffd700, #ffb800);
    border: none;
    color: #07112b;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.card-content .btn:last-of-type:hover {
    background: linear-gradient(135deg, #ffb800, #ff9500);
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(255, 183, 0, 0.4);
}

/* No Result Message */
#noResult {
    background: rgba(244, 67, 54, 0.1);
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #f44336;
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-5px); }
    40%, 80% { transform: translateX(5px); }
}

/* Responsive */
@media (max-width: 768px) {
    .ngo-cards {
        padding: 20px;
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .search-container {
        margin: -20px 20px 30px;
        flex-direction: column;
        border-radius: 20px;
    }
    
    .search-container input {
        padding: 18px 24px;
    }
    
    .search-container button {
        border-radius: 0 0 20px 20px;
        padding: 18px;
    }
    
    .card-content .btn {
        display: block;
        text-align: center;
        margin-right: 0;
    }
}
</style>
