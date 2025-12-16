<?php
/**
 * Modèle pour le statut en temps réel de l'utilisateur
 */
class UserStatus
{
    public int $userId;
    public string $status; // 'online', 'offline', 'away', 'busy'
    public string $statusMessage;
    public string $lastActivity;
    public bool $isOnline;

    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';
    const STATUS_AWAY = 'away';
    const STATUS_BUSY = 'busy';

    const ONLINE_THRESHOLD = 300; // 5 minutes en secondes

    public function __construct(
        int $userId = 0,
        string $status = self::STATUS_OFFLINE,
        string $statusMessage = '',
        string $lastActivity = '',
        bool $isOnline = false
    ) {
        $this->userId = $userId;
        $this->status = $status;
        $this->statusMessage = $statusMessage;
        $this->lastActivity = $lastActivity ?: date('Y-m-d H:i:s');
        $this->isOnline = $isOnline;
    }

    // Getters
    public function getUserId(): int { return $this->userId; }
    public function getStatus(): string { return $this->status; }
    public function getStatusMessage(): string { return $this->statusMessage; }
    public function getLastActivity(): string { return $this->lastActivity; }
    public function isOnline(): bool { return $this->isOnline; }

    // Setters
    public function setStatus(string $status): void { 
        if (in_array($status, [self::STATUS_ONLINE, self::STATUS_OFFLINE, self::STATUS_AWAY, self::STATUS_BUSY])) {
            $this->status = $status;
        }
    }
    public function setStatusMessage(string $message): void { $this->statusMessage = $message; }
    public function setLastActivity(string $lastActivity): void { $this->lastActivity = $lastActivity; }
    public function setIsOnline(bool $isOnline): void { $this->isOnline = $isOnline; }

    /**
     * Mettre à jour l'activité (ping)
     */
    public function updateActivity(): void {
        $this->lastActivity = date('Y-m-d H:i:s');
        $this->isOnline = true;
        if ($this->status === self::STATUS_OFFLINE) {
            $this->status = self::STATUS_ONLINE;
        }
    }

    /**
     * Vérifier si l'utilisateur est vraiment en ligne
     */
    public function checkOnlineStatus(): bool {
        $lastActivityTime = strtotime($this->lastActivity);
        $currentTime = time();
        $diff = $currentTime - $lastActivityTime;

        if ($diff > self::ONLINE_THRESHOLD) {
            $this->isOnline = false;
            if ($this->status === self::STATUS_ONLINE) {
                $this->status = self::STATUS_AWAY;
            }
            return false;
        }

        return true;
    }

    /**
     * Obtenir le temps écoulé depuis la dernière activité
     */
    public function getTimeSinceLastActivity(): string {
        $lastActivityTime = strtotime($this->lastActivity);
        $currentTime = time();
        $diff = $currentTime - $lastActivityTime;

        if ($diff < 60) {
            return 'À l\'instant';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "Il y a {$minutes} minute" . ($minutes > 1 ? 's' : '');
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "Il y a {$hours} heure" . ($hours > 1 ? 's' : '');
        } else {
            $days = floor($diff / 86400);
            return "Il y a {$days} jour" . ($days > 1 ? 's' : '');
        }
    }

    /**
     * Obtenir l'icône du statut
     */
    public function getStatusIcon(): string {
        switch ($this->status) {
            case self::STATUS_ONLINE:
                return '🟢';
            case self::STATUS_AWAY:
                return '🟡';
            case self::STATUS_BUSY:
                return '🔴';
            case self::STATUS_OFFLINE:
            default:
                return '⚫';
        }
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColor(): string {
        switch ($this->status) {
            case self::STATUS_ONLINE:
                return '#27ae60';
            case self::STATUS_AWAY:
                return '#f39c12';
            case self::STATUS_BUSY:
                return '#e74c3c';
            case self::STATUS_OFFLINE:
            default:
                return '#95a5a6';
        }
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabel(): string {
        switch ($this->status) {
            case self::STATUS_ONLINE:
                return 'En ligne';
            case self::STATUS_AWAY:
                return 'Absent';
            case self::STATUS_BUSY:
                return 'Occupé';
            case self::STATUS_OFFLINE:
            default:
                return 'Hors ligne';
        }
    }
}
?>
