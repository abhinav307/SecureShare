<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'SecureChat') ?></title>
    <!-- Tailwind CSS (via CDN for development) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                        },
                        dark: {
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: #0b0f19; 
            background-image: radial-gradient(circle at 15% 50%, rgba(99, 102, 241, 0.08), transparent 25%), radial-gradient(circle at 85% 30%, rgba(225, 29, 72, 0.05), transparent 25%);
            color: #f8fafc; 
            overflow: hidden; 
        }
        
        /* Premium Glassmorphism */
        .glass { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.03); box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); }
        .glass-header { background: rgba(11, 15, 25, 0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }

        /* Responsive UI Panes */
        .chat-sidebar { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .chat-main { transition: opacity 0.4s ease-in-out; }
        
        @media (max-width: 768px) {
            .mobile-active-chat .chat-sidebar { transform: translateX(-100%); position: absolute; width: 100%; height: 100%; z-index: 10; }
            .mobile-active-chat .chat-main { display: flex !important; width: 100%; position: absolute; height: 100%; top: 0; left: 0; z-index: 20; background: #0b0f19; }
            .chat-main { display: none; }
        }

        /* High-End Message Bubbles */
        .msg-bubble { max-width: 75%; min-width: 140px; width: fit-content; word-break: break-word; font-weight: 400; letter-spacing: 0.01em; box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
        .msg-sent { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); border-radius: 20px; border-bottom-right-radius: 4px; border: 1px solid rgba(255,255,255,0.1); }
        .msg-received { background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); border-radius: 20px; border-bottom-left-radius: 4px; border: 1px solid rgba(255, 255, 255, 0.05); }
        
        /* Smooth hover on contacts */
        .contact-item { border: 1px solid transparent; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .contact-item:hover { transform: translateX(4px); background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.05); }
        
        /* File Upload Drop Zone */
        .drag-active { background: rgba(99, 102, 241, 0.15) !important; border-color: #818cf8 !important; box-shadow: inset 0 0 50px rgba(99, 102, 241, 0.1); }

        /* Slide-in animation for panels */
        @keyframes slideInRight { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>
<body class="h-screen w-screen flex flex-col md:flex-row bg-dark-900 text-slate-200" id="app-container">

    <!-- SIDEBAR: Contacts & Search -->
    <div class="chat-sidebar w-full md:w-80 lg:w-96 h-full flex flex-col border-r border-slate-800 glass z-10 bg-dark-900/90 relative">
        
        <!-- Header: App Brand + Settings -->
        <div class="p-4 glass-header flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-rose-400 flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h1 class="font-bold text-white text-lg tracking-tight">SecureShare</h1>
            </div>
            <button onclick="openFileManager()" class="p-2 hover:bg-slate-700/50 rounded-xl transition-colors text-slate-400 hover:text-white" title="File Manager">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            </button>
        </div>

        <!-- Search & AI Hub Action -->
        <div class="p-3 pb-1">
            <div class="flex gap-2 mb-3 relative">
                <div class="relative flex-1">
                    <input type="text" id="global-search" placeholder="Search users globally..." class="w-full bg-dark-800 border-none rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-brand-500 outline-none transition-all">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <button id="create-group-btn" title="Create New Group" class="w-10 h-10 shrink-0 bg-dark-800 hover:bg-brand-600 border border-slate-700 hover:border-brand-500 rounded-xl flex items-center justify-center text-slate-300 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>
            
            <!-- Dedicated AI Hub Button -->
            <button id="launch-ai-btn" class="w-full flex items-center justify-center gap-2 py-2.5 bg-gradient-to-r from-brand-600 to-indigo-500 hover:from-brand-500 hover:to-indigo-400 text-white font-medium rounded-xl transition-all shadow-lg shadow-brand-500/20 group">
                <svg class="w-5 h-5 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Chat with SecureShare AI
            </button>
            
            <!-- Search Results Dropdown -->
            <ul id="search-results" class="hidden absolute top-[118px] left-3 right-3 bg-dark-800 border border-slate-700/50 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] z-[100] max-h-60 overflow-y-auto divide-y divide-slate-700/50">
                <!-- Injected via JS -->
            </ul>
        </div>

        <!-- Contacts & Groups List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1" id="contacts-list">
            
            <!-- Groups -->
            <?php if (!empty($groups)): ?>
                <?php foreach ($groups as $g): ?>
                    <button class="contact-item w-full flex items-center gap-3 p-3 rounded-xl hover:bg-dark-800 transition-colors text-left group border border-transparent" data-id="g<?= $g['id'] ?>-0" data-uid="g<?= $g['id'] ?>" data-name="<?= htmlspecialchars($g['name']) ?>" data-is-group="true">
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-600 to-indigo-600 text-white flex items-center justify-center font-bold shadow-sm">
                                <?= strtoupper(substr($g['name'], 0, 2)) ?>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-0.5">
                                <h3 class="font-semibold text-slate-200 truncate group-hover:text-white transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <?= htmlspecialchars($g['name']) ?>
                                </h3>
                            </div>
                            <p class="text-xs text-slate-400 truncate">
                                <?= htmlspecialchars($g['latest_message'] ?: 'No messages yet.') ?>
                            </p>
                        </div>
                    </button>
                <?php endforeach; ?>
                <div class="h-px bg-slate-800/80 my-2 mx-2"></div>
            <?php endif; ?>

            <!-- Direct Messages -->
            <?php if (empty($conversations) && empty($groups)): ?>
                <div class="text-center p-8 text-slate-500 flex flex-col items-center">
                    <div class="w-16 h-16 bg-dark-800 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <p class="text-sm">No recent chats.</p>
                    <p class="text-xs mt-1">Search for a user to start.</p>
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $c): ?>
                    <button class="contact-item w-full flex items-center gap-3 p-3 rounded-xl hover:bg-dark-800 transition-colors text-left group <?php echo (isset($_GET['c']) && $_GET['c'] == $c['conversation_id']) ? 'bg-brand-500/10 border border-brand-500/20' : 'border border-transparent' ?>" data-id="<?= $c['conversation_id'] ?>" data-uid="<?= $c['other_user_id'] ?>" data-name="<?= htmlspecialchars($c['other_user_name']) ?>" data-is-group="false">
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 rounded-full bg-dark-800 text-slate-300 flex items-center justify-center font-bold border border-slate-700">
                                <?= strtoupper(substr($c['other_user_name'], 0, 1)) ?>
                            </div>
                            <?php if ($c['unread_count'] > 0): ?>
                                <span class="absolute -top-1 -right-1 flex h-5 w-5 rounded-full bg-brand-500 border-2 border-dark-900 items-center justify-center text-[10px] font-bold text-white">
                                    <?= $c['unread_count'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-0.5">
                                <h3 class="font-semibold text-slate-200 truncate group-hover:text-white transition-colors"><?= htmlspecialchars($c['other_user_name']) ?></h3>
                                <span class="text-[10px] text-slate-500"><?= date('H:i', strtotime($c['updated_at'])) ?></span>
                            </div>
                            <p class="text-xs text-slate-400 truncate <?php echo $c['unread_count'] > 0 ? 'font-medium text-slate-300' : '' ?>">
                                <?= htmlspecialchars($c['latest_message'] ?: 'Attach encrypted file.') ?>
                            </p>
                        </div>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Bottom Profile Identity Bar -->
        <div class="shrink-0 p-3 border-t border-slate-800/80 bg-dark-900/70 backdrop-blur-sm">
            <a href="/profile/settings" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-800/60 transition-colors group">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-brand-600 to-rose-400 p-[2px] shrink-0">
                    <div class="w-full h-full bg-dark-800 rounded-full flex items-center justify-center font-bold text-white text-sm">
                        <?= strtoupper(substr($user['username'], 0, 2)) ?>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white text-sm leading-tight truncate">
                        <?php 
                            $displayName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                            echo htmlspecialchars($displayName ?: $user['username']);
                        ?>
                    </p>
                    <p class="text-xs text-brand-400 truncate"><?= round($user['storage_used'] / 1024 / 1024, 1) ?>MB / <?= round($user['storage_limit'] / 1024 / 1024, 0) ?>MB used</p>
                </div>
                <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-300 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </a>
        </div>
    </div>

    <!-- MAIN: Active Chat -->
    <div class="chat-main flex-1 flex-col h-full bg-transparent relative hidden md:flex overflow-hidden" id="chat-pane">
        
        <!-- Welcome Screen (When no chat selected) -->
        <div id="chat-placeholder" class="absolute inset-0 flex flex-col items-center justify-center z-0 bg-[#0b0f19]">
            <div class="relative w-40 h-40 mb-8 flex items-center justify-center">
                <div class="absolute inset-0 rounded-full bg-brand-500/20 blur-3xl animate-pulse"></div>
                <div class="absolute inset-4 rounded-full bg-gradient-to-tr from-brand-600 to-indigo-400 blur-2xl opacity-50"></div>
                <div class="relative w-24 h-24 bg-dark-800/80 backdrop-blur-xl rounded-full flex items-center justify-center shadow-2xl border border-white/10">
                    <svg class="w-10 h-10 text-white drop-shadow-[0_0_8px_rgba(255,255,255,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
            </div>
            <h2 class="text-3xl font-bold tracking-tight text-white mb-3">SecureChat</h2>
            <p class="text-slate-400 max-w-md text-center text-sm leading-relaxed">Experience next-generation messaging. All files and messages are wrapped in military-grade AES-256 encryption before transmission.</p>
        </div>

        <!-- Active Chat Interface -->
        <div id="active-chat-wrapper" class="hidden flex-col h-full w-full relative z-10">
            <!-- Active Chat Header -->
        <div class="h-16 shrink-0 border-b border-slate-800/80 glass flex items-center justify-between px-4 sm:px-6 relative z-30">
            <div class="flex items-center gap-4">
                <button id="back-to-contacts" class="md:hidden text-slate-400 hover:text-white transition-colors bg-slate-800/50 p-2 rounded-xl border border-slate-700 hover:border-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="chat-header-profile-btn" class="flex items-center gap-3 group text-left transition-all rounded-r-xl pr-3 hover:bg-slate-800/30">
                    <div class="relative group-hover:drop-shadow-[0_0_12px_rgba(99,102,241,0.5)] transition-all flex items-center justify-center">
                        <div class="absolute inset-0 bg-brand-500 rounded-full blur-sm opacity-20 group-hover:opacity-60 transition-opacity"></div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-600 to-indigo-500 text-white flex items-center justify-center font-bold text-lg shadow-lg relative z-10" id="active-avatar">
                            U
                        </div>
                    </div>
                    <div>
                        <h2 class="font-bold text-white tracking-tight group-hover:text-brand-300 transition-colors flex items-center gap-1.5" id="active-name">User</h2>
                        <div class="flex items-center gap-1.5 opacity-80" id="active-encryption-status">
                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span class="text-[10px] font-medium text-emerald-400 font-mono tracking-widest uppercase" id="end-to-end-txt">End-to-end encrypted session</span>
                        </div>
                    </div>
                </button>
            </div>
            
            <div class="flex items-center gap-1 sm:gap-2">
                <button class="p-2 sm:p-2.5 hover:bg-slate-800 text-slate-400 hover:text-brand-400 rounded-xl transition-all hover:scale-105 active:scale-95 tooltip" id="in-chat-search-btn" title="Search in Chat">
                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                <button class="p-2 sm:p-2.5 hover:bg-slate-800 text-slate-400 hover:text-brand-400 rounded-xl transition-all hover:scale-105 active:scale-95 tooltip hidden sm:block delay-1" id="chat-theme-btn" title="Chat Theme">
                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                </button>
                <div class="h-6 w-px bg-slate-700 mx-1 hidden sm:block delay-1"></div>
                <button class="p-2 sm:p-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition-all hover:scale-105 active:scale-95 tooltip mt-1 delay-2" id="chat-more-btn" title="More Options">
                    <svg class="w-5 h-5 mx-auto flex items-center justify-center p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
                <!-- More dropdown -->
                <div id="chat-more-dropdown" class="hidden absolute right-4 top-16 bg-dark-800 border border-slate-700 rounded-2xl shadow-2xl z-50 py-1 min-w-[180px]">
                    <button id="delete-conv-btn" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-400 hover:bg-rose-500/10 transition-colors rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete Conversation
                    </button>
                    <button id="block-user-btn" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700 transition-colors rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        Block User
                    </button>
                </div>
            </div>
        </div>

        <!-- Subgroups Bar (Hidden by default, shown for groups) -->
        <div id="subgroups-bar" class="hidden shrink-0 bg-dark-800 border-b border-dark-900 px-4 py-2 flex items-center gap-2 overflow-x-auto overflow-y-hidden custom-scrollbar">
            <!-- Injected by JS -->
        </div>

        <!-- In-Chat Search Bar (hidden by default) -->
            <div id="in-chat-search-bar" class="hidden px-4 pb-3 bg-dark-900/80 border-b border-slate-800">
                <div class="relative">
                    <input type="text" id="in-chat-search-input" placeholder="Search messages..." 
                           class="w-full bg-dark-800 border border-slate-700 rounded-xl py-2.5 pl-10 pr-10 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-brand-500 outline-none">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span id="search-match-count" class="absolute right-3 top-2.5 text-xs text-brand-400 font-medium"></span>
                </div>
            </div>

            <!-- Messages Area -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 scroll-smooth" id="messages-container">
                <!-- Messages injected here -->
            </div>
            
            <!-- File Drag Overlay -->
            <div id="drag-overlay" class="hidden absolute inset-0 z-30 bg-brand-500/20 backdrop-blur-sm border-2 border-brand-500 border-dashed m-4 rounded-2xl flex flex-col items-center justify-center pointer-events-none transition-all">
                <div class="bg-dark-900 p-6 rounded-2xl flex flex-col items-center shadow-2xl">
                    <svg class="w-12 h-12 text-brand-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <h3 class="text-xl font-bold text-white mb-1">Drop file to encrypt & send</h3>
                    <p class="text-sm text-slate-400" id="quota-display">Max file size: Remaining quota.</p>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-[rgba(15,23,42,0.95)] backdrop-blur-xl border-t border-slate-800 shrink-0 z-50 relative">
                <form id="chat-form" class="max-w-4xl mx-auto relative flex items-end gap-3 bg-[rgba(30,41,59,0.8)] border border-slate-700 rounded-3xl p-2 focus-within:ring-2 focus-within:ring-brand-500/50 focus-within:border-brand-500/50 transition-all duration-300 shadow-xl z-50">
                    
                    <button type="button" id="attachment-btn" class="p-3 text-slate-400 hover:text-brand-400 hover:bg-white/5 rounded-full transition-all shrink-0 focus:outline-none z-50 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    </button>
                    <input type="file" id="file-input" class="hidden">
                    
                    <div id="file-preview-indicator" class="hidden absolute -top-16 left-4 bg-dark-800/95 backdrop-blur-xl border border-slate-700 px-4 py-2 rounded-xl shadow-[0_8px_30px_rgba(0,0,0,0.6)] flex items-center gap-3 text-sm z-50 transition-all pointer-events-auto">
                        <div class="p-1.5 bg-brand-500/20 rounded-lg text-brand-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <span id="file-preview-name" class="truncate max-w-[200px] font-medium text-slate-200"></span>
                        <button type="button" id="remove-file" class="ml-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 p-1.5 rounded-md transition-colors cursor-pointer"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>

                    <textarea id="message-input" rows="1" placeholder="Type a secure message..." class="w-full bg-transparent border-none text-slate-100 placeholder-slate-500 resize-none py-3 px-2 focus:ring-0 focus:outline-none max-h-32 text-[15px] font-normal z-50 relative pointer-events-auto" style="min-height: 48px; position:relative; z-index:50;"></textarea>
                    
                    <button type="submit" id="send-btn" class="p-3 bg-brand-600 hover:bg-brand-500 text-white rounded-full transition-all shrink-0 focus:outline-none disabled:opacity-40 disabled:cursor-not-allowed shadow-[0_4px_14px_rgba(79,70,229,0.4)] hover:shadow-[0_6px_20px_rgba(79,70,229,0.6)] z-50 relative cursor-pointer">
                        <svg class="w-5 h-5 pl-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </form>
                <div class="text-center mt-3 relative z-50 pointer-events-none">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold opacity-80">Message & Files Secured with AES-256 before transit</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right-Click Context Menu -->
    <div id="msg-context-menu" class="hidden fixed z-[500] bg-dark-800 border border-slate-700 rounded-2xl shadow-2xl py-1.5 min-w-[180px]">
        <button id="ctx-copy" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-200 hover:bg-slate-700/60 transition-colors">
            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
            Copy Text
        </button>
        <button id="ctx-forward" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-200 hover:bg-slate-700/60 transition-colors">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Forward
        </button>
        <div class="border-t border-slate-700 my-1"></div>
        <button id="ctx-delete-msg" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-400 hover:bg-rose-500/10 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Delete Message
        </button>
    </div>

    <!-- Forward Message Modal -->
    <div id="forward-modal" class="hidden fixed inset-0 z-[400] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-800 border border-slate-700 rounded-3xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Forward Message
            </h3>
            <div class="relative mb-4">
                <input type="text" id="forward-search" placeholder="Search user to forward to..." class="w-full bg-dark-900 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-brand-500 outline-none">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <ul id="forward-user-list" class="max-h-48 overflow-y-auto space-y-1 mb-4"></ul>
            <button id="forward-cancel" class="w-full py-2.5 text-slate-400 hover:text-white border border-slate-700 rounded-xl text-sm transition-colors">Cancel</button>
        </div>
    </div>

    <!-- Chat Theme Modal -->
    <div id="theme-modal" class="hidden fixed inset-0 z-[400] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-800 border border-slate-700 rounded-3xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                Chat Theme
            </h3>
            <!-- Built-in Themes -->
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-3">Built-in Themes</p>
            <div class="grid grid-cols-3 gap-3 mb-6" id="theme-presets">
                <button class="theme-preset-btn h-16 rounded-2xl flex items-end p-2 text-xs font-bold text-white/80 overflow-hidden transition-all hover:scale-105 hover:ring-2 ring-brand-500" data-theme="default" style="background: linear-gradient(135deg,#1e293b,#0f172a)">Dark</button>
                <button class="theme-preset-btn h-16 rounded-2xl flex items-end p-2 text-xs font-bold text-slate-800 overflow-hidden transition-all hover:scale-105 hover:ring-2 ring-brand-500" data-theme="light" style="background: linear-gradient(135deg,#f1f5f9,#e2e8f0)">Light</button>
                <button class="theme-preset-btn h-16 rounded-2xl flex items-end p-2 text-xs font-bold text-white/80 overflow-hidden transition-all hover:scale-105 hover:ring-2 ring-brand-500" data-theme="ocean" style="background: linear-gradient(135deg,#0c4a6e,#082f49)">Ocean</button>
                <button class="theme-preset-btn h-16 rounded-2xl flex items-end p-2 text-xs font-bold text-white/80 overflow-hidden transition-all hover:scale-105 hover:ring-2 ring-brand-500" data-theme="forest" style="background: linear-gradient(135deg,#14532d,#052e16)">Forest</button>
                <button class="theme-preset-btn h-16 rounded-2xl flex items-end p-2 text-xs font-bold text-white/80 overflow-hidden transition-all hover:scale-105 hover:ring-2 ring-brand-500" data-theme="rose" style="background: linear-gradient(135deg,#881337,#4c0519)">Rose</button>
                <button class="theme-preset-btn h-16 rounded-2xl flex items-end p-2 text-xs font-bold text-white/80 overflow-hidden transition-all hover:scale-105 hover:ring-2 ring-brand-500" data-theme="aurora" style="background: linear-gradient(135deg,#312e81,#0f172a)">Aurora</button>
            </div>
            <!-- Custom Color -->
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-3">Custom Background</p>
            <div class="flex items-center gap-3 mb-6">
                <input type="color" id="custom-bg-color" value="#0f172a" class="w-12 h-10 rounded-xl cursor-pointer border-none bg-transparent">
                <input type="color" id="custom-bubble-color" value="#4f46e5" class="w-12 h-10 rounded-xl cursor-pointer border-none bg-transparent">
                <div>
                    <p class="text-sm text-white font-medium">Custom Colors</p>
                    <p class="text-xs text-slate-500">Background · Bubble</p>
                </div>
                <button id="apply-custom-theme" class="ml-auto px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold rounded-xl transition-colors">Apply</button>
            </div>
            <button id="theme-close" class="w-full py-2.5 text-slate-400 hover:text-white border border-slate-700 rounded-xl text-sm transition-colors">Close</button>
        </div>
    </div>

    <!-- User Profile Modal -->
    <div id="user-profile-modal" class="hidden fixed inset-0 z-[400] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-800 border border-slate-700 rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl relative">
            <button id="close-profile-modal" class="absolute top-4 right-4 p-2 bg-black/40 hover:bg-black/60 text-white rounded-full transition-colors z-10 backdrop-blur-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <div id="modal-cover-pic" class="h-32 bg-gradient-to-r from-brand-600 to-indigo-800 bg-cover bg-center"></div>
            
            <div class="px-6 pb-6 relative">
                <div class="flex justify-between items-end -mt-12 mb-4">
                    <div id="modal-avatar-pic" class="w-24 h-24 rounded-full border-4 border-dark-800 bg-dark-700 text-white flex items-center justify-center text-3xl font-bold uppercase shadow-lg bg-cover bg-center">
                        <span id="modal-avatar-initial">U</span>
                    </div>
                </div>
                
                <h3 id="modal-profile-name" class="text-xl font-bold text-white mb-1">User Name</h3>
                <p id="modal-profile-status" class="text-sm text-brand-400 font-medium mb-4"></p>
                
                <div class="space-y-4">
                    <div class="bg-dark-900/50 rounded-xl p-4 border border-slate-700/50">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">About</p>
                        <p id="modal-profile-about" class="text-sm text-slate-300 leading-relaxed min-h-[40px]">This user hasn't added a description yet.</p>
                    </div>

                    <div class="bg-dark-900/50 rounded-xl p-4 border border-slate-700/50 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Email</p>
                            <p id="modal-profile-email" class="text-sm text-slate-200 truncate" title="N/A">N/A</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Phone</p>
                            <p id="modal-profile-phone" class="text-sm text-slate-200 truncate" title="N/A">N/A</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Member Since</p>
                            <p id="modal-profile-join" class="text-sm text-slate-200">N/A</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 pt-2">
                        <button onclick="openMediaGallery('conv', activeConvId)" class="w-full flex items-center justify-center gap-2 py-2.5 bg-brand-600/10 hover:bg-brand-600/20 text-brand-400 border border-brand-500/30 rounded-xl text-sm font-medium transition-colors">📁 Shared Media</button>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <button id="modal-action-block" class="w-full flex items-center justify-center gap-2 py-2.5 bg-dark-900 hover:bg-rose-500/10 text-rose-400 border border-slate-700/50 hover:border-rose-500/30 rounded-xl text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            Block
                        </button>
                        <button id="modal-action-report" class="w-full flex items-center justify-center gap-2 py-2.5 bg-dark-900 hover:bg-amber-500/10 text-amber-400 border border-slate-700/50 hover:border-amber-500/30 rounded-xl text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Group Modal -->
    <div id="create-group-modal" class="hidden fixed inset-0 z-[400] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-800 border border-slate-700 rounded-3xl p-6 w-full max-w-lg shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <button id="close-create-group-modal" class="absolute top-4 right-4 p-2 bg-dark-900 border border-slate-700 hover:bg-slate-700 text-slate-400 hover:text-white rounded-full transition-colors z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-600 to-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                Create New Group
            </h3>
            
            <form id="create-group-form" class="space-y-4">
                <!-- Group Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Group Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="new-group-name" required placeholder="e.g. Project Alpha" class="w-full bg-dark-900 border border-slate-700 rounded-xl py-2.5 px-4 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-brand-500 hover:border-slate-600 outline-none transition-all">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Description</label>
                    <textarea id="new-group-desc" rows="2" placeholder="What is this group about?" class="w-full bg-dark-900 border border-slate-700 rounded-xl py-2.5 px-4 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-brand-500 hover:border-slate-600 outline-none transition-all resize-none"></textarea>
                </div>

                <!-- Add Members -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Add Members</label>
                    <div class="relative">
                        <input type="text" id="group-member-search" placeholder="Search users to add..." class="w-full bg-dark-900 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-brand-500 hover:border-slate-600 outline-none transition-all">
                        <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <!-- Search results dropdown -->
                    <ul id="group-member-results" class="hidden mt-1 bg-dark-900 border border-slate-700 rounded-xl max-h-36 overflow-y-auto divide-y divide-slate-700/50"></ul>
                    <!-- Selected members chips -->
                    <div id="selected-members" class="flex flex-wrap gap-2 mt-2"></div>
                </div>

                <!-- Settings Row -->
                <div class="space-y-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest ml-1">Group Settings</p>
                    
                    <!-- Private Group Toggle -->
                    <label for="new-group-private" class="flex items-center gap-3 p-3 border border-slate-700/50 bg-dark-900/50 rounded-xl cursor-pointer hover:border-slate-600 transition-colors">
                        <div class="shrink-0 relative">
                            <input type="checkbox" id="new-group-private" class="peer sr-only">
                            <div class="w-10 h-6 bg-slate-700 rounded-full peer-checked:bg-brand-500 transition-colors duration-300 relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-300 peer-checked:after:translate-x-4"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Private Group</p>
                            <p class="text-[11px] text-slate-400 leading-tight">Only invited members can find and join.</p>
                        </div>
                    </label>

                    <!-- Admin Only Messaging Toggle -->
                    <label for="new-group-admin-only" class="flex items-center gap-3 p-3 border border-slate-700/50 bg-dark-900/50 rounded-xl cursor-pointer hover:border-slate-600 transition-colors">
                        <div class="shrink-0 relative">
                            <input type="checkbox" id="new-group-admin-only" class="peer sr-only">
                            <div class="w-10 h-6 bg-slate-700 rounded-full peer-checked:bg-brand-500 transition-colors duration-300 relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-300 peer-checked:after:translate-x-4"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Admin-Only Messaging</p>
                            <p class="text-[11px] text-slate-400 leading-tight">Only admins can send messages. Members can read only.</p>
                        </div>
                    </label>
                </div>

                <!-- Actions -->
                <div class="pt-4 flex gap-3">
                    <button type="button" id="cancel-create-group" class="flex-1 py-2.5 px-4 rounded-xl text-sm font-medium border border-slate-700 text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl text-sm font-bold bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white shadow-lg shadow-brand-500/20 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Group
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Group Info Panel (Slide-out) -->
    <div id="group-info-panel" class="hidden fixed inset-0 z-[350] bg-black/50 backdrop-blur-sm">
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-dark-800 border-l border-slate-700 shadow-2xl flex flex-col overflow-hidden" style="animation: slideInRight 0.3s ease-out;">
            <!-- Panel Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-700/50 bg-dark-900/60 shrink-0">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Group Info
                </h3>
                <button id="close-group-info" class="p-2 hover:bg-slate-700 text-slate-400 hover:text-white rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto">
                <!-- Group Header with Cover -->
                <div class="relative border-b border-slate-700/50">
                    <div id="gi-cover" class="h-28 bg-gradient-to-br from-brand-900/50 to-indigo-900/50 bg-cover bg-center relative">
                        <input type="file" id="gi-cover-input" class="hidden" accept="image/*" onchange="window.uploadGroupCover(this)">
                        <button id="gi-cover-upload-btn" class="hidden absolute bottom-2 right-2 p-2 rounded-lg bg-black/60 text-white hover:bg-black/80 transition-colors" title="Change cover" onclick="document.getElementById('gi-cover-input').click()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </button>
                    </div>
                    <div class="px-6 pb-4 -mt-10 text-center relative">
                        <div class="relative inline-block">
                            <div id="gi-avatar" class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-brand-600 to-indigo-600 flex items-center justify-center text-3xl font-bold text-white shadow-lg shadow-brand-500/20 border-4 border-dark-800 bg-cover bg-center"></div>
                            <input type="file" id="gi-avatar-input" class="hidden" accept="image/*" onchange="window.uploadGroupAvatar(this)">
                            <button id="gi-avatar-upload-btn" class="hidden absolute -bottom-1 -right-1 p-1.5 rounded-full bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-lg" title="Change avatar" onclick="document.getElementById('gi-avatar-input').click()">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </button>
                        </div>
                        <h4 id="gi-name" class="text-xl font-bold text-white mt-2"></h4>
                        <p id="gi-desc" class="text-sm text-slate-400 mt-1"></p>
                        <div class="flex items-center justify-center gap-3 mt-3 text-xs text-slate-500 flex-wrap">
                            <span id="gi-member-count" class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> 0 members</span>
                            <span id="gi-privacy-badge" class="px-2 py-0.5 rounded-full bg-slate-700/50 text-slate-400"></span>
                            <span id="gi-status-badge" class="px-2 py-0.5 rounded-full text-[10px] font-semibold"></span>
                        </div>
                    </div>
                </div>
                <!-- Upload Buttons (admin/owner only) -->
                <div id="gi-upload-actions" class="hidden p-3 border-b border-slate-700/50 flex flex-wrap items-center justify-center gap-2">
                    <button onclick="document.getElementById('gi-avatar-input').click()" class="px-3 py-1.5 rounded-xl text-[11px] font-semibold bg-brand-600/20 text-brand-300 hover:bg-brand-600/40 transition-colors border border-brand-500/30">📷 Change Avatar</button>
                    <button onclick="document.getElementById('gi-cover-input').click()" class="px-3 py-1.5 rounded-xl text-[11px] font-semibold bg-brand-600/20 text-brand-300 hover:bg-brand-600/40 transition-colors border border-brand-500/30">🖼️ Change Cover</button>
                    <button onclick="removeGroupAvatar()" class="px-3 py-1.5 rounded-xl text-[11px] font-semibold bg-rose-600/20 text-rose-300 hover:bg-rose-600/40 transition-colors border border-rose-500/30">🗑️ Remove Avatar</button>
                    <button onclick="removeGroupCover()" class="px-3 py-1.5 rounded-xl text-[11px] font-semibold bg-rose-600/20 text-rose-300 hover:bg-rose-600/40 transition-colors border border-rose-500/30">🗑️ Remove Cover</button>
                    <button onclick="openMediaGallery('group', currentGroupId)" class="px-3 py-1.5 rounded-xl text-[11px] font-semibold bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/40 transition-colors border border-emerald-500/30">📁 Shared Media</button>
                </div>
                <!-- Subgroups / Channels -->
                <div class="p-4 border-b border-slate-700/50">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Channels</h5>
                        <button id="gi-add-subgroup-toggle" class="hidden text-brand-400 hover:text-brand-300 transition-colors p-1" title="New Channel"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
                    </div>
                    <div id="gi-add-subgroup-form" class="hidden mb-3 flex gap-2">
                        <input type="text" id="gi-new-subgroup-name" placeholder="Channel name..." class="flex-1 bg-dark-900 border border-slate-700 rounded-lg py-1.5 px-3 text-sm text-white placeholder-slate-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        <button id="gi-create-subgroup-btn" class="px-3 py-1.5 bg-brand-600 hover:bg-brand-500 text-white text-xs font-semibold rounded-lg transition-colors">Create</button>
                    </div>
                    <div id="gi-subgroups-list" class="space-y-1"><div class="text-sm text-slate-500 text-center py-2">Loading...</div></div>
                </div>
                <!-- Settings (admin/owner only) -->
                <div id="gi-permissions-section" class="hidden p-4 border-b border-slate-700/50 space-y-4">
                    <h5 class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Group Settings</h5>
                    <!-- Group Visibility -->
                    <div>
                        <label class="text-xs text-slate-400 mb-1 block">Group Visibility</label>
                        <select id="gi-group-visibility" class="w-full bg-dark-900 border border-slate-700 rounded-xl py-2 px-3 text-sm text-white focus:ring-1 focus:ring-brand-500 outline-none">
                            <option value="0">Public (visible in search)</option>
                            <option value="1">Private (hidden from search)</option>
                        </select>
                    </div>
                    <!-- Group Status -->
                    <div>
                        <label class="text-xs text-slate-400 mb-1 block">Group Status</label>
                        <select id="gi-group-status" class="w-full bg-dark-900 border border-slate-700 rounded-xl py-2 px-3 text-sm text-white focus:ring-1 focus:ring-brand-500 outline-none">
                            <option value="open">Open</option>
                            <option value="closed">Closed (No new members)</option>
                        </select>
                    </div>
                    <!-- Join Mode -->
                    <div id="gi-join-mode-wrap">
                        <label class="text-xs text-slate-400 mb-1 block">Join Mode</label>
                        <select id="gi-join-mode" class="w-full bg-dark-900 border border-slate-700 rounded-xl py-2 px-3 text-sm text-white focus:ring-1 focus:ring-brand-500 outline-none">
                            <option value="free">Free to Join</option>
                            <option value="approval">Requires Admin Approval</option>
                        </select>
                    </div>
                    <!-- Chat Permission -->
                    <div>
                        <label class="text-xs text-slate-400 mb-1 block">Who can send messages?</label>
                        <select id="gi-chat-permission" class="w-full bg-dark-900 border border-slate-700 rounded-xl py-2 px-3 text-sm text-white focus:ring-1 focus:ring-brand-500 outline-none">
                            <option value="member">Everyone (All Members)</option>
                            <option value="elder">Elders & Admins Only</option>
                            <option value="admin">Admins Only</option>
                        </select>
                    </div>
                    <!-- Elder Quotas -->
                    <div class="bg-dark-900/50 rounded-xl p-3 border border-slate-700/50">
                        <p class="text-xs font-semibold text-slate-400 mb-2">Elder Messaging Limits</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-[10px] text-slate-500 block mb-0.5">Messages / Hour</label>
                                <input type="number" id="gi-elder-msg-hr" min="1" max="999" value="15" class="w-full bg-dark-800 border border-slate-700 rounded-lg py-1.5 px-2 text-sm text-white outline-none focus:ring-1 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500 block mb-0.5">Max Chars / Msg</label>
                                <input type="number" id="gi-elder-max-chars" min="10" max="5000" value="200" class="w-full bg-dark-800 border border-slate-700 rounded-lg py-1.5 px-2 text-sm text-white outline-none focus:ring-1 focus:ring-brand-500">
                            </div>
                        </div>
                    </div>
                    <button id="gi-save-permission-btn" class="w-full py-2 rounded-xl text-xs font-semibold bg-brand-600/20 text-brand-300 hover:bg-brand-600/40 transition-colors border border-brand-500/30">Save Settings</button>
                </div>
                <!-- Pending Messages (admin) -->
                <div id="gi-pending-section" class="hidden p-4 border-b border-slate-700/50">
                    <h5 class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-3">Pending Approval</h5>
                    <div id="gi-pending-list" class="space-y-2"><div class="text-sm text-slate-500 text-center py-2">None</div></div>
                </div>
                <!-- Join Requests (admin) -->
                <div id="gi-join-requests-section" class="hidden p-4 border-b border-slate-700/50">
                    <h5 class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-3">Join Requests</h5>
                    <div id="gi-join-requests-list" class="space-y-2"><div class="text-sm text-slate-500 text-center py-2">None</div></div>
                </div>
                <!-- Members -->
                <div class="p-4 border-b border-slate-700/50">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Members</h5>
                    </div>
                    <div id="gi-add-member-section" class="hidden mb-3">
                        <div class="relative">
                            <input type="text" id="gi-add-member-search" placeholder="Search users to add..." class="w-full bg-dark-900 border border-slate-700 rounded-xl py-2 pl-9 pr-4 text-sm text-white placeholder-slate-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <ul id="gi-add-member-results" class="hidden mt-1 bg-dark-900 border border-slate-700 rounded-xl max-h-28 overflow-y-auto divide-y divide-slate-700/50"></ul>
                    </div>
                    <!-- Invite via Link -->
                    <div id="gi-invite-link-section" class="hidden mb-3">
                        <div class="flex gap-2">
                            <input type="text" id="gi-invite-link-display" readonly class="flex-1 bg-dark-900 border border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-400 outline-none truncate" placeholder="Generate invite link...">
                            <button id="gi-copy-invite-btn" onclick="window.copyInviteLink()" class="px-3 py-2 bg-brand-600/20 text-brand-300 hover:bg-brand-600/40 text-xs font-semibold rounded-xl transition-colors border border-brand-500/30 whitespace-nowrap">Copy Link</button>
                        </div>
                        <button id="gi-generate-invite-btn" onclick="window.generateInviteLink()" class="mt-2 w-full py-1.5 rounded-xl text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition-colors border border-emerald-500/20">🔗 Generate New Invite Link</button>
                    </div>
                    <div id="gi-members-list" class="space-y-1 max-h-80 overflow-y-auto"><div class="text-sm text-slate-500 text-center py-4">Loading...</div></div>
                </div>
                <!-- Danger Zone (owner only) -->
                <div id="gi-danger-zone" class="hidden p-4">
                    <h5 class="text-xs font-semibold text-rose-500 uppercase tracking-widest mb-3">Danger Zone</h5>
                    <button id="gi-delete-group-btn" class="w-full py-2.5 rounded-xl text-sm font-semibold bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/30 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete Group Permanently
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- File Viewer Lightbox UI -->
    <div id="file-lightbox" class="fixed inset-0 z-[200] hidden bg-dark-900/95 backdrop-blur-3xl flex flex-col items-center justify-center animate-fade-in-up">
        <!-- Header -->
        <div class="absolute top-0 w-full p-4 flex justify-between items-center bg-dark-900/50">
            <div class="flex items-center gap-3 text-slate-200">
                <svg class="w-6 h-6 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                <span id="lightbox-filename" class="font-medium text-lg truncate max-w-sm md:max-w-xl"></span>
            </div>
            <div class="flex gap-2">
                <a id="lightbox-download-btn" href="#" download class="p-2 bg-dark-800 hover:bg-dark-700 text-white rounded-full transition-colors" title="Download Original">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </a>
                <button id="close-lightbox" class="p-2 bg-dark-800 hover:bg-rose-500/20 hover:text-rose-400 text-slate-300 rounded-full transition-colors" title="Close">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div id="lightbox-content" class="w-full h-full flex items-center justify-center p-8 mt-12 mb-4 overflow-hidden">
            <!-- Loading indicator -->
            <div id="lightbox-loader" class="flex flex-col items-center justify-center gap-4">
                <div class="w-12 h-12 border-4 border-brand-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-brand-300 font-medium">Decrypting...</p>
            </div>
            <!-- Injected Media goes here -->
        </div>
    </div>

    <!-- Client-Side Javascript Logic -->
    <script>
        const currentUserId = <?= $_SESSION['user_id'] ?>;

        // ── Lightweight Markdown → HTML renderer (for bot messages only) ──
        function renderMarkdown(text) {
            // Escape HTML first to prevent XSS
            let html = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

            // Code blocks (```...```)
            html = html.replace(/```(\w*)\n?([\s\S]*?)```/g, '<pre class="bot-code-block"><code>$2</code></pre>');

            // Bold (**text** or __text__)
            html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            html = html.replace(/__(.+?)__/g, '<strong>$1</strong>');

            // Italic (*text* or _text_)
            html = html.replace(/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/g, '<em>$1</em>');

            // Inline code (`text`)
            html = html.replace(/`([^`]+)`/g, '<code class="bot-inline-code">$1</code>');

            // Headers (# text, ## text, ### text)
            html = html.replace(/^### (.+)$/gm, '<h4 class="bot-h4">$1</h4>');
            html = html.replace(/^## (.+)$/gm, '<h3 class="bot-h3">$1</h3>');
            html = html.replace(/^# (.+)$/gm, '<h2 class="bot-h2">$1</h2>');

            // Horizontal rule (--- or ***)
            html = html.replace(/^(-{3,}|\*{3,})$/gm, '<hr class="bot-hr">');

            // Numbered lists (1. item, 2. item)
            html = html.replace(/^\d+\.\s+(.+)$/gm, '<li class="bot-ol-item">$1</li>');
            html = html.replace(/((?:<li class="bot-ol-item">.*<\/li>\n?)+)/g, '<ol class="bot-ol">$1</ol>');

            // Bullet lists (- item or * item)
            html = html.replace(/^[-*]\s+(.+)$/gm, '<li class="bot-ul-item">$1</li>');
            html = html.replace(/((?:<li class="bot-ul-item">.*<\/li>\n?)+)/g, '<ul class="bot-ul">$1</ul>');

            // Line breaks (preserve double newlines as paragraphs, single as <br>)
            html = html.replace(/\n\n/g, '</p><p class="bot-para">');
            html = html.replace(/\n/g, '<br>');

            // Wrap in paragraph if not already wrapped
            if (!html.startsWith('<')) {
                html = '<p class="bot-para">' + html + '</p>';
            }

            return html;
        }
    </script>

    <!-- Bot Markdown Styles -->
    <style>
        .bot-markdown { font-size: 15px; line-height: 1.7; color: #e2e8f0; }
        .bot-markdown strong { font-weight: 700; color: #fff; }
        .bot-markdown em { font-style: italic; color: #cbd5e1; }
        .bot-markdown .bot-h2 { font-size: 1.25rem; font-weight: 700; color: #fff; margin: 12px 0 6px; border-bottom: 1px solid rgba(148,163,184,0.2); padding-bottom: 4px; }
        .bot-markdown .bot-h3 { font-size: 1.1rem; font-weight: 700; color: #f1f5f9; margin: 10px 0 4px; }
        .bot-markdown .bot-h4 { font-size: 1rem; font-weight: 600; color: #e2e8f0; margin: 8px 0 4px; }
        .bot-markdown .bot-ol { list-style-type: decimal; padding-left: 20px; margin: 8px 0; }
        .bot-markdown .bot-ul { list-style-type: disc; padding-left: 20px; margin: 8px 0; }
        .bot-markdown .bot-ol-item, .bot-markdown .bot-ul-item { margin: 4px 0; padding-left: 4px; }
        .bot-markdown .bot-inline-code { background: rgba(99,102,241,0.2); color: #a5b4fc; padding: 2px 6px; border-radius: 4px; font-family: 'Fira Code', 'Cascadia Code', monospace; font-size: 0.85em; }
        .bot-markdown .bot-code-block { background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 12px 16px; margin: 8px 0; overflow-x: auto; font-family: 'Fira Code', 'Cascadia Code', monospace; font-size: 0.85em; color: #a5b4fc; white-space: pre-wrap; }
        .bot-markdown .bot-code-block code { background: none; padding: 0; color: inherit; }
        .bot-markdown .bot-hr { border: none; border-top: 1px solid rgba(148,163,184,0.2); margin: 12px 0; }
        .bot-markdown .bot-para { margin: 4px 0; }
        .bot-markdown p:empty { display: none; }
    </style>

    <script>
        
        let activeConvId = null;
        let activeUid = null;
        let activeIsGroup = false;
        let lastMessageId = 0;
        let pollingInterval = null;
        let ctxMsgId = null;
        let ctxMsgText = '';

        // DOM Elements
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const fileInput = document.getElementById('file-input');
        const messagesContainer = document.getElementById('messages-container');
        const searchInput = document.getElementById('global-search');
        const searchResults = document.getElementById('search-results');
        const contextMenu = document.getElementById('msg-context-menu');
        const forwardModal = document.getElementById('forward-modal');
        const themeModal = document.getElementById('theme-modal');
        const userProfileModal = document.getElementById('user-profile-modal');

        // Ã¢â€â‚¬Ã¢â€â‚¬ THEME SYSTEM Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        const themes = {
            default: { bg: '#0b0f19', bubble: '#4f46e5' },
            light:   { bg: '#f1f5f9', bubble: '#6366f1' },
            ocean:   { bg: '#082f49', bubble: '#0284c7' },
            forest:  { bg: '#052e16', bubble: '#16a34a' },
            rose:    { bg: '#4c0519', bubble: '#e11d48' },
            aurora:  { bg: '#0f172a', bubble: '#8b5cf6' },
        };

        function applyTheme(name, customBg, customBubble) {
            const t = name === 'custom' ? { bg: customBg, bubble: customBubble } : (themes[name] || themes.default);
            const msgArea = document.getElementById('messages-container');
            if (msgArea) msgArea.style.background = t.bg;
            document.documentElement.style.setProperty('--chat-bubble-color', t.bubble);
            document.querySelector('.msg-sent') && null; // trigger recalculation
            // Inject/update CSS variable for bubble color
            let style = document.getElementById('theme-style');
            if (!style) { style = document.createElement('style'); style.id = 'theme-style'; document.head.appendChild(style); }
            style.textContent = `.msg-sent { background: linear-gradient(135deg, ${t.bubble}cc 0%, ${t.bubble} 100%) !important; }`;
            localStorage.setItem('chat_theme', name);
            if (name === 'custom') { localStorage.setItem('chat_bg', t.bg); localStorage.setItem('chat_bubble', t.bubble); }
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('chat_theme') || 'default';
        if (savedTheme === 'custom') {
            applyTheme('custom', localStorage.getItem('chat_bg') || '#0f172a', localStorage.getItem('chat_bubble') || '#4f46e5');
        } else {
            applyTheme(savedTheme);
        }

        // Theme modal
        document.getElementById('chat-theme-btn').addEventListener('click', () => themeModal.classList.remove('hidden'));
        document.getElementById('theme-close').addEventListener('click', () => themeModal.classList.add('hidden'));
        document.querySelectorAll('.theme-preset-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                applyTheme(btn.dataset.theme);
                fetch('/api/user/preferences', { method: 'POST', body: new URLSearchParams({ chat_theme: btn.dataset.theme }) });
                themeModal.classList.add('hidden');
            });
        });
        document.getElementById('apply-custom-theme').addEventListener('click', () => {
            const bg = document.getElementById('custom-bg-color').value;
            const bubble = document.getElementById('custom-bubble-color').value;
            applyTheme('custom', bg, bubble);
            fetch('/api/user/preferences', { method: 'POST', body: new URLSearchParams({ chat_theme: 'custom', chat_bg_color: bg, chat_bubble_color: bubble }) });
            themeModal.classList.add('hidden');
        });
        themeModal.addEventListener('mousedown', e => { if (e.target === themeModal) themeModal.classList.add('hidden'); });

        // ────── CREATE GROUP MODAL ─────────────────────────────────────────────────────────────
        const createGroupBtn = document.getElementById('create-group-btn');
        const createGroupModal = document.getElementById('create-group-modal');
        const closeCreateGroupBtn = document.getElementById('close-create-group-modal');
        const cancelCreateGroupBtn = document.getElementById('cancel-create-group');
        const createGroupForm = document.getElementById('create-group-form');

        const openGroupModal = () => createGroupModal.classList.remove('hidden');
        const closeGroupModal = () => createGroupModal.classList.add('hidden');

        if (createGroupBtn) createGroupBtn.addEventListener('click', openGroupModal);
        if (closeCreateGroupBtn) closeCreateGroupBtn.addEventListener('click', closeGroupModal);
        if (cancelCreateGroupBtn) cancelCreateGroupBtn.addEventListener('click', closeGroupModal);

        createGroupModal.addEventListener('mousedown', (e) => {
            if (e.target === createGroupModal) closeGroupModal();
        });

        // ── Group Member Search Logic ──
        const groupMemberSearch = document.getElementById('group-member-search');
        const groupMemberResults = document.getElementById('group-member-results');
        const selectedMembersContainer = document.getElementById('selected-members');
        let selectedMembers = []; // [{id, username}]
        let memberSearchTimeout = null;

        if (groupMemberSearch) {
            groupMemberSearch.addEventListener('input', (e) => {
                clearTimeout(memberSearchTimeout);
                const q = e.target.value.trim();
                if (q.length < 2) { groupMemberResults.classList.add('hidden'); return; }
                memberSearchTimeout = setTimeout(() => {
                    fetch(`/api/users/search?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(data => {
                            groupMemberResults.innerHTML = '';
                            const users = (data.users || []).filter(u => !u.is_group && !selectedMembers.find(m => m.id == u.id));
                            if (users.length > 0) {
                                users.forEach(u => {
                                    const li = document.createElement('li');
                                    li.innerHTML = `<button type="button" class="w-full text-left p-2.5 hover:bg-slate-800 flex items-center gap-3 transition-colors add-member-btn" data-uid="${u.id}" data-name="${u.username}">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-600 to-rose-400 flex items-center justify-center font-bold text-white text-xs">${u.username.charAt(0).toUpperCase()}</div>
                                        <span class="text-sm text-slate-200">@${u.username}</span>
                                    </button>`;
                                    li.querySelector('.add-member-btn').addEventListener('click', () => {
                                        addMember(u.id, u.username);
                                        groupMemberSearch.value = '';
                                        groupMemberResults.classList.add('hidden');
                                    });
                                    groupMemberResults.appendChild(li);
                                });
                                groupMemberResults.classList.remove('hidden');
                            } else {
                                groupMemberResults.innerHTML = '<li class="p-2.5 text-sm text-slate-500 text-center">No users found</li>';
                                groupMemberResults.classList.remove('hidden');
                            }
                        });
                }, 300);
            });
        }

        function addMember(id, username) {
            if (selectedMembers.find(m => m.id == id)) return;
            selectedMembers.push({ id, username });
            renderSelectedMembers();
        }

        function removeMember(id) {
            selectedMembers = selectedMembers.filter(m => m.id != id);
            renderSelectedMembers();
        }

        function renderSelectedMembers() {
            selectedMembersContainer.innerHTML = '';
            selectedMembers.forEach(m => {
                const chip = document.createElement('span');
                chip.className = 'inline-flex items-center gap-1.5 bg-brand-500/20 text-brand-300 text-xs font-medium px-2.5 py-1 rounded-full border border-brand-500/30';
                chip.innerHTML = `@${m.username} <button type="button" class="hover:text-rose-400 transition-colors" data-remove-id="${m.id}">&times;</button>`;
                chip.querySelector('button').addEventListener('click', () => removeMember(m.id));
                selectedMembersContainer.appendChild(chip);
            });
        }

        if (createGroupForm) {
            createGroupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const name = document.getElementById('new-group-name').value.trim();
                const desc = document.getElementById('new-group-desc').value.trim();
                const isPrivate = document.getElementById('new-group-private').checked ? 1 : 0;
                const adminOnly = document.getElementById('new-group-admin-only').checked ? 1 : 0;

                if (!name) return showToast('Group name is required.', 'error');

                const submitBtn = createGroupForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Creating...';
                submitBtn.disabled = true;

                const params = new URLSearchParams({ name, description: desc, is_private: isPrivate, admin_only: adminOnly });
                selectedMembers.forEach(m => params.append('member_ids[]', m.id));

                fetch('/api/groups/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: params
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Group created successfully!');
                        setTimeout(() => location.reload(), 800);
                    } else {
                        showToast(data.error || 'Failed to create group.', 'error');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(err => {
                    showToast('A network error occurred.', 'error');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }

        // ────── IN-CHAT SEARCH ─────────────────────────────────────────────────────────────
        const inChatSearchBtn = document.getElementById('in-chat-search-btn');
        const inChatSearchBar = document.getElementById('in-chat-search-bar');
        const inChatInput = document.getElementById('in-chat-search-input');
        const searchMatchCount = document.getElementById('search-match-count');

        inChatSearchBtn.addEventListener('click', () => {
            inChatSearchBar.classList.toggle('hidden');
            if (!inChatSearchBar.classList.contains('hidden')) inChatInput.focus();
        });

        inChatInput.addEventListener('input', () => {
            const q = inChatInput.value.trim().toLowerCase();
            // Remove old highlights
            messagesContainer.querySelectorAll('.search-highlight').forEach(el => {
                const parent = el.parentNode;
                parent.replaceChild(document.createTextNode(el.textContent), el);
                parent.normalize();
            });
            if (!q) { searchMatchCount.textContent = ''; return; }
            let count = 0;
            messagesContainer.querySelectorAll('p').forEach(p => {
                if (p.textContent.toLowerCase().includes(q)) {
                    const regex = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    p.innerHTML = p.innerHTML.replace(regex, '<mark class="search-highlight bg-amber-400/40 text-amber-200 rounded px-0.5">$1</mark>');
                    count++;
                }
            });
            searchMatchCount.textContent = count > 0 ? `${count} found` : 'Not found';
            // Scroll to first match
            const first = messagesContainer.querySelector('.search-highlight');
            if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ CONTEXT MENU Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        function closeContextMenu() { contextMenu.classList.add('hidden'); }
        document.addEventListener('click', closeContextMenu);
        document.addEventListener('keydown', e => { 
            if (e.key === 'Escape') { 
                closeContextMenu(); 
                forwardModal.classList.add('hidden'); 
                themeModal.classList.add('hidden'); 
                userProfileModal.classList.add('hidden');
            } 
        });

        document.getElementById('ctx-copy').addEventListener('click', () => {
            if (ctxMsgText) navigator.clipboard.writeText(ctxMsgText).then(() => showToast('Copied!'));
            closeContextMenu();
        });

        document.getElementById('ctx-forward').addEventListener('click', () => {
            closeContextMenu();
            forwardModal.classList.remove('hidden');
            loadForwardUsers('');
        });

        document.getElementById('ctx-delete-msg').addEventListener('click', () => {
            closeContextMenu();
            // Remove from DOM instantly (soft delete for UI)
            const el = document.querySelector(`[data-msg-id="${ctxMsgId}"]`);
            if (el) el.remove();
            showToast('Message deleted locally.');
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ MORE MENU (Block / Delete Conversation) Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        const moreBtn = document.getElementById('chat-more-btn');
        const moreDropdown = document.getElementById('chat-more-dropdown');
        moreBtn.addEventListener('click', (e) => { e.stopPropagation(); moreDropdown.classList.toggle('hidden'); });
        document.addEventListener('click', () => moreDropdown.classList.add('hidden'));

        document.getElementById('delete-conv-btn').addEventListener('click', () => {
            if (!activeConvId) return;
            if (!confirm('Delete this entire conversation? This cannot be undone.')) return;
            fetch('/api/chat/delete-conversation', { method: 'POST', body: new URLSearchParams({ conversation_id: activeConvId }) })
                .then(r => r.json()).then(d => {
                    if (d.success) { location.reload(); }
                    else showToast('Error deleting conversation.');
                });
        });

        document.getElementById('block-user-btn').addEventListener('click', () => {
            if (!activeUid) return;
            const name = document.getElementById('active-name').textContent;
            if (!confirm(`Block @${name}? They will be removed from your chat list.`)) return;
            fetch('/api/chat/block', { method: 'POST', body: new URLSearchParams({ blocked_id: activeUid }) })
                .then(r => r.json()).then(d => {
                    if (d.success) {
                        showToast(`@${name} has been blocked.`);
                        // Hide their contact entry
                        document.querySelector(`.contact-item[data-uid="${activeUid}"]`)?.remove();
                        // Reset chat area
                        document.getElementById('chat-placeholder').classList.remove('hidden');
                        document.getElementById('active-chat-wrapper').classList.add('hidden');
                        activeConvId = null; activeUid = null;
                        if (pollingInterval) clearInterval(pollingInterval);
                    }
                });
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ FORWARD MODAL Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        const forwardSearch = document.getElementById('forward-search');
        const forwardUserList = document.getElementById('forward-user-list');
        let fwdSearchTimeout;

        function loadForwardUsers(q) {
            fetch(`/api/users/search?q=${encodeURIComponent(q || 'a')}`)
                .then(r => r.json()).then(data => {
                    forwardUserList.innerHTML = '';
                    (data.users || []).slice(0, 8).forEach(u => {
                        const li = document.createElement('li');
                        li.innerHTML = `<button class="fwd-to-btn w-full text-left px-4 py-2.5 hover:bg-slate-700/60 rounded-xl text-sm text-slate-200 flex items-center gap-3 transition-colors" data-uid="${u.id}">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-600 to-rose-400 flex items-center justify-center font-bold text-white text-xs">${u.username.charAt(0).toUpperCase()}</div>
                            @${u.username}
                        </button>`;
                        forwardUserList.appendChild(li);
                    });
                });
        }

        forwardSearch.addEventListener('input', () => {
            clearTimeout(fwdSearchTimeout);
            fwdSearchTimeout = setTimeout(() => loadForwardUsers(forwardSearch.value.trim()), 300);
        });

        forwardUserList.addEventListener('click', e => {
            const btn = e.target.closest('.fwd-to-btn');
            if (!btn || !ctxMsgId) return;
            const toUid = btn.dataset.uid;
            fetch('/api/chat/forward', { method: 'POST', body: new URLSearchParams({ message_id: ctxMsgId, to_user_id: toUid }) })
                .then(r => r.json()).then(d => {
                    forwardModal.classList.add('hidden');
                    if (d.success) showToast('Message forwarded!');
                    else showToast('Failed to forward.');
                });
        });

        document.getElementById('forward-cancel').addEventListener('click', () => forwardModal.classList.add('hidden'));
        forwardModal.addEventListener('mousedown', e => { if (e.target === forwardModal) forwardModal.classList.add('hidden'); });

        // Ã¢â€â‚¬Ã¢â€â‚¬ TOAST NOTIFICATIONS Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        function showToast(msg, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-6 left-1/2 -translate-x-1/2 z-[999] px-5 py-3 rounded-2xl text-sm font-semibold shadow-2xl transition-all duration-300 ${
                type === 'error' ? 'bg-rose-500 text-white' : 'bg-emerald-500 text-white'
            }`;
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 2500);
        }

        // Auto-resize textarea
        messageInput.addEventListener('input', function() {
            this.style.height = '44px';
            this.style.height = (this.scrollHeight) + 'px';
            document.getElementById('send-btn').disabled = !(this.value.trim() || fileInput.files.length > 0);
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ GLOBAL USER SEARCH Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        let searchTimeout = null;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            if (query.length < 2) { searchResults.classList.add('hidden'); return; }
            searchTimeout = setTimeout(() => {
                fetch(`/api/users/search?q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.users && data.users.length > 0) {
                            data.users.forEach(u => {
                                const isGroup = u.is_group ? true : false;
                                const li = document.createElement('li');
                                li.innerHTML = `<button class="w-full text-left p-3 hover:bg-slate-800 flex items-center gap-3 transition-colors start-chat-btn" data-uid="${u.id}" data-name="${u.username}" data-is-group="${isGroup}">
                                    <div class="w-9 h-9 rounded-full ${isGroup ? 'bg-gradient-to-br from-brand-600 to-indigo-600' : 'bg-gradient-to-tr from-brand-600 to-rose-400'} flex items-center justify-center font-bold text-white text-sm">${u.username.charAt(0).toUpperCase()}</div>
                                    <div>
                                        <div class="flex items-center">
                                            <span class="font-semibold text-slate-200">${isGroup ? '' : '@'}${u.username}</span>
                                            ${isGroup ? '<span class="bg-brand-500/20 text-brand-400 text-[10px] px-1.5 py-0.5 rounded ml-2">Group</span>' : ''}
                                        </div>
                                        ${u.status_message ? `<p class="text-xs text-slate-500 truncate mt-0.5">${u.status_message}</p>` : ''}
                                    </div>
                                </button>`;
                                searchResults.appendChild(li);
                            });
                            searchResults.classList.remove('hidden');
                        } else {
                            searchResults.innerHTML = `<li class="p-3 text-sm text-slate-500 text-center">No users found</li>`;
                            searchResults.classList.remove('hidden');
                        }
                    });
            }, 300);
        });
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) searchResults.classList.add('hidden');
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ ACTIVE CHAT LOGIC Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        function setActiveChat(uid, name, convId = null, isGroup = false) {
            activeUid = uid;
            activeConvId = convId;
            activeIsGroup = isGroup;
            lastMessageId = 0;
            
            const placeholder = document.getElementById('chat-placeholder');
            const wrapper = document.getElementById('active-chat-wrapper');
            const subgroupsBar = document.getElementById('subgroups-bar');

            if(placeholder) placeholder.classList.add('hidden');
            if(wrapper) { wrapper.classList.remove('hidden'); wrapper.classList.add('flex'); }
            
            document.getElementById('active-name').textContent = name;
            document.getElementById('active-avatar').textContent = name.charAt(0).toUpperCase();
            if (window.innerWidth <= 768) document.getElementById('app-container').classList.add('mobile-active-chat');
            
            messagesContainer.innerHTML = '';
            inChatSearchBar.classList.add('hidden');
            inChatInput.value = '';
            searchMatchCount.textContent = '';
            
            // Toggle Group vs DM features
            if (isGroup) {
                subgroupsBar.classList.remove('hidden');
                subgroupsBar.innerHTML = '<span class="text-xs text-slate-500">Loading channels...</span>';
                
                // Fetch real subgroups from API
                const gId = uid.toString().replace('g', '').split('-')[0];
                fetchSubgroupsForBar(gId);

            } else {
                subgroupsBar.classList.add('hidden');
                subgroupsBar.innerHTML = '';
            }

            document.querySelectorAll('.contact-item').forEach(el => {
                el.classList.remove('bg-brand-500/10', 'border-brand-500/20');
                el.classList.add('border-transparent');
                if (el.dataset.uid == uid) {
                    el.classList.add('bg-brand-500/10', 'border-brand-500/20');
                    el.classList.remove('border-transparent');
                    const badge = el.querySelector('.bg-brand-500.absolute');
                    if (badge) badge.remove();
                }
            });
            
            if (pollingInterval) clearInterval(pollingInterval);
            searchResults.classList.add('hidden');
            searchInput.value = '';
            
            if (convId) {
                pollMessages();
                pollingInterval = setInterval(pollMessages, 2500);
            } else {
                messagesContainer.innerHTML = `<div class="h-full flex items-center justify-center text-slate-500 text-sm">No messages yet. Send an encrypted greeting to start.</div>`;
            }
        }

        document.addEventListener('mousedown', (e) => {
            const contactBtn = e.target.closest('.contact-item');
            if (contactBtn) {
                const isGroup = contactBtn.dataset.isGroup === 'true';
                setActiveChat(contactBtn.dataset.uid, contactBtn.dataset.name, contactBtn.dataset.id, isGroup);
            }
            const searchResultBtn = e.target.closest('.start-chat-btn');
            if (searchResultBtn) {
                let existingItem = document.querySelector(`.contact-item[data-uid="${searchResultBtn.dataset.uid}"]`);
                const isGroup = existingItem ? (existingItem.dataset.isGroup === 'true') : false;
                setActiveChat(searchResultBtn.dataset.uid, searchResultBtn.dataset.name, existingItem ? existingItem.dataset.id : null, isGroup);
                searchResults.classList.add('hidden');
                searchInput.value = '';
            }
        });

        document.getElementById('launch-ai-btn').addEventListener('click', () => {
            const btn = document.getElementById('launch-ai-btn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Connecting...`;
            fetch('/api/users/search?q=SystemBot').then(r => r.json()).then(data => {
                btn.innerHTML = originalHtml;
                if (data.users && data.users.length > 0) {
                    const bot = data.users[0];
                    let existingItem = document.querySelector(`.contact-item[data-uid="${bot.id}"]`);
                    const placeholder = document.getElementById('chat-placeholder');
                    const wrapper = document.getElementById('active-chat-wrapper');
                    if(placeholder) placeholder.classList.add('hidden');
                    if(wrapper) { wrapper.classList.remove('hidden'); wrapper.classList.add('flex'); }
                    setActiveChat(bot.id, bot.username, existingItem ? existingItem.dataset.id : null);
                }
            }).catch(() => { btn.innerHTML = originalHtml; });
        });

        document.getElementById('back-to-contacts').addEventListener('click', () => {
            document.getElementById('app-container').classList.remove('mobile-active-chat');
            if (pollingInterval) clearInterval(pollingInterval);
        });

        // ────── USER PROFILE MODAL ─────────────────────────────────────────────────────────────
        document.getElementById('chat-header-profile-btn').addEventListener('click', () => {
             if (!activeUid) return;

             // If in a group chat, open Group Info panel instead
             if (activeIsGroup) {
                 openGroupInfoPanel();
                 return;
             }
             
             // Open modal and show loading state loosely
             document.getElementById('modal-profile-name').textContent = document.getElementById('active-name').textContent;
             document.getElementById('modal-profile-about').innerHTML = '<span class="animate-pulse">Loading...</span>';
             document.getElementById('modal-profile-status').textContent = '';
             document.getElementById('modal-avatar-initial').textContent = document.getElementById('active-name').textContent.charAt(0).toUpperCase();
             document.getElementById('modal-cover-pic').style.backgroundImage = '';
             document.getElementById('modal-avatar-pic').style.backgroundImage = '';
             document.getElementById('modal-avatar-initial').style.display = 'block';
             document.getElementById('modal-profile-email').textContent = 'Loading...';
             document.getElementById('modal-profile-phone').textContent = 'Loading...';
             document.getElementById('modal-profile-join').textContent = 'Loading...';
             userProfileModal.classList.remove('hidden');

             fetch(`/api/users/profile?id=${activeUid}`)
                 .then(r => r.json())
                 .then(data => {
                     if (data.user) {
                         const u = data.user;
                         document.getElementById('modal-profile-name').textContent = u.first_name || u.last_name 
                            ? `${u.first_name || ''} ${u.last_name || ''}`.trim() 
                            : `@${u.username}`;
                         document.getElementById('modal-profile-status').textContent = u.status_message || `@${u.username}`;
                         document.getElementById('modal-profile-about').textContent = u.about_me || "This user hasn't added a description yet.";
                         
                         document.getElementById('modal-profile-email').textContent = u.email || "N/A";
                         document.getElementById('modal-profile-email').title = u.email || "N/A";
                         document.getElementById('modal-profile-phone').textContent = u.phone_number || "N/A";
                         document.getElementById('modal-profile-phone').title = u.phone_number || "N/A";
                         document.getElementById('modal-profile-join').textContent = u.join_date || "N/A";
                         
                         if (u.avatar_url) {
                             document.getElementById('modal-avatar-pic').style.backgroundImage = `url('${u.avatar_url}')`;
                             document.getElementById('modal-avatar-initial').style.display = 'none';
                         }
                         if (u.cover_url) {
                             document.getElementById('modal-cover-pic').style.backgroundImage = `url('${u.cover_url}')`;
                         }
                     } else {
                         document.getElementById('modal-profile-about').textContent = "Failed to load profile details.";
                     }
                 })
                 .catch(() => {
                     document.getElementById('modal-profile-about').textContent = "Failed to load profile details.";
                 });
        });

        // User Profile Action Handlers
        document.getElementById('modal-action-block').addEventListener('click', () => {
            if (!activeUid) return;
            const name = document.getElementById('modal-profile-name').textContent;
            if (!confirm(`Block ${name}? They will be removed from your chat list.`)) return;
            fetch('/api/chat/block', { method: 'POST', body: new URLSearchParams({ blocked_id: activeUid }) })
                .then(r => r.json()).then(d => {
                    if (d.success) {
                        showToast(`${name} has been blocked.`);
                        userProfileModal.classList.add('hidden');
                        // Hide their contact entry
                        document.querySelector(`.contact-item[data-uid="${activeUid}"]`)?.remove();
                        // Reset chat area
                        document.getElementById('chat-placeholder').classList.remove('hidden');
                        document.getElementById('active-chat-wrapper').classList.add('hidden');
                        activeConvId = null; activeUid = null;
                        if (pollingInterval) clearInterval(pollingInterval);
                    }
                });
        });

        document.getElementById('modal-action-report').addEventListener('click', () => {
             const name = document.getElementById('modal-profile-name').textContent;
             if (confirm(`Are you sure you want to report ${name} for inappropriate behavior?`)) {
                 showToast(`Report submitted for ${name}. Our team will review this.`, 'success');
                 userProfileModal.classList.add('hidden');
             }
        });

        document.getElementById('close-profile-modal').addEventListener('click', () => userProfileModal.classList.add('hidden'));
        userProfileModal.addEventListener('mousedown', e => { if (e.target === userProfileModal) userProfileModal.classList.add('hidden'); });

        // ────── GROUP INFO PANEL ──────────────────────────────────────────────────────────
        const groupInfoPanel = document.getElementById('group-info-panel');
        let currentGroupId = null;

        function openGroupInfoPanel() {
            if (!activeUid || !activeIsGroup) return;
            currentGroupId = activeUid.toString().replace('g', '').split('-')[0];
            groupInfoPanel.classList.remove('hidden');
            fetchGroupInfo(currentGroupId);
        }

        document.getElementById('close-group-info').addEventListener('click', () => groupInfoPanel.classList.add('hidden'));
        groupInfoPanel.addEventListener('mousedown', e => { if (e.target === groupInfoPanel) groupInfoPanel.classList.add('hidden'); });

        function fetchGroupInfo(groupId) {
            fetch(`/api/groups/info?group_id=${groupId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) { showToast(data.error, 'error'); return; }
                    renderGroupInfo(data.group, data.members, data.my_role);
                })
                .catch(() => showToast('Failed to load group info.', 'error'));
        }

        function renderGroupInfo(group, members, myRole) {
            const isAdmin = ['owner','admin'].includes(myRole);
            const isOwner = myRole === 'owner';

            // Cover
            const coverEl = document.getElementById('gi-cover');
            coverEl.style.backgroundImage = group.cover_url ? `url(${group.cover_url})` : '';
            document.getElementById('gi-cover-upload-btn').classList.toggle('hidden', !isAdmin);

            // Avatar
            const avatarEl = document.getElementById('gi-avatar');
            if (group.avatar_url) {
                avatarEl.style.backgroundImage = `url(${group.avatar_url})`;
                avatarEl.textContent = '';
            } else {
                avatarEl.style.backgroundImage = '';
                avatarEl.textContent = group.name.charAt(0).toUpperCase();
            }
            document.getElementById('gi-avatar-upload-btn').classList.toggle('hidden', !isAdmin);
            document.getElementById('gi-upload-actions').classList.toggle('hidden', !isAdmin);

            // Text info
            document.getElementById('gi-name').textContent = group.name;
            document.getElementById('gi-desc').textContent = group.description || 'No description';
            document.getElementById('gi-member-count').innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> ${members.length} member${members.length !== 1 ? 's' : ''}`;
            document.getElementById('gi-privacy-badge').textContent = group.is_private == 1 ? 'Private' : 'Public';
            const statusBadge = document.getElementById('gi-status-badge');
            statusBadge.textContent = (group.status || 'open') === 'open' ? 'Open' : 'Closed';
            statusBadge.className = `px-2 py-0.5 rounded-full text-[10px] font-semibold ${(group.status || 'open') === 'open' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400'}`;

            // Subgroups
            document.getElementById('gi-add-subgroup-toggle').classList.toggle('hidden', !isAdmin);
            fetchSubgroupsForPanel(group.id);

            // Settings section (admin/owner)
            const permSection = document.getElementById('gi-permissions-section');
            permSection.classList.toggle('hidden', !isAdmin);
            if (isAdmin) {
                document.getElementById('gi-group-visibility').value = group.is_private || '0';
                document.getElementById('gi-group-status').value = group.status || 'open';
                document.getElementById('gi-join-mode').value = group.join_mode || 'free';
                document.getElementById('gi-chat-permission').value = group.chat_permission || 'member';
                document.getElementById('gi-elder-msg-hr').value = group.elder_msg_per_hour || 15;
                document.getElementById('gi-elder-max-chars').value = group.elder_max_chars || 200;
                // Hide join mode if closed
                document.getElementById('gi-join-mode-wrap').style.display = (group.status === 'closed') ? 'none' : '';
                document.getElementById('gi-group-status').addEventListener('change', (e) => {
                    document.getElementById('gi-join-mode-wrap').style.display = e.target.value === 'closed' ? 'none' : '';
                });
                // Fetch pending messages
                fetchPendingMessages(group.id);
                fetchJoinRequests(group.id);
            }

            // Add member section (admin)
            document.getElementById('gi-add-member-section').classList.toggle('hidden', !isAdmin);
            // Pending/join sections
            document.getElementById('gi-pending-section').classList.toggle('hidden', !isAdmin);
            document.getElementById('gi-join-requests-section').classList.toggle('hidden', !isAdmin);

            // Invite link section (admin, owner, elder)
            const canInvite = ['owner','admin','elder'].includes(myRole);
            document.getElementById('gi-invite-link-section').classList.toggle('hidden', !canInvite);
            if (canInvite && group.invite_token) {
                document.getElementById('gi-invite-link-display').value = window.location.origin + '/invite/' + group.invite_token;
            } else {
                document.getElementById('gi-invite-link-display').value = '';
            }

            // Danger zone (owner)
            document.getElementById('gi-danger-zone').classList.toggle('hidden', !isOwner);

            // Render members
            const list = document.getElementById('gi-members-list');
            list.innerHTML = '';
            members.forEach(m => {
                const roleBadgeColors = { owner: 'bg-amber-500/20 text-amber-400', admin: 'bg-rose-500/20 text-rose-400', elder: 'bg-emerald-500/20 text-emerald-400', member: 'bg-slate-700/50 text-slate-400' };
                const el = document.createElement('div');
                el.className = 'flex items-center justify-between p-2.5 rounded-xl hover:bg-dark-900/50 transition-colors group';
                let roleControls = '';
                if (isAdmin && m.role !== 'owner' && m.user_id != currentUserId) {
                    roleControls = `<div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">`;
                    if (isOwner) {
                        roleControls += `<select class="gi-role-select bg-dark-900 border border-slate-700 rounded-lg text-[11px] px-1.5 py-1 text-white outline-none" data-uid="${m.user_id}">
                            <option value="member" ${m.role==='member'?'selected':''}>Member</option>
                            <option value="elder" ${m.role==='elder'?'selected':''}>Elder</option>
                            <option value="admin" ${m.role==='admin'?'selected':''}>Admin</option>
                        </select>`;
                    } else if (m.role !== 'admin') {
                        roleControls += `<select class="gi-role-select bg-dark-900 border border-slate-700 rounded-lg text-[11px] px-1.5 py-1 text-white outline-none" data-uid="${m.user_id}">
                            <option value="member" ${m.role==='member'?'selected':''}>Member</option>
                            <option value="elder" ${m.role==='elder'?'selected':''}>Elder</option>
                        </select>`;
                    }
                    roleControls += `<button class="gi-remove-member p-1 hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 rounded-lg transition-colors" data-uid="${m.user_id}" title="Remove"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>`;
                }
                el.innerHTML = `<div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-brand-600 to-rose-400 flex items-center justify-center font-bold text-white text-sm shrink-0">${m.username.charAt(0).toUpperCase()}</div>
                    <div class="min-w-0"><p class="text-sm font-medium text-white truncate">@${m.username}${m.user_id == currentUserId ? ' <span class="text-[10px] text-slate-500">(you)</span>' : ''}</p>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full ${roleBadgeColors[m.role] || roleBadgeColors.member} font-semibold uppercase">${m.role}</span></div>
                </div>${roleControls}`;
                list.appendChild(el);
            });

            // Role change
            list.querySelectorAll('.gi-role-select').forEach(sel => {
                sel.addEventListener('change', (e) => {
                    const uid = e.target.dataset.uid;
                    fetch('/api/groups/members/role', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ group_id: currentGroupId, user_id: uid, role: e.target.value }) })
                        .then(r => r.json()).then(d => { if (d.success) { showToast(`Role updated`); fetchGroupInfo(currentGroupId); } else showToast(d.error || 'Failed', 'error'); });
                });
            });
            // Remove member
            list.querySelectorAll('.gi-remove-member').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (!confirm('Remove this member?')) return;
                    fetch('/api/groups/members/remove', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ group_id: currentGroupId, user_id: btn.dataset.uid }) })
                        .then(r => r.json()).then(d => { if (d.success) { showToast('Member removed'); fetchGroupInfo(currentGroupId); } else showToast(d.error || 'Failed', 'error'); });
                });
            });
        }

        // Fetch pending messages
        function fetchPendingMessages(groupId) {
            fetch(`/api/groups/pending?group_id=${groupId}`).then(r => r.json()).then(data => {
                const container = document.getElementById('gi-pending-list');
                const items = data.pending || [];
                container.innerHTML = items.length === 0 ? '<div class="text-sm text-slate-500 text-center py-2">No pending messages</div>' : '';
                items.forEach(pm => {
                    const el = document.createElement('div');
                    el.className = 'bg-dark-900/50 rounded-xl p-3 border border-amber-500/20';
                    el.innerHTML = `<div class="flex items-center justify-between mb-1"><span class="text-xs font-medium text-amber-400">@${pm.sender_name}</span><span class="text-[10px] text-slate-500">${new Date(pm.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</span></div>
                        <p class="text-sm text-slate-300 mb-2">${pm.content ? '(encrypted)' : 'File attachment'}</p>
                        <div class="flex gap-2"><button class="flex-1 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 transition-colors" onclick="approvePending(${pm.id})">Approve</button><button class="flex-1 py-1.5 rounded-lg text-xs font-semibold bg-rose-500/20 text-rose-400 hover:bg-rose-500/30 transition-colors" onclick="rejectPending(${pm.id})">Reject</button></div>`;
                    container.appendChild(el);
                });
            });
        }
        window.approvePending = (id) => fetch('/api/groups/pending/approve', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({pending_id:id}) }).then(r=>r.json()).then(d=>{ if(d.success){showToast('Message approved');fetchGroupInfo(currentGroupId);} else showToast(d.error,'error'); });
        window.rejectPending = (id) => fetch('/api/groups/pending/reject', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({pending_id:id}) }).then(r=>r.json()).then(d=>{ if(d.success){showToast('Message rejected');fetchGroupInfo(currentGroupId);} else showToast(d.error,'error'); });

        // Fetch join requests
        function fetchJoinRequests(groupId) {
            fetch(`/api/groups/join-requests?group_id=${groupId}`).then(r => r.json()).then(data => {
                const container = document.getElementById('gi-join-requests-list');
                const items = data.requests || [];
                container.innerHTML = items.length === 0 ? '<div class="text-sm text-slate-500 text-center py-2">No pending requests</div>' : '';
                items.forEach(jr => {
                    const el = document.createElement('div');
                    el.className = 'flex items-center justify-between p-2.5 rounded-xl bg-dark-900/50 border border-emerald-500/20';
                    el.innerHTML = `<span class="text-sm text-white">@${jr.username}</span>
                        <div class="flex gap-1"><button class="px-2 py-1 rounded-lg text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30" onclick="approveJoin(${jr.id})">Accept</button><button class="px-2 py-1 rounded-lg text-[10px] font-semibold bg-rose-500/20 text-rose-400 hover:bg-rose-500/30" onclick="rejectJoin(${jr.id})">Reject</button></div>`;
                    container.appendChild(el);
                });
            });
        }
        window.approveJoin = (id) => fetch('/api/groups/join-requests/approve', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({request_id:id}) }).then(r=>r.json()).then(d=>{ if(d.success){showToast('Request approved');fetchGroupInfo(currentGroupId);} else showToast(d.error,'error'); });
        window.rejectJoin = (id) => fetch('/api/groups/join-requests/reject', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({request_id:id}) }).then(r=>r.json()).then(d=>{ if(d.success){showToast('Request rejected');fetchGroupInfo(currentGroupId);} else showToast(d.error,'error'); });

        // Fetch subgroups for panel
        function fetchSubgroupsForPanel(groupId) {
            fetch(`/api/groups/subgroups?group_id=${groupId}`)
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('gi-subgroups-list');
                    container.innerHTML = '';
                    (data.subgroups || []).forEach(sg => {
                        const el = document.createElement('div');
                        el.className = 'flex items-center gap-2 p-2 rounded-lg hover:bg-dark-900/50 transition-colors cursor-pointer text-sm text-slate-300';
                        el.innerHTML = `<span class="text-brand-400 font-medium">#</span> ${sg.name}`;
                        container.appendChild(el);
                    });
                    if ((data.subgroups || []).length === 0) container.innerHTML = '<div class="text-sm text-slate-500 text-center py-2">No channels yet</div>';
                });
        }

        // Toggle create subgroup form
        document.getElementById('gi-add-subgroup-toggle').addEventListener('click', () => {
            document.getElementById('gi-add-subgroup-form').classList.toggle('hidden');
        });

        // Create subgroup
        document.getElementById('gi-create-subgroup-btn').addEventListener('click', () => {
            const nameInput = document.getElementById('gi-new-subgroup-name');
            const name = nameInput.value.trim();
            if (!name || !currentGroupId) return;
            fetch('/api/groups/subgroups/create', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ group_id: currentGroupId, name }) })
                .then(r => r.json()).then(d => {
                    if (d.success) { showToast('Channel created!'); nameInput.value = ''; document.getElementById('gi-add-subgroup-form').classList.add('hidden'); fetchSubgroupsForPanel(currentGroupId); fetchSubgroupsForBar(currentGroupId); }
                    else showToast(d.error || 'Failed', 'error');
                });
        });

        // Save all group settings
        document.getElementById('gi-save-permission-btn').addEventListener('click', () => {
            const params = {
                group_id: currentGroupId,
                is_private: document.getElementById('gi-group-visibility').value,
                chat_permission: document.getElementById('gi-chat-permission').value,
                status: document.getElementById('gi-group-status').value,
                join_mode: document.getElementById('gi-join-mode').value,
                elder_msg_per_hour: document.getElementById('gi-elder-msg-hr').value,
                elder_max_chars: document.getElementById('gi-elder-max-chars').value
            };
            fetch('/api/groups/edit', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams(params) })
                .then(r => r.json()).then(d => { if (d.success) { showToast('Settings saved!'); fetchGroupInfo(currentGroupId); } else showToast(d.error || 'Failed', 'error'); });
        });

        // Avatar upload (global function for onclick)
        window.uploadGroupAvatar = function(input) {
            const file = input.files[0]; if (!file || !currentGroupId) return;
            const fd = new FormData(); fd.append('group_id', currentGroupId); fd.append('avatar', file);
            fetch('/api/groups/avatar', { method: 'POST', body: fd }).then(r=>r.json()).then(d => {
                if (d.success) { showToast('Avatar updated!'); document.getElementById('gi-avatar').style.backgroundImage = `url(${d.avatar_url})`; document.getElementById('gi-avatar').textContent = ''; }
                else showToast(d.error || 'Upload failed', 'error');
            }).catch(() => showToast('Upload failed', 'error'));
            input.value = '';
        };

        // Cover upload (global function for onclick)
        window.uploadGroupCover = function(input) {
            const file = input.files[0]; if (!file || !currentGroupId) return;
            const fd = new FormData(); fd.append('group_id', currentGroupId); fd.append('cover', file);
            fetch('/api/groups/cover', { method: 'POST', body: fd }).then(r=>r.json()).then(d => {
                if (d.success) { showToast('Cover updated!'); document.getElementById('gi-cover').style.backgroundImage = `url(${d.cover_url})`; }
                else showToast(d.error || 'Upload failed', 'error');
            }).catch(() => showToast('Upload failed', 'error'));
            input.value = '';
        };

        // Invite link functions
        window.generateInviteLink = function() {
            if (!currentGroupId) { showToast('Select a group first', 'error'); return; }
            fetch('/api/groups/invite-link', { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({group_id: currentGroupId}) })
                .then(r=>r.json()).then(d => {
                    if (d.success) {
                        const link = window.location.origin + '/invite/' + d.invite_token;
                        document.getElementById('gi-invite-link-display').value = link;
                        showToast('Invite link generated!');
                    } else showToast(d.error || 'Failed to generate', 'error');
                }).catch(err => { console.error('Invite link error:', err); showToast('Network error generating link', 'error'); });
        };

        window.copyInviteLink = function() {
            const input = document.getElementById('gi-invite-link-display');
            if (!input.value) { window.generateInviteLink(); return; }
            navigator.clipboard.writeText(input.value).then(() => showToast('Link copied to clipboard!')).catch(() => { input.select(); document.execCommand('copy'); showToast('Link copied!'); });
        };

        // Delete group
        document.getElementById('gi-delete-group-btn').addEventListener('click', () => {
            if (!confirm('Are you sure? This will permanently delete this group and all its messages.')) return;
            fetch('/api/groups/delete', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ group_id: currentGroupId }) })
                .then(r => r.json()).then(d => {
                    if (d.success) { showToast('Group deleted'); groupInfoPanel.classList.add('hidden'); location.reload(); }
                    else showToast(d.error || 'Failed', 'error');
                });
        });

        // Add member search inside Group Info
        let giMemberSearchTimeout = null;
        const giAddMemberSearch = document.getElementById('gi-add-member-search');
        const giAddMemberResults = document.getElementById('gi-add-member-results');
        if (giAddMemberSearch) {
            giAddMemberSearch.addEventListener('input', (e) => {
                clearTimeout(giMemberSearchTimeout);
                const q = e.target.value.trim();
                if (q.length < 2) { giAddMemberResults.classList.add('hidden'); return; }
                giMemberSearchTimeout = setTimeout(() => {
                    fetch(`/api/users/search?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(data => {
                            giAddMemberResults.innerHTML = '';
                            const users = (data.users || []).filter(u => !u.is_group);
                            if (users.length > 0) {
                                users.forEach(u => {
                                    const li = document.createElement('li');
                                    li.innerHTML = `<button type="button" class="w-full text-left p-2 hover:bg-slate-800 flex items-center gap-2 transition-colors text-sm"><div class="w-7 h-7 rounded-full bg-gradient-to-tr from-brand-600 to-rose-400 flex items-center justify-center font-bold text-white text-xs">${u.username.charAt(0).toUpperCase()}</div><span class="text-slate-200">@${u.username}</span></button>`;
                                    li.querySelector('button').addEventListener('click', () => {
                                        fetch('/api/groups/members/add', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ group_id: currentGroupId, user_id: u.id }) })
                                            .then(r => r.json()).then(d => {
                                                if (d.success) { showToast(`@${u.username} added!`); giAddMemberSearch.value = ''; giAddMemberResults.classList.add('hidden'); fetchGroupInfo(currentGroupId); }
                                                else showToast(d.error || 'Failed', 'error');
                                            });
                                    });
                                    giAddMemberResults.appendChild(li);
                                });
                                giAddMemberResults.classList.remove('hidden');
                            } else {
                                giAddMemberResults.innerHTML = '<li class="p-2 text-sm text-slate-500 text-center">No users found</li>';
                                giAddMemberResults.classList.remove('hidden');
                            }
                        });
                }, 300);
            });
        }

        // Fetch subgroups for the subgroups bar (in active chat)
        function fetchSubgroupsForBar(groupId) {
            const bar = document.getElementById('subgroups-bar');
            fetch(`/api/groups/subgroups?group_id=${groupId}`)
                .then(r => r.json())
                .then(data => {
                    bar.innerHTML = '';
                    const sgs = data.subgroups || [];
                    sgs.forEach((sg, i) => {
                        const btn = document.createElement('button');
                        const isActive = (i === 0); // first is active by default
                        btn.className = `px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${isActive ? 'bg-brand-500/20 text-brand-300 border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800'}`;
                        btn.textContent = `# ${sg.name}`;
                        btn.addEventListener('click', () => {
                            // Switch subgroup
                            activeConvId = `g${groupId}-${sg.id}`;
                            lastMessageId = 0;
                            lastRenderedSenderId = null; lastRenderedDate = null;
                            messagesContainer.innerHTML = '';
                            bar.querySelectorAll('button').forEach(b => { b.className = 'px-3 py-1.5 rounded-lg text-sm font-medium transition-colors text-slate-400 hover:text-white hover:bg-slate-800'; });
                            btn.className = 'px-3 py-1.5 rounded-lg text-sm font-medium transition-colors bg-brand-500/20 text-brand-300 border border-brand-500/30';
                            if (pollingInterval) clearInterval(pollingInterval);
                            pollMessages();
                            pollingInterval = setInterval(pollMessages, 2500);
                        });
                        bar.appendChild(btn);
                    });
                });
        }

        // Ã¢â€â‚¬Ã¢â€â‚¬ POLLING Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        function pollMessages() {
            if (!activeConvId) return;
            
            let url = `/api/chat/messages?last_id=${lastMessageId}`;
            if (activeIsGroup) {
                // activeConvId is "g{groupId}-{subgroupId}"
                const parts = activeConvId.substring(1).split('-');
                url += `&group_id=${parts[0]}&subgroup_id=${parts[1] || 0}`;
            } else {
                url += `&conversation_id=${activeConvId}`;
            }

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        if (messagesContainer.querySelector('.text-slate-500.text-sm')) messagesContainer.innerHTML = '';
                        let autoScroll = (messagesContainer.scrollTop + messagesContainer.clientHeight >= messagesContainer.scrollHeight - 50);
                        data.messages.forEach(msg => {
                            if (msg.id > lastMessageId) lastMessageId = msg.id;
                            if (!document.querySelector(`[data-msg-id="${msg.id}"]`)) appendMessageToDOM(msg);
                        });
                        if (autoScroll) messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                });
        }

        // Ã¢â€â‚¬Ã¢â€â‚¬ RENDER BUBBLE Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        let lastRenderedSenderId = null;
        let lastRenderedDate = null;

        function appendMessageToDOM(msg) {
            const isMe = msg.sender_id == currentUserId;
            const msgDate = new Date(msg.created_at);
            const dateStr = msgDate.toLocaleDateString([], { year: 'numeric', month: 'short', day: 'numeric' });

            // Date separator
            if (dateStr !== lastRenderedDate) {
                lastRenderedDate = dateStr;
                lastRenderedSenderId = null;
                const sep = document.createElement('div');
                sep.className = 'flex items-center justify-center my-4';
                sep.innerHTML = `<div class="px-4 py-1 rounded-full bg-dark-800 border border-slate-700/50 text-[11px] text-slate-400 font-medium">${dateStr}</div>`;
                messagesContainer.appendChild(sep);
            }

            // WhatsApp-style: same sender consecutive = hide name, less spacing
            const isSameSender = msg.sender_id == lastRenderedSenderId;
            lastRenderedSenderId = msg.sender_id;

            const wrapper = document.createElement('div');
            wrapper.className = `flex w-full ${isMe ? 'justify-end' : 'justify-start'} ${isSameSender ? 'mt-0.5' : 'mt-3'}`;
            wrapper.dataset.msgId = msg.id;
            wrapper.style.animation = 'fadeInUp 0.3s ease-out forwards';

            const bubbleContainer = document.createElement('div');
            bubbleContainer.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} max-w-[85%] sm:max-w-[75%]`;

            // Show sender name only in groups, non-self, first in sequence
            if (activeIsGroup && !isMe && msg.sender_name && !isSameSender) {
                const nameLabel = document.createElement('div');
                nameLabel.className = 'text-[11px] font-bold text-brand-400 mb-1 ml-1';
                nameLabel.textContent = msg.sender_name;
                bubbleContainer.appendChild(nameLabel);
            }

            const bubble = document.createElement('div');
            bubble.className = `msg-bubble p-3 sm:p-4 rounded-2xl shadow-sm ${isMe ? 'msg-sent' : 'msg-received'} select-text cursor-context-menu`;
            
            let contentHtml = '';
            if (msg.file) {
                const ext = msg.file.name.split('.').pop().toLowerCase();
                const isImage = ['jpg','jpeg','png','gif','webp'].includes(ext);
                const isAudio = ['mp3','wav','ogg','m4a'].includes(ext);
                let clickAction = `window.location.href='/download/${msg.file.id}'`;
                let iconStr = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>';
                if (isImage) { clickAction = `openLightbox('${msg.file.id}','${msg.file.name}','image')`; iconStr = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'; }
                else if (isAudio) { clickAction = `openLightbox('${msg.file.id}','${msg.file.name}','audio')`; iconStr = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>'; }
                contentHtml += `<div class="bg-dark-900/40 rounded-xl p-3 mb-2 flex items-center gap-3 border border-white/10 cursor-pointer hover:bg-dark-900/60 transition-colors" onclick="${clickAction}">
                    <div class="w-10 h-10 bg-dark-800 rounded-lg flex items-center justify-center ${isImage?'text-emerald-400':isAudio?'text-amber-400':'text-brand-400'}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${iconStr}</svg></div>
                    <div class="flex-1 min-w-0"><p class="text-sm font-medium text-white truncate">${msg.file.name}</p><p class="text-xs text-slate-400">${(msg.file.size/1024).toFixed(1)} KB</p></div>
                </div>`;
            }
            if (msg.content) {
                const isBotChat = document.getElementById('active-name').textContent === 'SystemBot';
                if (isBotChat && !isMe) {
                    // Render bot messages with markdown formatting
                    contentHtml += `<div class="text-[15px] leading-relaxed bot-markdown">${renderMarkdown(msg.content)}</div>`;
                } else {
                    const div = document.createElement('div'); div.innerText = msg.content;
                    contentHtml += `<p class="text-[15px] leading-relaxed whitespace-pre-wrap">${div.innerHTML}</p>`;
                }
            }
            const time = msgDate.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
            contentHtml += `<div class="text-[10px] mt-1.5 flex ${isMe?'justify-end text-brand-200/70':'justify-start text-slate-400'} items-center gap-1 whitespace-nowrap">${time}${isMe?' <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>':''}</div>`;
            bubble.innerHTML = contentHtml;

            bubble.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                ctxMsgId = msg.id;
                ctxMsgText = msg.content || '';
                contextMenu.style.left = Math.min(e.clientX, window.innerWidth - 200) + 'px';
                contextMenu.style.top = Math.min(e.clientY, window.innerHeight - 150) + 'px';
                contextMenu.classList.remove('hidden');
            });

            bubbleContainer.appendChild(bubble);
            wrapper.appendChild(bubbleContainer);
            messagesContainer.appendChild(wrapper);

            if (!document.getElementById('anim-style')) {
                const style = document.createElement('style');
                style.id = 'anim-style';
                style.textContent = `@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }`;
                document.head.appendChild(style);
            }
        }


        // Ã¢â€â‚¬Ã¢â€â‚¬ FILE UPLOAD UI Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        const fileIndicator = document.getElementById('file-preview-indicator');
        const fileNameDisp = document.getElementById('file-preview-name');
        document.getElementById('attachment-btn').addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameDisp.textContent = fileInput.files[0].name;
                fileIndicator.classList.remove('hidden');
                document.getElementById('send-btn').disabled = false;
            }
        });
        document.getElementById('remove-file').addEventListener('click', () => {
            fileInput.value = '';
            fileIndicator.classList.add('hidden');
            if (messageInput.value.trim() === '') document.getElementById('send-btn').disabled = true;
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ SEND MESSAGE Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const content = messageInput.value.trim();
            const file = fileInput.files[0];
            if (!content && !file) return;
            if (!activeUid) return;
            
            const formData = new FormData();
            
            if (activeIsGroup && activeConvId) {
                // activeConvId format: g{groupId}-{subgroupId}
                const parts = activeConvId.substring(1).split('-');
                formData.append('group_id', parts[0]);
                formData.append('subgroup_id', parts[1] || 0);
            } else {
                formData.append('recipient_id', activeUid);
            }
            
            formData.append('content', content);
            if (file) formData.append('attachment', file);
            
            if (content && !file) {
                messageInput.value = '';
                messageInput.style.height = '44px';
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            if (file) {
                fileInput.value = ''; fileIndicator.classList.add('hidden');
                document.getElementById('send-btn').disabled = true;
                messageInput.value = '';
                messagesContainer.insertAdjacentHTML('beforeend', `<div class="text-xs text-brand-400 text-center animate-pulse my-2" id="uploading-indicator">Encrypting & Uploading...</div>`);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            const isBotChat = document.getElementById('active-name').textContent === 'SystemBot';
            if (isBotChat && !file) {
                messagesContainer.insertAdjacentHTML('beforeend', `<div id="typing-indicator" class="flex w-full justify-start mt-2 mb-2"><div class="msg-bubble p-4 rounded-2xl msg-received flex items-center gap-1.5"><div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-duration:0.8s"></div><div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay:0.2s;animation-duration:0.8s"></div><div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay:0.4s;animation-duration:0.8s"></div></div></div>`);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            fetch('/api/chat/send', { method: 'POST', body: formData })
                .then(r => r.json()).then(data => {
                    document.getElementById('uploading-indicator')?.remove();
                    document.getElementById('typing-indicator')?.remove();
                    if (data.error) { showToast(data.error, 'error'); }
                    else if (data.pending_approval) { showToast('Quota exceeded. Message sent for admin approval.', 'success'); }
                    else if (data.success) {
                        if (!activeConvId) { activeConvId = data.conversation_id; pollingInterval = setInterval(pollMessages, 2500); }
                        pollMessages();
                    }
                }).catch(() => { document.getElementById('uploading-indicator')?.remove(); document.getElementById('typing-indicator')?.remove(); });
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ DRAG & DROP Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        const chatPane = document.getElementById('active-chat-wrapper');
        const dragOverlay = document.getElementById('drag-overlay');
        chatPane.addEventListener('dragover', e => { e.preventDefault(); dragOverlay.classList.remove('hidden'); });
        chatPane.addEventListener('dragleave', e => { e.preventDefault(); if (!chatPane.contains(e.relatedTarget)) dragOverlay.classList.add('hidden'); });
        chatPane.addEventListener('drop', e => {
            e.preventDefault(); dragOverlay.classList.add('hidden');
            if (e.dataTransfer.files.length > 0) { fileInput.files = e.dataTransfer.files; fileInput.dispatchEvent(new Event('change')); }
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ LIGHTBOX Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        const lightbox = document.getElementById('file-lightbox');
        const lightboxContent = document.getElementById('lightbox-content');
        const lightboxFilename = document.getElementById('lightbox-filename');
        const lightboxDownloadBtn = document.getElementById('lightbox-download-btn');
        const lightboxLoader = document.getElementById('lightbox-loader');
        window.openLightbox = function(fileId, filename, type) {
            lightboxFilename.textContent = filename;
            lightboxDownloadBtn.href = `/download/${fileId}`;
            lightbox.classList.remove('hidden');
            lightboxLoader.classList.remove('hidden');
            lightboxContent.querySelectorAll('img, audio').forEach(el => el.remove());
            const sourceUrl = `/api/file/stream/${fileId}`;
            if (type === 'image') {
                const img = new Image();
                img.onload = () => lightboxLoader.classList.add('hidden');
                img.onerror = () => { lightboxLoader.classList.add('hidden'); alert('Failed to load image.'); };
                img.className = 'max-w-full max-h-full object-contain rounded-lg shadow-2xl';
                img.src = sourceUrl; lightboxContent.appendChild(img);
            } else if (type === 'audio') {
                const audio = document.createElement('audio');
                audio.controls = true; audio.className = 'w-full max-w-2xl rounded-xl shadow-2xl bg-dark-800';
                audio.oncanplaythrough = () => lightboxLoader.classList.add('hidden');
                audio.onerror = () => { lightboxLoader.classList.add('hidden'); alert('Failed to load audio.'); };
                audio.src = sourceUrl; lightboxContent.appendChild(audio);
            }
        };
        const closeLightbox = () => {
            lightbox.classList.add('hidden');
            lightboxContent.querySelector('audio')?.pause();
            lightboxContent.querySelectorAll('img, audio').forEach(el => el.remove());
        };
        document.getElementById('close-lightbox').addEventListener('click', closeLightbox);
        lightbox.addEventListener('mousedown', e => { if (e.target === lightbox || e.target === lightboxContent) closeLightbox(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) closeLightbox(); });

        // Ã¢â€â‚¬Ã¢â€â‚¬ INIT Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        document.getElementById('send-btn').disabled = true;
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!document.getElementById('send-btn').disabled) chatForm.dispatchEvent(new Event('submit'));
            }
        });
    </script>

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    <!-- CALL OVERLAYS                                                  -->
    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->

    <!-- Outgoing Call Overlay -->
    <div id="outgoing-call-overlay" class="hidden fixed inset-0 z-[800] bg-dark-900/95 backdrop-blur-2xl flex flex-col items-center justify-center gap-6 overflow-hidden">
        <!-- Local Preview Video for Outgoing Video Calls -->
        <video id="oc-local-video" autoplay playsinline muted class="hidden absolute inset-0 w-full h-full object-cover z-0 opacity-40"></video>
        
        <div class="relative z-10 flex flex-col items-center">
            <div class="relative mb-6">
                <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-brand-600 to-rose-400 flex items-center justify-center text-white text-4xl font-bold shadow-2xl" id="oc-avatar">?</div>
                <div class="absolute inset-0 rounded-full border-4 border-brand-400/50 animate-ping"></div>
            </div>
            <div class="text-center">
                <p class="text-slate-400 text-sm uppercase tracking-widest mb-1 font-semibold" id="oc-type-label">Audio Call</p>
                <h2 class="text-4xl font-bold text-white tracking-tight" id="oc-name">Calling...</h2>
                <div class="flex items-center justify-center gap-2 mt-3">
                    <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0s;"></div>
                    <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.2s;"></div>
                    <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.4s;"></div>
                </div>
                <p class="text-slate-400 text-sm mt-3 animate-pulse">Ringing...</p>
            </div>
        </div>

        <button id="cancel-call-btn" class="relative z-10 w-16 h-16 bg-rose-500 hover:bg-rose-600 rounded-full flex flex-col items-center justify-center gap-1 shadow-[0_0_20px_rgba(244,63,94,0.4)] transition-all hover:scale-110 mt-8">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"/></svg>
            <span class="text-white text-[10px] font-medium">Cancel</span>
        </button>
    </div>

    <!-- Incoming Call Modal -->
    <div id="incoming-call-overlay" class="hidden fixed inset-0 z-[800] bg-dark-900/95 backdrop-blur-2xl flex flex-col items-center justify-center gap-6">
        <div class="relative">
            <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-emerald-500 to-brand-600 flex items-center justify-center text-white text-4xl font-bold shadow-2xl" id="ic-avatar">?</div>
            <div class="absolute inset-0 rounded-full border-4 border-emerald-400/60 animate-ping"></div>
        </div>
        <div class="text-center">
            <p class="text-slate-400 text-sm uppercase tracking-widest mb-1" id="ic-type-label">Incoming Audio Call</p>
            <h2 class="text-3xl font-bold text-white" id="ic-name">Caller</h2>
            <p class="text-slate-500 text-sm mt-1">is calling youâ€¦</p>
        </div>
        <div class="flex gap-10 mt-4">
            <button id="reject-call-btn" class="w-16 h-16 bg-rose-500 hover:bg-rose-600 rounded-full flex flex-col items-center justify-center shadow-2xl transition-all hover:scale-110 gap-1">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"/></svg>
                <span class="text-white text-[10px]">Decline</span>
            </button>
            <button id="accept-call-btn" class="w-16 h-16 bg-emerald-500 hover:bg-emerald-400 rounded-full flex flex-col items-center justify-center shadow-2xl transition-all hover:scale-110 gap-1">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span class="text-white text-[10px]">Accept</span>
            </button>
        </div>
    </div>

    <!-- Active Call Overlay -->
    <div id="active-call-overlay" class="hidden fixed inset-0 z-[700] bg-black flex flex-col">
        <!-- Remote Video (full screen) -->
        <video id="remote-video" autoplay playsinline class="absolute inset-0 w-full h-full object-cover bg-dark-900"></video>
        <!-- Remote audio icon (shown when audio-only call) -->
        <div id="audio-call-bg" class="absolute inset-0 hidden flex-col items-center justify-center bg-gradient-to-b from-dark-900 to-dark-800">
            <div class="w-32 h-32 rounded-full bg-gradient-to-tr from-brand-600 to-emerald-400 flex items-center justify-center text-white text-5xl font-bold shadow-2xl mb-6" id="ac-avatar">?</div>
            <h2 class="text-3xl font-bold text-white" id="ac-name">User</h2>
        </div>
        <!-- Local video (PiP) -->
        <video id="local-video" autoplay playsinline muted class="absolute bottom-24 right-4 w-36 h-52 rounded-2xl object-cover border-2 border-white/20 shadow-2xl z-10 bg-dark-800"></video>
        <!-- Top bar -->
        <div class="absolute top-0 left-0 right-0 p-5 flex items-center gap-3 bg-gradient-to-b from-black/70 to-transparent z-20">
            <div class="flex-1">
                <p class="text-slate-400 text-xs uppercase tracking-widest" id="ac-type-label">Audio Call</p>
                <h2 class="text-white font-bold text-xl" id="ac-name-top"></h2>
            </div>
            <div class="text-white font-mono text-lg bg-black/40 px-4 py-1.5 rounded-full" id="call-timer">00:00</div>
        </div>
        <!-- Bottom controls -->
        <div class="absolute bottom-0 left-0 right-0 p-8 flex items-center justify-center gap-6 bg-gradient-to-t from-black/80 to-transparent z-20">
            <button id="toggle-mute-btn" class="w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full flex flex-col items-center justify-center gap-1 transition-all" title="Mute">
                <svg id="mute-icon" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                <span class="text-white text-[9px]">Mute</span>
            </button>
            <button id="end-call-btn" class="w-20 h-20 bg-rose-500 hover:bg-rose-600 rounded-full flex items-center justify-center shadow-2xl transition-all hover:scale-110">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"/></svg>
            </button>
            <button id="toggle-camera-btn" class="w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full flex flex-col items-center justify-center gap-1 transition-all" title="Camera" id="camera-ctrl">
                <svg id="camera-icon" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span class="text-white text-[9px]">Camera</span>
            </button>
        </div>
    </div>

    <!-- Ringtone audio element -->
    <audio id="ringtone" loop>
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAA..." type="audio/wav">
    </audio>

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    <!-- WEBRTC CALL JAVASCRIPT (clean rewrite)                        -->
    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    <script>
    // â”€â”€ STATE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const CALL_MY_ID = <?= (int)$_SESSION['user_id'] ?>;
    const STUN = { iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]};

    let callPc          = null;   // RTCPeerConnection
    let callLocalStream = null;
    let callPeerId      = null;
    let callPeerName    = '';
    let callIsVideo     = false;
    let callMuted       = false;
    let callCamOff      = false;
    let callTimerSec    = 0;
    let callTimerIv     = null;
    let callPollIv      = null;
    let incomingOffer   = null;   // stored SDP offer waiting for user to accept
    let pendingIce      = [];     // ICE candidates queued before remote desc set
    let ringtoneIv      = null;
    let ringtoneCtx     = null;
    let inCallPoll      = false;  // lock to avoid concurrent polls

    // â”€â”€ DOM â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const callOverlayOut = document.getElementById('outgoing-call-overlay');
    const callOverlayIn  = document.getElementById('incoming-call-overlay');
    const callOverlayAct = document.getElementById('active-call-overlay');
    const elLocalVid     = document.getElementById('local-video');
    const elRemoteVid    = document.getElementById('remote-video');
    const elAudioBg      = document.getElementById('audio-call-bg');
    const elCallTimer    = document.getElementById('call-timer');

    // â”€â”€ LOCAL TOAST (independent of main script) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function callToast(msg, type = 'info') {
        const t = document.createElement('div');
        t.className = `fixed bottom-6 right-6 z-[900] px-5 py-3 rounded-2xl text-sm font-semibold shadow-2xl ${
            type === 'error' ? 'bg-rose-500 text-white' :
            type === 'success' ? 'bg-emerald-500 text-white' :
            'bg-slate-700 text-white'}`;
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 400); }, 3000);
    }

    // â”€â”€ RINGTONE (Web Audio API â€” no file needed) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function startRingtone() {
        stopRingtone();
        ringtoneCtx = new (window.AudioContext || window.webkitAudioContext)();
        const beep = () => {
            if (!ringtoneCtx) return;
            const osc = ringtoneCtx.createOscillator();
            const gain = ringtoneCtx.createGain();
            osc.connect(gain); gain.connect(ringtoneCtx.destination);
            osc.frequency.setValueAtTime(880, ringtoneCtx.currentTime);
            osc.frequency.linearRampToValueAtTime(660, ringtoneCtx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.4, ringtoneCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ringtoneCtx.currentTime + 0.5);
            osc.start(); osc.stop(ringtoneCtx.currentTime + 0.5);
        };
        beep();
        ringtoneIv = setInterval(beep, 1400);
    }
    function stopRingtone() {
        if (ringtoneIv) { clearInterval(ringtoneIv); ringtoneIv = null; }
        if (ringtoneCtx) { try { ringtoneCtx.close(); } catch(e){} ringtoneCtx = null; }
    }

    // â”€â”€ SIGNALING â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function sendSig(type, data) {
        if (!callPeerId) return;
        const body = new URLSearchParams({
            to_user_id: callPeerId,
            type,
            data: JSON.stringify(data)
        });
        return fetch('/api/call/signal', { method: 'POST', body });
    }

    // â”€â”€ PEER CONNECTION â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function buildPc() {
        if (callPc) { try { callPc.close(); } catch(e){} callPc = null; }
        callPc = new RTCPeerConnection(STUN);

        callPc.onicecandidate = (e) => {
            if (e.candidate) sendSig('ice-candidate', e.candidate.toJSON());
        };

        callPc.ontrack = (e) => {
            elRemoteVid.srcObject = e.streams[0];
        };

        callPc.onconnectionstatechange = () => {
            if (!callPc) return;
            if (callPc.connectionState === 'connected') showActiveCall();
            if (['failed','disconnected','closed'].includes(callPc.connectionState)) {
                hangup(false);
                callToast('Call ended.', 'info');
            }
        };

        // Add local tracks if stream ready
        if (callLocalStream) {
            callLocalStream.getTracks().forEach(t => callPc.addTrack(t, callLocalStream));
        }

        // Flush any queued ICE
        pendingIce.forEach(c => callPc.addIceCandidate(new RTCIceCandidate(c)).catch(()=>{}));
        pendingIce = [];
    }

    async function handleSig(type, data) {
        try {
            if (type === 'answer') {
                if (!callPc) return;
                await callPc.setRemoteDescription(new RTCSessionDescription(data));
                // Flush ICE
                for (const c of pendingIce) await callPc.addIceCandidate(new RTCIceCandidate(c)).catch(()=>{});
                pendingIce = [];
            } else if (type === 'ice-candidate') {
                if (callPc && callPc.remoteDescription) {
                    await callPc.addIceCandidate(new RTCIceCandidate(data));
                } else {
                    pendingIce.push(data);
                }
            } else if (type === 'end' || type === 'reject') {
                hangup(false);
                callToast(type === 'reject' ? 'Call declined.' : 'Call ended by other user.', 'error');
            }
        } catch(e) { console.warn('handleSig error', e); }
    }

    // â”€â”€ OUTGOING CALL â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    async function startCall(peerId, peerName, withVideo) {
        if (callPeerId) { callToast('Already in a call.', 'error'); return; }
        if (!peerId)    { callToast('Select a conversation first.', 'error'); return; }

        callPeerId   = peerId;
        callPeerName = peerName;
        callIsVideo  = withVideo;

        try {
            callLocalStream = await navigator.mediaDevices.getUserMedia({
                audio: true, video: withVideo
            });
        } catch(e) {
            callPeerId = null;
            callToast('Cannot access ' + (withVideo ? 'camera/mic' : 'microphone') + '. Grant permission.', 'error');
            return;
        }

        // Setup local video element for active call PiP
        elLocalVid.srcObject = callLocalStream;
        elLocalVid.classList.toggle('hidden', !withVideo);

        // Show outgoing overlay with Video Preview
        const ocLocalVid = document.getElementById('oc-local-video');
        if (withVideo) {
            ocLocalVid.srcObject = callLocalStream;
            ocLocalVid.classList.remove('hidden');
        } else {
            ocLocalVid.srcObject = null;
            ocLocalVid.classList.add('hidden');
        }

        document.getElementById('oc-avatar').textContent = peerName.charAt(0).toUpperCase();
        document.getElementById('oc-name').textContent   = peerName;
        document.getElementById('oc-type-label').textContent = withVideo ? 'Video Call' : 'Audio Call';
        callOverlayOut.classList.remove('hidden');

        // Build PC and send offer
        buildPc();
        const offer = await callPc.createOffer({ offerToReceiveAudio: true, offerToReceiveVideo: withVideo });
        await callPc.setLocalDescription(offer);
        sendSig('offer', { type: offer.type, sdp: offer.sdp, isVideo: withVideo });

        // Poll for answer/ICE
        startPeerPoll();
    }

    // â”€â”€ ACCEPT INCOMING CALL â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('accept-call-btn').addEventListener('click', async () => {
        if (!incomingOffer || !callPeerId) return;
        stopRingtone();
        callOverlayIn.classList.add('hidden');

        try {
            callLocalStream = await navigator.mediaDevices.getUserMedia({
                audio: true, video: callIsVideo
            });
        } catch(e) {
            callToast('Cannot access ' + (callIsVideo ? 'camera/mic' : 'microphone') + '.', 'error');
            hangup(false);
            return;
        }

        elLocalVid.srcObject = callLocalStream;
        elLocalVid.classList.toggle('hidden', !callIsVideo);

        buildPc(); // builds pc and adds tracks from callLocalStream
        await callPc.setRemoteDescription(new RTCSessionDescription(incomingOffer));
        incomingOffer = null;

        // Flush pending ICE
        for (const c of pendingIce) {
            await callPc.addIceCandidate(new RTCIceCandidate(c)).catch(()=>{});
        }
        pendingIce = [];

        const answer = await callPc.createAnswer();
        await callPc.setLocalDescription(answer);
        sendSig('answer', { type: answer.type, sdp: answer.sdp });

        showActiveCall();
        startPeerPoll();
    });

    // â”€â”€ DECLINE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('reject-call-btn').addEventListener('click', () => {
        sendSig('reject', {});
        hangup(false);
        callToast('Call declined.', 'info');
    });

    document.getElementById('cancel-call-btn').addEventListener('click', () => {
        sendSig('end', {});
        hangup(false);
    });

    document.getElementById('end-call-btn').addEventListener('click', () => {
        sendSig('end', {});
        hangup(false);
        callToast('Call ended.', 'info');
    });

    // â”€â”€ CONTROLS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('toggle-mute-btn').addEventListener('click', function() {
        if (!callLocalStream) return;
        callMuted = !callMuted;
        callLocalStream.getAudioTracks().forEach(t => t.enabled = !callMuted);
        this.classList.toggle('bg-rose-500/40', callMuted);
        this.querySelector('span').textContent = callMuted ? 'Unmute' : 'Mute';
    });

    document.getElementById('toggle-camera-btn').addEventListener('click', function() {
        if (!callLocalStream) return;
        callCamOff = !callCamOff;
        callLocalStream.getVideoTracks().forEach(t => t.enabled = !callCamOff);
        this.classList.toggle('bg-rose-500/40', callCamOff);
        this.querySelector('span').textContent = callCamOff ? 'Show Cam' : 'Camera';
    });

    // â”€â”€ HANGUP â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function hangup(sendEnd = true) {
        if (sendEnd && callPeerId) sendSig('end', {});
        stopPeerPoll();
        stopRingtone();
        stopCallTimer();
        if (callPc)          { try { callPc.close(); } catch(e){} callPc = null; }
        if (callLocalStream) { callLocalStream.getTracks().forEach(t => t.stop()); callLocalStream = null; }
        elRemoteVid.srcObject = null;
        elLocalVid.srcObject  = null;
        callOverlayOut.classList.add('hidden');
        callOverlayIn.classList.add('hidden');
        callOverlayAct.classList.add('hidden');
        callPeerId = null; callPeerName = ''; callIsVideo = false;
        callMuted = false; callCamOff = false;
        incomingOffer = null; pendingIce = [];
        document.getElementById('toggle-mute-btn').classList.remove('bg-rose-500/40');
        document.getElementById('toggle-mute-btn').querySelector('span').textContent = 'Mute';
        document.getElementById('toggle-camera-btn').classList.remove('bg-rose-500/40');
        document.getElementById('toggle-camera-btn').querySelector('span').textContent = 'Camera';
    }

    // â”€â”€ ACTIVE CALL UI â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function showActiveCall() {
        callOverlayOut.classList.add('hidden');
        callOverlayIn.classList.add('hidden');
        callOverlayAct.classList.remove('hidden');
        // Audio-only background
        if (!callIsVideo) {
            elAudioBg.classList.remove('hidden');
            elAudioBg.style.display = 'flex';
            document.getElementById('ac-avatar').textContent = callPeerName.charAt(0).toUpperCase();
            document.getElementById('ac-name').textContent   = callPeerName;
        } else {
            elAudioBg.classList.add('hidden');
        }
        document.getElementById('ac-name-top').textContent    = callPeerName;
        document.getElementById('ac-type-label').textContent  = callIsVideo ? 'Video Call' : 'Audio Call';
        startCallTimer();
    }

    // â”€â”€ TIMER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function startCallTimer() {
        callTimerSec = 0;
        if (callTimerIv) clearInterval(callTimerIv);
        callTimerIv = setInterval(() => {
            callTimerSec++;
            const m = String(Math.floor(callTimerSec/60)).padStart(2,'0');
            const s = String(callTimerSec%60).padStart(2,'0');
            elCallTimer.textContent = `${m}:${s}`;
        }, 1000);
    }
    function stopCallTimer() {
        if (callTimerIv) { clearInterval(callTimerIv); callTimerIv = null; }
        elCallTimer.textContent = '00:00';
    }

    // â”€â”€ PEER-SPECIFIC SIGNAL POLL (ICE/answer after call starts) â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function startPeerPoll() {
        if (callPollIv) clearInterval(callPollIv);
        callPollIv = setInterval(async () => {
            if (!callPeerId) return;
            try {
                const r = await fetch(`/api/call/signals?peer_id=${callPeerId}`);
                const d = await r.json();
                for (const sig of (d.signals || [])) {
                    await handleSig(sig.type, JSON.parse(sig.data || '{}'));
                }
            } catch(e) {}
        }, 1000);
    }
    function stopPeerPoll() {
        if (callPollIv) { clearInterval(callPollIv); callPollIv = null; }
    }

    // â”€â”€ GLOBAL INCOMING CALL POLL (runs always, detects new offers) â”€â”€â”€â”€â”€â”€â”€â”€
    setInterval(async () => {
        if (callPeerId || inCallPoll) return;   // already in a call or poll running
        inCallPoll = true;
        try {
            const r = await fetch('/api/call/signals');
            const d = await r.json();
            for (const sig of (d.signals || [])) {
                if (sig.type !== 'offer') continue;
                if (callPeerId) break; // concurrent offer came in

                const offerPayload = JSON.parse(sig.data || '{}');
                callPeerId   = sig.from_user_id;
                callIsVideo  = !!(offerPayload.isVideo);
                incomingOffer = { type: offerPayload.type, sdp: offerPayload.sdp };

                // Figure out caller name
                callPeerName = 'User #' + sig.from_user_id;
                const contactEl = document.querySelector(`.contact-item[data-uid="${sig.from_user_id}"]`);
                if (contactEl) { 
                    callPeerName = contactEl.dataset.name || callPeerName;
                } else {
                    // Quick fallback search if not in contact list
                    try {
                        const rr = await fetch(`/api/users/search?q=${callPeerName}`);
                        const dd = await rr.json();
                        if (dd.users && dd.users.length > 0) callPeerName = dd.users[0].username;
                    } catch(e) {}
                }

                // Show incoming UI
                document.getElementById('ic-avatar').textContent     = callPeerName.charAt(0).toUpperCase();
                document.getElementById('ic-name').textContent        = callPeerName;
                document.getElementById('ic-type-label').textContent  =
                    (callIsVideo ? 'Incoming Video' : 'Incoming Audio') + ' Call';
                callOverlayIn.classList.remove('hidden');

                // Browser Notification (if allowed)
                if (Notification.permission === 'granted') {
                    new Notification('ðŸ“ž Incoming Call', {
                        body: `${callPeerName} is calling you (${callIsVideo ? 'Video' : 'Audio'})`,
                        icon: '/favicon.ico'
                    });
                } else if (Notification.permission !== 'denied') {
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            new Notification('ðŸ“ž Incoming Call', {
                                body: `${callPeerName} is calling you...`,
                                icon: '/favicon.ico'
                            });
                        }
                    });
                }

                startRingtone();
                startPeerPoll();   // start polling for ICE candidates while ringing
                break;
            }
        } catch(e) {}
        inCallPoll = false;
    }, 1500);   // Poll every 1.5 seconds

    // â”€â”€ CALL BUTTONS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('audio-call-btn').addEventListener('click', () => {
        const name = document.getElementById('active-name').textContent.trim();
        if (!activeUid || !name || name === 'User') {
            callToast('Open a conversation first.', 'error'); return;
        }
        startCall(activeUid, name, false);
    });

    document.getElementById('video-call-btn').addEventListener('click', () => {
        const name = document.getElementById('active-name').textContent.trim();
        if (!activeUid || !name || name === 'User') {
            callToast('Open a conversation first.', 'error'); return;
        }
        startCall(activeUid, name, true);
    });

    // ── PAGE VISIBILITY: re-poll immediately when user switches back to tab ──
    // (browsers throttle setInterval in hidden tabs, so we fire immediately on focus)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && !callPeerId) {
            // fire the global poll right away without waiting for next interval
            (async () => {
                if (callPeerId || inCallPoll) return;
                inCallPoll = true;
                try {
                    const r = await fetch('/api/call/signals', { cache: 'no-store' });
                    const d = await r.json();
                    for (const sig of (d.signals || [])) {
                        if (sig.type !== 'offer' || callPeerId) continue;
                        const offerPayload = JSON.parse(sig.data || '{}');
                        callPeerId    = sig.from_user_id;
                        callIsVideo   = !!(offerPayload.isVideo);
                        incomingOffer = { type: offerPayload.type, sdp: offerPayload.sdp };
                        callPeerName  = 'User #' + sig.from_user_id;
                        const el = document.querySelector(`.contact-item[data-uid="${sig.from_user_id}"]`);
                        if (el) callPeerName = el.dataset.name || callPeerName;
                        document.getElementById('ic-avatar').textContent    = callPeerName.charAt(0).toUpperCase();
                        document.getElementById('ic-name').textContent       = callPeerName;
                        document.getElementById('ic-type-label').textContent = (callIsVideo ? 'Incoming Video' : 'Incoming Audio') + ' Call';
                        callOverlayIn.classList.remove('hidden');
                        startRingtone();
                        startPeerPoll();
                        break;
                    }
                } catch(e) {}
                inCallPoll = false;
            })();
        }
    });

    // ── GROUP CREATION LOGIC ──
    const cgBtn = document.getElementById('create-group-btn');
    const cgModal = document.getElementById('create-group-modal');
    const cgCancel = document.getElementById('cg-cancel-btn');
    const cgForm = document.getElementById('create-group-form');

    if(cgBtn) {
        cgBtn.addEventListener('click', () => {
            cgModal.classList.remove('hidden');
        });
    }

    if(cgCancel) {
        cgCancel.addEventListener('click', () => {
            cgModal.classList.add('hidden');
            cgForm.reset();
        });
    }

    if(cgForm) {
        cgForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('cg-name').value.trim();
            const desc = document.getElementById('cg-desc').value.trim();
            const isPrivate = document.getElementById('cg-private').checked ? 1 : 0;

            if(!name) return;

            const fd = new FormData();
            fd.append('name', name);
            fd.append('description', desc);
            fd.append('is_private', isPrivate);

            try {
                const res = await fetch('/api/groups/create', { method: 'POST', body: fd });
                const json = await res.json();
                
                if (json.error) {
                    showToast(json.error, 'error');
                } else if (json.success && json.group_id) {
                    showToast('Group Created successfully!', 'success');
                    cgModal.classList.add('hidden');
                    cgForm.reset();
                    // Custom group object flag added to global var
                    setActiveChat(`g${json.group_id}`, name, `g${json.group_id}-0`, true); // true = isGroup context flag
                }
            } catch(e) {
                showToast('Failed to contact server.', 'error');
            }
        });
    }
    
    </script>
    <!-- CREATE GROUP MODAL -->
    <div id="create-group-modal" class="hidden fixed inset-0 z-[1000] bg-dark-900/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-800 border border-slate-700 w-full max-w-md rounded-2xl shadow-2xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">Create New Group</h2>
            <form id="create-group-form">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Group Name</label>
                    <input type="text" id="cg-name" required class="w-full bg-dark-900 border border-slate-700/50 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:ring-2 focus:ring-brand-500 focus:border-transparent outline-none transition-all" placeholder="E.g. Engineering Team">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Description (Optional)</label>
                    <textarea id="cg-desc" rows="2" class="w-full bg-dark-900 border border-slate-700/50 rounded-xl px-4 py-2 text-white placeholder-slate-500 focus:ring-2 focus:ring-brand-500 focus:border-transparent outline-none transition-all" placeholder="What is this group about?"></textarea>
                </div>
                <div class="mb-6 flex items-center justify-between p-3 bg-dark-900/50 rounded-xl border border-slate-700/30">
                    <div>
                        <p class="text-sm font-medium text-white">Private Group</p>
                        <p class="text-xs text-slate-400">Hidden from global search. Invite only.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="cg-private" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                    </label>
                </div>
                <div class="flex flex-col gap-2">
                    <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl font-medium transition-colors">Create Group</button>
                    <button type="button" id="cg-cancel-btn" class="w-full py-2.5 bg-transparent hover:bg-slate-800 text-slate-300 rounded-xl font-medium transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══════ MEDIA GALLERY MODAL ═══════ -->
    <div id="media-gallery-modal" class="hidden fixed inset-0 z-[80] bg-black/70 flex items-center justify-center p-4">
        <div class="bg-dark-800 rounded-2xl w-full max-w-2xl max-h-[85vh] flex flex-col border border-slate-700/50 shadow-2xl" style="animation:slideInRight .3s ease">
            <div class="flex items-center justify-between p-4 border-b border-slate-700/50">
                <h3 class="text-lg font-bold text-white">📁 Shared Media</h3>
                <button onclick="document.getElementById('media-gallery-modal').classList.add('hidden')" class="p-1.5 hover:bg-slate-700/50 rounded-lg transition-colors text-slate-400">✕</button>
            </div>
            <!-- Tabs -->
            <div class="flex border-b border-slate-700/50 px-4" id="mg-tabs">
                <button class="mg-tab px-4 py-2.5 text-sm font-medium text-brand-400 border-b-2 border-brand-500" data-tab="photos">Photos</button>
                <button class="mg-tab px-4 py-2.5 text-sm font-medium text-slate-400 border-b-2 border-transparent hover:text-white" data-tab="videos">Videos</button>
                <button class="mg-tab px-4 py-2.5 text-sm font-medium text-slate-400 border-b-2 border-transparent hover:text-white" data-tab="documents">Documents</button>
                <button class="mg-tab px-4 py-2.5 text-sm font-medium text-slate-400 border-b-2 border-transparent hover:text-white" data-tab="links">Links</button>
                <div class="flex-1"></div>
                <button id="mg-view-toggle" class="p-2 text-slate-400 hover:text-white" title="Toggle grid/list view">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4" id="mg-content">
                <p class="text-center text-slate-500 py-8">No media found</p>
            </div>
        </div>
    </div>

    <!-- ═══════ FILE MANAGER MODAL ═══════ -->
    <div id="file-manager-modal" class="hidden fixed inset-0 z-[80] bg-black/70 flex items-center justify-center p-4">
        <div class="bg-dark-800 rounded-2xl w-full max-w-3xl max-h-[90vh] flex flex-col border border-slate-700/50 shadow-2xl" style="animation:slideInRight .3s ease">
            <div class="flex items-center justify-between p-4 border-b border-slate-700/50">
                <h3 class="text-lg font-bold text-white">🗂️ File Manager</h3>
                <div class="flex items-center gap-2">
                    <button id="fm-delete-selected" class="hidden px-3 py-1.5 rounded-lg bg-rose-600/20 text-rose-400 text-xs font-semibold hover:bg-rose-600/40 transition-colors">🗑️ Delete Selected</button>
                    <button id="fm-forward-selected" class="hidden px-3 py-1.5 rounded-lg bg-brand-600/20 text-brand-300 text-xs font-semibold hover:bg-brand-600/40 transition-colors">↗️ Forward</button>
                    <button onclick="document.getElementById('file-manager-modal').classList.add('hidden')" class="p-1.5 hover:bg-slate-700/50 rounded-lg transition-colors text-slate-400">✕</button>
                </div>
            </div>
            <div class="flex border-b border-slate-700/50 px-4" id="fm-tabs">
                <button class="fm-tab px-4 py-2.5 text-sm font-medium text-brand-400 border-b-2 border-brand-500" data-tab="photos">📷 Photos</button>
                <button class="fm-tab px-4 py-2.5 text-sm font-medium text-slate-400 border-b-2 border-transparent hover:text-white" data-tab="videos">🎬 Videos</button>
                <button class="fm-tab px-4 py-2.5 text-sm font-medium text-slate-400 border-b-2 border-transparent hover:text-white" data-tab="documents">📄 Documents</button>
                <button class="fm-tab px-4 py-2.5 text-sm font-medium text-slate-400 border-b-2 border-transparent hover:text-white" data-tab="links">🔗 Links</button>
                <div class="flex-1"></div>
                <button id="fm-view-toggle" class="p-2 text-slate-400 hover:text-white" title="Toggle grid/list">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4" id="fm-content">
                <p class="text-center text-slate-500 py-8">Loading...</p>
            </div>
        </div>
    </div>

    <!-- ═══════ FORWARD MODAL (Contact List) ═══════ -->
    <div id="forward-modal" class="hidden fixed inset-0 z-[90] bg-black/70 flex items-center justify-center p-4">
        <div class="bg-dark-800 rounded-2xl w-full max-w-sm max-h-[75vh] flex flex-col border border-slate-700/50 shadow-2xl" style="animation:slideInRight .3s ease">
            <div class="flex items-center justify-between p-4 border-b border-slate-700/50">
                <h3 class="text-lg font-bold text-white">↗️ Forward To</h3>
                <button onclick="document.getElementById('forward-modal').classList.add('hidden')" class="p-1.5 hover:bg-slate-700/50 rounded-lg transition-colors text-slate-400">✕</button>
            </div>
            <p class="text-xs text-slate-400 px-4 pt-2">Select up to 10 contacts</p>
            <div class="flex-1 overflow-y-auto p-3 space-y-1" id="fwd-contact-list">
                <p class="text-center text-slate-500 py-4">Loading contacts...</p>
            </div>
            <div class="p-3 border-t border-slate-700/50">
                <button id="fwd-send-btn" class="w-full py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl font-semibold transition-colors disabled:opacity-50" disabled>Send (0 selected)</button>
            </div>
        </div>
    </div>

    <script>
    // ═══════ MEDIA GALLERY ═══════
    let mgViewMode = 'grid';
    let mgData = {};
    let mgActiveTab = 'photos';

    function openMediaGallery(type, id) {
        const url = type === 'group' ? `/api/groups/media?group_id=${id}` : `/api/chat/media?conversation_id=${id}`;
        document.getElementById('media-gallery-modal').classList.remove('hidden');
        document.getElementById('mg-content').innerHTML = '<p class="text-center text-slate-500 py-8">Loading...</p>';
        fetch(url).then(r=>r.json()).then(d => {
            mgData = d;
            mgActiveTab = 'photos';
            document.querySelectorAll('.mg-tab').forEach(t => { t.classList.remove('text-brand-400','border-brand-500'); t.classList.add('text-slate-400','border-transparent'); });
            document.querySelector('.mg-tab[data-tab="photos"]').classList.add('text-brand-400','border-brand-500');
            renderMediaTab();
        }).catch(() => document.getElementById('mg-content').innerHTML = '<p class="text-center text-rose-400 py-8">Failed to load media</p>');
    }

    document.querySelectorAll('.mg-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            mgActiveTab = tab.dataset.tab;
            document.querySelectorAll('.mg-tab').forEach(t => { t.classList.remove('text-brand-400','border-brand-500'); t.classList.add('text-slate-400','border-transparent'); });
            tab.classList.add('text-brand-400','border-brand-500');
            tab.classList.remove('text-slate-400','border-transparent');
            renderMediaTab();
        });
    });
    document.getElementById('mg-view-toggle').addEventListener('click', () => { mgViewMode = mgViewMode === 'grid' ? 'list' : 'grid'; renderMediaTab(); });

    function renderMediaTab() {
        const items = mgData[mgActiveTab] || [];
        const el = document.getElementById('mg-content');
        if (!items.length) { el.innerHTML = '<p class="text-center text-slate-500 py-8">No ' + mgActiveTab + ' found</p>'; return; }
        if (mgActiveTab === 'links') {
            el.innerHTML = items.map(l => `<div class="flex items-center gap-3 p-3 rounded-xl hover:bg-dark-900/50 border border-slate-700/30 mb-2">
                <span class="text-2xl">🔗</span>
                <div class="flex-1 min-w-0"><a href="${l.url}" target="_blank" class="text-sm text-brand-400 hover:underline truncate block">${l.url}</a>
                <p class="text-[10px] text-slate-500">${l.sender} · ${new Date(l.date).toLocaleDateString()}</p></div></div>`).join('');
            return;
        }
        if (mgViewMode === 'grid') {
            el.innerHTML = '<div class="grid grid-cols-3 gap-2">' + items.map(f => {
                const isImg = f.mime && f.mime.startsWith('image/');
                const isVid = f.mime && f.mime.startsWith('video/');
                const streamUrl = `/api/file/stream/${f.file_id}`;
                return `<div class="aspect-square rounded-xl overflow-hidden border border-slate-700/30 relative group bg-dark-900 flex items-center justify-center cursor-pointer" onclick="openMgViewer('${streamUrl}', '${f.name.replace(/'/g,"\\'")}', ${f.file_id}, '${f.mime}')">
                    ${isImg ? `<img src="${streamUrl}" class="w-full h-full object-cover" alt="${f.name}" loading="lazy">` : `<div class="text-center p-2"><span class="text-2xl">${isVid?'🎬':'📄'}</span><p class="text-[10px] text-slate-400 truncate mt-1">${f.name}</p></div>`}
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-end justify-center gap-2 transition-opacity pb-8">
                        <a href="${streamUrl}" download="${f.name}" class="p-1.5 bg-white/20 rounded-lg text-white hover:bg-white/40 text-xs" title="Download" onclick="event.stopPropagation()">⬇️</a>
                        <button class="p-1.5 bg-white/20 rounded-lg text-white hover:bg-white/40 text-xs" title="Forward" onclick="event.stopPropagation(); forwardMediaFromGallery(${f.file_id}, '${f.name.replace(/'/g,"\\'")}')">↗️</button>
                    </div>
                    <p class="absolute bottom-0 left-0 right-0 bg-black/70 text-[9px] text-slate-300 p-1 truncate">${f.sender} · ${new Date(f.date).toLocaleDateString()}</p>
                </div>`;
            }).join('') + '</div>';
        } else {
            el.innerHTML = items.map(f => {
                const streamUrl = `/api/file/stream/${f.file_id}`;
                return `<div class="flex items-center gap-3 p-3 rounded-xl hover:bg-dark-900/50 border border-slate-700/30 mb-2 cursor-pointer" onclick="openMgViewer('${streamUrl}', '${f.name.replace(/'/g,"\\'")}', ${f.file_id}, '${f.mime}')">
                <span class="text-2xl">${f.mime&&f.mime.startsWith('image/')?'🖼️':f.mime&&f.mime.startsWith('video/')?'🎬':'📄'}</span>
                <div class="flex-1 min-w-0"><p class="text-sm text-white truncate">${f.name}</p>
                <p class="text-[10px] text-slate-500">${f.sender} · ${new Date(f.date).toLocaleDateString()} · ${(f.size/1024).toFixed(1)}KB</p></div>
                <button class="p-1.5 hover:bg-slate-700/50 rounded-lg text-slate-400 hover:text-white text-sm" title="Forward" onclick="event.stopPropagation(); forwardMediaFromGallery(${f.file_id}, '${f.name.replace(/'/g,"\\'")}')">↗️</button>
                <a href="${streamUrl}" download="${f.name}" class="p-1.5 hover:bg-slate-700/50 rounded-lg text-slate-400 hover:text-white text-sm" onclick="event.stopPropagation()">⬇️</a>
            </div>`;
            }).join('');
        }
    }

    // ═══════ MEDIA GALLERY VIEWER OVERLAY ═══════
    function openMgViewer(url, name, fileId, mime) {
        // Close the media gallery modal
        document.getElementById('media-gallery-modal').classList.add('hidden');
        
        if (mime && mime.startsWith('image/')) {
            // Create a fullscreen image viewer overlay
            let overlay = document.getElementById('mg-viewer-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'mg-viewer-overlay';
                overlay.className = 'fixed inset-0 z-[200] bg-black/95 flex flex-col items-center justify-center';
                overlay.innerHTML = `
                    <div class="absolute top-0 left-0 right-0 flex items-center justify-between p-4 bg-gradient-to-b from-black/80 to-transparent z-10">
                        <div class="flex items-center gap-3">
                            <span class="text-white/60">🖼️</span>
                            <span id="mg-viewer-name" class="text-white font-medium text-sm"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a id="mg-viewer-download" href="#" download class="p-2 hover:bg-white/20 rounded-lg text-white transition-colors" title="Download">⬇️</a>
                            <button id="mg-viewer-forward" class="p-2 hover:bg-white/20 rounded-lg text-white transition-colors" title="Forward">↗️</button>
                            <button id="mg-viewer-close" class="p-2 hover:bg-white/20 rounded-lg text-white transition-colors" title="Close">✕</button>
                        </div>
                    </div>
                    <img id="mg-viewer-img" class="max-w-[90vw] max-h-[85vh] object-contain rounded-lg shadow-2xl" src="" alt="">
                `;
                document.body.appendChild(overlay);
                document.getElementById('mg-viewer-close').addEventListener('click', () => { overlay.classList.add('hidden'); });
                overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.add('hidden'); });
            }
            document.getElementById('mg-viewer-name').textContent = name;
            document.getElementById('mg-viewer-img').src = url;
            document.getElementById('mg-viewer-download').href = url;
            document.getElementById('mg-viewer-download').download = name;
            document.getElementById('mg-viewer-forward').onclick = () => { overlay.classList.add('hidden'); forwardMediaFromGallery(fileId, name); };
            overlay.classList.remove('hidden');
        } else if (mime && mime.startsWith('video/')) {
            // For videos, open in a new tab
            window.open(url, '_blank');
        } else {
            // For documents, trigger download
            const a = document.createElement('a');
            a.href = url; a.download = name; a.click();
        }
    }

    // Forward a file from the media gallery
    function forwardMediaFromGallery(fileId, fileName) {
        // Show the forward modal and set up forwarding
        const fwdModal = document.getElementById('forward-modal');
        fwdModal.classList.remove('hidden');
        // Load contacts for forwarding
        fetch('/api/chat/contacts').then(r=>r.json()).then(data => {
            const listEl = document.getElementById('forward-contact-list');
            if (!listEl) return;
            listEl.innerHTML = '';
            (data.contacts || []).forEach(c => {
                const btn = document.createElement('button');
                btn.className = 'w-full flex items-center gap-3 p-3 rounded-xl hover:bg-dark-800 transition-colors text-left';
                btn.innerHTML = `<div class="w-10 h-10 rounded-full bg-dark-800 text-slate-300 flex items-center justify-center font-bold border border-slate-700">${(c.username||'?')[0].toUpperCase()}</div>
                    <div class="flex-1 min-w-0"><p class="text-sm text-white font-medium truncate">${c.username}</p></div>`;
                btn.onclick = () => {
                    // Forward the file to this user
                    const formData = new FormData();
                    formData.append('file_id', fileId);
                    formData.append('receiver_id', c.id);
                    fetch('/api/chat/forward', { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(resp => {
                            if (resp.success || resp.status === 'ok') {
                                fwdModal.classList.add('hidden');
                                showToast(`📤 "${fileName}" forwarded to ${c.username}`);
                            } else {
                                showToast('❌ Forward failed: ' + (resp.error || 'Unknown error'));
                            }
                        }).catch(() => showToast('❌ Network error'));
                };
                listEl.appendChild(btn);
            });
        });
    }

    function showToast(msg) {
        let t = document.getElementById('mg-toast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'mg-toast';
            t.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[300] px-5 py-3 bg-dark-800 border border-slate-700 rounded-xl text-white text-sm shadow-2xl transition-all';
            document.body.appendChild(t);
        }
        t.textContent = msg;
        t.style.opacity = '1';
        setTimeout(() => { t.style.opacity = '0'; }, 3000);
    }

    // ═══════ FILE MANAGER ═══════
    let fmViewMode = 'grid';
    let fmData = {};
    let fmActiveTab = 'photos';
    let fmSelectedFiles = new Set();

    function openFileManager() {
        document.getElementById('file-manager-modal').classList.remove('hidden');
        document.getElementById('fm-content').innerHTML = '<p class="text-center text-slate-500 py-8">Loading...</p>';
        fmSelectedFiles.clear();
        updateFmSelection();
        fetch('/api/files/all').then(r=>r.json()).then(d => {
            fmData = d;
            fmActiveTab = 'photos';
            document.querySelectorAll('.fm-tab').forEach(t => { t.classList.remove('text-brand-400','border-brand-500'); t.classList.add('text-slate-400','border-transparent'); });
            document.querySelector('.fm-tab[data-tab="photos"]').classList.add('text-brand-400','border-brand-500');
            renderFmTab();
        }).catch(() => document.getElementById('fm-content').innerHTML = '<p class="text-center text-rose-400 py-8">Failed to load</p>');
    }

    document.querySelectorAll('.fm-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            fmActiveTab = tab.dataset.tab;
            document.querySelectorAll('.fm-tab').forEach(t => { t.classList.remove('text-brand-400','border-brand-500'); t.classList.add('text-slate-400','border-transparent'); });
            tab.classList.add('text-brand-400','border-brand-500');
            tab.classList.remove('text-slate-400','border-transparent');
            fmSelectedFiles.clear();
            updateFmSelection();
            renderFmTab();
        });
    });
    document.getElementById('fm-view-toggle').addEventListener('click', () => { fmViewMode = fmViewMode === 'grid' ? 'list' : 'grid'; renderFmTab(); });

    function updateFmSelection() {
        const count = fmSelectedFiles.size;
        document.getElementById('fm-delete-selected').classList.toggle('hidden', count === 0);
        document.getElementById('fm-forward-selected').classList.toggle('hidden', count === 0);
    }

    function toggleFmSelect(fileId) {
        if (fmSelectedFiles.has(fileId)) fmSelectedFiles.delete(fileId);
        else fmSelectedFiles.add(fileId);
        updateFmSelection();
        document.querySelectorAll('.fm-checkbox').forEach(cb => { cb.checked = fmSelectedFiles.has(parseInt(cb.dataset.fid)); });
    }

    function renderFmTab() {
        const items = fmData[fmActiveTab] || [];
        const el = document.getElementById('fm-content');
        if (!items.length) { el.innerHTML = '<p class="text-center text-slate-500 py-8">No ' + fmActiveTab + '</p>'; return; }
        if (fmActiveTab === 'links') {
            el.innerHTML = items.map(l => `<div class="flex items-center gap-3 p-3 rounded-xl hover:bg-dark-900/50 border border-slate-700/30 mb-2">
                <span class="text-2xl">🔗</span>
                <div class="flex-1 min-w-0"><a href="${l.url}" target="_blank" class="text-sm text-brand-400 hover:underline truncate block">${l.url}</a>
                <p class="text-[10px] text-slate-500">${l.sender} · ${l.direction} · ${new Date(l.date).toLocaleDateString()}</p></div></div>`).join('');
            return;
        }
        if (fmViewMode === 'grid') {
            el.innerHTML = '<div class="grid grid-cols-3 gap-3">' + items.map(f => {
                const isImg = f.mime && f.mime.startsWith('image/');
                const isVid = f.mime && f.mime.startsWith('video/');
                const streamUrl = `/api/file/stream/${f.file_id}`;
                const checked = fmSelectedFiles.has(f.file_id) ? 'checked' : '';
                return `<div class="rounded-xl overflow-hidden border border-slate-700/30 relative group bg-dark-900 flex flex-col">
                    <div class="aspect-square relative cursor-pointer flex items-center justify-center" onclick="openMgViewer('${streamUrl}', '${f.name.replace(/'/g,"\\'")}', ${f.file_id}, '${f.mime}')">
                        <input type="checkbox" class="fm-checkbox absolute top-2 left-2 z-10 w-4 h-4 accent-brand-500" data-fid="${f.file_id}" ${checked} onclick="event.stopPropagation(); toggleFmSelect(${f.file_id})">
                        ${isImg ? `<img src="${streamUrl}" class="w-full h-full object-cover" alt="${f.name}" loading="lazy">` : `<div class="text-center p-2"><span class="text-3xl">${isVid?'🎬':'📄'}</span><p class="text-[10px] text-slate-400 truncate mt-1">${f.name}</p></div>`}
                    </div>
                    <div class="p-2 border-t border-slate-700/30 bg-dark-800/50">
                        <p class="text-[10px] text-slate-400 truncate mb-1.5">${f.name}</p>
                        <div class="flex items-center gap-1">
                            <button class="flex-1 py-1 px-2 rounded-lg bg-rose-600/15 hover:bg-rose-600/30 text-rose-400 text-[10px] font-medium transition-colors" onclick="deleteSingleFile(${f.file_id})" title="Delete">🗑️ Delete</button>
                            <button class="flex-1 py-1 px-2 rounded-lg bg-brand-600/15 hover:bg-brand-600/30 text-brand-300 text-[10px] font-medium transition-colors" onclick="forwardMediaFromGallery(${f.file_id}, '${f.name.replace(/'/g,"\\'")}')" title="Forward">↗️ Forward</button>
                            <a href="${streamUrl}" download="${f.name}" class="py-1 px-2 rounded-lg bg-slate-700/30 hover:bg-slate-700/50 text-slate-300 text-[10px] font-medium transition-colors" onclick="event.stopPropagation()">⬇️</a>
                        </div>
                    </div>
                </div>`;
            }).join('') + '</div>';
        } else {
            el.innerHTML = items.map(f => {
                const streamUrl = `/api/file/stream/${f.file_id}`;
                const checked = fmSelectedFiles.has(f.file_id) ? 'checked' : '';
                return `<div class="flex items-center gap-3 p-3 rounded-xl hover:bg-dark-900/50 border border-slate-700/30 mb-2">
                    <input type="checkbox" class="fm-checkbox w-4 h-4 accent-brand-500" data-fid="${f.file_id}" ${checked} onclick="toggleFmSelect(${f.file_id})">
                    <span class="text-2xl cursor-pointer" onclick="openMgViewer('${streamUrl}', '${f.name.replace(/'/g,"\\'")}', ${f.file_id}, '${f.mime}')">${f.mime&&f.mime.startsWith('image/')?'🖼️':f.mime&&f.mime.startsWith('video/')?'🎬':'📄'}</span>
                    <div class="flex-1 min-w-0 cursor-pointer" onclick="openMgViewer('${streamUrl}', '${f.name.replace(/'/g,"\\'")}', ${f.file_id}, '${f.mime}')"><p class="text-sm text-white truncate">${f.name}</p>
                    <p class="text-[10px] text-slate-500">${f.sender} · ${f.direction} · ${new Date(f.date).toLocaleDateString()} · ${(f.size/1024).toFixed(1)}KB</p></div>
                    <button class="p-1.5 hover:bg-rose-600/30 rounded-lg text-rose-400 text-sm" title="Delete" onclick="deleteSingleFile(${f.file_id})">🗑️</button>
                    <button class="p-1.5 hover:bg-brand-600/30 rounded-lg text-brand-300 text-sm" title="Forward" onclick="forwardMediaFromGallery(${f.file_id}, '${f.name.replace(/'/g,"\\'")}')" >↗️</button>
                    <a href="${streamUrl}" download="${f.name}" class="p-1.5 hover:bg-slate-700/50 rounded-lg text-slate-400 hover:text-white text-sm">⬇️</a>
                </div>`;
            }).join('');
        }
    }

    // Delete a single file with confirmation
    function deleteSingleFile(fileId) {
        if (!confirm('Delete this file?')) return;
        const fd = new FormData();
        fd.append('file_ids[]', fileId);
        fetch('/api/files/delete', { method: 'POST', body: fd }).then(r=>r.json()).then(d => {
            if (d.success) { showToast('🗑️ File deleted'); openFileManager(); }
            else showToast('❌ ' + (d.error || 'Failed'));
        });
    }

    document.getElementById('fm-delete-selected').addEventListener('click', () => {
        if (!fmSelectedFiles.size) return;
        if (!confirm(`Delete ${fmSelectedFiles.size} file(s)?`)) return;
        const fd = new FormData();
        fmSelectedFiles.forEach(id => fd.append('file_ids[]', id));
        fetch('/api/files/delete', { method: 'POST', body: fd }).then(r=>r.json()).then(d => {
            if (d.success) { showToast(`Deleted ${d.deleted} file(s)`); openFileManager(); }
            else showToast(d.error || 'Failed', 'error');
        });
    });
    document.getElementById('fm-forward-selected').addEventListener('click', () => { openForwardModal(null, Array.from(fmSelectedFiles)); });

    // ═══════ FORWARD MODAL ═══════
    let fwdMessageIds = [];
    let fwdSelectedUsers = new Set();

    function openForwardModal(messageIds, fileIds) {
        fwdMessageIds = messageIds || [];
        fwdSelectedUsers.clear();
        document.getElementById('forward-modal').classList.remove('hidden');
        document.getElementById('fwd-contact-list').innerHTML = '<p class="text-center text-slate-500 py-4">Loading contacts...</p>';
        updateFwdBtn();
        fetch('/api/chat/contacts').then(r=>r.json()).then(d => {
            const list = document.getElementById('fwd-contact-list');
            if (!d.contacts || !d.contacts.length) { list.innerHTML = '<p class="text-center text-slate-500 py-4">No contacts</p>'; return; }
            list.innerHTML = d.contacts.map(c => `<label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-dark-900/50 cursor-pointer transition-colors">
                <input type="checkbox" class="fwd-user-cb w-4 h-4 accent-brand-500" data-uid="${c.id}" onchange="toggleFwdUser(${c.id})">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-600 to-indigo-600 flex items-center justify-center text-xs font-bold text-white ${c.avatar_url ? 'bg-cover bg-center' : ''}" ${c.avatar_url ? `style="background-image:url(${c.avatar_url})"` : ''}>${c.avatar_url ? '' : c.username.charAt(0).toUpperCase()}</div>
                <div class="flex-1 min-w-0"><p class="text-sm text-white truncate">${c.username}</p></div>
            </label>`).join('');
        });
    }

    function toggleFwdUser(uid) {
        if (fwdSelectedUsers.has(uid)) fwdSelectedUsers.delete(uid);
        else if (fwdSelectedUsers.size < 10) fwdSelectedUsers.add(uid);
        else { showToast('Maximum 10 recipients', 'error'); document.querySelector(`.fwd-user-cb[data-uid="${uid}"]`).checked = false; return; }
        updateFwdBtn();
    }

    function updateFwdBtn() {
        const btn = document.getElementById('fwd-send-btn');
        btn.textContent = `Send (${fwdSelectedUsers.size} selected)`;
        btn.disabled = fwdSelectedUsers.size === 0;
    }

    document.getElementById('fwd-send-btn').addEventListener('click', () => {
        if (!fwdSelectedUsers.size || !fwdMessageIds.length) return;
        const fd = new FormData();
        fwdMessageIds.forEach(id => fd.append('message_ids[]', id));
        fwdSelectedUsers.forEach(uid => fd.append('to_user_ids[]', uid));
        fetch('/api/chat/forward', { method: 'POST', body: fd }).then(r=>r.json()).then(d => {
            if (d.success) { showToast(`Forwarded to ${fwdSelectedUsers.size} user(s)!`); document.getElementById('forward-modal').classList.add('hidden'); }
            else showToast(d.error || 'Failed', 'error');
        });
    });

    // ═══════ GROUP PHOTO REMOVAL ═══════
    window.removeGroupAvatar = function() {
        if (!currentGroupId || !confirm('Remove group avatar?')) return;
        fetch('/api/groups/avatar/remove', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({group_id:currentGroupId}) })
            .then(r=>r.json()).then(d => { if(d.success){ showToast('Avatar removed'); fetchGroupInfo(currentGroupId); } else showToast(d.error||'Failed','error'); });
    };
    window.removeGroupCover = function() {
        if (!currentGroupId || !confirm('Remove group cover?')) return;
        fetch('/api/groups/cover/remove', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({group_id:currentGroupId}) })
            .then(r=>r.json()).then(d => { if(d.success){ showToast('Cover removed'); fetchGroupInfo(currentGroupId); } else showToast(d.error||'Failed','error'); });
    };
    </script>

</body>
</html>

