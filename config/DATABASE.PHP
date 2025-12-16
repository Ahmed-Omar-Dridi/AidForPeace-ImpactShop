<?php
if (!class_exists('Database')) {
class Database {
    private static $connexion = null;
    private static $host = 'localhost';
    private static $port = '3306';
    private static $dbname = 'aidforpeace_db';
    private static $username = 'root';
    private static $password = '';

    public static function getConnexion() {
        if (self::$connexion === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";port=" .  self::$port .  ";dbname=" .  self::$dbname . ";charset=utf8mb4";
                self::$connexion = new PDO($dsn, self::$username, self::$password);
                self::$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Erreur de connexion: " . $e->getMessage());
            }
        }
        return self::$connexion;
    }

    public static function closeConnexion() {
        self::$connexion = null;
    }
}
} // end class_exists check
?>