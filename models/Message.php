<?php

class Message {
    private ?int $id = null;
    private ?int $conversation_id = null;
    private ?int $sender_id = null;
    private ?int $receiver_id = null;
    private ?string $content = null;
    private ?string $created_at = null;
    private ?int $is_read = 0;
    private ?int $is_deleted = 0;
    private ?string $sender_nom = null;
    private ?string $receiver_nom = null;
    private ?string $sujet = null;

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
    public function getConversationId(): ?int { return $this->conversation_id; }
    public function getSenderId(): ?int { return $this->sender_id; }
    public function getReceiverId(): ?int { return $this->receiver_id; }
    public function getContent(): ?string { return $this->content; }
    public function getContenu(): ?string { return $this->content; } // Alias for compatibility
    public function getCreatedAt(): string { 
        return $this->created_at ? date('d/m/Y H:i', strtotime($this->created_at)) : 'Inconnue';
    }
    public function getDateEnvoie(): string { 
        return $this->created_at ? date('d/m/Y H:i', strtotime($this->created_at)) : 'Inconnue';
    } // Alias for compatibility
    public function getCreatedAtRaw(): ?string { return $this->created_at; }
    public function getIsRead(): ?int { return $this->is_read; }
    public function getIsDeleted(): ?int { return $this->is_deleted; }
    public function getSenderNom(): ?string { return $this->sender_nom; }
    public function getReceiverNom(): ?string { return $this->receiver_nom; }
    public function getSujet(): ?string { return $this->sujet ?? 'Message'; } // Default subject

    // SETTERS
    public function setId($v): void { $this->id = (int)$v; }
    public function setConversationid($v): void { $this->conversation_id = (int)$v; }
    public function setSenderid($v): void { $this->sender_id = (int)$v; }
    public function setReceiverid($v): void { $this->receiver_id = (int)$v; }
    public function setContent($v): void { $this->content = $v; }
    public function setCreatedat($v): void { $this->created_at = $v; }
    public function setIsread($v): void { $this->is_read = (int)$v; }
    public function setIsdeleted($v): void { $this->is_deleted = (int)$v; }
    public function setSendernom($v): void { $this->sender_nom = $v; }
    public function setReceivernom($v): void { $this->receiver_nom = $v; }
    public function setSujet($v): void { $this->sujet = $v; }
}
?>
