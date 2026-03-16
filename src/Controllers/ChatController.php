<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\File;
use App\Services\EncryptionService;
use App\Services\AiChatService; // We'll implement this later

class ChatController
{
    private $userModel;
    private $conversationModel;
    private $messageModel;
    private $fileModel;
    private $encryptionService;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->userModel = new User();
        $this->conversationModel = new Conversation();
        $this->messageModel = new Message();
        $this->fileModel = new File();
        $this->encryptionService = new EncryptionService();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);
        $conversations = $this->conversationModel->getForUser($userId);
        
        // Decrypt latest_message previews for sidebar display
        foreach ($conversations as &$c) {
            if (!empty($c['latest_message'])) {
                $decrypted = $this->encryptionService->decrypt($c['latest_message']);
                if ($decrypted !== false && $decrypted !== '') {
                    $c['latest_message'] = mb_strlen($decrypted) > 50 ? mb_substr($decrypted, 0, 50) . '...' : $decrypted;
                } else {
                    $c['latest_message'] = '📎 Attachment';
                }
            }
        }
        unset($c);

        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT g.*, gm.role, 
            (SELECT content FROM messages WHERE group_id = g.id ORDER BY created_at DESC LIMIT 1) as latest_message 
            FROM groups g JOIN group_members gm ON g.id = gm.group_id WHERE gm.user_id = ?");
        $stmt->execute([$userId]);
        $groups = $stmt->fetchAll();

        // Decrypt latest_message previews for groups
        foreach ($groups as &$g) {
            if (!empty($g['latest_message'])) {
                $decrypted = $this->encryptionService->decrypt($g['latest_message']);
                if ($decrypted !== false && $decrypted !== '') {
                    $g['latest_message'] = mb_strlen($decrypted) > 50 ? mb_substr($decrypted, 0, 50) . '...' : $decrypted;
                } else {
                    $g['latest_message'] = '📎 Attachment';
                }
            }
        }
        unset($g);

        // Clean any buffered output (whitespace from includes/router) before
        // rendering the standalone full-screen chat page
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $title = 'SecureChat Dashboard';
        $viewFile = BASE_PATH . '/src/Views/chat/index.php';
        extract(['title' => $title, 'user' => $user, 'conversations' => $conversations, 'groups' => $groups]);
        require $viewFile;
        exit;
    }

    public function searchUsers()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 2) {
            echo json_encode(['users' => []]);
            exit;
        }

        $users = $this->userModel->searchByUsername($query, $_SESSION['user_id']);
        
        // Also search for public groups
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT id, name, description FROM groups WHERE (name LIKE ? OR description LIKE ?) AND is_private = 0 LIMIT 10");
        $likeQuery = '%' . $query . '%';
        $stmt->execute([$likeQuery, $likeQuery]);
        $groups = $stmt->fetchAll();
        
        foreach ($groups as $g) {
            $users[] = [
                'id' => 'g' . $g['id'],
                'username' => $g['name'],
                'status_message' => $g['description'],
                'is_group' => true
            ];
        }

        echo json_encode(['users' => $users]);
        exit;
    }

    public function getUserProfile()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = $_GET['id'] ?? null;
        if (!$userId) {
            echo json_encode(['error' => 'User ID is required']);
            exit;
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            echo json_encode(['error' => 'User not found']);
            exit;
        }

        // Exclude sensitive information
        unset($user['password_hash']);
        // Format the join date
        if (isset($user['created_at'])) {
            $user['join_date'] = date('F Y', strtotime($user['created_at']));
        }
        
        echo json_encode(['user' => $user]);
        exit;
    }

    public function sendMessage()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $recipientId = $_POST['recipient_id'] ?? null;
        $groupId = $_POST['group_id'] ?? null;
        $subgroupId = $_POST['subgroup_id'] ?? null;
        $content = trim($_POST['content'] ?? '');
        
        if ((!$recipientId && !$groupId) || (empty($content) && empty($_FILES['attachment']))) {
            echo json_encode(['error' => 'Invalid parameters']);
            exit;
        }

        $db = \App\Core\Database::getInstance();
        
        // Group Logic vs Direct Message Logic
        if ($groupId) {
            // Check membership, permissions, and elder quota
            $stmt = $db->prepare("SELECT g.chat_permission, g.elder_msg_per_hour, g.elder_max_chars, gm.role FROM groups g JOIN group_members gm ON gm.group_id = g.id WHERE g.id = ? AND gm.user_id = ?");
            $stmt->execute([$groupId, $userId]);
            $groupData = $stmt->fetch();
            
            if (!$groupData) {
                echo json_encode(['error' => 'Not a member of this group']);
                exit;
            }
            
            $role = $groupData['role'];
            $reqLevel = $groupData['chat_permission'] ?? 'member';
            
            // Permission hierarchy: owner > admin > elder > member
            $roleWeight = ['owner' => 4, 'admin' => 3, 'elder' => 2, 'member' => 1];
            $requiredWeight = $roleWeight[$reqLevel] ?? 1;
            $userWeight = $roleWeight[$role] ?? 1;

            if ($userWeight < $requiredWeight) {
                $levelNames = ['admin' => 'admins', 'elder' => 'elders and admins', 'member' => 'all members'];
                echo json_encode(['error' => 'Only ' . ($levelNames[$reqLevel] ?? $reqLevel) . ' can chat in this group']);
                exit;
            }

            // Elder quota enforcement (admins/owners bypass)
            if ($role === 'elder') {
                $maxPerHour = (int)($groupData['elder_msg_per_hour'] ?? 15);
                $maxChars = (int)($groupData['elder_max_chars'] ?? 200);

                // Check message length
                if (strlen($content) > $maxChars) {
                    echo json_encode(['error' => "Message too long. Elder limit is {$maxChars} characters.", 'quota_exceeded' => true]);
                    exit;
                }

                // Count messages in the last hour
                $stmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE group_id = ? AND sender_id = ? AND created_at >= datetime('now', '-1 hour')");
                $stmt->execute([$groupId, $userId]);
                $msgCount = (int)$stmt->fetchColumn();

                if ($msgCount >= $maxPerHour) {
                    // Quota exceeded — send to pending for admin approval
                    $encPending = empty($content) ? '' : $this->encryptionService->encrypt($content);
                    $stmt = $db->prepare("INSERT INTO pending_messages (group_id, subgroup_id, sender_id, content) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$groupId, $subgroupId ?: null, $userId, $encPending]);
                    echo json_encode(['success' => true, 'pending_approval' => true, 'message' => 'Quota exceeded. Message sent for admin approval.']);
                    exit;
                }
            }
        } else {
            // Verify recipient exists for DM
            $recipient = $this->userModel->findById($recipientId);
            if (!$recipient) {
                echo json_encode(['error' => 'Recipient not found']);
                exit;
            }
        }

        $fileId = null;
        
        // Handle file upload if present
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['attachment'];
            $fileSize = $file['size'];
            
            $sender = $this->userModel->findById($userId);
            
            // Check storage limit
            if ($sender['storage_used'] + $fileSize > $sender['storage_limit']) {
                echo json_encode(['error' => 'Storage limit exceeded. Please upgrade or delete old files.']);
                exit;
            }

            // Encrypt and store
            $uploadDir = BASE_PATH . '/storage/files';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $originalName = basename($file['name']);
            $storedName = bin2hex(random_bytes(16)) . '.enc';
            $destPath = $uploadDir . '/' . $storedName;

            $fileContent = file_get_contents($file['tmp_name']);
            $encryptedContent = $this->encryptionService->encrypt($fileContent);
            file_put_contents($destPath, $encryptedContent);

            $fileId = $this->fileModel->create($userId, $originalName, $storedName, $file['type'], $fileSize, 1);
            
            // Update storage
            $this->userModel->updateStorage($userId, $fileSize);
        }

        // Encrypt message content (AES)
        $encryptedMsg = empty($content) ? '' : $this->encryptionService->encrypt($content);

        if ($groupId) {
            $stmt = $db->prepare("INSERT INTO messages (conversation_id, group_id, subgroup_id, sender_id, content, file_id) VALUES (0, ?, ?, ?, ?, ?)");
            $stmt->execute([$groupId, $subgroupId ?: null, $userId, $encryptedMsg, $fileId]);
            $messageId = $db->lastInsertId();
            $conversationId = 'g' . $groupId . '-' . $subgroupId; // Mock frontend ID
        } else {
            // Find or create conversation
            $conversationId = $this->conversationModel->findOrCreate($userId, $recipientId);
            $messageId = $this->messageModel->create($conversationId, $userId, $encryptedMsg, $fileId);
        }

        // Check if recipient is the System Bot (only in DMs)
        if (!$groupId && $recipientId && $messageId) {
            $aiChat = new \App\Services\AiChatService();
            if ($recipientId == $aiChat->getBotId()) {
                // Determine file content if attached
                $fileContentForBot = null;
                $fileMimeType = null;
                if ($fileId && file_exists($destPath)) {
                    $fileContentForBot = $this->encryptionService->decrypt(file_get_contents($destPath));
                    $fileMimeType = $file['type'] ?? 'application/octet-stream';
                }
                
                // Fire AI generation inline (ideally a background job, but inline for demo)
                $aiChat->generateReply($conversationId, $content, $fileContentForBot, $fileMimeType);
            }
        }

        if ($messageId) {
            echo json_encode(['success' => true, 'message_id' => $messageId, 'conversation_id' => $conversationId]);
        } else {
            echo json_encode(['error' => 'Database error']);
        }
        exit;
    }

    public function getMessages()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $lastId = $_GET['last_id'] ?? 0;
        
        $db = \App\Core\Database::getInstance();
        $messages = [];

        if (isset($_GET['group_id'])) {
            $groupId = (int)$_GET['group_id'];
            $subId = (int)($_GET['subgroup_id'] ?? 0);
            
            // Check membership
            $stmt = $db->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
            $stmt->execute([$groupId, $userId]);
            if (!$stmt->fetchColumn()) {
                echo json_encode(['error' => 'Unauthorized']); exit;
            }

            // Fetch group messages
            $sql = "SELECT m.*, f.original_name, f.size, f.mime_type, u.username as sender_name 
                    FROM messages m 
                    LEFT JOIN files f ON m.file_id = f.id 
                    LEFT JOIN users u ON m.sender_id = u.id
                    WHERE m.group_id = ? ".($subId ? "AND m.subgroup_id = ?" : "AND m.subgroup_id IS NULL")." 
                    AND m.id > ? ORDER BY m.id ASC";
            $stmt = $db->prepare($sql);
            if ($subId) {
                $stmt->execute([$groupId, $subId, $lastId]);
            } else {
                $stmt->execute([$groupId, $lastId]);
            }
            $messages = $stmt->fetchAll();
            
        } else if (isset($_GET['conversation_id'])) {
            $conversationId = $_GET['conversation_id'];
            // Verify DM participation logic would normally go here
            $messages = $this->messageModel->getMessages($conversationId, $lastId);
            
            // Mark as read
            if (count($messages) > 0) {
                $this->messageModel->markAsRead($conversationId, $userId);
            }
        }
        
        $decryptedMessages = [];
        foreach ($messages as $msg) {
            $decryptedContent = empty($msg['content']) ? '' : $this->encryptionService->decrypt($msg['content']);
            
            $decryptedMessages[] = [
                'id' => $msg['id'],
                'conversation_id' => $msg['conversation_id'] ?? ('g'.$msg['group_id'].'-'.$msg['subgroup_id']),
                'sender_id' => $msg['sender_id'],
                'sender_name' => $msg['sender_name'] ?? null, // Will only be set for groups
                'content' => $decryptedContent,
                'created_at' => $msg['created_at'],
                'file' => $msg['file_id'] ? [
                    'id' => $msg['file_id'],
                    'name' => $msg['original_name'],
                    'size' => $msg['size'],
                    'mime' => $msg['mime_type']
                ] : null
            ];
        }

        // Mark as read (DMs only — groups don't use conversation_id)
        if (isset($conversationId) && count($decryptedMessages) > 0) {
            $this->messageModel->markAsRead($conversationId, $userId);
        }

        echo json_encode(['messages' => $decryptedMessages]);
        exit;
    }

    public function blockUser()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $blockerId = $_SESSION['user_id'];
        $blockedId = (int)($_POST['blocked_id'] ?? 0);
        if (!$blockedId || $blockedId === $blockerId) { echo json_encode(['error' => 'Invalid user']); exit; }
        $db = \App\Core\Database::getInstance();
        $db->prepare("INSERT OR IGNORE INTO blocked_users (blocker_id, blocked_id) VALUES (?, ?)")->execute([$blockerId, $blockedId]);
        echo json_encode(['success' => true, 'action' => 'blocked']);
        exit;
    }

    public function unblockUser()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $blockerId = $_SESSION['user_id'];
        $blockedId = (int)($_POST['blocked_id'] ?? 0);
        $db = \App\Core\Database::getInstance();
        $db->prepare("DELETE FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?")->execute([$blockerId, $blockedId]);
        echo json_encode(['success' => true, 'action' => 'unblocked']);
        exit;
    }

    public function getBlockedUsers()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $userId = $_SESSION['user_id'];
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT u.id, u.username FROM blocked_users b JOIN users u ON u.id = b.blocked_id WHERE b.blocker_id = ?");
        $stmt->execute([$userId]);
        echo json_encode(['blocked' => $stmt->fetchAll()]);
        exit;
    }

    public function deleteConversation()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $userId = $_SESSION['user_id'];
        $convId  = (int)($_POST['conversation_id'] ?? 0);
        if (!$convId) { echo json_encode(['error' => 'Invalid conversation']); exit; }
        $db = \App\Core\Database::getInstance();
        // Verify ownership
        $conv = $db->prepare("SELECT * FROM conversations WHERE id = ? AND (user1_id = ? OR user2_id = ?)");
        $conv->execute([$convId, $userId, $userId]);
        if (!$conv->fetch()) { echo json_encode(['error' => 'Not found']); exit; }
        // Delete messages + conversation
        $db->prepare("DELETE FROM messages WHERE conversation_id = ?")->execute([$convId]);
        $db->prepare("DELETE FROM conversations WHERE id = ?")->execute([$convId]);
        echo json_encode(['success' => true]);
        exit;
    }

    public function forwardMessage()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $userId = $_SESSION['user_id'];
        $db = \App\Core\Database::getInstance();

        $messageIds = $_POST['message_ids'] ?? [$_POST['message_id'] ?? 0];
        $toUserIds = $_POST['to_user_ids'] ?? [$_POST['to_user_id'] ?? 0];
        if (!is_array($messageIds)) $messageIds = [$messageIds];
        if (!is_array($toUserIds)) $toUserIds = [$toUserIds];
        $toUserIds = array_slice(array_filter(array_map('intval', $toUserIds)), 0, 10);
        $messageIds = array_filter(array_map('intval', $messageIds));
        if (empty($messageIds) || empty($toUserIds)) { echo json_encode(['error' => 'Invalid parameters']); exit; }

        $forwarded = 0;
        foreach ($messageIds as $msgId) {
            $stmt = $db->prepare("SELECT * FROM messages WHERE id = ?");
            $stmt->execute([$msgId]);
            $original = $stmt->fetch();
            if (!$original) continue;
            foreach ($toUserIds as $toUserId) {
                if ($toUserId == $userId) continue;
                $conv = $db->prepare("SELECT id FROM conversations WHERE (user1_id=? AND user2_id=?) OR (user1_id=? AND user2_id=?) LIMIT 1");
                $conv->execute([$userId, $toUserId, $toUserId, $userId]);
                $targetConv = $conv->fetch();
                if (!$targetConv) {
                    $db->prepare("INSERT INTO conversations (user1_id, user2_id) VALUES (?, ?)")->execute([$userId, $toUserId]);
                    $targetConvId = $db->lastInsertId();
                } else { $targetConvId = $targetConv['id']; }
                $fwdContent = $original['content'] ?: null;
                $db->prepare("INSERT INTO messages (conversation_id, sender_id, content, file_id) VALUES (?, ?, ?, ?)")
                   ->execute([$targetConvId, $userId, $fwdContent, $original['file_id'] ?: null]);
                $db->prepare("UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$targetConvId]);
                $forwarded++;
            }
        }
        echo json_encode(['success' => true, 'forwarded' => $forwarded]);
        exit;
    }

    // GET /api/chat/contacts
    public function getContactList()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $userId = $_SESSION['user_id'];
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT DISTINCT u.id, u.username, u.avatar_url, u.status_message
            FROM conversations c
            JOIN users u ON (u.id = CASE WHEN c.user1_id = ? THEN c.user2_id ELSE c.user1_id END)
            WHERE (c.user1_id = ? OR c.user2_id = ?)
            AND u.id NOT IN (SELECT blocked_id FROM blocked_users WHERE user_id = ?)
            ORDER BY u.username ASC
        ");
        $stmt->execute([$userId, $userId, $userId, $userId]);
        echo json_encode(['contacts' => $stmt->fetchAll()]);
        exit;
    }

    // GET /api/chat/media?conversation_id=X
    public function getSharedMedia()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $userId = $_SESSION['user_id'];
        $convId = (int)($_GET['conversation_id'] ?? 0);
        if (!$convId) { echo json_encode(['error' => 'Missing conversation_id']); exit; }
        $db = \App\Core\Database::getInstance();
        $check = $db->prepare("SELECT id FROM conversations WHERE id = ? AND (user1_id = ? OR user2_id = ?)");
        $check->execute([$convId, $userId, $userId]);
        if (!$check->fetch()) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $stmt = $db->prepare("
            SELECT m.id as message_id, m.content, m.created_at, m.sender_id, u.username as sender_name,
                   f.id as file_id, f.original_name, f.stored_name, f.mime_type, f.size
            FROM messages m JOIN users u ON u.id = m.sender_id LEFT JOIN files f ON f.id = m.file_id
            WHERE m.conversation_id = ? AND (m.file_id IS NOT NULL OR m.content LIKE '%http%')
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$convId]);
        $rows = $stmt->fetchAll();
        $photos = []; $videos = []; $documents = []; $links = [];
        foreach ($rows as $r) {
            if ($r['file_id']) {
                $item = ['file_id'=>$r['file_id'], 'name'=>$r['original_name'], 'stored'=>$r['stored_name'], 'mime'=>$r['mime_type'], 'size'=>$r['size'], 'date'=>$r['created_at'], 'sender'=>$r['sender_name'], 'sender_id'=>$r['sender_id']];
                if (strpos($r['mime_type'],'image/')===0) $photos[]=$item;
                elseif (strpos($r['mime_type'],'video/')===0) $videos[]=$item;
                else $documents[]=$item;
            }
            if ($r['content'] && preg_match_all('/https?:\/\/[^\s<>"\']+/', $r['content'], $m)) {
                foreach ($m[0] as $url) $links[] = ['url'=>$url, 'date'=>$r['created_at'], 'sender'=>$r['sender_name'], 'sender_id'=>$r['sender_id']];
            }
        }
        echo json_encode(['photos'=>$photos,'videos'=>$videos,'documents'=>$documents,'links'=>$links]);
        exit;
    }

    // GET /api/files/all — global file manager
    public function getAllMedia()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $userId = $_SESSION['user_id'];
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT m.id as message_id, m.content, m.created_at, m.sender_id, m.conversation_id, m.group_id,
                   u.username as sender_name,
                   f.id as file_id, f.original_name, f.stored_name, f.mime_type, f.size,
                   CASE WHEN m.sender_id = ? THEN 'sent' ELSE 'received' END as direction
            FROM messages m JOIN users u ON u.id = m.sender_id LEFT JOIN files f ON f.id = m.file_id
            WHERE (m.conversation_id IN (SELECT id FROM conversations WHERE user1_id=? OR user2_id=?)
                   OR m.group_id IN (SELECT group_id FROM group_members WHERE user_id=?))
            AND (m.file_id IS NOT NULL OR m.content LIKE '%http%')
            ORDER BY m.created_at DESC LIMIT 500
        ");
        $stmt->execute([$userId, $userId, $userId, $userId]);
        $rows = $stmt->fetchAll();
        $photos = []; $videos = []; $documents = []; $links = [];
        foreach ($rows as $r) {
            if ($r['file_id']) {
                $item = ['file_id'=>$r['file_id'],'name'=>$r['original_name'],'stored'=>$r['stored_name'],'mime'=>$r['mime_type'],'size'=>$r['size'],'date'=>$r['created_at'],'sender'=>$r['sender_name'],'sender_id'=>$r['sender_id'],'direction'=>$r['direction'],'conv_id'=>$r['conversation_id'],'group_id'=>$r['group_id']];
                if (strpos($r['mime_type'],'image/')===0) $photos[]=$item;
                elseif (strpos($r['mime_type'],'video/')===0) $videos[]=$item;
                else $documents[]=$item;
            }
            if ($r['content'] && preg_match_all('/https?:\/\/[^\s<>"\']+/', $r['content'], $m)) {
                foreach ($m[0] as $url) $links[] = ['url'=>$url,'date'=>$r['created_at'],'sender'=>$r['sender_name'],'sender_id'=>$r['sender_id'],'direction'=>$r['direction']];
            }
        }
        echo json_encode(['photos'=>$photos,'videos'=>$videos,'documents'=>$documents,'links'=>$links]);
        exit;
    }

    // POST /api/files/delete — bulk delete
    public function deleteMedia()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $userId = $_SESSION['user_id'];
        $fileIds = $_POST['file_ids'] ?? [];
        if (!is_array($fileIds)) $fileIds = [$fileIds];
        $fileIds = array_filter(array_map('intval', $fileIds));
        if (empty($fileIds)) { echo json_encode(['error' => 'No files selected']); exit; }
        $db = \App\Core\Database::getInstance();
        $deleted = 0;
        foreach ($fileIds as $fid) {
            $stmt = $db->prepare("SELECT f.id, f.stored_name FROM files f JOIN messages m ON m.file_id = f.id WHERE f.id = ? AND m.sender_id = ? LIMIT 1");
            $stmt->execute([$fid, $userId]);
            $file = $stmt->fetch();
            if ($file) {
                $path = BASE_PATH . '/public/uploads/' . $file['stored_name'];
                if (file_exists($path)) @unlink($path);
                $db->prepare("UPDATE messages SET file_id = NULL WHERE file_id = ?")->execute([$fid]);
                $db->prepare("DELETE FROM files WHERE id = ?")->execute([$fid]);
                $deleted++;
            }
        }
        echo json_encode(['success' => true, 'deleted' => $deleted]);
        exit;
    }

    public function getPreferences()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $userId = $_SESSION['user_id'];
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$userId]);
        $prefs = $stmt->fetch();
        if (!$prefs) {
            $db->prepare("INSERT OR IGNORE INTO user_preferences (user_id) VALUES (?)")->execute([$userId]);
            $prefs = ['user_id' => $userId, 'theme' => 'dark', 'chat_theme' => 'default', 'analytics_opt_out' => 0, 'show_read_receipts' => 1, 'show_online_status' => 1];
        }
        echo json_encode(['preferences' => $prefs]);
        exit;
    }

    public function savePreferences()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
        $userId = $_SESSION['user_id'];
        $db = \App\Core\Database::getInstance();
        $allowed = ['theme', 'chat_theme', 'chat_bg_color', 'chat_bubble_color', 'analytics_opt_out', 'data_retention_days', 'show_read_receipts', 'show_online_status'];
        $updates = [];
        $params = [];
        foreach ($allowed as $field) {
            if (isset($_POST[$field])) {
                $updates[] = "$field = ?";
                $params[] = $_POST[$field];
            }
        }
        if (empty($updates)) { echo json_encode(['error' => 'No data']); exit; }
        $params[] = $userId;
        $db->prepare("INSERT OR IGNORE INTO user_preferences (user_id) VALUES (?)")->execute([$userId]);
        $db->prepare("UPDATE user_preferences SET " . implode(', ', $updates) . " WHERE user_id = ?")->execute($params);
        echo json_encode(['success' => true]);
        exit;
    }
}

