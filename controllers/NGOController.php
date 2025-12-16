<?php
// Controller/NGOController.php
require_once(__DIR__ . '/../config/config.php');
require_once( __DIR__ . '/../models/NGO.php');

class NGOController {


    public function listNGOs() {
        $sql = "SELECT * FROM ngos";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }

    }
    public function getTotalNGOs() {
        $sql = "SELECT COUNT(*) AS total FROM ngos";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetch();
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }


    public function getNgo($id) {
      $sql="SELECT * FROM ngos WHERE id=$id";
      $db = config::getConnexion();
        try{
            $query=$db->prepare($sql);
            $query->execute();
            $ngo=$query->fetch();
            return $ngo;
        }
        catch (Exception $e){
            die('Error: '.$e->getMessage());
        }
    }
    

    public function createNGO(Ngo $ngo) {
    $sql="INSERT INTO ngos (name, country, address, history, image) 
           VALUES (:name, :country, :address, :history, :image)";
    $pdo = config::getConnexion();
    try{
        $query = $pdo->prepare($sql);
        $query->execute([
            'name' => $ngo->getName(),
            'country' => $ngo->getCountry(),
            'address' => $ngo->getAddress(),
            'history' => $ngo->getHistory(),
            'image' => $ngo->getImage()
        ]);
        return $pdo->lastInsertId(); // <- on récupère l'ID créé
    } catch (Exception $e){
        echo 'Erreur: '.$e->getMessage();
        return false;
    }
}


    public function updateNGO(Ngo $ngo,$id) {
        $sql="UPDATE ngos SET name=:name, country=:country, address=:address, history=:history, image=:image WHERE id=:ngo_id";
        $pdo = config::getConnexion();
        try{
            $query = $pdo->prepare($sql);
            $query->execute([
                'name' => $ngo->getName(),
                'country' => $ngo->getCountry(),
                'address' => $ngo->getAddress(),
                'history' => $ngo->getHistory(),
                'image' => $ngo->getImage(),
                'ngo_id' => $id
            ]);
        } catch (Exception $e){
            echo 'Erreur: '.$e->getMessage();
        }
    }

    public function deleteNGO($id) {
        $ngo = $this->getNgo($id);
        if($ngo && !empty($ngo['image']) && file_exists(__DIR__ . "/../../assets/images/" . $ngo['image'])) {
            unlink(__DIR__ . "/../../assets/images/" . $ngo['image']);
        }
        $sql = "DELETE FROM ngos WHERE id=:ngo_id";
        $pdo = config::getConnexion();
        try{
            $query = $pdo->prepare($sql);
            $query->execute(['ngo_id' => $id]);
        } catch (Exception $e){
            echo 'Erreur: '.$e->getMessage();
        }
    }
    
    
}
