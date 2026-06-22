<?php
session_start();
require_once __DIR__ . '/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$conversationId = (int)($_GET['conversation_id'] ?? 1);

$stmt = $conn->prepare("SELECT id, sender_id, content, created_at FROM messages WHERE conversation_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $conversationId);
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
    'user_id' => $_SESSION['user_id']
]);
