<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un témoignage - AidForPeace</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <a href="?page=testimonials" class="back-btn">← Retour aux témoignages</a>
            <h1>✍️ Ajouter un témoignage</h1>
            <p>Partagez votre expérience inspirante avec la communauté</p>
        </header>

        <?php if (isset($success) && $success): ?>
            <div class="alert alert-success">
                ✅ Votre témoignage a été ajouté avec succès ! Il est maintenant en attente de modération.
            </div>
        <?php endif; ?>

        <form method="POST" action="?page=add-testimonial" class="testimonial-form" onsubmit="return validateTestimonialForm()">
            <div class="form-group">
                <label for="title">Titre du témoignage *</label>
                <input type="text" id="title" name="title" class="form-control" 
                       placeholder="Donnez un titre significatif à votre témoignage" required>
                <span class="error-message" id="title-error"></span>
            </div>

            <div class="form-group">
                <label for="author">Votre nom *</label>
                <input type="text" id="author" name="author" class="form-control" 
                       placeholder="Comment souhaitez-vous vous identifier ?" required>
                <span class="error-message" id="author-error"></span>
            </div>

            <div class="form-group">
                <label for="content">Votre témoignage *</label>
                <textarea id="content" name="content" class="form-control" 
                          placeholder="Racontez votre expérience, votre histoire, votre message d'espoir..." 
                          rows="8" required></textarea>
                <div class="char-count">
                    <span id="char-count">0</span> caractères
                </div>
                <span class="error-message" id="content-error"></span>
            </div>

            <button type="submit" class="btn btn-primary btn-large">📤 Publier mon témoignage</button>
        </form>
    </div>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>