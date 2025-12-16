<?php
/**
 * Vue: Liste des commandes (Admin)
 * Date: 2025-11-25
 * Author: dridi10331
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes - ImpactShop BackOffice</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS INLINE - Couleurs Constra forcées -->
    <style>
        :root {
            --constra-yellow: #ffb600;
            --constra-blue: #1e3149;
            --constra-blue-dark: #15202e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f9f9f9;
            color: #5a6570;
            line-height: 1.6;
        }
        
        /* Header - Bleu Constra */
        header {
            background: linear-gradient(135deg, var(--constra-blue) 0%, var(--constra-blue-dark) 100%);
            color: white;
            padding: 30px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        header h1 {
            font-size: 2rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        
        .back-btn {
            background: var(--constra-yellow);
            color: var(--constra-blue);
            padding: 12px 30px;
            border: 2px solid var(--constra-yellow);
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 3px;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: white;
            transform: translateY(-2px);
        }
        
        /* Container */
        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 30px;
        }
        
        /* Navigation Tabs */
        .nav-tabs {
            display: flex;
            gap: 0;
            margin-bottom: 40px;
            background: white;
            border-radius: 3px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .nav-item {
            flex: 1;
            padding: 20px 30px;
            text-align: center;
            text-decoration: none;
            color: #5a6570;
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            transition: all 0.3s;
            border-right: 1px solid #ebebeb;
        }
        
        .nav-item:last-child {
            border-right: none;
        }
        
        .nav-item:hover {
            background: #f9f9f9;
            color: var(--constra-blue);
        }
        
        .nav-item.active {
            background: var(--constra-blue);
            color: white;
        }
        
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #282f39;
            text-transform: uppercase;
        }
        
        /* Alerts */
        .alert {
            padding: 20px 25px;
            border-radius: 3px;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        
        /* Table */
        table {
            width: 100%;
            background: white;
            border-radius: 3px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-collapse: collapse;
        }
        
        thead {
            background: var(--constra-blue);
            color: white;
        }
        
        thead th {
            padding: 20px 15px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 14px;
            text-align: left;
        }
        
        tbody tr {
            border-bottom: 1px solid #ebebeb;
            transition: all 0.3s;
        }
        
        tbody tr:hover {
            background: #f9f9f9;
        }
        
        tbody td {
            padding: 20px 15px;
        }
        
        /* Badge */
        .badge {
            padding: 8px 15px;
            border-radius: 3px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-primary {
            background: var(--constra-yellow);
            color: var(--constra-blue);
        }
        
        /* Button */
        .btn-secondary {
            background: var(--constra-blue);
            color: white;
            border: 2px solid var(--constra-blue);
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 3px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background: var(--constra-yellow);
            border-color: var(--constra-yellow);
            color: var(--constra-blue);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 3px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid var(--constra-yellow);
        }
        
        .stat-card h4 {
            color: #5a6570;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .stat-card p {
            font-size: 2rem;
            font-weight: 900;
            color: #282f39;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .nav-tabs {
                flex-direction: column;
            }
            
            table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="header-content">
            <h1><i class="fas fa-shopping-cart"></i> GESTION DES COMMANDES</h1>
            <a href="index.php" class="back-btn">← Retour à l'Accueil</a>
        </div>
    </header>

    <!-- Container -->
    <div class="container">
        
        <!-- Admin Navigation Bar -->
        <?php include __DIR__ . '/admin_navbar.php'; ?>

        <!-- Alerts -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>Succès!</strong> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Erreur!</strong> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="page-header">
            <h2><i class="fas fa-list"></i> LISTE DES COMMANDES</h2>
            <div>
                <span class="badge badge-primary" style="font-size: 1.1rem; padding: 12px 25px;">
                    <i class="fas fa-shopping-bag"></i> <?php echo count($orders); ?> commande(s)
                </span>
            </div>
        </div>

        <!-- Orders Table -->
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> N° Commande</th>
                    <th><i class="fas fa-user"></i> Client</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th><i class="fas fa-dollar-sign"></i> Montant</th>
                    <th><i class="fas fa-info-circle"></i> Statut</th>
                    <th><i class="fas fa-calendar"></i> Date</th>
                    <th><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <strong style="font-size: 1.15rem; color: var(--constra-blue);">
                                    #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                </strong>
                            </td>
                            <td>
                                <strong style="color: #282f39;">
                                    <?php 
                                    $firstName = $order['first_name'] ?? 'Inconnu';
                                    $lastName = $order['last_name'] ?? '';
                                    echo htmlspecialchars($firstName . ' ' . $lastName); 
                                    ?>
                                </strong>
                            </td>
                            <td style="color: #5a6570;">
                                <i class="fas fa-at" style="color: #ffb600; margin-right: 5px;"></i>
                                <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <strong style="font-size: 1.2rem; color: var(--constra-yellow);">
                                    $<?php echo number_format($order['total_amount'], 2); ?>
                                </strong>
                            </td>
                            <td>
                                <?php
                                $statusConfig = [
                                    'pending' => ['class' => 'warning', 'label' => 'En attente', 'icon' => 'clock'],
                                    'paid' => ['class' => 'success', 'label' => 'Payé', 'icon' => 'check-circle'],
                                    'cancelled' => ['class' => 'danger', 'label' => 'Annulé', 'icon' => 'times-circle']
                                ];
                                
                                $config = $statusConfig[$order['status']] ?? ['class' => 'default', 'label' => $order['status'], 'icon' => 'info-circle'];
                                ?>
                                <span class="badge badge-<?php echo $config['class']; ?>" style="padding: 8px 15px; font-size: 13px;">
                                    <i class="fas fa-<?php echo $config['icon']; ?>"></i>
                                    <?php echo $config['label']; ?>
                                </span>
                            </td>
                            <td style="color: #5a6570;">
                                <i class="fas fa-calendar-alt" style="color: #ffb600; margin-right: 5px;"></i>
                                <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                                <br>
                                <small style="color: #999;">
                                    <i class="fas fa-clock" style="margin-right: 3px;"></i>
                                    <?php echo date('H:i', strtotime($order['created_at'])); ?>
                                </small>
                            </td>
                            <td>
                                <a href="index.php?controller=order&action=view&id=<?php echo $order['id']; ?>" 
                                   class="btn-secondary" 
                                   style="padding: 10px 20px; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-eye"></i> Voir Détails
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3 style="color: #999; font-size: 1.5rem; margin-bottom: 10px;">Aucune commande pour le moment</h3>
                                <p style="color: #bbb; font-size: 1rem;">Les commandes des clients apparaîtront ici</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($orders)): ?>
            <!-- Summary Stats -->
            <div class="stats-grid">
                
                <!-- Total Commandes -->
                <div class="stat-card" style="border-left-color: #ffb600;">
                    <h4>Total Commandes</h4>
                    <p><?php echo count($orders); ?></p>
                </div>

                <!-- Montant Total -->
                <div class="stat-card" style="border-left-color: #52c41a;">
                    <h4>Montant Total</h4>
                    <p style="color: #ffb600;">
                        $<?php 
                        $total = array_sum(array_column($orders, 'total_amount'));
                        echo number_format($total, 2); 
                        ?>
                    </p>
                </div>

                <!-- En Attente -->
                <div class="stat-card" style="border-left-color: #faad14;">
                    <h4>En Attente</h4>
                    <p>
                        <?php 
                        $pending = count(array_filter($orders, function($o) { return $o['status'] === 'pending'; }));
                        echo $pending;
                        ?>
                    </p>
                </div>

                <!-- Payées -->
                <div class="stat-card" style="border-left-color: #1e3149;">
                    <h4>Payées</h4>
                    <p>
                        <?php 
                        $paid = count(array_filter($orders, function($o) { return $o['status'] === 'paid'; }));
                        echo $paid;
                        ?>
                    </p>
                </div>

            </div>
        <?php endif; ?>

    </div>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>

</html>