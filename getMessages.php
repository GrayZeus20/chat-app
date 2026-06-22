<?php
session_start();
require_once __DIR__ . '/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$receiverId = (int)($_GET['receiver_id'] ?? 0);
$userId = $_SESSION['user_id'];

if (!$receiverId) {
    echo json_encode(['status' => 'error', 'message' => 'receiver_id required']);
    exit;
}

$stmt = $conn->prepare("SELECT id, sender_id, receiver_id, content, created_at FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
$stmt->bind_param("iiii", $userId, $receiverId, $receiverId, $userId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();

echo json_encode([
    'status' => 'success',
    'messages' => $messages,
    'user_id' => $userId
]);
