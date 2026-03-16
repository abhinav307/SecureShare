<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'Privacy & Settings Hub') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                brand: { 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5' },
                dark: { 900: '#0f172a', 800: '#1e293b', 700: '#334155' }
            } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Outfit', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass-panel { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .field-input { width: 100%; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(100, 116, 139, 0.5); border-radius: 1rem; padding: 0.875rem 1.25rem; color: white; transition: all 0.2s; outline: none; }
        .field-input:focus { ring: 2px; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2); }
        .field-input::placeholder { color: #475569; }
    </style>
</head>
<body class="min-h-screen bg-dark-900 p-4 md:p-8">

<div class="max-w-4xl mx-auto">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="/chat" class="p-2.5 text-slate-400 hover:text-white bg-dark-800 hover:bg-slate-800 rounded-xl transition-colors border border-slate-700 shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white mb-1">Privacy & Settings Hub</h1>
                <p class="text-sm text-slate-400">Manage your SecureShare identity and data privacy.</p>
            </div>
        </div>
        <div class="hidden md:flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-full">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest">E2E Secured</span>
        </div>
    </div>

    <?php if (isset($error) && $error): ?>
        <div class="bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 p-4 rounded-r-xl mb-6 shadow-md flex items-center gap-3">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success) && $success): ?>
        <div class="bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 p-4 rounded-r-xl mb-6 shadow-md flex items-center gap-3">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Col -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profile Card -->
            <div class="glass-panel p-6 rounded-3xl flex flex-col items-center shadow-xl relative overflow-hidden group">
                <!-- Cover Photo Area -->
                <div class="absolute top-0 w-full h-32 bg-dark-800" id="cover-container">
                    <?php if (!empty($user['cover_url'])): ?>
                        <img src="<?= htmlspecialchars($user['cover_url']) ?>" alt="Cover" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-brand-600/30 to-rose-400/20"></div>
                    <?php endif; ?>
                    <label class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition-opacity backdrop-blur-sm">
                        <input type="file" id="cover-upload" class="hidden" accept="image/*">
                        <div class="bg-dark-900/80 p-2 rounded-xl text-white flex items-center gap-2 border border-white/10 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-xs font-medium">Update Cover</span>
                        </div>
                    </label>
                </div>

                <!-- Avatar Area -->
                <div class="relative w-32 h-32 rounded-full bg-gradient-to-tr from-brand-500 to-rose-400 p-1 mb-4 mt-16 shadow-2xl group/avatar z-10 transition-transform duration-500">
                    <div class="w-full h-full bg-dark-900 rounded-full flex items-center justify-center font-bold text-white text-5xl overflow-hidden relative" id="avatar-container">
                        <?php if (!empty($user['avatar_url'])): ?>
                            <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
                        <?php endif; ?>
                        
                        <label class="absolute inset-0 bg-black/50 opacity-0 group-hover/avatar:opacity-100 flex items-center justify-center cursor-pointer transition-opacity backdrop-blur-[2px]">
                            <input type="file" id="avatar-upload" class="hidden" accept="image/*">
                            <svg class="w-8 h-8 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </label>
                    </div>
                    <div class="absolute bottom-1 right-1 w-7 h-7 bg-emerald-500 border-4 border-dark-900 rounded-full z-20"></div>
                </div>
                <h2 class="text-2xl font-bold text-white tracking-tight">@<?= htmlspecialchars($user['username']) ?></h2>
                <?php $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                <?php if ($fullName): ?>
                    <p class="text-sm text-slate-300 font-medium mt-1"><?= htmlspecialchars($fullName) ?></p>
                <?php endif; ?>
                <p class="text-sm text-brand-300 truncate w-full text-center mt-1 font-medium"><?= htmlspecialchars($user['email']) ?></p>
                <?php if (!empty($user['status_message'])): ?>
                    <p class="mt-3 text-sm text-slate-300 text-center italic bg-dark-800/50 py-2 px-4 rounded-full border border-white/5">"<?= htmlspecialchars($user['status_message']) ?>"</p>
                <?php endif; ?>
                <?php if (!empty($user['about_me'])): ?>
                    <p class="mt-3 text-xs text-slate-400 text-center max-w-full"><?= htmlspecialchars($user['about_me']) ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Storage Quota Widget -->
            <div class="glass-panel p-6 rounded-3xl shadow-xl relative overflow-hidden">
                <h3 class="text-white font-semibold mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                    Cloud Storage
                </h3>
                <?php 
                    $usedMB = round($user['storage_used'] / 1024 / 1024, 2);
                    $limitMB = round($user['storage_limit'] / 1024 / 1024, 0);
                    $pct = min(100, ($user['storage_used'] / max(1, $user['storage_limit'])) * 100);
                    $barColor = $pct > 80 ? 'from-rose-500 to-red-400' : 'from-brand-500 to-indigo-400';
                ?>
                <div class="flex justify-between text-[13px] mb-3 font-medium">
                    <span class="text-brand-300"><?= $usedMB ?> MB Used</span>
                    <span class="text-slate-500"><?= $limitMB ?> MB Total</span>
                </div>
                <div class="w-full bg-dark-900 rounded-full h-3 mb-2 border border-slate-700/50 overflow-hidden p-0.5">
                    <div class="bg-gradient-to-r <?= $barColor ?> h-full rounded-full transition-all duration-1000" style="width: <?= $pct ?>%"></div>
                </div>
                <p class="text-[11px] text-slate-500 text-right"><?= 100 - round($pct, 1) ?>% Space Remaining</p>
            </div>

            <!-- Logout Button in sidebar -->
            <a href="/logout" class="flex items-center justify-center gap-2 w-full py-3 bg-rose-500/10 hover:bg-rose-500 border border-rose-500/30 hover:border-rose-500 text-rose-400 hover:text-white font-semibold rounded-2xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Log Out
            </a>
        </div>

        <!-- Right Col: Settings Form -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- E2E Panel -->
            <div class="bg-gradient-to-r from-dark-800 to-dark-900 p-6 rounded-3xl shadow-xl border border-emerald-500/20 relative overflow-hidden flex flex-col md:flex-row items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center shrink-0 shadow-[0_0_30px_rgba(16,185,129,0.15)]">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight mb-2">End-to-End Encryption is Active</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Your messages and files are encrypted on-device using AES-256-CBC before transmission. Nobody — not even our servers — can read your data.</p>
                </div>
            </div>

            <!-- Identity Form -->
            <div class="glass-panel p-6 md:p-8 rounded-3xl shadow-xl">
                <h3 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Identity Profile
                </h3>
                
                <form method="POST" class="space-y-5">
                    <!-- Name Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">First Name</label>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                   placeholder="John" maxlength="60"
                                   class="field-input focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Last Name</label>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                   placeholder="Doe" maxlength="60"
                                   class="field-input focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Phone Number</label>
                        <div class="relative">
                            <input type="tel" name="phone_number" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>"
                                   placeholder="+1 (555) 000-0000" maxlength="20"
                                   class="field-input focus:ring-2 focus:ring-brand-500 pl-12">
                            <svg class="w-5 h-5 text-slate-500 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Status Message</label>
                        <div class="relative">
                            <input type="text" name="status_message" value="<?= htmlspecialchars($user['status_message'] ?? '') ?>"
                                   placeholder="e.g., Available for collaboration" maxlength="60"
                                   class="field-input focus:ring-2 focus:ring-brand-500 pl-12">
                            <svg class="w-5 h-5 text-slate-500 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                    </div>

                    <!-- About Me -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">About Me</label>
                        <textarea name="about_me" rows="3" maxlength="300"
                                  placeholder="Tell others about yourself..."
                                  class="field-input focus:ring-2 focus:ring-brand-500 resize-none"><?= htmlspecialchars($user['about_me'] ?? '') ?></textarea>
                        <p class="text-xs text-slate-500 mt-1">Max 300 characters.</p>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" name="update_profile" value="1"
                                class="py-3 px-8 bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-2xl transition-all shadow-[0_4px_14px_rgba(79,70,229,0.3)] hover:shadow-[0_6px_20px_rgba(79,70,229,0.5)] transform hover:-translate-y-0.5">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- ── APPEARANCE SETTINGS ─────────────────────────────────────── -->
            <div class="glass-panel p-6 md:p-8 rounded-3xl mb-8">
                <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    Appearance
                </h3>

                <!-- App Theme: Dark / Light -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-300 mb-3">App Theme</label>
                    <div class="grid grid-cols-3 gap-3" id="app-theme-selector">
                        <button class="app-theme-btn flex flex-col items-center gap-2 p-4 rounded-2xl border-2 border-slate-700 hover:border-brand-500 transition-all" data-theme="dark">
                            <div class="w-full h-10 rounded-xl bg-gradient-to-br from-slate-900 to-slate-800 shadow-inner border border-slate-700"></div>
                            <span class="text-sm font-semibold text-slate-300">Dark</span>
                        </button>
                        <button class="app-theme-btn flex flex-col items-center gap-2 p-4 rounded-2xl border-2 border-slate-700 hover:border-brand-500 transition-all" data-theme="light">
                            <div class="w-full h-10 rounded-xl bg-gradient-to-br from-slate-100 to-white shadow-inner border border-slate-200"></div>
                            <span class="text-sm font-semibold text-slate-300">Light</span>
                        </button>
                        <button class="app-theme-btn flex flex-col items-center gap-2 p-4 rounded-2xl border-2 border-slate-700 hover:border-brand-500 transition-all" data-theme="system">
                            <div class="w-full h-10 rounded-xl bg-gradient-to-br from-slate-900 via-slate-500 to-white shadow-inner border border-slate-700"></div>
                            <span class="text-sm font-semibold text-slate-300">System</span>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Applies to the entire app interface.</p>
                </div>

                <!-- Font Size -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-3">Text Size</label>
                    <div class="flex gap-3">
                        <button class="font-size-btn flex-1 py-2 px-4 rounded-xl border border-slate-700 hover:border-brand-500 text-slate-300 font-medium text-xs transition-all" data-size="sm">Small</button>
                        <button class="font-size-btn flex-1 py-2 px-4 rounded-xl border border-slate-700 hover:border-brand-500 text-slate-300 font-medium text-sm transition-all" data-size="md">Medium</button>
                        <button class="font-size-btn flex-1 py-2 px-4 rounded-xl border border-slate-700 hover:border-brand-500 text-slate-300 font-medium text-base transition-all" data-size="lg">Large</button>
                    </div>
                </div>
            </div>

            <!-- ── PRIVACY & DATA SETTINGS ──────────────────────────────────── -->
            <div class="glass-panel p-6 md:p-8 rounded-3xl mb-8">
                <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Privacy & Data
                </h3>

                <div class="space-y-5">
                    <!-- Read Receipts -->
                    <div class="flex items-center justify-between p-4 bg-dark-900/50 rounded-2xl border border-slate-800">
                        <div>
                            <h4 class="text-white font-medium">Read Receipts</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Show others when you've read their messages</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="pref-read-receipts" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-700 peer-focus:ring-brand-500 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                        </label>
                    </div>

                    <!-- Online Status -->
                    <div class="flex items-center justify-between p-4 bg-dark-900/50 rounded-2xl border border-slate-800">
                        <div>
                            <h4 class="text-white font-medium">Online Status Visibility</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Allow others to see when you're active</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="pref-online-status" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-700 peer-focus:ring-brand-500 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                        </label>
                    </div>

                    <!-- Analytics -->
                    <div class="flex items-center justify-between p-4 bg-dark-900/50 rounded-2xl border border-slate-800">
                        <div>
                            <h4 class="text-white font-medium">Analytics & Diagnostics</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Help improve SecureShare with anonymous usage data</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="pref-analytics" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-700 peer-focus:ring-brand-500 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                        </label>
                    </div>

                    <!-- Data Retention -->
                    <div class="p-4 bg-dark-900/50 rounded-2xl border border-slate-800">
                        <h4 class="text-white font-medium mb-2">Message Retention</h4>
                        <p class="text-xs text-slate-500 mb-3">Automatically delete messages older than:</p>
                        <div class="flex gap-2 flex-wrap">
                            <button class="retention-btn px-3 py-1.5 text-xs rounded-lg border border-slate-700 hover:border-brand-500 text-slate-300 transition-all" data-days="30">30 days</button>
                            <button class="retention-btn px-3 py-1.5 text-xs rounded-lg border border-slate-700 hover:border-brand-500 text-slate-300 transition-all" data-days="90">90 days</button>
                            <button class="retention-btn px-3 py-1.5 text-xs rounded-lg border border-slate-700 hover:border-brand-500 text-slate-300 transition-all" data-days="180">6 months</button>
                            <button class="retention-btn px-3 py-1.5 text-xs rounded-lg border border-brand-500 text-brand-400 transition-all" data-days="365">1 year (default)</button>
                            <button class="retention-btn px-3 py-1.5 text-xs rounded-lg border border-slate-700 hover:border-brand-500 text-slate-300 transition-all" data-days="0">Never</button>
                        </div>
                    </div>

                    <!-- Cookies -->
                    <div class="p-4 bg-dark-900/50 rounded-2xl border border-slate-800">
                        <h4 class="text-white font-medium mb-1">Cookies & Local Storage</h4>
                        <p class="text-xs text-slate-500 mb-3">SecureShare uses only essential cookies for session management. No tracking cookies are used.</p>
                        <div class="flex gap-3">
                            <button onclick="if(confirm('Clear local cache? Your session will be reset.')) { localStorage.clear(); sessionStorage.clear(); window.location.href='/logout'; }"
                                    class="px-4 py-2 text-xs bg-dark-800 border border-slate-700 hover:border-slate-500 rounded-xl text-slate-300 transition-all font-semibold">
                                Clear Local Cache
                            </button>
                            <a href="/logout" class="px-4 py-2 text-xs bg-dark-800 border border-slate-700 hover:border-slate-500 rounded-xl text-slate-300 transition-all font-semibold flex items-center">
                                Invalidate Session
                            </a>
                        </div>
                    </div>

                    <!-- Save Privacy Prefs -->
                    <div class="pt-2 flex justify-end">
                        <button id="save-privacy-btn" class="py-3 px-8 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-2xl transition-all shadow-[0_4px_14px_rgba(16,185,129,0.3)]">
                            Save Privacy Settings
                        </button>
                    </div>

                    <!-- Blocked Users -->
                    <div class="pt-4 border-t border-slate-800">
                        <h4 class="text-white font-semibold mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            Blocked Users
                        </h4>
                        <ul id="blocked-users-list" class="space-y-2 max-h-48 overflow-y-auto">
                            <li class="text-xs text-slate-500 italic">Loading...</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="border border-rose-500/20 bg-rose-500/[0.02] p-6 md:p-8 rounded-3xl">
                <h3 class="text-sm font-black text-rose-500 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Danger Zone
                </h3>
                <div class="space-y-6">
                    <!-- Purge Files -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 bg-dark-900/50 rounded-2xl border border-rose-500/10">
                        <div>
                            <h4 class="text-white font-medium mb-1">Clear Cloud Storage Array</h4>
                            <p class="text-sm text-slate-400 max-w-md">Purge all your encrypted files and multimedia. Text messages remain but file blobs are permanently wiped.</p>
                        </div>
                        <form method="POST" onsubmit="return confirm('This is irreversible. All your files will be deleted. Continue?')">
                            <button type="submit" name="clear_storage" value="1" class="w-full md:w-auto py-2.5 px-6 bg-rose-500 hover:bg-rose-600 text-white font-semibold rounded-xl transition-all shadow-[0_4px_14px_rgba(244,63,94,0.4)] whitespace-nowrap">
                                Purge All Files
                            </button>
                        </form>
                    </div>

                    <!-- Delete Account -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 bg-dark-900/50 rounded-2xl border border-rose-500/10 opacity-75">
                        <div>
                            <h4 class="text-white font-medium mb-1">Terminate Account</h4>
                            <p class="text-sm text-slate-400 max-w-md">Permanently delete your account, conversations, files, and encryption keys. Cannot be undone.</p>
                        </div>
                        <button type="button" class="w-full md:w-auto py-2.5 px-6 border border-rose-500/50 text-rose-500 hover:bg-rose-500 hover:text-white font-semibold rounded-xl transition-all whitespace-nowrap"
                                onclick="alert('Account termination requires admin approval. Please contact support.')">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
