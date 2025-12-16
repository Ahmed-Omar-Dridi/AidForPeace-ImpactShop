<?php
// Controller/DonationController.php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../models/Donation.php');

class DonationController {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function getDonationById($id) {
        $sql = "SELECT * FROM donations WHERE id = :id";
        try {
            $query = $this->pdo->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    public function createDonation($data) {
        $sql = "INSERT INTO donations (donor_name, donor_email, amount, message, status, created_at)
                VALUES (:donor_name, :donor_email, :amount, :message, 'pending', NOW())";
        try {
            $query = $this->pdo->prepare($sql);
            
            // Handle both Donation object and array
            if ($data instanceof Donation) {
                $params = [
                    'donor_name' => $data->getDonorName() ?? '',
                    'donor_email' => $data->getDonorEmail() ?? '',
                    'amount' => $data->getAmount() ?? 0,
                    'message' => $data->getMessage() ?? ''
                ];
            } else {
                $params = [
                    'donor_name' => $data['donor_name'] ?? '',
                    'donor_email' => $data['donor_email'] ?? '',
                    'amount' => $data['amount'] ?? 0,
                    'message' => $data['message'] ?? ''
                ];
            }
            
            $query->execute($params);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getTotalDonations() {
        $sql = "SELECT COALESCE(SUM(amount), 0) AS total FROM donations";
        try {
            $query = $this->pdo->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getDonationsCountByCountry() {
        $sql = "SELECT 'Tunisia' as country, COUNT(*) as total FROM donations";
        try {
            $query = $this->pdo->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getDonationsByStatus($status) {
        // Map status names
        $statusMap = ['attente' => 'pending', 'accepter' => 'completed', 'refuse' => 'failed'];
        $dbStatus = $statusMap[$status] ?? $status;
        
        $sql = "SELECT * FROM donations WHERE status = :status ORDER BY created_at DESC";
        try {
            $query = $this->pdo->prepare($sql);
            $query->execute(['status' => $dbStatus]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function updateDonation($data, $id) {
        $sql = "UPDATE donations SET donor_name=:donor_name, donor_email=:donor_email, amount=:amount, message=:message, status=:status WHERE id=:id";
        try {
            $query = $this->pdo->prepare($sql);
            return $query->execute([
                'donor_name' => $data['donor_name'] ?? '',
                'donor_email' => $data['donor_email'] ?? '',
                'amount' => $data['amount'] ?? 0,
                'message' => $data['message'] ?? '',
                'status' => $data['status'] ?? 'pending',
                'id' => $id
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteDonation($donation_id) {
        $sql = "DELETE FROM donations WHERE id=:donation_id";
        try {
            $query = $this->pdo->prepare($sql);
            return $query->execute(['donation_id' => $donation_id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function changeStatus($donation_id, $status) {
        $statusMap = ['attente' => 'pending', 'accepter' => 'completed', 'refuse' => 'failed'];
        $dbStatus = $statusMap[$status] ?? $status;
        
        $sql = "UPDATE donations SET status=:status WHERE id=:donation_id";
        try {
            $query = $this->pdo->prepare($sql);
            return $query->execute(['status' => $dbStatus, 'donation_id' => $donation_id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getDonationsCountByEmail($email) {
        $sql = "SELECT COUNT(*) AS total FROM donations WHERE donor_email = :email OR email = :email2";
        try {
            $query = $this->pdo->prepare($sql);
            $query->execute(['email' => $email, 'email2' => $email]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function searchDonations($term = '', $country = '') {
        $term = "%$term%";
        $sql = "SELECT * FROM donations WHERE donor_name LIKE ? OR donor_email LIKE ? OR email LIKE ? ORDER BY created_at DESC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$term, $term, $term]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getDonationsByNGOAndStatus($ngo_id, $status) {
        return $this->getDonationsByStatus($status);
    }
}
?>
