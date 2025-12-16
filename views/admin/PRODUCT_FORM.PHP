<?php
// Déterminer si on est en mode édition
// Support both $product and $productData variable names
if (isset($product) && !isset($productData)) {
    $productData = $product;
}
$isEdit = isset($productData) && !empty($productData);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'Modifier' : 'Ajouter'; ?> un Produit</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-view">
        <header>
            <h1><i class="fas fa-box"></i> <?php echo $isEdit ? 'Modifier' : 'Ajouter'; ?> un Produit</h1>
            <a href="index.php?controller=product&action=index" class="back-btn">← Retour</a>
        </header>

        <div class="admin-container">
            <div class="product-form-container">
                <form action="index.php?controller=product&action=<?php echo $isEdit ? 'update' : 'store'; ?>" method="post">
                    
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?php echo $productData['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Nom (EN) *</label>
                        <input type="text" name="name_en" class="form-control" 
                               value="<?php echo $isEdit ? htmlspecialchars($productData['name_en']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nom (FR) *</label>
                        <input type="text" name="name_fr" class="form-control" 
                               value="<?php echo $isEdit ? htmlspecialchars($productData['name_fr']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Description (EN) *</label>
                        <textarea name="description_en" class="form-control" rows="4" required><?php echo $isEdit ? htmlspecialchars($productData['description_en']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Description (FR) *</label>
                        <textarea name="description_fr" class="form-control" rows="4" required><?php echo $isEdit ? htmlspecialchars($productData['description_fr']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Prix ($) *</label>
                        <input type="number" name="price" class="form-control" step="0.01" 
                               value="<?php echo $isEdit ? htmlspecialchars($productData['price']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nom Image *</label>
                        <input type="text" name="img_name" class="form-control" 
                               value="<?php echo $isEdit ? htmlspecialchars($productData['img_name']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Stock *</label>
                        <input type="number" name="stock" class="form-control" min="0"
                               value="<?php echo $isEdit ? htmlspecialchars($productData['stock'] ?? 0) : '0'; ?>" required>
                    </div>

                    <?php if (!empty($categories)): ?>
                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- Aucune catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo ($isEdit && isset($productData['category_id']) && $productData['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name_fr']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-actions">
                        <a href="index.php?controller=product&action=index" class="btn btn-cancel">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $isEdit ? 'Mettre à jour' : 'Créer'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>