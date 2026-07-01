<?php

// Load production credentials from env.php (for GitHub Actions or InfinityFree)
if (file_exists(__DIR__ . '/env.php')) {
    require_once __DIR__ . '/env.php';
    $servername = defined('DB_HOST') ? DB_HOST : getenv('DB_HOST');
    $username   = defined('DB_USER') ? DB_USER : getenv('DB_USER');
    $password   = defined('DB_PASS') ? DB_PASS : getenv('DB_PASS');
    $database   = defined('DB_NAME') ? DB_NAME : getenv('DB_NAME');
} elseif (getenv('DB_HOST') && getenv('DB_USER') && getenv('DB_NAME')) {
    $servername = getenv('DB_HOST');
    $username   = getenv('DB_USER');
    $password   = getenv('DB_PASS') ?: '';
    $database   = getenv('DB_NAME');
} else {
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $database   = "chat_app";
}

$conn = @new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$conn->set_charset('utf8mb4');
