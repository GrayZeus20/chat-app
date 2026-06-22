<?php
session_start();
require_once __DIR__ . '/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi!']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    echo json_encode(['status' => 'error', 'message' => 'Email sudah terdaftar!']);
    $stmt->close();
    exit;
}
$stmt->close();

$stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(name) = LOWER(?)");
$stmt->bind_param("s", $name);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    echo json_encode(['status' => 'error', 'message' => 'Nama sudah digunakan!']);
    $stmt->close();
    exit;
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
