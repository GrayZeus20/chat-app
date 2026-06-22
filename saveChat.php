<?php
session_start();
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['content']) || trim($data['content']) === '') {
    echo json_encode(['status' => 'error', 'message' => 'Content is required']);
    exit;
}

$content = $data['content'];
$receiverId = (int) ($data['receiverId'] ?? 0);
$senderId = $_SESSION['user_id'];

if (!$receiverId) {
    echo json_encode(['status' => 'error', 'message' => 'receiverId required']);
    exit;
}

$conversationId = (int) ($data['conversationId'] ?? 1);

$stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, receiver_id, content) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $conversationId, $senderId, $receiverId, $content);

if ($stmt->execute()) {
    $messageId = $stmt->insert_id;

    if (isPusherConfigured()) {
        require_once __DIR__ . '/vendor/autoload.php';

        $options = [
            'cluster' => PUSHER_APP_CLUSTER,
            'useTLS' => true
        ];
        $pusher = new Pusher\Pusher(PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_APP_ID, $options);

        $pusher->trigger('chat', 'receive', [
            'message_id' => $messageId,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'content' => $content
        ]);
    }

    echo json_encode(['status' => 'success', 'message_id' => $messageId]);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
