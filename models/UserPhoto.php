<?php
require_once(__DIR__ . '/../config/config.php');

class UserPhoto {
    public int $id_photo;
    public int $user_id;
    public ?int $album_id;
    public ?string $photo_title;
    public ?string $photo_description;
    public string $photo_path;
    public ?string $photo_thumbnail;
    public ?int $file_size;
    public ?string $mime_type;
    public ?int $width;
    public ?int $height;
    public bool $is_profile_photo;
    public bool $is_public;
    public int $views_count;
    public int $likes_count;
    public int $display_order;
    public string $uploaded_at;

    public function __construct(
        int $user_id = 0,
        string $photo_path = '',
        ?int $album_id = null,
        ?string $photo_title = null,
        ?string $photo_description = null
    ) {
        $this->id_photo = 0;
        $this->user_id = $user_id;
        $this->album_id = $album_id;
        $this->photo_title = $photo_title;
        $this->photo_description = $photo_description;
        $this->photo_path = $photo_path;
        $this->photo_thumbnail = null;
        $this->file_size = null;
        $this->mime_type = null;
        $this->width = null;
        $this->height = null;
        $this->is_profile_photo = false;
        $this->is_public = true;
        $this->views_count = 0;
        $this->likes_count = 0;
        $this->display_order = 0;
        $this->uploaded_at = date('Y-m-d H:i:s');
    }

