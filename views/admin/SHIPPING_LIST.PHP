<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraisons - Admin ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="container">
        <!-- Admin Navigation Bar -->
        <?php include __DIR__ . '/admin_navbar.php'; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="header">
            <h2><?php echo count($shippings); ?> Livraisons</h2>
            <a href="index.php?controller=shipping&action=pending" class="btn btn-warning"><i class="fas fa-clock"></i> En Cours</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Commande</th>
                    <th>Client</th>
                    <th>Tracking</th>
                    <th>Statut</th>
                    <th>Ville</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($shippings)): ?>
                    <tr><td colspan="7" style="text-align:center;">Aucune livraison</td></tr>
                <?php else: ?>
                    <?php foreach ($shippings as $shipping): ?>
                        <tr>
                            <td>#<?php echo $shipping['id']; ?></td>
                            <td>#<?php echo str_pad($shipping['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars(($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '')); ?></td>
                            <td><code><?php echo htmlspecialchars($shipping['tracking_number']); ?></code></td>
                            <td>
                                <?php
                                $statusClass = 'secondary';
                                $statusLabel = $shipping['status'];
                                if ($shipping['status'] === 'processing') { $statusClass = 'warning'; $statusLabel = 'Preparation'; }
                                elseif ($shipping['status'] === 'shipped') { $statusClass = 'info'; $statusLabel = 'Expedie'; }
                                elseif ($shipping['status'] === 'in_transit') { $statusClass = 'primary'; $statusLabel = 'En Transit'; }
                                elseif ($shipping['status'] === 'delivered') { $statusClass = 'success'; $statusLabel = 'Livre'; }
                                ?>
                                <span class="badge badge-<?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($shipping['city']); ?></td>
                            <td class="actions">
                                <?php if ($shipping['status'] !== 'delivered'): ?>
                                    <form method="POST" action="index.php?controller=shipping&action=updateStatus" style="display:inline;">
                                        <input type="hidden" name="shipping_id" value="<?php echo $shipping['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="status-select">
                                            <option value="processing" <?php echo $shipping['status'] === 'processing' ? 'selected' : ''; ?>>Preparation</option>
                                            <option value="shipped" <?php echo $shipping['status'] === 'shipped' ? 'selected' : ''; ?>>Expedie</option>
                                            <option value="in_transit" <?php echo $shipping['status'] === 'in_transit' ? 'selected' : ''; ?>>En Transit</option>
                                            <option value="delivered" <?php echo $shipping['status'] === 'delivered' ? 'selected' : ''; ?>>Livre</option>
                                        </select>
                                    </form>
                                <?php else: ?>
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Livre</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>