<?php
$isEdit = isset($this->product->id) && !empty($this->product->id);
$formTitle = $isEdit ? "Modifier le Produit" : "Ajouter un Nouveau Produit";
$formAction = $isEdit ? "update" : "store";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $formTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-view">
        <header>
            <div>
                <h1>ImpactShop Backoffice</h1>
                <a href="index.php?controller=product&action=index" class="back-btn">← Retour à la Liste</a>
            </div>
        </header>

        <div class="admin-container">
            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div class="alert alert-error">
                    <h4>Erreurs de validation :</h4>
                    <ul>
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <div class="product-form-container">
                <h2><?php echo $formTitle; ?></h2>
                
                <form method="POST" action="index.php?controller=product&action=<?php echo $formAction; ?>" id="product-form">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($this->product->id); ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name-en">Nom du Produit (Anglais) *</label>
                        <input 
                            type="text" 
                            id="name-en" 
                            name="name_en" 
                            value="<?php echo $isEdit ? htmlspecialchars($this->product->name_en) : ''; ?>"
                            placeholder="Ex: Water Filter">
                        <small class="form-help">Minimum 3 caractères</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="name-fr">Nom du Produit (Français) *</label>
                        <input 
                            type="text" 
                            id="name-fr" 
                            name="name_fr" 
                            value="<?php echo $isEdit ? htmlspecialchars($this->product->name_fr) : ''; ?>"
                            placeholder="Ex: Filtre à Eau">
                        <small class="form-help">Minimum 3 caractères</small>
                    </div>

                    <div class="form-group">
                        <label for="price">Prix ($) *</label>
                        <input 
                            type="text" 
                            id="price" 
                            name="price" 
                            value="<?php echo $isEdit ? htmlspecialchars($this->product->price) : ''; ?>"
                            placeholder="Ex: 49.99">
                        <small class="form-help">Prix positif uniquement</small>
                    </div>

                    <div class="form-group">
                        <label for="img-name">Nom du Fichier Image *</label>
                        <input 
                            type="text" 
                            id="img-name" 
                            name="img_name" 
                            value="<?php echo $isEdit ? htmlspecialchars($this->product->img_name) : ''; ?>"
                            placeholder="Ex: water-filter.jpg">
                        <small class="form-help">Nom du fichier avec extension (jpg, png, etc.)</small>
                    </div>

                    <div class="form-group">
                        <label for="desc-en">Description (Anglais) *</label>
                        <textarea 
                            id="desc-en" 
                            name="description_en" 
                            rows="4"
                            placeholder="Detailed description in English..."><?php echo $isEdit ? htmlspecialchars($this->product->description_en) : ''; ?></textarea>
                        <small class="form-help">Minimum 10 caractères</small>
                    </div>

                    <div class="form-group">
                        <label for="desc-fr">Description (Français) *</label>
                        <textarea 
                            id="desc-fr" 
                            name="description_fr" 
                            rows="4"
                            placeholder="Description détaillée en français..."><?php echo $isEdit ? htmlspecialchars($this->product->description_fr) : ''; ?></textarea>
                        <small class="form-help">Minimum 10 caractères</small>
                    </div>

                    <div class="form-actions">
                        <a href="index.php?controller=product&action=index" class="btn btn-cancel">Annuler</a>
                        <button type="submit" class="btn btn-accent">
                            <?php echo $isEdit ? "Mettre à Jour" : "Créer le Produit"; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/validation.js"></script>
</body>
</html>