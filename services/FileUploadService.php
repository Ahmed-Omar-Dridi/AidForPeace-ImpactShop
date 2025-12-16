<?php
/**
 * FileUploadService - Gestion des uploads de fichiers
 */
class FileUploadService {
    private string $uploadDir;
    
    public function __construct(string $uploadDir = 'uploads/') {
        $this->uploadDir = __DIR__ . '/../' . $uploadDir;
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload d'une photo de profil
     */
    public function uploadPhoto(array $file, int $userId): array {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Le fichier est trop volumineux (max 5MB).'];
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
        $destination = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'error' => 'Erreur lors de l\'upload du fichier.'];
    }
    
    /**
     * Upload d'un fichier audio
     */
    public function uploadAudio(array $file, int $userId): array {
        $allowedTypes = ['audio/webm', 'audio/mpeg', 'audio/wav', 'audio/ogg'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier audio non autorisé.'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Le fichier audio est trop volumineux (max 10MB).'];
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'bio_audio_' . $userId . '_' . time() . '.' . $extension;
        $destination = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'path' => 'uploads/' . $filename];
        }
        
        return ['success' => false, 'error' => 'Erreur lors de l\'upload du fichier audio.'];
    }
    
    /**
     * Supprimer un fichier
     */
    public function deleteFile(string $filename): bool {
        $filepath = $this->uploadDir . $filename;
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
}
