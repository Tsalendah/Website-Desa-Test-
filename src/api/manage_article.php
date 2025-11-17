<?php
ini_set('display_errors', 0);
error_reporting(0);

require 'db.php';
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");

function json_err($m, $c = 400)
{
    http_response_code($c);
    echo json_encode(['error' => $m]);
    exit;
}

try {
    $action = $_GET['action'] ?? ($_POST['action'] ?? 'list');

    if ($action === 'list') {
        $stmt = $pdo->query("SELECT a.id, a.title, a.image_url, a.created_at, u.username FROM articles a LEFT JOIN users u ON u.id = a.author_id ORDER BY a.created_at DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['articles' => $rows]);
        exit;
    }

    if ($action === 'get') {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id)
            json_err('ID tidak valid', 400);
        $stmt = $pdo->prepare("SELECT a.*, u.username FROM articles a LEFT JOIN users u ON u.id = a.author_id WHERE a.id = ? LIMIT 1");
        $stmt->execute([$id]);
        $art = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$art)
            json_err('Artikel tidak ditemukan', 404);
        echo json_encode(['article' => $art]);
        exit;
    }

    // protected actions: delete, update -> require token and admin
    $token = $_POST['token'] ?? null;
    if (!$token)
        json_err('Token harus dikirim.', 401);

    $s = $pdo->prepare("SELECT u.id, u.username, u.role FROM sessions s JOIN users u ON u.id = s.user_id WHERE s.session_token = ? LIMIT 1");
    $s->execute([$token]);
    $user = $s->fetch(PDO::FETCH_ASSOC);
    if (!$user)
        json_err('Sesi tidak valid', 401);
    if (($user['role'] ?? '') !== 'admin')
        json_err('Akses ditolak', 403);

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id)
            json_err('ID tidak valid', 400);
        // ambil image untuk dihapus
        $q = $pdo->prepare("SELECT image_url FROM articles WHERE id = ? LIMIT 1");
        $q->execute([$id]);
        $r = $q->fetch(PDO::FETCH_ASSOC);
        if ($r && !empty($r['image_url'])) {
            $fs = realpath(__DIR__ . '/../' . $r['image_url']);
            if ($fs && file_exists($fs))
                @unlink($fs);
        }
        $d = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $d->execute([$id]);
        echo json_encode(['message' => 'Artikel dihapus']);
        exit;
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if (!$id || !$title || !$content)
            json_err('ID, judul, dan konten wajib.', 400);

        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK)
                json_err('Upload error', 400);
            $finfo = @getimagesize($file['tmp_name']);
            if ($finfo === false)
                json_err('Bukan gambar', 400);
            $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif'];
            $mime = $finfo['mime'];
            if (!isset($allowed[$mime]))
                json_err('Tipe gambar tidak didukung', 400);

            $uploadDir = realpath(__DIR__ . '/../uploads') ?: (__DIR__ . '/../uploads');
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true))
                    json_err('Gagal membuat folder uploads', 500);
            }
            $filename = bin2hex(random_bytes(12)) . $allowed[$mime];
            $dest = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
            if (!move_uploaded_file($file['tmp_name'], $dest))
                json_err('Gagal menyimpan file', 500);

            // hapus old image
            $q = $pdo->prepare("SELECT image_url FROM articles WHERE id = ? LIMIT 1");
            $q->execute([$id]);
            $old = $q->fetch(PDO::FETCH_ASSOC);
            if ($old && !empty($old['image_url'])) {
                $fs = realpath(__DIR__ . '/../' . $old['image_url']);
                if ($fs && file_exists($fs))
                    @unlink($fs);
            }

            $image_url = 'uploads/' . $filename;
        }

        if ($image_url !== null) {
            $u = $pdo->prepare("UPDATE articles SET title = ?, content = ?, image_url = ? WHERE id = ?");
            $u->execute([$title, $content, $image_url, $id]);
        } else {
            $u = $pdo->prepare("UPDATE articles SET title = ?, content = ? WHERE id = ?");
            $u->execute([$title, $content, $id]);
        }

        echo json_encode(['message' => 'Perubahan disimpan']);
        exit;
    }

    json_err('Aksi tidak dikenali', 400);

} catch (PDOException $e) {
    json_err('Kesalahan database', 500);
} catch (Exception $e) {
    json_err('Kesalahan server', 500);
}
?>