<?php
$sessionLifetime = 86400 * 30;
@ini_set('session.gc_maxlifetime', $sessionLifetime);
session_set_cookie_params([
    'lifetime' => $sessionLifetime,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: chat.php');
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - App Chat</title>
    <link rel="manifest" href="manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#2ea6ff">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
    body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="icon-192.png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 md:bg-gray-50" style="font-family: 'Inter', system-ui, -apple-system, sans-serif;">

<div id="auth-section" class="flex min-h-dvh flex-col justify-center px-4 sm:px-6 py-12" style="background: linear-gradient(135deg, #eef6ff 0%, #e7f3ff 50%, #f0f9ff 100%);">
    <div class="w-full max-w-sm mx-auto">
        <div class="flex justify-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200/50" style="background: linear-gradient(135deg, #2ea6ff, #1e96ef);">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
        </div>
        <h2 id="auth-title" class="mt-6 text-center text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Selamat Datang</h2>
        <p id="auth-subtitle" class="mt-2 text-center text-sm text-gray-500">Masuk untuk melanjutkan ke App Chat</p>
    </div>

    <div class="mt-8 w-full max-w-sm mx-auto">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 px-6 sm:px-8 py-8">
            <div id="auth-error" class="hidden bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span id="auth-error-text"></span>
            </div>

            <form id="login-form" class="space-y-5">
                <div>
                    <label for="login-email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                        <input id="login-email" type="email" required placeholder="you@example.com" class="block w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                    </div>
                </div>
                <div>
                    <label for="login-password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        </div>
                        <input id="login-password" type="password" required placeholder="••••••••" class="block w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                    </div>
                </div>
                <button type="submit" class="flex w-full justify-center rounded-xl px-3 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-200/50 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ea6ff] focus-visible:ring-offset-2 transition-all hover:brightness-110" style="background: linear-gradient(135deg, #2ea6ff, #1e96ef);">Masuk</button>
                <p class="text-center text-sm text-gray-500">Belum punya akun? <a href="#" id="show-register" class="font-semibold hover:brightness-110 transition" style="color: #2ea6ff;">Daftar</a></p>
            </form>

            <form id="register-form" class="space-y-5 hidden">
                <div>
                    <label for="reg-name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </div>
                        <input id="reg-name" type="text" required placeholder="Nama Anda" class="block w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                    </div>
                </div>
                <div>
                    <label for="reg-email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                        <input id="reg-email" type="email" required placeholder="you@example.com" class="block w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                    </div>
                </div>
                <div>
                    <label for="reg-password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        </div>
                        <input id="reg-password" type="password" required placeholder="••••••••" class="block w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] focus:border-transparent transition">
                    </div>
                </div>
                <button type="submit" class="flex w-full justify-center rounded-xl px-3 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-200/50 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ea6ff] focus-visible:ring-offset-2 transition-all hover:brightness-110" style="background: linear-gradient(135deg, #2ea6ff, #1e96ef);">Buat Akun</button>
                <p class="text-center text-sm text-gray-500">Sudah punya akun? <a href="#" id="show-login" class="font-semibold hover:brightness-110 transition" style="color: #2ea6ff;">Masuk</a></p>
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

document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    document.getElementById('auth-error').classList.add('hidden');
    try {
        const res = await fetch('api.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: document.getElementById('login-email').value,
                password: document.getElementById('login-password').value
            })
        });
        const data = await res.json();
        if (data.status === 'success') {
            window.location.replace('chat.php');
        } else {
            document.getElementById('auth-error-text').textContent = data.message;
            document.getElementById('auth-error').classList.remove('hidden');
        }
    } catch (err) {
        document.getElementById('auth-error-text').textContent = 'Kesalahan: ' + err.message;
        document.getElementById('auth-error').classList.remove('hidden');
    }
});

document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    document.getElementById('auth-error').classList.add('hidden');
    try {
        const res = await fetch('api.php?action=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                name: document.getElementById('reg-name').value,
                email: document.getElementById('reg-email').value,
                password: document.getElementById('reg-password').value
            })
        });
        const data = await res.json();
        if (data.status === 'success') {
            window.location.replace('chat.php');
        } else {
            document.getElementById('auth-error-text').textContent = data.message;
            document.getElementById('auth-error').classList.remove('hidden');
        }
    } catch (err) {
        document.getElementById('auth-error-text').textContent = 'Kesalahan: ' + err.message;
        document.getElementById('auth-error').classList.remove('hidden');
    }
});
</script>
</body>
</html>
