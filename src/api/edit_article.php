<?php
require 'api/db.php';
$article = null;
$error = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = $_GET['id'];
    try {
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artikel - Admin Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body class="bg-gray-100">

    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Edit Artikel</h1>
            <a href="admin.php" class="hover:underline">Kembali ke Admin Panel</a>
        </div>
    </header>

    <main id="admin-content" class="container mx-auto p-8 mt-10">
        <?php if ($article): ?>
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-8">Edit Artikel:
                    <?php echo htmlspecialchars($article['title']); ?></h2>

                <form id="edit-form">
                    <input type="hidden" id="article_id" value="<?php echo $article['id']; ?>">

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Artikel</label>
                        <input type="text" id="title" name="title" required
                            value="<?php echo htmlspecialchars($article['title']); ?>"
                            class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Ganti Gambar
                            (Opsional)</label>
                        <?php if ($article['image_url']): ?>
                            <p class="text-sm text-gray-500 mb-2">Gambar saat ini:
                                <a href="<?php echo htmlspecialchars($article['image_url']); ?>" target="_blank"
                                    class="text-blue-500 hover:underline">Lihat Gambar</a>
                            </p>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/webp"
                            class="w-full p-3 border border-gray-300 rounded-md file:mr-4 file:py-2 file:px-4 ...">
                    </div>

                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Isi Artikel</label>
                        <textarea id="content" name="content"
                            rows="10"><?php echo htmlspecialchars($article['content']); ?></textarea>
                    </div>

                    <p id="form-message" class="text-center mb-4"></p>
                    <button type="submit" id="submit-btn"
                        class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-md hover:bg-blue-700 transition duration-300">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-red-500"><?php echo $error; ?></h2>
                <a href="admin.php" class="text-blue-600 hover:underline mt-4 inline-block">Kembali ke Admin Panel</a>
            </div>
        <?php endif; ?>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const token = localStorage.getItem("auth_token");
            const role = localStorage.getItem("userRole");

            // Cek admin
            if (!role || role !== 'admin' || !token) {
                document.body.innerHTML = '<p class="text-xl text-red-500 text-center p-20">Akses Ditolak.</p>';
                setTimeout(() => window.location.href = 'index.php', 2000);
                return;
            }

            // Inisialisasi TinyMCE
            tinymce.init({
                selector: 'textarea#content',
                plugins: 'lists link image code preview',
                toolbar: 'undo redo | blocks | bold italic | bullist numlist | link image | code | preview',
                height: 500
            });

            // --- Form Submit (Update Artikel) ---
            const editForm = document.getElementById("edit-form");
            const formMessage = document.getElementById("form-message");
            const submitBtn = document.getElementById("submit-btn");

            editForm.addEventListener("submit", async (e) => {
                e.preventDefault();
                formMessage.textContent = "";
                submitBtn.disabled = true;
                submitBtn.textContent = "Menyimpan...";

                // Ambil konten dari TinyMCE
                const content = tinymce.get('content').getContent();
                const title = document.getElementById("title").value;
                const imageFile = document.getElementById("image").files[0];
                const articleId = document.getElementById("article_id").value;

                const formData = new FormData();
                formData.append("token", token);
                formData.append("id", articleId); // Kirim ID artikel
                formData.append("title", title);
                formData.append("content", content);
                if (imageFile) {
                    formData.append("image", imageFile);
                }

                try {
                    // Panggil API manage_articles.php dengan method POST
                    const res = await fetch("api/manage_articles.php", {
                        method: "POST", // POST akan ditangani sebagai UPDATE oleh API
                        body: formData
                    });
                    const data = await res.json();

                    if (res.ok) {
                        formMessage.textContent = "Artikel berhasil diperbarui!";
                        formMessage.className = "text-center mb-4 text-green-500";
                    } else {
                        formMessage.textContent = data.error || "Gagal memperbarui.";
                        formMessage.className = "text-center mb-4 text-red-500";
                    }
                } catch (err) {
                    formMessage.textContent = "Error: Tidak dapat terhubung ke server.";
                    formMessage.className = "text-center mb-4 text-red-500";
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = "Simpan Perubahan";
                }
            });
        });
    </script>
</body>

</html>