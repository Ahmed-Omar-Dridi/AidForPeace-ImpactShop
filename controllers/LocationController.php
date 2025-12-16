<?php
require_once(__DIR__ . '/../models/UserLocation.php');
require_once(__DIR__ . '/../config/config.php');

/**
 * Contrôleur pour la gestion de la localisation géographique
 */
class LocationController
{
    /**
     * Mettre à jour la localisation d'un utilisateur
     */
    public function updateLocation(int $userId): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Méthode non autorisée'];
        }

        $country = trim($_POST['location_country'] ?? '');
        $city = trim($_POST['location_city'] ?? '');
        $latitude = !empty($_POST['location_latitude']) ? floatval($_POST['location_latitude']) : null;
        $longitude = !empty($_POST['location_longitude']) ? floatval($_POST['location_longitude']) : null;
        $timezone = trim($_POST['location_timezone'] ?? 'UTC');
        $isPublic = isset($_POST['location_public']) && $_POST['location_public'] === '1';

        try {
            $pdo = config::getConnexion();
            $sql = "UPDATE user SET 
                    location_country = :country,
                    location_city = :city,
                    location_latitude = :latitude,
                    location_longitude = :longitude,
                    location_timezone = :timezone,
                    location_public = :is_public
                    WHERE id_user = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'country' => $country,
                'city' => $city,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'timezone' => $timezone,
                'is_public' => $isPublic ? 1 : 0,
                'user_id' => $userId
            ]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Localisation mise à jour avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Erreur lors de la mise à jour'
                ];
            }
        } catch (Exception $e) {
            error_log("Erreur updateLocation: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur serveur'
            ];
        }
    }

    /**
     * Obtenir la localisation d'un utilisateur
     */
    public function getLocation(int $userId): ?UserLocation {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT location_country, location_city, location_latitude, location_longitude, location_timezone, location_public 
                                   FROM user WHERE id_user = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $data = $stmt->fetch();

            if ($data) {
                return new UserLocation(
                    $userId,
                    $data['location_country'] ?? '',
                    $data['location_city'] ?? '',
                    $data['location_latitude'],
                    $data['location_longitude'],
                    $data['location_timezone'] ?? 'UTC',
                    (bool)($data['location_public'] ?? false)
                );
            }

            return null;
        } catch (Exception $e) {
            error_log("Erreur getLocation: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtenir la localisation via l'IP (API externe)
     */
    public function getLocationFromIP(string $ip = null): array {
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }

        // Ne pas géolocaliser les IPs locales
        if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0) {
            return [
                'success' => false,
                'error' => 'IP locale non géolocalisable'
            ];
        }

        try {
            // Utiliser une API gratuite de géolocalisation
            $url = "http://ip-api.com/json/{$ip}?fields=status,country,city,lat,lon,timezone";
            $response = @file_get_contents($url);
            
            if ($response === false) {
                return ['success' => false, 'error' => 'Erreur API'];
            }

            $data = json_decode($response, true);

            if ($data['status'] === 'success') {
                return [
                    'success' => true,
                    'country' => $data['country'] ?? '',
                    'city' => $data['city'] ?? '',
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                    'timezone' => $data['timezone'] ?? 'UTC'
                ];
            }

            return ['success' => false, 'error' => 'Localisation non trouvée'];
        } catch (Exception $e) {
            error_log("Erreur getLocationFromIP: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erreur serveur'];
        }
    }

    /**
     * Trouver les utilisateurs à proximité
     */
    public function findNearbyUsers(int $userId, float $radiusKm = 50): array {
        $userLocation = $this->getLocation($userId);
        
        if (!$userLocation || !$userLocation->hasCoordinates()) {
            return [];
        }

        try {
            $pdo = config::getConnexion();
            // Requête pour trouver les utilisateurs avec localisation publique
            $stmt = $pdo->prepare("SELECT id_user, Prenom, nom, location_city, location_country, 
                                          location_latitude, location_longitude 
                                   FROM user 
                                   WHERE location_public = 1 
                                   AND location_latitude IS NOT NULL 
                                   AND location_longitude IS NOT NULL
                                   AND id_user != :user_id");
            $stmt->execute(['user_id' => $userId]);
            $users = $stmt->fetchAll();

            $nearbyUsers = [];
            foreach ($users as $user) {
                $otherLocation = new UserLocation(
                    $user['id_user'],
                    $user['location_country'],
                    $user['location_city'],
                    $user['location_latitude'],
                    $user['location_longitude']
                );

                $distance = $userLocation->distanceTo($otherLocation);
                
                if ($distance !== null && $distance <= $radiusKm) {
                    $nearbyUsers[] = [
                        'user_id' => $user['id_user'],
                        'name' => $user['Prenom'] . ' ' . $user['nom'],
                        'location' => $otherLocation->getFullLocation(),
                        'distance' => round($distance, 1)
                    ];
                }
            }

            // Trier par distance
            usort($nearbyUsers, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });

            return $nearbyUsers;
        } catch (Exception $e) {
            error_log("Erreur findNearbyUsers: " . $e->getMessage());
            return [];
        }
    }
}
?>
