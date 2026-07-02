<?php
$sessionLifetime = 86400 * 30;
@ini_set('session.gc_maxlifetime', $sessionLifetime);
@ini_set('session.gc_probability', 0);
session_set_cookie_params(['lifetime' => $sessionLifetime, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>App Chat</title>
    <link rel="manifest" href="manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#2ea6ff">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="icon-192.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<style>
* { -webkit-tap-highlight-color: transparent; }
input, button, a, .cursor-pointer { touch-action: manipulation; }
#form-chat { padding-bottom: env(safe-area-inset-bottom, 0px); }

/* Same-sender vertical grouping (WhatsApp/Telegram style) */
#empty-state.hidden { display: none !important; }
#wrapper-chat > .msg-block { display: flex; flex-direction: column; }
#wrapper-chat > .msg-block + .msg-block { margin-top: 10px; }

/* A group of consecutive messages from the same sender stacks vertically */
.msg-col {
  display: flex;
  flex-direction: column;
  gap: 3px;
  max-width: 88%;
}
@media (min-width: 768px) {
  .msg-col { max-width: 55%; }
}
.msg-col-own { justify-content: flex-end; align-items: flex-end; }
.msg-col-other { justify-content: flex-start; align-items: flex-start; }
.msg-col-own .msg-col { align-items: flex-end; }
.msg-col-other .msg-col { align-items: flex-start; }

/* Each individual message (bubble + its meta row) */
.msg-item { display: flex; flex-direction: column; max-width: 100%; }
.msg-col-own .msg-item { align-items: flex-end; }
.msg-col-other .msg-item { align-items: flex-start; }

@media (min-width: 768px) {
  #back-to-accounts { display: none; }
}

/* Mobile: chat view slides over */
@media (max-width: 767px) {
  #app-section { height: 100dvh; max-width: 100%; border-radius: 0; box-shadow: none; border: none; }
  #chat-view { position: absolute; inset: 0; z-index: 10; }
  #accounts-view { position: relative; z-index: 1; }
}

/* Sidebar scroll */
#user-list { overscroll-behavior: contain; }

/* Chat bubbles */
.bubble-own {
  background: #2ea6ff;
  color: white;
  border-radius: 16px 16px 4px 16px;
  padding: 8px 12px;
  max-width: 100%;
  word-wrap: break-word;
  line-height: 1.35;
  font-size: 14px;
  box-shadow: 0 1px 1px rgba(0,0,0,0.06);
}
.bubble-other {
  background: white;
  color: #222;
  border-radius: 16px 16px 16px 4px;
  padding: 8px 12px;
  max-width: 100%;
  word-wrap: break-word;
  line-height: 1.35;
  font-size: 14px;
  box-shadow: 0 1px 1px rgba(0,0,0,0.05);
}

/* Consecutive bubbles from the same sender get tighter corners, like WA/Telegram */
.msg-item:not(:last-child) .bubble-own { border-radius: 16px 16px 4px 16px; }
.msg-item:not(:first-child) .bubble-own { border-radius: 16px 4px 4px 16px; }
.msg-item:not(:first-child):not(:last-child) .bubble-own { border-radius: 16px 4px 4px 16px; }
.msg-item:not(:first-child) .bubble-other { border-radius: 4px 16px 16px 16px; }

/* Meta row (time / read receipt) */
.msg-meta {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-top: 3px;
  font-size: 11px;
  line-height: 1;
  opacity: 0.85;
}
.msg-meta-own { justify-content: flex-end; color: #9aa5b1; }
.msg-meta-other { justify-content: flex-start; color: #6b7280; }

/* Read receipt */
.read-receipt { font-size: 12px; color: #2ea6ff; }
</style>
</head>
<body class="bg-gray-100 md:bg-[#e7ebf0]">

<div id="app-section" class="h-dvh md:h-screen flex flex-col md:flex-row w-full bg-white md:bg-[#e7ebf0] overflow-hidden relative">

    <!-- Settings overlay & panel -->
    <div id="settings-overlay" class="fixed inset-0 bg-black/40 z-20 hidden opacity-0 transition-opacity duration-200" onclick="closeSettings()"></div>
    <div id="settings-panel" class="fixed left-0 top-0 h-full w-72 bg-white z-30 shadow-2xl -translate-x-full transition-transform duration-200 ease-out">
        <div class="bg-[#2ea6ff] p-5 text-white">
            <div class="flex items-center gap-3">
                <div id="settings-avatar" class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-xl font-bold">?</div>
                <div class="min-w-0">
                    <div id="settings-name" class="font-semibold text-base truncate">-</div>
                    <div id="settings-email" class="text-sm text-white/70 truncate">-</div>
                </div>
            </div>
        </div>
        <div class="p-3 space-y-1">
            <div id="settings-form-section" class="px-3 py-4 space-y-3 hidden">
                <div id="settings-msg" class="text-xs hidden"></div>
                <input id="set-name" type="text" placeholder="Nama" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input id="set-email" type="email" placeholder="Email" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input id="set-new-password" type="password" placeholder="Kata sandi baru" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input id="set-current-password" type="password" placeholder="Kata sandi saat ini *" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button id="set-save-btn" class="w-full bg-[#2ea6ff] text-white text-sm font-semibold py-2 rounded-lg hover:bg-[#1e96ef] transition">Simpan Perubahan</button>
                <button id="set-cancel-btn" class="w-full text-gray-500 text-sm py-1.5 hover:text-gray-700 transition">Batal</button>
            </div>
            <div id="settings-menu">
                <button id="settings-edit-profile-btn" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Profil
                </button>
                <button onclick="if(confirm('Apakah Anda Ingin Keluar ?')){ window.location.href='logout.php'; }" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1044 21 19 21H15"/><path d="M10 17L15 12L10 7"/><path d="M15 12H3"/></svg>
                    Keluar
                </button>
            </div>
        </div>
    </div>

    <!-- Sidebar: User list -->
    <div id="accounts-view" class="flex flex-col flex-1 md:flex md:flex-none md:w-80 md:border-r md:border-gray-200 md:bg-white min-h-0">
        <div class="bg-[#2ea6ff] px-4 py-3 text-white flex items-center justify-center shrink-0 relative">
            <button id="settings-btn" class="absolute left-4 hover:bg-[#1e96ef] rounded-lg p-2 transition">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="6" r="4" stroke="#ffffff" stroke-width="1.5"/>
                    <path d="M15 20.6151C14.0907 20.8619 13.0736 21 12 21C8.13401 21 5 19.2091 5 17C5 14.7909 8.13401 13 12 13C15.866 13 19 14.791 19 17C19 17.3453 18.923 17.6804 18.775 18" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
            <span class="font-semibold text-base">Pesan</span>
        </div>
        <div class="flex-1 overflow-y-auto bg-gray-50 md:bg-white min-h-0" id="user-list"></div>
    </div>

    <!-- Chat Area -->
    <div id="chat-view" class="hidden md:flex flex-col flex-1 min-h-0 min-w-0 bg-[#e7ebf0]">

        <!-- Empty state (desktop: shown when no chat selected) -->
        <div id="empty-state" class="hidden md:flex absolute inset-0 z-20 flex-col items-center justify-center text-gray-400 px-6 bg-[#e7ebf0]">
            <svg class="w-24 h-24 text-gray-300 mb-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p class="text-lg font-medium text-gray-500">Pilih user untuk mulai chat</p>
            <p class="text-sm text-gray-400 mt-1">Pilih percakapan dari sidebar kiri</p>
        </div>

        <!-- Chat content -->
        <div id="chat-inner" class="hidden flex-col flex-1 min-h-0">
            <div class="bg-[#2ea6ff] px-4 py-3 text-white flex items-center shrink-0 relative">
                <button id="back-to-accounts" class="hover:bg-[#1e96ef] rounded-lg p-2 transition mr-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19L5 12L12 5"/></svg>
                </button>
                <span id="chat-title" class="font-semibold text-base truncate">Chat</span>
            </div>
            <div id="chat-container" class="flex-1 overflow-y-auto min-h-0 px-3 py-4 bg-[#e7ebf0]">
                <div id="chat-loading" class="hidden text-center text-gray-400 text-sm py-8">Memuat pesan...</div>
                <div id="wrapper-chat"></div>
            </div>
            <form id="form-chat" class="shrink-0">
                <div class="bg-white px-3 py-2 flex items-center gap-2">
                    <input type="text" name="content" placeholder="Tulis pesan..." class="flex-1 border-0 rounded-2xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2ea6ff] bg-[#f0f4f8] transition min-h-[44px]" id="content" autocomplete="off">
                    <button type="submit" class="bg-[#2ea6ff] text-white rounded-full w-[44px] h-[44px] hover:bg-[#1e96ef] active:scale-95 transition-all flex items-center justify-center shrink-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13"/></svg>
                    </button>
                </div>
            </form>

<script>
let userId = null;
let receiverId = null;
let editingMessageId = null;
const wrapperChat = document.getElementById('wrapper-chat');
const chatContainer = document.getElementById('chat-container');
const form = document.getElementById('form-chat');
const contentInput = document.getElementById('content');

function isDesktop() {
    return window.innerWidth >= 768;
}

async function apiFetch(url, options) {
    const res = await fetch(url, options);
    const text = await res.text();
    let data;
    try { data = JSON.parse(text); } catch (e) {
        throw new Error('Invalid JSON response');
    }
    if (data.status === 'error' && data.message === 'Unauthorized') {
        window.location.replace('login.php');
        throw new Error('Unauthorized');
    }
    return data;
}

function showAccounts() {
    receiverId = null;
    if (!isDesktop()) {
        document.getElementById('accounts-view').classList.remove('hidden');
        document.getElementById('chat-view').classList.add('hidden');
    }
    document.getElementById('empty-state').classList.remove('hidden');
    document.getElementById('chat-inner').classList.add('hidden');
    if (pusher) {
        pusher.unsubscribe('chat');
    }
}

let chatSwitching = false;

function showChat(id) {
    if (chatSwitching) return;
    if (id === receiverId) return;
    chatSwitching = true;
    receiverId = id;
    if (!isDesktop()) {
        document.getElementById('accounts-view').classList.add('hidden');
        document.getElementById('chat-view').classList.remove('hidden');
    }
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('chat-inner').classList.remove('hidden');
    wrapperChat.innerHTML = '';
    document.getElementById('chat-loading').classList.remove('hidden');
    loadChatTitle().then(() => loadMessages()).then(() => { initPusher(); }).finally(() => {
        document.getElementById('chat-loading').classList.add('hidden');
        chatSwitching = false;
    });
}

document.getElementById('back-to-accounts').addEventListener('click', showAccounts);

const settingsBtn = document.getElementById('settings-btn');
const settingsPanel = document.getElementById('settings-panel');
const settingsOverlay = document.getElementById('settings-overlay');

function openSettings() {
    settingsOverlay.classList.remove('hidden');
    requestAnimationFrame(() => {
        settingsOverlay.classList.remove('opacity-0');
        settingsPanel.classList.remove('-translate-x-full');
    });
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
        if (data.status === 'success') {
            document.getElementById('set-name').value = data.user.name;
            document.getElementById('set-email').value = data.user.email;
        }
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
    if (!currentPassword) {
        msg.textContent = 'Kata sandi saat ini wajib diisi';
        msg.className = 'text-xs text-red-500';
        msg.classList.remove('hidden');
        return;
    }
    try {
        const data = await apiFetch('api.php?action=updateProfile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                name: document.getElementById('set-name').value.trim(),
                email: document.getElementById('set-email').value.trim(),
                currentPassword: currentPassword,
                newPassword: document.getElementById('set-new-password').value
            })
        });
        if (data.status === 'success') {
            msg.textContent = 'Profil berhasil diperbarui';
            msg.className = 'text-xs text-green-600';
            msg.classList.remove('hidden');
            document.getElementById('set-new-password').value = '';
            document.getElementById('set-current-password').value = '';
            loadProfile();
        } else {
            msg.textContent = data.message;
            msg.className = 'text-xs text-red-500';
            msg.classList.remove('hidden');
        }
    } catch (err) {
        msg.textContent = 'Kesalahan: ' + err.message;
        msg.className = 'text-xs text-red-500';
        msg.classList.remove('hidden');
    }
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
            div.className = 'flex items-center px-4 py-3 hover:bg-gray-100 active:bg-gray-200 transition cursor-pointer min-h-[56px]';
            div.onclick = () => showChat(user.id);
            div.innerHTML = `
                <div class="w-11 h-11 ${color} rounded-full flex items-center justify-center text-white font-semibold text-base shrink-0 shadow-sm">${initial}</div>
                <div class="ml-3 flex-1 min-w-0">
                    <h2 class="text-[15px] font-semibold text-gray-900">${escapeHtml(name)}</h2>
                    <p class="text-xs text-gray-400 truncate">Ketuk untuk mulai chat</p>
                </div>
            `;
            container.appendChild(div);
        });
    } catch (err) {
        document.getElementById('user-list').innerHTML = '<div class="text-center text-gray-400 text-sm py-8">Koneksi terputus. Silakan refresh.</div>';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

async function loadChatTitle() {
    try {
        const data = await apiFetch('api.php?action=getUserName&id=' + receiverId);
        if (data.status === 'success') {
            document.getElementById('chat-title').textContent = data.name.charAt(0).toUpperCase() + data.name.slice(1);
        }
    } catch (err) {}
}

async function loadMessages() {
    try {
        const data = await apiFetch('api.php?action=getMessages&receiver_id=' + receiverId);
        if (data.status === 'error') {
            wrapperChat.innerHTML = '<div class="text-center text-gray-400 text-sm py-8">Gagal memuat pesan. Silakan refresh.</div>';
            return;
        }
        userId = data.user_id;
        wrapperChat.innerHTML = '';
        data.messages.forEach(msg => appendMessage(msg.id, msg.sender_id, msg.content));
        scrollToBottom();
    } catch (err) {
        if (err.message === 'Unauthorized') throw err;
        wrapperChat.innerHTML = '<div class="text-center text-gray-400 text-sm py-8">Koneksi terputus. Silakan refresh.</div>';
    }
}

/**
 * Builds the small "time / read-receipt" row shown under a bubble.
 */
function buildMeta(isOwn, timeText) {
    const meta = document.createElement('div');
    meta.className = 'msg-meta ' + (isOwn ? 'msg-meta-own' : 'msg-meta-other');
    meta.innerHTML = `<span>${escapeHtml(timeText)}</span>`;
    if (isOwn) {
        const receipt = document.createElement('span');
        receipt.className = 'read-receipt';
        receipt.textContent = '✓✓';
        receipt.title = 'Dibaca';
        meta.appendChild(receipt);
    }
    return meta;
}

/**
 * Appends a single message. Consecutive messages from the same sender
 * are stacked vertically inside the same .msg-col group (WhatsApp/Telegram
 * style), each as its own .msg-item carrying its own data-message-id so
 * edit/delete events from Pusher can target the exact bubble.
 */
function appendMessage(messageId, senderId, content) {
    const isOwn = senderId == userId;
    const timeText = messageId && String(messageId).startsWith('temp-') ? 'Mengirim...' : 'Terkirim';

    const item = document.createElement('div');
    item.className = 'msg-item';
    item.dataset.messageId = messageId;

    const bubble = document.createElement('div');
    bubble.className = isOwn ? 'bubble-own' : 'bubble-other';
    bubble.textContent = content;
    item.appendChild(bubble);
    item.appendChild(buildMeta(isOwn, timeText));

    const lastWrapper = wrapperChat.lastElementChild;
    const sameSender = lastWrapper && lastWrapper.dataset.senderId == String(senderId);

    if (sameSender) {
        const existingColumn = lastWrapper.querySelector('.msg-col');
        if (existingColumn) {
            existingColumn.appendChild(item);
            return;
        }
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

function showMenu(btn, messageId, content) {
    document.querySelectorAll('#chat-view .group > div:last-child').forEach(m => {
        if (m !== btn.nextElementSibling) m.classList.add('hidden');
    });
    btn.nextElementSibling.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    document.querySelectorAll('#chat-view .group > div:last-child').forEach(menu => {
        if (!menu.classList.contains('hidden') && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });
});

function editMessage(messageId, bubble) {
    const originalText = bubble.textContent;
    const input = document.createElement('input');
    input.type = 'text';
    input.value = originalText;
    input.className = 'w-full bg-white text-gray-800 p-2 rounded-lg border border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm';
    const actions = document.createElement('div');
    actions.className = 'flex gap-1 mt-1 justify-end';
    const saveBtn = document.createElement('button');
    saveBtn.type = 'button';
    saveBtn.className = 'text-xs bg-[#2ea6ff] text-white px-2.5 py-1 rounded-lg hover:bg-[#1e96ef] transition';
    saveBtn.textContent = 'Simpan';
    const cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.className = 'text-xs bg-gray-200 text-gray-600 px-2.5 py-1 rounded-lg hover:bg-gray-300 transition';
    cancelBtn.textContent = 'Batal';
    actions.appendChild(cancelBtn);
    actions.appendChild(saveBtn);
    bubble.innerHTML = '';
    bubble.className = 'bg-white p-2 rounded-2xl rounded-br-sm shadow-sm border border-gray-200';
    bubble.appendChild(input);
    bubble.appendChild(actions);
    input.focus();
    input.setSelectionRange(input.value.length, input.value.length);
    cancelBtn.onclick = function() { cancelEdit(messageId, bubble, originalText); };
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') saveEdit(messageId, bubble, input.value);
        else if (e.key === 'Escape') cancelEdit(messageId, bubble, originalText);
    });
    saveBtn.onclick = function() { saveEdit(messageId, bubble, input.value); };
}

async function saveEdit(messageId, bubble, newContent) {
    newContent = newContent.trim();
    if (!newContent || editingMessageId) return;
    editingMessageId = messageId;
    try {
        await apiFetch('api.php?action=editMessage', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ messageId, content: newContent })
        });
    } catch (err) {}
    bubble.innerHTML = '';
    bubble.className = 'bubble-own';
    bubble.textContent = newContent;
    editingMessageId = null;
}

