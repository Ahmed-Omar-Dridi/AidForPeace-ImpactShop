<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ImpactShop BackOffice</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="admin-view">
        <header>
            <div>
                <h1><i class="fas fa-chart-line"></i> Dashboard ImpactShop</h1>
                <a href="index.php" class="back-btn">← Retour à l'Accueil</a>
            </div>
        </header>

        <div class="admin-container">
            
            <!-- Navigation du BackOffice -->
            <div class="admin-nav">
                <a href="index.php?controller=dashboard&action=index" class="nav-item active">
                    <i class="fas fa-chart-bar"></i> Dashboard
                </a>
                <a href="index.php?controller=product&action=index" class="nav-item">
                    <i class="fas fa-box"></i> Produits
                </a>
                <a href="index.php?controller=order&action=index" class="nav-item">
                    <i class="fas fa-shopping-cart"></i> Commandes
                </a>
            </div>

            <!-- Cartes de Statistiques -->
            <div class="stats-grid">
                <!-- Revenu Total -->
                <div class="stat-card revenue">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Revenu Total</h3>
                        <p class="stat-value">$<?php echo number_format($stats['total_revenue'], 2); ?></p>
                        <span class="stat-label">Toutes les commandes payées</span>
                    </div>
                </div>

                <!-- Revenu du Mois -->
                <div class="stat-card monthly">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Revenu du Mois</h3>
                        <p class="stat-value">$<?php echo number_format($stats['monthly_revenue'], 2); ?></p>
                        <span class="stat-label"><?php echo date('F Y'); ?></span>
                    </div>
                </div>

                <!-- Total Commandes -->
                <div class="stat-card orders">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Commandes</h3>
                        <p class="stat-value"><?php echo $stats['total_orders']; ?></p>
                        <span class="stat-label">Commandes passées</span>
                    </div>
                </div>

                <!-- Commandes en Attente -->
                <div class="stat-card pending">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>En Attente</h3>
                        <p class="stat-value"><?php echo $stats['pending_orders']; ?></p>
                        <span class="stat-label">À traiter</span>
                    </div>
                </div>

                <!-- Total Clients -->
                <div class="stat-card customers">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Clients</h3>
                        <p class="stat-value"><?php echo $stats['total_customers']; ?></p>
                        <span class="stat-label">Clients enregistrés</span>
                    </div>
                </div>

                <!-- Montant Moyen -->
                <div class="stat-card average">
                    <div class="stat-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Panier Moyen</h3>
                        <p class="stat-value">$<?php echo number_format($stats['average_order'], 2); ?></p>
                        <span class="stat-label">Par commande</span>
                    </div>
                </div>
            </div>

            <!-- Graphique des Revenus Mensuels -->
            <div class="dashboard-row">
                <div class="dashboard-card large">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Évolution des Revenus (12 derniers mois)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Produits les Plus Vendus & Dernières Commandes -->
            <div class="dashboard-row">
                <!-- Produits Top -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-star"></i> Produits les Plus Vendus</h3>
                    </div>
                    <div class="card-body">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Vendus</th>
                                    <th>Revenu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topProducts)): ?>
                                    <?php foreach ($topProducts as $product): ?>
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <img src="assets/images/<?php echo htmlspecialchars($product['img_name']); ?>" 
                                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;"
                                                         onerror="this.src='https://via.placeholder.com/40x40/2d6a4f/ffffff?text=P'">
                                                    <span><?php echo htmlspecialchars($product['name_fr']); ?></span>
                                                </div>
                                            </td>
                                            <td><strong><?php echo $product['total_sold']; ?></strong> unités</td>
                                            <td><strong style="color: var(--accent);">$<?php echo number_format($product['total_revenue'], 2); ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 20px;">Aucun produit vendu</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Dernières Commandes -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-list"></i> Dernières Commandes</h3>
                        <a href="index.php?controller=order&action=index" class="btn-sm">Voir tout</a>
                    </div>
                    <div class="card-body">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Client</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentOrders)): ?>
                                    <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                                        <tr>
                                            <td><strong>#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'paid' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'En attente',
                                                    'paid' => 'Payé',
                                                    'cancelled' => 'Annulé'
                                                ];
                                                $color = $statusColors[$order['status']] ?? 'default';
                                                $label = $statusLabels[$order['status']] ?? $order['status'];
                                                ?>
                                                <span class="badge badge-<?php echo $color; ?>">
                                                    <?php echo $label; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 20px;">Aucune commande</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Données pour le graphique
        const monthlyData = <?php echo json_encode(array_reverse($monthlyStats)); ?>;
        
        const labels = monthlyData.map(item => {
            const [year, month] = item.month.split('-');
            const date = new Date(year, month - 1);
            return date.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
        });
        
        const revenues = monthlyData.map(item => parseFloat(item.revenue));
        const orders = monthlyData.map(item => parseInt(item.order_count));

        // Configuration du graphique
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenus ($)',
                    data: revenues,
                    borderColor: '#f4a261',
                    backgroundColor: 'rgba(244, 162, 97, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#f4a261',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                return 'Revenus: $' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>