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
    // INITIALISATION DU CHATBOT
    // =============================================
    const container = document.getElementById('chatbot-fullpage');
    if (!container) return;

    container.innerHTML = `
        <div id="chatbot-window" class="visible">
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
                    <button class="suggestion-btn" data-action="missions">Missions</button>
                </div>
                
                <!-- Zone de saisie -->
                <div class="input-container">
                    <form id="chatbot-input-form">
                        <input type="text" 
                               id="chatbot-input" 
                               placeholder="Écrivez votre question ici..." 
                               autocomplete="off"
                               maxlength="500"
                               aria-label="Message à envoyer" />
                    </form>
                    
                    <button class="input-action" id="send-btn" title="Envoyer">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    // =============================================
    // ÉLÉMENTS DOM
    // =============================================
    const elements = {
        window: document.getElementById('chatbot-window'),
        messages: document.getElementById('chatbot-messages'),
        input: document.getElementById('chatbot-input'),
        form: document.getElementById('chatbot-input-form'),
        typingIndicator: document.getElementById('chatbot-typing'),
        sendBtn: document.getElementById('send-btn'),
        suggestions: document.querySelectorAll('.suggestion-btn')
    };

    // =============================================
    // ÉTATS ET DONNÉES
    // =============================================
    const state = {
        conversation: []
    };

    // Base de connaissances enrichie
    const knowledgeBase = {
        inscription: {
            title: "Comment m'inscrire ?",
            response: `<strong>Processus d'inscription :</strong><br><br>
            1. Cliquez sur "Inscription" dans le menu principal<br>
            2. Remplissez le formulaire avec vos informations<br>
            3. Vérifiez votre adresse email<br>
            4. Activez votre compte via le lien reçu<br><br>
            <strong>Documents nécessaires :</strong><br>
            • Aucun document n'est requis pour l'inscription standard<br>
            • Certaines missions spécifiques peuvent demander une vérification supplémentaire<br><br>
            <strong>Durée :</strong> Le processus prend environ 5 minutes`
        },
        password: {
            title: "J'ai oublié mon mot de passe",
            response: `<strong>Réinitialisation du mot de passe :</strong><br><br>
            1. Allez sur la page de connexion<br>
            2. Cliquez sur "Mot de passe oublié"<br>
            3. Entrez votre adresse email<br>
            4. Suivez les instructions dans l'email reçu<br>
            5. Créez un nouveau mot de passe sécurisé<br><br>
            <strong>Exigences de sécurité :</strong><br>
            • Minimum 8 caractères<br>
            • Au moins une majuscule et une minuscule<br>
            • Au moins un chiffre<br>
            • Au moins un caractère spécial<br><br>
            <strong>Support :</strong> Si vous ne recevez pas l'email, contactez support@aidforpeace.org`
        },
        contact: {
            title: "Comment contacter le support ?",
            response: `<strong>Coordonnées du support :</strong><br><br>
            <strong>Email principal :</strong> support@aidforpeace.org<br>
            <strong>Email admin :</strong> admin@aidforpeace.org<br><br>
            <strong>Disponibilité :</strong><br>
            • Lundi au vendredi : 9h00 - 18h00<br>
            • Samedi : 10h00 - 16h00<br>
            • Réponse sous 24 heures ouvrées<br><br>
            <strong>Pour une aide rapide :</strong><br>
            • Utilisez cet assistant conversationnel<br>
            • Consultez notre FAQ complète<br>
            • Rejoignez notre communauté en ligne`
        },
        profile: {
            title: "Comment modifier mon profil ?",
            response: `<strong>Gestion de votre profil :</strong><br><br>
            1. Connectez-vous à votre compte<br>
            2. Cliquez sur "Mon Profil" dans le menu<br>
            3. Sélectionnez "Modifier le profil"<br>
            4. Mettez à jour vos informations<br>
            5. Sauvegardez les modifications<br><br>
            <strong>Éléments modifiables :</strong><br>
            • Photo de profil<br>
            • Biographie (500 caractères max)<br>
            • Compétences et intérêts<br>
            • Disponibilités<br>
            • Paramètres de confidentialité<br><br>
            <strong>Conseil :</strong> Un profil complet augmente vos chances d'être sélectionné pour des missions.`
        },
        missions: {
            title: "Comment participer aux missions ?",
            response: `<strong>Participation aux missions :</strong><br><br>
            1. Complétez votre profil à 100%<br>
            2. Consultez les missions disponibles<br>
            3. Filtrez par localisation et intérêts<br>
            4. Postulez aux missions qui vous intéressent<br>
            5. Recevez une confirmation par email<br><br>
            <strong>Types de missions :</strong><br>
            • Bénévolat local<br>
            • Collectes de fonds<br>
            • Sensibilisation<br>
            • Support administratif<br>
            • Événements spéciaux<br><br>
            <strong>Bénéfices :</strong> Points de réputation, badges et certificats.`
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

    function findResponse(userMessage) {
        const message = userMessage.toLowerCase();

        if (message.includes('inscrire') || message.includes('créer compte') || message.includes('enregistrer')) {
            return knowledgeBase.inscription.response;
        }
        if (message.includes('mot de passe') || message.includes('mdp') || message.includes('oublié')) {
            return knowledgeBase.password.response;
        }
        if (message.includes('contacter') || message.includes('support') || message.includes('aide') || message.includes('contact')) {
            return knowledgeBase.contact.response;
        }
        if (message.includes('profil') || message.includes('modifier') || message.includes('compte')) {
            return knowledgeBase.profile.response;
        }
        if (message.includes('mission') || message.includes('bénévolat') || message.includes('participer')) {
            return knowledgeBase.missions.response;
        }

        return `<strong>Je comprends que vous cherchez : "${userMessage}"</strong><br><br>
        Voici les sujets sur lesquels je peux vous aider :<br><br>
        • <strong>Inscription</strong> - Création de compte et procédure d'inscription<br>
        • <strong>Mot de passe</strong> - Réinitialisation et problèmes de connexion<br>
        • <strong>Support</strong> - Contact avec notre équipe d'assistance<br>
        • <strong>Profil</strong> - Gestion et modification de votre compte<br>
        • <strong>Missions</strong> - Participation aux activités de bénévolat<br><br>
        <em>Vous pouvez cliquer sur les boutons ci-dessus ou reformuler votre question.</em>`;
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
    // INITIALISATION
    // =============================================

    // Message de bienvenue initial
    setTimeout(() => {
        addMessage(`<strong>Bonjour ! 👋 Je suis l'assistant Aid for Peace.</strong><br><br>
        Je suis là pour vous aider avec toutes vos questions concernant notre plateforme.<br><br>
        <strong>Voici comment je peux vous assister :</strong><br>
        • Processus d'inscription et création de compte<br>
        • Gestion du mot de passe et problèmes de connexion<br>
        • Modification et optimisation de votre profil<br>
        • Participation aux missions de bénévolat<br>
        • Contact avec notre équipe de support<br><br>
        <em>N'hésitez pas à me poser vos questions ou utiliser les boutons de suggestions ci-dessous !</em>`, false);
    }, 1000);

    // =============================================
    // ÉVÉNEMENTS
    // =============================================

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

    // Focus sur l'input au chargement
    setTimeout(() => {
        elements.input.focus();
    }, 1500);
});