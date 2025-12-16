<?php
// Safety check - redirect if user data is not available
if (!isset($user) || $user === null) {
    header('Location: index.php?controller=user&action=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Aid for Peace</title>
    <link rel="stylesheet" href="../../assets/css/unified-theme.css">
    <link rel="stylesheet" href="../../assets/css/profile-modern.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="modern-navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">🏠 Aid for Peace</a>
            <div class="navbar-actions">
                <span class="navbar-user-info">
                    Bonjour, <?= htmlspecialchars($user['Prenom'] ?? 'Utilisateur') ?>
                </span>
                <a href="index.php" class="navbar-btn btn-home">🏠 Accueil</a>
                <a href="index.php?controller=user&action=logout" class="navbar-btn btn-logout">🚪 Déconnexion</a>
            </div>
        </div>
    </nav>
    
    <div class="profile-container">
        <h1>Mon Profil</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <!-- Informations utilisateur -->
        <div class="profile-info">
            <div class="profile-avatar">
                <img src="uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Avatar">
            </div>
            
            <div class="profile-details">
                <h2><?= htmlspecialchars($user['Prenom'] . ' ' . $user['nom']) ?></h2>
                <p class="email">📧 <?= htmlspecialchars($user['email']) ?></p>
                <p class="role">👤 <?= htmlspecialchars($user['role']) ?></p>
                <p class="points">⭐ <?= htmlspecialchars($user['points']) ?> points</p>
            </div>
        </div>
        
        <!-- Biographie -->
        <?php if (!empty($user['bio']) || !empty($user['bio_audio_path'])): ?>
        <div class="bio-section">
            <h3>📝 Ma Biographie</h3>
            <?php if ($user['bio_type'] === 'text'): ?>
                <p><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            <?php else: ?>
                <p><strong>Type:</strong> Message vocal</p>
                <?php if (!empty($user['bio_audio_path']) && file_exists($user['bio_audio_path'])): ?>
                    <audio controls>
                        <source src="<?= htmlspecialchars($user['bio_audio_path']) ?>" type="audio/webm">
                        Votre navigateur ne supporte pas l'élément audio.
                    </audio>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Actions -->
        <div class="profile-actions">
            <a href="index.php?controller=user&action=editProfile" class="btn btn-primary">✏️ Modifier mon profil</a>
            <a href="index.php?controller=features&action=dashboard" class="btn btn-secondary">📊 Tableau de bord</a>
        </div>
    </div>
</body>
</html>
