<?php
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
        $stmt = $pdo->query("SELECT * FROM hukum_tua ORDER BY id ASC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['items' => $data]);
        exit;
    }

    // protected actions: require token and admin role
    $token = $_POST['token'] ?? null;
    if (!$token)
        json_err('Token required', 401);

    $s = $pdo->prepare("SELECT u.id,u.role FROM sessions s JOIN users u ON u.id = s.user_id WHERE s.session_token = ? LIMIT 1");
    $s->execute([$token]);
    $user = $s->fetch(PDO::FETCH_ASSOC);
    if (!$user)
        json_err('Invalid session', 401);
    if (($user['role'] ?? '') !== 'admin')
        json_err('Access denied', 403);

    if ($action === 'create') {
        $periode = trim($_POST['periode'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        if (!$periode || !$nama)
            json_err('Periode dan Nama wajib', 400);
        $ins = $pdo->prepare("INSERT INTO hukum_tua (periode,nama,keterangan) VALUES (?,?,?)");
        $ins->execute([$periode, $nama, $keterangan]);
        echo json_encode(['message' => 'Item created']);
        exit;
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $periode = trim($_POST['periode'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        if (!$id || !$periode || !$nama)
            json_err('ID, Periode dan Nama wajib', 400);
        $u = $pdo->prepare("UPDATE hukum_tua SET periode=?, nama=?, keterangan=? WHERE id=?");
        $u->execute([$periode, $nama, $keterangan, $id]);
        echo json_encode(['message' => 'Item updated']);
        exit;
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id)
            json_err('ID tidak valid', 400);
        $d = $pdo->prepare("DELETE FROM hukum_tua WHERE id=?");
        $d->execute([$id]);
        echo json_encode(['message' => 'Item deleted']);
        exit;
    }

    json_err('Unknown action', 400);

} catch (PDOException $e) {
    json_err('Database error', 500);
}
?>