<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'Verify Email') ?></title>
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
        .otp-input { letter-spacing: 0.5em; font-size: 1.5rem; text-align: center; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4"
      style="background-image: radial-gradient(circle at 15% 50%, rgba(99,102,241,0.12), transparent 40%), radial-gradient(circle at 85% 30%, rgba(225,29,72,0.07), transparent 35%);">

<div class="w-full max-w-md">

    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-tr from-brand-600 to-emerald-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
        <h1 class="text-2xl font-bold text-white">Verify Your Email</h1>
        <p class="text-slate-400 text-sm mt-1">Enter the 6-digit code sent to <span class="text-brand-400 font-medium"><?= htmlspecialchars($email ?? '') ?></span></p>
    </div>

    <div class="glass rounded-3xl p-8 shadow-2xl">

        <?php if (isset($success) && $success): ?>
            <div class="bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 rounded-xl mb-6 text-sm">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error) && $error): ?>
            <div class="bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 p-4 rounded-xl mb-6 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/register/verify" class="space-y-5">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

            <!-- OTP Input -->
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">6-Digit Verification Code</label>
                <input type="text" name="otp" id="otp" required maxlength="6" pattern="\d{6}"
                       placeholder="· · · · · ·"
                       class="otp-input w-full bg-dark-800 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                <p class="text-xs text-slate-500 mt-2">Check your Gmail inbox and spam folder. Code expires in 15 minutes.</p>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-brand-600 to-indigo-500 hover:from-brand-500 hover:to-indigo-400 text-white font-semibold rounded-2xl transition-all shadow-[0_4px_20px_rgba(99,102,241,0.4)] hover:shadow-[0_6px_28px_rgba(99,102,241,0.6)] transform hover:-translate-y-0.5">
                Verify & Create Account
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Didn't receive the code?
            <a href="/register" class="text-brand-400 hover:text-brand-300 font-semibold ml-1">Register Again</a>
        </p>
    </div>
</div>

</body>
</html>
