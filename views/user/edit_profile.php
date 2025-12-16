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
    <title>Modifier mon profil - AidForPeace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ffb600;
            --primary-dark: #e5a400;
            --secondary: #1e3149;
            --secondary-light: #2a4365;
            --white: #ffffff;
            --light-bg: #f8fafc;
            --text-dark: #1e3149;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --success: #10b981;
            --error: #ef4444;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light-bg);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* Navbar */
        .modern-navbar {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            padding: 1rem 2rem;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            color: var(--white);
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand span {
            color: var(--primary);
        }

        .navbar-actions {
            display: flex;
            gap: 1rem;
        }

        .navbar-btn {
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-btn:not(.btn-logout) {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .navbar-btn:not(.btn-logout):hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-logout {
            background: var(--primary);
            color: var(--secondary);
            border: none;
        }

        .btn-logout:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Container principal */
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .profile-container h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .profile-container h1::after {
            content: '';
            flex: 1;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), transparent);
            border-radius: 3px;
            margin-left: 1rem;
        }

        /* Alertes */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #059669;
        }

        /* Formulaire */
        form {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"],
        .form-group input[type="file"],
        .form-group textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 182, 0, 0.15);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* Type de biographie */
        .bio-type-selector {
            background: var(--light-bg);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
        }

        .bio-type-selector h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        .bio-type-selector label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-right: 2rem;
            cursor: pointer;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-sm);
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .bio-type-selector label:hover {
            background: rgba(255, 182, 0, 0.1);
        }

        .bio-type-selector input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        /* Contrôles audio */
        .audio-controls {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        /* Boutons */
        .btn {
            padding: 0.875rem 1.5rem;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--secondary);
            box-shadow: 0 4px 15px rgba(255, 182, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 182, 0, 0.4);
        }

        .btn-secondary {
            background: var(--light-bg);
            color: var(--text-dark);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            border-color: var(--secondary);
            background: var(--white);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: var(--white);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: var(--white);
        }

        .btn-block {
            width: 100%;
            margin-top: 1rem;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Photo preview */
        .photo-preview {
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .photo-preview img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-container {
                flex-direction: column;
                gap: 1rem;
            }

            .navbar-actions {
                width: 100%;
                justify-content: center;
            }

            .profile-container {
                margin: 1rem auto;
            }

            .profile-container h1 {
                font-size: 1.5rem;
            }

            form {
                padding: 1.5rem;
            }

            .bio-type-selector label {
                display: flex;
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="modern-navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <span>Aid</span>ForPeace
            </a>
            <div class="navbar-actions">
                <a href="index.php?controller=user&action=profile" class="navbar-btn">
                    <i class="fas fa-arrow-left"></i> Retour au profil
                </a>
                <a href="index.php?controller=user&action=logout" class="navbar-btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>
    
    <div class="profile-container">
        <h1><i class="fas fa-user-edit"></i> Modifier mon profil</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?controller=user&action=editProfile" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="prenom"><i class="fas fa-user"></i> Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['Prenom'] ?? '') ?>" required placeholder="Votre prénom">
                </div>
                
                <div class="form-group">
                    <label for="nom"><i class="fas fa-user"></i> Nom *</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required placeholder="Votre nom">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required placeholder="votre@email.com">
            </div>
            
            <div class="form-group">
                <label for="birthdate"><i class="fas fa-calendar-alt"></i> Date de naissance *</label>
                <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($user['date_naissance'] ?? $user['birthdate'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="photo"><i class="fas fa-camera"></i> Photo de profil</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                <small><i class="fas fa-info-circle"></i> Formats acceptés: JPG, PNG, GIF (max 5MB)</small>
                
                <?php if (!empty($user['photo']) && $user['photo'] !== 'default.jpg'): ?>
                <div class="photo-preview">
                    <img src="uploads/avatars/<?= htmlspecialchars($user['photo']) ?>" alt="Photo actuelle">
                    <span>Photo actuelle</span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Type de biographie -->
            <div class="bio-type-selector">
                <h3><i class="fas fa-pen-fancy"></i> Type de biographie :</h3>
                <label>
                    <input type="radio" name="bio_type" value="text" <?= (!isset($user['bio_type']) || $user['bio_type'] === 'text') ? 'checked' : '' ?>>
                    <i class="fas fa-align-left"></i> Texte
                </label>
                <label>
                    <input type="radio" name="bio_type" value="audio" <?= (isset($user['bio_type']) && $user['bio_type'] === 'audio') ? 'checked' : '' ?>>
                    <i class="fas fa-microphone"></i> Message vocal
                </label>
            </div>
            
            <!-- Biographie texte -->
            <div id="text-bio-container" class="form-group">
                <label for="bio"><i class="fas fa-quote-left"></i> Biographie</label>
                <textarea id="bio" name="bio" rows="5" maxlength="500" placeholder="Parlez-nous de vous..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                <small><i class="fas fa-text-width"></i> Maximum 500 caractères</small>
            </div>
            
            <!-- Biographie audio -->
            <div id="audio-bio-container" class="form-group" style="display: none;">
                <label><i class="fas fa-microphone-alt"></i> Biographie (message vocal)</label>
                <div class="audio-controls">
                    <button type="button" id="record-btn" class="btn btn-danger">
                        <i class="fas fa-microphone"></i> Enregistrer
                    </button>
                    <button type="button" id="stop-btn" class="btn btn-secondary" disabled>
                        <i class="fas fa-stop"></i> Arrêter
                    </button>
                    <button type="button" id="play-btn" class="btn btn-success" disabled>
                        <i class="fas fa-play"></i> Écouter
                    </button>
                </div>
                <input type="file" id="bio_audio_file" name="bio_audio_file" accept="audio/*" style="margin-top: 1rem;">
                <small><i class="fas fa-info-circle"></i> Ou téléchargez un fichier audio existant</small>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </form>
    </div>
    
    <script>
        // Toggle bio type
        document.querySelectorAll('input[name="bio_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('text-bio-container').style.display = 
                    this.value === 'text' ? 'block' : 'none';
                document.getElementById('audio-bio-container').style.display = 
                    this.value === 'audio' ? 'block' : 'none';
            });
        });

        // Initialize based on current selection
        const currentBioType = document.querySelector('input[name="bio_type"]:checked');
        if (currentBioType && currentBioType.value === 'audio') {
            document.getElementById('text-bio-container').style.display = 'none';
            document.getElementById('audio-bio-container').style.display = 'block';
        }
    </script>
</body>
</html>
