<?php
session_start();
require_once __DIR__ . '/connection.php';

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
$userId = $_SESSION['user_id'];

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$currentPassword = $data['currentPassword'] ?? '';
$newPassword = $data['newPassword'] ?? '';

if (empty($currentPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Password saat ini wajib diisi']);
    exit;
}

$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($currentPassword, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Password saat ini salah']);
    exit;
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
        exit;
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
        exit;
    }
    $updates[] = "email = ?";
    $params[] = $email;
    $types .= 's';
}

if (!empty($newPassword)) {
    if (strlen($newPassword) < 4) {
        echo json_encode(['status' => 'error', 'message' => 'Password minimal 4 karakter']);
        exit;
    }
    $updates[] = "password = ?";
    $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
    $types .= 's';
}

if (empty($updates)) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada perubahan']);
    exit;
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
