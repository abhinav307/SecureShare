<div class="max-w-xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-white">Upload Secure File</h2>
            <p class="text-sm text-slate-400 mt-1">Files are encrypted instantly before storage.</p>
        </div>
    </div>
    <div class="flex items-center gap-4 mb-8">
        <a href="/chat" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors border border-transparent hover:border-slate-700 font-medium text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back
        </a>
    </div>

    <div class="glass-panel rounded-2xl p-8 shadow-xl">
        <?php if (isset($error)): ?>
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-medium"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="/upload" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="form-group relative">
                <div class="border-2 border-dashed border-slate-700/50 hover:border-brand-500/50 bg-slate-800/30 hover:bg-brand-500/5 rounded-2xl p-12 text-center transition-all group cursor-pointer">
                    <input type="file" id="document" name="document" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div class="relative z-0 pointer-events-none flex flex-col items-center">
                        <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mb-4 text-slate-400 group-hover:text-brand-400 group-hover:scale-110 transition-all shadow-lg group-hover:shadow-brand-500/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <p class="text-lg font-medium text-slate-200 mb-1 group-hover:text-brand-300 transition-colors file-name-display">Drop file here or click to browse</p>
                        <p class="text-sm text-slate-500">Maximum file size: 512MB</p>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-brand-500/25 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Encrypt & Upload
            </button>
        </form>
    </div>
</div>
