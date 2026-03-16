<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">
            Welcome back, <?= htmlspecialchars($user['username']) ?>
        </h1>
        <p class="text-slate-400 mt-1 text-sm">Manage your encrypted files and active shares.</p>
    </div>
    
    <a href="/upload" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white px-5 py-2.5 rounded-xl font-medium transition-all shadow-lg shadow-brand-500/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        Upload File
    </a>
</div>

<!-- Files Table -->
<div class="glass-panel rounded-2xl overflow-hidden animate-[fadeIn_0.5s_ease-out_0.2s] shadow-xl">
    <div class="p-6 border-b border-white/5 flex justify-between items-center bg-slate-800/20">
        <h3 class="text-lg font-semibold text-white">Your Files</h3>
        <span class="text-xs font-medium px-2.5 py-1 bg-slate-800 text-slate-300 rounded-lg border border-slate-700">Storage: <?= count($files) ?> files</span>
    </div>
    
    <?php if (empty($files)): ?>
        <div class="flex flex-col items-center justify-center p-16 text-center">
            <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mb-4 text-slate-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <h4 class="text-lg font-medium text-white mb-2">No files yet</h4>
            <p class="text-slate-400 max-w-sm mb-6 text-sm">Upload your first file to securely store and share it with others.</p>
            <a href="/upload" class="text-brand-400 hover:text-brand-300 font-medium text-sm transition-colors decoration-brand-400/30 underline underline-offset-4">Start uploading &rarr;</a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/40 text-slate-400 text-xs uppercase tracking-wider border-b border-white/5">
                        <th class="px-6 py-4 font-medium">Filename</th>
                        <th class="px-6 py-4 font-medium">Size</th>
                        <th class="px-6 py-4 font-medium">AI Tags</th>
                        <th class="px-6 py-4 font-medium">Date Modified</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 shrink-0">
                    <?php foreach ($files as $file): ?>
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span class="text-slate-200 font-medium truncate max-w-[200px] block"><?= htmlspecialchars($file['original_name']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                                <?= round($file['size'] / 1024, 2) ?> KB
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-[250px]">
                                    <?php if ($file['ai_tags']): ?>
                                        <?php foreach(explode(',', $file['ai_tags']) as $tag): ?>
                                            <span class="inline-flex text-[11px] font-medium px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-300 border border-brand-500/20 whitespace-nowrap">
                                                <?= htmlspecialchars(trim($tag)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-500 hidden group-hover:block transition-all">No tags</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                                <?= date('M j, Y', strtotime($file['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="/download/<?= $file['id'] ?>" title="Download" class="p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors border border-transparent hover:border-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                    <a href="/share/<?= $file['id'] ?>" title="Share" class="p-2 text-brand-400 hover:text-white hover:bg-brand-600 rounded-lg transition-colors border border-transparent hover:border-brand-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
