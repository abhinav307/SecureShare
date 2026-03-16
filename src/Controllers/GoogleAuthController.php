<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\User;

class GoogleAuthController
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
        $this->clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
        $this->redirectUri = rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/') . '/auth/google/callback';
    }

    /**
     * Redirect user to Google's OAuth consent screen
     */
    public function redirect()
    {
        $params = http_build_query([
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ]);

        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
        exit;
    }

    /**
     * Handle the callback from Google after user grants permission
     */
    public function callback()
    {
        $code = $_GET['code'] ?? '';

        if (!$code) {
            header('Location: /login?error=Google+authentication+failed');
            exit;
        }

        // Exchange the authorization code for an access token
        $tokenData = $this->exchangeCodeForToken($code);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            header('Location: /login?error=Failed+to+get+Google+token');
            exit;
        }

        // Use the access token to get the user's profile information
        $googleUser = $this->getGoogleUserInfo($tokenData['access_token']);

        if (!$googleUser || !isset($googleUser['email'])) {
            header('Location: /login?error=Failed+to+get+Google+profile');
            exit;
        }

        $email = $googleUser['email'];
        $name  = $googleUser['name'] ?? explode('@', $email)[0];

        // Check if this Google user already exists in our database
        $userModel = new User();
        $existingUser = $userModel->findByEmail($email);

        if ($existingUser) {
            // User exists — log them in
            $_SESSION['user_id']  = $existingUser['id'];
            $_SESSION['username'] = $existingUser['username'];
            $_SESSION['role']     = $existingUser['role'];
            header('Location: /chat');
            exit;
        }

        // New user — register them automatically
        $db = Database::getInstance();

        // Generate a random secure password (they won't need it since they use Google)
        $randomPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

        // Use Google name as username, ensure uniqueness
        $username = preg_replace('/[^a-zA-Z0-9_ ]/', '', $name);
        $username = trim($username) ?: 'User' . rand(1000, 9999);

        // Check if username taken, if so append random digits
        $checkStmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->execute([$username]);
        if ($checkStmt->fetch()) {
            $username = $username . rand(100, 999);
        }

        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$username, $email, $randomPassword]);

        $userId = $db->lastInsertId();

        // If Google provides a profile picture, save it
        if (!empty($googleUser['picture'])) {
            $db->prepare("UPDATE users SET avatar_url = ? WHERE id = ?")->execute([$googleUser['picture'], $userId]);
        }

        $_SESSION['user_id']  = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role']     = 'user';

        header('Location: /chat');
        exit;
    }

    /**
     * Exchange authorization code for access token
     */
    private function exchangeCodeForToken($code)
    {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri'  => $this->redirectUri,
                'grant_type'    => 'authorization_code',
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Get user profile info from Google using the access token
     */
    private function getGoogleUserInfo($accessToken)
    {
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