function cancelEdit(messageId, bubble, originalText) {
    bubble.innerHTML = '';
    bubble.className = 'bubble-own';
    bubble.textContent = originalText;
}

async function deleteMessage(messageId, wrapper) {
    if (!confirm('Hapus pesan ini?')) return;
    try {
        const data = await apiFetch('api.php?action=deleteMessage', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ messageId })
        });
        if (data.status === 'success') { wrapper.remove(); }
        else { alert('Gagal menghapus: ' + data.message); }
    } catch (err) {
        if (err.message === 'Unauthorized') throw err;
        alert('Gagal menghapus pesan');
    }
}

function scrollToBottom() {
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const chatContent = contentInput.value.trim();
    if (!chatContent) return;
    const tempId = 'temp-' + Date.now();
    appendMessage(tempId, userId, chatContent);
    scrollToBottom();
    try {
        const data = await apiFetch('api.php?action=saveChat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ content: chatContent, receiverId: receiverId })
        });
        if (data.status === 'success' && data.message_id) {
            const el = wrapperChat.querySelector(`[data-message-id="${tempId}"]`);
            if (el) el.dataset.messageId = data.message_id;
        }
    } catch (err) { console.error('Error:', err); }
    contentInput.value = '';
});

contentInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        form.dispatchEvent(new Event('submit'));
    }
});

