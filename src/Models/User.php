<?php

namespace App\Models;

use App\Core\Database;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($username, $email, $password, $role = 'user')
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :password_hash, :role)");
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $hash,
            ':role' => $role
        ]);
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function searchByUsername($query, $excludeId)
    {
        $stmt = $this->db->prepare("SELECT id, username, avatar_url, status_message FROM users WHERE username LIKE :query AND id != :excludeId LIMIT 10");
        $stmt->execute([
            ':query' => '%' . $query . '%',
            ':excludeId' => $excludeId
        ]);
        return $stmt->fetchAll();
    }

    public function updateStorage($userId, $bytesAdded)
    {
        $stmt = $this->db->prepare("UPDATE users SET storage_used = storage_used + :bytes WHERE id = :id");
        return $stmt->execute([
            ':bytes' => $bytesAdded,
            ':id' => $userId
        ]);
    }
}
