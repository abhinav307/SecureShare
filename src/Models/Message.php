<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Message
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($conversationId, $senderId, $content, $fileId = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO messages (conversation_id, sender_id, content, file_id) 
            VALUES (?, ?, ?, ?)
        ");
        $success = $stmt->execute([$conversationId, $senderId, $content, $fileId]);
        
        if ($success) {
            // Touch conversation
            $conv = new Conversation();
            $conv->touch($conversationId);
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Fetch messages for a conversation, typically polling based on last_id
    public function getMessages($conversationId, $lastMessageId = 0)
    {
        $stmt = $this->db->prepare("
            SELECT m.*, f.original_name, f.stored_name, f.size, f.mime_type
            FROM messages m
            LEFT JOIN files f ON m.file_id = f.id
            WHERE m.conversation_id = ? AND m.id > ?
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$conversationId, $lastMessageId]);
        return $stmt->fetchAll();
    }

    public function markAsRead($conversationId, $userId)
    {
        // Mark all messages as read in this conversation that were NOT sent by $userId
        $stmt = $this->db->prepare("
            UPDATE messages 
            SET is_read = 1 
            WHERE conversation_id = ? AND sender_id != ? AND is_read = 0
        ");
        $stmt->execute([$conversationId, $userId]);
    }
}
