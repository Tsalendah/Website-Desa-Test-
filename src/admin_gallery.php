<?php
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Admin - Gallery</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 text-gray-800 min-h-screen">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="pt-28 container mx-auto px-4 pb-16">
        <div class="max-w-4xl mx-auto bg-white rounded shadow p-6">
            <h1 class="text-xl font-bold mb-4">Kelola Galeri</h1>

            <form id="upload-form" class="mb-4">
                <label class="block mb-2">Pilih gambar (bisa multiple)</label>
                <input id="images" type="file" accept="image/*" multiple class="mb-3" />
                <div class="flex gap-2">
                    <button id="upload-btn" type="button"
                        class="bg-blue-600 text-white px-4 py-2 rounded">Unggah</button>
                    <button id="refresh-btn" type="button" class="border px-4 py-2 rounded">Refresh</button>
                </div>
                <p id="msg" class="text-sm mt-2"></p>
            </form>

            <div id="grid" class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3"></div>
        </div>
    </main>

    <script>
        const token = localStorage.getItem('auth_token') || '';
        if (!token) { alert('Login sebagai admin terlebih dahulu'); location.href = 'index.php'; }

        async function loadList() {
            const res = await fetch('api/upload_gallery.php?action=list');
            const j = await res.json();
            const grid = document.getElementById('grid');
            grid.innerHTML = '';
            (j.items || []).forEach(it => {
                const card = document.createElement('div');
                card.className = 'bg-white rounded shadow overflow-hidden';
                card.innerHTML = `
      <div class="h-44 overflow-hidden"><img src="${it.src}" alt="${it.name}" class="w-full h-full object-cover"></div>
      <div class="p-3 text-sm">
        <div class="mb-2 truncate">${it.name}</div>
        <div class="flex justify-between">
          <a href="${it.src}" target="_blank" class="text-blue-600 underline text-xs">Lihat</a>
          <button data-src="${encodeURIComponent(it.src)}" class="del-btn text-xs bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
        </div>
      </div>`;
                grid.appendChild(card);
            });
            document.querySelectorAll('.del-btn').forEach(b => {
                b.addEventListener('click', async (ev) => {
                    if (!confirm('Hapus gambar?')) return;
                    const src = decodeURIComponent(ev.currentTarget.dataset.src);
                    const fd = new FormData();
                    fd.append('token', token);
                    fd.append('action', 'delete');
                    fd.append('src', src);
                    const res = await fetch('api/upload_gallery.php', { method: 'POST', body: fd });
                    const body = await res.json();
                    if (!res.ok) alert(body.error || 'Gagal hapus');
                    else loadList();
                });
            });
        }

        document.getElementById('refresh-btn').addEventListener('click', loadList);

        document.getElementById('upload-btn').addEventListener('click', async () => {
            const inp = document.getElementById('images');
            const files = inp.files;
            if (!files.length) { document.getElementById('msg').textContent = 'Pilih file terlebih dahulu'; return; }
            const fd = new FormData();
            fd.append('token', token);
            fd.append('action', 'upload');
            for (let i = 0; i < files.length; i++) fd.append('images[]', files[i]);
            document.getElementById('msg').textContent = 'Mengunggah...';
            const res = await fetch('api/upload_gallery.php', { method: 'POST', body: fd });
            const body = await res.json();
            if (!res.ok) {
                document.getElementById('msg').textContent = body.error || 'Gagal upload';
            } else {
                document.getElementById('msg').textContent = body.message || 'Sukses';
                inp.value = '';
                loadList();
            }
        });

        loadList();
    </script>
    <script src="../Js/index.js"></script>
</body>

</html>