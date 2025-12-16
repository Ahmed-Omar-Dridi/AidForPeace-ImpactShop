<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programme Fidélité - Admin AidForPeace</title>
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

        /* Alert */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid var(--gray-200);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .stat-card h3 {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .stat-card p {
            color: var(--gray-500);
            font-size: 0.9rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            color: var(--dark);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header h3 i { color: var(--accent); }

        .card-body { padding: 1.5rem; }

        /* Form */
        .bonus-form form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-group {
            flex: 1;
            min-width: 150px;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }

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
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
        }

        th {
            background: var(--gray-50);
            font-weight: 600;
            color: var(--dark);
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        td { color: var(--gray-500); }

        tr:hover { background: var(--gray-50); }

        /* Badges */
        .level-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .level-bronze { background: #cd7f32; color: white; }
        .level-silver { background: #c0c0c0; color: #333; }
        .level-gold { background: #ffd700; color: #333; }
        .level-platinum { background: linear-gradient(135deg, #e5e4e2, #a8a8a8); color: #333; }

        .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray-500);
        }

        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
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
                <a href="index.php?controller=loyalty&action=adminIndex" class="active"><i class="fas fa-coins"></i> Programme Fidélité</a>
                <a href="index.php"><i class="fas fa-home"></i> Retour au site</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-coins"></i> Programme Fidélité</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-coins"></i>
                    <h3><?= number_format($stats['total_earned'] ?? 0) ?></h3>
                    <p>Points distribués</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-exchange-alt"></i>
                    <h3><?= number_format($stats['total_redeemed'] ?? 0) ?></h3>
                    <p>Points échangés</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-wallet"></i>
                    <h3><?= number_format($stats['in_circulation'] ?? 0) ?></h3>
                    <p>Points en circulation</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3><?= $stats['total_members'] ?? 0 ?></h3>
                    <p>Membres fidélité</p>
                </div>
            </div>

            <!-- Add Bonus Form -->
            <div class="card bonus-form">
                <div class="card-header">
                    <h3><i class="fas fa-gift"></i> Ajouter des Points Bonus</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?controller=loyalty&action=addBonus">
                        <div class="form-group">
                            <label>Client</label>
                            <select name="customer_id" required>
                                <option value="">-- Sélectionner --</option>
                                <?php if (!empty($topMembers)): ?>
                                    <?php foreach ($topMembers as $member): ?>
                                        <option value="<?= $member['id'] ?>">
                                            <?= htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')) ?> (<?= $member['balance'] ?? 0 ?> pts)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Points</label>
                            <input type="number" name="points" min="1" required placeholder="100">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" placeholder="Points bonus" value="Points bonus">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                    </form>
                </div>
            </div>

            <!-- Top Members -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-trophy"></i> Top Membres Fidélité</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Client</th>
                            <th>Email</th>
                            <th>Points Gagnés</th>
                            <th>Solde</th>
                            <th>Niveau</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topMembers)): ?>
                            <tr><td colspan="6" class="empty-state">Aucun membre fidélité</td></tr>
                        <?php else: ?>
                            <?php $rank = 1; foreach ($topMembers as $member): ?>
                                <?php
                                $totalEarned = $member['total_earned'] ?? 0;
                                $level = 'bronze'; $levelName = '🥉 Bronze';
                                if ($totalEarned >= 5000) { $level = 'platinum'; $levelName = '💎 Platine'; }
                                elseif ($totalEarned >= 1500) { $level = 'gold'; $levelName = '🥇 Or'; }
                                elseif ($totalEarned >= 500) { $level = 'silver'; $levelName = '🥈 Argent'; }
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($rank <= 3): ?>
                                            <span style="font-size: 1.5rem;"><?= $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : '🥉') ?></span>
                                        <?php else: ?>
                                            #<?= $rank ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')) ?></strong></td>
                                    <td><?= htmlspecialchars($member['email'] ?? '') ?></td>
                                    <td><strong style="color: var(--success);"><?= number_format($totalEarned) ?></strong></td>
                                    <td><strong style="color: var(--accent);"><?= number_format($member['balance'] ?? 0) ?></strong></td>
                                    <td><span class="level-badge level-<?= $level ?>"><?= $levelName ?></span></td>
                                </tr>
                            <?php $rank++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Rewards -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-gift"></i> Récompenses Configurées</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Récompense</th>
                            <th>Points Requis</th>
                            <th>Type</th>
                            <th>Valeur</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rewards)): ?>
                            <tr><td colspan="5" class="empty-state">Aucune récompense configurée</td></tr>
                        <?php else: ?>
                            <?php foreach ($rewards as $reward): ?>
                                <tr>
                                    <td>
                                        <i class="fas <?= htmlspecialchars($reward['icon'] ?? 'fa-gift') ?>" style="color: var(--accent); margin-right: 10px;"></i>
                                        <strong><?= htmlspecialchars($reward['name']) ?></strong>
                                    </td>
                                    <td><strong><?= number_format($reward['points_required']) ?></strong> pts</td>
                                    <td><?= ucfirst(str_replace('_', ' ', $reward['reward_type'])) ?></td>
                                    <td><?= $reward['value'] ?><?= $reward['reward_type'] === 'discount_percent' ? '%' : ' TND' ?></td>
                                    <td>
                                        <?php if ($reward['is_active']): ?>
                                            <span class="badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
