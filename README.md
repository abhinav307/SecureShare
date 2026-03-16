# 🔒 SecureShare — Encrypted File Sharing & Chat Platform

> **[🌐 Live Demo →](https://secureshare.iceiy.com)**

A full-stack **PHP web application** for secure file sharing and real-time encrypted messaging with AES-256 encryption, Google OAuth integration, and AI-powered chatbot.

---

## ✨ Features

### 🔐 Security
- **AES-256-CBC Encryption** — All files and messages encrypted at rest with HMAC verification
- **Zero-Knowledge Architecture** — Files encrypted before storage, decrypted only on authorized requests
- **Secure Session Management** — PHP sessions with proper authentication guards
- **Password Hashing** — bcrypt with `PASSWORD_DEFAULT` algorithm

### 💬 Chat System
- **1-on-1 Direct Messages** — Real-time encrypted private conversations
- **Group Chat** — Create groups with roles (Owner, Admin, Elder, Member)
- **Subgroups** — Organize topics within groups
- **AI Chatbot** — Integrated Groq-powered assistant (Llama 3.3 70B)
- **File Sharing in Chat** — Send encrypted files directly in conversations
- **Message Forwarding** — Forward messages to multiple recipients
- **Block/Unblock Users** — Privacy controls
- **Read Receipts** — Track message delivery

### 📁 File Management
- **Encrypted Upload/Download** — AES-256 encryption for all stored files
- **Expiring Share Links** — Set expiration dates on shared file links
- **Download Tracking** — Monitor who accesses shared files
- **Storage Quotas** — Per-user storage limits managed by admin
- **Global File Manager** — Grid/list view with bulk actions

### 👤 User Profiles
- **Profile Pictures & Cover Images** — Upload and manage personal images
- **Status Messages** — Custom status and about me sections
- **User Search** — Find and connect with other users

### 🔑 Authentication
- **Email/Password Registration** — With OTP email verification via Gmail SMTP
- **Google OAuth 2.0** — One-click sign-in with Google
- **Password Reset** — Secure OTP-based password recovery
- **Two-Step Admin Auth** — Secret key + Google OAuth for server administration

### ⚙️ Administration
- **Server Admin Panel** — Full dashboard with encryption status, user management, storage monitoring
- **User Role Management** — Promote users, manage storage limits
- **Group Admin Controls** — Chat permissions, elder quotas, join modes, invite links

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.x |
| **Database** | SQLite (PDO) |
| **Encryption** | OpenSSL (AES-256-CBC + HMAC-SHA256) |
| **Auth** | Google OAuth 2.0, Session-based |
| **AI** | Groq API (Llama 3.3 70B) |
| **Email** | Gmail SMTP with STARTTLS + PHP mail() fallback |
| **Frontend** | Vanilla JavaScript, CSS3 |
| **Hosting** | AeonFree (PHP shared hosting) |

---

## 📁 Project Structure

```
SecureShare/
├── public/                 # Web-accessible files
│   ├── index.php          # Application entry point
│   ├── css/style.css      # Stylesheet
│   ├── js/app.js          # Client-side JavaScript
│   └── uploads/           # User uploads (avatars, group images)
├── src/
│   ├── Controllers/       # 12 controllers handling all routes
│   ├── Core/              # Database, Router, View engine
│   ├── Models/            # User, Message, Conversation, File, Share
│   ├── Services/          # Encryption, Email, AI Chat services
│   └── Views/             # PHP templates (auth, chat, admin, etc.)
├── storage/               # Encrypted files & database (gitignored)
├── .env.example           # Environment variables template
├── .htaccess              # URL routing
├── index.php              # Root bootstrap
└── migrate.php            # Database migration script
```

---

## 🚀 Quick Start

### 1. Clone & Configure
```bash
git clone https://github.com/abhinav307/SecureShare.git
cd SecureShare
cp .env.example .env
```

### 2. Edit `.env`
```env
APP_URL=http://localhost:8080
APP_KEY=<generate with: php -r "echo base64_encode(random_bytes(32));">
ENCRYPTION_KEY=<generate with: php -r "echo base64_encode(random_bytes(32));">
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
SMTP_USERNAME=your_gmail@gmail.com
SMTP_PASSWORD=your_gmail_app_password
```

### 3. Run Migration
```bash
php migrate.php
```

### 4. Start Server
```bash
php -S localhost:8080 -t public
```

Visit **http://localhost:8080** 🎉

---

## 📸 Screenshots

| Home Page | Chat Interface | Server Admin |
|:---------:|:--------------:|:------------:|
| Military-grade encryption landing | Real-time encrypted messaging | Full admin dashboard |

---

## 🔒 Security Considerations

- `.env` file is **never committed** — contains all secrets
- Database and uploaded files are **gitignored**
- File encryption uses **AES-256-CBC with HMAC** verification (timing-attack resistant)
- All passwords are **bcrypt hashed**
- CSRF protection via session tokens
- XSS prevention through output escaping
- SQL injection prevention via **PDO prepared statements**

---

## 📝 License

This project is built for educational purposes.

---

<p align="center">
  Built with ❤️ by <a href="https://github.com/abhinav307">Abhinav Tripathi</a>
</p>
