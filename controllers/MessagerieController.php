<?php

require_once __DIR__ . '/../config/DATABASE.PHP';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Conversation.php';

class MessagerieController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnexion();
    }

    // Get or create conversation between two users
    public function getOrCreateConversation($user1_id, $user2_id) {
        $participant1 = min($user1_id, $user2_id);
        $participant2 = max($user1_id, $user2_id);

        $sql = "SELECT id FROM conversations WHERE participant1_id = ? AND participant2_id = ? AND is_active = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$participant1, $participant2]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['id'];
        }

        $sql = "INSERT INTO conversations (participant1_id, participant2_id, created_at, updated_at, is_active) VALUES (?, ?, NOW(), NOW(), 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$participant1, $participant2]);
        return $this->pdo->lastInsertId();
    }

    // Get all conversations for a user
    public function getUserConversations($user_id) {
        $sql = "SELECT c.*, 
                CASE WHEN c.participant1_id = ? THEN c.participant2_id ELSE c.participant1_id END as other_user_id,
                (SELECT u.nom FROM utilisateurs u WHERE u.id = CASE WHEN c.participant1_id = ? THEN c.participant2_id ELSE c.participant1_id END) as other_user_nom,
                (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND receiver_id = ? AND is_read = 0) as unread_count,
                (SELECT content FROM messages WHERE conversation_id = c.id AND is_deleted = 0 ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT sender_id FROM messages WHERE conversation_id = c.id AND is_deleted = 0 ORDER BY created_at DESC LIMIT 1) as last_message_sender_id,
                (SELECT DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') FROM messages WHERE conversation_id = c.id AND is_deleted = 0 ORDER BY created_at DESC LIMIT 1) as last_message_date
                FROM conversations c
                WHERE (c.participant1_id = ? OR c.participant2_id = ?) AND c.is_active = 1
                ORDER BY c.updated_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get conversation by ID
    public function getConversationById($conversation_id, $user_id) {
        $sql = "SELECT c.* FROM conversations c
                WHERE c.id = ? AND (c.participant1_id = ? OR c.participant2_id = ?) AND c.is_active = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversation_id, $user_id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Send message
    public function sendMessage($conversation_id, $sender_id, $receiver_id, $content) {
        if (empty(trim($content))) {
            return false;
        }

        $sql = "UPDATE conversations SET updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversation_id]);

        $sql = "INSERT INTO messages (conversation_id, sender_id, receiver_id, content, created_at, is_read, is_deleted) 
                VALUES (?, ?, ?, ?, NOW(), 0, 0)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$conversation_id, $sender_id, $receiver_id, $content]);
    }

    // Get conversation messages
    public function getConversationMessages($conversation_id, $user_id) {
        $sql = "SELECT m.* FROM messages m
                WHERE m.conversation_id = ? AND m.is_deleted = 0
                ORDER BY m.created_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversation_id]);

        $this->markConversationAsRead($conversation_id, $user_id);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mark conversation as read
    public function markConversationAsRead($conversation_id, $user_id) {
        $sql = "UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND receiver_id = ? AND is_read = 0";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$conversation_id, $user_id]);
    }

    // Delete message
    public function deleteMessage($message_id, $sender_id) {
        $sql = "UPDATE messages SET is_deleted = 1 WHERE id = ? AND sender_id = ? AND is_deleted = 0";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$message_id, $sender_id]);
    }

    // Get unread count
    public function getUnreadCount($user_id) {
        $sql = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0 AND is_deleted = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    // Get all users
    public function getUtilisateurs() {
        $sql = "SELECT id, nom FROM utilisateurs ORDER BY nom";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user by ID
    public function getUserById($user_id) {
        $sql = "SELECT * FROM utilisateurs WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get statistics for dashboard
    public function getStats($user_id) {
        $stats = [
            'total_conversations' => 0,
            'unread_messages' => 0,
            'total_messages_sent' => 0
        ];

        $sql = "SELECT COUNT(*) as count FROM conversations WHERE (participant1_id = ? OR participant2_id = ?) AND is_active = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id, $user_id]);
        $stats['total_conversations'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        $sql = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $stats['unread_messages'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        $sql = "SELECT COUNT(*) as count FROM messages WHERE sender_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $stats['total_messages_sent'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return $stats;
    }

    // Add message (old method for compatibility)
    public function ajouter($sender_id, $reciever_id, $sujet, $contenu) {
        $conversation_id = $this->getOrCreateConversation($sender_id, $reciever_id);
        return $this->sendMessage($conversation_id, $sender_id, $reciever_id, $contenu);
    }

    // Get my messages (received messages)
    public function getMyMessages($user_id) {
        $sql = "SELECT m.*, u.nom as sender_nom FROM messages m
                LEFT JOIN utilisateurs u ON m.sender_id = u.id
                WHERE m.receiver_id = ? AND m.is_deleted = 0
                ORDER BY m.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new Message($row);
        }
        return $messages;
    }

    // Get message by ID
    public function getMessageById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM messages WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Message($data) : null;
    }
}
?>
