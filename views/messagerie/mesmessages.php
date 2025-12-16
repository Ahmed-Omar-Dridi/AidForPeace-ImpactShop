<?php
require_once __DIR__ . '/../../controllers/MessagerieController.php';
$controller = new MessagerieController();
$utilisateurs = $controller->getUtilisateurs();
$user_id = isset($_GET['user']) ? (int)$_GET['user'] : 1;
$mes_messages = $controller->getMyMessages($user_id);
$current_user = $controller->getUserById($user_id);
$nom_utilisateur = $current_user['nom'] ?? 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes messages - AidForPeace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<!-- HEADER NOIR -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><img src="https://www.esprit.tn/images/logo.png" alt="ESPRIT" height="45"></a>
        <div class="ms-auto text-white text-end">
            <h5>Projet Web 2A - 2025-2026</h5>
            <small>Messagerie Interne AidForPeace</small>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR BLEU FONCÉ -->
        <div class="col-md-3 sidebar text-center">
            <h3 class="mb-1">AidForPeace</h3>
            <p class="opacity-75 mb-5">Messagerie Interne</p>
            <div class="nav flex-column">
                <a href="index.php?controller=messagerie&action=index" class="nav-link"><i class="fas fa-home"></i> Accueil</a>
                <a href="index.php?controller=messagerie&action=envoyermessage" class="nav-link"><i class="fas fa-paper-plane"></i> Envoyer un message</a>
                <a href="index.php?controller=messagerie&action=mesmessages" class="nav-link active"><i class="fas fa-inbox"></i> Mes messages</a>
                <a href="index.php?controller=messagerie&action=inbox" class="nav-link"><i class="fas fa-comments"></i> Conversations</a>
                <a href="index.php?controller=messagerie&action=chatbot" class="nav-link"><i class="fas fa-robot"></i> ChatBot</a>
            </div>
        </div>

        <!-- CONTENU PRINCIPAL -->
        <div class="col-md-9 p-5">
            <h1 class="mb-5 text-primary">Boîte de réception de <?= htmlspecialchars($nom_utilisateur) ?></h1>

            <!-- Sélecteur d'utilisateur -->
            <div class="card shadow-sm mb-5 p-4 rounded-4">
                <label class="form-label fw-bold fs-5">Consulter la boîte de réception de :</label>
                <select class="form-select form-select-lg w-50" onchange="location.href='index.php?controller=messagerie&action=mesmessages&user='+this.value">
                    <?php foreach($utilisateurs as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $u['id'] == $user_id ? 'selected' : '' ?>><?= htmlspecialchars($u['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (empty($mes_messages)): ?>
                <div class="text-center p-5 bg-light rounded-4 shadow-sm">
                    <i class="fas fa-envelope-open-text fa-4x text-muted mb-3"></i>
                    <p class="fs-4 text-muted">Aucun message reçu pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($mes_messages as $m): ?>
                    <div class="card shadow-sm mb-4 rounded-4 msg-card">
                        <div class="card-body">
                            <h3 class="text-primary"><?= htmlspecialchars($m->getSujet()) ?></h3>
                            <p><strong>De :</strong> <?= htmlspecialchars($m->getSenderNom()) ?></p>
                            <p class="text-muted"><small>Reçu le <?= $m->getDateEnvoie() ?></small></p>
                            <hr>
                            <p><?= nl2br(htmlspecialchars($m->getContenu())) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="mt-4">
                <a href="index.php?controller=messagerie&action=index" class="btn btn-outline-primary btn-lg">Retour à l'accueil</a>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('select').focus();
</script>
</body>
</html>
