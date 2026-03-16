<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy — SecureShare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5' }, dark: { 900: '#0f172a', 800: '#1e293b' } } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Outfit', sans-serif; background-color: #0b0f19; color: #f8fafc; }
        .glass { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.06); }
    </style>
</head>
<body class="min-h-screen" style="background-image: radial-gradient(circle at 15% 50%, rgba(99,102,241,0.08), transparent 40%);">

<div class="max-w-3xl mx-auto px-6 py-12">

    <!-- Back -->
    <a href="/login" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-white transition-colors mb-8">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Login
    </a>

    <!-- Header -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-tr from-brand-600 to-indigo-400 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Privacy Policy</h1>
        </div>
        <p class="text-slate-400 text-sm">Last Updated: <?= date('F d, Y') ?></p>
    </div>

    <div class="glass rounded-3xl p-8 md:p-10 space-y-8">

        <!-- 1 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">1</span>
                Introduction
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                SecureShare ("we", "our", "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information when you use our encrypted communication and file-sharing platform. By using SecureShare, you agree to the practices described in this policy.
            </p>
        </section>

        <!-- 2 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">2</span>
                Information We Collect
            </h2>
            <div class="space-y-3 text-sm text-slate-300 leading-relaxed">
                <div class="bg-dark-800/50 rounded-xl p-4 border border-slate-700/30">
                    <p class="font-semibold text-white mb-1">Account Information</p>
                    <p>When you register, we collect your username, email address, and a hashed version of your password. If you sign in with Google, we receive your name, email, and profile picture from Google.</p>
                </div>
                <div class="bg-dark-800/50 rounded-xl p-4 border border-slate-700/30">
                    <p class="font-semibold text-white mb-1">Profile Information</p>
                    <p>Optional details you provide such as your first name, last name, phone number, status message, about me description, profile picture, and cover photo.</p>
                </div>
                <div class="bg-dark-800/50 rounded-xl p-4 border border-slate-700/30">
                    <p class="font-semibold text-white mb-1">Usage Metadata</p>
                    <p>We collect non-content metadata such as message timestamps, file sizes, storage usage, and login activity. We do <strong class="text-emerald-400">NOT</strong> have access to the content of your encrypted messages or files.</p>
                </div>
            </div>
        </section>

        <!-- 3 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-emerald-500/20 text-emerald-400 rounded-lg flex items-center justify-center text-xs font-black">3</span>
                End-to-End Encryption
            </h2>
            <div class="bg-emerald-500/5 border border-emerald-500/20 rounded-xl p-5">
                <p class="text-slate-300 text-sm leading-relaxed">
                    All messages and files shared on SecureShare are encrypted using <strong class="text-emerald-400">AES-256-CBC</strong> encryption before being stored. This means:
                </p>
                <ul class="mt-3 space-y-2 text-sm text-slate-300">
                    <li class="flex items-start gap-2"><span class="text-emerald-400 mt-0.5">✓</span> Your messages are encrypted at rest and cannot be read by server administrators</li>
                    <li class="flex items-start gap-2"><span class="text-emerald-400 mt-0.5">✓</span> File content is encrypted before being stored on our servers</li>
                    <li class="flex items-start gap-2"><span class="text-emerald-400 mt-0.5">✓</span> Even in the event of a data breach, encrypted content remains unreadable</li>
                    <li class="flex items-start gap-2"><span class="text-emerald-400 mt-0.5">✓</span> Server administrators can only view metadata (timestamps, file sizes, storage usage)</li>
                </ul>
            </div>
        </section>

        <!-- 4 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">4</span>
                How We Use Your Information
            </h2>
            <ul class="space-y-2 text-sm text-slate-300">
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> To create and maintain your account</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> To provide encrypted messaging and file-sharing services</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> To send verification emails (OTP) during registration and password resets</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> To enforce storage limits and platform policies</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> To improve platform security and prevent abuse</li>
            </ul>
        </section>

        <!-- 5 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">5</span>
                Data Storage & Retention
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                Your data is stored on secure servers. Account data is retained as long as your account is active. When your account is deleted (by you or an administrator), all associated data — including messages, files, conversations, group memberships, and preferences — is permanently and irreversibly removed from our systems.
            </p>
        </section>

        <!-- 6 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">6</span>
                Third-Party Services
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed mb-3">We integrate with the following third-party services:</p>
            <div class="space-y-2 text-sm text-slate-300">
                <div class="bg-dark-800/50 rounded-xl p-3 border border-slate-700/30 flex items-center gap-3">
                    <span class="text-lg">🔑</span>
                    <div><strong class="text-white">Google OAuth</strong> — Used for "Continue with Google" sign-in. We receive only your email, name, and profile picture.</div>
                </div>
                <div class="bg-dark-800/50 rounded-xl p-3 border border-slate-700/30 flex items-center gap-3">
                    <span class="text-lg">📧</span>
                    <div><strong class="text-white">Gmail SMTP</strong> — Used to send OTP verification emails during registration and password resets.</div>
                </div>
                <div class="bg-dark-800/50 rounded-xl p-3 border border-slate-700/30 flex items-center gap-3">
                    <span class="text-lg">🤖</span>
                    <div><strong class="text-white">Groq AI</strong> — Powers the SecureShare AI chatbot. Your messages to the bot are sent to Groq for processing. Do not share sensitive information with the AI assistant.</div>
                </div>
            </div>
        </section>

        <!-- 7 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">7</span>
                Your Rights
            </h2>
            <ul class="space-y-2 text-sm text-slate-300">
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> <strong class="text-white">Access:</strong> View your account information in your profile settings</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> <strong class="text-white">Modify:</strong> Update your profile details at any time</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> <strong class="text-white">Delete:</strong> Request account deletion by contacting the administrator</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> <strong class="text-white">Block:</strong> Block other users to prevent them from contacting you</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> <strong class="text-white">Report:</strong> Report users for inappropriate behavior</li>
            </ul>
        </section>

        <!-- 8 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">8</span>
                Contact Us
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                If you have questions about this Privacy Policy, please contact the platform administrator through the SecureShare AI chatbot or via email.
            </p>
        </section>

    </div>

    <div class="text-center mt-8 text-xs text-slate-600">
        © <?= date('Y') ?> SecureShare. All rights reserved.
    </div>
</div>

</body>
</html>