    /**
     * Créer une photo
     */
    public function create(): bool {
        try {
            $pdo = config::getConnexion();
            
            $sql = "INSERT INTO user_photos 
                    (user_id, album_id, photo_title, photo_description, photo_path, 
                     photo_thumbnail, file_size, mime_type, width, height, 
                     is_profile_photo, is_public, display_order) 
                    VALUES 
                    (:user_id, :album_id, :photo_title, :photo_description, :photo_path,
                     :photo_thumbnail, :file_size, :mime_type, :width, :height,
                     :is_profile_photo, :is_public, :display_order)";
            
            $stmt = $pdo->prepare($sql);
            
            $result = $stmt->execute([
                'user_id' => $this->user_id,
                'album_id' => $this->album_id,
                'photo_title' => $this->photo_title,
                'photo_description' => $this->photo_description,
                'photo_path' => $this->photo_path,
                'photo_thumbnail' => $this->photo_thumbnail,
                'file_size' => $this->file_size,
                'mime_type' => $this->mime_type,
                'width' => $this->width,
                'height' => $this->height,
                'is_profile_photo' => $this->is_profile_photo ? 1 : 0,
                'is_public' => $this->is_public ? 1 : 0,
                'display_order' => $this->display_order
            ]);

            if ($result) {
                $this->id_photo = $pdo->lastInsertId();
            }

            return $result;
        } catch (Exception $e) {
            error_log("Erreur création photo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer une photo
     */
    public function delete(): bool {
        try {
            $pdo = config::getConnexion();
            
            // Supprimer les fichiers physiques
            if (file_exists($this->photo_path)) {
                unlink($this->photo_path);
            }
            if ($this->photo_thumbnail && file_exists($this->photo_thumbnail)) {
                unlink($this->photo_thumbnail);
            }
            
            $stmt = $pdo->prepare("DELETE FROM user_photos WHERE id_photo = :id_photo");
            return $stmt->execute(['id_photo' => $this->id_photo]);
        } catch (Exception $e) {
            error_log("Erreur suppression photo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les photos d'un utilisateur
     */
    public static function getUserPhotos(int $userId, ?int $albumId = null, bool $publicOnly = false): array {
        try {
            $pdo = config::getConnexion();
            
            $sql = "SELECT * FROM user_photos WHERE user_id = :user_id";
            $params = ['user_id' => $userId];
            
            if ($albumId !== null) {
                $sql .= " AND album_id = :album_id";
                $params['album_id'] = $albumId;
            }
            
            if ($publicOnly) {
                $sql .= " AND is_public = 1";
            }
            
            $sql .= " ORDER BY display_order ASC, uploaded_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération photos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Incrémenter les vues
     */
    public static function incrementViews(int $photoId): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("UPDATE user_photos SET views_count = views_count + 1 WHERE id_photo = :id_photo");
            return $stmt->execute(['id_photo' => $photoId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Liker/Unliker une photo
     */
    public static function toggleLike(int $photoId, int $userId): array {
        try {
            $pdo = config::getConnexion();
            
            // Vérifier si déjà liké
            $stmt = $pdo->prepare("SELECT id_like FROM photo_likes WHERE photo_id = :photo_id AND user_id = :user_id");
            $stmt->execute(['photo_id' => $photoId, 'user_id' => $userId]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Unliker
                $stmt = $pdo->prepare("DELETE FROM photo_likes WHERE photo_id = :photo_id AND user_id = :user_id");
                $stmt->execute(['photo_id' => $photoId, 'user_id' => $userId]);
                
                $stmt = $pdo->prepare("UPDATE user_photos SET likes_count = likes_count - 1 WHERE id_photo = :id_photo");
                $stmt->execute(['id_photo' => $photoId]);
                
                return ['success' => true, 'liked' => false];
            } else {
                // Liker
                $stmt = $pdo->prepare("INSERT INTO photo_likes (photo_id, user_id) VALUES (:photo_id, :user_id)");
                $stmt->execute(['photo_id' => $photoId, 'user_id' => $userId]);
                
                $stmt = $pdo->prepare("UPDATE user_photos SET likes_count = likes_count + 1 WHERE id_photo = :id_photo");
                $stmt->execute(['id_photo' => $photoId]);
                
                return ['success' => true, 'liked' => true];
            }
        } catch (Exception $e) {
            error_log("Erreur toggle like: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Upload et traitement d'image
     */
    public static function uploadPhoto(array $file, int $userId, ?int $albumId = null): array {
        $uploadDir = __DIR__ . '/../uploads/photos/';
        $thumbDir = __DIR__ . '/../uploads/photos/thumbnails/';
        
        // Créer les dossiers s'ils n'existent pas
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);
        
        // Vérifier le type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé'];
        }
        
        // Vérifier la taille (max 10MB)
        $maxSize = 10 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Fichier trop volumineux (max 10MB)'];
        }
        
        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'photo_' . $userId . '_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        $thumbpath = $thumbDir . 'thumb_' . $filename;
        
        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'error' => 'Erreur lors de l\'upload'];
        }
        
        // Créer la miniature
        // Miniature désactivée (GD non disponible)
        if (extension_loaded('gd')) {
            self::createThumbnail($filepath, $thumbpath, 300, 300);
        } else {
            // Copier l'image originale comme miniature
            @copy($filepath, $thumbpath);
        }
        
        // Récupérer les dimensions
        list($width, $height) = getimagesize($filepath);
        
        // Créer l'objet photo
        $photo = new UserPhoto($userId, 'uploads/photos/' . $filename, $albumId);
        $photo->photo_thumbnail = 'uploads/photos/thumbnails/thumb_' . $filename;
        $photo->file_size = $file['size'];
        $photo->mime_type = $file['type'];
        $photo->width = $width;
        $photo->height = $height;
        
        if ($photo->create()) {
            return [
                'success' => true,
                'photo_id' => $photo->id_photo,
                'photo_path' => $photo->photo_path,
                'thumbnail' => $photo->photo_thumbnail
            ];
        }
        
        return ['success' => false, 'error' => 'Erreur lors de l\'enregistrement'];
    }

    /**
     * Créer une miniature
     */
    private static function createThumbnail(string $source, string $dest, int $maxWidth, int $maxHeight): bool {
        try {
            list($width, $height, $type) = getimagesize($source);
            
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int)($width * $ratio);
            $newHeight = (int)($height * $ratio);
            
            $thumb = imagecreatetruecolor($newWidth, $newHeight);
            
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($source);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($source);
                    imagealphablending($thumb, false);
                    imagesavealpha($thumb, true);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($source);
                    break;
                default:
                    return false;
            }
            
            imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            imagejpeg($thumb, $dest, 85);
            imagedestroy($thumb);
            imagedestroy($image);
            
            return true;
        } catch (Exception $e) {
            error_log("Erreur création miniature: " . $e->getMessage());
            return false;
        }
    }
}
?>
