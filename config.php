<?php

// Pusher credentials live in env.php (gitignored) or process env — never in git.
if (file_exists(__DIR__ . '/env.php')) {
    require_once __DIR__ . '/env.php';
}

if (!defined('PUSHER_APP_ID')) {
    define('PUSHER_APP_ID', getenv('PUSHER_APP_ID') ?: 'YOUR_PUSHER_APP_ID');
}
if (!defined('PUSHER_APP_KEY')) {
    define('PUSHER_APP_KEY', getenv('PUSHER_APP_KEY') ?: 'YOUR_PUSHER_APP_KEY');
}
if (!defined('PUSHER_APP_SECRET')) {
    define('PUSHER_APP_SECRET', getenv('PUSHER_APP_SECRET') ?: 'YOUR_PUSHER_SECRET');
}
if (!defined('PUSHER_APP_CLUSTER')) {
    define('PUSHER_APP_CLUSTER', getenv('PUSHER_APP_CLUSTER') ?: 'ap1');
}

function isPusherConfigured(): bool
{
    return PUSHER_APP_ID !== '' && PUSHER_APP_ID !== 'YOUR_PUSHER_APP_ID'
        && PUSHER_APP_KEY !== '' && PUSHER_APP_KEY !== 'YOUR_PUSHER_APP_KEY'
        && PUSHER_APP_SECRET !== '' && PUSHER_APP_SECRET !== 'YOUR_PUSHER_SECRET';
}
