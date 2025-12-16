/**
 * Gestion des compétences dans le profil
 */

console.log('🎯 profile-skills.js chargé');

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM chargé - Initialisation des compétences');

    // ============================================
    // GESTION DES COMPÉTENCES
    // ============================================
    
    // Affichage du niveau en temps réel
    const skillLevelInput = document.getElementById('new_skill_level');
    const skillLevelDisplay = document.getElementById('skill-level-display');
    
    if (skillLevelInput && skillLevelDisplay) {
        skillLevelInput.addEventListener('input', function() {
            const level = this.value;
            skillLevelDisplay.textContent = level;
            const stars = skillLevelDisplay.nextElementSibling;
            if (stars) {
                stars.textContent = '⭐'.repeat(level);
            }
        });
        console.log('✅ Slider de niveau initialisé');
    }

    // Ajouter une compétence
    const addSkillBtn = document.getElementById('add-skill-btn');
    if (addSkillBtn) {
        addSkillBtn.addEventListener('click', async function() {
            console.log('🔘 Clic sur Ajouter compétence');
            
            const skillName = document.getElementById('new_skill_name').value.trim();
            const skillLevel = document.getElementById('new_skill_level').value;
            const skillCategory = document.getElementById('new_skill_category').value;
            const skillYears = document.getElementById('new_skill_years').value;
            const skillCertified = document.getElementById('new_skill_certified').checked;

            if (!skillName) {
                alert('❌ Veuillez entrer un nom de compétence');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('skill_name', skillName);
                formData.append('skill_level', skillLevel);
                formData.append('skill_category', skillCategory);
                formData.append('years_experience', skillYears);
                formData.append('is_certified', skillCertified ? '1' : '0');

                console.log('📤 Envoi de la compétence:', skillName);

                const response = await fetch('index.php?controller=user&action=add_skill', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('📥 Réponse:', result);

                if (result.success) {
                    alert('✅ ' + result.message);
                    // Réinitialiser le formulaire
                    document.getElementById('new_skill_name').value = '';
                    document.getElementById('new_skill_level').value = 3;
                    document.getElementById('new_skill_years').value = 0;
                    document.getElementById('new_skill_certified').checked = false;
                    skillLevelDisplay.textContent = '3';
                    const stars = skillLevelDisplay.nextElementSibling;
                    if (stars) stars.textContent = '⭐⭐⭐';
                    // Recharger la liste
                    loadUserSkills();
                } else {
                    alert('❌ ' + result.error);
                }
            } catch (error) {
                console.error('❌ Erreur ajout compétence:', error);
                alert('❌ Erreur lors de l\'ajout de la compétence');
            }
        });
        console.log('✅ Bouton Ajouter initialisé');
    } else {
        console.error('❌ Bouton add-skill-btn introuvable');
    }

    // Charger les compétences de l'utilisateur
    window.loadUserSkills = async function() {
        const container = document.getElementById('skills-container');
        if (!container) {
            console.error('❌ Conteneur skills-container introuvable');
            return;
        }

        console.log('📥 Chargement des compétences...');

        try {
            container.innerHTML = '<p style="text-align: center; color: #95a5a6; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Chargement...</p>';

            const response = await fetch('index.php?controller=user&action=get_user_skills');
            const skills = await response.json();

            console.log('✅ Compétences reçues:', skills.length);

            if (skills.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #95a5a6; padding: 20px;">Aucune compétence ajoutée pour le moment</p>';
                return;
            }

            // Grouper par catégorie
            const grouped = {};
            skills.forEach(skill => {
                if (!grouped[skill.skill_category]) {
                    grouped[skill.skill_category] = [];
                }
                grouped[skill.skill_category].push(skill);
            });

            container.innerHTML = '';
            
            Object.keys(grouped).forEach(category => {
                const categoryDiv = document.createElement('div');
                categoryDiv.style.marginBottom = '20px';
                
                const categoryHeader = document.createElement('h5');
                categoryHeader.style.color = '#667eea';
                categoryHeader.style.marginBottom = '10px';
                categoryHeader.innerHTML = getCategoryIcon(category) + ' ' + category;
                categoryDiv.appendChild(categoryHeader);

                grouped[category].forEach(skill => {
                    const skillCard = document.createElement('div');
                    skillCard.style.cssText = 'background: white; padding: 15px; border-radius: 8px; border: 2px solid #e1e8ed; margin-bottom: 10px;';
                    
                    skillCard.innerHTML = `
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                                    <strong style="font-size: 16px;">${skill.skill_name}</strong>
                                    ${skill.is_certified == 1 ? '<span style="background: #ffd700; color: #000; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">🏆 CERTIFIÉ</span>' : ''}
                                </div>
                                <div style="color: #666; font-size: 14px; margin-bottom: 8px;">
                                    Niveau: ${'⭐'.repeat(skill.skill_level)} (${skill.skill_level}/5)
                                    ${skill.years_experience > 0 ? ` • ${skill.years_experience} an${skill.years_experience > 1 ? 's' : ''} d'expérience` : ''}
                                </div>
                            </div>
                            <button type="button" onclick="deleteSkill(${skill.id_skill})" style="background: #e74c3c; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    
                    categoryDiv.appendChild(skillCard);
                });

                container.appendChild(categoryDiv);
            });

            console.log('✅ Compétences affichées');
        } catch (error) {
            console.error('❌ Erreur chargement compétences:', error);
            container.innerHTML = '<p style="text-align: center; color: #e74c3c; padding: 20px;">❌ Erreur lors du chargement</p>';
        }
    };

    // Supprimer une compétence
    window.deleteSkill = async function(skillId) {
        if (!confirm('❌ Êtes-vous sûr de vouloir supprimer cette compétence ?')) {
            return;
        }

        console.log('🗑️ Suppression de la compétence:', skillId);

        try {
            const formData = new FormData();
            formData.append('skill_id', skillId);

            const response = await fetch('index.php?controller=user&action=delete_skill', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            console.log('📥 Réponse:', result);

            if (result.success) {
                alert('✅ ' + result.message);
                loadUserSkills();
            } else {
                alert('❌ ' + result.error);
            }
        } catch (error) {
            console.error('❌ Erreur suppression compétence:', error);
            alert('❌ Erreur lors de la suppression');
        }
    };

    // Fonction helper pour les icônes de catégorie
    function getCategoryIcon(category) {
        const icons = {
            'Technique': '💻',
            'Communication': '💬',
            'Gestion': '📊',
            'Créativité': '🎨',
            'Leadership': '👑',
            'Langues': '🌍'
        };
        return icons[category] || '🎯';
    }

    // Charger les compétences au chargement de la page
    if (document.getElementById('skills-container')) {
        console.log('🔄 Chargement initial des compétences');
        loadUserSkills();
    }

    console.log('✅ Gestion des compétences initialisée');
});
