<?php

// ─── Security: Require a secret key to run migrations ───────────────
// Usage: php migrate.php (CLI) or visit migrate.php?key=YOUR_SECRET_KEY (browser)
$MIGRATE_SECRET = 'secureshare_migrate_2026'; // Change this to your own secret!

if (php_sapi_name() !== 'cli') {
    // Running in browser — require secret key
    if (!isset($_GET['key']) || $_GET['key'] !== $MIGRATE_SECRET) {
        http_response_code(403);
        die('⛔ Access denied. Provide the correct migration key as ?key=YOUR_SECRET');
    }
    // Make output readable in browser
    header('Content-Type: text/plain; charset=utf-8');
}

try {
    $db = new PDO('sqlite:' . __DIR__ . '/storage/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

echo "Running migrations for Chat Pivot...\n";

// 1. Add new columns to users table
$columnsToAdd = [
    "ALTER TABLE users ADD COLUMN storage_limit INTEGER DEFAULT 536870912;" => "users.storage_limit", // 512MB
    "ALTER TABLE users ADD COLUMN storage_used INTEGER DEFAULT 0;" => "users.storage_used",
    "ALTER TABLE users ADD COLUMN avatar_url TEXT DEFAULT NULL;" => "users.avatar_url",
    "ALTER TABLE users ADD COLUMN status_message TEXT DEFAULT NULL;" => "users.status_message"
];

foreach ($columnsToAdd as $sql => $colName) {
    try {
        $db->exec($sql);
        echo "Added column: $colName\n";
    } catch (PDOException $e) {
        // Ignore "duplicate column name" errors if ran multiple times
        if (strpos($e->getMessage(), 'duplicate column name') === false) {
            echo "Error adding $colName: " . $e->getMessage() . "\n";
        } else {
            echo "Column $colName already exists.\n";
        }
    }
}

// 2. Create conversations table
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS conversations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user1_id INTEGER NOT NULL,
            user2_id INTEGER NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user1_id) REFERENCES users(id),
            FOREIGN KEY (user2_id) REFERENCES users(id)
        )
    ');
    echo "Created table: conversations\n";
} catch (PDOException $e) {
    echo "Error creating conversations: " . $e->getMessage() . "\n";
}

// 3. Create messages table
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            conversation_id INTEGER NOT NULL,
            sender_id INTEGER NOT NULL,
            content TEXT,
            file_id INTEGER DEFAULT NULL,
            is_read INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (conversation_id) REFERENCES conversations(id),
            FOREIGN KEY (sender_id) REFERENCES users(id),
            FOREIGN KEY (file_id) REFERENCES files(id)
        )
    ');
    echo "Created table: messages\n";
} catch (PDOException $e) {
    echo "Error creating messages: " . $e->getMessage() . "\n";
}

// 4. Create Groups table
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS groups (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            owner_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            description TEXT,
            avatar_url TEXT DEFAULT NULL,
            cover_url TEXT DEFAULT NULL,
            is_private INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (owner_id) REFERENCES users(id)
        )
    ');
    echo "Created table: groups\n";
} catch (PDOException $e) {
    echo "Error creating groups: " . $e->getMessage() . "\n";
}

// 5. Create Group Members table
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS group_members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            role TEXT DEFAULT "member", -- admin, elder, member
            joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(group_id, user_id)
        )
    ');
    echo "Created table: group_members\n";
} catch (PDOException $e) {
    echo "Error creating group_members: " . $e->getMessage() . "\n";
}

// 6. Create Subgroups (Channels) table
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS subgroups (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE
        )
    ');
    echo "Created table: subgroups\n";
} catch (PDOException $e) {
    echo "Error creating subgroups: " . $e->getMessage() . "\n";
}

// 7. Add columns to messages table for groups
$msgColumnsToAdd = [
    "ALTER TABLE messages ADD COLUMN group_id INTEGER DEFAULT NULL REFERENCES groups(id) ON DELETE CASCADE;" => "messages.group_id",
    "ALTER TABLE messages ADD COLUMN subgroup_id INTEGER DEFAULT NULL REFERENCES subgroups(id) ON DELETE CASCADE;" => "messages.subgroup_id"
];

foreach ($msgColumnsToAdd as $sql => $colName) {
    try {
        $db->exec($sql);
        echo "Added column: $colName\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'duplicate column name') === false) {
            echo "Error adding $colName: " . $e->getMessage() . "\n";
        } else {
            echo "Column $colName already exists.\n";
        }
    }
}

// 8. Add group configuration for chat permissions
try {
    $db->exec('ALTER TABLE groups ADD COLUMN chat_permission TEXT DEFAULT "member";');
    echo "Added column: groups.chat_permission\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'duplicate column name') === false) {
        echo "Error adding groups.chat_permission: " . $e->getMessage() . "\n";
    }
}

