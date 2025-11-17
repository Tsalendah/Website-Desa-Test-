<?php
require 'db.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    // Ambil 3 artikel terbaru, tambahkan image_url
    $stmt = $pdo->query("
        SELECT id, title, image_url 
        FROM articles 
        ORDER BY created_at DESC 
        LIMIT 3
    ");
    $articles = $stmt->fetchAll();

    echo json_encode($articles);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Gagal mengambil artikel."]);
}
?>