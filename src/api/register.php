<?php
require 'db.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));

// Validasi input
if (
    !isset($data->username) || empty(trim($data->username)) ||
    !isset($data->email) || empty(trim($data->email)) ||
    !isset($data->password) || empty($data->password)
) {
    http_response_code(400);
    echo json_encode(["error" => "Semua field wajib diisi."]);
    exit;
}

$username = trim($data->username);
$email = trim($data->email);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Email tidak valid."]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(["error" => "Username atau email sudah terdaftar."]);
        exit;
    }

    // GANTI MD5 KE PASSWORD_HASH
    $hash = password_hash($data->password, PASSWORD_BCRYPT);

    $ins = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $ins->execute([$username, $email, $hash]);

    http_response_code(201);
    echo json_encode(["message" => "Akun berhasil dibuat. Silakan login."]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Terjadi kesalahan server: " . $e->getMessage()]);
    exit;
}
?>