<?php
require_once(__DIR__ . '/../config/config.php');

class Profil
{
    public int $id;
    public int $user_id;
    public string $statut;
    public string $date_modification;

    public function __construct(int $user_id = 0, string $statut = '', string $date_modification = '')
    {
        $this->id = 0;
        $this->user_id = $user_id;
        $this->statut = $statut;
        $this->date_modification = $date_modification;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getUserId(): int { return $this->user_id; }
    public function getStatut(): string { return $this->statut; }
    public function getDateModification(): string { return $this->date_modification; }

    // Setters
    public function setUserId(int $user_id): void { $this->user_id = $user_id; }
    public function setStatut(string $statut): void { $this->statut = $statut; }
    public function setDateModification(string $date_modification): void { $this->date_modification = $date_modification; }
}
?>