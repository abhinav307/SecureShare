<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Database;

class ServerAdminController
{
    private $db;
    private $serverSecret;
    private $allowedEmails;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = Database::getInstance();
        // The server secret key and allowed admin emails from .env (comma-separated)
        $this->serverSecret = $_ENV['SERVER_ADMIN_SECRET'] ?? '';
        $emailStr = $_ENV['SERVER_ADMIN_EMAIL'] ?? '';
        $this->allowedEmails = array_map('trim', array_map('strtolower', explode(',', $emailStr)));
    }

    /**
     * Step 1: Show the server admin login page (secret key input)
     */
    public function showLogin()
    {
        // If already authenticated as server admin, go to dashboard
        if (isset($_SESSION['server_admin']) && $_SESSION['server_admin'] === true) {
            header('Location: /server-admin/dashboard');
            exit;
        }

        $error = $_GET['error'] ?? null;
        // Render the server admin login page (standalone, not using main layout)
        include __DIR__ . '/../Views/server_admin/login.php';
    }

    /**
     * Step 1 handler: Verify the server secret key, then redirect to Google OAuth
     */
    public function verifySecret()
    {
        $secret = $_POST['server_secret'] ?? '';

        if (empty($this->serverSecret)) {
            header('Location: /server-admin?error=' . urlencode('Server admin not configured. Set SERVER_ADMIN_SECRET in .env'));
            exit;
        }

        if (!hash_equals($this->serverSecret, $secret)) {
            // Log failed attempt
            error_log('[SERVER ADMIN] Failed login attempt from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            header('Location: /server-admin?error=' . urlencode('Invalid server secret key. This attempt has been logged.'));
            exit;
        }

        // Secret is correct - store in session and redirect to Google OAuth for identity verification
        $_SESSION['server_admin_step1'] = true;
        $_SESSION['server_admin_step1_time'] = time();

        // Redirect to Google OAuth with a special state parameter
        $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
        $redirectUri = rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/') . '/server-admin/google-callback';

        $params = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account',
            'state'         => 'server_admin_verify',
        ]);

        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
        exit;
    }

    /**
     * Step 2: Google OAuth callback - verify the email matches the allowed admin
     */
    public function googleCallback()
    {
        // Check step 1 was completed
        if (empty($_SESSION['server_admin_step1'])) {
            header('Location: /server-admin?error=' . urlencode('Please enter the server secret key first.'));
            exit;
        }

        // Check step 1 hasn't expired (5 minutes)
        if (time() - ($_SESSION['server_admin_step1_time'] ?? 0) > 300) {
            unset($_SESSION['server_admin_step1'], $_SESSION['server_admin_step1_time']);
            header('Location: /server-admin?error=' . urlencode('Session expired. Please try again.'));
            exit;
        }

        $code = $_GET['code'] ?? '';
        if (!$code) {
            header('Location: /server-admin?error=' . urlencode('Google authentication failed.'));
            exit;
        }

        // Exchange code for token
        $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
        $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
        $redirectUri = rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/') . '/server-admin/google-callback';

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => $redirectUri,
                'grant_type'    => 'authorization_code',
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $tokenData = json_decode($response, true);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            header('Location: /server-admin?error=' . urlencode('Failed to verify Google identity.'));
            exit;
        }

        // Get user profile
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tokenData['access_token']],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $profileResponse = curl_exec($ch);
        curl_close($ch);
        $googleUser = json_decode($profileResponse, true);

        if (!$googleUser || !isset($googleUser['email'])) {
            header('Location: /server-admin?error=' . urlencode('Failed to retrieve Google profile.'));
            exit;
        }

        // Verify the email matches one of the allowed admin emails
        if (!in_array(strtolower($googleUser['email']), $this->allowedEmails)) {
            error_log('[SERVER ADMIN] Unauthorized email attempt: ' . $googleUser['email'] . ' from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            unset($_SESSION['server_admin_step1'], $_SESSION['server_admin_step1_time']);
            header('Location: /server-admin?error=' . urlencode('Access denied. Your Google account is not authorized for server administration. This attempt has been logged.'));
            exit;
        }

        // Both verifications passed! Grant server admin access
        $_SESSION['server_admin'] = true;
        $_SESSION['server_admin_email'] = $googleUser['email'];
        $_SESSION['server_admin_name'] = $googleUser['name'] ?? 'Admin';
        $_SESSION['server_admin_picture'] = $googleUser['picture'] ?? '';
        $_SESSION['server_admin_login_time'] = time();
        unset($_SESSION['server_admin_step1'], $_SESSION['server_admin_step1_time']);

        header('Location: /server-admin/dashboard');
        exit;
    }

    /**
     * Require server admin authentication
     */
    private function requireServerAdmin()
    {
        if (empty($_SESSION['server_admin']) || $_SESSION['server_admin'] !== true) {
            header('Location: /server-admin');
            exit;
        }

        // Auto-expire after 2 hours
        if (time() - ($_SESSION['server_admin_login_time'] ?? 0) > 7200) {
            $this->logout();
        }
    }

    /**
     * Server Admin Dashboard
     */
    public function dashboard()
    {
        $this->requireServerAdmin();

        // Fetch all users with standard details (no encrypted content!)
        $users = $this->db->query("
            SELECT id, username, email, role, first_name, last_name, phone_number,
                   storage_used, storage_limit, created_at, status_message, avatar_url
            FROM users
            ORDER BY created_at DESC
        ")->fetchAll();

        $totalUsers = count($users);
        $totalStorage = array_sum(array_column($users, 'storage_used'));
        $totalLimit = array_sum(array_column($users, 'storage_limit'));

        // Count total files (metadata only, not content)
        $fileCount = $this->db->query("SELECT COUNT(*) as cnt FROM files")->fetch()['cnt'] ?? 0;

        // Count total conversations
        $convCount = $this->db->query("SELECT COUNT(*) as cnt FROM conversations")->fetch()['cnt'] ?? 0;

        // Count total messages (just count, not content)
        $msgCount = $this->db->query("SELECT COUNT(*) as cnt FROM messages")->fetch()['cnt'] ?? 0;

        // Recent signups (last 7 days)
        $recentSignups = $this->db->query("
            SELECT COUNT(*) as cnt FROM users 
            WHERE created_at >= datetime('now', '-7 days')
        ")->fetch()['cnt'] ?? 0;

        // Database file size
        $dbPath = BASE_PATH . '/storage/database.sqlite';
        $dbSize = file_exists($dbPath) ? filesize($dbPath) : 0;

        // Storage directory size
        $storageDir = BASE_PATH . '/storage/files';
        $storageDirSize = 0;
        if (is_dir($storageDir)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($storageDir)) as $file) {
                if ($file->isFile()) $storageDirSize += $file->getSize();
            }
        }

        include __DIR__ . '/../Views/server_admin/dashboard.php';
    }

    /**
     * Delete a user from server admin panel
     */
    public function deleteUser()
    {
        $this->requireServerAdmin();

        $userId = (int)($_POST['user_id'] ?? 0);
        if (!$userId) {
            header('Location: /server-admin/dashboard?error=Invalid+user');
            exit;
        }

        try {
            // Get username before deletion for logging
            $user = $this->db->prepare("SELECT username, email FROM users WHERE id = ?");
            $user->execute([$userId]);
            $userData = $user->fetch();

            // Delete physical files from disk
            $files = $this->db->prepare("SELECT stored_name FROM files WHERE user_id = ?");
            $files->execute([$userId]);
            $uploadDir = BASE_PATH . '/storage/files';
            foreach ($files->fetchAll() as $f) {
                $path = $uploadDir . '/' . $f['stored_name'];
                if (file_exists($path)) @unlink($path);
            }

            // Full cascade delete across ALL tables
            $this->db->prepare("DELETE FROM blocked_users WHERE blocker_id = ? OR blocked_id = ?")->execute([$userId, $userId]);
            $this->db->prepare("DELETE FROM user_preferences WHERE user_id = ?")->execute([$userId]);
            $this->db->prepare("DELETE FROM group_join_requests WHERE user_id = ?")->execute([$userId]);
            $this->db->prepare("DELETE FROM pending_messages WHERE sender_id = ?")->execute([$userId]);
            $this->db->prepare("DELETE FROM group_members WHERE user_id = ?")->execute([$userId]);
            $this->db->prepare("DELETE FROM downloads WHERE user_id = ?")->execute([$userId]);
            $this->db->prepare("DELETE FROM shares WHERE file_id IN (SELECT id FROM files WHERE user_id = ?)")->execute([$userId]);
            $this->db->prepare("DELETE FROM messages WHERE sender_id = ?")->execute([$userId]);
            $this->db->prepare("UPDATE messages SET file_id = NULL WHERE file_id IN (SELECT id FROM files WHERE user_id = ?)")->execute([$userId]);
            $this->db->prepare("DELETE FROM files WHERE user_id = ?")->execute([$userId]);
            $this->db->prepare("DELETE FROM conversations WHERE user1_id = ? OR user2_id = ?")->execute([$userId, $userId]);
            $this->db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$userData['email'] ?? '']);
            // Transfer group ownership or delete groups owned by this user
            $this->db->prepare("DELETE FROM groups WHERE owner_id = ?")->execute([$userId]);
            $this->db->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);

            error_log('[SERVER ADMIN] User deleted: @' . ($userData['username'] ?? 'unknown') . ' (' . ($userData['email'] ?? 'unknown') . ') by ' . $_SESSION['server_admin_email']);

            header('Location: /server-admin/dashboard?success=' . urlencode('User @' . ($userData['username'] ?? '') . ' deleted successfully'));
            exit;
        } catch (\Exception $e) {
            error_log('[SERVER ADMIN] Delete failed: ' . $e->getMessage());
            header('Location: /server-admin/dashboard?error=' . urlencode('Failed to delete user: ' . $e->getMessage()));
            exit;
        }
    }

    /**
     * Update user storage limit
     */
    public function updateStorage()
    {
        $this->requireServerAdmin();

        $userId = (int)($_POST['user_id'] ?? 0);
        $limitMb = (int)($_POST['storage_limit_mb'] ?? 64);
        $limitBytes = $limitMb * 1024 * 1024;

        if ($userId) {
            $this->db->prepare("UPDATE users SET storage_limit = ? WHERE id = ?")->execute([$limitBytes, $userId]);
        }

        header('Location: /server-admin/dashboard?success=' . urlencode('Storage limit updated to ' . $limitMb . 'MB'));
        exit;
    }

    /**
     * Logout from server admin
     */
    public function logout()
    {
        unset(
            $_SESSION['server_admin'],
            $_SESSION['server_admin_email'],
            $_SESSION['server_admin_name'],
            $_SESSION['server_admin_picture'],
            $_SESSION['server_admin_login_time'],
            $_SESSION['server_admin_step1'],
            $_SESSION['server_admin_step1_time']
        );
        header('Location: /login');
        exit;
    }
}
