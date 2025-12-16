<?php
require_once __DIR__ . '/../models/CountryModel.php';

class CountryController {
    private $model;
    
    public function __construct() {
        $this->model = new CountryModel();
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $countryId = $_POST['country_id'];
            $data = [
                'country_name' => $_POST['country_name'],
                'crisis_level' => $_POST['crisis_level'],
                'description' => $_POST['description'],
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude']
            ];
            
            if ($this->model->editCountry($countryId, $data)) {
                header("Location: admin.php?success=country_updated");
                exit;
            } else {
                header("Location: admin.php?error=update_failed");
                exit;
            }
        }
    }
    
    public function delete() {
        if (isset($_GET['delete_country'])) {
            $countryId = $_GET['delete_country'];
            if ($this->model->deleteCountry($countryId)) {
                header("Location: admin.php?success=country_deleted");
                exit;
            } else {
                header("Location: admin.php?error=delete_failed");
                exit;
            }
        }
    }
    
    public function getCountries() {
        return $this->model->getAllCountriesWithNGOs();
    }
    
    public function getStats() {
        return $this->model->getDashboardStats();
    }
}
?>