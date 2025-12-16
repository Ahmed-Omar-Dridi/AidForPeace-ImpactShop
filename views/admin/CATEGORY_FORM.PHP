<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($category) ? 'Modifier' : 'Nouvelle'; ?> Categorie - Admin ImpactShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: #f5f5f5; }
        .navbar { background: #1e3149; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 1.5rem; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 600px; margin: 30px auto; padding: 0 20px; }
        .form-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #1e3149; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #ffb600; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input { width: auto; }
        .btn { padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; text-decoration: none; }
        .btn-primary { background: #ffb600; color: #1e3149; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn:hover { opacity: 0.9; }
        .form-actions { display: flex; gap: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1><i class="fas fa-folder"></i> <?php echo isset($category) ? 'Modifier' : 'Nouvelle'; ?> Categorie</h1>
        <div>
            <a href="index.php?controller=category&action=index"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-card">
            <form method="POST" action="index.php?controller=category&action=<?php echo isset($category) ? 'update' : 'store'; ?>">
                <?php if (isset($category)): ?>
                    <input type="hidden" name="id" value="<?php echo $category->getId(); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="name_fr">Nom (Francais) *</label>
                    <input type="text" id="name_fr" name="name_fr" required value="<?php echo isset($category) ? htmlspecialchars($category->getNameFr()) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="name_en">Nom (Anglais) *</label>
                    <input type="text" id="name_en" name="name_en" required value="<?php echo isset($category) ? htmlspecialchars($category->getNameEn()) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="description_fr">Description (Francais)</label>
                    <textarea id="description_fr" name="description_fr"><?php echo isset($category) ? htmlspecialchars($category->getDescriptionFr()) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="description_en">Description (Anglais)</label>
                    <textarea id="description_en" name="description_en"><?php echo isset($category) ? htmlspecialchars($category->getDescriptionEn()) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="parent_id">Categorie Parente</label>
                    <select id="parent_id" name="parent_id">
                        <option value="">-- Aucune (categorie principale) --</option>
                        <?php if (!empty($parentCategories)): ?>
                            <?php foreach ($parentCategories as $parent): ?>
                                <?php if (!isset($category) || $parent['id'] != $category->getId()): ?>
                                    <option value="<?php echo $parent['id']; ?>" <?php echo (isset($category) && $category->getParentId() == $parent['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($parent['name_fr']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="img_name">Nom de l'image</label>
                    <input type="text" id="img_name" name="img_name" value="<?php echo isset($category) ? htmlspecialchars($category->getImgName()) : ''; ?>">
                </div>

                <?php if (isset($category)): ?>
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" <?php echo $category->isActive() ? 'checked' : ''; ?>>
                        <label for="is_active">Categorie active</label>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="index.php?controller=category&action=index" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
