<?php
require __DIR__ . '/db.php'; // gunakan session/token dari sistem Anda
header('Content-Type: application/json; charset=UTF-8');

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$allowedDirs = [
    realpath(__DIR__ . '/../uploads'),
    realpath(__DIR__ . '/../Gallery'),
];
function json($d, $code = 200)
{
    http_response_code($code);
    echo json_encode($d);
    exit;
}

if ($action === 'list') {
    $items = [];
    foreach ($allowedDirs as $d) {
        if (!$d || !is_dir($d))
            continue;
        foreach (glob($d . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE) as $f) {
            $items[] = $f;
        }
    }
    sort($items);
    $out = [];
    $doc = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
    foreach ($items as $f) {
        $real = str_replace('\\', '/', realpath($f));
        $web = str_replace($doc, '', $real);
        $out[] = ['src' => $web, 'name' => basename($f), 'path' => $real];
    }
    json(['items' => $out]);
}

// require token + admin
$token = $_POST['token'] ?? null;
if (!$token)
    json(['error' => 'Token required'], 401);

$s = $pdo->prepare("SELECT u.id,u.role FROM sessions s JOIN users u ON u.id = s.user_id WHERE s.session_token = ? LIMIT 1");
$s->execute([$token]);
$user = $s->fetch(PDO::FETCH_ASSOC);
if (!$user)
    json(['error' => 'Invalid session'], 401);
if (($user['role'] ?? '') !== 'admin')
    json(['error' => 'Access denied'], 403);

if ($action === 'upload') {
    if (empty($_FILES['images']))
        json(['error' => 'No files'], 400);
    $saved = 0;
    $errors = [];
    $targetDir = realpath(__DIR__ . '/../uploads') ?: (__DIR__ . '/../uploads');
    if (!is_dir($targetDir))
        mkdir($targetDir, 0755, true);
    foreach ($_FILES['images']['error'] as $i => $err) {
        if ($err !== UPLOAD_ERR_OK) {
            $errors[] = "Error file #$i";
            continue;
        }
        $tmp = $_FILES['images']['tmp_name'][$i];
        $orig = $_FILES['images']['name'][$i];
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $errors[] = "$orig: ekstensi tidak diperbolehkan";
            continue;
        }
        $fname = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = $targetDir . DIRECTORY_SEPARATOR . $fname;
        if (move_uploaded_file($tmp, $dest))
            $saved++;
        else
            $errors[] = "$orig: gagal dipindahkan";
    }
    json(['message' => "$saved file tersimpan", 'errors' => $errors]);
}

if ($action === 'delete') {
    $src = $_POST['src'] ?? '';
    if (!$src)
        json(['error' => 'src required'], 400);
    // convert src (web path) to realpath
    $doc = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
    $candidate = realpath($doc . $src);
    if (!$candidate)
        json(['error' => 'File tidak ditemukan'], 404);
    $allowed = false;
    foreach ($allowedDirs as $d) {
        if (strpos($candidate, $d) === 0) {
            $allowed = true;
            break;
        }
    }
    if (!$allowed)
        json(['error' => 'Aksi tidak diizinkan'], 403);
    if (!is_file($candidate))
        json(['error' => 'Bukan file'], 400);
    if (!unlink($candidate))
        json(['error' => 'Gagal hapus'], 500);
    json(['message' => 'File dihapus']);
}

json(['error' => 'Unknown action'], 400);
?>