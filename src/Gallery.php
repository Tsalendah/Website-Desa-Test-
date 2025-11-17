<?php
// kumpulkan gambar dari beberapa folder yang mungkin dipakai
$dirs = [
    __DIR__ . '/uploads',
    __DIR__ . '/Gallery',
    __DIR__ . '/api/uploads',
];

$images = [];
foreach ($dirs as $d) {
    if (!is_dir($d))
        continue;
    foreach (glob($d . '/*.{jpg,jpeg,png,gif,webp,PNG,JPG,JPEG,GIF,WEBP}', GLOB_BRACE) as $f) {
        $images[] = $f;
    }
}

// urutkan nama file agar deterministic
sort($images);

// helper buat path web (relatif ke document root)
function webPath($file)
{
    $doc = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
    $f = str_replace('\\', '/', realpath($file));
    return str_replace($doc, '', $f);
}
?><!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Galeri Desa Tolok</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="pt-28 container mx-auto px-6 pb-16">
        <header class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold">Galeri Desa Tolok</h1>
            <p class="text-gray-600 mt-3 max-w-2xl mx-auto">Momen dan pemandangan yang terekam di Desa Tolok. Klik
                gambar untuk melihat lebih besar.</p>
        </header>

        <?php if (empty($images)): ?>
            <p class="text-center text-gray-500">Belum ada gambar di galeri.</p>
        <?php else: ?>
            <div class="max-w-6xl mx-auto">
                <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                    <?php foreach ($images as $img):
                        $src = webPath($img);
                        $fname = basename($img);
                        ?>
                        <figure class="bg-white rounded-lg shadow overflow-hidden">
                            <button class="w-full h-56 block focus:outline-none open-lightbox"
                                data-src="<?php echo htmlspecialchars($src); ?>"
                                aria-label="<?php echo htmlspecialchars($fname); ?>">
                                <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($fname); ?>"
                                    class="w-full h-56 object-cover transition-transform transform hover:scale-105">
                            </button>
                            <figcaption class="p-4 text-sm text-gray-700">
                                <?php echo htmlspecialchars(pathinfo($fname, PATHINFO_FILENAME)); ?>
                            </figcaption>
                        </figure>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Lightbox modal -->
    <div id="lightbox" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl w-full">
            <button id="lb-close"
                class="absolute top-2 right-2 bg-white bg-opacity-90 rounded-full p-2 focus:outline-none">×</button>
            <img id="lb-img" src="" alt="" class="w-full max-h-[85vh] object-contain rounded">
            <div id="lb-caption" class="mt-2 text-center text-sm text-gray-200"></div>
        </div>
    </div>

    <script>
        // lightbox simple
        document.querySelectorAll('.open-lightbox').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const src = btn.dataset.src;
                const alt = btn.getAttribute('aria-label') || '';
                const lb = document.getElementById('lightbox');
                document.getElementById('lb-img').src = src;
                document.getElementById('lb-caption').textContent = alt;
                lb.classList.remove('hidden');
            });
        });

        document.getElementById('lb-close').addEventListener('click', () => {
            const lb = document.getElementById('lightbox');
            lb.classList.add('hidden');
            document.getElementById('lb-img').src = '';
        });

        // klik di luar gambar tutup
        document.getElementById('lightbox').addEventListener('click', (e) => {
            if (e.target.id === 'lightbox') {
                document.getElementById('lb-close').click();
            }
        });

        // ESC untuk tutup
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const lb = document.getElementById('lightbox');
                if (!lb.classList.contains('hidden')) document.getElementById('lb-close').click();
            }
        });
    </script>

    <!-- pastikan script header/login juga dimuat -->
    <script src="../Js/index.js"></script>
</body>

</html>