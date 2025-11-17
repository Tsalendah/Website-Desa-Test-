<?php
// 1. Hubungkan ke database
require 'api/db.php'; //

$article = null;
$error = null;

// 2. Ambil ID artikel dari URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = $_GET['id'];

    try {
        // 3. Ambil data artikel dari database
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);
        $article = $stmt->fetch();

        if (!$article) {
            $error = "Artikel tidak ditemukan.";
        }
    } catch (Exception $e) {
        $error = "Gagal mengambil data: " . $e->getMessage();
    }
} else {
    $error = "ID Artikel tidak valid.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $article ? htmlspecialchars($article['title']) : 'Artikel Tidak Ditemukan'; ?> - Inovasi Digital
    </title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body {
            margin: 0;
        }

        .article-content h1,
        .article-content h2,
        .article-content h3 {
            font-size: 1.5em;
            font-weight: bold;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }

        .article-content p {
            margin-bottom: 1em;
            line-height: 1.6;
        }

        .article-content ul,
        .article-content ol {
            margin-left: 1.5em;
            margin-bottom: 1em;
            list-style: revert;
        }

        .article-content a {
            color: #3b82f6;
            text-decoration: underline;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">

    <?php include 'header.php'; ?>

    <main class="pt-20">
        <div class="container mx-auto px-6 py-16">
            <div class="bg-white p-8 md:p-12 rounded-lg shadow-lg max-w-4xl mx-auto">

                <?php if ($article): ?>
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        <?php echo htmlspecialchars($article['title']); ?>
                    </h1>

                    <p class="text-gray-500 mb-6">
                        Diposting pada: <?php echo date('d F Y', strtotime($article['created_at'])); ?>
                    </p>

                    <?php if ($article['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($article['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($article['title']); ?>"
                            class="w-full h-auto rounded-lg shadow-md mb-8">
                    <?php endif; ?>

                    <div class="article-content text-lg">
                        <?php
                        echo $article['content'];
                        ?>
                    </div>

                <?php else: ?>
                    <h1 class="text-4xl font-bold text-center text-red-500"><?php echo $error; ?></h1>
                    <p class="text-center mt-4">
                        <a href="index.php" class="text-blue-600 hover:underline">Kembali ke Halaman Utama</a>
                    </p>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <?php // include 'footer.php'; ?>

    <script src="../Js/index.js"></script>

</body>

</html>