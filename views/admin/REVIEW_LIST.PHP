<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis - Admin ImpactShop</title>
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
            <h2><?php echo count($reviews); ?> Avis</h2>
            <a href="index.php?controller=review&action=pending" class="btn btn-warning"><i class="fas fa-clock"></i> A Moderer</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produit</th>
                    <th>Client</th>
                    <th>Note</th>
                    <th>Commentaire</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr><td colspan="7" style="text-align:center;">Aucun avis</td></tr>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td>#<?php echo $review['id']; ?></td>
                            <td><?php echo htmlspecialchars($review['product_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(($review['first_name'] ?? '') .  ' ' . ($review['last_name'] ?? '')); ?></td>
                            <td>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? '#ffb600' : '#ddd'; ?>;"></i>
                                <?php endfor; ?>
                            </td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($review['comment'] ?? ''); ?>
                            </td>
                            <td>
                                <?php if ($review['is_approved']): ?>
                                    <span class="badge badge-success">Approuve</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <?php if (!$review['is_approved']): ?>
                                    <form method="POST" action="index.php?controller=review&action=approve" style="display:inline;">
                                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="index.php?controller=review&action=reject" style="display:inline;">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet avis?');"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
