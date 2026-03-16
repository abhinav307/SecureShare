<?php if (isset($error)): ?>
<div class="max-w-md mx-auto mt-16 text-center">
    <div class="w-20 h-20 bg-rose-500/10 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
    </div>
    <h2 class="text-2xl font-bold text-white mb-2">Access Denied</h2>
    <p class="text-slate-400 mb-8"><?= htmlspecialchars($error) ?></p>
    <a href="/" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-medium transition-colors border border-slate-700">
        Return Home
    </a>
</div>
<?php else: ?>
<div class="max-w-2xl mx-auto pt-8">
    <div class="glass-panel p-8 md:p-12 rounded-3xl shadow-2xl text-center relative overflow-hidden">
        <!-- Decorative background glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-brand-500/20 blur-[80px] rounded-full pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col items-center">
            <div class="w-20 h-20 bg-slate-800/80 rounded-2xl flex items-center justify-center mb-8 border border-slate-700/50 shadow-inner group transition-transform hover:scale-110">
                <svg class="w-10 h-10 text-brand-400 group-hover:text-brand-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            
            <h2 class="text-3xl font-bold text-white mb-2 break-all"><?= htmlspecialchars($file['original_name']) ?></h2>
            
            <div class="flex items-center gap-4 text-sm text-slate-400 mb-10">
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg><?= round($file['size']/1024, 2) ?> KB</span>
                <span class="w-1 h-1 bg-slate-600 rounded-full"></span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>AES-256</span>
                <?php if ($file['expires_at']): ?>
                <span class="w-1 h-1 bg-slate-600 rounded-full"></span>
                <span class="flex items-center gap-1.5 text-amber-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Expires <?= date('M j, Y', strtotime($file['expires_at'])) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="w-full max-w-sm mb-8">
                <a href="/download/<?= $file['id'] ?>?token=<?= htmlspecialchars($_GET['token'] ?? '') ?>" class="w-full py-4 px-6 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-semibold rounded-xl transition-all shadow-[0_0_30px_rgba(79,70,229,0.3)] flex items-center justify-center gap-3 text-lg group">
                    <svg class="w-6 h-6 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Encrypted File
                </a>
                <p class="text-xs text-slate-500 mt-4 flex items-center justify-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> File will be implicitly decrypted on your device.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
