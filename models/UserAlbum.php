<?php
require_once(__DIR__ . '/../config/config.php');

class UserAlbum {
    public int $id_album;
    public int $user_id;
    public string $album_name;
    public ?string $album_description;
    public ?string $cover_image;
    public bool $is_public;
    public int $display_order;
    public string $created_at;
    public string $updated_at;

    public function __construct(
        int $user_id = 0,
        string $album_name = '',
        ?string $album_description = null,
        bool $is_public = true
    ) {
        $this->id_album = 0;
        $this->user_id = $user_id;
        $this->album_name = $album_name;
        $this->album_description = $album_description;
        $this->cover_image = null;
        $this->is_public = $is_public;
        $this->display_order = 0;
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    /**
     * Créer un album
     */
    public function create(): bool {
        try {
            $pdo = config::getConnexion();
            
            $sql = "INSERT INTO user_albums 
                    (user_id, album_name, album_description, is_public, display_order) 
                    VALUES 
                    (:user_id, :album_name, :album_description, :is_public, :display_order)";
            
            $stmt = $pdo->prepare($sql);
            
            $result = $stmt->execute([
                'user_id' => $this->user_id,
                'album_name' => $this->album_name,
                'album_description' => $this->album_description,
                'is_public' => $this->is_public ? 1 : 0,
                'display_order' => $this->display_order
            ]);

            if ($result) {
                $this->id_album = $pdo->lastInsertId();
            }

            return $result;
        } catch (Exception $e) {
            error_log("Erreur création album: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour un album
     */
    public function update(): bool {
        try {
            $pdo = config::getConnexion();
            
            $sql = "UPDATE user_albums SET 
                    album_name = :album_name,
                    album_description = :album_description,
                    cover_image = :cover_image,
                    is_public = :is_public,
                    display_order = :display_order
                    WHERE id_album = :id_album";
            
            $stmt = $pdo->prepare($sql);
            
            return $stmt->execute([
                'album_name' => $this->album_name,
                'album_description' => $this->album_description,
                'cover_image' => $this->cover_image,
                'is_public' => $this->is_public ? 1 : 0,
                'display_order' => $this->display_order,
                'id_album' => $this->id_album
            ]);
        } catch (Exception $e) {
            error_log("Erreur mise à jour album: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un album
     */
    public function delete(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM user_albums WHERE id_album = :id_album");
            return $stmt->execute(['id_album' => $this->id_album]);
        } catch (Exception $e) {
            error_log("Erreur suppression album: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les albums d'un utilisateur
     */
    public static function getUserAlbums(int $userId, bool $publicOnly = false): array {
        try {
            $pdo = config::getConnexion();
            
            $sql = "SELECT a.*, COUNT(p.id_photo) as photo_count
                    FROM user_albums a
                    LEFT JOIN user_photos p ON a.id_album = p.album_id
                    WHERE a.user_id = :user_id";
            
            if ($publicOnly) {
                $sql .= " AND a.is_public = 1";
            }
            
            $sql .= " GROUP BY a.id_album ORDER BY a.display_order ASC, a.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération albums: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer un album par ID
     */
    public static function getAlbumById(int $albumId): ?array {
        try {
            $pdo = config::getConnexion();
            
            $stmt = $pdo->prepare("
                SELECT a.*, COUNT(p.id_photo) as photo_count
                FROM user_albums a
                LEFT JOIN user_photos p ON a.id_album = p.album_id
                WHERE a.id_album = :id_album
                GROUP BY a.id_album
            ");
            
            $stmt->execute(['id_album' => $albumId]);
            $album = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $album ?: null;
        } catch (Exception $e) {
            error_log("Erreur récupération album: " . $e->getMessage());
            return null;
        }
    }
}
?>
