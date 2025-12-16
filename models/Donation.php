<?php
// Model/Donation.php
class Donation {
    private ?int $ngo_id;
    private ?string $donor_name;
    private ?string $donor_email;
    private ?string $country;
    private ?string $type;
    private ?string $amount;
    private ?string $message;
    private ?string $status;
    private ?DateTime $donation_date;



    public function __construct(?int $ngo_id, ?string $donor_name, ?string $donor_email, ?string $country, ?string $type, ?string $amount, ?string $message,?string $status ,?DateTime $donation_date) {
        $this->ngo_id = $ngo_id;
        $this->donor_name = $donor_name;
        $this->donor_email = $donor_email;
        $this->country = $country;
        $this->type = $type;
        $this->amount = $amount;
        $this->message = $message;
        $this->status = $status;
        $this->donation_date = $donation_date;
        
    }
    
    public function getNgoId(): ?int {
        return $this->ngo_id;
    }   
    public function setNgoId(?int $ngo_id): void {
        $this->ngo_id = $ngo_id;
    }
    public function getDonorName(): ?string {
        return $this->donor_name;
    }
    public function setDonorName(?string $donor_name): void {
        $this->donor_name = $donor_name;
    }
    public function getDonorEmail(): ?string {
        return $this->donor_email;
    }
    public function setDonorEmail(?string $donor_email): void {
        $this->donor_email = $donor_email;
    }
    public function getCountry(): ?string {
        return $this->country;
    }
    public function setCountry(?string $country): void {
        $this->country = $country;
    }
    public function getType(): ?string {
        return $this->type;
    }
    public function setType(?string $type): void {
        $this->type = $type;
    }
    public function getAmount(): ?string {
        return $this->amount;
    }
    public function setAmount(?string $amount): void {
        $this->amount = $amount;
    }
    public function getMessage(): ?string {
        return $this->message;
    }
    public function setMessage(?string $message): void {
        $this->message = $message;
    }
    public function getStatus(): ?string {
        return $this->status;
    }
    public function setStatus(?string $status): void {
        $this->status = $status;
    }
    public function getDonationDate(): ?DateTime {
        return $this->donation_date;
    }
    public function setDonationDate(?DateTime $donation_date): void {
        $this->donation_date = $donation_date;
    }


}
