<?php
$sessionLifetime = 86400 * 30;
@ini_set('session.gc_maxlifetime', $sessionLifetime);
@ini_set('session.gc_probability', 0);
session_set_cookie_params(['lifetime' => $sessionLifetime, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();
?><!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Chat</title>
    <link rel="manifest" href="manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#2ea6ff">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="icon-192.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <script>
    // Theme initialization
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>
<style>
    /* Add smooth transitions for theme changes */
    body { transition: background-color 0.2s, color 0.2s; }
    .dark .bg-white { background-color: #1f2937; }
    .dark .text-gray-900 { color: #f9fafb; }
    .dark .text-gray-500 { color: #9ca3af; }
    .dark .text-gray-700 { color: #d1d5db; }
    .dark .bg-gray-100 { background-color: #111827; }
    .dark .bg-gray-50 { background-color: #030712; }
    .dark #form-chat { background: #1f2937; }
    .dark .bubble-other { background: #374151; color: #f9fafb; }
    .dark .bg-\[\#e7ebf0\] { background-color: #111827; }
    html, body { overscroll-behavior: none; }
    input, button, a, .cursor-pointer { touch-action: manipulation; }
    #form-chat { padding-bottom: env(safe-area-inset-bottom, 0px); background: white; }
    @media (max-width: 767px) { #form-chat { box-shadow: 0 -2px 10px rgba(0,0,0,0.06); position: sticky; bottom: 0; z-index: 15; } }
    #empty-state.hidden { display: none !important; }
    #wrapper-chat > .msg-block { display: flex; flex-direction: column; }
    #wrapper-chat > .msg-block + .msg-block { margin-top: 10px; }
    .msg-col { display: flex; flex-direction: column; gap: 3px; max-width: 88%; }
    @media (min-width: 768px) { .msg-col { max-width: 55%; } }
    .msg-col-own { justify-content: flex-end; align-items: flex-end; }
    .msg-col-other { justify-content: flex-start; align-items: flex-start; }
    .msg-col-own .msg-col { align-items: flex-end; }
    .msg-col-other .msg-col { align-items: flex-start; }
    .msg-item { display: flex; flex-direction: column; max-width: 100%; }
    .msg-col-own .msg-item { align-items: flex-end; }
    .msg-col-other .msg-item { align-items: flex-start; }
    @media (min-width: 768px) { #back-to-accounts { display: none; } }
    @media (max-width: 767px) { #app-section { height: 100dvh; max-width: 100%; border-radius: 0; box-shadow: none; border: none; } #chat-view { position: fixed; inset: 0; z-index: 10; } #accounts-view { position: relative; z-index: 1; } }
    #user-list { overscroll-behavior: contain; }
    .bubble-own { background: #2ea6ff; color: white; border-radius: 16px 16px 4px 16px; padding: 8px 12px; max-width: 100%; word-wrap: break-word; line-height: 1.35; font-size: 14px; box-shadow: 0 1px 1px rgba(0,0,0,0.06); }
    .bubble-other { background: white; color: #222; border-radius: 16px 16px 16px 4px; padding: 8px 12px; max-width: 100%; word-wrap: break-word; line-height: 1.35; font-size: 14px; box-shadow: 0 1px 1px rgba(0,0,0,0.05); }
    .dark .bubble-other { background: #374151; color: #f9fafb; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    .msg-item:not(:last-child) .bubble-own { border-radius: 16px 16px 4px 16px; }
    .msg-item:not(:first-child) .bubble-own { border-radius: 16px 4px 4px 16px; }
    .msg-item:not(:first-child):not(:last-child) .bubble-own { border-radius: 16px 4px 4px 16px; }
    .msg-item:not(:first-child) .bubble-other { border-radius: 4px 16px 16px 16px; }
    .msg-meta { display: inline-flex; align-items: center; gap: 6px; margin-top: 3px; font-size: 11px; line-height: 1; opacity: 0.85; }
    .msg-meta-own { justify-content: flex-end; color: #9aa5b1; }
    .msg-meta-other { justify-content: flex-start; color: #6b7280; }
    .read-receipt { font-size: 12px; color: #2ea6ff; }
    .user-item.active { background: #e0f0ff; }
    .dark .user-item.active { background: #1e3a8a; }
    .user-item.active h2 { color: #1a73e8; }
    .dark .user-item.active h2 { color: #60a5fa; }
    .msg-actions { position: absolute; right: 0; top: 100%; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 30; min-width: 120px; overflow: hidden; }
    .dark .msg-actions { background: #374151; }
    .msg-actions button { display: block; width: 100%; padding: 10px 16px; text-align: left; font-size: 13px; background: none; border: none; cursor: pointer; transition: background 0.15s; color: #374151; }
    .dark .msg-actions button { color: #e5e7eb; }
    .msg-actions button:hover { background: #f3f4f6; }
    .dark .msg-actions button:hover { background: #4b5563; }
    .msg-item { position: relative; }
    
    /* Logout modal */
    .logout-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; display: none; align-items: center; justify-content: center; padding: 16px; backdrop-filter: blur(2px); animation: fadeIn 0.15s ease; }
    .logout-overlay.show { display: flex; }
    .logout-card { background: white; border-radius: 20px; padding: 28px 24px 20px; max-width: 320px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.2); animation: scaleIn 0.2s ease; }
    .dark .logout-card { background: #1f2937; }
    .logout-card svg { margin: 0 auto 12px; }
    .logout-card h3 { font-size: 18px; font-weight: 700; color: #1f2937; margin-bottom: 6px; }
    .dark .logout-card h3 { color: #f9fafb; }
    .logout-card p { font-size: 14px; color: #6b7280; margin-bottom: 24px; line-height: 1.4; }
    .dark .logout-card p { color: #9ca3af; }
    .logout-card .btn-group { display: flex; gap: 10px; }
    .logout-card button { flex: 1; padding: 11px 0; border-radius: 12px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: all 0.15s; }
    .logout-card .btn-cancel { background: #f3f4f6; color: #4b5563; }
    .dark .logout-card .btn-cancel { background: #374151; color: #d1d5db; }
    .logout-card .btn-cancel:hover { background: #e5e7eb; }
    .dark .logout-card .btn-cancel:hover { background: #4b5563; }
    .logout-card .btn-logout { background: #ef4444; color: white; }
    .logout-card .btn-logout:hover { background: #dc2626; }
    .logout-card .btn-logout:active, .logout-card .btn-cancel:active { transform: scale(0.97); }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes scaleIn { from { transform: scale(0.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>
</head>
<body class="bg-gray-100 dark:bg-gray-950 md:bg-gray-50 dark:md:bg-gray-950" style="font-family: 'Inter', system-ui, -apple-system, sans-serif;">

<?php if (!isset($_SESSION['user_id'])): ?>
<h1 class="sr-only">Masuk ke App Chat</h1>
<div id="auth-section" class="flex min-h-dvh flex-col justify-center px-4 sm:px-6 py-12 dark:bg-gray-900" style="background: linear-gradient(135deg, #eef6ff 0%, #e7f3ff 50%, #f0f9ff 100%);">
    <div class="w-full max-w-sm mx-auto">
        <div class="flex justify-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200/50" style="background: linear-gradient(135deg, #2ea6ff, #1e96ef);">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
        </div>
        <h2 id="auth-title" class="mt-8 text-center text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Selamat Datang</h2>
        <p id="auth-subtitle" class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Masuk untuk melanjutkan ke App Chat</p>
    </div>

    <div class="mt-8 w-full max-w-sm mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-gray-200/60 dark:shadow-gray-900/60 px-8 sm:px-10 py-10">
            <div id="auth-error" role="alert" class="hidden bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span id="auth-error-text"></span>
            </div>

            <form id="login-form" class="space-y-5">
                <div>
                    <label for="login-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                         <input id="login-email" type="email" required placeholder="you@example.com" autocomplete="email" class="block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white pl-10 pr-3 py-3 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                    </div>
                </div>
                <div>
                    <label for="login-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kata Sandi</label>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        </div>
                         <input id="login-password" type="password" required placeholder="••••••••" autocomplete="current-password" class="block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white pl-10 pr-10 py-3 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                         <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                             <svg class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                             <svg class="h-5 w-5 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                         </button>
                    </div>
                </div>
                <button type="submit" class="flex w-full justify-center rounded-xl px-3 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-200/50 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ea6ff] focus-visible:ring-offset-2 transition-all hover:brightness-110" style="background: linear-gradient(135deg, #2ea6ff, #1e96ef);">Masuk</button>
                <p class="text-center text-sm text-gray-600 dark:text-gray-400">Belum punya akun? <a href="#" id="show-register" class="font-semibold hover:brightness-110 transition" style="color: #2ea6ff;">Daftar</a></p>
            </form>

            <form id="register-form" class="space-y-5 hidden">
                <div>
                    <label for="reg-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </div>
                         <input id="reg-name" type="text" required placeholder="Nama Anda" autocomplete="name" class="block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white pl-10 pr-3 py-3 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                    </div>
                </div>
                <div>
                    <label for="reg-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                         <input id="reg-email" type="email" required placeholder="you@example.com" autocomplete="email" class="block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white pl-10 pr-3 py-3 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                    </div>
                </div>
                <div>
                    <label for="reg-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kata Sandi</label>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        </div>
                         <input id="reg-password" type="password" required placeholder="••••••••" autocomplete="new-password" class="block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white pl-10 pr-10 py-3 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                         <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                             <svg class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                             <svg class="h-5 w-5 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                         </button>
                    </div>
                </div>
                <button type="submit" class="flex w-full justify-center rounded-xl px-3 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-200/50 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ea6ff] focus-visible:ring-offset-2 transition-all hover:brightness-110" style="background: linear-gradient(135deg, #2ea6ff, #1e96ef);">Buat Akun</button>
                <p class="text-center text-sm text-gray-600 dark:text-gray-400">Sudah punya akun? <a href="#" id="show-login" class="font-semibold hover:brightness-110 transition" style="color: #2ea6ff;">Masuk</a></p>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('show-register').addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('login-form').classList.add('hidden');
    document.getElementById('register-form').classList.remove('hidden');
    document.getElementById('auth-title').textContent = 'Buat Akun';
    document.getElementById('auth-subtitle').textContent = 'Bergabung dengan App Chat dan mulai chatting';
});

document.getElementById('show-login').addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('register-form').classList.add('hidden');
    document.getElementById('login-form').classList.remove('hidden');
    document.getElementById('auth-title').textContent = 'Selamat Datang';
    document.getElementById('auth-subtitle').textContent = 'Masuk untuk melanjutkan ke App Chat';
});

document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = btn.closest('.relative').querySelector('input');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.querySelector('.eye-open').classList.toggle('hidden', isPassword);
        btn.querySelector('.eye-closed').classList.toggle('hidden', !isPassword);
    });
});

document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    document.getElementById('auth-error').classList.add('hidden');
    const email = document.getElementById('login-email');
    const pass = document.getElementById('login-password');
    const btn = e.target.querySelector('button[type="submit"]');
    
    // Validation
    if (!email.value.includes('@') || !email.value.includes('.')) {
        document.getElementById('auth-error-text').textContent = 'Format email tidak valid';
        document.getElementById('auth-error').classList.remove('hidden');
        return;
    }

    // Loading state
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    try {
        const res = await fetch('api.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email.value, password: pass.value })
        });
        const data = await res.json();
        if (data.status === 'success') {
            window.location.replace('');
        } else {
            document.getElementById('auth-error-text').textContent = data.message;
            document.getElementById('auth-error').classList.remove('hidden');
        }
    } catch (err) {
        document.getElementById('auth-error-text').textContent = 'Kesalahan: ' + err.message;
        document.getElementById('auth-error').classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    document.getElementById('auth-error').classList.add('hidden');
    const name = document.getElementById('reg-name');
    const email = document.getElementById('reg-email');
    const pass = document.getElementById('reg-password');
    const btn = e.target.querySelector('button[type="submit"]');

    // Validation
    if (!email.value.includes('@') || !email.value.includes('.')) {
        document.getElementById('auth-error-text').textContent = 'Format email tidak valid';
        document.getElementById('auth-error').classList.remove('hidden');
        return;
    }
    if (pass.value.length < 6) {
        document.getElementById('auth-error-text').textContent = 'Kata sandi minimal 6 karakter';
        document.getElementById('auth-error').classList.remove('hidden');
        return;
    }

    // Loading state
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    try {
        const res = await fetch('api.php?action=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: name.value, email: email.value, password: pass.value })
        });
        const data = await res.json();
        if (data.status === 'success') {
            window.location.replace('');
        } else {
            document.getElementById('auth-error-text').textContent = data.message;
            document.getElementById('auth-error').classList.remove('hidden');
        }
    } catch (err) {
        document.getElementById('auth-error-text').textContent = 'Kesalahan: ' + err.message;
        document.getElementById('auth-error').classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});
</script>

<?php else: ?>
<h1 class="sr-only">App Chat</h1>
<div id="app-section" class="h-dvh md:h-screen flex flex-col md:flex-row w-full bg-white dark:bg-gray-900 md:bg-[#e7ebf0] dark:md:bg-gray-950 overflow-hidden relative">

    <div id="logout-overlay" class="logout-overlay" onclick="if(event.target===this)closeLogoutModal()">
        <div class="logout-card">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <h3>Keluar</h3>
            <p>Apakah Anda yakin ingin keluar dari akun ini?</p>
            <div class="btn-group">
                <button class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
                <button class="btn-logout" onclick="window.location.href='logout.php'">Keluar</button>
            </div>
        </div>
    </div>
    <div id="settings-overlay" class="fixed inset-0 bg-black/40 z-20 hidden opacity-0 transition-opacity duration-200" onclick="closeSettings()"></div>
    <div id="settings-panel" class="fixed inset-y-0 left-0 z-30 w-72 bg-white dark:bg-gray-900 shadow-xl transform -translate-x-full transition-transform duration-300 flex flex-col">
        <div class="bg-[#2ea6ff] dark:bg-[#1e40af] p-5 text-white">
            <div class="flex items-center gap-3">
                <div id="settings-avatar" class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-xl font-bold">?</div>
                <div class="min-w-0">
                    <div id="settings-name" class="font-semibold text-base truncate">-</div>
                    <div id="settings-email" class="text-sm text-white/70 truncate">-</div>
                </div>
            </div>
        </div>
        <div class="p-3 space-y-1 flex-1 overflow-y-auto">
            <div id="settings-form-section" class="px-3 py-4 space-y-3 hidden">
                <div id="settings-msg" class="text-xs hidden"></div>
                <input id="set-name" type="text" placeholder="Nama" class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input id="set-email" type="email" placeholder="Email" class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input id="set-new-password" type="password" placeholder="Kata sandi baru" class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input id="set-current-password" type="password" placeholder="Kata sandi saat ini *" class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button id="set-save-btn" class="w-full bg-[#2ea6ff] dark:bg-blue-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-[#1e96ef] transition">Simpan Perubahan</button>
                <button id="set-cancel-btn" class="w-full text-gray-500 text-sm py-1.5 hover:text-gray-700 transition">Batal</button>
            </div>
            <div id="settings-menu">
                <button onclick="toggleTheme()" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg id="theme-icon-dark" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" /></svg>
                    <svg id="theme-icon-light" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                    <span id="theme-text">Ganti ke Mode Gelap</span>
                </button>
                <button id="settings-edit-profile-btn" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Profil
                </button>
                <button onclick="showLogoutModal()" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1044 21 19 21H15"/><path d="M10 17L15 12L10 7"/><path d="M15 12H3"/></svg>
                    Keluar
                </button>
            </div>
        </div>
    </div>

    <div id="accounts-view" class="flex flex-col flex-1 md:flex md:flex-none md:w-80 md:border-r md:border-gray-200 dark:md:border-gray-800 md:bg-white dark:md:bg-gray-900 min-h-0">
        <div class="bg-[#2ea6ff] px-4 py-3 text-white flex items-center justify-center shrink-0 relative">
             <button id="settings-btn" aria-label="Pengaturan" class="absolute left-4 hover:bg-[#1e96ef] rounded-lg p-2 transition">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="6" r="4" stroke="#ffffff" stroke-width="1.5"/>
                    <path d="M15 20.6151C14.0907 20.8619 13.0736 21 12 21C8.13401 21 5 19.2091 5 17C5 14.7909 8.13401 13 12 13C15.866 13 19 14.791 19 17C19 17.3453 18.923 17.6804 18.775 18" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
            <span class="font-semibold text-base">Pesan</span>
        </div>
        <div id="user-list" class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-950 md:bg-white dark:md:bg-gray-900 min-h-0"></div>
    </div>

    <div id="chat-view" class="hidden md:flex flex flex-col flex-1 min-h-0 min-w-0 bg-[#e7ebf0]">
        <div id="empty-state" class="hidden absolute inset-0 z-20 flex-col items-center justify-center text-gray-400 dark:text-gray-500 px-6 bg-[#e7ebf0] dark:bg-gray-900">
            <svg class="w-24 h-24 text-gray-300 mb-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p class="text-lg font-medium text-gray-500">Pilih user untuk mulai chat</p>
            <p class="text-sm text-gray-400 mt-1">Pilih percakapan dari sidebar kiri</p>
        </div>

        <div id="chat-inner" class="hidden flex flex-col flex-1 min-h-0">
            <div class="bg-[#2ea6ff] dark:bg-blue-900 px-4 py-3 text-white flex items-center shrink-0 relative">
                 <button id="back-to-accounts" aria-label="Kembali ke Daftar Pesan" class="hover:bg-[#1e96ef] rounded-lg p-2 transition mr-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19L5 12L12 5"/></svg>
                </button>
                <span id="chat-title" class="font-semibold text-base truncate">Chat</span>
            </div>
            <div id="chat-container" class="flex-1 overflow-y-auto min-h-0 px-3 py-4 bg-[#e7ebf0] dark:bg-gray-900">
                <div id="chat-loading" class="hidden text-center text-gray-400 text-sm py-8">Memuat pesan...</div>
                <div id="wrapper-chat"></div>
            </div>
            <form id="form-chat" class="shrink-0">
                <div class="bg-white px-3 py-2 flex items-center gap-2">
                    <input type="text" name="content" placeholder="Tulis pesan..." class="flex-1 border-0 rounded-2xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] bg-[#f0f4f8] dark:bg-gray-700 dark:text-white transition min-h-[44px]" id="content" autocomplete="off">
                     <button type="submit" aria-label="Kirim Pesan" class="bg-[#2ea6ff] text-white rounded-full w-[44px] h-[44px] hover:bg-[#1e96ef] active:scale-95 transition-all flex items-center justify-center shrink-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
let userId = null;
let receiverId = null;
let editingMessageId = null;
let isEditing = false;
const wrapperChat = document.getElementById('wrapper-chat');
const chatContainer = document.getElementById('chat-container');
const form = document.getElementById('form-chat');
const contentInput = document.getElementById('content');

function isDesktop() { return window.innerWidth >= 768; }

async function apiFetch(url, options) {
    const res = await fetch(url, options);
    const text = await res.text();
    let data;
    try { data = JSON.parse(text); } catch (e) { throw new Error('Invalid JSON response'); }
    if (data.status === 'error' && data.message === 'Unauthorized') { window.location.replace(''); throw new Error('Unauthorized'); }
    return data;
}

function showAccounts() {
    receiverId = null;
    document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
    if (!isDesktop()) { document.getElementById('accounts-view').classList.remove('hidden'); document.getElementById('chat-view').classList.add('hidden'); }
    document.getElementById('empty-state').classList.remove('hidden');
    document.getElementById('chat-inner').classList.add('hidden');
    if (pusher) { pusher.unsubscribe('chat'); }
}

let chatSwitching = false;

function setActiveUser(id) {
    document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
    const active = document.querySelector(`.user-item[data-user-id="${id}"]`);
    if (active) active.classList.add('active');
}

function showChat(id) {
    if (chatSwitching) return;
    if (id === receiverId) return;
    chatSwitching = true;
    receiverId = id;
    console.log('showChat: receiverId =', receiverId);
    setActiveUser(id);
    if (!isDesktop()) { document.getElementById('accounts-view').classList.add('hidden'); document.getElementById('chat-view').classList.remove('hidden'); }
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('chat-inner').classList.remove('hidden');
    wrapperChat.innerHTML = '';
    document.getElementById('chat-loading').classList.remove('hidden');
    loadChatTitle().then(() => loadMessages()).then(() => { initPusher(); }).finally(() => { document.getElementById('chat-loading').classList.add('hidden'); chatSwitching = false; });
}

document.getElementById('back-to-accounts').addEventListener('click', showAccounts);

const settingsBtn = document.getElementById('settings-btn');
const settingsPanel = document.getElementById('settings-panel');
const settingsOverlay = document.getElementById('settings-overlay');

function openSettings() {
    settingsOverlay.classList.remove('hidden');
    requestAnimationFrame(() => { settingsOverlay.classList.remove('opacity-0'); settingsPanel.classList.remove('-translate-x-full'); });
}
function closeSettings() {
    settingsOverlay.classList.add('opacity-0');
    settingsPanel.classList.add('-translate-x-full');
    document.getElementById('settings-form-section').classList.add('hidden');
    document.getElementById('settings-menu').classList.remove('hidden');
    setTimeout(() => settingsOverlay.classList.add('hidden'), 200);
}
settingsBtn.addEventListener('click', openSettings);

async function loadProfile() {
    try {
        const data = await apiFetch('api.php?action=getProfile');
        if (data.status === 'success') {
            const u = data.user;
            document.getElementById('settings-name').textContent = u.name.charAt(0).toUpperCase() + u.name.slice(1);
            document.getElementById('settings-email').textContent = u.email;
            document.getElementById('settings-avatar').textContent = u.name.charAt(0).toUpperCase();
        }
    } catch (err) {}
}

document.getElementById('settings-edit-profile-btn').addEventListener('click', async () => {
    document.getElementById('settings-menu').classList.add('hidden');
    const section = document.getElementById('settings-form-section');
    section.classList.remove('hidden');
    document.getElementById('settings-msg').classList.add('hidden');
    try {
        const data = await apiFetch('api.php?action=getProfile');
        if (data.status === 'success') { document.getElementById('set-name').value = data.user.name; document.getElementById('set-email').value = data.user.email; }
    } catch (err) {}
    document.getElementById('set-new-password').value = '';
    document.getElementById('set-current-password').value = '';
});

document.getElementById('set-cancel-btn').addEventListener('click', () => {
    document.getElementById('settings-form-section').classList.add('hidden');
    document.getElementById('settings-menu').classList.remove('hidden');
});

document.getElementById('set-save-btn').addEventListener('click', async () => {
    const msg = document.getElementById('settings-msg');
    msg.classList.add('hidden');
    const currentPassword = document.getElementById('set-current-password').value;
    if (!currentPassword) { msg.textContent = 'Kata sandi saat ini wajib diisi'; msg.className = 'text-xs text-red-500'; msg.classList.remove('hidden'); return; }
    try {
        const data = await apiFetch('api.php?action=updateProfile', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: document.getElementById('set-name').value.trim(), email: document.getElementById('set-email').value.trim(), currentPassword, newPassword: document.getElementById('set-new-password').value })
        });
        if (data.status === 'success') { msg.textContent = 'Profil berhasil diperbarui'; msg.className = 'text-xs text-green-600'; msg.classList.remove('hidden'); document.getElementById('set-new-password').value = ''; document.getElementById('set-current-password').value = ''; loadProfile(); }
        else { msg.textContent = data.message; msg.className = 'text-xs text-red-500'; msg.classList.remove('hidden'); }
    } catch (err) { msg.textContent = 'Kesalahan: ' + err.message; msg.className = 'text-xs text-red-500'; msg.classList.remove('hidden'); }
});

async function loadUsers() {
    try {
        const data = await apiFetch('api.php?action=getUsers');
        const container = document.getElementById('user-list');
    container.innerHTML = '';
    data.users.forEach((user, index) => {
        const name = user.name.charAt(0).toUpperCase() + user.name.slice(1);
        const initial = user.name.charAt(0).toUpperCase();
        const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-amber-500', 'bg-teal-500', 'bg-pink-500'];
        const color = colors[index % colors.length];
        const div = document.createElement('div');
        div.className = 'user-item flex items-center px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-800 active:bg-gray-200 dark:active:bg-gray-700 transition cursor-pointer min-h-[56px]';
        div.dataset.userId = user.id;
        div.onclick = () => showChat(user.id);
        div.innerHTML = `<div class="w-11 h-11 ${color} rounded-full flex items-center justify-center text-white font-semibold text-base shrink-0 shadow-sm">${initial}</div><div class="ml-3 flex-1 min-w-0"><h2 class="text-[15px] font-semibold text-gray-900">${escapeHtml(name)}</h2><p class="text-xs text-gray-400 truncate">Ketuk untuk mulai chat</p></div>`;
        container.appendChild(div);
    });
} catch (err) { document.getElementById('user-list').innerHTML = '<div class="text-center text-gray-400 text-sm py-8">Koneksi terputus. Silakan periksa jaringan Anda.</div>'; }
}

function escapeHtml(text) { const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }

async function loadChatTitle() {
    try {
        const data = await apiFetch('api.php?action=getUserName&id=' + receiverId);
        if (data.status === 'success') { document.getElementById('chat-title').textContent = data.name.charAt(0).toUpperCase() + data.name.slice(1); }
    } catch (err) {}
}

async function loadMessages() {
    try {
        const data = await apiFetch('api.php?action=getMessages&receiver_id=' + receiverId);
        if (data.status === 'error') { wrapperChat.innerHTML = '<div class="text-center text-gray-400 text-sm py-8">' + escapeHtml(data.message) + '</div>'; return; }
        userId = data.user_id;
        wrapperChat.innerHTML = '';
        data.messages.forEach(msg => appendMessage(msg.id, msg.sender_id, msg.content, msg.created_at));
        scrollToBottom();
    } catch (err) { if (err.message === 'Unauthorized') throw err; wrapperChat.innerHTML = '<div class="text-center text-gray-400 text-sm py-8">Koneksi terputus. Silakan periksa jaringan Anda.</div>'; }
}

function buildMeta(isOwn, timeText) {
    const meta = document.createElement('div');
    meta.className = 'msg-meta ' + (isOwn ? 'msg-meta-own' : 'msg-meta-other');
    meta.innerHTML = `<span>${escapeHtml(timeText)}</span>`;
    return meta;
}

function formatTime(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return '';
    return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function appendMessage(messageId, senderId, content, createdAt) {
    const isOwn = senderId == userId;
    let timeText;
    if (messageId && String(messageId).startsWith('temp-')) { timeText = 'Mengirim...'; }
    else if (createdAt) { timeText = formatTime(createdAt); }
    else { timeText = ''; }
    const item = document.createElement('div');
    item.className = 'msg-item';
    item.dataset.messageId = messageId;
    const bubble = document.createElement('div');
    bubble.className = isOwn ? 'bubble-own' : 'bubble-other';
    bubble.textContent = content;
    item.appendChild(bubble);
    item.appendChild(buildMeta(isOwn, timeText));
    if (isOwn && messageId && !String(messageId).startsWith('temp-')) {
        bubble.style.cursor = 'pointer';
        let actions = null;
        bubble.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isEditing) return;
            if (actions && actions.parentNode) {
                actions.remove();
                actions = null;
                return;
            }
            document.querySelectorAll('.msg-actions').forEach(m => m.remove());
            actions = document.createElement('div');
            actions.className = 'msg-actions';
            actions.innerHTML = '<button class="edit-action">Edit</button><button class="delete-action">Hapus</button>';
            item.appendChild(actions);
            actions.querySelector('.edit-action').addEventListener('click', function(ev) { ev.stopPropagation(); actions.remove(); actions = null; editMessage(messageId, bubble); });
            actions.querySelector('.delete-action').addEventListener('click', function(ev) { ev.stopPropagation(); actions.remove(); actions = null; deleteMessage(messageId, item); });
        });
    }
    const lastWrapper = wrapperChat.lastElementChild;
    const sameSender = lastWrapper && lastWrapper.dataset.senderId == String(senderId);
    if (sameSender) {
        const existingColumn = lastWrapper.querySelector('.msg-col');
        if (existingColumn) { existingColumn.appendChild(item); return; }
    }
    const wrapper = document.createElement('div');
    wrapper.dataset.senderId = senderId;
    wrapper.className = 'msg-block ' + (isOwn ? 'msg-col-own' : 'msg-col-other');
    const col = document.createElement('div');
    col.className = 'msg-col';
    col.appendChild(item);
    wrapper.appendChild(col);
    wrapperChat.appendChild(wrapper);
}

document.addEventListener('click', function(e) { document.querySelectorAll('.msg-actions').forEach(menu => { if (menu.parentNode && !menu.contains(e.target)) { menu.remove(); } }); });

function editMessage(messageId, bubble) {
    isEditing = true;
    const originalText = bubble.textContent;
    const input = document.createElement('input'); input.type = 'text'; input.value = originalText; input.className = 'w-full bg-white text-gray-800 p-2 rounded-lg border border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm';
    const actions = document.createElement('div'); actions.className = 'flex gap-1 mt-1 justify-end';
    const saveBtn = document.createElement('button'); saveBtn.type = 'button'; saveBtn.className = 'text-xs bg-[#2ea6ff] text-white px-2.5 py-1 rounded-lg hover:bg-[#1e96ef] transition'; saveBtn.textContent = 'Simpan';
    const cancelBtn = document.createElement('button'); cancelBtn.type = 'button'; cancelBtn.className = 'text-xs bg-gray-200 text-gray-600 px-2.5 py-1 rounded-lg hover:bg-gray-300 transition'; cancelBtn.textContent = 'Batal';
    actions.appendChild(cancelBtn); actions.appendChild(saveBtn);
    bubble.innerHTML = ''; bubble.className = 'bg-white p-2 rounded-2xl rounded-br-sm shadow-sm border border-gray-200';
    bubble.appendChild(input); bubble.appendChild(actions); input.focus(); input.setSelectionRange(input.value.length, input.value.length);
    cancelBtn.onclick = function() { cancelEdit(messageId, bubble, originalText); };
    input.addEventListener('keydown', function(e) { if (e.key === 'Enter') saveEdit(messageId, bubble, input.value); else if (e.key === 'Escape') cancelEdit(messageId, bubble, originalText); });
    saveBtn.onclick = function() { saveEdit(messageId, bubble, input.value); };
}

async function saveEdit(messageId, bubble, newContent) {
    newContent = newContent.trim(); if (!newContent || editingMessageId) return;
    editingMessageId = messageId;
    try { await apiFetch('api.php?action=editMessage', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ messageId, content: newContent }) }); } catch (err) {}
    bubble.innerHTML = ''; bubble.className = 'bubble-own'; bubble.textContent = newContent; editingMessageId = null; isEditing = false;
}

function cancelEdit(messageId, bubble, originalText) { bubble.innerHTML = ''; bubble.className = 'bubble-own'; bubble.textContent = originalText; isEditing = false; }

async function deleteMessage(messageId, item) {
    if (!confirm('Hapus pesan ini?')) return;
    try {
        const data = await apiFetch('api.php?action=deleteMessage', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ messageId }) });
        if (data.status === 'success') { const col = item.parentElement; const block = col ? col.parentElement : null; item.remove(); if (col && col.children.length === 0 && block) { block.remove(); } }
        else { alert('Gagal menghapus: ' + data.message); }
    } catch (err) { if (err.message === 'Unauthorized') throw err; alert('Gagal menghapus pesan'); }
}

function attachMessageActions(item, messageId) {
    const bubble = item.querySelector('.bubble-own');
    if (!bubble) return;
    let act = null;
    bubble.style.cursor = 'pointer';
    bubble.addEventListener('click', function(e) {
        e.stopPropagation();
        if (isEditing) return;
        if (act && act.parentNode) { act.remove(); act = null; return; }
        document.querySelectorAll('.msg-actions').forEach(m => m.remove());
        act = document.createElement('div');
        act.className = 'msg-actions';
        act.innerHTML = '<button class="edit-action">Edit</button><button class="delete-action">Hapus</button>';
        item.appendChild(act);
        act.querySelector('.edit-action').addEventListener('click', function(ev) { ev.stopPropagation(); act.remove(); act = null; editMessage(messageId, bubble); });
        act.querySelector('.delete-action').addEventListener('click', function(ev) { ev.stopPropagation(); act.remove(); act = null; deleteMessage(messageId, item); });
    });
}

function scrollToBottom() { chatContainer.scrollTop = chatContainer.scrollHeight; }

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const chatContent = contentInput.value.trim();
    if (!chatContent) return;
    const tempId = 'temp-' + Date.now();
    appendMessage(tempId, userId, chatContent);
    scrollToBottom();
    try {
        const data = await apiFetch('api.php?action=saveChat', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ content: chatContent, receiverId }) });
        if (data.status === 'success' && data.message_id) {
            const el = wrapperChat.querySelector(`[data-message-id="${tempId}"]`);
            if (el) { el.dataset.messageId = data.message_id; const meta = el.querySelector('.msg-meta span'); if (meta && meta.textContent === 'Mengirim...') { meta.textContent = formatTime(new Date().toISOString()); } attachMessageActions(el, data.message_id); }
        }
    } catch (err) { console.error('Error:', err); }
    contentInput.value = '';
});

contentInput.addEventListener('keydown', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.dispatchEvent(new Event('submit')); } });

let pusher = null;
let channel = null;

function initPusher() {
    if (pusher) { pusher.disconnect(); }
    pusher = new Pusher('579716c8e98cf3fb7f38', { cluster: 'ap1' });
    channel = pusher.subscribe('chat');
    channel.bind('receive', function(data) { if (data.sender_id == userId) return; if (data.sender_id != receiverId) return; appendMessage(data.message_id, data.sender_id, data.content, data.created_at); scrollToBottom(); });
    channel.bind('edit', function(data) { const isForThisChat = (data.sender_id == userId && data.receiver_id == receiverId) || (data.sender_id == receiverId && data.receiver_id == userId); if (!isForThisChat) return; const item = wrapperChat.querySelector(`[data-message-id="${data.message_id}"]`); if (item) { const bubble = item.querySelector('.bubble-own, .bubble-other'); if (bubble) bubble.textContent = data.content; } });
    channel.bind('delete', function(data) { const isForThisChat = (data.sender_id == userId && data.receiver_id == receiverId) || (data.sender_id == receiverId && data.receiver_id == userId); if (!isForThisChat) return; const item = wrapperChat.querySelector(`[data-message-id="${data.message_id}"]`); if (item) { const col = item.parentElement; const block = col ? col.parentElement : null; item.remove(); if (col && col.children.length === 0 && block) { block.remove(); } } });
}

function showLogoutModal() { document.getElementById('logout-overlay').classList.add('show'); }
function closeLogoutModal() { document.getElementById('logout-overlay').classList.remove('show'); }

loadUsers();
loadProfile();

if ('serviceWorker' in navigator) { navigator.serviceWorker.register('sw.js'); }

function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
        document.getElementById('theme-text').textContent = 'Ganti ke Mode Gelap';
        document.getElementById('theme-icon-light').classList.remove('hidden');
        document.getElementById('theme-icon-dark').classList.add('hidden');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
        document.getElementById('theme-text').textContent = 'Ganti ke Mode Terang';
        document.getElementById('theme-icon-dark').classList.remove('hidden');
        document.getElementById('theme-icon-light').classList.add('hidden');
    }
}
updateThemeIcons();

function updateThemeIcons() {
    if (document.documentElement.classList.contains('dark')) {
        document.getElementById('theme-text').textContent = 'Ganti ke Mode Terang';
        document.getElementById('theme-icon-dark').classList.remove('hidden');
        document.getElementById('theme-icon-light').classList.add('hidden');
    } else {
        document.getElementById('theme-text').textContent = 'Ganti ke Mode Gelap';
        document.getElementById('theme-icon-light').classList.remove('hidden');
        document.getElementById('theme-icon-dark').classList.add('hidden');
    }
}
</script></div>

<?php endif; ?>

</body>
</html>
