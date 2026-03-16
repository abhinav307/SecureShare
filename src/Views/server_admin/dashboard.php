<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Administration Dashboard — SecureShare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5' }, dark: { 900: '#0f172a', 800: '#1e293b', 700: '#334155' } } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Outfit', sans-serif; background-color: #0b0f19; color: #f8fafc; }
        .glass { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05); }
        .stat-card { transition: transform 0.2s; } .stat-card:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="min-h-screen" style="background-image: radial-gradient(circle at 15% 50%, rgba(220,38,38,0.06), transparent 30%), radial-gradient(circle at 85% 20%, rgba(99,102,241,0.06), transparent 30%);">

<!-- Top Nav -->
<header class="sticky top-0 z-50 bg-dark-900/90 backdrop-blur-xl border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-tr from-rose-600 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-rose-500/20">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-white">SecureShare <span class="text-rose-400">Server Admin</span></h1>
                <p class="text-[10px] text-slate-500 -mt-0.5">🔒 End-to-End Encryption — Content Not Accessible</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <?php if (!empty($_SESSION['server_admin_picture'])): ?>
                    <img src="<?= htmlspecialchars($_SESSION['server_admin_picture']) ?>" class="w-8 h-8 rounded-full border-2 border-rose-500/30" alt="Admin">
                <?php endif; ?>
                <div class="text-right">
                    <p class="text-sm font-semibold text-white"><?= htmlspecialchars($_SESSION['server_admin_name'] ?? 'Admin') ?></p>
                    <p class="text-[10px] text-slate-500"><?= htmlspecialchars($_SESSION['server_admin_email'] ?? '') ?></p>
                </div>
            </div>
            <a href="/server-admin/logout" class="text-sm bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-medium px-4 py-2 rounded-xl transition-colors border border-rose-500/20">Sign Out</a>
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto px-6 py-8">

    <!-- Alerts -->
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 rounded-xl mb-6 text-sm">
            ✓ <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 p-4 rounded-xl mb-6 text-sm">
            ✗ <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <!-- E2E Encryption Notice -->
    <div class="glass rounded-2xl p-4 mb-8 flex items-center gap-3 border-rose-500/10">
        <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-white">End-to-End Encryption Active</p>
            <p class="text-xs text-slate-400">Message content and file data are encrypted. Only metadata and statistics are visible to server administration. This complies with industry-standard privacy practices.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="stat-card glass rounded-2xl p-5">
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Total Users</p>
            <p class="text-3xl font-black text-white"><?= $totalUsers ?></p>
            <p class="text-xs text-emerald-400 mt-1">+<?= $recentSignups ?> this week</p>
        </div>
        <div class="stat-card glass rounded-2xl p-5">
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Total Messages</p>
            <p class="text-3xl font-black text-brand-400"><?= number_format($msgCount) ?></p>
            <p class="text-xs text-slate-500 mt-1">Encrypted (AES-256)</p>
        </div>
        <div class="stat-card glass rounded-2xl p-5">
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Files Stored</p>
            <p class="text-3xl font-black text-amber-400"><?= number_format($fileCount) ?></p>
            <p class="text-xs text-slate-500 mt-1"><?= round($storageDirSize / 1024 / 1024, 1) ?> MB on disk</p>
        </div>
        <div class="stat-card glass rounded-2xl p-5">
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Conversations</p>
            <p class="text-3xl font-black text-emerald-400"><?= number_format($convCount) ?></p>
            <p class="text-xs text-slate-500 mt-1">Active sessions</p>
        </div>
    </div>

    <!-- System Info Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="glass rounded-2xl p-5">
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-2">Storage Usage</p>
            <?php $usagePercent = $totalLimit > 0 ? round(($totalStorage / $totalLimit) * 100, 1) : 0; ?>
            <div class="flex items-end gap-2 mb-2">
                <span class="text-2xl font-bold text-white"><?= round($totalStorage / 1024 / 1024, 1) ?> MB</span>
                <span class="text-sm text-slate-500 mb-0.5">/ <?= round($totalLimit / 1024 / 1024, 0) ?> MB</span>
            </div>
            <div class="w-full h-2 bg-dark-800 rounded-full overflow-hidden">
                <div class="h-full rounded-full <?= $usagePercent > 80 ? 'bg-rose-500' : ($usagePercent > 50 ? 'bg-amber-500' : 'bg-emerald-500') ?>" style="width: <?= min($usagePercent, 100) ?>%"></div>
            </div>
            <p class="text-xs text-slate-500 mt-1"><?= $usagePercent ?>% utilized</p>
        </div>
        <div class="glass rounded-2xl p-5">
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-2">Database</p>
            <p class="text-2xl font-bold text-white"><?= round($dbSize / 1024 / 1024, 2) ?> MB</p>
            <p class="text-xs text-slate-500 mt-1">SQLite database file</p>
        </div>
        <div class="glass rounded-2xl p-5">
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-2">Encryption</p>
            <p class="text-lg font-bold text-emerald-400 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                AES-256-CBC Active
            </p>
            <p class="text-xs text-slate-500 mt-1">All messages & files encrypted at rest</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-white">User Management</h2>
                <p class="text-xs text-slate-500 mt-0.5">View user metadata. Encrypted content (messages, file data) is not accessible.</p>
            </div>
            <span class="text-xs text-slate-500 bg-dark-800 px-3 py-1.5 rounded-full border border-slate-700"><?= $totalUsers ?> accounts</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800 text-[10px] uppercase text-slate-500 tracking-wider">
                        <th class="text-left p-4">User</th>
                        <th class="text-left p-4">Email</th>
                        <th class="text-left p-4">Role</th>
                        <th class="text-left p-4">Storage</th>
                        <th class="text-left p-4">Joined</th>
                        <th class="text-right p-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-brand-600 to-rose-400 p-[2px] shrink-0">
                                    <div class="w-full h-full bg-dark-800 rounded-full flex items-center justify-center font-bold text-white text-xs">
                                        <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">@<?= htmlspecialchars($u['username']) ?></p>
                                    <?php if (trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))): ?>
                                        <p class="text-xs text-slate-500"><?= htmlspecialchars(trim($u['first_name'] . ' ' . $u['last_name'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-slate-400 text-xs"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full <?= $u['role'] === 'admin' ? 'bg-brand-500/20 text-brand-400 border border-brand-500/30' : 'bg-slate-700/50 text-slate-400 border border-slate-600/30' ?>">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <?php
                                $usedMb = round($u['storage_used'] / 1024 / 1024, 1);
                                $limitMb = round($u['storage_limit'] / 1024 / 1024, 0);
                                $pct = $u['storage_limit'] > 0 ? round(($u['storage_used'] / $u['storage_limit']) * 100) : 0;
                            ?>
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 bg-dark-800 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full <?= $pct > 80 ? 'bg-rose-500' : ($pct > 50 ? 'bg-amber-500' : 'bg-emerald-500') ?>" style="width: <?= min($pct, 100) ?>%"></div>
                                </div>
                                <span class="text-xs text-slate-400"><?= $usedMb ?> / <?= $limitMb ?> MB</span>
                            </div>
                        </td>
                        <td class="p-4 text-slate-500 text-xs"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td class="p-4">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Update Storage Limit -->
                                <form method="POST" action="/server-admin/update-storage" class="inline flex items-center gap-1">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <input type="number" name="storage_limit_mb" value="<?= $limitMb ?>" min="1" max="10240"
                                           class="w-16 bg-dark-800 border border-slate-700 rounded-lg py-1 px-2 text-xs text-white outline-none focus:ring-1 focus:ring-brand-500">
                                    <button type="submit" class="text-[10px] px-2 py-1 rounded-lg font-medium bg-brand-600/20 hover:bg-brand-600/40 text-brand-400 border border-brand-600/30 transition-colors">MB</button>
                                </form>
                                <!-- Delete -->
                                <form method="POST" action="/server-admin/delete-user" class="inline"
                                      onsubmit="return confirm('⚠️ DELETE @<?= htmlspecialchars($u['username']) ?>?\n\nThis will permanently remove:\n• Their account\n• All their files\n• All their messages\n\nThis cannot be undone.')">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="text-[10px] px-2.5 py-1 rounded-lg font-medium bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 transition-all">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center">
        <p class="text-[11px] text-slate-600">
            🔒 Server Administration Panel — Session expires automatically after 2 hours
            <br>Logged in as <span class="text-slate-400"><?= htmlspecialchars($_SESSION['server_admin_email'] ?? '') ?></span>
            at <?= date('H:i', $_SESSION['server_admin_login_time'] ?? time()) ?>
        </p>
    </div>
</div>

</body>
</html>
