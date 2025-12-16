<?php
require_once(__DIR__ . '/../models/UserAnalytics.php');
require_once(__DIR__ . '/../models/UserPhoto.php');
require_once(__DIR__ . '/../models/UserAlbum.php');
require_once(__DIR__ . '/../models/UserSearch.php');

class AdvancedFeaturesController {
    
    // =============================================
    // ANALYTICS / TABLEAU DE BORD
    // =============================================
    
    public function getDashboard(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $userId = $_SESSION['user_id'];
        
        return [
            'success' => true,
            'stats' => UserAnalytics::getGlobalStats($userId),
            'summary' => UserAnalytics::getStatsSummary($userId),
            'recent_activity' => UserAnalytics::getUserStats($userId, 7)
        ];
    }
    
    public function getChartData(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $userId = $_SESSION['user_id'];
        $activityType = $_GET['type'] ?? 'login';
        $days = intval($_GET['days'] ?? 30);
        
        $data = UserAnalytics::getChartData($userId, $activityType, $days);
        
        return ['success' => true, 'data' => $data];
    }
    
    // =============================================
    // GALERIE PHOTOS
    // =============================================
    
    public function uploadPhoto(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        if (!isset($_FILES['photo'])) {
            return ['success' => false, 'error' => 'Aucun fichier'];
        }
        
        $userId = $_SESSION['user_id'];
        $albumId = !empty($_POST['album_id']) ? intval($_POST['album_id']) : null;
        
        $result = UserPhoto::uploadPhoto($_FILES['photo'], $userId, $albumId);
        
        if ($result['success']) {
            // Log l'activité
            UserAnalytics::logActivity($userId, UserAnalytics::TYPE_PHOTO_UPLOAD);
        }
        
        return $result;
    }
    
    public function getPhotos(): array {
        $userId = intval($_GET['user_id'] ?? $_SESSION['user_id'] ?? 0);
        $albumId = !empty($_GET['album_id']) ? intval($_GET['album_id']) : null;
        $publicOnly = !isset($_SESSION['user_id']) || $_SESSION['user_id'] != $userId;
        
        $photos = UserPhoto::getUserPhotos($userId, $albumId, $publicOnly);
        
        return ['success' => true, 'photos' => $photos];
    }
    
    public function deletePhoto(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $photoId = intval($_POST['photo_id'] ?? 0);
        
        if ($photoId <= 0) {
            return ['success' => false, 'error' => 'ID invalide'];
        }
        
        // Vérifier que la photo appartient à l'utilisateur
        $photos = UserPhoto::getUserPhotos($_SESSION['user_id']);
        $found = false;
        $photoToDelete = null;
        
        foreach ($photos as $photo) {
            if ($photo['id_photo'] == $photoId) {
                $found = true;
                $photoToDelete = new UserPhoto();
                $photoToDelete->id_photo = $photoId;
                $photoToDelete->photo_path = $photo['photo_path'];
                $photoToDelete->photo_thumbnail = $photo['photo_thumbnail'];
                break;
            }
        }
        
        if (!$found) {
            return ['success' => false, 'error' => 'Photo non trouvée'];
        }
        
        if ($photoToDelete->delete()) {
            return ['success' => true, 'message' => 'Photo supprimée'];
        }
        
        return ['success' => false, 'error' => 'Erreur suppression'];
    }
    
    public function likePhoto(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $photoId = intval($_POST['photo_id'] ?? 0);
        
        if ($photoId <= 0) {
            return ['success' => false, 'error' => 'ID invalide'];
        }
        
        return UserPhoto::toggleLike($photoId, $_SESSION['user_id']);
    }
    
    // =============================================
    // ALBUMS
    // =============================================
    
    public function createAlbum(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $albumName = trim($_POST['album_name'] ?? '');
        $albumDescription = trim($_POST['album_description'] ?? '');
        $isPublic = isset($_POST['is_public']) && $_POST['is_public'] === '1';
        
        if (empty($albumName)) {
            return ['success' => false, 'error' => 'Nom requis'];
        }
        
        $album = new UserAlbum($_SESSION['user_id'], $albumName, $albumDescription, $isPublic);
        
        if ($album->create()) {
            return [
                'success' => true,
                'message' => 'Album créé',
                'album_id' => $album->id_album
            ];
        }
        
        return ['success' => false, 'error' => 'Erreur création'];
    }
    
