<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BackOffice - Commandes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-view">
        <header>
            <div>
                <h1>ImpactShop Backoffice</h1>
                <a href="index.php" class="back-btn">← Retour à l'Accueil</a>
            </div>
        </header>

        <div class="admin-container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="admin-header">
                <h2>Liste des Commandes</h2>
                <a href="index.php?controller=product&action=index" class="btn btn-secondary">
                    <i class="fas fa-box"></i> Gérer les Produits
                </a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['email']); ?></td>
                                <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                <td>
                                    <?php 
                                    $statusColors = [
                                        'pending' => '#f39c12',
                                        'paid' => '#27ae60',
                                        'cancelled' => '#e74c3c'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'En attente',
                                        'paid' => 'Payé',
                                        'cancelled' => 'Annulé'
                                    ];
                                    $color = $statusColors[$order['status']] ?? '#666';
                                    $label = $statusLabels[$order['status']] ?? $order['status'];
                                    ?>
                                    <span style="background: <?php echo $color; ?>; color: white; padding: 5px 10px; border-radius: 5px; font-size: 0.85rem; font-weight: 600;">
                                        <?php echo $label; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="index.php?controller=order&action=view&id=<?php echo $order['id']; ?>" 
                                       class="btn btn-primary" style="font-size: 0.9rem; padding: 8px 15px;">
                                        👁️ Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                Aucune commande pour le moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>