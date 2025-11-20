<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DEsa - Profile desa</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <style>
    html {
      scroll-behavior: smooth;
    }

    .section {
      scroll-margin-top: 80px;
    }

    body {
      margin: 0;
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">
  <?php include __DIR__ . '/header.php'; ?>
  <main class="pt-20">
    <section id="home" class="section text-white text-center relative min-h-[60vh] md:min-h-[80vh] py-20 md:py-32"
      style="
          background-image: url('./Gallery/image2.jpg');
          background-size: cover;
          background-position: center;
        ">
      <!-- overlay -->
      <div class="absolute inset-0 bg-black/40"></div>

      <div class="container mx-auto px-6 relative z-10">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">
          Inovasi Digital Sulawesi
        </h1>
        <p class="text-lg md:text-2xl">
          Membawa Transformasi Digital untuk Masa Depan
        </p>
      </div>
    </section>
    <section id="about" class="section container mx-auto px-6 py-16">
      <h2 class="text-3xl font-bold text-center mb-8">Tentang Kami</h2>
      <div class="flex flex-col md:flex-row items-center gap-8">
        <div class="md:w-1/2">
          <h3 class="text-2xl font-semibold mb-4">Sejarah Singkat</h3>
          <p class="mb-4">
            Didirikan pada tahun 2025, Inovasi Digital Sulawesi lahir dari
            visi untuk membantu usaha kecil dan menengah di Indonesia
            memanfaatkan kekuatan teknologi digital. Kami memulai sebagai tim
            kecil yang bersemangat tentang teknologi dan desain, dan telah
            berkembang menjadi perusahaan solusi digital lengkap yang melayani
            klien di berbagai industri.
          </p>
        </div>
        <div class="md:w-1/2 flex justify-center">
          <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=2070&auto=format&fit=crop"
            alt="Our Team" class="rounded-lg shadow-lg max-w-[90%] h-auto" />
        </div>
      </div>
    </section>
    <section id="vision-mission" class="section bg-gray-200 py-16">
      <div class="container mx-auto px-6 text-center">
        <div class="grid md:grid-cols-2 gap-12">
          <div>
            <h3 class="text-2xl font-bold mb-4">Visi</h3>
            <p>
              Menjadi mitra transformasi digital terdepan di Indonesia,
              memberdayakan bisnis untuk berkembang dan bersaing di panggung
              global melalui inovasi teknologi.
            </p>
          </div>
          <div>
            <h3 class="text-2xl font-bold mb-4">Misi</h3>
            <ul class="list-disc list-inside text-left mx-auto max-w-md">
              <li>
                Memberikan solusi digital yang inovatif, berkualitas, dan
                terukur.
              </li>
              <li>
                Membangun hubungan kemitraan jangka panjang dengan klien
                berdasarkan kepercayaan.
              </li>
              <li>
                Menciptakan lingkungan kerja yang positif dan mendorong
                pertumbuhan profesional.
              </li>
              <li>
                Berkontribusi pada kemajuan ekosistem digital di Indonesia.
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <section id="gallery" class="section bg-gray-200 py-16">
      <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12">Galeri Kegiatan</h2>
        <?php
        // Tampilkan 3 file gambar terbaru dari folder src/Gallery
        $gdir = __DIR__ . '/Gallery';
        $imgs = [];
        if (is_dir($gdir)) {
          foreach (scandir($gdir) as $f) {
            if ($f === '.' || $f === '..')
              continue;
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
              $full = $gdir . DIRECTORY_SEPARATOR . $f;
              $imgs[] = ['file' => $f, 'mtime' => filemtime($full)];
            }
          }
        }

        if (empty($imgs)) {
          echo '<p class="text-center text-gray-500">Belum ada gambar di galeri.</p>';
        } else {
          // Urutkan berdasarkan waktu modifikasi (terbaru dulu)
          usort($imgs, function ($a, $b) {
            return $b['mtime'] <=> $a['mtime'];
          });
          $latest = array_slice($imgs, 0, 3);
          echo '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
          foreach ($latest as $img) {
            $f = $img['file'];
            $url = 'Gallery/' . rawurlencode($f);
            echo '<div class="rounded-lg overflow-hidden shadow-md bg-white">';
            echo '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($f) . '" class="w-full h-64 md:h-48 object-cover">';
            echo '</div>';
          }
          echo '</div>';
        }
        ?>
      </div>
    </section>
    <section id="clients" class="section container mx-auto px-6 py-16">
      <section id="contact" class="section bg-blue-600 text-white py-16">
        <div class="bg-image-2 section" style="padding-top: 0; padding-bottom: 0">
          <div class="container mx-auto px-6 py-16 text-white">
            <h2 class="text-3xl font-bold text-center mb-12">Hubungi Kami</h2>
            <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-8">
              <div>
                <h4 class="text-xl font-semibold mb-4">Informasi Kontak</h4>
                <p class="mb-2">
                  <strong>Alamat:</strong><br />Jl. Gotong Royong No. 321,
                  Manado, Indonesia
                </p>
                <p class="mb-2"><strong>Telepon:</strong> 0815-9966-662</p>
                <p class="mb-2"><strong>Email:</strong> halo@Guys</p>
              </div>
              <form>
                <div class="mb-4">
                  <label for="name" class="block mb-2">Nama</label>
                  <input type="text" id="name" class="w-full p-2 rounded bg-blue-500 text-white placeholder-gray-300" />
                </div>
                <div class="mb-4">
                  <label for="email" class="block mb-2">Email</label>
                  <input type="email" id="email"
                    class="w-full p-2 rounded bg-blue-500 text-white placeholder-gray-300" />
                </div>
                <div class="mb-4">
                  <label for="message" class="block mb-2">Pesan</label>
                  <textarea id="message" rows="4"
                    class="w-full p-2 rounded bg-blue-500 text-white placeholder-gray-300"></textarea>
                </div>
                <button type="submit"
                  class="w-full bg-white text-blue-600 font-bold py-2 px-4 rounded hover:bg-gray-200">
                  Kirim Pesan
                </button>
              </form>
            </div>
          </div>
        </div>
      </section>
  </main>
  <footer class="bg-gray-800 text-white py-6">
    <div class="container mx-auto px-6 text-center">
      <p>&copy; 2025 Inovasi Digital Nusantara. All Rights Reserved.</p>
    </div>
  </footer>
  <div id="login-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-sm">
      <h2 id="form-title" class="text-2xl font-bold mb-6 text-center">
        Login
      </h2>
      <form id="login-form">
        <div class="mb-4">
          <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
          <input type="text" id="username"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            required />
        </div>
        <div class="mb-6">
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input type="password" id="password"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            required />
        </div>
        <p id="error-message" class="text-red-500 text-sm mb-4 hidden"></p>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
          Login
        </button>
      </form>
      <button onclick="hideLoginModal()"
        class="w-full bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 mt-2">
        Cancel
      </button>
    </div>
  </div>

  <!-- Register -->
  <div id="register-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-sm">
      <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
      <form id="register-form">
        <div class="mb-4">
          <label for="reg-username" class="block text-sm font-medium text-gray-700">Username</label>
          <input type="text" id="reg-username" required class="mt-1 block w-full px-3 py-2 border rounded-md" />
        </div>
        <div class="mb-4">
          <label for="reg-email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" id="reg-email" required class="mt-1 block w-full px-3 py-2 border rounded-md" />
        </div>
        <div class="mb-6">
          <label for="reg-password" class="block text-sm font-medium text-gray-700">Password</label>
          <input type="password" id="reg-password" required class="mt-1 block w-full px-3 py-2 border rounded-md" />
        </div>
        <p id="register-error" class="text-red-500 text-sm mb-4 hidden"></p>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
          Create account
        </button>
      </form>
      <button onclick="hideRegisterModal()"
        class="w-full bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 mt-2">
        Cancel
      </button>
    </div>
  </div>
</body>
<script>
  function adjustMainOffset() {
    const header = document.querySelector("header");
    const main = document.querySelector("main");
    if (!header || !main) return;
    main.style.paddingTop = header.offsetHeight + "px";
  }
  window.addEventListener("load", adjustMainOffset);
  window.addEventListener("resize", adjustMainOffset);
</script>

<script src="../Js/index.js"></script>

</html>