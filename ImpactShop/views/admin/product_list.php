<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BackOffice - Liste des Produits</title>
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
                <h2>Liste des Produits</h2>
                <a href="index.php?controller=product&action=create" class="btn btn-primary">+ Ajouter un Produit</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom (EN)</th>
                        <th>Nom (FR)</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                <td>
                                    <img src="assets/images/<?php echo htmlspecialchars($product['img_name']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name_en']); ?>"
                                         onerror="this.src='assets/images/placeholder.jpg'">
                                </td>
                                <td><?php echo htmlspecialchars($product['name_en']); ?></td>
                                <td><?php echo htmlspecialchars($product['name_fr']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <a href="index.php?controller=product&action=edit&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-primary" style="font-size: 0.9rem; padding: 8px 15px;">
                                        ✏️ Modifier
                                    </a>
                                    <a href="index.php?controller=product&action=delete&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                        🗑️ Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                Aucun produit disponible. Commencez par en ajouter un !
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