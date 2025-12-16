<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un commentaire - AidForPeace</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <h1>AidForPeace - Ajouter un commentaire</h1>
        <nav>
            <a href="index.php?action=testimonials">← Retour aux témoignages</a>
            <a href="index.php?action=show_testimonial&id=<?php echo $_GET['testimonial_id']; ?>">← Retour au témoignage</a>
        </nav>
    </header>

    <main class="container">
        <h2>Ajouter un commentaire</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="index.php?action=add_comment&testimonial_id=<?php echo $_GET['testimonial_id']; ?>" 
              method="POST" class="comment-form" onsubmit="return validateCommentForm()">
            
            <div class="form-group">
                <label for="name">Votre nom *</label>
                <input type="text" id="name" name="name" placeholder="Entrez votre nom" required>
                <span class="error-message" id="nameError"></span>
            </div>

            <div class="form-group">
                <label for="email">Votre email *</label>
                <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
                <span class="error-message" id="emailError"></span>
            </div>

            <div class="form-group">
                <label for="content">Votre commentaire *</label>
                <textarea id="content" name="content" rows="6" placeholder="Partagez votre avis..." required></textarea>
                <span class="error-message" id="contentError"></span>
            </div>

            <button type="submit">Publier le commentaire</button>
            <a href="index.php?action=show_testimonial&id=<?php echo $_GET['testimonial_id']; ?>" class="btn-cancel">Annuler</a>
        </form>
    </main>

    <script src="../../assets/js/validation.js"></script>
</body>
</html>