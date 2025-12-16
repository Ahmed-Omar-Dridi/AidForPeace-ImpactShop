<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';

// Load testimonials from database
require_once __DIR__ . '/../../config/config.php';

try {
    $pdo = config::getConnexion();
    $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $testimonials = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback & Témoignages - AidForPeace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/global-theme.css">
    <style>
        /* Page-specific styles */
        .navbar-right { display: flex; align-items: center; gap: 15px; }
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            padding: 12px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover { background: var(--primary-light); transform: translateY(-2px); }

        /* HERO HEADER */
        .hero-header {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            padding: 60px 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,182,0,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-header h1 {
            color: var(--white);
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .hero-header h1 i {
            color: var(--primary);
        }

        .hero-header p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        /* SUB NAV */
        .sub-nav {
            background: var(--white);
            padding: 20px 50px;
            display: flex;
            justify-content: center;
            gap: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            flex-wrap: wrap;
        }

        .sub-nav a {
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sub-nav a.active {
            background: var(--primary);
            color: var(--secondary);
        }

        .sub-nav a:not(.active) {
            background: var(--light-bg);
            color: var(--text-dark);
        }

        .sub-nav a:hover:not(.active) {
            background: rgba(255,182,0,0.2);
            color: var(--primary-dark);
        }

        /* MAIN CONTAINER */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* TESTIMONIALS GRID */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .testimonial-card {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border-left: 5px solid var(--primary);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }

        .testimonial-card h3 {
            color: var(--secondary);
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .testimonial-content {
            color: var(--text-muted);
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .testimonial-meta {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 1px solid var(--light-bg);
            border-bottom: 1px solid var(--light-bg);
            margin-bottom: 20px;
        }

        .testimonial-meta span {
            color: var(--text-muted);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .testimonial-meta .author {
            font-weight: 600;
            color: var(--secondary);
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,182,0,0.3);
        }

        .btn-like {
            background: var(--light-bg);
            color: var(--text-dark);
        }

        .btn-like:hover {
            background: rgba(16,185,129,0.2);
            color: var(--success);
        }

        .btn-share {
            background: var(--light-bg);
            color: var(--text-dark);
        }

        .btn-share:hover {
            background: rgba(30,49,73,0.1);
            color: var(--secondary);
        }

        /* EMPTY STATE */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 40px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: var(--secondary);
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        /* FOOTER */
        footer {
            background: var(--secondary);
            color: rgba(255,255,255,0.7);
            text-align: center;
            padding: 30px;
            margin-top: 50px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .navbar-menu { display: none; }
            .hero-header { padding: 40px 20px; }
            .hero-header h1 { font-size: 1.8rem; }
            .sub-nav { padding: 15px 20px; }
            .testimonials-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="navbar-logo">
            <i class="fas fa-hand-holding-heart"></i>
            <span><span class="aid">Aid</span> <span class="peace">ForPeace</span></span>
        </a>
        
        <ul class="navbar-menu">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="index.php?controller=user&action=index">Utilisateurs</a></li>
            <li><a href="index.php?controller=map&action=index">Map Mondiale</a></li>
            <li><a href="index.php?controller=messagerie&action=inbox&user=1">Messagerie</a></li>
            <li><a href="index.php?page=testimonials" class="active">Feedback</a></li>
            <li><a href="index.php?controller=product&action=shop">Boutique</a></li>
        </ul>
        
        <div class="navbar-right">
            <?php if ($is_logged_in): ?>
                <a href="index.php?controller=page&action=profile" class="btn-login">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($user_name) ?>
                </a>
            <?php else: ?>
                <a href="index.php?controller=user&action=login" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Connexion
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- HERO HEADER -->
    <div class="hero-header">
        <h1><i class="fas fa-comments"></i> Feedback & Témoignages</h1>
        <p>Partagez votre expérience et inspirez la communauté AidForPeace</p>
    </div>

    <!-- SUB NAV -->
    <div class="sub-nav">
        <a href="index.php?page=testimonials" class="active">
            <i class="fas fa-list"></i> Tous les témoignages
        </a>
        <a href="index.php?page=add-testimonial">
            <i class="fas fa-plus"></i> Ajouter un témoignage
        </a>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <a href="index.php?page=admin-testimonials">
            <i class="fas fa-cog"></i> Administration
        </a>
        <?php endif; ?>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-container">
        <div class="testimonials-grid">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="testimonial-card">
                        <h3><?= htmlspecialchars($testimonial['title']) ?></h3>
                        <p class="testimonial-content"><?= nl2br(htmlspecialchars(substr($testimonial['content'], 0, 200))) ?>...</p>
                        <div class="testimonial-meta">
                            <span class="author"><i class="fas fa-user"></i> <?= htmlspecialchars($testimonial['author']) ?></span>
                            <span class="date"><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($testimonial['created_at'])) ?></span>
                        </div>
                        <div class="actions">
                            <a href="index.php?page=testimonial-details&id=<?= $testimonial['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Voir plus
                            </a>
                            <button class="btn btn-like" onclick="likeTestimonial(<?= $testimonial['id'] ?>)">
                                <i class="fas fa-heart"></i> <?= $testimonial['likes'] ?? 0 ?>
                            </button>
                            <button class="btn btn-share" onclick="shareTestimonial(<?= $testimonial['id'] ?>)">
                                <i class="fas fa-share"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-comment-dots"></i>
                    <h3>Aucun témoignage pour le moment</h3>
                    <p>Soyez le premier à partager votre expérience inspirante avec la communauté !</p>
                    <a href="index.php?page=add-testimonial" class="btn btn-primary">
                        <i class="fas fa-pen"></i> Rédiger mon témoignage
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2025 AidForPeace. Tous droits réservés.</p>
    </footer>

    <script>
        function likeTestimonial(id) {
            // TODO: Implement like functionality
            alert('Merci pour votre like !');
        }

        function shareTestimonial(id) {
            const url = window.location.origin + window.location.pathname + '?page=testimonial-details&id=' + id;
            if (navigator.share) {
                navigator.share({ title: 'Témoignage AidForPeace', url: url });
            } else {
                navigator.clipboard.writeText(url);
                alert('Lien copié !');
            }
        }
    </script>
</body>
</html>
