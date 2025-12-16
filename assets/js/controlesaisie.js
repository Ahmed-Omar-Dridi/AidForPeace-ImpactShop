class ControleSaisie {
    static validerInscription() {
        let isValid = true;

        // Validation prénom
        const prenom = document.getElementById('prenom');
        const erreurPrenom = document.getElementById('erreurprenom');
        if (!this.validerNom(prenom.value)) {
            erreurPrenom.textContent = "Le prénom doit contenir 2-50 caractères alphabétiques";
            erreurPrenom.style.display = 'block';
            isValid = false;
        } else {
            erreurPrenom.style.display = 'none';
        }

        // Validation nom
        const nom = document.getElementById('nom');
        const erreurNom = document.getElementById('erreurnom');
        if (!this.validerNom(nom.value)) {
            erreurNom.textContent = "Le nom doit contenir 2-50 caractères alphabétiques";
            erreurNom.style.display = 'block';
            isValid = false;
        } else {
            erreurNom.style.display = 'none';
        }

        // Validation email
        const email = document.getElementById('email');
        const erreurEmail = document.getElementById('erreuremail');
        if (!this.validerEmail(email.value)) {
            erreurEmail.textContent = "Format d'email invalide";
            erreurEmail.style.display = 'block';
            isValid = false;
        } else {
            erreurEmail.style.display = 'none';
        }

        // Validation mot de passe
        const password = document.getElementById('password');
        const erreurPassword = document.getElementById('erreurpassword');
        if (!this.validerPassword(password.value)) {
            erreurPassword.textContent = "Le mot de passe doit contenir 8 caractères, majuscule, minuscule, chiffre et caractère spécial";
            erreurPassword.style.display = 'block';
            isValid = false;
        } else {
            erreurPassword.style.display = 'none';
        }

        // Validation confirmation mot de passe
        const password2 = document.getElementById('password2');
        const erreurPassword2 = document.getElementById('erreurpassword2');
        if (password.value !== password2.value) {
            erreurPassword2.textContent = "Les mots de passe ne correspondent pas";
            erreurPassword2.style.display = 'block';
            isValid = false;
        } else {
            erreurPassword2.style.display = 'none';
        }

        return isValid;
    }

    static validerProfil() {
        let isValid = true;

        // Validation biographie
        const bio = document.getElementById('bio');
        const erreurBio = document.getElementById('erreurbio');
        if (bio.value.length > 500) {
            erreurBio.textContent = "La biographie ne doit pas dépasser 500 caractères";
            erreurBio.style.display = 'block';
            isValid = false;
        } else {
            erreurBio.style.display = 'none';
        }

        // Validation statut
        const statut = document.getElementById('statut');
        const erreurStatut = document.getElementById('erreurstatut');
        if (statut.value.trim() === '') {
            erreurStatut.textContent = "Le statut est obligatoire";
            erreurStatut.style.display = 'block';
            isValid = false;
        } else {
            erreurStatut.style.display = 'none';
        }

        return isValid;
    }

    static validerNom(nom) {
        return /^[a-zA-ZÀ-ÿ\s\-]{2,50}$/.test(nom);
    }

    static validerEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    static validerPassword(password) {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/.test(password);
    }
}

// Validation en temps réel
document.addEventListener('DOMContentLoaded', function () {
    // Validation du prénom en temps réel
    const prenom = document.getElementById('prenom');
    if (prenom) {
        prenom.addEventListener('input', function () {
            const erreurPrenom = document.getElementById('erreurprenom');
            if (!ControleSaisie.validerNom(this.value)) {
                erreurPrenom.textContent = "Le prénom doit contenir 2-50 caractères alphabétiques";
                erreurPrenom.style.display = 'block';
            } else {
                erreurPrenom.style.display = 'none';
            }
        });
    }

    // Validation du nom en temps réel
    const nom = document.getElementById('nom');
    if (nom) {
        nom.addEventListener('input', function () {
            const erreurNom = document.getElementById('erreurnom');
            if (!ControleSaisie.validerNom(this.value)) {
                erreurNom.textContent = "Le nom doit contenir 2-50 caractères alphabétiques";
                erreurNom.style.display = 'block';
            } else {
                erreurNom.style.display = 'none';
            }
        });
    }

    // Validation de l'email en temps réel
    const email = document.getElementById('email');
    if (email) {
        email.addEventListener('input', function () {
            const erreurEmail = document.getElementById('erreuremail');
            if (!ControleSaisie.validerEmail(this.value)) {
                erreurEmail.textContent = "Format d'email invalide";
                erreurEmail.style.display = 'block';
            } else {
                erreurEmail.style.display = 'none';
            }
        });
    }

    // Validation du mot de passe en temps réel
    const password = document.getElementById('password');
    if (password) {
        password.addEventListener('input', function () {
            const erreurPassword = document.getElementById('erreurpassword');
            if (!ControleSaisie.validerPassword(this.value)) {
                erreurPassword.textContent = "Le mot de passe doit contenir 8 caractères, majuscule, minuscule, chiffre et caractère spécial";
                erreurPassword.style.display = 'block';
            } else {
                erreurPassword.style.display = 'none';
            }
        });
    }

    // Validation de la confirmation du mot de passe en temps réel
    const password2 = document.getElementById('password2');
    const passwordField = document.getElementById('password');
    if (password2 && passwordField) {
        password2.addEventListener('input', function () {
            const erreurPassword2 = document.getElementById('erreurpassword2');
            if (this.value !== passwordField.value) {
                erreurPassword2.textContent = "Les mots de passe ne correspondent pas";
                erreurPassword2.style.display = 'block';
            } else {
                erreurPassword2.style.display = 'none';
            }
        });

        // Également vérifier quand le premier mot de passe change
        passwordField.addEventListener('input', function () {
            const erreurPassword2 = document.getElementById('erreurpassword2');
            if (password2.value !== this.value) {
                erreurPassword2.textContent = "Les mots de passe ne correspondent pas";
                erreurPassword2.style.display = 'block';
            } else {
                erreurPassword2.style.display = 'none';
            }
        });
    }

    // Validation de la biographie en temps réel
    const bio = document.getElementById('bio');
    if (bio) {
        bio.addEventListener('input', function () {
            const erreurBio = document.getElementById('erreurbio');
            if (this.value.length > 500) {
                erreurBio.textContent = "La biographie ne doit pas dépasser 500 caractères";
                erreurBio.style.display = 'block';
            } else {
                erreurBio.style.display = 'none';
            }
        });
    }

    // Validation du statut en temps réel
    const statut = document.getElementById('statut');
    if (statut) {
        statut.addEventListener('input', function () {
            const erreurStatut = document.getElementById('erreurstatut');
            if (this.value.trim() === '') {
                erreurStatut.textContent = "Le statut est obligatoire";
                erreurStatut.style.display = 'block';
            } else {
                erreurStatut.style.display = 'none';
            }
        });
    }
});