<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Database;
use App\Models\User;
use App\Services\EmailService;

class PasswordResetController
{
    private $db;
    private $userModel;
    private $emailService;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = Database::getInstance();
        $this->userModel = new User();
        $this->emailService = new EmailService();
    }

    /**
     * Show the "Forgot Password" form
     */
    public function showForgotPassword()
    {
        View::render('auth/forgot_password', ['title' => 'Forgot Password']);
    }

    /**
     * Handle the email submission, generate & send OTP via Gmail SMTP
     */
    public function sendOtp()
    {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            View::render('auth/forgot_password', [
                'title' => 'Forgot Password',
                'error' => 'Please enter a valid email address.'
            ]);
            return;
        }

        // Only allow Gmail addresses
        if (!preg_match('/@gmail\.com$/i', $email)) {
            View::render('auth/forgot_password', [
                'title' => 'Forgot Password',
                'error' => 'Only Gmail addresses (@gmail.com) are supported.'
            ]);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        // Always show generic success for security - don't reveal if email exists
        if ($user) {
            // Generate secure 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $otpHash = password_hash($otp, PASSWORD_DEFAULT);
            $expiresAt = date('Y-m-d H:i:s', time() + 900); // 15 minutes

            // Clean old OTPs for this email
            $this->db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

            // Store new OTP
            $this->db->prepare("INSERT INTO password_resets (email, otp_hash, expires_at) VALUES (?, ?, ?)")
                     ->execute([$email, $otpHash, $expiresAt]);

            // Send OTP via Gmail SMTP
            if ($this->emailService->isConfigured()) {
                $this->emailService->sendOtpEmail($email, $otp, 'Password Reset');
            }
        }

        View::render('auth/verify_otp', [
            'title' => 'Verify OTP',
            'email' => $email,
            'success' => 'If your email is registered, a 6-digit OTP has been sent to your Gmail inbox.'
        ]);
    }

    /**
     * Show the OTP verification form
     */
    public function showVerifyOtp()
    {
        $email = $_GET['email'] ?? '';
        View::render('auth/verify_otp', ['title' => 'Verify OTP', 'email' => $email]);
    }

    /**
     * Verify OTP and reset the password
     */
    public function resetPassword()
    {
        $email       = trim($_POST['email'] ?? '');
        $otp         = trim($_POST['otp'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';

        if (empty($email) || empty($otp) || empty($newPassword)) {
            View::render('auth/verify_otp', [
                'title' => 'Verify OTP',
                'email' => $email,
                'error' => 'All fields are required.'
            ]);
            return;
        }

        if ($newPassword !== $confirm) {
            View::render('auth/verify_otp', [
                'title' => 'Verify OTP',
                'email' => $email,
                'error' => 'Passwords do not match.'
            ]);
            return;
        }

        if (strlen($newPassword) < 8) {
            View::render('auth/verify_otp', [
                'title' => 'Verify OTP',
                'email' => $email,
                'error' => 'Password must be at least 8 characters.'
            ]);
            return;
        }

        // Find valid OTP record
        $stmt = $this->db->prepare("SELECT * FROM password_resets WHERE email = ? AND expires_at > datetime('now') ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$email]);
        $record = $stmt->fetch();

        if (!$record || !password_verify($otp, $record['otp_hash'])) {
            View::render('auth/verify_otp', [
                'title' => 'Verify OTP',
                'email' => $email,
                'error' => 'Invalid or expired OTP. Please request a new one.'
            ]);
            return;
        }

        // Update password
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->prepare("UPDATE users SET password_hash = ? WHERE email = ?")
                 ->execute([$hash, $email]);

        // Clean up OTP records
        $this->db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

        View::render('auth/login', [
            'title'   => 'Login',
            'success' => 'Your password has been reset successfully! You can now log in with your new password.'
        ]);
    }
}
