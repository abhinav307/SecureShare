<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Database;
use App\Models\User;
use App\Services\EmailService;

class AuthController
{
    private $userModel;
    private $emailService;

    public function __construct()
    {
        $this->userModel = new User();
        $this->emailService = new EmailService();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showLogin()
    {
        View::render('auth/login', ['title' => 'Login']);
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: /chat");
            exit;
        }

        View::render('auth/login', [
            'title' => 'Login',
            'error' => 'Invalid email or password.'
        ]);
    }

    public function showRegister()
    {
        View::render('auth/register', ['title' => 'Register']);
    }

    /**
     * Step 1: Validate input, send OTP email, redirect to verification page
     */
    public function register()
    {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validate Gmail only
        if (!preg_match('/@gmail\.com$/i', $email)) {
            View::render('auth/register', [
                'title' => 'Register',
                'error' => 'Only Gmail addresses (@gmail.com) are accepted for registration.'
            ]);
            return;
        }

        if (empty($username) || strlen($username) < 3) {
            View::render('auth/register', [
                'title' => 'Register',
                'error' => 'Username must be at least 3 characters.'
            ]);
            return;
        }

        if (strlen($password) < 8) {
            View::render('auth/register', [
                'title' => 'Register',
                'error' => 'Password must be at least 8 characters.'
            ]);
            return;
        }

        if ($password !== $password_confirm) {
            View::render('auth/register', [
                'title' => 'Register',
                'error' => 'Passwords do not match.'
            ]);
            return;
        }

        // Check if user already exists
        if ($this->userModel->findByEmail($email)) {
            View::render('auth/register', [
                'title' => 'Register',
                'error' => 'An account with this email already exists.'
            ]);
            return;
        }

        $db = Database::getInstance();

        // Check username taken
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            View::render('auth/register', [
                'title' => 'Register',
                'error' => 'This username is already taken.'
            ]);
            return;
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash = password_hash($otp, PASSWORD_DEFAULT);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $expiresAt = date('Y-m-d H:i:s', time() + 900); // 15 minutes

        // Clean old verifications for this email
        $db->prepare("DELETE FROM email_verifications WHERE email = ?")->execute([$email]);

        // Store verification record
        $db->prepare("INSERT INTO email_verifications (email, username, password_hash, otp_hash, expires_at) VALUES (?, ?, ?, ?, ?)")
           ->execute([$email, $username, $passwordHash, $otpHash, $expiresAt]);

        // Send OTP via Gmail SMTP
        $mailSent = false;
        if ($this->emailService->isConfigured()) {
            $mailSent = $this->emailService->sendOtpEmail($email, $otp, 'Account Verification');
        }

        if (!$mailSent) {
            View::render('auth/register', [
                'title' => 'Register',
                'error' => 'Failed to send verification email. Please check SMTP configuration or try again later.'
            ]);
            return;
        }

        View::render('auth/verify_email', [
            'title' => 'Verify Email',
            'email' => $email,
            'success' => 'A 6-digit OTP has been sent to your Gmail. Please check your inbox.'
        ]);
    }

    /**
     * Show the email verification form
     */
    public function showVerifyEmail()
    {
        $email = $_GET['email'] ?? '';
        View::render('auth/verify_email', ['title' => 'Verify Email', 'email' => $email]);
    }

    /**
     * Step 2: Verify OTP and create the account
     */
    public function verifyEmail()
    {
        $email = trim($_POST['email'] ?? '');
        $otp = trim($_POST['otp'] ?? '');

        if (empty($email) || empty($otp)) {
            View::render('auth/verify_email', [
                'title' => 'Verify Email',
                'email' => $email,
                'error' => 'Email and OTP are required.'
            ]);
            return;
        }

        $db = Database::getInstance();

        // Find valid verification record
        $stmt = $db->prepare("SELECT * FROM email_verifications WHERE email = ? AND expires_at > datetime('now') ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$email]);
        $record = $stmt->fetch();

        if (!$record || !password_verify($otp, $record['otp_hash'])) {
            View::render('auth/verify_email', [
                'title' => 'Verify Email',
                'email' => $email,
                'error' => 'Invalid or expired OTP. Please request a new one.'
            ]);
            return;
        }

        // Create the user account
        try {
            $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$record['username'], $record['email'], $record['password_hash']]);
            $userId = $db->lastInsertId();

            // Clean up verification records
            $db->prepare("DELETE FROM email_verifications WHERE email = ?")->execute([$email]);

            // Log the user in
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $record['username'];
            $_SESSION['role'] = 'user';
            header("Location: /chat");
            exit;

        } catch (\PDOException $e) {
            View::render('auth/verify_email', [
                'title' => 'Verify Email',
                'email' => $email,
                'error' => 'Account creation failed. Username or email may already exist.'
            ]);
            return;
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: /");
        exit;
    }
}
