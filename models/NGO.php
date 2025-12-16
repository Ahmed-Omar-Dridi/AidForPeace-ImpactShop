<?php
// Model/NGO.php
class NGO {
    private ?int $id;
    private ?string $name;
    private ?string $country;
    private ?string $address;
    private ?string $history;
    private ?string $image;

    public function __construct(?int $id, ?string $name, ?string $country, ?string $address, ?string $history, ?string $image) {
        $this->id = $id;
        $this->name = $name;
        $this->country = $country;
        $this->address = $address;
        $this->history = $history;
        $this->image = $image;
    }


    
    public function getId(): ?int {
        return $this->id;
    }
    public function setId(?int $id): void {
        $this->id = $id;
    }   
    public function getName(): ?string {
        return $this->name;
    }
    public function setName(?string $name): void {
        $this->name = $name;
    }
    public function getCountry(): ?string {
        return $this->country;
    }
    public function setCountry(?string $country): void {
        $this->country = $country;
    }
    public function getAddress(): ?string {
        return $this->address;
    }
    public function setAddress(?string $address): void {
        $this->address = $address;
    }
    public function getHistory(): ?string {
        return $this->history;
    }
    public function setHistory(?string $history): void {
        $this->history = $history;
    }
    public function getImage(): ?string {
        return $this->image;
    }
    public function setImage(?string $image): void {
        $this->image = $image;
    }
    

}
