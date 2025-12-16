<?php
// Define base URL for the project
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    // Get the root directory of the project
    $rootDir = str_replace('\\', '/', $scriptDir);
    if (strpos($rootDir, '/views/') !== false) {
        $rootDir = substr($rootDir, 0, strpos($rootDir, '/views/'));
    } elseif (strpos($rootDir, '/controllers/') !== false) {
        $rootDir = substr($rootDir, 0, strpos($rootDir, '/controllers/'));
    }
    define('BASE_URL', $protocol . '://' . $host . $rootDir . '/');
}

class config
{   
    private static $pdo = null;
    
    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "aidforpeace_db";
            
            try {
                self::$pdo = new PDO(
                    "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                die('Erreur de connexion: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>