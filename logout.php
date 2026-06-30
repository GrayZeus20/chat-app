<?php
$sessionLifetime = 86400 * 30;
session_set_cookie_params($sessionLifetime);
session_start();
session_destroy();
header('Location: index.html');
exit;
