<?php

define('PUSHER_APP_ID', '1895901');
define('PUSHER_APP_KEY', '579716c8e98cf3fb7f38');
define('PUSHER_APP_SECRET', 'REMOVED_BY_PURGE');
define('PUSHER_APP_CLUSTER', 'ap1');

function isPusherConfigured(): bool
{
    return PUSHER_APP_ID !== 'YOUR_PUSHER_APP_ID'
        && PUSHER_APP_SECRET !== 'YOUR_PUSHER_SECRET';
}
