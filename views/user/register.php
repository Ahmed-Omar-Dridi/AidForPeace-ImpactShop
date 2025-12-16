<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - AidForPeace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(-45deg, #07112b, #1a3a5c, #07102b, #0d2240);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            flex-direction: column;
            color: #fff;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,0.4), transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.3), transparent),
                radial-gradient(1px 1px at 90px 40px, rgba(255,255,255,0.5), transparent),
                radial-gradient(2px 2px at 130px 80px, rgba(255,255,255,0.3), transparent);
            background-size: 250px 250px;
            animation: twinkle 5s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ffd700, #00bcd4, #ffd700);
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
            z-index: 9999;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .auth-header {
            background: rgba(7, 17, 43, 0.95);
            backdrop-filter: blur(20px);
            padding: 0 40px;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
            position: relative;
            z-index: 100;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-100%); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .auth-header .logo h1 {
            color: white;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .auth-header .logo h1 span { color: #ffd700; }

        .auth-header .logo i {
            color: #ffd700;
            font-size: 1.6rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-nav a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .header-nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ffd700, #ffb800);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.4s ease;
            z-index: -1;
        }

        .header-nav a:hover {
            color: #07112b;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        }

        .header-nav a:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }

        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 45px;
            max-width: 550px;
            width: 100%;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
            position: relative;
            animation: fadeInUp 0.8s ease-out;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ffd700, #00bcd4, #ffd700);
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-card h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #07112b;
            font-size: 2rem;
            font-weight: 800;
        }

        .auth-card .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #07112b;
            font-size: 0.9rem;
        }

        .form-group label i {
            color: #ffd700;
            margin-right: 8px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.4s ease;
            background: #f8f9fa;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.15);
            background: white;
        }

        .btn {
            padding: 16px 28px;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ffd700, #ffb800);
            color: #07112b;
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.35);
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(255, 215, 0, 0.45);
        }

        .facial-section {
            background: linear-gradient(135deg, rgba(0, 188, 212, 0.1), rgba(0, 188, 212, 0.05));
            border: 2px solid rgba(0, 188, 212, 0.3);
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .facial-section p {
            color: #0097a7;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #00bcd4, #0097a7);
            color: white;
            box-shadow: 0 8px 25px rgba(0, 188, 212, 0.35);
        }

        .btn-secondary:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0, 188, 212, 0.45);
        }

        #facial-container video {
            border-radius: 14px;
            border: 3px solid #00bcd4;
            box-shadow: 0 10px 30px rgba(0, 188, 212, 0.25);
        }

        .auth-links {
            margin-top: 25px;
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .auth-links a {
            color: #07112b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .auth-links a i { color: #ffd700; }

        .auth-links a:hover { color: #ffd700; }

        .alert {
            padding: 16px 22px;
            border-radius: 14px;
            margin-bottom: 22px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border: 2px solid rgba(76, 175, 80, 0.3);
        }

        .alert-error {
            background: rgba(244, 67, 54, 0.1);
            color: #c62828;
            border: 2px solid rgba(244, 67, 54, 0.3);
        }

        .auth-footer {
            background: rgba(7, 17, 43, 0.95);
            backdrop-filter: blur(20px);
            padding: 25px;
            text-align: center;
            border-top: 1px solid rgba(255, 215, 0, 0.2);
            position: relative;
            z-index: 100;
        }

        .auth-footer p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .auth-footer span { color: #ffd700; }

        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .auth-card { padding: 30px 22px; margin: 20px; }
            .auth-card h2 { font-size: 1.7rem; }
            .auth-header { padding: 0 20px; }
        }
    </style>
</head>
<body>
    <header class="auth-header">
        <a href="index.php" class="logo">
            <i class="fas fa-hand-holding-heart"></i>
            <h1>Aid for <span>Peace</span></h1>
        </a>
        <nav class="header-nav">
            <a href="index.php"><i class="fas fa-home"></i> <span>Accueil</span></a>
            <a href="index.php?controller=user&action=login"><i class="fas fa-sign-in-alt"></i> <span>Connexion</span></a>
        </nav>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <h2>📝 Inscription</h2>
            <p class="subtitle">Rejoignez la communauté AidForPeace</p>
            
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
            
            <form method="POST" action="index.php?controller=user&action=register" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nom</label>
                        <input type="text" name="nom" placeholder="Votre nom" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Prénom</label>
                        <input type="text" name="prenom" placeholder="Votre prénom" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="votre@email.com" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Mot de passe</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirmer</label>
                        <input type="password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Photo de profil</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
                
                <div class="facial-section">
                    <p><i class="fas fa-camera"></i> Enregistrer votre visage (optionnel)</p>
                    <button type="button" id="facial-btn" class="btn btn-secondary">
                        <i class="fas fa-user-circle"></i> Capturer mon visage
                    </button>
                    <div id="facial-container" style="display: none; margin-top: 15px;">
                        <video id="video" width="280" height="210" autoplay></video>
                        <p id="facial-status" style="margin-top: 10px; font-weight: 600;"></p>
                    </div>
                    <input type="hidden" name="facial_data" id="facial_data">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Créer mon compte
                </button>
            </form>
            
            <div class="auth-links">
                <a href="index.php?controller=user&action=login">
                    <i class="fas fa-sign-in-alt"></i> Déjà un compte ? Se connecter
                </a>
            </div>
        </div>
    </div>

    <footer class="auth-footer">
        <p>&copy; 2025 <span>AidForPeace</span> - Making a difference, one donation at a time.</p>
    </footer>

    <script>
        const facialBtn = document.getElementById('facial-btn');
        const facialContainer = document.getElementById('facial-container');
        const video = document.getElementById('video');
        const facialStatus = document.getElementById('facial-status');
        const facialDataInput = document.getElementById('facial_data');
        
        facialBtn.addEventListener('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                facialContainer.style.display = 'block';
                facialBtn.textContent = '📸 Capturer';
                facialStatus.textContent = 'Positionnez votre visage...';
                facialStatus.style.color = '#0097a7';
                
                facialBtn.onclick = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = 320;
                    canvas.height = 240;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, 320, 240);
                    const imageData = canvas.toDataURL('image/jpeg');
                    facialDataInput.value = imageData;
                    
                    stream.getTracks().forEach(track => track.stop());
                    facialContainer.style.display = 'none';
                    facialBtn.innerHTML = '<i class="fas fa-check"></i> Visage capturé !';
                    facialBtn.style.background = 'linear-gradient(135deg, #4caf50, #2e7d32)';
                    facialBtn.disabled = true;
                };
            } catch (err) {
                facialStatus.textContent = '❌ Impossible d\'accéder à la caméra';
                facialStatus.style.color = '#f44336';
            }
        });
    </script>
</body>
</html>
