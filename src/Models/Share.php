<?php

namespace App\Models;

use App\Core\Database;

class Share
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($fileId, $shareToken, $permissions = 'read', $expiresAt = null)
    {
        $stmt = $this->db->prepare("INSERT INTO shares (file_id, share_token, permissions, expires_at) VALUES (:file_id, :share_token, :permissions, :expires_at)");
        return $stmt->execute([
            ':file_id' => $fileId,
            ':share_token' => $shareToken,
            ':permissions' => $permissions,
            ':expires_at' => $expiresAt
        ]);
    }

    public function findByToken($token)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, f.original_name, f.size, f.mime_type, f.stored_name
            FROM shares s
            JOIN files f ON s.file_id = f.id
            WHERE s.share_token = :token
        ");
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }
}
