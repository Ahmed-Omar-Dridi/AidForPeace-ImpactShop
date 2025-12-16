<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - AidForPeace</title>
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

        /* Animated Stars */
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
                radial-gradient(2px 2px at 130px 80px, rgba(255,255,255,0.3), transparent),
                radial-gradient(1px 1px at 160px 120px, rgba(255,255,255,0.4), transparent);
            background-size: 250px 250px;
            animation: twinkle 5s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        /* Animated Top Line */
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

        /* Header */
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

        .auth-header .logo h1 span {
            color: #ffd700;
        }

        .auth-header .logo i {
            color: #ffd700;
            font-size: 1.6rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Nav Links */
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
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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

        /* Main Container */
        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        /* Auth Card */
        .auth-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 45px;
            max-width: 480px;
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

        /* Test Account Info */
        .test-info {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 215, 0, 0.05));
            border: 2px solid rgba(255, 215, 0, 0.3);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 25px;
        }

        .test-info h4 {
            color: #07112b;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .test-info h4 i {
            color: #ffd700;
        }

        .test-info p {
            color: #333;
            font-size: 0.85rem;
            margin: 6px 0;
        }

        .test-info code {
            background: rgba(255, 215, 0, 0.2);
            padding: 3px 10px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #07112b;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 700;
            color: #07112b;
            font-size: 0.95rem;
        }

        .form-group label i {
            color: #ffd700;
            margin-right: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.4s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.15);
            background: white;
        }

        /* Buttons */
        .btn {
            padding: 16px 28px;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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

        .btn-secondary {
            background: linear-gradient(135deg, #00bcd4, #0097a7);
            color: white;
            box-shadow: 0 8px 25px rgba(0, 188, 212, 0.35);
        }

        .btn-secondary:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0, 188, 212, 0.45);
        }

        /* Facial Recognition Section */
        .facial-section {
            background: linear-gradient(135deg, rgba(0, 188, 212, 0.1), rgba(0, 188, 212, 0.05));
            border: 2px solid rgba(0, 188, 212, 0.3);
            border-radius: 16px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }

        .facial-section .divider {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
            color: #666;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .facial-section .divider::before,
        .facial-section .divider::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(0, 188, 212, 0.4), transparent);
        }

        .facial-section .divider span {
            padding: 0 18px;
        }

        .facial-section p {
            color: #0097a7;
            font-weight: 600;
            margin-bottom: 18px;
            font-size: 0.95rem;
        }

        #facial-login-container video {
            border-radius: 14px;
            border: 3px solid #00bcd4;
            box-shadow: 0 10px 30px rgba(0, 188, 212, 0.25);
        }

        #facial-login-status {
            margin-top: 15px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Auth Links */
        .auth-links {
            margin-top: 28px;
            text-align: center;
            padding-top: 22px;
            border-top: 2px solid #f0f0f0;
        }

        .auth-links a {
            color: #07112b;
            text-decoration: none;
            font-size: 14px;
            margin: 0 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .auth-links a i {
            color: #ffd700;
        }

        .auth-links a:hover {
            color: #ffd700;
        }

        /* Alerts */
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

        /* Footer */
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

        .auth-footer span {
            color: #ffd700;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-header {
                padding: 0 20px;
            }
            .auth-card {
                padding: 30px 22px;
                margin: 20px;
            }
            .auth-card h2 {
                font-size: 1.7rem;
            }
            .header-nav a span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="auth-header">
        <a href="index.php" class="logo">
            <i class="fas fa-hand-holding-heart"></i>
            <h1>Aid for <span>Peace</span></h1>
        </a>
        <nav class="header-nav">
            <a href="index.php">
                <i class="fas fa-home"></i> <span>Accueil</span>
            </a>
            <a href="index.php?controller=donation&action=ngos">
                <i class="fas fa-heart"></i> <span>Faire un don</span>
            </a>
            <a href="index.php?controller=user&action=register">
                <i class="fas fa-user-plus"></i> <span>Inscription</span>
            </a>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="auth-container">
        <div class="auth-card">
            <h2>🔐 Connexion</h2>
            <p class="subtitle">Accédez à votre compte AidForPeace</p>
            
            <!-- Test Account Info -->
            <div class="test-info">
                <h4><i class="fas fa-info-circle"></i> Compte Admin de test</h4>
                <p>Email: <code>admin@aidforpeace.com</code></p>
                <p>Mot de passe: <code>password</code></p>
            </div>
            
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
            
            <form method="POST" action="index.php?controller=user&action=login">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
            
            <!-- Facial Recognition -->
            <div class="facial-section">
                <div class="divider"><span>OU</span></div>
                <p><i class="fas fa-camera"></i> Connexion par Reconnaissance Faciale</p>
                <button type="button" id="facial-login-btn" class="btn btn-secondary">
                    <i class="fas fa-user-circle"></i> Activer la caméra
                </button>
                <div id="facial-login-container" style="display: none; margin-top: 15px;">
                    <video id="video-login" width="280" height="210" autoplay></video>
                    <p id="facial-login-status"></p>
                </div>
            </div>
            
            <div class="auth-links">
                <a href="index.php?controller=user&action=register"><i class="fas fa-user-plus"></i> Créer un compte</a>
                <a href="index.php?controller=user&action=forgot_password"><i class="fas fa-key"></i> Mot de passe oublié ?</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="auth-footer">
        <p>&copy; 2025 <span>AidForPeace</span> - Making a difference, one donation at a time.</p>
    </footer>

    <script>
        // Facial Recognition Login
        const facialLoginBtn = document.getElementById('facial-login-btn');
        const facialContainer = document.getElementById('facial-login-container');
        const videoLogin = document.getElementById('video-login');
        const statusLogin = document.getElementById('facial-login-status');
        
        facialLoginBtn.addEventListener('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                videoLogin.srcObject = stream;
                facialContainer.style.display = 'block';
                facialLoginBtn.style.display = 'none';
                statusLogin.textContent = '📹 Positionnez votre visage devant la caméra...';
                statusLogin.style.color = '#0097a7';
                
                setTimeout(() => {
                    const canvas = document.createElement('canvas');
                    canvas.width = 320;
                    canvas.height = 240;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(videoLogin, 0, 0, 320, 240);
                    const imageData = canvas.toDataURL('image/jpeg');
                    
                    stream.getTracks().forEach(track => track.stop());
                    statusLogin.textContent = '🔍 Vérification en cours...';
                    
                    fetch('index.php?controller=user&action=facialLogin', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ facial_data: imageData })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            statusLogin.textContent = '✅ Connexion réussie !';
                            statusLogin.style.color = '#4caf50';
                            setTimeout(() => {
                                window.location.href = 'index.php?controller=user&action=profile';
                            }, 1000);
                        } else {
                            statusLogin.textContent = '❌ Visage non reconnu. Utilisez le formulaire.';
                            statusLogin.style.color = '#f44336';
                            facialLoginBtn.style.display = 'inline-flex';
                            facialContainer.style.display = 'none';
                        }
                    })
                    .catch(err => {
                        statusLogin.textContent = '❌ Erreur de connexion';
                        statusLogin.style.color = '#f44336';
                        facialLoginBtn.style.display = 'inline-flex';
                    });
                }, 2000);
                
            } catch (err) {
                statusLogin.textContent = '❌ Impossible d\'accéder à la caméra';
                statusLogin.style.color = '#f44336';
            }
        });
    </script>
</body>
</html>
