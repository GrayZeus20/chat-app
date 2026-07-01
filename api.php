<?php
$sessionLifetime = 86400 * 30;
ini_set('session.gc_maxlifetime', $sessionLifetime);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
session_set_cookie_params(['lifetime' => $sessionLifetime, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
ob_start();
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Internal server error']);
    }
});

$action = $_GET['action'] ?? '';

// checkSession tidak butuh database — biar tetap jalan meski MySQL down
if ($action !== 'checkSession') {
    require_once __DIR__ . '/connection.php';
}

switch ($action) {
    case 'login': handleLogin(); break;
    case 'register': handleRegister(); break;
    case 'getUsers': handleGetUsers(); break;
    case 'getMessages': handleGetMessages(); break;
    case 'saveChat': handleSaveChat(); break;
    case 'editMessage': handleEditMessage(); break;
    case 'deleteMessage': handleDeleteMessage(); break;
    case 'updateProfile': handleUpdateProfile(); break;
    case 'getProfile': handleGetProfile(); break;
    case 'getUserName': handleGetUserName(); break;
    case 'checkSession': handleCheckSession(); break;
    default: echo json_encode(['status' => 'error', 'message' => 'Unknown action']); break;
}

function handleLogin() {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email dan password harus diisi!']);
        return;
    }
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email atau password salah!']);
    }
    $stmt->close();
}

function handleRegister() {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi!']);
        return;
    }
    $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        echo json_encode(['status' => 'error', 'message' => 'Email sudah terdaftar!']);
        $stmt->close();
        return;
    }
    $stmt->close();
    $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(name) = LOWER(?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        echo json_encode(['status' => 'error', 'message' => 'Nama sudah digunakan!']);
        $stmt->close();
        return;
    }
    $stmt->close();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mendaftar, coba lagi']);
    }
    $stmt->close();
}

function handleGetUsers() {
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
    session_write_close();
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE id != ? ORDER BY name ASC");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    echo json_encode(['status' => 'success', 'users' => $users]);
}

function handleGetMessages() {
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
    $receiverId = (int)($_GET['receiver_id'] ?? 0);
    $userId = $_SESSION['user_id'];
    if (!$receiverId) {
        echo json_encode(['status' => 'error', 'message' => 'receiver_id required']);
        return;
    }
    session_write_close();
    $stmt = $conn->prepare("SELECT id, sender_id, receiver_id, content, created_at FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt->bind_param("iiii", $userId, $receiverId, $receiverId, $userId);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $stmt->error]);
        $stmt->close();
        return;
    }
    $result = $stmt->get_result();
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Result failed: ' . $stmt->error]);
        $stmt->close();
        return;
    }
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
    echo json_encode(['status' => 'success', 'messages' => $messages, 'user_id' => $userId]);
}

