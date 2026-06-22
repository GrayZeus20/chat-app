<?php
session_start();
require_once __DIR__ . '/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$users = [];
$result = $conn->query("SELECT id, name FROM users ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(['status' => 'success', 'users' => $users]);
