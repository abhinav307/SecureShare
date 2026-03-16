<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Administration — SecureShare</title>
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
<body class="min-h-screen flex items-center justify-center p-4"
      style="background-image: radial-gradient(circle at 15% 50%, rgba(220,38,38,0.08), transparent 40%), radial-gradient(circle at 85% 30%, rgba(99,102,241,0.07), transparent 35%);">

<div class="w-full max-w-md">

    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-tr from-rose-600 to-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl shadow-rose-500/30">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </div>
        <h1 class="text-2xl font-bold text-white">Server Administration</h1>
        <p class="text-slate-400 text-sm mt-1">Authorized access only. All attempts are logged.</p>
    </div>

    <div class="glass rounded-3xl p-8 shadow-2xl">

        <?php if (isset($error) && $error): ?>
            <div class="bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 p-4 rounded-xl mb-6 text-sm flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Step indicator -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-rose-500 flex items-center justify-center text-white text-xs font-bold">1</div>
                <span class="text-xs text-rose-400 font-semibold">Secret Key</span>
            </div>
            <div class="flex-1 h-px bg-slate-700"></div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-700 flex items-center justify-center text-slate-400 text-xs font-bold">2</div>
                <span class="text-xs text-slate-500 font-semibold">Google Verify</span>
            </div>
        </div>

        <form method="POST" action="/server-admin/verify" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">
                    <svg class="w-4 h-4 inline mr-1 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    Server Secret Key
                </label>
                <input type="password" name="server_secret" required autocomplete="off"
                       placeholder="Enter your server administration key"
                       class="w-full bg-dark-800 border border-slate-700/80 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition-all">
                <p class="text-xs text-slate-500 mt-2">After verification, you'll be redirected to Google for identity confirmation.</p>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-rose-600 to-amber-500 hover:from-rose-500 hover:to-amber-400 text-white font-semibold rounded-xl transition-all shadow-lg shadow-rose-500/25">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Verify & Continue
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-700/50 text-center">
            <a href="/login" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">← Back to User Login</a>
        </div>
    </div>

    <p class="text-center text-[11px] text-slate-600 mt-6">
        🔒 This area requires 2-step verification: Server Key + Google Identity
    </p>
</div>

</body>
</html>
