<?php
require 'db.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->token) || empty($data->token)) {
    http_response_code(400);
    echo json_encode(["error" => "Token tidak ada."]);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE session_token = ?");
    $stmt->execute([$data->token]);

    http_response_code(200);
    echo json_encode(["message" => "Logout berhasil"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Terjadi kesalahan: " . $e->getMessage()]);
}
?>