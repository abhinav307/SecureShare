<?php

// public/index.php

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load environment variables (Simple parser)
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Simple Autoloader
spl_autoload_register(function ($class) {
    // Map App\ to src/
    $prefix = 'App\\';
    $base_dir = BASE_PATH . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize router
$router = new \App\Core\Router();

// Define routes
$router->get('/', [\App\Controllers\HomeController::class, 'index']);

// Auth Routes
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login']);
$router->get('/register', [\App\Controllers\AuthController::class, 'showRegister']);
$router->post('/register', [\App\Controllers\AuthController::class, 'register']);
$router->get('/register/verify', [\App\Controllers\AuthController::class, 'showVerifyEmail']);
$router->post('/register/verify', [\App\Controllers\AuthController::class, 'verifyEmail']);
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);

// Dashboard Route
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index']);

// File Routes
$router->get('/upload', [\App\Controllers\FileController::class, 'showUpload']);
$router->post('/upload', [\App\Controllers\FileController::class, 'upload']);
$router->get('/download/{id}', [\App\Controllers\FileController::class, 'download']);

// Share Routes
$router->get('/share/{id}', [\App\Controllers\ShareController::class, 'createSharePage']);
$router->post('/share/{id}', [\App\Controllers\ShareController::class, 'createSharePage']);
$router->get('/s/{token}', [\App\Controllers\ShareController::class, 'viewShare']);
$router->post('/s/{token}', [\App\Controllers\ShareController::class, 'viewShare']);

// File Streaming Route
$router->get('/api/file/stream/{id}', [\App\Controllers\FileController::class, 'stream']);

// Chat Application Routes
$router->get('/chat', [\App\Controllers\ChatController::class, 'index']);
$router->get('/api/users/search', [\App\Controllers\ChatController::class, 'searchUsers']);
$router->get('/api/users/profile', [\App\Controllers\ChatController::class, 'getUserProfile']);
$router->post('/api/chat/send', [\App\Controllers\ChatController::class, 'sendMessage']);
$router->get('/api/chat/messages', [\App\Controllers\ChatController::class, 'getMessages']);
$router->post('/api/chat/block', [\App\Controllers\ChatController::class, 'blockUser']);
$router->post('/api/chat/unblock', [\App\Controllers\ChatController::class, 'unblockUser']);
$router->get('/api/chat/blocked', [\App\Controllers\ChatController::class, 'getBlockedUsers']);
$router->post('/api/chat/delete-conversation', [\App\Controllers\ChatController::class, 'deleteConversation']);
$router->post('/api/chat/forward', [\App\Controllers\ChatController::class, 'forwardMessage']);
$router->get('/api/user/preferences', [\App\Controllers\ChatController::class, 'getPreferences']);
$router->post('/api/user/preferences', [\App\Controllers\ChatController::class, 'savePreferences']);
// Group Chat Routes
$router->post('/api/groups/create', [\App\Controllers\GroupController::class, 'createGroup']);
$router->post('/api/groups/edit', [\App\Controllers\GroupController::class, 'editGroup']);
$router->post('/api/groups/members/add', [\App\Controllers\GroupController::class, 'addMember']);
$router->post('/api/groups/members/remove', [\App\Controllers\GroupController::class, 'removeMember']);
$router->post('/api/groups/members/role', [\App\Controllers\GroupController::class, 'updateRole']);
$router->post('/api/groups/subgroups/create', [\App\Controllers\GroupController::class, 'createSubgroup']);
$router->post('/api/groups/delete', [\App\Controllers\GroupController::class, 'deleteGroup']);
$router->get('/api/groups/search', [\App\Controllers\GroupController::class, 'searchGroups']);
$router->get('/api/groups/info', [\App\Controllers\GroupController::class, 'getGroupInfo']);
$router->get('/api/groups/subgroups', [\App\Controllers\GroupController::class, 'getSubgroups']);
$router->post('/api/groups/avatar', [\App\Controllers\GroupController::class, 'uploadGroupAvatar']);
$router->post('/api/groups/cover', [\App\Controllers\GroupController::class, 'uploadGroupCover']);
$router->get('/api/groups/pending', [\App\Controllers\GroupController::class, 'getPendingMessages']);
$router->post('/api/groups/pending/approve', [\App\Controllers\GroupController::class, 'approvePendingMessage']);
$router->post('/api/groups/pending/reject', [\App\Controllers\GroupController::class, 'rejectPendingMessage']);
$router->post('/api/groups/join', [\App\Controllers\GroupController::class, 'requestJoinGroup']);
$router->get('/api/groups/join-requests', [\App\Controllers\GroupController::class, 'getJoinRequests']);
$router->post('/api/groups/join-requests/approve', [\App\Controllers\GroupController::class, 'approveJoinRequest']);
$router->post('/api/groups/join-requests/reject', [\App\Controllers\GroupController::class, 'rejectJoinRequest']);
$router->post('/api/groups/invite-link', [\App\Controllers\GroupController::class, 'generateInviteLink']);
$router->post('/api/groups/join-via-link', [\App\Controllers\GroupController::class, 'joinViaInvite']);
$router->post('/api/groups/avatar/remove', [\App\Controllers\GroupController::class, 'removeGroupAvatar']);
$router->post('/api/groups/cover/remove', [\App\Controllers\GroupController::class, 'removeGroupCover']);
$router->get('/api/groups/media', [\App\Controllers\GroupController::class, 'getGroupMedia']);


// Profile Routes
$router->get('/profile/settings', [\App\Controllers\ProfileController::class, 'settings']);
$router->post('/profile/settings', [\App\Controllers\ProfileController::class, 'settings']);
$router->post('/api/profile/upload-avatar', [\App\Controllers\ProfileController::class, 'uploadAvatar']);
$router->post('/api/profile/upload-cover', [\App\Controllers\ProfileController::class, 'uploadCover']);
$router->post('/api/profile/remove-avatar', [\App\Controllers\ProfileController::class, 'removeAvatar']);
$router->post('/api/profile/remove-cover', [\App\Controllers\ProfileController::class, 'removeCover']);

// Chat Media & File Manager Routes
$router->get('/api/chat/contacts', [\App\Controllers\ChatController::class, 'getContactList']);
$router->get('/api/chat/media', [\App\Controllers\ChatController::class, 'getSharedMedia']);
$router->get('/api/files/all', [\App\Controllers\ChatController::class, 'getAllMedia']);
$router->post('/api/files/delete', [\App\Controllers\ChatController::class, 'deleteMedia']);


// Google OAuth Routes
$router->get('/auth/google', [\App\Controllers\GoogleAuthController::class, 'redirect']);
$router->get('/auth/google/callback', [\App\Controllers\GoogleAuthController::class, 'callback']);

// Password Reset (OTP) Routes
$router->get('/forgot-password', [\App\Controllers\PasswordResetController::class, 'showForgotPassword']);
$router->post('/forgot-password/send', [\App\Controllers\PasswordResetController::class, 'sendOtp']);
$router->get('/forgot-password/verify', [\App\Controllers\PasswordResetController::class, 'showVerifyOtp']);
$router->post('/forgot-password/reset', [\App\Controllers\PasswordResetController::class, 'resetPassword']);

// Admin Routes
$router->get('/admin', [\App\Controllers\AdminController::class, 'dashboard']);
$router->post('/admin/delete', [\App\Controllers\AdminController::class, 'deleteUser']);
$router->post('/admin/promote', [\App\Controllers\AdminController::class, 'promoteUser']);

// Server Admin Routes (2-step: Secret Key + Google OAuth)
$router->get('/server-admin', [\App\Controllers\ServerAdminController::class, 'showLogin']);
$router->post('/server-admin/verify', [\App\Controllers\ServerAdminController::class, 'verifySecret']);
$router->get('/server-admin/google-callback', [\App\Controllers\ServerAdminController::class, 'googleCallback']);
$router->get('/server-admin/dashboard', [\App\Controllers\ServerAdminController::class, 'dashboard']);
$router->post('/server-admin/delete-user', [\App\Controllers\ServerAdminController::class, 'deleteUser']);
$router->post('/server-admin/update-storage', [\App\Controllers\ServerAdminController::class, 'updateStorage']);
$router->get('/server-admin/logout', [\App\Controllers\ServerAdminController::class, 'logout']);

// Legal Pages
$router->get('/privacy', function() { require BASE_PATH . '/src/Views/legal/privacy.php'; });
$router->get('/terms', function() { require BASE_PATH . '/src/Views/legal/terms.php'; });

// Serve static files from /storage/
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($uri, '/storage/') === 0) {
    $filePath = BASE_PATH . $uri;
    if (file_exists($filePath) && is_file($filePath)) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml'];
        header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
