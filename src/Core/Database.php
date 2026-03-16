<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;

    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance === null) {
            try {
                // Ensure storage directory exists
                $dbDir = BASE_PATH . '/storage';
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0777, true);
                }

                $dbFile = BASE_PATH . '/' . ($_ENV['DB_DATABASE'] ?? 'storage/database.sqlite');
                self::$instance = new PDO("sqlite:" . $dbFile);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                self::initSchema();
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    private static function initSchema()
    {
        $db = self::$instance;
        
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                storage_used INTEGER DEFAULT 0,
                storage_limit INTEGER DEFAULT 33554432,
                avatar_url TEXT,
                cover_url TEXT,
                status_message TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS files (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                original_name TEXT NOT NULL,
                stored_name TEXT NOT NULL,
                mime_type TEXT,
                size INTEGER,
                ai_tags TEXT,
                is_encrypted INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )",
            "CREATE TABLE IF NOT EXISTS shares (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                file_id INTEGER,
                share_token TEXT UNIQUE NOT NULL,
                permissions TEXT DEFAULT 'read',
                expires_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (file_id) REFERENCES files(id)
            )",
            "CREATE TABLE IF NOT EXISTS downloads (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                file_id INTEGER,
                user_id INTEGER NULL,
                ip_address TEXT,
                downloaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (file_id) REFERENCES files(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            )"
        ];

        foreach ($queries as $query) {
            $db->exec($query);
        }

        // Add cover_url if it doesn't exist (for existing databases)
        try {
            $db->exec("ALTER TABLE users ADD COLUMN cover_url TEXT");
        } catch (PDOException $e) {
            // Ignore if column already exists
        }
    }
}