echo "Group Chat Migrations complete.\n";

// ─── Phase 2 Migrations ───────────────────────────────────────────

// 9. Elder quota columns on groups
$groupCols = [
    "ALTER TABLE groups ADD COLUMN elder_msg_per_hour INTEGER DEFAULT 15;" => "groups.elder_msg_per_hour",
    "ALTER TABLE groups ADD COLUMN elder_max_chars INTEGER DEFAULT 200;" => "groups.elder_max_chars",
    "ALTER TABLE groups ADD COLUMN status TEXT DEFAULT 'open';" => "groups.status",
    "ALTER TABLE groups ADD COLUMN join_mode TEXT DEFAULT 'free';" => "groups.join_mode",
];
foreach ($groupCols as $sql => $colName) {
    try {
        $db->exec($sql);
        echo "Added column: $colName\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'duplicate column name') === false) {
            echo "Error adding $colName: " . $e->getMessage() . "\n";
        } else {
            echo "Column $colName already exists.\n";
        }
    }
}

// 10. Pending messages table (elder approval)
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS pending_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER NOT NULL,
            subgroup_id INTEGER DEFAULT NULL,
            sender_id INTEGER NOT NULL,
            content TEXT,
            file_id INTEGER DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ');
    echo "Created table: pending_messages\n";
} catch (PDOException $e) {
    echo "Error creating pending_messages: " . $e->getMessage() . "\n";
}

// 11. Group join requests table
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS group_join_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(group_id, user_id)
        )
    ');
    echo "Created table: group_join_requests\n";
} catch (PDOException $e) {
    echo "Error creating group_join_requests: " . $e->getMessage() . "\n";
}

// 12. Add invite_token column to groups
try {
    $db->exec('ALTER TABLE groups ADD COLUMN invite_token TEXT DEFAULT NULL;');
    echo "Added column: groups.invite_token\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'duplicate column name') === false) {
        echo "Error adding groups.invite_token: " . $e->getMessage() . "\n";
    } else {
        echo "Column groups.invite_token already exists.\n";
    }
}

echo "Phase 2 Migrations complete.\n";

// ─── Phase 3: Deployment Readiness Migrations ─────────────────────

// 13. Email Verifications table (registration OTP)
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS email_verifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            username TEXT NOT NULL,
            password_hash TEXT NOT NULL,
            otp_hash TEXT NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    echo "Created table: email_verifications\n";
} catch (PDOException $e) {
    echo "Error creating email_verifications: " . $e->getMessage() . "\n";
}

// 14. Password Resets table
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS password_resets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            otp_hash TEXT NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    echo "Created table: password_resets\n";
} catch (PDOException $e) {
    echo "Error creating password_resets: " . $e->getMessage() . "\n";
}

// 15. Blocked Users table
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS blocked_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            blocker_id INTEGER NOT NULL,
            blocked_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (blocker_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (blocked_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(blocker_id, blocked_id)
        )
    ');
    echo "Created table: blocked_users\n";
} catch (PDOException $e) {
    echo "Error creating blocked_users: " . $e->getMessage() . "\n";
}

// 16. User Preferences table
try {
    $db->exec('
        CREATE TABLE IF NOT EXISTS user_preferences (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL UNIQUE,
            theme TEXT DEFAULT "dark",
            chat_theme TEXT DEFAULT "default",
            chat_bg_color TEXT DEFAULT NULL,
            chat_bubble_color TEXT DEFAULT NULL,
            analytics_opt_out INTEGER DEFAULT 0,
            data_retention_days INTEGER DEFAULT 365,
            show_read_receipts INTEGER DEFAULT 1,
            show_online_status INTEGER DEFAULT 1,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ');
    echo "Created table: user_preferences\n";
} catch (PDOException $e) {
    echo "Error creating user_preferences: " . $e->getMessage() . "\n";
}

// 17. Add profile columns to users table
$profileCols = [
    "ALTER TABLE users ADD COLUMN first_name TEXT DEFAULT NULL;" => "users.first_name",
    "ALTER TABLE users ADD COLUMN last_name TEXT DEFAULT NULL;" => "users.last_name",
    "ALTER TABLE users ADD COLUMN about_me TEXT DEFAULT NULL;" => "users.about_me",
    "ALTER TABLE users ADD COLUMN phone_number TEXT DEFAULT NULL;" => "users.phone_number",
    "ALTER TABLE users ADD COLUMN cover_url TEXT DEFAULT NULL;" => "users.cover_url",
];
foreach ($profileCols as $sql => $colName) {
    try {
        $db->exec($sql);
        echo "Added column: $colName\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'duplicate column name') === false) {
            echo "Error adding $colName: " . $e->getMessage() . "\n";
        } else {
            echo "Column $colName already exists.\n";
        }
    }
}

echo "Phase 3 (Deployment) Migrations complete.\n";
