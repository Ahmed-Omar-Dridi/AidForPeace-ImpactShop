<?php
/**
 * ValidationService - Validation des données
 */
class ValidationService {
    
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
    
    public function validateAge(string $birthdate, int $minAge = 13): bool {
        $birthDate = new DateTime($birthdate);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        return $age >= $minAge;
    }
    
    public function validateRegistration(array $data): array {
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
    
    public function validateProfile(array $data): array {
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

        if (isset($data['bio']) && strlen($data['bio']) > 500) {
            $errors[] = "La biographie ne doit pas dépasser 500 caractères.";
        }

        if (isset($data['points']) && $data['points'] < 0) {
            $errors[] = "Les points ne peuvent pas être négatifs.";
        }

        return $errors;
    }
    
    public function validateBase64Image(string $base64): bool {
        if (preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $base64)) {
            return true;
        }
        return false;
    }
}
