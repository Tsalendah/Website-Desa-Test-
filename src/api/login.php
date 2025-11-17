<?php
require 'db.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));

if (
    !isset($data->username) || empty(trim($data->username)) ||
    !isset($data->password) || empty($data->password)
) {
    http_response_code(400);
    echo json_encode(["error" => "Username dan password wajib diisi."]);
    exit;
}

$username = trim($data->username);
$provided = $data->password;

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode(["error" => "Username atau password salah."]);
        exit;
    }

    $stored = $user['password'];
    $password_ok = false;

    // 1) jika hash bcrypt/argon2 => password_verify
    if (is_string($stored) && (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0 || strpos($stored, '$argon2') !== false)) {
        if (password_verify($provided, $stored)) {
            $password_ok = true;
        }
    } else {
        // 2) fallback MD5 (jika sebelumnya pakai md5)
        if (md5($provided) === $stored) {
            $password_ok = true;
            // migrasi ke password_hash
            $newHash = password_hash($provided, PASSWORD_BCRYPT);
            $u = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $u->execute([$newHash, $user['id']]);
        } elseif ($provided === $stored) {
            // 3) fallback plaintext (tidak aman) -> migrasi juga
            $password_ok = true;
            $newHash = password_hash($provided, PASSWORD_BCRYPT);
            $u = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $u->execute([$newHash, $user['id']]);
        }
    }

    if ($password_ok) {
        // buat session token (sudah ada tabel sessions)
        $user_id = $user['id'];
        $session_token = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $stmt = $pdo->prepare("INSERT INTO sessions (user_id, session_token) VALUES (?, ?)");
        $stmt->execute([$user_id, $session_token]);

        http_response_code(200);
        echo json_encode([
            "message" => "Login berhasil.",
            "token" => $session_token,
            "username" => $user['username'],
            "role" => $user['role'] ?? null
        ]);
        exit;
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Username atau password salah."]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Terjadi kesalahan: " . $e->getMessage()]);
    exit;
}