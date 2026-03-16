<?php

namespace App\Models;

use App\Core\Database;

class Download
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function record($fileId, $userId, $ipAddress)
    {
        $stmt = $this->db->prepare("INSERT INTO downloads (file_id, user_id, ip_address) VALUES (:file_id, :user_id, :ip_address)");
        return $stmt->execute([
            ':file_id' => $fileId,
            ':user_id' => $userId,
            ':ip_address' => $ipAddress
        ]);
    }
}
