<?php
ini_set('display_errors', 0);
error_reporting(0);

require 'db.php';

header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");

function json_error($msg, $code = 400)
{
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

try {
    // Ambil data dari form-data (FormData di client)
    $token = $_POST['token'] ?? null;
    $title = isset($_POST['title']) ? trim($_POST['title']) : null;
    $content = isset($_POST['content']) ? trim($_POST['content']) : null;

    if (!$token || !$title || !$content) {
        json_error('Token, judul, dan konten wajib diisi.', 400);
    }

    // Verifikasi session + ambil user
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.role
        FROM sessions s
        JOIN users u ON u.id = s.user_id
        WHERE s.session_token = ? 
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        json_error('Sesi tidak valid. Silakan login ulang.', 401);
    }
    if (!isset($user['role']) || $user['role'] !== 'admin') {
        json_error('Akses ditolak. Hanya admin bisa mengunggah artikel.', 403);
    }

    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            json_error('Kesalahan upload file (code: ' . $file['error'] . ')', 400);
        }

        // Validasi tipe gambar
        $finfo = @getimagesize($file['tmp_name']);
        if ($finfo === false) {
            json_error('File unggahan bukan gambar yang valid.', 400);
        }
        $mime = $finfo['mime'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $allowed, true)) {
            json_error('Tipe gambar tidak didukung. (jpg, png, gif)', 400);
        }

        // Pastikan folder uploads ada
        $uploadDir = realpath(__DIR__ . '/uploads') ?: (__DIR__ . '/../uploads');
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                json_error('Gagal membuat folder uploads di server.', 500);
            }
        }

        // Simpan file dengan nama unik
        $extMap = [
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif'
        ];
        $ext = $extMap[$mime] ?? '';
        $filename = bin2hex(random_bytes(12)) . $ext;
        $dest = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            json_error('Gagal menyimpan file di server.', 500);
        }

        // Simpan path relatif (untuk dikonsumsi frontend)
        $image_url = 'uploads/' . $filename;
    }

    // Masukkan artikel ke tabel articles
    $ins = $pdo->prepare("INSERT INTO articles (title, content, image_url, author_id) VALUES (?, ?, ?, ?)");
    $ins->execute([$title, $content, $image_url, $user['id'] ?? null]);

    echo json_encode(['message' => 'Artikel berhasil diunggah.']);
    exit;

} catch (PDOException $e) {
    // Jangan kirim stack trace — cukup pesan umum
    json_error('Terjadi kesalahan database.', 500);
} catch (Exception $e) {
    json_error('Terjadi kesalahan server.', 500);
}