// ── LOAD PREFERENCES ──────────────────────────────────────────────────────
async function loadPreferences() {
    try {
        const r = await fetch('/api/user/preferences');
        const data = await r.json();
        const p = data.preferences || {};

        // Apply app theme
        applyAppTheme(p.theme || localStorage.getItem('app_theme') || 'dark');
        highlightThemeBtn(p.theme || localStorage.getItem('app_theme') || 'dark');

        // Privacy toggles
        document.getElementById('pref-read-receipts').checked = (p.show_read_receipts != 0);
        document.getElementById('pref-online-status').checked = (p.show_online_status != 0);
        document.getElementById('pref-analytics').checked = (p.analytics_opt_out == 1 ? false : true);

        // Retention
        const days = p.data_retention_days || 365;
        document.querySelectorAll('.retention-btn').forEach(btn => {
            const active = btn.dataset.days == days;
            btn.classList.toggle('border-brand-500', active);
            btn.classList.toggle('text-brand-400', active);
            btn.classList.toggle('border-slate-700', !active);
            btn.classList.toggle('text-slate-300', !active);
        });
    } catch(e) {}

    // Blocked users
    loadBlockedUsers();
}

async function loadBlockedUsers() {
    try {
        const r = await fetch('/api/chat/blocked');
        const data = await r.json();
        const list = document.getElementById('blocked-users-list');
        if (!data.blocked || data.blocked.length === 0) {
            list.innerHTML = '<li class="text-xs text-slate-500 italic">No blocked users.</li>';
            return;
        }
        list.innerHTML = '';
        data.blocked.forEach(u => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between py-2.5 px-3 bg-dark-900/60 rounded-xl border border-slate-800';
            li.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-brand-600 to-rose-400 flex items-center justify-center text-white text-xs font-bold">${u.username.charAt(0).toUpperCase()}</div>
                    <span class="text-sm text-slate-200 font-medium">@${u.username}</span>
                </div>
                <button class="unblock-btn px-3 py-1 text-xs bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 rounded-lg transition-all font-semibold" data-uid="${u.id}">Unblock</button>
            `;
            list.appendChild(li);
        });
    } catch(e) {}
}

// Unblock
document.getElementById('blocked-users-list').addEventListener('click', async e => {
    const btn = e.target.closest('.unblock-btn');
    if (!btn) return;
    const uid = btn.dataset.uid;
    await fetch('/api/chat/unblock', { method: 'POST', body: new URLSearchParams({ blocked_id: uid }) });
    showSettingsToast('User unblocked!');
    loadBlockedUsers();
});

// ── APP THEME ──────────────────────────────────────────────────────────────
function applyAppTheme(theme) {
    const body = document.body;
    if (theme === 'light' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: light)').matches)) {
        body.classList.add('bg-slate-100');
        body.style.backgroundColor = '#f1f5f9';
        body.style.color = '#1e293b';
    } else {
        body.classList.remove('bg-slate-100');
        body.style.backgroundColor = '#0f172a';
        body.style.color = '#f8fafc';
    }
    localStorage.setItem('app_theme', theme);
}

function highlightThemeBtn(theme) {
    document.querySelectorAll('.app-theme-btn').forEach(btn => {
        const active = btn.dataset.theme === theme;
        btn.classList.toggle('border-brand-500', active);
        btn.classList.toggle('border-slate-700', !active);
    });
}

document.querySelectorAll('.app-theme-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const t = btn.dataset.theme;
        applyAppTheme(t);
        highlightThemeBtn(t);
        fetch('/api/user/preferences', { method: 'POST', body: new URLSearchParams({ theme: t }) });
        showSettingsToast('Theme applied!');
    });
});

// Font size
document.querySelectorAll('.font-size-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const sizeMap = { sm: '14px', md: '16px', lg: '18px' };
        document.documentElement.style.fontSize = sizeMap[btn.dataset.size];
        localStorage.setItem('font_size', btn.dataset.size);
        document.querySelectorAll('.font-size-btn').forEach(b => {
            b.classList.toggle('border-brand-500', b === btn);
            b.classList.toggle('text-brand-400', b === btn);
            b.classList.toggle('border-slate-700', b !== btn);
            b.classList.toggle('text-slate-300', b !== btn);
        });
        showSettingsToast('Font size updated!');
    });
});

// Retention buttons
let activeRetention = 365;
document.querySelectorAll('.retention-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        activeRetention = parseInt(btn.dataset.days);
        document.querySelectorAll('.retention-btn').forEach(b => {
            const active = b === btn;
            b.classList.toggle('border-brand-500', active);
            b.classList.toggle('text-brand-400', active);
            b.classList.toggle('border-slate-700', !active);
            b.classList.toggle('text-slate-300', !active);
        });
    });
});

// ── SAVE PRIVACY SETTINGS ──────────────────────────────────────────────────
document.getElementById('save-privacy-btn').addEventListener('click', async () => {
    const body = new URLSearchParams({
        show_read_receipts: document.getElementById('pref-read-receipts').checked ? 1 : 0,
        show_online_status: document.getElementById('pref-online-status').checked ? 1 : 0,
        analytics_opt_out:  document.getElementById('pref-analytics').checked ? 0 : 1,
        data_retention_days: activeRetention
    });
    const r = await fetch('/api/user/preferences', { method: 'POST', body });
    const data = await r.json();
    if (data.success) showSettingsToast('Privacy settings saved!');
    else showSettingsToast('Failed to save.', 'error');
});

// Toast
function showSettingsToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl text-sm font-semibold shadow-2xl transition-all ${
        type === 'error' ? 'bg-rose-500 text-white' : 'bg-emerald-500 text-white'
    }`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 2500);
}

// Apply saved font size on load
const savedFont = localStorage.getItem('font_size');
if (savedFont) { const sizeMap = { sm: '14px', md: '16px', lg: '18px' }; document.documentElement.style.fontSize = sizeMap[savedFont] || '16px'; }

loadPreferences();
</script>
    <script>
    function showToast(msg, type = 'info') {
        const t = document.createElement('div');
        t.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl text-sm font-semibold shadow-2xl transition-all duration-300 transform translate-y-10 opacity-0 ${
            type === 'success' ? 'bg-emerald-500 text-white' : 
            type === 'error' ? 'bg-rose-500 text-white' : 
            'bg-dark-800 text-white border border-slate-700'}`;
        t.textContent = msg;
        document.body.appendChild(t);
        
        requestAnimationFrame(() => {
            t.classList.remove('translate-y-10', 'opacity-0');
        });
        
        setTimeout(() => {
            t.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => t.remove(), 300);
        }, 3000);
    }

    async function uploadImage(file, endpoint, containerId, isCover = false) {
        if (!file) return;
        
        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            showToast('Please select a valid image (JPG, PNG, WEBP, GIF).', 'error');
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            showToast('Image must be less than 5MB.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('image', file);

        try {
            showToast('Uploading...', 'info');
            const res = await fetch(endpoint, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (res.ok && data.success) {
                const container = document.getElementById(containerId);
                const styleClass = isCover ? 'w-full h-full object-cover' : 'w-full h-full object-cover rounded-full';
                container.innerHTML = `<img src="${data.url}" class="${styleClass}" alt="Image">` + 
                    (isCover ? container.querySelector('label').outerHTML : container.querySelector('label').outerHTML);
                
                showToast('Image updated successfully!', 'success');
            } else {
                showToast(data.error || 'Upload failed', 'error');
            }
        } catch (err) {
            showToast('Network error during upload.', 'error');
        }
    }

    document.getElementById('avatar-upload').addEventListener('change', function(e) {
        uploadImage(e.target.files[0], '/api/profile/upload-avatar', 'avatar-container', false);
    });

    document.getElementById('cover-upload').addEventListener('change', function(e) {
        uploadImage(e.target.files[0], '/api/profile/upload-cover', 'cover-container', true);
    });
    </script>
</body>
</html>
