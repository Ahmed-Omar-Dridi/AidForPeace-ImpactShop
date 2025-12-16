<?php
require_once(__DIR__ . '/../config/config.php');

class User
{
    public int $id;
    private string $nom;
    private string $prenom;
    private string $datenaissance;
    private string $email;
    private string $role;
    private string $password;
    private int $niveau;
    public string $bio;
    public string $bio_type; // 'text' ou 'audio'
    public string $bio_audio_path; // chemin vers le fichier audio
    public int $pointtotal;
    public string $photo;
    public string $badge;
    public string $rank;
    private string $facial_data;
    private string $facial_descriptor;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    public function __construct(
        string $nom = '', 
        string $prenom = '', 
        string $datenaissance = '', 
        string $email = '', 
        string $password = '', 
        string $role = self::ROLE_USER, 
        int $niveau = 1, 
        int $pointtotal = 0, 
        string $photo = 'default.jpg', 
        string $badge = 'beginner', 
        string $rank = 'bronze', 
        string $bio = '',
        string $bio_type = 'text',
        string $bio_audio_path = '',
        string $facial_data = '',
        string $facial_descriptor = ''
    ) {
        $this->id = 0;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->datenaissance = $datenaissance;
        $this->email = $email;
        $this->password = $password;
        $this->niveau = $niveau;
        $this->pointtotal = $pointtotal;
        $this->photo = $photo;
        $this->badge = $badge;
        $this->role = $role;
        $this->rank = $rank;
        $this->bio = $bio;
        $this->bio_type = $bio_type;
        $this->bio_audio_path = $bio_audio_path;
        $this->facial_data = $facial_data;
        $this->facial_descriptor = $facial_descriptor;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getDatenaissance(): string { return $this->datenaissance; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getNiveau(): int { return $this->niveau; }
    public function getRole(): string { return $this->role; }
    public function getBio(): string { return $this->bio; }
    public function getBioType(): string { return $this->bio_type; }
    public function getBioAudioPath(): string { return $this->bio_audio_path; }
    public function getPointtotal(): int { return $this->pointtotal; }
    public function getPhoto(): string { return $this->photo; }
    public function getBadge(): string { return $this->badge; }
    public function getRank(): string { return $this->rank; }
    public function getFacialData(): string { return $this->facial_data; }
    public function getFacialDescriptor(): string { return $this->facial_descriptor; }

    // Setters
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setDatenaissance(string $datenaissance): void { $this->datenaissance = $datenaissance; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setNiveau(int $niveau): void { $this->niveau = $niveau; }
    public function setRole(string $role): void { $this->role = $role; }
    public function setBio(string $bio): void { $this->bio = $bio; }
    public function setBioType(string $bio_type): void { $this->bio_type = $bio_type; }
    public function setBioAudioPath(string $bio_audio_path): void { $this->bio_audio_path = $bio_audio_path; }
    public function setPointtotal(int $pointtotal): void { $this->pointtotal = $pointtotal; }
    public function setPhoto(string $photo): void { $this->photo = $photo; }
    public function setBadge(string $badge): void { $this->badge = $badge; }
    public function setRank(string $rank): void { $this->rank = $rank; }
    public function setFacialData(string $facial_data): void { $this->facial_data = $facial_data; }
    public function setFacialDescriptor(string $facial_descriptor): void { $this->facial_descriptor = $facial_descriptor; }

    // CRUD Methods
    public function create(): bool {
        try {
            $pdo = config::getConnexion();
            $hash = password_hash($this->password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO user (Prenom, nom, email, password, birthdate, role, points, photo, bio, bio_type, bio_audio_path, badges, rank, niveau, facial_data, facial_descriptor) 
                    VALUES (:Prenom, :nom, :email, :password, :birthdate, :role, :points, :photo, :bio, :bio_type, :bio_audio_path, :badges, :rank, :niveau, :facial_data, :facial_descriptor)";
            
            $stmt = $pdo->prepare($sql);
            
            $result = $stmt->execute([
                'Prenom' => $this->prenom,
                'nom' => $this->nom,
                'email' => $this->email,
                'password' => $hash,
                'birthdate' => $this->datenaissance,
                'role' => $this->role,
                'points' => $this->pointtotal,
                'photo' => $this->photo,
                'bio' => $this->bio,
                'bio_type' => $this->bio_type,
                'bio_audio_path' => $this->bio_audio_path,
                'badges' => $this->badge,
                'rank' => $this->rank,
                'niveau' => $this->niveau,
                'facial_data' => $this->facial_data,
                'facial_descriptor' => $this->facial_descriptor
            ]);

            if ($result) {
                $this->id = $pdo->lastInsertId();
            }

            return $result;
        } catch (Exception $e) {
            error_log("Erreur lors de l'inscription: " . $this->prenom . " " . $this->nom . " - " . $e->getMessage());
            return false;
        }
    }

    public function update(): bool {
        try {
            $pdo = config::getConnexion();
            
            if (!empty($this->password)) {
                $hash = password_hash($this->password, PASSWORD_DEFAULT);
                
                $sql = "UPDATE user SET 
                    Prenom = :Prenom, nom = :nom, email = :email, password = :password, 
                    birthdate = :birthdate, role = :role, points = :points, photo = :photo, 
                    bio = :bio, bio_type = :bio_type, bio_audio_path = :bio_audio_path, badges = :badges, rank = :rank, niveau = :niveau,
                    facial_data = :facial_data, facial_descriptor = :facial_descriptor
                    WHERE id_user = :id";
                    
                $params = [
                    'Prenom' => $this->prenom,
                    'nom' => $this->nom,
                    'email' => $this->email,
                    'password' => $hash,
                    'birthdate' => $this->datenaissance,
                    'role' => $this->role,
                    'points' => $this->pointtotal,
                    'photo' => $this->photo,
                    'bio' => $this->bio,
                    'bio_type' => $this->bio_type,
                    'bio_audio_path' => $this->bio_audio_path,
                    'badges' => $this->badge,
                    'rank' => $this->rank,
                    'niveau' => $this->niveau,
                    'facial_data' => $this->facial_data,
                    'facial_descriptor' => $this->facial_descriptor,
                    'id' => $this->id
                ];
            } else {
                $sql = "UPDATE user SET 
                    Prenom = :Prenom, nom = :nom, email = :email, 
                    birthdate = :birthdate, role = :role, points = :points, photo = :photo, 
                    bio = :bio, bio_type = :bio_type, bio_audio_path = :bio_audio_path, badges = :badges, rank = :rank, niveau = :niveau,
                    facial_data = :facial_data, facial_descriptor = :facial_descriptor
                    WHERE id_user = :id";
                    
                $params = [
                    'Prenom' => $this->prenom,
                    'nom' => $this->nom,
                    'email' => $this->email,
                    'birthdate' => $this->datenaissance,
                    'role' => $this->role,
                    'points' => $this->pointtotal,
                    'photo' => $this->photo,
                    'bio' => $this->bio,
                    'bio_type' => $this->bio_type,
                    'bio_audio_path' => $this->bio_audio_path,
                    'badges' => $this->badge,
                    'rank' => $this->rank,
                    'niveau' => $this->niveau,
                    'facial_data' => $this->facial_data,
                    'facial_descriptor' => $this->facial_descriptor,
                    'id' => $this->id
                ];
            }

            $stmt = $pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour user ID {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour récupérer un utilisateur par email
    public static function getByEmail(string $email): ?User {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $data = $stmt->fetch();
            
            if ($data) {
                $user = new User(
                    $data['nom'],
                    $data['Prenom'],
                    $data['birthdate'],
                    $data['email'],
                    '',
                    $data['role'],
                    $data['niveau'],
                    $data['points'],
                    $data['photo'],
                    $data['badges'],
                    $data['rank'],
                    $data['bio'],
                    $data['bio_type'] ?? 'text',
                    $data['bio_audio_path'] ?? '',
                    $data['facial_data'] ?? '',
                    $data['facial_descriptor'] ?? ''
                );
                $user->id = $data['id_user'];
                return $user;
            }
            return null;
        } catch (Exception $e) {
            error_log("Erreur récupération user par email {$email}: " . $e->getMessage());
            return null;
        }
    }

    // Nouvelle méthode pour récupérer par ID avec données faciales
    public static function getByIdWithFacialData(int $id): ?User {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE id_user = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch();
            
            if ($data) {
                $user = new User(
                    $data['nom'],
                    $data['Prenom'],
                    $data['birthdate'],
                    $data['email'],
                    '',
                    $data['role'],
                    $data['niveau'],
                    $data['points'],
                    $data['photo'],
                    $data['badges'],
                    $data['rank'],
                    $data['bio'],
                    $data['bio_type'] ?? 'text',
                    $data['bio_audio_path'] ?? '',
                    $data['facial_data'] ?? '',
                    $data['facial_descriptor'] ?? ''
                );
                $user->id = $data['id_user'];
                return $user;
            }
            return null;
        } catch (Exception $e) {
            error_log("Erreur récupération user par ID {$id}: " . $e->getMessage());
            return null;
        }
    }
}
?>