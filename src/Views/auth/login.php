<div class="max-w-md mx-auto w-full pt-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-white tracking-tight mb-2">Welcome Back</h2>
        <p class="text-slate-400">Sign in to access your encrypted files.</p>
    </div>

    <div class="glass-panel p-8 rounded-2xl shadow-xl">
        <?php if (isset($error)): ?>
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="text-sm font-medium"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-medium"><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                <input type="email" id="email" name="email" required class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all">
            </div>
            
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                    <a href="/forgot-password" class="text-xs text-brand-400 hover:text-brand-300 font-medium transition-colors">Forgot Password?</a>
                </div>
                <input type="password" id="password" name="password" required class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all">
            </div>
            
            <button type="submit" class="w-full py-3.5 px-4 bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-brand-500/25 mt-4">
                Sign In
            </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-3 mt-6 mb-4">
            <div class="flex-1 h-px bg-slate-700/50"></div>
            <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">or</span>
            <div class="flex-1 h-px bg-slate-700/50"></div>
        </div>

        <!-- Continue with Google -->
        <a href="/auth/google" class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-white hover:bg-gray-100 text-gray-700 font-semibold rounded-xl transition-all shadow-md border border-gray-200">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continue with Google
        </a>
        
        <div class="mt-6 pt-6 border-t border-slate-700/50 text-center">
            <p class="text-slate-400 text-sm">
                Don't have an account? 
                <a href="/register" class="font-medium text-brand-400 hover:text-brand-300 transition-colors ml-1">Create one now</a>
            </p>
        </div>
    </div>
</div>

<!-- Footer links (privacy, terms, server admin) -->
<div class="max-w-md mx-auto w-full mt-6 text-center">
    <p class="text-[11px] text-slate-600">
        <a href="/privacy" class="hover:text-slate-400 transition-colors">Privacy Policy</a>
        <span class="mx-1.5">·</span>
        <a href="/terms" class="hover:text-slate-400 transition-colors">Terms of Service</a>
        <span class="mx-1.5">·</span>
        <a href="/server-admin" class="hover:text-slate-400 transition-colors">Server Administration</a>
    </p>
</div>
