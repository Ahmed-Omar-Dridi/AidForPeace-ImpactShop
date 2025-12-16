<?php

class Conversation {
    private ?int $id = null;
    private ?int $participant1_id = null;
    private ?int $participant2_id = null;
    private ?string $participant1_nom = null;
    private ?string $participant2_nom = null;
    private ?string $last_message = null;
    private ?string $last_message_date = null;
    private ?int $last_message_sender_id = null;
    private ?int $unread_count = 0;
    private ?string $created_at = null;
    private ?string $updated_at = null;
    private ?int $is_active = 1;
    private ?int $other_user_id = null;
    private ?string $other_user_nom = null;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate(array $data) {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(str_replace('_', '', ucwords($key, '_')));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }
    public function getParticipant1Id(): ?int { return $this->participant1_id; }
    public function getParticipant2Id(): ?int { return $this->participant2_id; }
    public function getParticipant1Nom(): ?string { return $this->participant1_nom; }
    public function getParticipant2Nom(): ?string { return $this->participant2_nom; }
    public function getLastMessage(): ?string { return $this->last_message; }
    public function getLastMessageDate(): string { 
        return $this->last_message_date ? date('d/m/Y H:i', strtotime($this->last_message_date)) : 'Inconnue';
    }
    public function getLastMessageSenderId(): ?int { return $this->last_message_sender_id; }
    public function getUnreadCount(): ?int { return $this->unread_count; }
    public function getCreatedAt(): ?string { return $this->created_at; }
    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function getIsActive(): ?int { return $this->is_active; }
    public function getOtherUserId(): ?int { return $this->other_user_id; }
    public function getOtherUserNom(): ?string { return $this->other_user_nom; }

    // SETTERS
    public function setId($v): void { $this->id = (int)$v; }
    public function setParticipant1id($v): void { $this->participant1_id = (int)$v; }
    public function setParticipant2id($v): void { $this->participant2_id = (int)$v; }
    public function setParticipant1nom($v): void { $this->participant1_nom = $v; }
    public function setParticipant2nom($v): void { $this->participant2_nom = $v; }
    public function setLastmessage($v): void { $this->last_message = $v; }
    public function setLastmessagedate($v): void { $this->last_message_date = $v; }
    public function setLastmessagesenderid($v): void { $this->last_message_sender_id = (int)$v; }
    public function setUnreadcount($v): void { $this->unread_count = (int)$v; }
    public function setCreatedat($v): void { $this->created_at = $v; }
    public function setUpdatedat($v): void { $this->updated_at = $v; }
    public function setIsactive($v): void { $this->is_active = (int)$v; }
    public function setOtheruserid($v): void { $this->other_user_id = (int)$v; }
    public function setOtherusenom($v): void { $this->other_user_nom = $v; }
}
?>
