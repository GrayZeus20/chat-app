<?php
session_start();
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$messageId = (int) ($data['messageId'] ?? 0);
$senderId = $_SESSION['user_id'];

if (!$messageId) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

$stmt = $conn->prepare("SELECT sender_id, receiver_id FROM messages WHERE id = ?");
$stmt->bind_param("i", $messageId);
$stmt->execute();
$result = $stmt->get_result();
$message = $result->fetch_assoc();
$stmt->close();

if (!$message) {
    echo json_encode(['status' => 'error', 'message' => 'Message not found']);
    exit;
}

if ($message['sender_id'] != $senderId) {
    echo json_encode(['status' => 'error', 'message' => 'Not your message']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
$stmt->bind_param("i", $messageId);

if ($stmt->execute()) {

    if (isPusherConfigured()) {
        require_once __DIR__ . '/vendor/autoload.php';

        $options = [
            'cluster' => PUSHER_APP_CLUSTER,
            'useTLS' => true
        ];
        $pusher = new Pusher\Pusher(PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_APP_ID, $options);

        $pusher->trigger('chat', 'delete', [
            'message_id' => $messageId,
            'sender_id' => $senderId,
            'receiver_id' => $message['receiver_id']
        ]);
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
