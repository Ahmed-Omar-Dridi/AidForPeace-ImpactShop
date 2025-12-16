<?php
require_once(__DIR__ . '/../models/user.php');
require_once(__DIR__ . '/../models/profil.php');

class UserController {
    
    // CREATE - Inscription utilisateur
    public function register(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'errors' => ['Méthode non autorisée.']];
        }

        // ✅ RECAPTCHA - Vérification en premier
        if (isset($GLOBALS['recaptchaController'])) {
            $recaptchaError = $GLOBALS['recaptchaController']->middleware('register');
            if ($recaptchaError !== null) {
                return $recaptchaError; // Retourne l'erreur reCAPTCHA
            }
        }

        $data = [
            'prenom' => trim($_POST['prenom'] ?? ''),
            'nom' => trim($_POST['nom'] ?? ''),
            'birthdate' => $_POST['birthdate'] ?? '',
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password2' => $_POST['password2'] ?? '',
            'facial_data' => $_POST['facial_data'] ?? '',
            'facial_descriptor' => $_POST['facial_descriptor'] ?? ''
        ];

        // Validation standard
        $errors = $this->validateRegistration($data);
        
        // Validation des données faciales
        $facialErrors = $this->validateFacialData($data);
        $errors = array_merge($errors, $facialErrors);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['Cet email est déjà utilisé.']];
        }

        $role = ($data['email'] === 'admin@aidforpeace.org') ? User::ROLE_ADMIN : User::ROLE_USER;

        // Traiter les données faciales
        $facial_processed = $this->processFacialData($data);

        $user = new User(
            $data['nom'],
            $data['prenom'],
            $data['birthdate'],
            $data['email'],
            $data['password'],
            $role,
            1,
            0,
            'default.jpg',
            'beginner',
            'bronze',
            '',
            'text',  // bio_type
            '',      // bio_audio_path
            $facial_processed['facial_data'],
            $facial_processed['facial_descriptor']
        );

        if ($user->create()) {
            // Créer un profil par défaut
            $profil = new Profil($user->getId(), 'Nouveau membre - En attente de configuration', date('Y-m-d H:i:s'));
            $this->saveProfil($profil);
            
            // ✅ VÉRIFICATION EMAIL - Envoyer le code de vérification
            if (defined('EMAIL_VERIFICATION_ENABLED') && EMAIL_VERIFICATION_ENABLED && isset($GLOBALS['emailVerificationController'])) {
                $emailResult = $GLOBALS['emailVerificationController']->sendVerificationCode(
                    $user->getId(),
                    $data['email'],
                    $data['prenom'] . ' ' . $data['nom']
                );
                
                if ($emailResult['success']) {
                    $message = 'Inscription réussie! Un code de vérification a été envoyé à votre email.';
                } else {
                    $message = 'Inscription réussie! Attention: ' . $emailResult['message'];
                }
            } else {
                $message = 'Inscription réussie';
                if (!empty($data['facial_data'])) {
                    $message .= ' avec enregistrement facial!';
                } else {
                    $message .= '!';
                }
            }
            
            return ['success' => true, 'message' => $message, 'user_id' => $user->getId()];
        } else {
            return ['success' => false, 'errors' => ['Erreur lors de l\'inscription.']];
        }
    }

    // NOUVELLES MÉTHODES POUR LA RECONNAISSANCE FACIALE
    private function validateFacialData(array $data): array {
        $errors = [];
        
        // Si des données faciales sont fournies, vérifiez-les
        if (!empty($data['facial_data']) && !$this->validateBase64Image($data['facial_data'])) {
            $errors[] = "Les données faciales ne sont pas valides.";
        }
        
        return $errors;
    }

    private function validateBase64Image(string $base64): bool {
        // Vérifie si c'est une chaîne base64 valide pour une image
        if (preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $base64)) {
            return true;
        }
        return false;
    }

    private function processFacialData(array $data): array {
        $facial_data = trim($data['facial_data'] ?? '');
        $facial_descriptor = trim($data['facial_descriptor'] ?? '');
        
        // Nettoyer et limiter la taille
        if (strlen($facial_data) > 1000000) { // Limiter à 1MB
            $facial_data = substr($facial_data, 0, 1000000);
        }
        
        return [
            'facial_data' => $facial_data,
            'facial_descriptor' => $facial_descriptor
        ];
    }

    // READ - Connexion utilisateur
    public function login(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Méthode non autorisée.'];
        }

        // ✅ RECAPTCHA - Vérification en premier
        if (isset($GLOBALS['recaptchaController'])) {
            $recaptchaError = $GLOBALS['recaptchaController']->middleware('login');
            if ($recaptchaError !== null) {
                // Convertir le format pour correspondre à login (error au lieu de errors)
                return [
                    'success' => false,
                    'error' => $recaptchaError['errors'][0] ?? 'Erreur reCAPTCHA'
                ];
            }
        }

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Email et mot de passe requis.'];
        }

        try {
            $userData = $this->getUserByEmail($email);
            
            if ($userData && password_verify($password, $userData['password'])) {
                return [
                    'success' => true, 
                    'user' => $userData,
                    'message' => 'Connexion réussie!'
                ];
            } else {
                return ['success' => false, 'error' => 'Email ou mot de passe incorrect.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Erreur lors de la connexion.'];
        }
    }

    // =============================================
    // MOT DE PASSE OUBLIÉ / RESET PASSWORD
    // =============================================

    public function requestPasswordReset(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Méthode non autorisée.'];
        }

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (empty($email) || empty($password) || empty($password2)) {
            return ['success' => false, 'error' => 'Email et nouveaux mots de passe requis.'];
        }

        if ($password !== $password2) {
            return ['success' => false, 'error' => 'Les mots de passe ne correspondent pas.'];
        }

        if (!$this->validatePassword($password)) {
            return ['success' => false, 'error' => 'Le mot de passe ne respecte pas les règles de sécurité.'];
        }

        $user = $this->getUserByEmail($email);
        if (!$user) {
            // Par sécurité, on ne révèle pas si l'email existe ou non
            return ['success' => true, 'message' => 'Si un compte existe avec cet email, le mot de passe a été mis à jour.'];
        }

        if ($this->updateUserPassword((int)$user['id_user'], $password)) {
            return [
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès. Vous pouvez maintenant vous connecter.'
            ];
        }

        return ['success' => false, 'error' => 'Erreur lors de la mise à jour du mot de passe.'];
    }

    // READ - Récupérer tous les utilisateurs
    public function getAllUsers(): array {
        return $this->getAllUsersFromDB();
    }

    // READ - Récupérer un utilisateur par ID
    public function getUserById(int $id): array {
        $user = $this->getUserByIdFromDB($id);
        if ($user) {
            return ['success' => true, 'user' => $user];
        } else {
            return ['success' => false, 'errors' => ['Utilisateur non trouvé.']];
        }
    }

    // UPDATE - Modifier un utilisateur
    public function updateUser(int $id): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'errors' => ['Méthode non autorisée.']];
        }

        $data = [
            'prenom' => trim($_POST['prenom'] ?? ''),
            'nom' => trim($_POST['nom'] ?? ''),
            'birthdate' => $_POST['birthdate'] ?? '',
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? User::ROLE_USER,
            'niveau' => intval($_POST['niveau'] ?? 1),
            'points' => intval($_POST['points'] ?? 0),
            'photo' => $_POST['photo'] ?? 'default.jpg',
            'badge' => $_POST['badge'] ?? 'beginner',
            'rank' => $_POST['rank'] ?? 'bronze',
            'bio' => trim($_POST['bio'] ?? '')
        ];

        $errors = $this->validateUpdate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $existingUser = $this->getUserByIdFromDB($id);
        if (!$existingUser) {
            return ['success' => false, 'errors' => ['Utilisateur non trouvé.']];
        }

        if ($data['email'] !== $existingUser['email'] && $this->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['Cet email est déjà utilisé.']];
        }

        $user = new User(
            $data['nom'],
            $data['prenom'],
            $data['birthdate'],
            $data['email'],
            $data['password'],
            $data['role'],
            $data['niveau'],
            $data['points'],
            $data['photo'],
            $data['badge'],
            $data['rank'],
            $data['bio']
        );
        $user->id = $id;

        if ($user->update()) {
            return ['success' => true, 'message' => 'Utilisateur mis à jour avec succès.'];
        } else {
            return ['success' => false, 'errors' => ['Erreur lors de la mise à jour.']];
        }
    }

    // UPDATE - Modifier le profil utilisateur
    public function updateProfile(int $id): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'errors' => ['Méthode non autorisée.']];
        }

        $data = [
            'prenom' => trim($_POST['prenom'] ?? ''),
            'nom' => trim($_POST['nom'] ?? ''),
            'birthdate' => $_POST['birthdate'] ?? '',
            'email' => trim($_POST['email'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'bio_type' => $_POST['bio_type'] ?? 'text',
            'statut' => trim($_POST['statut'] ?? ''),
            'points' => intval($_POST['points'] ?? 0),
            // Nouvelles données - Statut
            'status' => $_POST['status'] ?? 'offline',
            'status_message' => trim($_POST['status_message'] ?? ''),
            // Nouvelles données - Localisation
            'location_country' => trim($_POST['location_country'] ?? ''),
            'location_city' => trim($_POST['location_city'] ?? ''),
            'location_latitude' => !empty($_POST['location_latitude']) ? floatval($_POST['location_latitude']) : null,
            'location_longitude' => !empty($_POST['location_longitude']) ? floatval($_POST['location_longitude']) : null,
            'location_timezone' => trim($_POST['location_timezone'] ?? 'UTC'),
            'location_public' => isset($_POST['location_public']) && $_POST['location_public'] === '1',
            // Nouvelles données - 2FA
            'two_factor_enabled' => isset($_POST['two_factor_enabled']) && $_POST['two_factor_enabled'] === '1',
            'two_factor_method' => $_POST['two_factor_method'] ?? 'app'
        ];

        // Gérer l'upload de la photo de profil
        $photoPath = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handlePhotoUpload($_FILES['photo'], $id);
            if ($uploadResult['success']) {
                $photoPath = $uploadResult['filename'];
            } else {
                return ['success' => false, 'errors' => [$uploadResult['error']]];
            }
        }
        
        // Gérer l'upload du fichier audio si le type est 'audio'
        $bioAudioPath = '';
        if ($data['bio_type'] === 'audio' && isset($_FILES['bio_audio_file']) && $_FILES['bio_audio_file']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleAudioUpload($_FILES['bio_audio_file'], $id);
            if ($uploadResult['success']) {
                $bioAudioPath = $uploadResult['path'];
            } else {
                return ['success' => false, 'errors' => [$uploadResult['error']]];
            }
        }

        $errors = $this->validateProfile($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $existingUser = $this->getUserByIdFromDB($id);
        if (!$existingUser) {
            return ['success' => false, 'errors' => ['Utilisateur non trouvé.']];
        }

        if ($data['email'] !== $existingUser['email'] && $this->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['Cet email est déjà utilisé.']];
        }

        // Mettre à jour l'utilisateur
        $user = new User(
            $data['nom'],
            $data['prenom'],
            $data['birthdate'],
            $data['email'],
            '', // password vide = pas de modification
            $existingUser['role'],
            $existingUser['niveau'],
            $data['points'],
            $photoPath ?: $existingUser['photo'], // Utiliser la nouvelle photo ou garder l'ancienne
            $existingUser['badges'],
            $existingUser['rank'],
            $data['bio_type'] === 'text' ? $data['bio'] : '',
            $data['bio_type'],
            $bioAudioPath ?: ($existingUser['bio_audio_path'] ?? '')
        );
        $user->id = $id;

        if ($user->update()) {
            // Mettre à jour les nouvelles données (statut, localisation, 2FA)
            $this->updateAdvancedFeatures($id, $data);
            
            // Mettre à jour ou créer le profil
            $profil = new Profil($id, $data['statut'], date('Y-m-d H:i:s'));
            if ($this->saveProfil($profil)) {
                return ['success' => true, 'message' => 'Profil mis à jour avec succès.'];
            } else {
                return ['success' => false, 'errors' => ['Erreur lors de la mise à jour du statut.']];
            }
        } else {
            return ['success' => false, 'errors' => ['Erreur lors de la mise à jour.']];
        }
    }

    // DELETE - Supprimer un utilisateur (version admin)
    public function deleteUserAdmin(int $id): array {
        // Vérifier que l'utilisateur à supprimer n'est pas un admin
        $userToDelete = $this->getUserByIdFromDB($id);
        
        if (!$userToDelete) {
            return ['success' => false, 'errors' => ['Utilisateur non trouvé.']];
        }
        
        if ($userToDelete['role'] === User::ROLE_ADMIN) {
            return ['success' => false, 'errors' => ['Impossible de supprimer un administrateur.']];
        }
        
        if ($this->deleteUserFromDB($id)) {
            return ['success' => true, 'message' => 'Utilisateur supprimé avec succès.'];
        } else {
            return ['success' => false, 'errors' => ['Erreur lors de la suppression.']];
        }
    }

    // DELETE - Supprimer un profil
    public function deleteProfil(int $profil_id): array {
        if ($this->deleteProfilFromDB($profil_id)) {
            return ['success' => true, 'message' => 'Profil supprimé avec succès.'];
        } else {
            return ['success' => false, 'errors' => ['Erreur lors de la suppression du profil.']];
        }
    }

    // READ - Voir l'historique du profil
    public function getProfileHistory(int $userId): array {
        return $this->getProfileHistoryFromDB($userId);
    }

    // READ - Récupérer le profil complet
    public function getCompleteProfile(int $userId): array {
        $user = $this->getUserByIdFromDB($userId);
        if (!$user) {
            return ['success' => false, 'errors' => ['Utilisateur non trouvé.']];
        }

        $profilHistory = $this->getProfileHistoryFromDB($userId);
        $currentProfil = $this->getCurrentProfilByUser($userId);

        return [
            'success' => true,
            'user' => $user,
            'current_profil' => $currentProfil,
            'history' => $profilHistory
        ];
    }

    // READ - Récupérer tous les profils avec utilisateurs
    public function getAllProfilsWithUsers(): array {
        return $this->getAllProfilsWithUsersFromDB();
    }

    // Méthodes de validation
    private function validateRegistration(array $data): array {
        $errors = [];

        if (empty($data['prenom']) || !$this->validateName($data['prenom'])) {
            $errors[] = "Le prénom doit contenir entre 2 et 50 caractères alphabétiques.";
        }

        if (empty($data['nom']) || !$this->validateName($data['nom'])) {
            $errors[] = "Le nom doit contenir entre 2 et 50 caractères alphabétiques.";
        }

        if (empty($data['birthdate']) || !$this->validateAge($data['birthdate'])) {
            $errors[] = "Vous devez avoir au moins 13 ans.";
        }

        if (empty($data['email']) || !$this->validateEmail($data['email'])) {
            $errors[] = "L'email n'est pas valide.";
        }

        if (empty($data['password']) || !$this->validatePassword($data['password'])) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        }

        if ($data['password'] !== $data['password2']) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        return $errors;
    }

    private function validateUpdate(array $data): array {
        $errors = [];

        if (empty($data['prenom']) || !$this->validateName($data['prenom'])) {
            $errors[] = "Le prénom doit contenir entre 2 et 50 caractères alphabétiques.";
        }

        if (empty($data['nom']) || !$this->validateName($data['nom'])) {
            $errors[] = "Le nom doit contenir entre 2 et 50 caractères alphabétiques.";
        }

        if (empty($data['birthdate']) || !$this->validateAge($data['birthdate'])) {
            $errors[] = "Vous devez avoir au moins 13 ans.";
        }

        if (empty($data['email']) || !$this->validateEmail($data['email'])) {
            $errors[] = "L'email n'est pas valide.";
        }

        if (!empty($data['password']) && !$this->validatePassword($data['password'])) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        }

        return $errors;
    }

    private function validateProfile(array $data): array {
        $errors = [];

        if (empty($data['prenom']) || !$this->validateName($data['prenom'])) {
            $errors[] = "Le prénom doit contenir entre 2 et 50 caractères alphabétiques.";
        }

        if (empty($data['nom']) || !$this->validateName($data['nom'])) {
            $errors[] = "Le nom doit contenir entre 2 et 50 caractères alphabétiques.";
        }

        if (empty($data['birthdate']) || !$this->validateAge($data['birthdate'])) {
            $errors[] = "Vous devez avoir au moins 13 ans.";
        }

        if (empty($data['email']) || !$this->validateEmail($data['email'])) {
            $errors[] = "L'email n'est pas valide.";
        }

        if (strlen($data['bio']) > 500) {
            $errors[] = "La biographie ne doit pas dépasser 500 caractères.";
        }

        if (empty($data['statut'])) {
            $errors[] = "Le statut est obligatoire.";
        }

        if ($data['points'] < 0) {
            $errors[] = "Les points ne peuvent pas être négatifs.";
        }

        return $errors;
    }

    // Statistiques
    public function getStats(): array {
        return [
            'total_users' => $this->countUsers(),
            'admin_users' => $this->countAdmins(),
            'active_users' => $this->countActiveUsers()
        ];
    }

    // Méthodes de validation simples
    public function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePassword(string $password): bool {
        return strlen($password) >= 8 &&
               preg_match("/[A-Z]/", $password) &&
               preg_match("/[a-z]/", $password) &&
               preg_match("/[0-9]/", $password) &&
               preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password);
    }

    public function validateName(string $name): bool {
        return preg_match("/^[a-zA-ZÀ-ÿ\s\-]{2,50}$/", $name) === 1;
    }

    public function validateAge(string $birthdate): bool {
        $minAge = 13;
        $birthDate = new DateTime($birthdate);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        return $age >= $minAge;
    }

    // =============================================
    // MÉTHODES D'ACCÈS À LA BASE DE DONNÉES
    // =============================================

    private function emailExists(string $email): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
            $stmt->execute(['email' => $email]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getUserByEmail(string $email) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute(['email' => $email]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    private function updateUserPassword(int $userId, string $newPassword): bool {
        try {
            $pdo = config::getConnexion();
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                UPDATE user 
                SET password = :password 
                WHERE id_user = :id
            ");
            return $stmt->execute([
                'password' => $hash,
                'id' => $userId
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    private function getUserByIdFromDB(int $id) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE id_user = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    private function getAllUsersFromDB(): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user ORDER BY nom, Prenom");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    private function deleteUserFromDB(int $id): bool {
        try {
            $pdo = config::getConnexion();
            
            // Commencer une transaction pour assurer l'intégrité des données
            $pdo->beginTransaction();
            
            // Supprimer d'abord les profils associés (cascade)
            $stmt = $pdo->prepare("DELETE FROM profils WHERE user_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Puis supprimer l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = :id");
            $result = $stmt->execute(['id' => $id]);
            
            // Valider la transaction
            $pdo->commit();
            
            return $result;
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            error_log("Erreur lors de la suppression: " . $e->getMessage());
            return false;
        }
    }

    private function countUsers(): int {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    private function countAdmins(): int {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE role = 'admin'");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    private function countActiveUsers(): int {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE points > 0");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    // Méthodes pour les profils
    private function saveProfil(Profil $profil): bool {
        try {
            $pdo = config::getConnexion();
            
            // Vérifier si un profil existe déjà pour cet utilisateur
            $existing = $this->getCurrentProfilByUser($profil->user_id);
            
            if ($existing) {
                // Mettre à jour le profil existant
                $stmt = $pdo->prepare("UPDATE profils SET statut = :statut, date_modification = :date_modification WHERE user_id = :user_id");
                return $stmt->execute([
                    'statut' => $profil->statut,
                    'date_modification' => $profil->date_modification,
                    'user_id' => $profil->user_id
                ]);
            } else {
                // Créer un nouveau profil
                $stmt = $pdo->prepare("INSERT INTO profils (user_id, statut, date_modification) VALUES (:user_id, :statut, :date_modification)");
                return $stmt->execute([
                    'user_id' => $profil->user_id,
                    'statut' => $profil->statut,
                    'date_modification' => $profil->date_modification
                ]);
            }
        } catch (Exception $e) {
            error_log("Erreur sauvegarde profil: " . $e->getMessage());
            return false;
        }
    }

    private function getProfileHistoryFromDB(int $user_id): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM profils WHERE user_id = :user_id ORDER BY date_modification DESC");
            $stmt->execute(['user_id' => $user_id]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur récupération historique profil: " . $e->getMessage());
            return [];
        }
    }

    private function getCurrentProfilByUser(int $user_id) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM profils WHERE user_id = :user_id ORDER BY date_modification DESC LIMIT 1");
            $stmt->execute(['user_id' => $user_id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Erreur récupération profil actuel: " . $e->getMessage());
            return null;
        }
    }

    private function getAllProfilsWithUsersFromDB(): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                SELECT p.*, u.Prenom, u.nom, u.email 
                FROM profils p 
                INNER JOIN user u ON p.user_id = u.id_user 
                ORDER BY p.date_modification DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur récupération profils avec users: " . $e->getMessage());
            return [];
        }
    }

    private function deleteProfilFromDB(int $profil_id): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM profils WHERE id_profil = :id");
            return $stmt->execute(['id' => $profil_id]);
        } catch (Exception $e) {
            error_log("Erreur suppression profil: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour gérer l'upload de la photo de profil
    private function handlePhotoUpload(array $file, int $userId): array {
        $uploadDir = __DIR__ . '/../assets/images/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Vérifier le type de fichier
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $fileType = $file['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.'];
        }

        // Vérifier la taille (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'L\'image est trop volumineuse (max 5MB).'];
        }

        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'user_' . $userId . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;

        // Déplacer le fichier uploadé
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => true,
                'filename' => $fileName
            ];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de l\'upload de l\'image.'];
        }
    }
    
    // Méthode pour gérer l'upload des fichiers audio
    private function handleAudioUpload(array $file, int $userId): array {
        $uploadDir = __DIR__ . '/../uploads/bio_audio/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Vérifier le type de fichier
        $allowedTypes = ['audio/webm', 'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'];
        $fileType = $file['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier audio non autorisé.'];
        }

        // Vérifier la taille (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Le fichier audio est trop volumineux (max 5MB).'];
        }

        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (empty($extension)) {
            $extension = 'webm'; // Par défaut pour les enregistrements web
        }
        $fileName = 'bio_' . $userId . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;

        // Déplacer le fichier uploadé
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => true,
                'path' => 'uploads/bio_audio/' . $fileName
            ];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de l\'upload du fichier audio.'];
        }
    }

    // =============================================
    // MÉTIERS AVANCÉS
    // =============================================

    /**
     * Mettre à jour les fonctionnalités avancées (statut, localisation, 2FA)
     */
    private function updateAdvancedFeatures(int $userId, array $data): bool {
        try {
            $pdo = config::getConnexion();
            
            $sql = "UPDATE user SET 
                    status = :status,
                    status_message = :status_message,
                    last_activity = NOW(),
                    is_online = :is_online,
                    location_country = :location_country,
                    location_city = :location_city,
                    location_latitude = :location_latitude,
                    location_longitude = :location_longitude,
                    location_timezone = :location_timezone,
                    location_public = :location_public,
                    two_factor_enabled = :two_factor_enabled,
                    two_factor_method = :two_factor_method
                    WHERE id_user = :user_id";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                'status' => $data['status'],
                'status_message' => $data['status_message'],
                'is_online' => $data['status'] === 'online' ? 1 : 0,
                'location_country' => $data['location_country'],
                'location_city' => $data['location_city'],
                'location_latitude' => $data['location_latitude'],
                'location_longitude' => $data['location_longitude'],
                'location_timezone' => $data['location_timezone'],
                'location_public' => $data['location_public'] ? 1 : 0,
                'two_factor_enabled' => $data['two_factor_enabled'] ? 1 : 0,
                'two_factor_method' => $data['two_factor_method'],
                'user_id' => $userId
            ]);
        } catch (Exception $e) {
            error_log("Erreur updateAdvancedFeatures: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ping pour maintenir le statut en ligne
     */
    public function pingStatus(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false];
        }

        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("UPDATE user SET last_activity = NOW(), is_online = 1 WHERE id_user = :user_id");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }

    // Méthode pour corriger l'admin
    public static function fixAdminUser(): bool {
        try {
            $pdo = config::getConnexion();
            
            // Vérifier si l'admin existe
            $stmt = $pdo->prepare("SELECT * FROM user WHERE email = 'admin@aidforpeace.org'");
            $stmt->execute();
            $admin = $stmt->fetch();
            
            if ($admin) {
                // Mettre à jour l'admin existant
                $hash = password_hash('Admin123!', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    UPDATE user SET 
                        password = :password,
                        role = 'admin',
                        points = 1000,
                        badges = 'admin',
                        rank = 'gold',
                        niveau = 10
                    WHERE email = 'admin@aidforpeace.org'
                ");
                $result = $stmt->execute(['password' => $hash]);
                
                // Créer le profil admin
                $profil = new Profil($admin['id_user'], 'Administrateur système', date('Y-m-d H:i:s'));
                $userController = new UserController();
                $userController->saveProfil($profil);
                
                return $result;
            } else {
                // Créer l'admin
                $hash = password_hash('Admin123!', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO user (Prenom, nom, email, password, birthdate, role, points, photo, bio, badges, rank, niveau) 
                    VALUES ('Admin', 'System', 'admin@aidforpeace.org', :password, '1990-01-01', 'admin', 1000, 'admin.jpg', 'Administrateur principal', 'admin', 'gold', 10)
                ");
                $result = $stmt->execute(['password' => $hash]);
                
                if ($result) {
                    $adminId = $pdo->lastInsertId();
                    $profil = new Profil($adminId, 'Administrateur système', date('Y-m-d H:i:s'));
                    $userController = new UserController();
                    $userController->saveProfil($profil);
                }
                
                return $result;
            }
        } catch (Exception $e) {
            error_log("Erreur correction admin: " . $e->getMessage());
            return false;
        }
    }

    // =============================================
    // GESTION DES COMPÉTENCES
    // =============================================

    /**
     * Ajouter une compétence à un utilisateur
     */
    public function addSkill(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Méthode non autorisée.'];
        }

        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Utilisateur non connecté.'];
        }

        require_once(__DIR__ . '/../models/UserSkill.php');

        $data = [
            'skill_name' => trim($_POST['skill_name'] ?? ''),
            'skill_level' => intval($_POST['skill_level'] ?? 1),
            'skill_category' => trim($_POST['skill_category'] ?? 'general'),
            'skill_description' => trim($_POST['skill_description'] ?? ''),
            'years_experience' => intval($_POST['years_experience'] ?? 0),
            'is_certified' => isset($_POST['is_certified']) && $_POST['is_certified'] === '1'
        ];

        // Validation
        if (empty($data['skill_name'])) {
            return ['success' => false, 'error' => 'Le nom de la compétence est requis.'];
        }

        if ($data['skill_level'] < 1 || $data['skill_level'] > 5) {
            return ['success' => false, 'error' => 'Le niveau doit être entre 1 et 5.'];
        }

        $skill = new UserSkill(
            $_SESSION['user_id'],
            $data['skill_name'],
            $data['skill_level'],
            $data['skill_category'],
            $data['skill_description'],
            $data['years_experience'],
            $data['is_certified']
        );

        if ($skill->create()) {
            return [
                'success' => true,
                'message' => 'Compétence ajoutée avec succès!',
                'skill_id' => $skill->id_skill
            ];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de l\'ajout de la compétence.'];
        }
    }

    /**
     * Modifier une compétence
     */
    public function updateSkill(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Méthode non autorisée.'];
        }

        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Utilisateur non connecté.'];
        }

        require_once(__DIR__ . '/../models/UserSkill.php');

        $skillId = intval($_POST['skill_id'] ?? 0);
        if ($skillId <= 0) {
            return ['success' => false, 'error' => 'ID de compétence invalide.'];
        }

        $data = [
            'skill_name' => trim($_POST['skill_name'] ?? ''),
            'skill_level' => intval($_POST['skill_level'] ?? 1),
            'skill_category' => trim($_POST['skill_category'] ?? 'general'),
            'skill_description' => trim($_POST['skill_description'] ?? ''),
            'years_experience' => intval($_POST['years_experience'] ?? 0),
            'is_certified' => isset($_POST['is_certified']) && $_POST['is_certified'] === '1'
        ];

        // Validation
        if (empty($data['skill_name'])) {
            return ['success' => false, 'error' => 'Le nom de la compétence est requis.'];
        }

        if ($data['skill_level'] < 1 || $data['skill_level'] > 5) {
            return ['success' => false, 'error' => 'Le niveau doit être entre 1 et 5.'];
        }

        $skill = new UserSkill(
            $_SESSION['user_id'],
            $data['skill_name'],
            $data['skill_level'],
            $data['skill_category'],
            $data['skill_description'],
            $data['years_experience'],
            $data['is_certified']
        );
        $skill->id_skill = $skillId;

        if ($skill->update()) {
            return ['success' => true, 'message' => 'Compétence mise à jour avec succès!'];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de la mise à jour de la compétence.'];
        }
    }

    /**
     * Supprimer une compétence
     */
    public function deleteSkill(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Méthode non autorisée.'];
        }

        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Utilisateur non connecté.'];
        }

        require_once(__DIR__ . '/../models/UserSkill.php');

        $skillId = intval($_POST['skill_id'] ?? 0);
        if ($skillId <= 0) {
            return ['success' => false, 'error' => 'ID de compétence invalide.'];
        }

        $skill = new UserSkill();
        $skill->id_skill = $skillId;
        $skill->user_id = $_SESSION['user_id'];

        if ($skill->delete()) {
            return ['success' => true, 'message' => 'Compétence supprimée avec succès!'];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de la suppression de la compétence.'];
        }
    }

    /**
     * Récupérer les compétences d'un utilisateur
     */
    public function getUserSkills(int $userId): array {
        require_once(__DIR__ . '/../models/UserSkill.php');
        return UserSkill::getUserSkills($userId);
    }

    /**
     * Récupérer les compétences groupées par catégorie
     */
    public function getUserSkillsByCategory(int $userId): array {
        require_once(__DIR__ . '/../models/UserSkill.php');
        return UserSkill::getUserSkillsByCategory($userId);
    }

    /**
     * Récupérer les catégories de compétences
     */
    public function getSkillCategories(): array {
        require_once(__DIR__ . '/../models/UserSkill.php');
        return UserSkill::getCategories();
    }

    /**
     * Récupérer les suggestions de compétences
     */
    public function getSkillSuggestions(string $search = ''): array {
        require_once(__DIR__ . '/../models/UserSkill.php');
        return UserSkill::getSuggestions($search);
    }

    /**
     * Récupérer les statistiques des compétences
     */
    public function getUserSkillsStats(int $userId): array {
        require_once(__DIR__ . '/../models/UserSkill.php');
        return UserSkill::getUserSkillsStats($userId);
    }

    // =============================================
    // VÉRIFICATION EMAIL
    // =============================================

    /**
     * Vérifier un code de vérification email
     */
    public function verifyEmail(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Méthode non autorisée.'];
        }

        if (!isset($GLOBALS['emailVerificationController'])) {
            return ['success' => false, 'error' => 'Service de vérification non disponible.'];
        }

        $result = $GLOBALS['emailVerificationController']->verifyCodeFromPost();
        
        return [
            'success' => $result['success'],
            'error' => $result['success'] ? '' : $result['message'],
            'message' => $result['success'] ? $result['message'] : '',
            'user_id' => $result['user_id'] ?? 0
        ];
    }

    /**
     * Vérifier un token de vérification email (depuis un lien)
     */
    public function verifyEmailToken(string $token): array {
        if (!isset($GLOBALS['emailVerificationController'])) {
            return ['success' => false, 'error' => 'Service de vérification non disponible.'];
        }

        $result = $GLOBALS['emailVerificationController']->verifyToken($token);
        
        return [
            'success' => $result['success'],
            'error' => $result['success'] ? '' : $result['message'],
            'message' => $result['success'] ? $result['message'] : '',
            'user_id' => $result['user_id'] ?? 0
        ];
    }

    /**
     * Renvoyer un code de vérification
     */
    public function resendVerificationCode(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Méthode non autorisée.'];
        }

        if (!isset($GLOBALS['emailVerificationController'])) {
            return ['success' => false, 'error' => 'Service de vérification non disponible.'];
        }

        $result = $GLOBALS['emailVerificationController']->resendCode();
        
        return [
            'success' => $result['success'],
            'error' => $result['success'] ? '' : $result['message'],
            'message' => $result['success'] ? $result['message'] : ''
        ];
    }

    /**
     * Récupérer les compétences de l'utilisateur connecté
     */
    public function get_user_skills(): void {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([]);
            exit;
        }

        require_once 'models/UserSkill.php';
        $userSkill = new UserSkill();
        $skills = $userSkill->getUserSkills($_SESSION['user_id']);
        
        echo json_encode($skills);
        exit;
    }

    /**
     * Ajouter une compétence
     */
    public function add_skill(): void {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Non connecté']);
            exit;
        }

        $skillName = trim($_POST['skill_name'] ?? '');
        $skillLevel = intval($_POST['skill_level'] ?? 3);
        $skillCategory = trim($_POST['skill_category'] ?? 'Technique');
        $yearsExperience = intval($_POST['years_experience'] ?? 0);
        $isCertified = isset($_POST['is_certified']) && $_POST['is_certified'] == '1' ? 1 : 0;

        if (empty($skillName)) {
            echo json_encode(['success' => false, 'error' => 'Le nom de la compétence est requis']);
            exit;
        }

        require_once 'models/UserSkill.php';
        $userSkill = new UserSkill();
        
        $result = $userSkill->addSkill(
            $_SESSION['user_id'],
            $skillName,
            $skillLevel,
            $skillCategory,
            $yearsExperience,
            $isCertified
        );

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Compétence ajoutée avec succès']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'ajout de la compétence']);
        }
        exit;
    }

    /**
     * Supprimer une compétence
     */
    public function delete_skill(): void {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Non connecté']);
            exit;
        }

        $skillId = intval($_POST['skill_id'] ?? 0);

        if ($skillId <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de compétence invalide']);
            exit;
        }

        require_once 'models/UserSkill.php';
        $userSkill = new UserSkill();
        
        $result = $userSkill->deleteSkill($skillId, $_SESSION['user_id']);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Compétence supprimée avec succès']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
        }
        exit;
    }

    /**
     * Obtenir des suggestions de compétences
     */
    public function get_skill_suggestions(): void {
        header('Content-Type: application/json');
        
        $search = trim($_GET['search'] ?? '');
        
        if (strlen($search) < 2) {
            echo json_encode([]);
            exit;
        }

        require_once 'models/UserSkill.php';
        $userSkill = new UserSkill();
        $suggestions = $userSkill->getSkillSuggestions($search);
        
        echo json_encode($suggestions);
        exit;
    }
}
