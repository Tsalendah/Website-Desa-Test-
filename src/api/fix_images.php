<?php
require 'db.php'; // pastikan this file memberi $pdo

// Simple scanner: periksa setiap artikel, jika file tidak ada coba cari kandidat di folder uploads
$uploadsDir = realpath(__DIR__ . '/../uploads');
if (!$uploadsDir || !is_dir($uploadsDir)) {
    echo "Uploads folder not found: $uploadsDir\n";
    exit;
}

$stmt = $pdo->query("SELECT id, image_url FROM articles");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
$fixed = [];

foreach ($articles as $a) {
    $id = $a['id'];
    $img = $a['image_url'] ?? '';
    if (!$img)
        continue;

    $fsPath = realpath(__DIR__ . '/../' . $img);
    if ($fsPath && file_exists($fsPath))
        continue; // sudah ada

    // coba cari file dengan basename yang mengandung bagian unik (hapus spasi/karakter)
    $base = pathinfo($img, PATHINFO_BASENAME);
    $baseClean = preg_replace('/[^A-Za-z0-9]/', '', pathinfo($base, PATHINFO_FILENAME));

    $candidates = glob($uploadsDir . "/*{$baseClean}*.*");
    if (count($candidates) === 1) {
        $found = $candidates[0];
        $webPath = 'uploads/' . basename($found);
        $u = $pdo->prepare("UPDATE articles SET image_url = ? WHERE id = ?");
        $u->execute([$webPath, $id]);
        $fixed[] = "id=$id -> $webPath";
    } elseif (count($candidates) > 1) {
        $fixed[] = "id=$id -> multiple matches (skip)";
    } else {
        $fixed[] = "id=$id -> not found";
    }
}

echo "Scan complete:\n";
echo implode("\n", $fixed);
?>