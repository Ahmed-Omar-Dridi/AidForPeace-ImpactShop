/**
 * Gestion des onglets du profil
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('📑 Profile Tabs: Initialisation...');
    
    const tabs = document.querySelectorAll('.profile-tab');
    const tabContents = document.querySelectorAll('.profile-tab-content');
    
    if (tabs.length === 0) {
        console.log('⚠️ Aucun onglet trouvé');
        return;
    }
    
    console.log(`✅ ${tabs.length} onglets trouvés`);
    
    // Fonction pour changer d'onglet
    function switchTab(tabId) {
        console.log(`🔄 Changement vers l'onglet: ${tabId}`);
        
        // Désactiver tous les onglets
        tabs.forEach(tab => tab.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Activer l'onglet sélectionné
        const selectedTab = document.querySelector(`[data-tab="${tabId}"]`);
        const selectedContent = document.getElementById(tabId);
        
        if (selectedTab && selectedContent) {
            selectedTab.classList.add('active');
            selectedContent.classList.add('active');
            
            // Sauvegarder l'onglet actif dans localStorage
            localStorage.setItem('activeProfileTab', tabId);
        }
    }
    
    // Ajouter les événements de clic
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            switchTab(tabId);
        });
    });
    
    // Restaurer l'onglet actif depuis localStorage
    const savedTab = localStorage.getItem('activeProfileTab');
    if (savedTab && document.getElementById(savedTab)) {
        switchTab(savedTab);
    } else {
        // Activer le premier onglet par défaut
        if (tabs.length > 0) {
            const firstTabId = tabs[0].getAttribute('data-tab');
            switchTab(firstTabId);
        }
    }
});
