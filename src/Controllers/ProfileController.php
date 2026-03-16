<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\User;
use App\Core\Database;
use PDO;

class ProfileController
{
    private $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new User();
    }

    public function settings()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);
        
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $status = trim($_POST['status_message'] ?? '');
            
            // Handle profile update
            if (isset($_POST['update_profile'])) {
                $firstName   = trim($_POST['first_name'] ?? '');
                $lastName    = trim($_POST['last_name'] ?? '');
                $phoneNumber = trim($_POST['phone_number'] ?? '');
                $aboutMe     = trim($_POST['about_me'] ?? '');
                $status      = trim($_POST['status_message'] ?? '');

                $stmt = $db->prepare("UPDATE users SET 
                    first_name = :first_name, last_name = :last_name,
                    phone_number = :phone_number, about_me = :about_me,
                    status_message = :status
                    WHERE id = :id");
                $result = $stmt->execute([
                    ':first_name'   => empty($firstName) ? null : $firstName,
                    ':last_name'    => empty($lastName) ? null : $lastName,
                    ':phone_number' => empty($phoneNumber) ? null : $phoneNumber,
                    ':about_me'     => empty($aboutMe) ? null : $aboutMe,
                    ':status'       => empty($status) ? null : $status,
                    ':id'           => $userId
                ]);

                if ($result) {
                    $success = "Profile updated successfully!";
                    $user = array_merge($user, [
                        'first_name'    => $firstName,
                        'last_name'     => $lastName,
                        'phone_number'  => $phoneNumber,
                        'about_me'      => $aboutMe,
                        'status_message'=> $status,
                    ]);
                } else {
                    $error = "Failed to update profile.";
                }
            } 
            // Handle clearing storage (deleting own files and matching messages)
            else if (isset($_POST['clear_storage'])) {
                // Find all simple file uploads for this user attached to messages
                // This is a brutal clear for demonstration
                $stmt = $db->prepare("SELECT id, stored_name, size FROM files WHERE user_id = ?");
                $stmt->execute([$userId]);
                $files = $stmt->fetchAll();
                
                $freedSpace = 0;
                $uploadDir = BASE_PATH . '/storage/files';
                
                foreach ($files as $f) {
                    $p = $uploadDir . '/' . $f['stored_name'];
                    if (file_exists($p)) {
                        unlink($p);
                    }
                    $freedSpace += $f['size'];
                    
                    // Set file_id to null in messages to prevent broken links
                    $db->prepare("UPDATE messages SET file_id = NULL WHERE file_id = ?")->execute([$f['id']]);
                }
                
                // Delete file records
                $db->prepare("DELETE FROM files WHERE user_id = ?")->execute([$userId]);
                
                // Reset storage counter
                $db->prepare("UPDATE users SET storage_used = 0 WHERE id = ?")->execute([$userId]);
                
                $success = "Storage cleared. Freed " . round($freedSpace / 1024 / 1024, 2) . " MB.";
                $user['storage_used'] = 0;
            }
        }

        View::render('profile/settings', [
            'title' => 'Profile Settings',
            'user' => $user,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function uploadAvatar()
    {
        $this->handleImageUpload('avatar_url');
    }

    public function uploadCover()
    {
        $this->handleImageUpload('cover_url');
    }

    public function removeAvatar()
    {
        $this->handleImageRemove('avatar_url');
    }

    public function removeCover()
    {
        $this->handleImageRemove('cover_url');
    }

    private function handleImageRemove($column)
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); return; }
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT {$column} FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();
        if ($row && $row[$column]) {
            $path = BASE_PATH . '/public' . $row[$column];
            if (file_exists($path)) @unlink($path);
        }
        $db->prepare("UPDATE users SET {$column} = NULL WHERE id = ?")->execute([$_SESSION['user_id']]);
        echo json_encode(['success' => true]);
    }

    private function handleImageUpload($column)
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['image'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No image uploaded']);
            return;
        }

        $file = $_FILES['image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Upload failed']);
            return;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid format. Use JPG, PNG, WEBP or GIF']);
            return;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'File too large (max 5MB)']);
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'jpg';
        $filename = uniqid('img_') . '.' . $ext;
        $uploadDir = BASE_PATH . '/public/uploads/profiles';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $dest = $uploadDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $url = '/uploads/profiles/' . $filename;
            
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE users SET {$column} = :url WHERE id = :id");
            $stmt->execute([':url' => $url, ':id' => $_SESSION['user_id']]);

            echo json_encode(['success' => true, 'url' => $url]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save image']);
        }
    }
}
