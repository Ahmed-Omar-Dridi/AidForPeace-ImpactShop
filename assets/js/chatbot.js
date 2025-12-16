
// Chatbot Aid for Peace - Version Professionnelle Simplifiée
document.addEventListener('DOMContentLoaded', function () {
    // =============================================
    // CONFIGURATION
    // =============================================
    const CHATBOT_CONFIG = {
        name: "Assistant Aid for Peace",
        status: "En ligne • Assistant intelligent",
        avatar: "🤖",
        responseDelay: 600,
        typingDuration: 800
    };

    // =============================================
    // INITIALISATION DU WIDGET
    // =============================================
    const widget = document.createElement('div');
    widget.id = 'chatbot-widget';
    widget.innerHTML = `
        <!-- Bouton flottant -->
        <div id="chatbot-button" class="professionnel">
            <div class="chatbot-icon">
                <i class="fas fa-headset"></i>
            </div>
            <div class="chatbot-tooltip">Besoin d'aide ?</div>
        </div>
        
        <!-- Fenêtre de chat -->
        <div id="chatbot-window" class="hidden">
            <!-- En-tête -->
            <div id="chatbot-header">
                <div class="header-left">
                    <div class="chatbot-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="chatbot-info">
                        <span class="chatbot-name">${CHATBOT_CONFIG.name}</span>
                        <span class="chatbot-status">
                            <span class="status-dot"></span>
                            ${CHATBOT_CONFIG.status}
                        </span>
                    </div>
                </div>
                <div class="header-right">
                    <button class="header-btn" id="chatbot-close" title="Fermer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Corps du chat -->
            <div id="chatbot-body">
                <div id="chatbot-messages">
                    <!-- Messages seront injectés ici -->
                </div>
                
                <!-- Indicateur de saisie -->
                <div id="chatbot-typing" class="hidden">
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                    <span>L'assistant écrit...</span>
                </div>
            </div>
            
            <!-- Pied de page avec saisie -->
            <div id="chatbot-footer">
                <!-- Suggestions rapides -->
                <div id="chatbot-suggestions">
                    <button class="suggestion-btn" data-action="inscription">Inscription</button>
                    <button class="suggestion-btn" data-action="password">Mot de passe</button>
                    <button class="suggestion-btn" data-action="contact">Contacter</button>
                    <button class="suggestion-btn" data-action="profile">Profil</button>
                </div>
                
                <!-- Zone de saisie -->
                <div class="input-container">
                    <form id="chatbot-input-form">
                        <input type="text" 
                               id="chatbot-input" 
                               placeholder="Écrivez votre message..." 
                               autocomplete="off"
                               maxlength="500"
                               aria-label="Message à envoyer" />
                    </form>
                    
                    <button class="input-action" id="send-btn" title="Envoyer">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                
                <!-- Information de confidentialité -->
                <div class="privacy-notice">
                    <small><i class="fas fa-shield-alt"></i> Vos conversations sont sécurisées</small>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(widget);

    // =============================================
    // ÉLÉMENTS DOM
    // =============================================
    const elements = {
        button: document.getElementById('chatbot-button'),
        window: document.getElementById('chatbot-window'),
        messages: document.getElementById('chatbot-messages'),
        input: document.getElementById('chatbot-input'),
        form: document.getElementById('chatbot-input-form'),
        typingIndicator: document.getElementById('chatbot-typing'),
        sendBtn: document.getElementById('send-btn'),
        suggestions: document.querySelectorAll('.suggestion-btn'),
        closeBtn: document.getElementById('chatbot-close'),
        openChatbotBtn: document.getElementById('openChatbotBtn')
    };

    // =============================================
    // ÉTATS ET DONNÉES
    // =============================================
    const state = {
        isOpen: false,
        unreadCount: 0,
        conversation: []
    };

    // Base de connaissances
    const knowledgeBase = {
        inscription: {
            title: "Inscription",
            response: `Pour vous inscrire sur Aid for Peace :<br><br>
            <strong>Étapes :</strong><br>
            1. Cliquez sur "Inscription" en haut de la page<br>
            2. Remplissez le formulaire avec vos informations personnelles<br>
            3. Confirmez votre adresse email<br>
            4. Validez votre compte<br><br>
            <strong>Durée :</strong> 2-3 minutes`
        },
        password: {
            title: "Mot de passe oublié",
            response: `Procédure de réinitialisation du mot de passe :<br><br>
            <strong>Via l'interface :</strong><br>
            1. Cliquez sur "Mot de passe oublié" sur la page de connexion<br>
            2. Saisissez votre adresse email<br>
            3. Suivez les instructions reçues par email<br>
            4. Définissez un nouveau mot de passe sécurisé<br><br>
            <strong>Support :</strong> admin@aidforpeace.org`
        },
        contact: {
            title: "Contacter le support",
            response: `Coordonnées du support Aid for Peace :<br><br>
            <strong>Email principal :</strong> admin@aidforpeace.org<br><br>
            <strong>Disponibilité :</strong><br>
            • Lundi - Vendredi : 9h-18h<br>
            • Réponse sous 24h ouvrées<br><br>
            <strong>Pour une aide rapide :</strong><br>
            • Consultez notre FAQ complète<br>
            • Utilisez cet assistant conversationnel`
        },
        profile: {
            title: "Gestion du profil",
            response: `Gérer votre profil utilisateur :<br><br>
            <strong>Modification du profil :</strong><br>
            1. Connectez-vous à votre compte<br>
            2. Cliquez sur "Mon Profil"<br>
            3. Sélectionnez "Modifier le profil"<br>
            4. Mettez à jour vos informations<br>
            5. Sauvegardez les modifications<br><br>
            <strong>Conseil :</strong> Un profil complet augmente votre visibilité.`
        }
    };

    // =============================================
    // FONCTIONS UTILITAIRES
    // =============================================

    function formatTime() {
        return new Date().toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function createMessageElement(content, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isUser ? 'user' : 'bot'}`;

        const timestamp = document.createElement('div');
        timestamp.className = 'message-timestamp';
        timestamp.textContent = formatTime();

        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.innerHTML = content;

        if (!isUser) {
            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.innerHTML = CHATBOT_CONFIG.avatar;
            messageDiv.appendChild(avatar);

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            bubble.appendChild(contentDiv);
            bubble.appendChild(timestamp);
            messageDiv.appendChild(bubble);
        } else {
            const bubble = document.createElement('div');
            bubble.className = 'message-bubble user';
            bubble.appendChild(contentDiv);
            bubble.appendChild(timestamp);
            messageDiv.appendChild(bubble);
        }

        return messageDiv;
    }

    function addMessage(content, isUser = false) {
        const messageElement = createMessageElement(content, isUser);
        elements.messages.appendChild(messageElement);
        scrollToBottom();

        state.conversation.push({
            content,
            isUser,
            timestamp: new Date().toISOString()
        });

        if (!isUser && !state.isOpen) {
            state.unreadCount++;
            updateBadge();
            animateButton();
        }
    }

    function showTypingIndicator() {
        elements.typingIndicator.classList.remove('hidden');
        scrollToBottom();
    }

    function hideTypingIndicator() {
        elements.typingIndicator.classList.add('hidden');
    }

    function simulateTyping(response, callback) {
        showTypingIndicator();

        setTimeout(() => {
            hideTypingIndicator();
            setTimeout(() => {
                if (callback) callback(response);
            }, 100);
        }, CHATBOT_CONFIG.typingDuration);
    }

    function scrollToBottom() {
        elements.messages.scrollTop = elements.messages.scrollHeight;
    }

    function updateBadge() {
        const badge = elements.button.querySelector('.badge');
        if (state.unreadCount > 0) {
            if (!badge) {
                const badgeEl = document.createElement('div');
                badgeEl.className = 'badge';
                badgeEl.textContent = state.unreadCount;
                elements.button.appendChild(badgeEl);
            } else {
                badge.textContent = state.unreadCount;
            }
        } else if (badge) {
            badge.remove();
        }
    }

    function animateButton() {
        elements.button.style.transform = 'scale(1.1)';
        setTimeout(() => {
            elements.button.style.transform = 'scale(1)';
        }, 300);
    }

    function findResponse(userMessage) {
        const message = userMessage.toLowerCase();

        if (message.includes('inscrire') || message.includes('créer compte') || message.includes('enregistrer')) {
            return knowledgeBase.inscription.response;
        }
        if (message.includes('mot de passe') || message.includes('mdp') || message.includes('oublié')) {
            return knowledgeBase.password.response;
        }
        if (message.includes('contacter') || message.includes('support') || message.includes('aide')) {
            return knowledgeBase.contact.response;
        }
        if (message.includes('profil') || message.includes('modifier') || message.includes('compte')) {
            return knowledgeBase.profile.response;
        }

        return `Je comprends que vous cherchez des informations sur "${userMessage}".<br><br>
        Pour vous aider plus efficacement, voici les sujets que je maîtrise :<br>
        • <strong>Inscription</strong> - Créer un compte<br>
        • <strong>Mot de passe</strong> - Réinitialiser vos identifiants<br>
        • <strong>Support</strong> - Contacter l'équipe<br>
        • <strong>Profil</strong> - Gérer vos informations<br><br>
        Vous pouvez cliquer sur les boutons ci-dessous ou formuler votre question différemment.`;
    }

    function sendMessage() {
        const message = elements.input.value.trim();
        if (!message) return;

        addMessage(message, true);
        elements.input.value = '';

        simulateTyping(message, (userMessage) => {
            const response = findResponse(userMessage);
            addMessage(response, false);
        });
    }

    function sendSuggestion(action) {
        const data = knowledgeBase[action];
        if (!data) return;

        addMessage(data.title, true);

        simulateTyping(action, () => {
            addMessage(data.response, false);
        });
    }

    // =============================================
    // GESTION DE L'INTERFACE
    // =============================================

    function openChat() {
        state.isOpen = true;
        elements.window.classList.remove('hidden');
        elements.window.classList.add('visible');

        state.unreadCount = 0;
        updateBadge();

        setTimeout(() => {
            elements.input.focus();
        }, 300);

        if (state.conversation.length === 0) {
            setTimeout(() => {
                addMessage(`Bonjour ! Je suis votre assistant Aid for Peace. 🤝<br><br>
                Je suis là pour vous aider avec :<br>
                • L'inscription et la création de compte<br>
                • La gestion de votre mot de passe<br>
                • Le contact avec notre équipe<br>
                • La modification de votre profil<br><br>
                N'hésitez pas à me poser vos questions ou utiliser les boutons ci-dessous pour une aide rapide.`, false);
            }, 500);
        }
    }

    function closeChat() {
        state.isOpen = false;
        elements.window.classList.remove('visible');
        setTimeout(() => {
            elements.window.classList.add('hidden');
        }, 300);
    }

    function toggleChat() {
        if (state.isOpen) {
            closeChat();
        } else {
            openChat();
        }
    }

    // =============================================
    // ÉVÉNEMENTS
    // =============================================

    // Bouton flottant
    elements.button.addEventListener('click', toggleChat);

    // Bouton dans le formulaire
    if (elements.openChatbotBtn) {
        elements.openChatbotBtn.addEventListener('click', toggleChat);
    }

    // Fermeture
    elements.closeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        closeChat();
    });

    // Envoi du formulaire
    elements.form.addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessage();
    });

    // Bouton d'envoi
    elements.sendBtn.addEventListener('click', sendMessage);

    // Entrée clavier
    elements.input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Suggestions
    elements.suggestions.forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.dataset.action;
            sendSuggestion(action);
        });
    });
});