    public function getAlbums(): array {
        $userId = intval($_GET['user_id'] ?? $_SESSION['user_id'] ?? 0);
        $publicOnly = !isset($_SESSION['user_id']) || $_SESSION['user_id'] != $userId;
        
        $albums = UserAlbum::getUserAlbums($userId, $publicOnly);
        
        return ['success' => true, 'albums' => $albums];
    }
    
    public function deleteAlbum(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $albumId = intval($_POST['album_id'] ?? 0);
        
        if ($albumId <= 0) {
            return ['success' => false, 'error' => 'ID invalide'];
        }
        
        // Vérifier que l'album appartient à l'utilisateur
        $albums = UserAlbum::getUserAlbums($_SESSION['user_id']);
        $found = false;
        
        foreach ($albums as $album) {
            if ($album['id_album'] == $albumId) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            return ['success' => false, 'error' => 'Album non trouvé'];
        }
        
        $albumObj = new UserAlbum();
        $albumObj->id_album = $albumId;
        
        if ($albumObj->delete()) {
            return ['success' => true, 'message' => 'Album supprimé'];
        }
        
        return ['success' => false, 'error' => 'Erreur suppression'];
    }
    
    // =============================================
    // RECHERCHE AVANCÉE
    // =============================================
    
    public function searchUsers(): array {
        $criteria = [
            'query' => trim($_GET['q'] ?? ''),
            'country' => trim($_GET['country'] ?? ''),
            'city' => trim($_GET['city'] ?? ''),
            'status' => trim($_GET['status'] ?? ''),
            'skill' => trim($_GET['skill'] ?? ''),
            'skill_category' => trim($_GET['skill_category'] ?? ''),
            'min_level' => !empty($_GET['min_level']) ? intval($_GET['min_level']) : null,
            'max_level' => !empty($_GET['max_level']) ? intval($_GET['max_level']) : null,
            'online_only' => isset($_GET['online_only']) && $_GET['online_only'] === '1',
            'sort' => $_GET['sort'] ?? 'relevance'
        ];
        
        $page = intval($_GET['page'] ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $results = UserSearch::advancedSearch($criteria, $perPage, $offset);
        $total = UserSearch::countSearchResults($criteria);
        
        // Log la recherche si connecté
        if (isset($_SESSION['user_id']) && !empty($criteria['query'])) {
            UserSearch::logSearch($_SESSION['user_id'], $criteria['query'], $criteria, $total);
            UserAnalytics::logActivity($_SESSION['user_id'], UserAnalytics::TYPE_SEARCH);
        }
        
        return [
            'success' => true,
            'results' => $results,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    public function getSimilarUsers(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $limit = intval($_GET['limit'] ?? 10);
        $users = UserSearch::getSimilarUsers($_SESSION['user_id'], $limit);
        
        return ['success' => true, 'users' => $users];
    }
    
    public function saveSearch(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $searchName = trim($_POST['search_name'] ?? '');
        $criteria = json_decode($_POST['criteria'] ?? '{}', true);
        
        if (empty($searchName)) {
            return ['success' => false, 'error' => 'Nom requis'];
        }
        
        if (UserSearch::saveSearch($_SESSION['user_id'], $searchName, $criteria)) {
            return ['success' => true, 'message' => 'Recherche sauvegardée'];
        }
        
        return ['success' => false, 'error' => 'Erreur sauvegarde'];
    }
    
    public function getSavedSearches(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $searches = UserSearch::getSavedSearches($_SESSION['user_id']);
        
        return ['success' => true, 'searches' => $searches];
    }
    
    public function getProfileViews(): array {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Non connecté'];
        }
        
        $days = intval($_GET['days'] ?? 30);
        $views = UserSearch::getProfileViews($_SESSION['user_id'], $days);
        
        return ['success' => true, 'views' => $views, 'total' => count($views)];
    }
}
?>
