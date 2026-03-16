<?php

namespace App\Services;

class EncryptionService
{
    private $key;
    private $cipher = 'aes-256-cbc';

    public function __construct()
    {
        // In a real app, this should be a robust key from .env
        $this->key = $_ENV['ENCRYPTION_KEY'] ?? 'fallback_insecure_key_1234567890!';
        // Hash it to ensure it's the correct length for AES-256
        $this->key = hash('sha256', $this->key, true);
    }

    public function encrypt($data)
    {
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($data, $this->cipher, $this->key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary=true);
        return base64_encode( $iv.$hmac.$ciphertext_raw );
    }

    public function decrypt($c)
    {
        $c = base64_decode($c);
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary=true);
        if (hash_equals($hmac, $calcmac)) // timeline attack resistant comparison
        {
            return openssl_decrypt($ciphertext_raw, $this->cipher, $this->key, $options=OPENSSL_RAW_DATA, $iv);
        }
        return false;
    }
}
