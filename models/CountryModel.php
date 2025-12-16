<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../config/DATABASE.PHP';

// PHPMailer via Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class CountryModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnexion();
    }
    
    // All your getter methods (unchanged)
    public function getAllCountriesWithNGOs() {
        $stmt = $this->pdo->query("
            SELECT c.*, 
                   n.id as ngo_id, 
                   n.name as ngo_name, 
                   n.mission, 
                   n.contact_info, 
                   n.type_of_aid 
            FROM countries c 
            LEFT JOIN ngos n ON c.id = n.country_id 
            ORDER BY c.name
        ");
        
        $countries = [];
        while ($row = $stmt->fetch()) {
            $countryId = $row['id'];
            
            if (!isset($countries[$countryId])) {
                $countries[$countryId] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'crisis_level' => $row['crisis_level'],
                    'description' => $row['description'],
                    'coords' => [(float)$row['longitude'], (float)$row['latitude']],
                    'ngos' => []
                ];
            }
            
            if ($row['ngo_id']) {
                $countries[$countryId]['ngos'][] = [
                    'name' => $row['ngo_name'],
                    'mission' => $row['mission'],
                    'contact' => $row['contact_info'],
                    'type' => $row['type_of_aid']
                ];
            }
        }
        return array_values($countries);
    }

    public function getDashboardStats() {
        $stats = [];
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM countries");
        $stats['total_countries'] = $stmt->fetch()['total'];
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM ngos");
        $stats['total_ngos'] = $stmt->fetch()['total'];
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM countries WHERE crisis_level = 'Critical'");
        $stats['critical_areas'] = $stmt->fetch()['total'];
        return $stats;
    }

    public function getAllCountries() {
        return $this->pdo->query("SELECT * FROM countries ORDER BY name")->fetchAll();
    }

    public function getAllCountriesWithCount() {
        return $this->pdo->query("
            SELECT c.*, COUNT(n.id) as ngo_count 
            FROM countries c 
            LEFT JOIN ngos n ON c.id = n.country_id 
            GROUP BY c.id 
            ORDER BY c.name
        ")->fetchAll();
    }

    public function getAllNGOs() {
        return $this->pdo->query("
            SELECT n.*, c.name as country_name 
            FROM ngos n 
            JOIN countries c ON n.country_id = c.id 
            ORDER BY n.name
        ")->fetchAll();
    }

    public function countryExists($countryName) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM countries WHERE name = ?");
        $stmt->execute([$countryName]);
        return $stmt->fetch()['count'] > 0;
    }

    public function getCountryById($countryId) {
        $stmt = $this->pdo->prepare("SELECT * FROM countries WHERE id = ?");
        $stmt->execute([$countryId]);
        return $stmt->fetch();
    }

    // Crisis alert with Brevo SMTP + test logs
    public function sendCrisisAlert($countryId, $countryName, $oldLevel, $newLevel) {
        if ($newLevel !== 'Critical' || $oldLevel === 'Critical') {
            return false;
        }

        // Always create test log file (proof it worked)
        $logDir = __DIR__ . '/../logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
            file_put_contents($logDir . '/.htaccess', "Deny from all\n");
        }

        $timestamp = date('Y-m-d H:i:s');
        $htmlFile = $logDir . '/brevo_email_' . date('Ymd_His') . '_' . preg_replace('/[^a-z0-9]/i', '_', strtolower($countryName)) . '.html';
        $htmlContent = $this->getAlertEmailHTML($countryName, $oldLevel, $newLevel);
        file_put_contents($htmlFile, $htmlContent);

        error_log("[BREVO CRISIS ALERT] {$countryName} → Critical at {$timestamp}");

        // Send real email via Brevo
        $mail = new PHPMailer(true);

        try {
            // === BREVO SMTP CONFIG (change the key below) ===
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'benamira027@gmail.com';
            $mail->Password   = 'jhwezsdkozjdtqng';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // Changed to SMTPS
            $mail->Port       = 465;  // Changed to 465
            // Sender & recipients
            $mail->setFrom('benamira027@gmail.com', 'Charity Connect Alerts');
            $mail->addAddress('mehdibenamira2005@gmail.com');  // ← Your main recipient
            // Add more: $mail->addAddress('subscriber1@example.com');

            // Content
            $mail->isHTML(true);
            $mail->Subject = "🚨 CRISIS ALERT: {$countryName} is now CRITICAL!";
            $mail->Body    = $htmlContent;
            $mail->AltBody = "URGENT: {$countryName} crisis level changed from {$oldLevel} to Critical. Immediate attention required.";

            $mail->send();
            error_log("[BREVO EMAIL SENT] Crisis alert for {$countryName}");
            return true;

        } catch (Exception $e) {
            error_log("[BREVO EMAIL FAILED] " . $mail->ErrorInfo);
            return false;
        }
    }

    // HTML email template (unchanged)
    private function getAlertEmailHTML($countryName, $oldLevel, $newLevel) {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CRISIS ALERT</title>
    <style>
        body {font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:20px;}
        .container {max-width:700px; margin:20px auto; background:white; border-radius:15px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1);}
        .header {background:linear-gradient(135deg,#dc3545,#c82333); color:white; padding:30px; text-align:center;}
        .header h1 {margin:0; font-size:32px;}
        .content {padding:40px;}
        .alert {background:#fff3cd; border:3px solid #ffc107; border-radius:12px; padding:25px; margin:20px 0;}
        .level {display:inline-block; background:#dc3545; color:white; padding:12px 24px; border-radius:30px; font-weight:bold; font-size:20px;}
        .btn {display:inline-block; background:#28a745; color:white; padding:16px 32px; text-decoration:none; border-radius:10px; font-weight:bold; margin:10px 10px 10px 0;}
        .btn-admin {background:#007bff;}
        .footer {background:#f8f9fa; padding:25px; text-align:center; color:#666; font-size:14px;}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CRISIS ALERT</h1>
        </div>
        <div class="content">
            <h2>' . htmlspecialchars($countryName) . ' is now <span class="level">CRITICAL</span></h2>
            <div class="alert">
                <strong>Previous level:</strong> ' . htmlspecialchars($oldLevel) . '<br>
                <strong>New level:</strong> ' . htmlspecialchars($newLevel) . '
            </div>
            <p><strong>Immediate action is required.</strong></p>
            <div>
                <a href="http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI'], 3) . '/views/frontoffice/index.php" class="btn">View on Globe</a>
                <a href="http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI'], 3) . '/views/backoffice/admin.php" class="btn btn-admin">Go to Admin Panel</a>
            </div>
        </div>
        <div class="footer">
            Charity Connect Crisis Monitoring System • ' . date('Y-m-d H:i') . '
        </div>
    </div>
</body>
</html>';
    }

    // All your setter methods (unchanged)
    public function addCountry($data) {
        try {
            if ($this->countryExists($data['country_name'])) return 'exists';
            $stmt = $this->pdo->prepare("INSERT INTO countries (name, crisis_level, description, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$data['country_name'], $data['crisis_level'], $data['description'], $data['latitude'], $data['longitude']]) ? true : 'error';
        } catch (PDOException $e) {
            error_log("Add country error: " . $e->getMessage());
            return 'error';
        }
    }

    public function addNGO($data) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO ngos (name, country_id, mission, contact_info, type_of_aid) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$data['ngo_name'], $data['country_id'], $data['mission'], $data['contact_info'], $data['type_of_aid']]);
        } catch (PDOException $e) {
            error_log("Add NGO error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCountry($countryId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM countries WHERE id = ?");
            return $stmt->execute([$countryId]);
        } catch (PDOException $e) {
            error_log("Delete country error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteNGO($ngoId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM ngos WHERE id = ?");
            return $stmt->execute([$ngoId]);
        } catch (PDOException $e) {
            error_log("Delete NGO error: " . $e->getMessage());
            return false;
        }
    }

    public function editCountry($countryId, $data) {
        try {
            $currentCountry = $this->getCountryById($countryId);
            $oldLevel = $currentCountry ? $currentCountry['crisis_level'] : null;

            // Check name conflict
            $checkStmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM countries WHERE name = ? AND id != ?");
            $checkStmt->execute([$data['country_name'], $countryId]);
            if ($checkStmt->fetch()['count'] > 0) return 'exists';

            $stmt = $this->pdo->prepare("UPDATE countries SET name = ?, crisis_level = ?, description = ?, latitude = ?, longitude = ? WHERE id = ?");
            $success = $stmt->execute([
                $data['country_name'],
                $data['crisis_level'],
                $data['description'],
                $data['latitude'],
                $data['longitude'],
                $countryId
            ]);

            if ($success && $oldLevel !== $data['crisis_level'] && $data['crisis_level'] === 'Critical') {
                $this->sendCrisisAlert($countryId, $data['country_name'], $oldLevel, $data['crisis_level']);
            }

            return $success ? true : 'error';
        } catch (PDOException $e) {
            error_log("Edit country error: " . $e->getMessage());
            return false;
        }
    }
}
?>