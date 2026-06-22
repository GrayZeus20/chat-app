<?php

define('PUSHER_APP_ID', 'YOUR_PUSHER_APP_ID');
define('PUSHER_APP_KEY', 'cd37a9cac61b05b6944b');
define('PUSHER_APP_SECRET', 'YOUR_PUSHER_SECRET');
define('PUSHER_APP_CLUSTER', 'ap1');

function isPusherConfigured(): bool
{
    return PUSHER_APP_ID !== 'YOUR_PUSHER_APP_ID'
        && PUSHER_APP_SECRET !== 'YOUR_PUSHER_SECRET';
}
