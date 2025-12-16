<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($testimonial['title']) ?> - AidForPeace</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <a href="?page=testimonials" class="back-btn">← Retour aux témoignages</a>
            <h1>📝 Détails du témoignage</h1>
        </header>

        <div class="testimonial-detail">
            <div class="testimonial-header">
                <h2><?= htmlspecialchars($testimonial['title']) ?></h2>
                <div class="testimonial-meta">
                    <span class="author">👤 <?= htmlspecialchars($testimonial['author']) ?></span>
                    <span class="date">📅 <?= date('d/m/Y H:i', strtotime($testimonial['created_at'])) ?></span>
                </div>
            </div>
            
            <div class="testimonial-content">
                <?= nl2br(htmlspecialchars($testimonial['content'])) ?>
            </div>

            <div class="social-actions">
                <button class="btn btn-like" onclick="likeTestimonial(<?= $testimonial['id'] ?>)">
                    👍 J'aime (<?= $testimonial['likes'] ?>)
                </button>
                <button class="btn btn-share" onclick="shareTestimonial(<?= $testimonial['id'] ?>)">
                    🔗 Partager (<?= $testimonial['shares'] ?>)
                </button>
            </div>
        </div>

        <!-- Section Commentaires -->
        <div class="comments-section">
            <h3>💬 Commentaires (<?= count($comments) ?>)</h3>
            
            <!-- Formulaire d'ajout de commentaire -->
            <form method="POST" action="?page=add-comment" class="comment-form" onsubmit="return validateCommentForm()">
                <input type="hidden" name="testimonial_id" value="<?= $testimonial['id'] ?>">
                
                <div class="form-group">
                    <input type="text" name="author" placeholder="Votre nom" required class="form-control">
                </div>
                
                <div class="form-group">
                    <textarea name="content" placeholder="Votre commentaire..." required class="form-control"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">📤 Publier le commentaire</button>
            </form>

            <!-- Liste des commentaires -->
            <div class="comments-list">
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-card">
                            <div class="comment-header">
                                <strong><?= htmlspecialchars($comment['author']) ?></strong>
                                <span class="comment-date"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></span>
                            </div>
                            <p class="comment-content"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            <button class="btn btn-react" onclick="reactToComment(<?= $comment['id'] ?>)">
                                ❤️ Réagir (<?= $comment['reactions'] ?>)
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-comments">Aucun commentaire pour le moment. Soyez le premier à réagir !</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../../assets/js/validation.js"></script>
    <script src="../../assets/js/social-share.js"></script>
</body>
</html>