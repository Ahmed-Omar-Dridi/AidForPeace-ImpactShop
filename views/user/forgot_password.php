<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Aid for Peace</title>
    <link rel="stylesheet" href="../../assets/css/unified-theme.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>🔑 Réinitialiser le mot de passe</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <p>Entrez votre email et votre nouveau mot de passe.</p>
            
            <form method="POST" action="index.php?controller=user&action=forgotPassword">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Nouveau mot de passe *</label>
                    <input type="password" id="password" name="password" required>
                    <small>Min 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial</small>
                </div>
                
                <div class="form-group">
                    <label for="password2">Confirmer le mot de passe *</label>
                    <input type="password" id="password2" name="password2" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Réinitialiser</button>
            </form>
            
            <div class="auth-links">
                <a href="index.php?controller=user&action=login">← Retour à la connexion</a>
            </div>
        </div>
    </div>
</body>
</html>
