<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "chat_app";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

