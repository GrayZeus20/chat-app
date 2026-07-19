<?php
$sessionLifetime = 86400 * 30;
@ini_set('session.gc_maxlifetime', $sessionLifetime);
@ini_set('session.gc_probability', 0);
session_set_cookie_params(['lifetime' => $sessionLifetime, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Ngobrol</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="apple-touch-icon" href="logo.png">
    <link rel="manifest" href="manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: { extend: {
                colors: {
                    "primary":"#2563eb","on-primary":"#ffffff","primary-container":"#d9eafd","on-primary-container":"#1e40af",
                    "secondary":"#bccdcc","on-secondary":"#1e293b","secondary-container":"#e2eeed","on-secondary-container":"#2d4a48",
                    "tertiary":"#9aa6b2","on-tertiary":"#ffffff","tertiary-container":"#dce4eb","on-tertiary-container":"#3b4a56",
                    "error":"#dc2626","on-error":"#ffffff","error-container":"#fee2e2","on-error-container":"#991b1b",
                    "background":"#f8fafc","on-background":"#1e293b",
                    "surface":"#ffffff","on-surface":"#1e293b","surface-dim":"#f1f5f9","surface-variant":"#e2e8f0","on-surface-variant":"#475569",
                    "outline":"#94a3b8","outline-variant":"#e2e8f0",
                    "inverse-surface":"#1e293b","inverse-on-surface":"#f8fafc","inverse-primary":"#93c5fd",
                    "surface-bright":"#ffffff","surface-container-lowest":"#ffffff","surface-container-low":"#f8fafc",
                    "surface-container":"#f1f5f9","surface-container-high":"#e8eef6","surface-container-highest":"#dde5f0"
                },
                "borderRadius":{"DEFAULT":"0.75rem","lg":"1rem","xl":"1.25rem","2xl":"1.5rem","full":"9999px"},
                "spacing":{"sidebar":"300px","gutter":"24px"},
                "fontFamily":{"display":["Inter"],"body":["Inter"]},
                "fontSize":{
                    "display":["32px",{"lineHeight":"40px","letterSpacing":"-0.02em","fontWeight":"700"}],
                    "headline":["20px",{"lineHeight":"28px","letterSpacing":"-0.01em","fontWeight":"600"}],
                    "title":["16px",{"lineHeight":"24px","fontWeight":"600"}],
                    "body":["14px",{"lineHeight":"20px","fontWeight":"400"}],
                    "label":["12px",{"lineHeight":"16px","letterSpacing":"0.01em","fontWeight":"500"}],
                    "tiny":["11px",{"lineHeight":"14px","letterSpacing":"0.02em","fontWeight":"500"}]
                }
            }}
        }
    </script>
    <script>if(localStorage.theme==='dark'){document.documentElement.classList.add('dark');}</script>
    <style>
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;vertical-align:middle}
        body{font-family:'Inter',system-ui,-apple-system,sans-serif;overscroll-behavior:none;transition:background-color .3s,color .3s}
        input,button,a,.cursor-pointer{touch-action:manipulation}
        .scrollbar-hide::-webkit-scrollbar{display:none}.scrollbar-hide{-ms-overflow-style:none;scrollbar-width:none}
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}
        .custom-scrollbar::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:10px}
        .dark .custom-scrollbar::-webkit-scrollbar-thumb{background:#34343d}
        .placeholder-outline::placeholder{color:#64748b;opacity:1}
        .msg-meta{display:inline-flex;align-items:center;gap:4px;margin-top:4px;font-size:11px}
        .msg-meta-own{justify-content:flex-end;color:#64748b;font-weight:500}
        .msg-meta-other{justify-content:flex-start;color:#64748b;font-weight:500}
        .dark .msg-meta-own,.dark .msg-meta-other{color:#a1a1aa}
        .bubble-own{background-color:#2563eb;color:#fff;border-radius:20px 20px 6px 20px;padding:10px 14px;max-width:100%;word-wrap:break-word;line-height:1.4;font-size:14px;box-shadow:0 2px 8px rgba(37,99,235,.15);user-select:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;-webkit-touch-callout:none;-webkit-tap-highlight-color:transparent}
        .dark .bubble-own{background-color:#c0c1ff!important;color:#1000a9!important}
        .bubble-other{background-color:#fff;color:#1e293b;border-radius:20px 20px 20px 6px;padding:10px 14px;max-width:100%;word-wrap:break-word;line-height:1.4;font-size:14px;box-shadow:0 1px 4px rgba(0,0,0,.08);user-select:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;-webkit-touch-callout:none;-webkit-tap-highlight-color:transparent;border:1px solid #e2e8f0}
        .dark .bubble-other{background-color:#292932!important;color:#e4e1ed!important;border-color:#464554!important}
        .msg-item:not(:last-child) .bubble-own{border-radius:20px 20px 6px 20px}
        .msg-item:not(:first-child) .bubble-own{border-radius:20px 6px 6px 20px}
        .msg-item:not(:first-child):not(:last-child) .bubble-own{border-radius:20px 6px 6px 20px}
        .msg-item:not(:first-child) .bubble-other{border-radius:6px 20px 20px 20px}
        .msg-item{position:relative;display:flex;flex-direction:column;max-width:100%}
        .msg-col{display:flex;flex-direction:column;gap:4px;max-width:80%}
        @media(min-width:768px){.msg-col{max-width:55%}}
        .msg-col-own{justify-content:flex-end;align-items:flex-end}
        .msg-col-other{justify-content:flex-start;align-items:flex-start}
        .msg-block{display:flex;flex-direction:column}
        .msg-block+.msg-block{margin-top:12px}
        .msg-status{font-size:14px;margin-left:2px;vertical-align:middle}
        .msg-status-read{color:#2563eb}.dark .msg-status-read{color:#c0c1ff}
        .msg-actions{position:absolute;right:0;top:100%;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.12);z-index:30;min-width:130px;overflow:hidden;margin-top:4px;border:1px solid #e2e8f0}
        .dark .msg-actions{background:#292932!important;border-color:#464554!important}
        .msg-actions button{display:flex;align-items:center;gap:8px;width:100%;padding:10px 16px;text-align:left;font-size:13px;background:none;border:none;cursor:pointer;transition:background .15s;color:#1e293b}
        .dark .msg-actions button{color:#e4e1ed!important}
        .msg-actions button:hover{background:#f1f5f9}
        .dark .msg-actions button:hover{background:#34343d!important}
        .user-item{transition:all .15s}
        .user-item.active{background:#d9eafd;border-left:3px solid #2563eb}
        .dark .user-item.active{background:#1f1f27!important;border-left-color:#c0c1ff!important}
        .user-item.active h2{color:#2563eb}
        .dark .user-item.active h2{color:#c0c1ff!important}
        #form-chat{padding-bottom:env(safe-area-inset-bottom,0px)}
        @media(max-width:767px){#form-chat{box-shadow:0 -4px 20px rgba(0,0,0,.06);position:sticky;bottom:0;z-index:15}.dark #form-chat{box-shadow:0 -4px 20px rgba(0,0,0,.3)}}
        .settings-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:20;display:none;align-items:stretch;backdrop-filter:blur(4px);animation:fadeIn .2s ease}
        .settings-overlay.show{display:flex}
        .logout-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:50;display:none;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(4px);animation:fadeIn .15s ease}
        .logout-overlay.show{display:flex}
        .logout-card{background:#fff;border-radius:24px;padding:32px 24px 24px;max-width:340px;width:100%;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.15);animation:scaleIn .2s ease;border:1px solid #e2e8f0}
        .dark .logout-card{background:#1f1f27!important;border-color:#464554!important}
        .logout-card h3{font-size:20px;font-weight:700;color:#1e293b;margin-bottom:6px}
        .dark .logout-card h3{color:#e4e1ed!important}
        .logout-card p{font-size:14px;color:#64748b;margin-bottom:28px;line-height:1.5}
        .dark .logout-card p{color:#908fa0!important}
        .logout-card .btn-group{display:flex;gap:10px}
        .logout-card button{flex:1;padding:12px 0;border-radius:12px;font-size:14px;font-weight:600;border:none;cursor:pointer;transition:all .15s}
        .logout-card .btn-cancel{background:#f1f5f9;color:#475569}
        .dark .logout-card .btn-cancel{background:#34343d!important;color:#c7c4d7!important}
        .logout-card .btn-cancel:hover{background:#e2e8f0}
        .dark .logout-card .btn-cancel:hover{background:#464554!important}
        .logout-card .btn-logout{background:#dc2626;color:#fff}
        .logout-card .btn-logout:hover{background:#b91c1c}
        .logout-card .btn-logout:active,.logout-card .btn-cancel:active{transform:scale(.97)}
        #empty-state.hidden{display:none!important}
        @media(min-width:768px){#back-to-accounts{display:none}}
        @media(max-width:767px){#app-section{height:100dvh;max-width:100%;border-radius:0;box-shadow:none;border:none}#chat-view{position:fixed;inset:0;z-index:10}#accounts-view{position:relative;z-index:1}}
        #user-list{overscroll-behavior:contain}
        #msg-search-bar{display:none}
        #msg-search-bar.active{display:flex}
        .search-highlight{background-color:#fde68a;border-radius:2px;padding:0 1px}
        .dark .search-highlight{background-color:#854d0e;color:#fef3c7}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        @keyframes scaleIn{from{transform:scale(.92);opacity:0}to{transform:scale(1);opacity:1}}
    </style>
</head>
<body class="bg-background text-on-background">

<?php if (!isset($_SESSION['user_id'])): ?>

<!-- ==================== AUTH SECTION ==================== -->
<div id="auth-section" class="flex min-h-dvh flex-col justify-center px-4 sm:px-6 py-12 bg-background">
    <div class="w-full max-w-sm mx-auto">
        <div class="flex justify-center"><img src="logo.png" alt="Ngobrol" class="h-16 w-16"></div>
        <h2 id="auth-title" class="mt-8 text-center text-display font-display text-on-background">Selamat Datang</h2>
        <p id="auth-subtitle" class="mt-2 text-center text-body text-on-surface-variant">Masuk untuk melanjutkan ke Ngobrol</p>
    </div>
    <div class="mt-8 w-full max-w-sm mx-auto">
        <div class="bg-surface dark:bg-surface-container rounded-2xl shadow-xl border border-outline-variant/50 px-8 sm:px-10 py-10">
            <div id="auth-error" role="alert" class="hidden bg-error-container text-on-error-container text-body px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">error</span><span id="auth-error-text"></span>
            </div>
            <form id="login-form" class="space-y-5">
                <div>
                    <label for="login-email" class="block text-label font-medium text-on-surface-variant mb-2">Email</label>
                    <div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><span class="material-symbols-outlined text-[20px] text-outline">mail</span></div>
                    <input id="login-email" type="email" required placeholder="you@example.com" autocomplete="email" class="block w-full rounded-xl border border-outline-variant bg-surface-container dark:bg-surface-container-high text-on-surface pl-11 pr-3 py-3 text-body placeholder-outline focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"></div>
                </div>
                <div>
                    <label for="login-password" class="block text-label font-medium text-on-surface-variant mb-2">Kata Sandi</label>
                    <div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><span class="material-symbols-outlined text-[20px] text-outline">lock</span></div>
                    <input id="login-password" type="password" required placeholder="••••••••" autocomplete="current-password" class="block w-full rounded-xl border border-outline-variant bg-surface-container dark:bg-surface-container-high text-on-surface pl-11 pr-11 py-3 text-body placeholder-outline focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition">
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3.5 flex items-center text-outline hover:text-on-surface-variant transition"><span class="material-symbols-outlined eye-open text-[20px]">visibility</span><span class="material-symbols-outlined eye-closed hidden text-[20px]">visibility_off</span></button></div>
                </div>
                <button type="submit" class="flex w-full justify-center items-center gap-2 rounded-xl px-4 py-3 text-body font-semibold text-on-primary bg-primary shadow-lg shadow-primary/20 active:scale-[0.98] transition-all hover:shadow-xl hover:shadow-primary/30 hover:brightness-110">
                    <span class="material-symbols-outlined text-[20px]">login</span>Masuk
                </button>
                <p class="text-center text-body text-on-surface-variant">Belum punya akun? <a href="#" id="show-register" class="font-semibold text-primary hover:underline">Daftar</a></p>
            </form>
            <form id="register-form" class="space-y-5 hidden">
                <div><label for="reg-name" class="block text-label font-medium text-on-surface-variant mb-2">Nama</label>
                <div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><span class="material-symbols-outlined text-[20px] text-outline">person</span></div>
                <input id="reg-name" type="text" required placeholder="Nama Anda" autocomplete="name" class="block w-full rounded-xl border border-outline-variant bg-surface-container dark:bg-surface-container-high text-on-surface pl-11 pr-3 py-3 text-body placeholder-outline focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"></div></div>
                <div><label for="reg-email" class="block text-label font-medium text-on-surface-variant mb-2">Email</label>
                <div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><span class="material-symbols-outlined text-[20px] text-outline">mail</span></div>
                <input id="reg-email" type="email" required placeholder="you@example.com" autocomplete="email" class="block w-full rounded-xl border border-outline-variant bg-surface-container dark:bg-surface-container-high text-on-surface pl-11 pr-3 py-3 text-body placeholder-outline focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"></div></div>
                <div><label for="reg-password" class="block text-label font-medium text-on-surface-variant mb-2">Kata Sandi</label>
                <div class="relative"><div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><span class="material-symbols-outlined text-[20px] text-outline">lock</span></div>
                <input id="reg-password" type="password" required placeholder="••••••••" autocomplete="new-password" class="block w-full rounded-xl border border-outline-variant bg-surface-container dark:bg-surface-container-high text-on-surface pl-11 pr-11 py-3 text-body placeholder-outline focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition">
                <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3.5 flex items-center text-outline hover:text-on-surface-variant transition"><span class="material-symbols-outlined eye-open text-[20px]">visibility</span><span class="material-symbols-outlined eye-closed hidden text-[20px]">visibility_off</span></button></div></div>
                <button type="submit" class="flex w-full justify-center items-center gap-2 rounded-xl px-4 py-3 text-body font-semibold text-on-primary bg-primary shadow-lg shadow-primary/20 active:scale-[0.98] transition-all hover:shadow-xl hover:shadow-primary/30 hover:brightness-110">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>Buat Akun
                </button>
                <p class="text-center text-body text-on-surface-variant">Sudah punya akun? <a href="#" id="show-login" class="font-semibold text-primary hover:underline">Masuk</a></p>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('show-register').addEventListener('click',e=>{e.preventDefault();document.getElementById('login-form').classList.add('hidden');document.getElementById('register-form').classList.remove('hidden');document.getElementById('auth-title').textContent='Buat Akun';document.getElementById('auth-subtitle').textContent='Bergabung dengan Ngobrol dan mulai chatting'});
document.getElementById('show-login').addEventListener('click',e=>{e.preventDefault();document.getElementById('register-form').classList.add('hidden');document.getElementById('login-form').classList.remove('hidden');document.getElementById('auth-title').textContent='Selamat Datang';document.getElementById('auth-subtitle').textContent='Masuk untuk melanjutkan ke Ngobrol'});
document.querySelectorAll('.toggle-password').forEach(btn=>{btn.addEventListener('click',()=>{const input=btn.closest('.relative').querySelector('input');const isP=input.type==='password';input.type=isP?'text':'password';btn.querySelector('.eye-open').classList.toggle('hidden',isP);btn.querySelector('.eye-closed').classList.toggle('hidden',!isP)})});
document.getElementById('login-form').addEventListener('submit',async e=>{e.preventDefault();document.getElementById('auth-error').classList.add('hidden');const email=document.getElementById('login-email'),pass=document.getElementById('login-password'),btn=e.target.querySelector('button[type="submit"]');if(!email.value.includes('@')||!email.value.includes('.')){document.getElementById('auth-error-text').textContent='Format email tidak valid';document.getElementById('auth-error').classList.remove('hidden');return}const orig=btn.innerHTML;btn.disabled=true;btn.innerHTML='<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>';try{const res=await fetch('api.php?action=login',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({email:email.value,password:pass.value})});const data=await res.json();if(data.status==='success'){window.location.replace('')}else{document.getElementById('auth-error-text').textContent=data.message;document.getElementById('auth-error').classList.remove('hidden')}}catch(err){document.getElementById('auth-error-text').textContent='Kesalahan: '+err.message;document.getElementById('auth-error').classList.remove('hidden')}finally{btn.disabled=false;btn.innerHTML=orig}});
document.getElementById('register-form').addEventListener('submit',async e=>{e.preventDefault();document.getElementById('auth-error').classList.add('hidden');const name=document.getElementById('reg-name'),email=document.getElementById('reg-email'),pass=document.getElementById('reg-password'),btn=e.target.querySelector('button[type="submit"]');if(!email.value.includes('@')||!email.value.includes('.')){document.getElementById('auth-error-text').textContent='Format email tidak valid';document.getElementById('auth-error').classList.remove('hidden');return}if(pass.value.length<6){document.getElementById('auth-error-text').textContent='Kata sandi minimal 6 karakter';document.getElementById('auth-error').classList.remove('hidden');return}const orig=btn.innerHTML;btn.disabled=true;btn.innerHTML='<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>';try{const res=await fetch('api.php?action=register',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name:name.value,email:email.value,password:pass.value})});const data=await res.json();if(data.status==='success'){window.location.replace('')}else{document.getElementById('auth-error-text').textContent=data.message;document.getElementById('auth-error').classList.remove('hidden')}}catch(err){document.getElementById('auth-error-text').textContent='Kesalahan: '+err.message;document.getElementById('auth-error').classList.remove('hidden')}finally{btn.disabled=false;btn.innerHTML=orig}});
</script>

<?php else: ?>

<!-- ==================== MAIN APP ==================== -->
<div id="app-section" class="h-dvh md:h-screen flex flex-col md:flex-row w-full bg-background overflow-hidden relative">
    <!-- Logout Modal -->
    <div id="logout-overlay" class="logout-overlay" onclick="if(event.target===this)closeLogoutModal()">
        <div class="logout-card">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-error-container flex items-center justify-center"><span class="material-symbols-outlined text-error text-[28px]">logout</span></div>
            <h3>Keluar</h3><p>Apakah Anda yakin ingin keluar dari akun ini?</p>
            <div class="btn-group"><button class="btn-cancel" onclick="closeLogoutModal()">Batal</button><button class="btn-logout" onclick="window.location.href='logout.php'">Keluar</button></div>
        </div>
    </div>
    <!-- Settings -->
    <div id="settings-overlay" class="settings-overlay" onclick="closeSettings()"></div>
    <div id="settings-panel" class="fixed inset-y-0 left-0 z-30 w-80 bg-surface dark:bg-surface-container-low shadow-2xl transform -translate-x-full transition-transform duration-300 flex flex-col border-r border-outline-variant/50">
        <div class="bg-primary p-6 text-on-primary">
            <div class="flex items-center justify-between mb-4"><span class="text-title font-semibold">Pengaturan</span><button onclick="closeSettings()" class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center hover:bg-white/25 transition"><span class="material-symbols-outlined text-[20px]">close</span></button></div>
            <div class="flex items-center gap-3"><div id="settings-avatar" class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-xl font-bold">?</div><div class="min-w-0"><div id="settings-name" class="font-semibold text-title truncate">-</div><div id="settings-email" class="text-sm text-white/70 truncate">-</div></div></div>
        </div>
        <div class="flex-1 overflow-y-auto scrollbar-hide">
            <div id="settings-form-section" class="p-4 space-y-3 hidden">
                <div id="settings-msg" class="text-xs hidden rounded-lg p-3"></div>
                <div><label class="block text-label text-on-surface-variant mb-1.5">Nama</label><input id="set-name" type="text" placeholder="Nama" class="w-full border border-outline-variant bg-surface-container dark:bg-surface-container-high text-on-surface rounded-xl px-4 py-2.5 text-body focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"></div>
                <div><label class="block text-label text-on-surface-variant mb-1.5">Email</label><input id="set-email" type="email" placeholder="Email" class="w-full border border-outline-variant bg-surface-container dark:bg-surface-container-high text-on-surface rounded-xl px-4 py-2.5 text-body focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"></div>
                <div><label class="block text-label text-on-surface-variant mb-1.5">Kata sandi baru</label><input id="set-new-password" type="password" placeholder="Kata sandi baru (opsional)" class="w-full border border-outline-variant bg-surface-container dark:bg-surface-container-high text-on-surface rounded-xl px-4 py-2.5 text-body focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"></div>
                <div><label class="block text-label text-on-surface-variant mb-1.5">Kata sandi saat ini *</label><input id="set-current-password" type="password" placeholder="Kata sandi saat ini" class="w-full border border-outline-variant bg-surface-container dark:bg-surface-container-high text-on-surface rounded-xl px-4 py-2.5 text-body focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"></div>
                <button id="set-save-btn" class="w-full bg-primary text-on-primary text-body font-semibold py-2.5 rounded-xl hover:brightness-110 transition shadow-md shadow-primary/15 active:scale-[0.98]">Simpan Perubahan</button>
                <button id="set-cancel-btn" class="w-full text-on-surface-variant text-body py-2 hover:text-on-surface transition">Batal</button>
            </div>
            <div id="settings-menu" class="p-2">
                <button onclick="toggleTheme()" class="flex items-center gap-3 w-full px-4 py-3 text-body text-on-surface hover:bg-surface-container dark:hover:bg-surface-container-high rounded-xl transition"><span id="theme-icon-light" class="material-symbols-outlined text-[22px] text-primary">dark_mode</span><span id="theme-icon-dark" class="material-symbols-outlined text-[22px] text-primary hidden">light_mode</span><span id="theme-text">Ganti ke Mode Gelap</span></button>
                <button id="settings-edit-profile-btn" class="flex items-center gap-3 w-full px-4 py-3 text-body text-on-surface hover:bg-surface-container dark:hover:bg-surface-container-high rounded-xl transition"><span class="material-symbols-outlined text-[22px] text-primary">edit</span>Edit Profil</button>
                <button onclick="showLogoutModal()" class="flex items-center gap-3 w-full px-4 py-3 text-body text-error hover:bg-error-container/50 rounded-xl transition"><span class="material-symbols-outlined text-[22px]">logout</span>Keluar</button>
            </div>
        </div>
    </div>

    <!-- ==================== SIDEBAR ==================== -->
    <div id="accounts-view" class="flex flex-col flex-1 md:flex md:flex-none md:w-[320px] md:border-r md:border-outline-variant bg-surface dark:bg-surface-container-low min-h-0">
        <div class="bg-surface dark:bg-surface-container-low px-5 py-4 flex items-center justify-between shrink-0 border-b border-outline-variant/50">
            <a href="/" class="flex items-center gap-3"><img src="logo.jpg" alt="Logo" class="h-9 w-auto"></a>
            <button id="settings-btn" aria-label="Pengaturan" class="w-10 h-10 rounded-full hover:bg-surface-container dark:hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant"><span class="material-symbols-outlined text-[22px]">settings</span></button>
        </div>
        <!-- Search Kontak -->
        <div class="px-4 py-2 shrink-0">
            <div class="flex items-center bg-surface-container dark:bg-surface-container-high rounded-xl px-3 py-2 border border-outline-variant/50 focus-within:ring-2 focus-within:ring-primary/30 focus-within:border-primary/50 transition-all">
                <span class="material-symbols-outlined text-outline text-[20px]">search</span>
                <input type="text" id="contact-search" placeholder="Cari kontak..." class="flex-1 bg-transparent border-0 text-body text-on-surface placeholder-outline focus:outline-none focus:ring-0 ml-2" autocomplete="off">
                <button id="contact-search-clear" class="hidden material-symbols-outlined text-outline text-[18px] hover:text-on-surface transition">close</button>
            </div>
        </div>
        <div id="user-list" class="flex-1 overflow-y-auto scrollbar-hide p-3"></div>
    </div>

    <!-- ==================== CHAT VIEW ==================== -->
    <div id="chat-view" class="hidden md:flex flex flex-col flex-1 min-h-0 min-w-0 bg-surface-dim dark:bg-background">
        <div id="empty-state" class="hidden absolute inset-0 z-20 flex-col items-center justify-center text-on-surface-variant px-6 bg-surface-dim dark:bg-background">
            <div class="w-20 h-20 rounded-full bg-surface-container dark:bg-surface-container-high flex items-center justify-center mb-5"><span class="material-symbols-outlined text-outline text-[40px]">chat_bubble_outline</span></div>
            <p class="text-headline text-on-surface font-semibold">Pilih user untuk mulai chat</p>
            <p class="text-body text-on-surface-variant mt-1">Pilih percakapan dari sidebar</p>
        </div>
        <div id="chat-inner" class="hidden flex flex-col flex-1 min-h-0">
            <!-- Chat Header -->
            <div class="bg-surface dark:bg-surface-container px-4 py-3 flex items-center shrink-0 border-b border-outline-variant/50 relative z-10">
                <button id="back-to-accounts" aria-label="Kembali" class="md:hidden hover:bg-surface-container dark:hover:bg-surface-container-high rounded-full p-2 transition mr-1 text-on-surface-variant"><span class="material-symbols-outlined">arrow_back</span></button>
                <div class="flex-1 min-w-0"><span id="chat-title" class="text-title text-on-surface font-semibold truncate block">Chat</span></div>
                <button id="msg-search-btn" aria-label="Cari pesan" class="w-9 h-9 rounded-full hover:bg-surface-container dark:hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant"><span class="material-symbols-outlined text-[22px]">search</span></button>
            </div>
            <!-- Search Bar Pesan -->
            <div id="msg-search-bar" class="bg-surface dark:bg-surface-container px-4 py-2 border-b border-outline-variant/50 items-center gap-2 shrink-0">
                <span class="material-symbols-outlined text-outline text-[20px]">search</span>
                <input type="text" id="msg-search-input" placeholder="Cari pesan..." class="flex-1 bg-transparent border-0 text-body text-on-surface placeholder-outline focus:outline-none focus:ring-0" autocomplete="off">
                <span id="msg-search-count" class="text-label text-on-surface-variant whitespace-nowrap"></span>
                <button id="msg-search-prev" class="w-8 h-8 rounded-full hover:bg-surface-container dark:hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant"><span class="material-symbols-outlined text-[18px]">keyboard_arrow_up</span></button>
                <button id="msg-search-next" class="w-8 h-8 rounded-full hover:bg-surface-container dark:hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant"><span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span></button>
                <button id="msg-search-close" class="w-8 h-8 rounded-full hover:bg-surface-container dark:hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant"><span class="material-symbols-outlined text-[18px]">close</span></button>
            </div>
            <!-- Chat Container -->
            <div id="chat-container" class="flex-1 overflow-y-auto custom-scrollbar min-h-0 px-4 py-4 bg-surface-dim dark:bg-background">
                <div id="chat-loading" class="hidden text-center text-on-surface-variant text-body py-8 flex items-center justify-center gap-2"><span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>Memuat pesan...</div>
                <div id="wrapper-chat"></div>
            </div>
            <!-- Chat Input -->
            <form id="form-chat" class="shrink-0 bg-surface dark:bg-surface-container border-t border-outline-variant/50">
                <div class="px-4 py-3 flex items-center gap-2">
                    <div class="flex-1 flex items-center bg-surface-container dark:bg-surface-container-high rounded-2xl border border-outline-variant/50 focus-within:ring-2 focus-within:ring-primary/30 focus-within:border-primary/50 transition-all">
                        <input type="text" name="content" placeholder="Tulis pesan..." class="flex-1 bg-transparent border-0 text-body text-on-surface placeholder-outline py-3 pl-4 focus:outline-none focus:ring-0 min-h-[44px]" id="content" autocomplete="off">
                    </div>
                    <button type="submit" aria-label="Kirim Pesan" class="bg-primary text-on-primary rounded-full w-11 h-11 hover:brightness-110 active:scale-90 transition-all flex items-center justify-center shrink-0 shadow-md shadow-primary/20"><span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">send</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let userId=null,receiverId=null,editingMessageId=null,isEditing=false,chatSwitching=false,pusher=null,channel=null;
let allUsers=[];
const wrapperChat=document.getElementById('wrapper-chat'),chatContainer=document.getElementById('chat-container'),form=document.getElementById('form-chat'),contentInput=document.getElementById('content');

function isDesktop(){return window.innerWidth>=768}

async function apiFetch(url,options){const res=await fetch(url,options);const text=await res.text();let data;try{data=JSON.parse(text)}catch(e){throw new Error('Invalid JSON response')}if(data.status==='error'&&data.message==='Unauthorized'){window.location.replace('');throw new Error('Unauthorized')}return data}

function escapeHtml(text){const div=document.createElement('div');div.textContent=text;return div.innerHTML}

// Dark mode sudah dihandle oleh CSS di akhir document

// ==================== CONTACT SEARCH ====================
const contactSearch=document.getElementById('contact-search');
const contactClear=document.getElementById('contact-search-clear');
const userList=document.getElementById('user-list');

function filterContacts(query){
    const q=query.toLowerCase().trim();
    const items=userList.querySelectorAll('.user-item');
    items.forEach(item=>{
        const name=item.dataset.userName||'';
        item.style.display=(!q||name.includes(q))?'':'none';
    });
}

contactSearch.addEventListener('input',()=>{
    filterContacts(contactSearch.value);
    contactClear.classList.toggle('hidden',!contactSearch.value);
});
contactClear.addEventListener('click',()=>{
    contactSearch.value='';
    filterContacts('');
    contactClear.classList.add('hidden');
    contactSearch.focus();
});

// ==================== MESSAGE SEARCH ====================
const msgSearchBtn=document.getElementById('msg-search-btn');
const msgSearchBar=document.getElementById('msg-search-bar');
const msgSearchInput=document.getElementById('msg-search-input');
const msgSearchCount=document.getElementById('msg-search-count');
const msgSearchPrev=document.getElementById('msg-search-prev');
const msgSearchNext=document.getElementById('msg-search-next');
const msgSearchClose=document.getElementById('msg-search-close');
let searchResults=[];
let searchIndex=0;

function escapeRegex(s){return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')}

function searchMessages(query){
    searchResults=[];
    searchIndex=0;
    document.querySelectorAll('.search-highlight').forEach(el=>{
        el.replaceWith(document.createTextNode(el.textContent));
    });
    if(!query.trim())return;
    const bubbles=wrapperChat.querySelectorAll('.bubble-own, .bubble-other');
    bubbles.forEach(bubble=>{
        const walker=document.createTreeWalker(bubble,NodeFilter.SHOW_TEXT);
        const textNodes=[];
        while(walker.nextNode())textNodes.push(walker.currentNode);
        textNodes.forEach(node=>{
            const idx=node.textContent.toLowerCase().indexOf(query.toLowerCase());
            if(idx===-1)return;
            const before=node.textContent.substring(0,idx);
            const match=node.textContent.substring(idx,idx+query.length);
            const after=node.textContent.substring(idx+query.length);
            const span=document.createElement('span');
            span.className='search-highlight';
            span.textContent=match;
            const parent=node.parentNode;
            if(before)parent.insertBefore(document.createTextNode(before),node);
            parent.insertBefore(span,node);
            if(after)parent.insertBefore(document.createTextNode(after),node);
            parent.removeChild(node);
            searchResults.push(span);
        });
    });
    if(searchResults.length>0)highlightSearch(0);
    msgSearchCount.textContent=searchResults.length?`${searchResults.length} hasil`:'Tidak ditemukan';
}

function highlightSearch(idx){
    searchResults.forEach(r=>r.style.outline='none');
    if(idx>=0&&idx<searchResults.length){
        const el=searchResults[idx];
        el.style.outline='2px solid #2563eb';
        el.style.outlineOffset='1px';
        el.scrollIntoView({behavior:'smooth',block:'center'});
    }
    searchIndex=idx;
    if(searchResults.length)msgSearchCount.textContent=`${idx+1}/${searchResults.length}`;
}

msgSearchBtn.addEventListener('click',()=>{
    msgSearchBar.classList.toggle('active');
    if(msgSearchBar.classList.contains('active')){msgSearchInput.focus();}
    else{searchMessages('');msgSearchInput.value='';}
});
msgSearchClose.addEventListener('click',()=>{
    msgSearchBar.classList.remove('active');
    searchMessages('');msgSearchInput.value='';
});
msgSearchInput.addEventListener('input',()=>searchMessages(msgSearchInput.value));
msgSearchPrev.addEventListener('click',()=>{
    if(!searchResults.length)return;
    highlightSearch((searchIndex-1+searchResults.length)%searchResults.length);
});
msgSearchNext.addEventListener('click',()=>{
    if(!searchResults.length)return;
    highlightSearch((searchIndex+1)%searchResults.length);
});
msgSearchInput.addEventListener('keydown',e=>{
    if(e.key==='Enter'){e.preventDefault();msgSearchNext.click();}
    if(e.key==='Escape'){msgSearchClose.click();}
});

// ==================== NAVIGATION ====================
function showAccounts(){
    receiverId=null;document.querySelectorAll('.user-item').forEach(el=>el.classList.remove('active'));
    if(!isDesktop()){document.getElementById('accounts-view').classList.remove('hidden');document.getElementById('chat-view').classList.add('hidden')}
    document.getElementById('empty-state').classList.remove('hidden');
    document.getElementById('chat-inner').classList.add('hidden');
    if(pusher){pusher.unsubscribe('chat')}
}

function setActiveUser(id){
    document.querySelectorAll('.user-item').forEach(el=>el.classList.remove('active'));
    const active=document.querySelector(`.user-item[data-user-id="${id}"]`);
    if(active)active.classList.add('active');
}

function showChat(id){
    if(chatSwitching||id===receiverId)return;
    chatSwitching=true;receiverId=id;setActiveUser(id);
    if(!isDesktop()){document.getElementById('accounts-view').classList.add('hidden');document.getElementById('chat-view').classList.remove('hidden')}
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('chat-inner').classList.remove('hidden');
    wrapperChat.innerHTML='';msgSearchBar.classList.remove('active');searchMessages('');
    document.getElementById('chat-loading').classList.remove('hidden');
    loadChatTitle().then(()=>loadMessages()).then(()=>{initPusher()}).finally(()=>{document.getElementById('chat-loading').classList.add('hidden');chatSwitching=false});
}

document.getElementById('back-to-accounts').addEventListener('click',showAccounts);

// ==================== SETTINGS ====================
const settingsBtn=document.getElementById('settings-btn'),settingsPanel=document.getElementById('settings-panel'),settingsOverlay=document.getElementById('settings-overlay');
function openSettings(){settingsOverlay.classList.add('show');requestAnimationFrame(()=>{settingsPanel.classList.remove('-translate-x-full')})}
function closeSettings(){settingsPanel.classList.add('-translate-x-full');settingsOverlay.classList.remove('show');document.getElementById('settings-form-section').classList.add('hidden');document.getElementById('settings-menu').classList.remove('hidden')}
settingsBtn.addEventListener('click',openSettings);

async function loadProfile(){try{const d=await apiFetch('api.php?action=getProfile');if(d.status==='success'){const u=d.user;document.getElementById('settings-name').textContent=u.name.charAt(0).toUpperCase()+u.name.slice(1);document.getElementById('settings-email').textContent=u.email;document.getElementById('settings-avatar').textContent=u.name.charAt(0).toUpperCase()}}catch(e){}}
document.getElementById('settings-edit-profile-btn').addEventListener('click',async()=>{document.getElementById('settings-menu').classList.add('hidden');document.getElementById('settings-form-section').classList.remove('hidden');document.getElementById('settings-msg').classList.add('hidden');try{const d=await apiFetch('api.php?action=getProfile');if(d.status==='success'){document.getElementById('set-name').value=d.user.name;document.getElementById('set-email').value=d.user.email}}catch(e){}document.getElementById('set-new-password').value='';document.getElementById('set-current-password').value=''});
document.getElementById('set-cancel-btn').addEventListener('click',()=>{document.getElementById('settings-form-section').classList.add('hidden');document.getElementById('settings-menu').classList.remove('hidden')});
document.getElementById('set-save-btn').addEventListener('click',async()=>{const msg=document.getElementById('settings-msg');msg.classList.add('hidden');const cp=document.getElementById('set-current-password').value;if(!cp){msg.textContent='Kata sandi saat ini wajib diisi';msg.className='text-xs text-error bg-error-container rounded-lg p-3';msg.classList.remove('hidden');return}try{const d=await apiFetch('api.php?action=updateProfile',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name:document.getElementById('set-name').value.trim(),email:document.getElementById('set-email').value.trim(),currentPassword:cp,newPassword:document.getElementById('set-new-password').value})});if(d.status==='success'){msg.textContent='Profil berhasil diperbarui';msg.className='text-xs text-green-600 bg-green-50 dark:text-green-400 dark:bg-green-900/30 rounded-lg p-3';msg.classList.remove('hidden');document.getElementById('set-new-password').value='';document.getElementById('set-current-password').value='';loadProfile()}else{msg.textContent=d.message;msg.className='text-xs text-error bg-error-container rounded-lg p-3';msg.classList.remove('hidden')}}catch(e){msg.textContent='Kesalahan: '+e.message;msg.className='text-xs text-error bg-error-container rounded-lg p-3';msg.classList.remove('hidden')}});

// ==================== USERS ====================
async function loadUsers(){
    try{
        const d=await apiFetch('api.php?action=getUsers');
        allUsers=d.users;
        const c=document.getElementById('user-list');c.innerHTML='';
        const colors=['bg-blue-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-teal-500','bg-rose-500','bg-indigo-500','bg-cyan-500'];
        d.users.forEach((u,i)=>{
            const name=u.name.charAt(0).toUpperCase()+u.name.slice(1);
            const initial=u.name.charAt(0).toUpperCase();
            const color=colors[i%colors.length];
            const div=document.createElement('div');
            div.className='user-item flex items-center gap-3 px-3 py-3 hover:bg-surface-container dark:hover:bg-surface-container-high cursor-pointer rounded-xl mx-1';
            div.dataset.userId=u.id;div.dataset.userName=u.name.toLowerCase();
            div.onclick=()=>showChat(u.id);
            div.innerHTML=`<div class="w-12 h-12 ${color} rounded-full flex items-center justify-center text-white font-semibold text-base shrink-0 shadow-sm">${initial}</div><div class="flex-1 min-w-0"><h2 class="text-title text-on-surface font-semibold truncate">${escapeHtml(name)}</h2><p class="text-label text-on-surface-variant truncate mt-0.5">Ketuk untuk mulai chat</p></div>`;
            c.appendChild(div);
        });
    }catch(e){document.getElementById('user-list').innerHTML='<div class="text-center text-on-surface-variant text-body py-8 flex flex-col items-center gap-2"><span class="material-symbols-outlined text-[32px] text-outline">wifi_off</span>Koneksi terputus</div>'}
}

async function loadChatTitle(){try{const d=await apiFetch('api.php?action=getUserName&id='+receiverId);if(d.status==='success')document.getElementById('chat-title').textContent=d.name.charAt(0).toUpperCase()+d.name.slice(1)}catch(e){}}

// ==================== MESSAGES ====================
async function loadMessages(){
    try{
        const d=await apiFetch('api.php?action=getMessages&receiver_id='+receiverId);
        if(d.status==='error'){wrapperChat.innerHTML='<div class="text-center text-on-surface-variant text-body py-8">'+escapeHtml(d.message)+'</div>';return}
        userId=d.user_id;wrapperChat.innerHTML='';
        d.messages.forEach(m=>appendMessage(m.id,m.sender_id,m.content,m.created_at));
        updateStatusIcons();scrollToBottom();
    }catch(e){if(e.message==='Unauthorized')throw e;wrapperChat.innerHTML='<div class="text-center text-on-surface-variant text-body py-8 flex flex-col items-center gap-2"><span class="material-symbols-outlined text-[32px] text-outline">wifi_off</span>Koneksi terputus</div>'}
}

function formatTime(dateStr){if(!dateStr)return'';const d=new Date(dateStr);if(isNaN(d.getTime()))return'';return d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})}

function updateStatusIcons(){
    document.querySelectorAll('.msg-status').forEach(el=>el.remove());
    const allItems=wrapperChat.querySelectorAll('.msg-item');
    let lastOwn=null;
    allItems.forEach(item=>{if(item.querySelector('.bubble-own'))lastOwn=item});
    if(lastOwn){const meta=lastOwn.querySelector('.msg-meta');if(meta){const icon=document.createElement('span');icon.className='material-symbols-outlined msg-status msg-status-read';icon.style.fontVariationSettings="'FILL' 1,'wght' 600";icon.textContent='done_all';meta.appendChild(icon)}}
}

function appendMessage(messageId,senderId,content,createdAt){
    const isOwn=senderId==userId;
    let timeText;
    if(messageId&&String(messageId).startsWith('temp-')){timeText='Mengirim...'}
    else if(createdAt){timeText=formatTime(createdAt)}else{timeText=''}
    const item=document.createElement('div');item.className='msg-item';item.dataset.messageId=messageId;
    const bubble=document.createElement('div');bubble.className=isOwn?'bubble-own':'bubble-other';bubble.textContent=content;
    const meta=document.createElement('div');meta.className='msg-meta '+(isOwn?'msg-meta-own':'msg-meta-other');meta.innerHTML='<span>'+escapeHtml(timeText)+'</span>';
    item.appendChild(bubble);item.appendChild(meta);
    if(messageId&&!String(messageId).startsWith('temp-')){
        bubble.style.cursor='pointer';let actions=null;
        bubble.addEventListener('click',function(e){
            e.stopPropagation();if(isEditing)return;
            if(actions&&actions.parentNode){actions.remove();actions=null;return}
            document.querySelectorAll('.msg-actions').forEach(m=>m.remove());
            actions=document.createElement('div');actions.className='msg-actions';
            let html='';if(isOwn){html+='<button class="edit-action"><span class="material-symbols-outlined text-[16px]">edit</span>Edit</button>';html+='<button class="delete-action"><span class="material-symbols-outlined text-[16px]">delete</span>Hapus</button>'}
            html+='<button class="copy-action"><span class="material-symbols-outlined text-[16px]">content_copy</span>Salin</button>';actions.innerHTML=html;item.appendChild(actions);
            if(isOwn){actions.querySelector('.edit-action').addEventListener('click',function(ev){ev.stopPropagation();actions.remove();actions=null;editMessage(messageId,bubble)});actions.querySelector('.delete-action').addEventListener('click',function(ev){ev.stopPropagation();actions.remove();actions=null;deleteMessage(messageId,item)})}
            actions.querySelector('.copy-action').addEventListener('click',function(ev){ev.stopPropagation();actions.remove();actions=null;copyToClipboard(content)});
        });
    }
    const last=wrapperChat.lastElementChild;const sameSender=last&&last.dataset.senderId==String(senderId);
    if(sameSender){const col=last.querySelector('.msg-col');if(col){col.appendChild(item);return}}
    const wrapper=document.createElement('div');wrapper.dataset.senderId=senderId;wrapper.className='msg-block '+(isOwn?'msg-col-own':'msg-col-other');
    const col=document.createElement('div');col.className='msg-col';col.appendChild(item);wrapper.appendChild(col);wrapperChat.appendChild(wrapper);
}

document.addEventListener('click',e=>{document.querySelectorAll('.msg-actions').forEach(menu=>{if(menu.parentNode&&!menu.contains(e.target))menu.remove()})});

function editMessage(messageId,bubble){
    isEditing=true;const orig=bubble.textContent;
    const input=document.createElement('input');input.type='text';input.value=orig;
    input.className='w-full bg-surface dark:bg-surface-container-high text-on-surface p-2 rounded-xl border border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/40 text-body';
    const actions=document.createElement('div');actions.className='flex gap-1.5 mt-2 justify-end';
    const saveBtn=document.createElement('button');saveBtn.type='button';saveBtn.className='text-tiny bg-primary text-on-primary px-3 py-1.5 rounded-lg font-semibold hover:brightness-110 transition';saveBtn.textContent='Simpan';
    const cancelBtn=document.createElement('button');cancelBtn.type='button';cancelBtn.className='text-tiny bg-surface-container dark:bg-surface-container-high text-on-surface-variant px-3 py-1.5 rounded-lg font-semibold hover:bg-surface-container-high dark:hover:bg-surface-container-highest transition';cancelBtn.textContent='Batal';
    actions.appendChild(cancelBtn);actions.appendChild(saveBtn);
    bubble.innerHTML='';bubble.className='bg-surface dark:bg-surface-container-high p-3 rounded-2xl rounded-br-sm shadow-sm border border-outline-variant/50';
    bubble.appendChild(input);bubble.appendChild(actions);input.focus();input.setSelectionRange(input.value.length,input.value.length);
    cancelBtn.onclick=()=>cancelEdit(messageId,bubble,orig);
    input.addEventListener('keydown',e=>{if(e.key==='Enter')saveEdit(messageId,bubble,input.value);else if(e.key==='Escape')cancelEdit(messageId,bubble,orig)});
    saveBtn.onclick=()=>saveEdit(messageId,bubble,input.value);
}

async function saveEdit(messageId,bubble,newContent){
    newContent=newContent.trim();if(!newContent||editingMessageId)return;editingMessageId=messageId;
    try{await apiFetch('api.php?action=editMessage',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({messageId,content:newContent})})}catch(e){}
    bubble.innerHTML='';bubble.className='bubble-own';bubble.textContent=newContent;editingMessageId=null;isEditing=false;
}
function cancelEdit(messageId,bubble,orig){bubble.innerHTML='';bubble.className='bubble-own';bubble.textContent=orig;isEditing=false}

async function deleteMessage(messageId,item){
    if(!confirm('Hapus pesan ini?'))return;
    try{const d=await apiFetch('api.php?action=deleteMessage',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({messageId})});if(d.status==='success'){const col=item.parentElement;const block=col?col.parentElement:null;item.remove();if(col&&col.children.length===0&&block)block.remove();updateStatusIcons()}else{alert('Gagal menghapus: '+d.message)}}catch(e){if(e.message==='Unauthorized')throw e;alert('Gagal menghapus pesan')}
}

function attachMessageActions(item,messageId){
    const bubble=item.querySelector('.bubble-own');if(!bubble)return;let act=null;bubble.style.cursor='pointer';
    bubble.addEventListener('click',function(e){
        e.stopPropagation();if(isEditing)return;
        if(act&&act.parentNode){act.remove();act=null;return}
        document.querySelectorAll('.msg-actions').forEach(m=>m.remove());
        act=document.createElement('div');act.className='msg-actions';
        act.innerHTML='<button class="edit-action"><span class="material-symbols-outlined text-[16px]">edit</span>Edit</button><button class="delete-action"><span class="material-symbols-outlined text-[16px]">delete</span>Hapus</button><button class="copy-action"><span class="material-symbols-outlined text-[16px]">content_copy</span>Salin</button>';
        item.appendChild(act);
        act.querySelector('.edit-action').addEventListener('click',ev=>{ev.stopPropagation();act.remove();act=null;editMessage(messageId,bubble)});
        act.querySelector('.delete-action').addEventListener('click',ev=>{ev.stopPropagation();act.remove();act=null;deleteMessage(messageId,item)});
        act.querySelector('.copy-action').addEventListener('click',ev=>{ev.stopPropagation();act.remove();act=null;copyToClipboard(bubble.textContent)});
    });
}

function scrollToBottom(){chatContainer.scrollTop=chatContainer.scrollHeight}

// ==================== ANTI COPY-PASTE ====================
const chatContainerEl=document.getElementById('wrapper-chat');
chatContainerEl.addEventListener('contextmenu',e=>{if(e.target.closest('.bubble-own,.bubble-other')){e.preventDefault()}});
chatContainerEl.addEventListener('copy',e=>{if(e.target.closest('.bubble-own,.bubble-other')){e.preventDefault()}});
chatContainerEl.addEventListener('cut',e=>{if(e.target.closest('.bubble-own,.bubble-other')){e.preventDefault()}});
chatContainerEl.addEventListener('selectstart',e=>{if(e.target.closest('.bubble-own,.bubble-other')){e.preventDefault()}});

function copyToClipboard(text){
    navigator.clipboard.writeText(text).then(()=>{
        const t=document.createElement('div');t.className='fixed bottom-20 left-1/2 -translate-x-1/2 bg-inverse-surface text-inverse-on-surface px-5 py-2.5 rounded-full text-label font-medium shadow-lg z-50';
        t.innerHTML='<span class="material-symbols-outlined text-[16px] mr-1" style="vertical-align:middle">check_circle</span>Pesan disalin';
        document.body.appendChild(t);setTimeout(()=>{t.style.transition='opacity .3s';t.style.opacity='0';setTimeout(()=>t.remove(),300)},1800);
    }).catch(e=>console.error('Gagal menyalin:',e));
}

form.addEventListener('submit',async event=>{
    event.preventDefault();const chatContent=contentInput.value.trim();if(!chatContent)return;
    const tempId='temp-'+Date.now();appendMessage(tempId,userId,chatContent);updateStatusIcons();scrollToBottom();
    try{const d=await apiFetch('api.php?action=saveChat',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({content:chatContent,receiverId})});if(d.status==='success'&&d.message_id){const el=wrapperChat.querySelector(`[data-message-id="${tempId}"]`);if(el){el.dataset.messageId=d.message_id;const meta=el.querySelector('.msg-meta span');if(meta&&meta.textContent==='Mengirim...')meta.textContent=formatTime(new Date().toISOString());attachMessageActions(el,d.message_id);updateStatusIcons()}}}catch(e){console.error('Error:',e)}
    contentInput.value='';
});
contentInput.addEventListener('keydown',e=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();form.dispatchEvent(new Event('submit'))}});

function initPusher(){
    if(pusher)pusher.disconnect();
    pusher=new Pusher('579716c8e98cf3fb7f38',{cluster:'ap1'});
    channel=pusher.subscribe('chat');
    channel.bind('receive',function(d){if(d.sender_id==userId||d.sender_id!=receiverId)return;appendMessage(d.message_id,d.sender_id,d.content,d.created_at);updateStatusIcons();scrollToBottom()});
    channel.bind('edit',function(d){const ok=(d.sender_id==userId&&d.receiver_id==receiverId)||(d.sender_id==receiverId&&d.receiver_id==userId);if(!ok)return;const item=wrapperChat.querySelector(`[data-message-id="${d.message_id}"]`);if(item){const b=item.querySelector('.bubble-own,.bubble-other');if(b)b.textContent=d.content}});
    channel.bind('delete',function(d){const ok=(d.sender_id==userId&&d.receiver_id==receiverId)||(d.sender_id==receiverId&&d.receiver_id==userId);if(!ok)return;const item=wrapperChat.querySelector(`[data-message-id="${d.message_id}"]`);if(item){const col=item.parentElement;const block=col?col.parentElement:null;item.remove();if(col&&col.children.length===0&&block)block.remove();updateStatusIcons()}});
}

function showLogoutModal(){document.getElementById('logout-overlay').classList.add('show')}
function closeLogoutModal(){document.getElementById('logout-overlay').classList.remove('show')}

loadUsers();loadProfile();
if('serviceWorker' in navigator){navigator.serviceWorker.register('sw.js')}

function toggleTheme(){
    const isDark=document.documentElement.classList.toggle('dark');
    localStorage.theme=isDark?'dark':'light';
    const txt=document.getElementById('theme-text'),iD=document.getElementById('theme-icon-dark'),iL=document.getElementById('theme-icon-light');
    if(txt)txt.textContent=isDark?'Ganti ke Mode Terang':'Ganti ke Mode Gelap';
    if(isDark){iD&&iD.classList.remove('hidden');iL&&iL.classList.add('hidden')}else{iL&&iL.classList.remove('hidden');iD&&iD.classList.add('hidden')}
}

function updateThemeIcons(){
    const txt=document.getElementById('theme-text'),iD=document.getElementById('theme-icon-dark'),iL=document.getElementById('theme-icon-light');
    if(!txt||!iD||!iL)return;
    if(document.documentElement.classList.contains('dark')){txt.textContent='Ganti ke Mode Terang';iD.classList.remove('hidden');iL.classList.add('hidden')}
    else{txt.textContent='Ganti ke Mode Gelap';iL.classList.remove('hidden');iD.classList.add('hidden')}
}
updateThemeIcons();
</script>
<?php endif; ?>

<!-- ========== DARK MODE CSS OVERRIDE (LAST UNTUK MENANG DARI TAILWIND CDN) ========== -->
<style id="dark-mode-css">
.dark,.dark body{background-color:#13131b!important;color:#e4e1ed!important}
.dark .bg-background{background-color:#13131b!important}.dark .text-on-background{color:#e4e1ed!important}
.dark .bg-surface{background-color:#1b1b23!important}.dark .text-on-surface{color:#e4e1ed!important}
.dark .bg-surface-dim{background-color:#13131b!important}
.dark .bg-surface-container{background-color:#292932!important}
.dark .bg-surface-container-low{background-color:#1b1b23!important}
.dark .bg-surface-container-high{background-color:#34343d!important}
.dark .text-on-surface-variant{color:#c7c4d7!important}
.dark .border-outline-variant{border-color:#464554!important}
.dark .text-outline{color:#908fa0!important}.dark .text-primary{color:#c0c1ff!important}
.dark .bg-primary{background-color:#c0c1ff!important;color:#1000a9!important}
.dark .text-on-primary{color:#1000a9!important}
.dark .bg-primary-container{background-color:#8083ff!important}
.dark .text-on-primary-container{color:#e1e0ff!important}
.dark .text-error{color:#ffb4ab!important}
.dark .bg-error-container{background-color:#93000a!important;color:#ffdad6!important}
.dark .bg-inverse-surface{background-color:#e4e1ed!important}
.dark .text-inverse-on-surface{color:#303038!important}
.dark .shadow-xl,.dark .shadow-lg,.dark .shadow-md{box-shadow:0 10px 40px rgba(0,0,0,.5)!important}
.dark .placeholder-outline::placeholder{color:#908fa0!important}
.dark .bubble-own{background-color:#c0c1ff!important;color:#1000a9!important}
.dark .bubble-other{background-color:#292932!important;color:#e4e1ed!important;border-color:#464554!important}
.dark .msg-actions{background:#292932!important;border-color:#464554!important}
.dark .msg-actions button{color:#e4e1ed!important}.dark .msg-actions button:hover{background:#34343d!important}
.dark .user-item.active{background:#1f1f27!important;border-left-color:#c0c1ff!important}
.dark .user-item.active h2{color:#c0c1ff!important}
.dark .logout-card{background:#1f1f27!important;border-color:#464554!important}
.dark .logout-card h3{color:#e4e1ed!important}.dark .logout-card p{color:#908fa0!important}
.dark .logout-card .btn-cancel{background:#34343d!important;color:#c7c4d7!important}
.dark .logout-card .btn-cancel:hover{background:#464554!important}
.dark .settings-overlay{background:rgba(0,0,0,.6)!important}
.dark .msg-meta-own,.dark .msg-meta-other{color:#a1a1aa!important}
.dark #form-chat{box-shadow:0 -4px 20px rgba(0,0,0,.3)!important}
.dark .hover\:bg-surface-container:hover{background-color:#292932!important}
.dark .hover\:bg-surface-container-high:hover{background-color:#34343d!important}
</style>
</body>
</html>