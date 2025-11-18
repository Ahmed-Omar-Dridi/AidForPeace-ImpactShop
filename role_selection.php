<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactShop - Sélection du Rôle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="role-container">
        <div class="role-box">
            <h1>Bienvenue sur ImpactShop</h1>
            <p>Veuillez sélectionner votre rôle pour continuer :</p>

            <div class="choice-grid">
                <a href="index.php?controller=product&action=shop" class="choice-card buyer-choice" style="text-decoration: none;">
                    <h2>🛒 Acheteur</h2>
                    <p>Parcourir et acheter des produits d'aide</p>
                </a>

                <a href="index.php?controller=dashboard&action=index" class="choice-card admin-choice" style="text-decoration: none;">
                    <h2>⚙️ Administrateur</h2>
                    <p>Gérer le catalogue et voir le dashboard</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>