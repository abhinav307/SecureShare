<?php

namespace App\Models;

use App\Core\Database;

class File
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($userId, $originalName, $storedName, $mimeType, $size, $isEncrypted = 1, $aiTags = null)
    {
        $stmt = $this->db->prepare("INSERT INTO files (user_id, original_name, stored_name, mime_type, size, is_encrypted, ai_tags) VALUES (:user_id, :original_name, :stored_name, :mime_type, :size, :is_encrypted, :ai_tags)");
        $stmt->execute([
            ':user_id' => $userId,
            ':original_name' => $originalName,
            ':stored_name' => $storedName,
            ':mime_type' => $mimeType,
            ':size' => $size,
            ':is_encrypted' => $isEncrypted,
            ':ai_tags' => $aiTags
        ]);
        return $this->db->lastInsertId();
    }

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM files WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM files WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