let pusher = null;
let channel = null;

function initPusher() {
    if (pusher) {
        pusher.disconnect();
    }
    pusher = new Pusher('cd37a9cac61b05b6944b', { cluster: 'ap1' });
    channel = pusher.subscribe('chat');
    channel.bind('receive', function(data) {
        if (data.sender_id == userId) return;
        if (data.sender_id != receiverId) return;
        appendMessage(data.message_id, data.sender_id, data.content);
        scrollToBottom();
    });
    channel.bind('edit', function(data) {
        const isForThisChat = (data.sender_id == userId && data.receiver_id == receiverId) || (data.sender_id == receiverId && data.receiver_id == userId);
        if (!isForThisChat) return;
        const item = wrapperChat.querySelector(`[data-message-id="${data.message_id}"]`);
        if (item) {
            const bubble = item.querySelector('.bubble-own, .bubble-other');
            if (bubble) bubble.textContent = data.content;
        }
    });
    channel.bind('delete', function(data) {
        const isForThisChat = (data.sender_id == userId && data.receiver_id == receiverId) || (data.sender_id == receiverId && data.receiver_id == userId);
        if (!isForThisChat) return;
        const item = wrapperChat.querySelector(`[data-message-id="${data.message_id}"]`);
        if (item) {
            const col = item.parentElement;
            const block = col ? col.parentElement : null;
            item.remove();
            if (col && col.children.length === 0 && block) {
                block.remove();
            }
        }
    });
}

loadUsers();
loadProfile();

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('sw.js');
}
</script>
</body>
</html>