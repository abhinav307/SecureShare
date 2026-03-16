<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Database;

class AdminController
{
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = Database::getInstance();
    }

    private function requireAdmin()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            header("Location: /chat");
            exit;
        }
    }

    public function dashboard()
    {
        $this->requireAdmin();

        $users = $this->db->query("
            SELECT id, username, email, role, first_name, last_name, phone_number,
                   storage_used, storage_limit, created_at, status_message
            FROM users
            ORDER BY created_at DESC
        ")->fetchAll();

        $totalUsers   = count($users);
        $totalStorage = array_sum(array_column($users, 'storage_used'));

        View::render('admin/dashboard', [
            'title'        => 'Admin Portal',
            'users'        => $users,
            'totalUsers'   => $totalUsers,
            'totalStorage' => $totalStorage,
        ]);
    }

    public function deleteUser()
    {
        $this->requireAdmin();

        $userId = (int)($_POST['user_id'] ?? 0);
        if (!$userId) {
            header("Location: /admin");
            exit;
        }

        // Don't allow deleting own account
        if ($userId === (int)$_SESSION['user_id']) {
            header("Location: /admin?error=Cannot+delete+your+own+account");
            exit;
        }

        // Delete physical files
        $files = $this->db->prepare("SELECT stored_name FROM files WHERE user_id = ?");
        $files->execute([$userId]);
        $uploadDir = BASE_PATH . '/storage/files';
        foreach ($files->fetchAll() as $f) {
            $path = $uploadDir . '/' . $f['stored_name'];
            if (file_exists($path)) unlink($path);
        }

        // Cascade delete (messages, files, conversations, password_resets)
        $this->db->prepare("DELETE FROM messages WHERE sender_id = ?")->execute([$userId]);
        $this->db->prepare("UPDATE messages SET file_id = NULL WHERE file_id IN (SELECT id FROM files WHERE user_id = ?)")->execute([$userId]);
        $this->db->prepare("DELETE FROM files WHERE user_id = ?")->execute([$userId]);
        $this->db->prepare("DELETE FROM conversations WHERE user1_id = ? OR user2_id = ?")->execute([$userId, $userId]);
        $this->db->prepare("DELETE FROM password_resets WHERE email = (SELECT email FROM users WHERE id = ?)")->execute([$userId]);
        $this->db->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);

        header("Location: /admin?success=User+deleted+successfully");
        exit;
    }

    public function promoteUser()
    {
        $this->requireAdmin();
        $userId = (int)($_POST['user_id'] ?? 0);
        $role   = in_array($_POST['role'] ?? '', ['user', 'admin']) ? $_POST['role'] : 'user';
        if ($userId) {
            $this->db->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, $userId]);
        }
        header("Location: /admin?success=User+role+updated");
        exit;
    }
}
