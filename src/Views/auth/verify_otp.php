<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'Verify OTP') ?></title>
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
        <div class="w-16 h-16 bg-gradient-to-tr from-emerald-500 to-brand-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </div>
        <h1 class="text-2xl font-bold text-white">Enter OTP & Reset</h1>
        <p class="text-slate-400 text-sm mt-1">Enter the code sent to <span class="text-brand-400 font-medium"><?= htmlspecialchars($email ?? '') ?></span></p>
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

        <form method="POST" action="/forgot-password/reset" class="space-y-5">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

            <!-- OTP Input -->
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">6-Digit OTP Code</label>
                <input type="text" name="otp" id="otp" required maxlength="6" pattern="\d{6}"
                       placeholder="· · · · · ·"
                       class="otp-input w-full bg-dark-800 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                <p class="text-xs text-slate-500 mt-2">OTP is valid for 15 minutes. Check your Gmail inbox and spam folder.</p>
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">New Password</label>
                <div class="relative">
                    <input type="password" name="new_password" id="new_password" required minlength="8" placeholder="Min. 8 characters"
                           class="w-full bg-dark-800 border border-slate-700/80 rounded-2xl px-5 py-3.5 pl-12 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                    <svg class="w-5 h-5 text-slate-500 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Confirm New Password</label>
                <div class="relative">
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="8" placeholder="Repeat password"
                           class="w-full bg-dark-800 border border-slate-700/80 rounded-2xl px-5 py-3.5 pl-12 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                    <svg class="w-5 h-5 text-slate-500 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-semibold rounded-2xl transition-all shadow-[0_4px_20px_rgba(16,185,129,0.4)] hover:shadow-[0_6px_28px_rgba(16,185,129,0.6)] transform hover:-translate-y-0.5">
                Reset Password
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Didn't get the OTP?
            <a href="/forgot-password" class="text-brand-400 hover:text-brand-300 font-semibold ml-1">Request Again</a>
        </p>
    </div>
</div>

</body>
</html>
