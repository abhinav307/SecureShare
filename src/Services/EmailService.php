<?php

namespace App\Services;

/**
 * Lightweight SMTP Email Service for sending OTP emails via Gmail.
 * Uses raw socket connection with STARTTLS - no Composer/PHPMailer needed.
 */
class EmailService
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $fromName;

    public function __construct()
    {
        $this->host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $this->port = (int)($_ENV['SMTP_PORT'] ?? 587);
        $this->username = $_ENV['SMTP_USERNAME'] ?? '';
        $this->password = $_ENV['SMTP_PASSWORD'] ?? '';
        $this->fromName = $_ENV['SMTP_FROM_NAME'] ?? 'SecureShare';
    }

    /**
     * Check if SMTP is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->username) && !empty($this->password);
    }

    /**
     * Send an OTP email
     */
    public function sendOtpEmail(string $to, string $otp, string $purpose = 'Password Reset'): bool
    {
        $subject = "SecureShare - Your {$purpose} OTP";
        
        $htmlBody = $this->buildOtpEmailHtml($otp, $purpose);
        $textBody = "Your 6-digit OTP for SecureShare ({$purpose}) is: {$otp}\n\nThis OTP is valid for 15 minutes.\n\nIf you did not request this, please ignore this email.";

        return $this->sendEmail($to, $subject, $htmlBody, $textBody);
    }

    /**
     * Build a beautiful HTML email for OTP
     */
    private function buildOtpEmailHtml(string $otp, string $purpose): string
    {
        $digits = str_split($otp);
        $digitBoxes = '';
        foreach ($digits as $d) {
            $digitBoxes .= "<span style=\"display:inline-block;width:42px;height:50px;line-height:50px;text-align:center;font-size:24px;font-weight:bold;color:#4f46e5;background:#f1f5f9;border:2px solid #e2e8f0;border-radius:10px;margin:0 3px;font-family:'Segoe UI',Arial,sans-serif;\">{$d}</span>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#0f172a;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0f172a;padding:40px 20px;">
<tr><td align="center">
<table width="480" cellpadding="0" cellspacing="0" style="background:#1e293b;border-radius:16px;overflow:hidden;border:1px solid rgba(255,255,255,0.05);">
    <tr><td style="background:linear-gradient(135deg,#4f46e5,#6366f1);padding:32px;text-align:center;">
        <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:700;">SecureShare</h1>
        <p style="color:rgba(255,255,255,0.8);margin:8px 0 0;font-size:14px;">{$purpose} Verification</p>
    </td></tr>
    <tr><td style="padding:32px;">
        <p style="color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 24px;">Hello,</p>
        <p style="color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 24px;">Your one-time verification code is:</p>
        <div style="text-align:center;padding:20px 0;">{$digitBoxes}</div>
        <p style="color:#94a3b8;font-size:13px;text-align:center;margin:16px 0 24px;">This code expires in <strong style="color:#f1f5f9;">15 minutes</strong></p>
        <div style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);border-radius:12px;padding:16px;margin-top:16px;">
            <p style="color:#818cf8;font-size:12px;margin:0;line-height:1.5;">🔒 If you didn't request this code, you can safely ignore this email. Someone may have entered your email address by mistake.</p>
        </div>
    </td></tr>
    <tr><td style="padding:20px 32px;border-top:1px solid rgba(255,255,255,0.05);text-align:center;">
        <p style="color:#64748b;font-size:11px;margin:0;">© SecureShare · End-to-End Encrypted Messaging</p>
    </td></tr>
</table>
</td></tr>
</table>
</body>
</html>
HTML;
    }

    /**
     * Send email — tries SMTP socket first, falls back to PHP mail() if blocked
     */
    private function sendEmail(string $to, string $subject, string $htmlBody, string $textBody): bool
    {
        if (!$this->isConfigured()) {
            error_log("EmailService: SMTP not configured. Set SMTP_USERNAME and SMTP_PASSWORD in .env");
            return false;
        }

        // Try SMTP socket first (works locally and on most hosts)
        $smtpResult = $this->sendViaSmtp($to, $subject, $htmlBody, $textBody);
        
        if ($smtpResult) {
            return true;
        }

        // Fallback: Use PHP's built-in mail() function (works on InfinityFree and shared hosting)
        error_log("EmailService: SMTP socket failed, falling back to PHP mail()");
        return $this->sendViaPhpMail($to, $subject, $htmlBody, $textBody);
    }

    /**
     * Send via direct SMTP socket connection (STARTTLS)
     */
    private function sendViaSmtp(string $to, string $subject, string $htmlBody, string $textBody): bool
    {
        $boundary = md5(uniqid(time()));
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: {$this->fromName} <{$this->username}>\r\n";
        $headers .= "To: {$to}\r\n";
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
        $headers .= "X-Mailer: SecureShare/1.0\r\n";

        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $body .= $textBody . "\r\n\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $body .= $htmlBody . "\r\n\r\n";
        $body .= "--{$boundary}--\r\n";

        try {
            $socket = @fsockopen('tcp://' . $this->host, $this->port, $errno, $errstr, 10);
            if (!$socket) {
                error_log("EmailService: SMTP connection failed: {$errstr} ({$errno})");
                return false;
            }

            stream_set_timeout($socket, 10);
            $this->readResponse($socket); // 220 greeting

            $this->sendCommand($socket, "EHLO localhost");
            $this->sendCommand($socket, "STARTTLS");

            // Enable TLS
            $crypto = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
            if (!$crypto) {
                error_log("EmailService: STARTTLS failed");
                fclose($socket);
                return false;
            }

            $this->sendCommand($socket, "EHLO localhost");
            $this->sendCommand($socket, "AUTH LOGIN");
            $this->sendCommand($socket, base64_encode($this->username));
            $response = $this->sendCommand($socket, base64_encode($this->password));

            if (strpos($response, '235') === false) {
                error_log("EmailService: Authentication failed: {$response}");
                fclose($socket);
                return false;
            }

            $this->sendCommand($socket, "MAIL FROM:<{$this->username}>");
            $this->sendCommand($socket, "RCPT TO:<{$to}>");
            $this->sendCommand($socket, "DATA");

            // Send message data
            fwrite($socket, $headers . "\r\n" . $body . "\r\n.\r\n");
            $dataResponse = $this->readResponse($socket);

            $this->sendCommand($socket, "QUIT");
            fclose($socket);

            $success = strpos($dataResponse, '250') !== false;
            if (!$success) {
                error_log("EmailService: SMTP send failed: {$dataResponse}");
            }
            return $success;

        } catch (\Exception $e) {
            error_log("EmailService: SMTP exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fallback: Send via PHP's built-in mail() function
     * Works on shared hosting (InfinityFree, 000WebHost, etc.) where SMTP ports may be blocked
     */
    private function sendViaPhpMail(string $to, string $subject, string $htmlBody, string $textBody): bool
    {
        try {
            $boundary = md5(uniqid(time()));

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "From: {$this->fromName} <{$this->username}>\r\n";
            $headers .= "Reply-To: {$this->username}\r\n";
            $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
            $headers .= "X-Mailer: SecureShare/1.0\r\n";

            $body = "--{$boundary}\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $body .= $textBody . "\r\n\r\n";
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
            $body .= $htmlBody . "\r\n\r\n";
            $body .= "--{$boundary}--\r\n";

            $result = @mail($to, $subject, $body, $headers);
            
            if (!$result) {
                error_log("EmailService: PHP mail() also failed for {$to}");
            }
            return $result;

        } catch (\Exception $e) {
            error_log("EmailService: PHP mail() exception: " . $e->getMessage());
            return false;
        }
    }

    private function sendCommand($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->readResponse($socket);
    }

    private function readResponse($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            // End of multi-line response: 4th char is space (e.g., "250 OK")
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $response;
    }
}
