<?php
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Potensi Desa Tolok</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="pt-28 container mx-auto px-6 pb-16">
        <header class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold">Potensi Desa Tolok</h1>
            <p class="text-gray-600 mt-3 max-w-2xl mx-auto">Berbagai potensi unggulan yang dimiliki Desa Tolok, mulai
                dari alam yang indah, UMKM kreatif, hingga hasil bumi yang melimpah.</p>
        </header>

        <!-- Potensi Wisata -->
        <section class="max-w-5xl mx-auto mb-12">
            <h2 class="text-2xl font-bold text-center mb-6">Potensi Wisata</h2>
            <div class="grid gap-6 md:grid-cols-2">
                <article class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="uploads/waruga.jpg" alt="Waruga Mawale" class="w-full h-56 object-cover">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Waruga Mawale</h3>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Waruga Mawale merupakan peninggalan bersejarah yang menjadi bukti peradaban masyarakat
                            Minahasa zaman dahulu. Lokasinya berada di dekat area persawahan Desa Tolok dan dapat
                            dikunjungi sebagai objek wisata budaya yang unik dan edukatif.
                        </p>
                    </div>
                </article>

                <article class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="uploads/sawah.jpg" alt="Pemandangan Sawah" class="w-full h-56 object-cover">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Pemandangan Sawah</h3>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Hamparan sawah hijau di Desa Tolok menyajikan pemandangan alami yang menenangkan. Tempat ini
                            cocok untuk bersantai, berfoto, atau sekadar menikmati suasana pedesaan dengan latar
                            pegunungan yang indah.
                        </p>
                    </div>
                </article>
            </div>
        </section>

        <!-- Potensi UMKM -->
        <section class="max-w-5xl mx-auto">
            <h2 class="text-2xl font-bold text-center mb-6">Potensi UMKM & Hasil Bumi</h2>
            <div class="grid gap-6 md:grid-cols-2">
                <article class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="uploads/umkm.jpg" alt="UMKM Lokal" class="w-full h-56 object-cover">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">UMKM Lokal</h3>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Pelaku UMKM di Desa Tolok menghasilkan kerajinan dan olahan lokal yang potensial untuk
                            dikembangkan pasarannya. Dukungan promosi dan pelatihan dapat meningkatkan nilai tambah
                            produk desa.
                        </p>
                    </div>
                </article>

                <article class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="uploads/hasil_bumi.jpg" alt="Hasil Bumi" class="w-full h-56 object-cover">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Hasil Bumi</h3>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Produk pertanian dan perkebunan setempat seperti padi dan tanaman hortikultura merupakan
                            sumber penghidupan utama. Optimalisasi produksi dan pemasaran membuka peluang ekonomi lebih
                            besar.
                        </p>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <script src="../Js/index.js"></script>
</body>

</html>