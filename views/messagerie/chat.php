<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../controllers/MessagerieController.php';
$ctrl = new MessagerieController();

// Utiliser l'ID de session si disponible
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (isset($_GET['user']) ? (int)$_GET['user'] : 1);
$conversation_id = isset($_GET['conv']) ? (int)$_GET['conv'] : null;

if (!$conversation_id) {
    header("Location: index.php?controller=messagerie&action=inbox&user=" . $user_id);
    exit;
}

$conversation = $ctrl->getConversationById($conversation_id, $user_id);
if (!$conversation) {
    header("Location: index.php?controller=messagerie&action=inbox&user=" . $user_id);
    exit;
}

$messages = $ctrl->getConversationMessages($conversation_id, $user_id);
$other_user_id = $conversation['participant1_id'] == $user_id ? $conversation['participant2_id'] : $conversation['participant1_id'];
$other_user = $ctrl->getUserById($other_user_id);

// Handle sending message
$send_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_message'])) {
        $content = trim($_POST['message'] ?? '');
        if (empty($content)) {
            $send_error = "Le message ne peut pas être vide";
        } else {
            if ($ctrl->sendMessage($conversation_id, $user_id, $other_user_id, $content)) {
                header("Location: index.php?controller=messagerie&action=chat&conv=" . $conversation_id . "&user=" . $user_id);
                exit();
            } else {
                $send_error = "Erreur lors de l'envoi du message";
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['msg_id'])) {
    $msg_id = (int)$_GET['msg_id'];
    $ctrl->deleteMessage($msg_id, $user_id);
    header("Location: index.php?controller=messagerie&action=chat&conv=" . $conversation_id . "&user=" . $user_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - AidForPeace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e3149;
            --primary-dark: #15202e;
            --accent: #ffb600;
        }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            min-height: 100vh;
            padding: 20px;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,182,0,0.2);
            color: var(--accent);
        }
        .sidebar .nav-link i { margin-right: 10px; width: 20px; }
        .chat-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chat-messages {
            height: 60vh;
            overflow-y: auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .message-bubble {
            max-width: 70%;
            margin-bottom: 15px;
            animation: slideIn 0.3s ease;
        }
        .message-bubble.sent { margin-left: auto; }
        .message-bubble.received { margin-right: auto; }
        .message-content {
            padding: 12px 16px;
            border-radius: 18px;
            word-wrap: break-word;
        }
        .message-bubble.sent .message-content {
            background: var(--accent);
            color: #1e3149;
        }
        .message-bubble.received .message-content {
            background: white;
            color: #333;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .message-time {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
            text-align: center;
        }
        .chat-input {
            background-color: white;
            border-top: 1px solid #dee2e6;
            padding: 15px 20px;
        }
        .message-delete {
            opacity: 0;
            transition: 0.2s;
        }
        .message-bubble:hover .message-delete { opacity: 1; }
        .btn-custom {
            background: var(--accent);
            color: var(--primary);
            font-weight: bold;
            border: none;
        }
        .btn-custom:hover {
            background: #e6a500;
            color: var(--primary);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php $action = 'inbox'; include __DIR__ . '/includes/sidebar.php'; ?>

        <!-- CONTENU PRINCIPAL -->
        <div class="col-md-9 d-flex flex-column p-0">
            <!-- HEADER CONVERSATION -->
            <div class="chat-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <a href="index.php?controller=messagerie&action=inbox&user=<?= $user_id ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-user-circle"></i> 
                            <?= htmlspecialchars($other_user['nom'] ?? 'Utilisateur') ?>
                        </h5>
                        <small class="opacity-75">Conversation active</small>
                    </div>
                </div>
            </div>

            <!-- MESSAGES -->
            <div class="chat-messages" id="messagesContainer">
                <?php if (empty($messages)): ?>
                    <div class="text-center mt-5 text-muted">
                        <i class="fas fa-comments fa-4x mb-3 opacity-50"></i>
                        <p class="fs-5">Aucun message pour le moment</p>
                        <small>Commencez la conversation !</small>
                    </div>
                <?php else: ?>
                    <?php foreach($messages as $msg): ?>
                        <div class="message-bubble <?= $msg['sender_id'] == $user_id ? 'sent' : 'received' ?> d-flex flex-column">
                            <div class="d-flex align-items-end gap-2 <?= $msg['sender_id'] == $user_id ? 'flex-row-reverse' : '' ?>">
                                <div class="message-content">
                                    <?= htmlspecialchars($msg['content']) ?>
                                </div>
                                <?php if ($msg['sender_id'] == $user_id): ?>
                                    <a href="index.php?controller=messagerie&action=chat&conv=<?= $conversation_id ?>&user=<?= $user_id ?>&delete=1&msg_id=<?= $msg['id'] ?>" 
                                       class="message-delete btn btn-sm btn-danger" 
                                       onclick="return confirm('Supprimer ce message ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="message-time">
                                <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- INPUT -->
            <div class="chat-input">
                <?php if ($send_error): ?>
                    <div class="alert alert-danger alert-sm mb-2"><?= $send_error ?></div>
                <?php endif; ?>
                <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="send_message" value="1">
                    <input type="text" name="message" placeholder="Écrivez un message..." 
                           class="form-control" autocomplete="off" required>
                    <button type="submit" class="btn btn-custom btn-sm px-4">
                        <i class="fas fa-paper-plane"></i> Envoyer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-scroll to bottom
const container = document.getElementById('messagesContainer');
if (container) {
    container.scrollTop = container.scrollHeight;
}
</script>
</body>
</html>
