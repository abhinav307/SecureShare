<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'Forgot Password') ?></title>
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
      style="background-image: radial-gradient(circle at 15% 50%, rgba(99,102,241,0.12), transparent 40%), radial-gradient(circle at 85% 30%, rgba(225,29,72,0.07), transparent 35%);">

<div class="w-full max-w-md">

    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-tr from-brand-600 to-rose-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        </div>
        <h1 class="text-2xl font-bold text-white">Forgot Password?</h1>
        <p class="text-slate-400 text-sm mt-1">Enter your email to receive a 6-digit OTP</p>
    </div>

    <div class="glass rounded-3xl p-8 shadow-2xl">

        <?php if (isset($error) && $error): ?>
            <div class="bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 p-4 rounded-xl mb-6 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/forgot-password/send" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Email Address</label>
                <div class="relative">
                    <input type="email" name="email" id="email" required placeholder="you@example.com"
                           class="w-full bg-dark-800 border border-slate-700/80 rounded-2xl px-5 py-3.5 pl-12 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                    <svg class="w-5 h-5 text-slate-500 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-brand-600 to-indigo-500 hover:from-brand-500 hover:to-indigo-400 text-white font-semibold rounded-2xl transition-all shadow-[0_4px_20px_rgba(99,102,241,0.4)] hover:shadow-[0_6px_28px_rgba(99,102,241,0.6)] transform hover:-translate-y-0.5">
                Send OTP
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Remember your password?
            <a href="/login" class="text-brand-400 hover:text-brand-300 font-semibold ml-1">Log In</a>
        </p>
    </div>
</div>

</body>
</html>
