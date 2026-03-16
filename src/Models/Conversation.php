<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Conversation
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Find a conversation by the two user IDs, or create one if it doesn't exist
    public function findOrCreate($user1_id, $user2_id)
    {
        // Sort IDs so user1 is always the smaller ID. Prevents duplicate conversations.
        $u1 = min($user1_id, $user2_id);
        $u2 = max($user1_id, $user2_id);

        $stmt = $this->db->prepare("SELECT id FROM conversations WHERE user1_id = ? AND user2_id = ?");
        $stmt->execute([$u1, $u2]);
        $conversation = $stmt->fetch();

        if ($conversation) {
            return $conversation['id'];
        }

        // Create new
        $stmt = $this->db->prepare("INSERT INTO conversations (user1_id, user2_id) VALUES (?, ?)");
        $stmt->execute([$u1, $u2]);
        
        return $this->db->lastInsertId();
    }

    // Get all conversations for a specific user, with latest message and unread count
    public function getForUser($userId)
    {
        // This query fetches the list of users this person has a conversation with,
        // along with the timestamp of the last update to sort by recent.
        $stmt = $this->db->prepare("
            SELECT 
                c.id as conversation_id,
                c.updated_at,
                u.id as other_user_id,
                u.username as other_user_name,
                u.avatar_url,
                (SELECT content FROM messages m WHERE m.conversation_id = c.id ORDER BY m.created_at DESC LIMIT 1) as latest_message,
                (SELECT COUNT(*) FROM messages m2 WHERE m2.conversation_id = c.id AND m2.is_read = 0 AND m2.sender_id != ?) as unread_count
            FROM conversations c
            JOIN users u ON (u.id = c.user1_id OR u.id = c.user2_id) AND u.id != ?
            WHERE c.user1_id = ? OR c.user2_id = ?
            ORDER BY c.updated_at DESC
        ");
        $stmt->execute([$userId, $userId, $userId, $userId]);
        return $stmt->fetchAll();
    }
    
    // Update the updated_at timestamp when a new message is sent
    public function touch($conversationId)
    {
        $stmt = $this->db->prepare("UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$conversationId]);
    }
}
