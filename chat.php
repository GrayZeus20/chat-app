<?php
session_start();
$query = $_SERVER['QUERY_STRING'];
$redirect = 'chat.html' . ($query ? '?' . $query : '');
header('Location: ' . $redirect);
exit;