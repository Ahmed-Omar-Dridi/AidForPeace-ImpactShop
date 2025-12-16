<?php
/**
 * DeliveryZone Model - Zones de livraison
 */

require_once __DIR__ . '/../config/DATABASE.PHP';

class DeliveryZone
{
    // Zones de livraison en Tunisie
    public static $zones = [
        'tunis' => [
            'name' => 'Grand Tunis',
            'cities' => ['Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'La Marsa', 'Carthage', 'Sidi Bou Said'],
            'delay' => '2-3 jours',
            'cost' => 7.00,
            'icon' => 'fa-city'
        ],
        'nord' => [
            'name' => 'Nord',
            'cities' => ['Bizerte', 'Béja', 'Jendouba', 'Le Kef', 'Siliana'],
            'delay' => '3-5 jours',
            'cost' => 10.00,
            'icon' => 'fa-mountain'
        ],
        'sahel' => [
            'name' => 'Sahel',
            'cities' => ['Sousse', 'Monastir', 'Mahdia', 'Sfax'],
            'delay' => '3-4 jours',
            'cost' => 9.00,
            'icon' => 'fa-umbrella-beach'
        ],
        'centre' => [
            'name' => 'Centre',
            'cities' => ['Kairouan', 'Kasserine', 'Sidi Bouzid', 'Gafsa'],
            'delay' => '4-6 jours',
            'cost' => 12.00,
            'icon' => 'fa-sun'
        ],
        'sud' => [
            'name' => 'Sud',
            'cities' => ['Gabès', 'Médenine', 'Tataouine', 'Tozeur', 'Kébili'],
            'delay' => '5-7 jours',
            'cost' => 15.00,
            'icon' => 'fa-desert'
        ],
        'cap_bon' => [
            'name' => 'Cap Bon',
            'cities' => ['Nabeul', 'Hammamet', 'Kelibia', 'Korba'],
            'delay' => '2-4 jours',
            'cost' => 8.00,
            'icon' => 'fa-water'
        ]
    ];

    /**
     * Obtenir toutes les zones
     */
    public static function getAll()
    {
        return self::$zones;
    }

    /**
     * Obtenir une zone par clé
     */
    public static function getByKey($key)
    {
        return self::$zones[$key] ?? null;
    }

    /**
     * Trouver la zone d'une ville
     */
    public static function findZoneByCity($city)
    {
        $city = strtolower(trim($city));

        foreach (self::$zones as $key => $zone) {
            foreach ($zone['cities'] as $zoneCity) {
                if (strtolower($zoneCity) === $city || strpos(strtolower($zoneCity), $city) !== false) {
                    return array_merge(['key' => $key], $zone);
                }
            }
        }

        // Zone par défaut si ville non trouvée
        return array_merge(['key' => 'autre'], [
            'name' => 'Autre région',
            'cities' => [],
            'delay' => '5-7 jours',
            'cost' => 15.00,
            'icon' => 'fa-map-marker-alt'
        ]);
    }

    /**
     * Calculer les frais de livraison
     */
    public static function calculateShippingCost($city, $orderTotal = 0)
    {
        $zone = self::findZoneByCity($city);
        $cost = $zone['cost'];

        // Livraison gratuite au-dessus de 100 TND
        if ($orderTotal >= 100) {
            return 0;
        }

        return $cost;
    }

    /**
     * Obtenir le délai de livraison estimé
     */
    public static function getEstimatedDelivery($city)
    {
        $zone = self::findZoneByCity($city);
        return $zone['delay'];
    }

    /**
     * Obtenir toutes les villes
     */
    public static function getAllCities()
    {
        $cities = [];
        foreach (self::$zones as $zone) {
            $cities = array_merge($cities, $zone['cities']);
        }
        sort($cities);
        return $cities;
    }
}
