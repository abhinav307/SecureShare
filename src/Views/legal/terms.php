<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service — SecureShare</title>
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
<body class="min-h-screen" style="background-image: radial-gradient(circle at 85% 30%, rgba(99,102,241,0.08), transparent 40%);">

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
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Terms of Service</h1>
        </div>
        <p class="text-slate-400 text-sm">Last Updated: <?= date('F d, Y') ?></p>
    </div>

    <div class="glass rounded-3xl p-8 md:p-10 space-y-8">

        <!-- 1 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">1</span>
                Acceptance of Terms
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                By accessing or using SecureShare, you agree to be bound by these Terms of Service. If you do not agree to these terms, you may not access or use the platform. These terms apply to all users, including visitors, registered users, and administrators.
            </p>
        </section>

        <!-- 2 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">2</span>
                Account Registration
            </h2>
            <ul class="space-y-2 text-sm text-slate-300">
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> You must provide a valid email address during registration and verify it via OTP</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> You are responsible for maintaining the security of your account credentials</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> You may register using email/password or Google Sign-In</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> You must not create accounts for the purpose of impersonation or misrepresentation</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> You must be at least 13 years of age to use SecureShare</li>
            </ul>
        </section>

        <!-- 3 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">3</span>
                Acceptable Use
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed mb-3">You agree <strong class="text-white">NOT</strong> to use SecureShare to:</p>
            <div class="bg-rose-500/5 border border-rose-500/20 rounded-xl p-5 space-y-2">
                <p class="flex items-start gap-2 text-sm text-slate-300"><span class="text-rose-400">✗</span> Transmit illegal, harmful, threatening, or abusive content</p>
                <p class="flex items-start gap-2 text-sm text-slate-300"><span class="text-rose-400">✗</span> Share malware, viruses, or malicious files</p>
                <p class="flex items-start gap-2 text-sm text-slate-300"><span class="text-rose-400">✗</span> Harass, bully, or intimidate other users</p>
                <p class="flex items-start gap-2 text-sm text-slate-300"><span class="text-rose-400">✗</span> Attempt to bypass encryption or security measures</p>
                <p class="flex items-start gap-2 text-sm text-slate-300"><span class="text-rose-400">✗</span> Use automated tools to scrape data or spam other users</p>
                <p class="flex items-start gap-2 text-sm text-slate-300"><span class="text-rose-400">✗</span> Violate any applicable local, state, national, or international laws</p>
            </div>
        </section>

        <!-- 4 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">4</span>
                Storage & File Sharing
            </h2>
            <div class="space-y-3 text-sm text-slate-300 leading-relaxed">
                <div class="bg-dark-800/50 rounded-xl p-4 border border-slate-700/30">
                    <p class="font-semibold text-white mb-1">Storage Limits</p>
                    <p>Each user is allocated a default storage quota (currently 32 MB). Administrators may adjust individual storage limits. Files exceeding your storage limit cannot be uploaded.</p>
                </div>
                <div class="bg-dark-800/50 rounded-xl p-4 border border-slate-700/30">
                    <p class="font-semibold text-white mb-1">File Encryption</p>
                    <p>All files uploaded to SecureShare are encrypted using AES-256-CBC before storage. You are responsible for the content of files you upload and share.</p>
                </div>
                <div class="bg-dark-800/50 rounded-xl p-4 border border-slate-700/30">
                    <p class="font-semibold text-white mb-1">Shared Links</p>
                    <p>When you share files via links, anyone with the link may be able to access the file. Use share links responsibly and revoke them when no longer needed.</p>
                </div>
            </div>
        </section>

        <!-- 5 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">5</span>
                Groups & Communication
            </h2>
            <ul class="space-y-2 text-sm text-slate-300">
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> Group owners and admins are responsible for moderating group content and behavior</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> Elders and admins may have messaging quotas to prevent spam</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> Group owners may delete groups at any time, which removes all group data permanently</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> Users may be blocked or reported for violating community guidelines</li>
            </ul>
        </section>

        <!-- 6 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">6</span>
                AI Chatbot (SecureShare AI)
            </h2>
            <div class="bg-amber-500/5 border border-amber-500/20 rounded-xl p-5">
                <p class="text-slate-300 text-sm leading-relaxed">
                    SecureShare includes an AI-powered chatbot for general assistance. Please note:
                </p>
                <ul class="mt-3 space-y-2 text-sm text-slate-300">
                    <li class="flex items-start gap-2"><span class="text-amber-400">⚠</span> AI responses are generated by third-party models and may not always be accurate</li>
                    <li class="flex items-start gap-2"><span class="text-amber-400">⚠</span> Do not share passwords, personal data, or sensitive information with the AI chatbot</li>
                    <li class="flex items-start gap-2"><span class="text-amber-400">⚠</span> Messages sent to the AI assistant are processed by Groq (third-party) and are not end-to-end encrypted</li>
                    <li class="flex items-start gap-2"><span class="text-amber-400">⚠</span> SecureShare is not responsible for any advice or information provided by the AI</li>
                </ul>
            </div>
        </section>

        <!-- 7 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">7</span>
                Account Termination
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                We reserve the right to suspend or terminate your account at any time, with or without notice, for violations of these Terms. Upon termination, all associated data — messages, files, conversations, group memberships, and preferences — will be permanently deleted. You may also request account deletion by contacting an administrator.
            </p>
        </section>

        <!-- 8 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">8</span>
                Disclaimers & Limitations
            </h2>
            <ul class="space-y-2 text-sm text-slate-300">
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> SecureShare is provided "as is" without warranties of any kind</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> We do not guarantee 100% uptime or uninterrupted service</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> We are not liable for data loss caused by user error, system failures, or security breaches</li>
                <li class="flex items-start gap-2"><span class="text-brand-400">•</span> Users are responsible for backing up their own important files</li>
            </ul>
        </section>

        <!-- 9 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">9</span>
                Changes to Terms
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                We reserve the right to modify these Terms of Service at any time. Continued use of SecureShare after changes constitutes acceptance of the updated terms. We encourage users to review these terms periodically.
            </p>
        </section>

        <!-- 10 -->
        <section>
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand-600/20 text-brand-400 rounded-lg flex items-center justify-center text-xs font-black">10</span>
                Contact
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                For questions regarding these Terms of Service, please contact the platform administrator through the SecureShare AI chatbot or via email.
            </p>
        </section>

    </div>

    <div class="text-center mt-8 text-xs text-slate-600">
        © <?= date('Y') ?> SecureShare. All rights reserved.
    </div>
</div>

</body>
</html>
