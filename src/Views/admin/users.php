<!-- Header Layout -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-400 to-orange-400">
            User Management
        </h1>
        <p class="text-slate-400 mt-1 text-sm">View and manage platform members.</p>
    </div>
    
    <a href="/admin" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-xl transition-all border border-slate-700 flex items-center gap-2 text-sm shadow-lg shadow-black/20 w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Admin Dashboard
    </a>
</div>

<!-- Users Table -->
<div class="glass-panel rounded-2xl overflow-hidden shadow-xl border border-slate-700/50">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-900/60 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/50">
                    <th class="px-6 py-4 font-medium">Username</th>
                    <th class="px-6 py-4 font-medium">Email</th>
                    <th class="px-6 py-4 font-medium">Role</th>
                    <th class="px-6 py-4 font-medium text-center">Files Uploaded</th>
                    <th class="px-6 py-4 font-medium">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-sm border border-indigo-500/30">
                                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                </div>
                                <span class="text-slate-200 font-medium"><?= htmlspecialchars($u['username']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                            <?= htmlspecialchars($u['email']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-md bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                    Admin
                                </span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-md bg-slate-800 text-slate-300 border border-slate-700">
                                    User
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300 text-center font-medium">
                            <?= $u['file_count'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                            <?= date('M j, Y', strtotime($u['created_at'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
