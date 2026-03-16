<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Services\EncryptionService;

class AiChatService
{
    private $apiKey;
    private $messageModel;
    private $encryptionService;

    // We define the System Bot's user ID statically.
    // In a real system, we'd ensure a user with ID=1 or a specific flag exists.
    // Let's assume the System Bot has ID = 1 (or we can use a dedicated constant).
    const SYSTEM_BOT_ID = 1;

    public function __construct()
    {
        // Prefer GROQ_API_KEY, fallback to AI_API_KEY for backward compatibility
        $this->apiKey = $_ENV['GROQ_API_KEY'] ?? $_ENV['AI_API_KEY'] ?? '';
        $this->messageModel = new Message();
        $this->encryptionService = new EncryptionService();
    }

    public function ensureSystemBotExists()
    {
        $userModel = new User();
        $bot = $userModel->findById(self::SYSTEM_BOT_ID);
        if (!$bot) {
            $userModel->create('SystemBot', 'bot@securechat.local', bin2hex(random_bytes(16)), 'admin');
        }
    }

    public function getBotId()
    {
        $userModel = new User();
        $bot = $userModel->findByEmail('bot@securechat.local');
        if (!$bot) {
            $userModel->create('SystemBot', 'bot@securechat.local', bin2hex(random_bytes(16)), 'admin');
            $bot = $userModel->findByEmail('bot@securechat.local');
        }
        return $bot['id'];
    }

    /**
     * Call Groq API (Llama 3.3 70B) to reply to a message and insert it as the bot.
     */
    public function generateReply($conversationId, $userMessageText, $fileContent = null, $fileMimeType = null)
    {
        if (empty($this->apiKey) || $this->apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
            $this->saveBotMessage($conversationId, "I'm offline right now. My API key hasn't been configured.");
            return;
        }

        $botId = $this->getBotId();

        // Build the messages array for Groq (OpenAI-compatible format)
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are SecureBot, a helpful AI assistant integrated into SecureShare chat application. You are friendly, concise, and helpful. Reply in a conversational tone. Keep responses under 200 words unless the user asks for a detailed explanation.'
            ],
            [
                'role' => 'user',
                'content' => $userMessageText
            ]
        ];

        // If a file is attached, add its content to the user message
        if ($fileContent) {
            if ($fileMimeType && (strpos($fileMimeType, 'image/') === 0)) {
                // Groq text models can't process images, so just acknowledge it
                $messages[1]['content'] .= "\n\n[The user attached an image file. Please acknowledge that you received it but note that you cannot process images directly.]";
            } else {
                // For text/document files, include content
                $messages[1]['content'] .= "\n\nAttached file content:\n" . substr($fileContent, 0, 8000);
            }
        }

        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $payload = [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 800,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // If cURL itself failed
        if ($response === false) {
            $this->saveBotMessage($conversationId, "⚠️ I couldn't connect to the AI server. Network error: " . $curlError);
            return;
        }

        $replyText = "";

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['choices'][0]['message']['content'])) {
                $replyText = trim($data['choices'][0]['message']['content']);
            } else {
                $replyText = "🤔 I received an unexpected response. Please try again.";
            }
        } elseif ($httpCode === 429) {
            $replyText = "⏳ I'm getting too many requests right now. Please try again in a minute.";
        } elseif ($httpCode === 401) {
            $replyText = "🔑 My API key is invalid. Please ask the admin to check the GROQ_API_KEY setting.";
        } else {
            $errData = json_decode($response, true);
            $errMsg = $errData['error']['message'] ?? "Unknown error";
            $replyText = "❌ Error (HTTP $httpCode): $errMsg";
        }

        $this->saveBotMessage($conversationId, $replyText);
    }

    private function saveBotMessage($conversationId, $textContent)
    {
        $botId = $this->getBotId();
        $encryptedMsg = $this->encryptionService->encrypt($textContent);
        $this->messageModel->create($conversationId, $botId, $encryptedMsg, null);
    }
}
