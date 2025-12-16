<?php
require_once __DIR__ . '/../../controllers/MessagerieController.php';
$controller = new MessagerieController();
$utilisateurs = $controller->getUtilisateurs();
$message_envoye = '';

if ($_POST && isset($_POST['js_valide'])) {
    $sender_id = $_POST['sender_id'] ?? '';
    $reciever_id = $_POST['reciever_id'] ?? '';
    $sujet = $_POST['sujet'] ?? '';
    $contenu = $_POST['contenu'] ?? '';

    if ($sender_id && $reciever_id && $sujet && $contenu) {
        $controller->ajouter($sender_id, $reciever_id, $sujet, $contenu);
        $message_envoye = "Message envoyé avec succès!";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoyer un message - AidForPeace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
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
        <div class="col-md-3 sidebar text-center">
            <h3 class="mb-1">AidForPeace</h3>
            <p class="opacity-75 mb-5">Messagerie Interne</p>
            <div class="nav flex-column">
                <a href="index.php?controller=messagerie&action=index" class="nav-link"><i class="fas fa-home"></i> Accueil</a>
                <a href="index.php?controller=messagerie&action=envoyermessage" class="nav-link active"><i class="fas fa-paper-plane"></i> Envoyer un message</a>
                <a href="index.php?controller=messagerie&action=mesmessages" class="nav-link"><i class="fas fa-inbox"></i> Mes messages</a>
                <a href="index.php?controller=messagerie&action=inbox" class="nav-link"><i class="fas fa-comments"></i> Conversations</a>
                <a href="index.php?controller=messagerie&action=chatbot" class="nav-link"><i class="fas fa-robot"></i> ChatBot</a>
            </div>
        </div>

        <div class="col-md-9 p-5">
            <h1 class="mb-5 text-primary">Envoyer un message</h1>

            <?php if ($message_envoye): ?>
                <div class="alert alert-success rounded-pill shadow-sm"><?= $message_envoye ?></div>
            <?php endif; ?>

            <div id="zone-erreur" class="erreur"></div>

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <form id="mon-formulaire" method="post">
                        <input type="hidden" name="js_valide" value="1">

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Expéditeur</label>
                                <select name="sender_id" class="form-select form-select-lg">
                                    <option value="">-- Choisir l'expéditeur --</option>
                                    <?php foreach($utilisateurs as $u): ?>
                                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Destinataire</label>
                                <select name="reciever_id" class="form-select form-select-lg">
                                    <option value="">-- Choisir le destinataire --</option>
                                    <?php foreach($utilisateurs as $u): ?>
                                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Sujet</label>
                                <input type="text" name="sujet" id="sujet" class="form-control form-control-lg" placeholder="Entrez le sujet (1 caractère minimum)">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Message</label>
                                <textarea name="contenu" id="contenu" rows="8" class="form-control form-control-lg" placeholder="Écrivez votre message (1 caractère minimum)"></textarea>
                            </div>

                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-custom btn-lg px-5">Envoyer le message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <br>
            <a href="index.php?controller=messagerie&action=index" class="btn btn-outline-primary">Retour</a>
        </div>
    </div>
</div>

<script>
document.getElementById('mon-formulaire').addEventListener('submit', function(e) {
    e.preventDefault();
    const erreurDiv = document.getElementById('zone-erreur');
    erreurDiv.style.display = 'none';
    erreurDiv.innerHTML = '';

    const sender = document.querySelector('[name="sender_id"]').value;
    const receiver = document.querySelector('[name="reciever_id"]').value;
    const sujet = document.getElementById('sujet').value.trim();
    const contenu = document.getElementById('contenu').value.trim();

    let erreurs = [];
    if (!sender) erreurs.push("Veuillez choisir un expéditeur !");
    if (!receiver) erreurs.push("Veuillez choisir un destinataire !");
    if (sujet === "") erreurs.push("Le sujet est obligatoire (minimum 1 caractère) !");
    if (contenu === "") erreurs.push("Le message ne peut pas être vide (minimum 1 caractère) !");

    if (erreurs.length > 0) {
        erreurDiv.style.display = 'block';
        erreurDiv.innerHTML = "<strong>Erreurs de saisie :</strong><br>• " + erreurs.join("<br>• ");
    } else {
        if (confirm("Envoyer ce message ?")) {
            this.submit();
        }
    }
});

document.getElementById('sujet').focus();
</script>
</body>
</html>
