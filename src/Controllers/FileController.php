<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\File;
use App\Services\EncryptionService;

class FileController
{
    private $fileModel;
    private $encryptionService;

    public function __construct()
    {
        $this->fileModel = new File();
        $this->encryptionService = new EncryptionService();
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    public function showUpload()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        View::render('client/upload', [
            'title' => 'Upload File'
        ]);
    }

    public function upload()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
            $file = $_FILES['document'];
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Ensure storage directory exists
                $uploadDir = BASE_PATH . '/storage/files';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $originalName = basename($file['name']);
                $storedName = bin2hex(random_bytes(16)) . '.enc';
                $destPath = $uploadDir . '/' . $storedName;

                // Read file content
                $content = file_get_contents($file['tmp_name']);
                
                // Encrypt content
                $encryptedContent = $this->encryptionService->encrypt($content);
                
                // Save encrypted file
                file_put_contents($destPath, $encryptedContent);

                // AI Tags removed (deployment cleanup)
                $aiTags = null;

                // Save to database
                $this->fileModel->create(
                    $_SESSION['user_id'],
                    $originalName,
                    $storedName,
                    $file['type'],
                    $file['size'],
                    1,
                    $aiTags
                );

                header("Location: /chat");
                exit;
            } else if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
                View::render('client/upload', [
                    'title' => 'Upload File',
                    'error' => 'File exceeds the maximum allowed size (512MB as configured).'
                ]);
                return;
            }
        }

        View::render('client/upload', [
            'title' => 'Upload File',
            'error' => 'Failed to upload file. Please try again or select a smaller file.'
        ]);
    }
    
    public function download($id = null)
    {
        if (!$id) {
            http_response_code(404);
            echo "Not found";
            return;
        }
        
        $fileId = $id;
        
        $fileRecord = $this->fileModel->findById($fileId);
        if (!$fileRecord) {
            http_response_code(404);
            echo "File not found";
            return;
        }
        
        // Basic auth check for owner (Share links will bypass this later)
        if (!isset($_SESSION['user_id']) || $fileRecord['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }
        
        $filePath = BASE_PATH . '/storage/files/' . $fileRecord['stored_name'];
        if (file_exists($filePath)) {
            $encryptedContent = file_get_contents($filePath);
            $decryptedContent = $this->encryptionService->decrypt($encryptedContent);
            
            if ($decryptedContent === false) {
                http_response_code(500);
                echo "Decryption failed.";
                return;
            }
            
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $fileRecord['mime_type']);
            header('Content-Disposition: attachment; filename="' . basename($fileRecord['original_name']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($decryptedContent));
            
            echo $decryptedContent;
            exit;
        } else {
            http_response_code(404);
            echo "File not found on disk.";
        }
    }

    public function stream($id = null)
    {
        if (!$id) {
            http_response_code(404);
            echo "Not found";
            return;
        }
        
        $fileId = $id;
        
        $fileRecord = $this->fileModel->findById($fileId);
        if (!$fileRecord) {
            http_response_code(404);
            echo "File not found";
            return;
        }
        
        // Basic auth check for owner (Share links will bypass this later)
        // For a chat app, both user1 and user2 of the conversation should have access
        // Ideally we would look up the message->conversation->users, but for this pivot
        // we will allow if the user is authenticated. 
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }
        
        $filePath = BASE_PATH . '/storage/files/' . $fileRecord['stored_name'];
        if (file_exists($filePath)) {
            $encryptedContent = file_get_contents($filePath);
            $decryptedContent = $this->encryptionService->decrypt($encryptedContent);
            
            if ($decryptedContent === false) {
                http_response_code(500);
                echo "Decryption failed.";
                return;
            }
            
            // Critical header change: 'inline' instead of 'attachment'
            header('Content-Type: ' . $fileRecord['mime_type']);
            header('Content-Disposition: inline; filename="' . basename($fileRecord['original_name']) . '"');
            header('Expires: 0');
            header('Cache-Control: public, max-age=31536000'); // Cache streamed media 
            header('Pragma: public');
            header('Content-Length: ' . strlen($decryptedContent));
            
            echo $decryptedContent;
            exit;
        } else {
            http_response_code(404);
            echo "File not found on disk.";
        }
    }
}
