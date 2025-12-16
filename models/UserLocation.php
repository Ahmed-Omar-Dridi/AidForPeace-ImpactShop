<?php
/**
 * Modèle pour la localisation géographique de l'utilisateur
 */
class UserLocation
{
    public int $userId;
    public string $country;
    public string $city;
    public ?float $latitude;
    public ?float $longitude;
    public string $timezone;
    public bool $isPublic;

    public function __construct(
        int $userId = 0,
        string $country = '',
        string $city = '',
        ?float $latitude = null,
        ?float $longitude = null,
        string $timezone = 'UTC',
        bool $isPublic = false
    ) {
        $this->userId = $userId;
        $this->country = $country;
        $this->city = $city;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timezone = $timezone;
        $this->isPublic = $isPublic;
    }

    // Getters
    public function getUserId(): int { return $this->userId; }
    public function getCountry(): string { return $this->country; }
    public function getCity(): string { return $this->city; }
    public function getLatitude(): ?float { return $this->latitude; }
    public function getLongitude(): ?float { return $this->longitude; }
    public function getTimezone(): string { return $this->timezone; }
    public function isPublic(): bool { return $this->isPublic; }

    // Setters
    public function setCountry(string $country): void { $this->country = $country; }
    public function setCity(string $city): void { $this->city = $city; }
    public function setLatitude(?float $latitude): void { $this->latitude = $latitude; }
    public function setLongitude(?float $longitude): void { $this->longitude = $longitude; }
    public function setTimezone(string $timezone): void { $this->timezone = $timezone; }
    public function setIsPublic(bool $isPublic): void { $this->isPublic = $isPublic; }

    /**
     * Obtenir la localisation complète sous forme de chaîne
     */
    public function getFullLocation(): string {
        $parts = [];
        if (!empty($this->city)) $parts[] = $this->city;
        if (!empty($this->country)) $parts[] = $this->country;
        return implode(', ', $parts);
    }

    /**
     * Vérifier si les coordonnées GPS sont définies
     */
    public function hasCoordinates(): bool {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Calculer la distance avec une autre localisation (en km)
     */
    public function distanceTo(UserLocation $other): ?float {
        if (!$this->hasCoordinates() || !$other->hasCoordinates()) {
            return null;
        }

        $earthRadius = 6371; // Rayon de la Terre en km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($other->getLatitude());
        $lonTo = deg2rad($other->getLongitude());

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
?>
