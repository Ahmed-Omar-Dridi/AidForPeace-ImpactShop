<?php 
if (session_status() === PHP_SESSION_NONE) session_start();

// Load testimonials
require_once __DIR__ . '/../../config/config.php';
try {
    $pdo = config::getConnexion();
    $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $testimonials = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Témoignages - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --accent: #f59e0b;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --success: #10b981;
            --danger: #ef4444;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-500: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--dark) 0%, var(--dark-light) 100%);
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1.5rem;
        }

        .sidebar-header h2 {
            color: white;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-header h2 i { color: var(--accent); }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--accent);
        }

        .sidebar-nav a i { width: 20px; color: var(--accent); }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-header h1 i { color: var(--accent); }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #f87171 100%);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* Testimonials Grid */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .testimonial-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid var(--gray-200);
            border-left: 4px solid var(--accent);
            transition: all 0.3s;
        }

        .testimonial-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .testimonial-card h3 {
            color: var(--dark);
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }

        .testimonial-content {
            color: var(--gray-500);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .testimonial-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1rem 0;
            border-top: 1px solid var(--gray-200);
            border-bottom: 1px solid var(--gray-200);
            margin-bottom: 1rem;
        }

        .testimonial-meta span {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        .testimonial-meta .author { font-weight: 600; color: var(--dark); }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--accent);
        }

        .status-approved {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .card-actions .btn {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--gray-500);
        }

        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }

        @media (max-width: 768px) {
            .testimonials-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-cogs"></i> Admin Panel</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php?controller=admin&action=dashboard"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="index.php?controller=admin&action=pending_donations"><i class="fas fa-inbox"></i> Dons en attente</a>
                <a href="index.php?controller=admin&action=manage_donations"><i class="fas fa-hand-holding-usd"></i> Gérer les dons</a>
                <a href="index.php?controller=admin&action=manage_ngos"><i class="fas fa-building"></i> ONG Partenaires</a>
                <a href="index.php?page=admin-testimonials" class="active"><i class="fas fa-quote-left"></i> Témoignages</a>
                <a href="index.php?controller=loyalty&action=adminIndex"><i class="fas fa-coins"></i> Programme Fidélité</a>
                <a href="index.php"><i class="fas fa-home"></i> Retour au site</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-quote-left"></i> Gestion des Témoignages</h1>
                <a href="index.php?controller=admin&action=dashboard" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="testimonials-grid">
                <?php if (!empty($testimonials)): ?>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="testimonial-card">
                            <h3><?= htmlspecialchars($testimonial['title'] ?? 'Sans titre') ?></h3>
                            <p class="testimonial-content">
                                <?= nl2br(htmlspecialchars(substr($testimonial['content'] ?? '', 0, 200))) ?>...
                            </p>
                            <div class="testimonial-meta">
                                <span class="author">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($testimonial['author'] ?? 'Anonyme') ?>
                                </span>
                                <span>
                                    <i class="fas fa-calendar"></i> <?= isset($testimonial['created_at']) ? date('d/m/Y', strtotime($testimonial['created_at'])) : 'N/A' ?>
                                </span>
                                <span class="status-badge status-<?= $testimonial['status'] ?? 'pending' ?>">
                                    <?= ($testimonial['status'] ?? 'pending') === 'pending' ? '⏳ En attente' : '✅ Approuvé' ?>
                                </span>
                            </div>
                            <div class="card-actions">
                                <?php if (($testimonial['status'] ?? 'pending') === 'pending'): ?>
                                    <form method="POST" action="index.php?page=moderate" style="display: inline;">
                                        <input type="hidden" name="testimonial_id" value="<?= $testimonial['id'] ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Approuver
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <a href="index.php?page=testimonial-details&id=<?= $testimonial['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-comment-dots"></i>
                        <h3>Aucun témoignage</h3>
                        <p>Il n'y a pas encore de témoignages à modérer.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
