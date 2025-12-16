<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../controllers/ChatbotController.php';
$ctrl = new ChatbotController();
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (isset($_GET['user']) ? (int)$_GET['user'] : 1);
$action = 'chatbot';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatBot AidForPeace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        :root {
            --primary: #1e3149;
            --accent: #ffb600;
        }
        .chatbot-container { height: 80vh; display: flex; flex-direction: column; }
        .chatbot-messages { flex: 1; overflow-y: auto; padding: 20px; background: #f8fafc; }
        .message { margin-bottom: 15px; animation: slideIn 0.3s ease; }
        .message.user { text-align: right; }
        .message.bot { text-align: left; }
        .message-bubble { display: inline-block; max-width: 70%; padding: 12px 16px; border-radius: 18px; }
        .message.user .message-bubble { background: #1e3149; color: white; }
        .message.bot .message-bubble { background: white; color: #333; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #e5e5e5; }
        .message-time { font-size: 0.75rem; color: #666; margin-top: 5px; }
        .chatbot-input { background: white; padding: 15px 20px; border-top: 1px solid #dee2e6; }
        .typing-indicator { display: flex; gap: 4px; align-items: center; }
        .typing-dot { width: 8px; height: 8px; border-radius: 50%; background: #999; animation: typing 1.4s infinite; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing { 0%, 60%, 100% { opacity: 0.3; } 30% { opacity: 1; } }
        @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .suggested-actions { display: flex; gap: 10px; flex-wrap: wrap; margin: 10px 0; }
        .suggested-btn { padding: 8px 12px; background: #e8f4fd; border: 1px solid #1e3149; color: #1e3149; border-radius: 20px; cursor: pointer; font-size: 0.9rem; transition: 0.2s; }
        .suggested-btn:hover { background: #1e3149; color: white; }
        /* Sidebar fix */
        .sidebar { background: linear-gradient(180deg, #15202e, #1e3149); color: white; padding: 30px 20px; min-height: 100vh; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 15px; display: block; text-decoration: none; border-left: 3px solid transparent; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; border-left-color: #ffb600; background: rgba(255,255,255,0.1); }
        .sidebar .nav-link i { margin-right: 10px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <!-- CHATBOT -->
        <div class="col-md-9 p-0">
            <div class="chatbot-container">
                <!-- MESSAGES -->
                <div class="chatbot-messages" id="messagesContainer">
                    <div class="message bot">
                        <div class="message-bubble">
                            <strong>🤖 ChatBot Assistant</strong><br>
                            Bienvenue! 👋 Je suis votre assistant AidForPeace. Je peux vous aider avec des questions fréquemment posées.<br><br>
                            Comment puis-je vous aider?
                        </div>
                        <div class="message-time">Maintenant</div>
                    </div>
                    <div class="suggested-actions">
                        <button class="suggested-btn" onclick="sendMessage('Aide')">💡 Aide</button>
                        <button class="suggested-btn" onclick="sendMessage('Info')">📝 Info</button>
                        <button class="suggested-btn" onclick="sendMessage('Horaires')">⏰ Horaires</button>
                        <button class="suggested-btn" onclick="sendMessage('Guide')">📖 Guide</button>
                    </div>
                </div>

                <!-- INPUT -->
                <div class="chatbot-input">
                    <form id="chatForm" class="d-flex gap-2">
                        <input type="text" id="messageInput" placeholder="Posez votre question..." class="form-control" autocomplete="off" required>
                        <button type="submit" class="btn btn-custom btn-sm px-4"><i class="fas fa-paper-plane"></i> Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const messagesContainer = document.getElementById('messagesContainer');
const chatForm = document.getElementById('chatForm');
const messageInput = document.getElementById('messageInput');

function sendMessage(message) {
    if (!message) message = messageInput.value.trim();
    if (!message) return;

    const userMsg = document.createElement('div');
    userMsg.className = 'message user';
    userMsg.innerHTML = `<div class="message-bubble">${escapeHtml(message)}</div><div class="message-time">À l'instant</div>`;
    messagesContainer.appendChild(userMsg);

    const typing = document.createElement('div');
    typing.className = 'message bot';
    typing.id = 'typingIndicator';
    typing.innerHTML = `<div class="message-bubble"><div class="typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div></div>`;
    messagesContainer.appendChild(typing);
    scrollToBottom();

    fetch('index.php?controller=messagerie&action=get_bot_response', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'message=' + encodeURIComponent(message)
    }).then(response => response.json()).then(data => {
        typing.remove();
        if (data.success) {
            const botMsg = document.createElement('div');
            botMsg.className = 'message bot';
            botMsg.innerHTML = `<div class="message-bubble">${escapeHtml(data.response).replace(/\n/g, '<br>')}</div><div class="message-time">À l'instant</div>`;
            messagesContainer.appendChild(botMsg);
        }
        scrollToBottom();
    }).catch(error => {
        typing.remove();
        console.error('Erreur:', error);
    });

    messageInput.value = '';
    messageInput.focus();
}

function scrollToBottom() {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function escapeHtml(text) {
    const map = {'&': '&amp;','<': '&lt;','>': '&gt;','"': '&quot;',"'": '&#039;'};
    return text.replace(/[&<>"']/g, m => map[m]);
}

chatForm.addEventListener('submit', (e) => {
    e.preventDefault();
    sendMessage();
});

messageInput.focus();
</script>
</body>
</html>
