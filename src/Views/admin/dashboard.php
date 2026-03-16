<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Portal') ?></title>
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
    </style>
</head>
<body class="min-h-screen" style="background-image: radial-gradient(circle at 15% 50%, rgba(99,102,241,0.08), transparent 30%);">

<!-- Top Nav -->
<header class="sticky top-0 z-50 bg-dark-900/90 backdrop-blur-xl border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-tr from-brand-600 to-rose-400 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h1 class="text-lg font-bold text-white">SecureShare <span class="text-brand-400">Admin</span></h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="/chat" class="text-sm text-slate-400 hover:text-white transition-colors">← Back to App</a>
            <a href="/logout" class="text-sm bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-medium px-4 py-2 rounded-xl transition-colors border border-rose-500/20">Log Out</a>
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto px-6 py-10">

    <!-- Alerts -->
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 rounded-xl mb-8 text-sm">
            ✓ <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 p-4 rounded-xl mb-8 text-sm">
            ✗ <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="glass rounded-2xl p-6">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">Total Users</p>
            <p class="text-4xl font-black text-white"><?= $totalUsers ?></p>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">Total Storage Used</p>
            <p class="text-4xl font-black text-brand-400"><?= round($totalStorage / 1024 / 1024, 1) ?> <span class="text-xl font-semibold text-slate-400">MB</span></p>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">Encryption Protocol</p>
            <p class="text-xl font-bold text-emerald-400 flex items-center gap-2 mt-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                AES-256-CBC Active
            </p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">Registered Users</h2>
            <span class="text-xs text-slate-500 bg-dark-800 px-3 py-1.5 rounded-full border border-slate-700"><?= $totalUsers ?> accounts</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800 text-xs uppercase text-slate-500 tracking-wider">
                        <th class="text-left p-4">User</th>
                        <th class="text-left p-4">Email</th>
                        <th class="text-left p-4">Name</th>
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
                                    <?php if ($u['status_message']): ?>
                                        <p class="text-xs text-slate-500 truncate max-w-[120px]"><?= htmlspecialchars($u['status_message']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-slate-400"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="p-4 text-slate-400">
                            <?= trim(htmlspecialchars($u['first_name'] . ' ' . $u['last_name'])) ?: '<span class="text-slate-600 italic">Not set</span>' ?>
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full <?= $u['role'] === 'admin' ? 'bg-brand-500/20 text-brand-400 border border-brand-500/30' : 'bg-slate-700/50 text-slate-400 border border-slate-600/30' ?>">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="text-slate-300 font-medium"><?= round($u['storage_used'] / 1024 / 1024, 1) ?> MB</span>
                            <span class="text-slate-600"> / <?= round($u['storage_limit'] / 1024 / 1024, 0) ?> MB</span>
                        </td>
                        <td class="p-4 text-slate-500 text-xs"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td class="p-4">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Promote/Demote -->
                                <form method="POST" action="/admin/promote" class="inline">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="role" value="<?= $u['role'] === 'admin' ? 'user' : 'admin' ?>">
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors <?= $u['role'] === 'admin' ? 'bg-slate-700 hover:bg-slate-600 text-slate-300' : 'bg-brand-600/20 hover:bg-brand-600/40 text-brand-400 border border-brand-600/30' ?>">
                                        <?= $u['role'] === 'admin' ? 'Revoke Admin' : 'Make Admin' ?>
                                    </button>
                                </form>
                                <!-- Delete -->
                                <form method="POST" action="/admin/delete" class="inline"
                                      onsubmit="return confirm('Delete @<?= htmlspecialchars($u['username']) ?>? This will permanently remove their account and all files.')">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg font-medium bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 transition-all">
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
</div>

</body>
</html>
