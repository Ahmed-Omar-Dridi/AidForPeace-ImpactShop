<!-- Chatbot Widget -->
<div id="chatbot-widget">
    <!-- Toggle Button -->
    <button id="chatbot-toggle" onclick="toggleChatbot()">
        <i class="fas fa-comments"></i>
        <span class="chat-badge" id="chatBadge" style="display: none;">1</span>
    </button>
    
    <!-- Chat Window -->
    <div id="chatbot-window">
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-avatar"><i class="fas fa-robot"></i></div>
                <div>
                    <h4>Assistant ImpactShop</h4>
                    <span class="status"><i class="fas fa-circle"></i> En ligne</span>
                </div>
            </div>
            <button class="close-chat" onclick="toggleChatbot()"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="chat-messages" id="chatMessages">
            <div class="message bot">
                <div class="message-content">
                    Bonjour! 👋 Je suis l'assistant ImpactShop. Comment puis-je vous aider?
                </div>
            </div>
            <div class="quick-replies" id="quickReplies">
                <button onclick="sendQuickReply('Comment passer commande?')">📦 Commander</button>
                <button onclick="sendQuickReply('Où est ma livraison?')">🚚 Suivi</button>
                <button onclick="sendQuickReply('Programme de fidélité')">🎁 Fidélité</button>
                <button onclick="sendQuickReply('Zones de livraison')">🗺️ Zones</button>
            </div>
        </div>
        
        <div class="chat-input">
            <input type="text" id="chatInput" placeholder="Tapez votre message..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<style>
#chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Montserrat', sans-serif;
}

#chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffb600, #ff9500);
    border: none;
    color: #1e3149;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 5px 25px rgba(255, 182, 0, 0.4);
    transition: all 0.3s;
    position: relative;
}

#chatbot-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 35px rgba(255, 182, 0, 0.5);
}

.chat-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #e74c3c;
    color: white;
    font-size: 0.75rem;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

#chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    height: 500px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 50px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

#chatbot-window.active {
    display: flex;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.chat-header {
    background: linear-gradient(135deg, #1e3149, #15202e);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-avatar {
    width: 40px;
    height: 40px;
    background: #ffb600;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1e3149;
    font-size: 1.2rem;
}

.chat-header h4 {
    margin: 0;
    font-size: 1rem;
}

.chat-header .status {
    font-size: 0.75rem;
    opacity: 0.8;
}

.chat-header .status i {
    color: #27ae60;
    font-size: 0.5rem;
    margin-right: 5px;
}

.close-chat {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    opacity: 0.8;
}

.close-chat:hover {
    opacity: 1;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
}

.message {
    margin-bottom: 15px;
    display: flex;
}

.message.bot {
    justify-content: flex-start;
}

.message.user {
    justify-content: flex-end;
}

.message-content {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 0.9rem;
    line-height: 1.4;
}

.message.bot .message-content {
    background: white;
    color: #333;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.message.user .message-content {
    background: linear-gradient(135deg, #ffb600, #ff9500);
    color: #1e3149;
    border-bottom-right-radius: 5px;
}

.message-content a {
    color: #1e3149;
    font-weight: 600;
}

.quick-replies {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.quick-replies button {
    background: white;
    border: 2px solid #e0e0e0;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s;
    font-family: inherit;
}

.quick-replies button:hover {
    border-color: #ffb600;
    background: #fff9e6;
}

.chat-input {
    padding: 15px;
    background: white;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}

.chat-input input {
    flex: 1;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 25px;
    font-size: 0.9rem;
    font-family: inherit;
    outline: none;
}

.chat-input input:focus {
    border-color: #ffb600;
}

.chat-input button {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #ffb600;
    border: none;
    color: #1e3149;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.chat-input button:hover {
    background: #ff9500;
    transform: scale(1.05);
}

.typing-indicator {
    display: flex;
    gap: 5px;
    padding: 12px 16px;
    background: white;
    border-radius: 18px;
    border-bottom-left-radius: 5px;
    width: fit-content;
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    background: #ccc;
    border-radius: 50%;
    animation: typing 1s infinite;
}

.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

@media (max-width: 480px) {
    #chatbot-window {
        width: calc(100vw - 40px);
        height: 70vh;
        bottom: 70px;
        right: -10px;
    }
}
</style>

<script>
let chatbotOpen = false;

function toggleChatbot() {
    const window = document.getElementById('chatbot-window');
    const badge = document.getElementById('chatBadge');
    chatbotOpen = !chatbotOpen;
    
    if (chatbotOpen) {
        window.classList.add('active');
        badge.style.display = 'none';
        document.getElementById('chatInput').focus();
    } else {
        window.classList.remove('active');
    }
}

function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    addMessage(message, 'user');
    input.value = '';
    
    // Hide quick replies after first message
    document.getElementById('quickReplies').style.display = 'none';
    
    // Show typing indicator
    showTyping();
    
    // Send to chatbot API
    fetch('index.php?controller=chatbot&action=respond', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: message })
    })
    .then(r => r.json())
    .then(data => {
        hideTyping();
        addMessage(data.response, 'bot');
    })
    .catch(() => {
        hideTyping();
        addMessage("Désolé, une erreur s'est produite. Veuillez réessayer.", 'bot');
    });
}

function sendQuickReply(text) {
    document.getElementById('chatInput').value = text;
    sendMessage();
}

function addMessage(text, type) {
    const container = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.className = 'message ' + type;
    div.innerHTML = '<div class="message-content">' + text.replace(/\n/g, '<br>') + '</div>';
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function showTyping() {
    const container = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.id = 'typingIndicator';
    div.className = 'message bot';
    div.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function hideTyping() {
    const typing = document.getElementById('typingIndicator');
    if (typing) typing.remove();
}

function handleKeyPress(e) {
    if (e.key === 'Enter') sendMessage();
}

// Show welcome badge after 3 seconds
setTimeout(() => {
    if (!chatbotOpen) {
        document.getElementById('chatBadge').style.display = 'flex';
    }
}, 3000);
</script>
