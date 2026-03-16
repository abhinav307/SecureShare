<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\File;
use App\Models\Share;
use App\Models\Download;
use App\Services\EncryptionService;

class ShareController
{
    private $fileModel;
    private $shareModel;
    private $downloadModel;
    private $encryptionService;

    public function __construct()
    {
        $this->fileModel = new File();
        $this->shareModel = new Share();
        $this->downloadModel = new Download();
        $this->encryptionService = new EncryptionService();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Handles /share/{file_id}
    public function createSharePage($fileId = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
        
        if (!$fileId) {
            http_response_code(404);
            return;
        }
        
        $file = $this->fileModel->findById($fileId);

        if (!$file || $file['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo "Forbidden or File not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = bin2hex(random_bytes(16)); // Secure unique link
            $expires = $_POST['expires'] ?? null;
            if (empty($expires)) $expires = null;
            
            if ($this->shareModel->create($fileId, $token, 'read', $expires)) {
                $shareUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/') . '/s/' . $token;
                View::render('client/share_create', [
                    'title' => 'Share File',
                    'file' => $file,
                    'shareUrl' => $shareUrl
                ]);
                return;
            }
        }

        View::render('client/share_create', [
            'title' => 'Share File',
            'file' => $file
        ]);
    }

    // Handles /s/{token}
    public function viewShare($token = null)
    {
        if (!$token) {
            http_response_code(404);
            return;
        }
        
        $share = $this->shareModel->findByToken($token);

        if (!$share) {
            http_response_code(404);
            View::render('home', ['title' => 'Not Found', 'error' => 'Share link invalid or expired.']);
            return;
        }

        // Check expiration
        if ($share['expires_at'] && strtotime($share['expires_at']) < time()) {
            http_response_code(403);
            die("This share link has expired.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'])) {
            // Track download
            $userId = $_SESSION['user_id'] ?? null;
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $this->downloadModel->record($share['file_id'], $userId, $ipAddress);

            // Serve file
            $filePath = BASE_PATH . '/storage/files/' . $share['stored_name'];
            if (file_exists($filePath)) {
                $encryptedContent = file_get_contents($filePath);
                $decryptedContent = $this->encryptionService->decrypt($encryptedContent);
                
                header('Content-Description: File Transfer');
                header('Content-Type: ' . $share['mime_type']);
                header('Content-Disposition: attachment; filename="' . basename($share['original_name']) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . strlen($decryptedContent));
                
                echo $decryptedContent;
                exit;
            } else {
                die("File no longer exists on server.");
            }
        }

        View::render('client/share_view', [
            'title' => 'Download File',
            'share' => $share
        ]);
    }
}