function handleSaveChat() {
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['content']) || trim($data['content']) === '') {
        echo json_encode(['status' => 'error', 'message' => 'Content is required']);
        return;
    }
    $content = $data['content'];
    $receiverId = (int)($data['receiverId'] ?? 0);
    $senderId = $_SESSION['user_id'];
    session_write_close();
    if (!$receiverId) {
        echo json_encode(['status' => 'error', 'message' => 'receiverId required']);
        return;
    }
    $conversationId = (int)($data['conversationId'] ?? 1);
    $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, receiver_id, content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $conversationId, $senderId, $receiverId, $content);
    if ($stmt->execute()) {
        $messageId = $stmt->insert_id;
        if (isPusherConfigured()) {
            require_once __DIR__ . '/vendor/autoload.php';
            $options = ['cluster' => PUSHER_APP_CLUSTER, 'useTLS' => true];
            $pusher = new Pusher\Pusher(PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_APP_ID, $options);
            $pusher->trigger('chat', 'receive', [
                'message_id' => $messageId, 'sender_id' => $senderId,
                'receiver_id' => $receiverId, 'content' => $content
            ]);
        }
        echo json_encode(['status' => 'success', 'message_id' => $messageId]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
}

function handleEditMessage() {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        return;
    }
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $messageId = (int)($data['messageId'] ?? 0);
    $content = trim($data['content'] ?? '');
    $senderId = $_SESSION['user_id'];
    session_write_close();
    if (!$messageId || empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        return;
    }
    $stmt = $conn->prepare("SELECT sender_id, receiver_id FROM messages WHERE id = ?");
    $stmt->bind_param("i", $messageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();
    $stmt->close();
    if (!$message) {
        echo json_encode(['status' => 'error', 'message' => 'Message not found']);
        return;
    }
    if ($message['sender_id'] != $senderId) {
        echo json_encode(['status' => 'error', 'message' => 'Not your message']);
        return;
    }
    $stmt = $conn->prepare("UPDATE messages SET content = ? WHERE id = ?");
    $stmt->bind_param("si", $content, $messageId);
    if ($stmt->execute()) {
        if (isPusherConfigured()) {
            require_once __DIR__ . '/vendor/autoload.php';
            $options = ['cluster' => PUSHER_APP_CLUSTER, 'useTLS' => true];
            $pusher = new Pusher\Pusher(PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_APP_ID, $options);
            $pusher->trigger('chat', 'edit', [
                'message_id' => $messageId, 'content' => $content,
                'sender_id' => $senderId, 'receiver_id' => $message['receiver_id']
            ]);
        }
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
}

function handleDeleteMessage() {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        return;
    }
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $messageId = (int)($data['messageId'] ?? 0);
    $senderId = $_SESSION['user_id'];
    session_write_close();
    if (!$messageId) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        return;
    }
    $stmt = $conn->prepare("SELECT sender_id, receiver_id FROM messages WHERE id = ?");
    $stmt->bind_param("i", $messageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();
    $stmt->close();
    if (!$message) {
        echo json_encode(['status' => 'error', 'message' => 'Message not found']);
        return;
    }
    if ($message['sender_id'] != $senderId) {
        echo json_encode(['status' => 'error', 'message' => 'Not your message']);
        return;
    }
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $messageId);
    if ($stmt->execute()) {
        if (isPusherConfigured()) {
            require_once __DIR__ . '/vendor/autoload.php';
            $options = ['cluster' => PUSHER_APP_CLUSTER, 'useTLS' => true];
            $pusher = new Pusher\Pusher(PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_APP_ID, $options);
            $pusher->trigger('chat', 'delete', [
                'message_id' => $messageId, 'sender_id' => $senderId,
                'receiver_id' => $message['receiver_id']
            ]);
        }
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
}

function handleUpdateProfile() {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        return;
    }
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];
    session_write_close();
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $currentPassword = $data['currentPassword'] ?? '';
    $newPassword = $data['newPassword'] ?? '';
    if (empty($currentPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Password saat ini wajib diisi']);
        return;
    }
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    if (!$user || !password_verify($currentPassword, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Password saat ini salah']);
        return;
    }
    $updates = [];
    $params = [];
    $types = '';
    if (!empty($name)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(name) = LOWER(?) AND id != ?");
        $stmt->bind_param("si", $name, $userId);
        $stmt->execute();
        $dup = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($dup) {
            echo json_encode(['status' => 'error', 'message' => 'Nama sudah digunakan']);
            return;
        }
        $updates[] = "name = ?";
        $params[] = $name;
        $types .= 's';
    }
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) AND id != ?");
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $dup = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($dup) {
            echo json_encode(['status' => 'error', 'message' => 'Email sudah digunakan']);
            return;
        }
        $updates[] = "email = ?";
        $params[] = $email;
        $types .= 's';
    }
    if (!empty($newPassword)) {
        if (strlen($newPassword) < 4) {
            echo json_encode(['status' => 'error', 'message' => 'Password minimal 4 karakter']);
            return;
        }
        $updates[] = "password = ?";
        $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
        $types .= 's';
    }
    if (empty($updates)) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada perubahan']);
        return;
    }
    $params[] = $userId;
    $types .= 'i';
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
}

function handleGetProfile() {
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
    session_write_close();
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    if ($user) {
        echo json_encode(['status' => 'success', 'user' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
}

function handleGetUserName() {
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid id']);
        return;
    }
    session_write_close();
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    if ($user) {
        echo json_encode(['status' => 'success', 'name' => $user['name']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
}

function handleCheckSession() {
    if (isset($_SESSION['user_id'])) {
        session_write_close();
        echo json_encode(['status' => 'success', 'user_id' => $_SESSION['user_id']]);
    } else {
        session_write_close();
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    }
}
