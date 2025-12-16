<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiements - Admin ImpactShop</title>
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
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="header">
            <h2><?php echo count($payments); ?> Paiements</h2>
            <a href="index.php?controller=payment&action=pending" class="btn btn-warning"><i class="fas fa-clock"></i> En Attente</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Commande</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Methode</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr><td colspan="8" style="text-align:center;">Aucun paiement</td></tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td>#<?php echo $payment['id']; ?></td>
                            <td>#<?php echo str_pad($payment['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars(($payment['first_name'] ?? '') . ' ' . ($payment['last_name'] ?? '')); ?></td>
                            <td><strong><?php echo number_format($payment['amount'], 2); ?> TND</strong></td>
                            <td><?php echo ucfirst($payment['method']); ?></td>
                            <td>
                                <?php
                                $statusClass = 'secondary';
                                if ($payment['status'] === 'pending') $statusClass = 'warning';
                                elseif ($payment['status'] === 'completed') $statusClass = 'success';
                                elseif ($payment['status'] === 'failed') $statusClass = 'danger';
                                elseif ($payment['status'] === 'refunded') $statusClass = 'info';
                                ?>
                                <span class="badge badge-<?php echo $statusClass; ?>"><?php echo ucfirst($payment['status']); ?></span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($payment['created_at'])); ?></td>
                            <td class="actions">
                                <?php if ($payment['status'] === 'pending'): ?>
                                    <form method="POST" action="index.php?controller=payment&action=confirm" style="display:inline;">
                                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirmer ce paiement?');"><i class="fas fa-check"></i></button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($payment['status'] === 'completed'): ?>
                                    <form method="POST" action="index.php?controller=payment&action=refund" style="display:inline;">
                                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Rembourser ce paiement?');"><i class="fas fa-undo"></i></button>
                                    </form>
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
