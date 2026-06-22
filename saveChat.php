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
$conversationId = (int) ($data['conversationId'] ?? 1);
$senderId = $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, content) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $conversationId, $senderId, $content);

if ($stmt->execute()) {

    if (isPusherConfigured()) {
        require_once __DIR__ . '/vendor/autoload.php';

        $options = [
            'cluster' => PUSHER_APP_CLUSTER,
            'useTLS' => true
        ];
        $pusher = new Pusher\Pusher(PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_APP_ID, $options);

        $pusher->trigger('chat', 'receive', [
            'sender_id' => $senderId,
            'content' => $content
        ]);
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
