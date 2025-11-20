<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Admin Panel - Artikel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white p-4 shadow-md fixed w-full z-20 top-0">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <h1 class="text-lg font-bold">Admin Panel</h1>
                <a href="admin_gallery.php" class="underline text-sm">Admin Gallery</a>
            </div>
            <div>
                <a href="index.php" class="underline text-sm">Kembali ke Website</a>
            </div>
        </div>
    </header>

    <main class="pt-28 container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Upload / Create -->
            <section class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-4">Buat / Unggah Artikel</h2>
                <form id="article-form" class="space-y-4" autocomplete="off">
                    <input type="hidden" id="edit-id" value="" />
                    <div>
                        <label class="block mb-1">Judul</label>
                        <input id="title" type="text" class="w-full border rounded px-3 py-2" required />
                    </div>
                    <div>
                        <label class="block mb-1">Gambar (opsional)</label>
                        <input id="image" type="file" accept="image/*" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Isi</label>
                        <textarea id="content" rows="8" class="w-full border rounded px-3 py-2" required></textarea>
                    </div>

                    <p id="msg" class="text-sm"></p>

                    <div>
                        <button id="submit-btn" type="button" class="w-full bg-blue-600 text-white py-2 rounded">Simpan
                            Artikel</button>
                    </div>
                </form>
            </section>

            <!-- Manage list -->
            <section class="bg-white p-6 rounded shadow col-span-1 md:col-span-1">
                <h2 class="text-xl font-bold mb-4">Daftar Artikel Terbit</h2>
                <div id="list-msg" class="text-red-600 mb-3 hidden"></div>
                <div class="overflow-x-auto">
                    <table id="articles-table" class="w-full text-sm">
                        <thead>
                            <tr class="text-left bg-gray-100">
                                <th class="p-2">Judul</th>
                                <th class="p-2">Penulis</th>
                                <th class="p-2">Tanggal</th>
                                <th class="p-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="articles-body"></tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <!-- Edit modal (improved full-screen on small, centered on desktop) -->
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-70 hidden z-50 flex items-center justify-center">
        <div id="edit-modal-panel"
            class="bg-white w-full h-full md:h-auto md:max-w-4xl md:rounded-lg overflow-auto relative mx-4 my-6">
            <div class="p-4 border-b flex items-center justify-between">
                <h3 class="text-lg font-bold">Edit Artikel</h3>
                <button id="close-edit" class="text-gray-600 px-3 py-1 rounded hover:bg-gray-100"
                    aria-label="Tutup">Tutup ✕</button>
            </div>
            <div class="p-6">
                <form id="edit-form" class="space-y-4">
                    <input type="hidden" id="edit-id-2" />
                    <div>
                        <label class="block mb-1 font-medium">Judul</label>
                        <input id="edit-title" class="w-full border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Gambar (optional)</label>
                        <input id="edit-image" type="file" accept="image/*" class="w-full" />
                        <div id="current-image" class="mt-3"></div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Isi</label>
                        <textarea id="edit-content" rows="10" class="w-full border rounded px-3 py-2"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-edit" class="px-4 py-2 border rounded">Batal</button>
                        <button type="button" id="save-edit"
                            class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('auth_token') || '';
            const msg = document.getElementById('msg');
            const listMsg = document.getElementById('list-msg');

            if (!token) {
                msg.className = 'text-red-600';
                msg.textContent = 'Silakan login terlebih dahulu.';
                // optionally redirect: window.location.href = 'index.php';
                return;
            }

            async function fetchArticles() {
                try {
                    const res = await fetch('api/manage_article.php?action=list');
                    const body = await res.json();
                    if (!res.ok) throw body;
                    renderArticles(body.articles || []);
                } catch (e) {
                    listMsg.classList.remove('hidden');
                    listMsg.textContent = (e && e.error) ? e.error : 'Gagal memuat artikel.';
                    console.error(e);
                }
            }

            function renderArticles(items) {
                const tbody = document.getElementById('articles-body');
                tbody.innerHTML = '';
                if (!items.length) {
                    tbody.innerHTML = `<tr><td colspan="4" class="p-3 text-center text-gray-600">Belum ada artikel</td></tr>`;
                    return;
                }
                items.forEach(a => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
        <td class="p-2 align-top">${escapeHtml(a.title)}</td>
        <td class="p-2 align-top">${escapeHtml(a.username || '')}</td>
        <td class="p-2 align-top">${escapeHtml(a.created_at || '')}</td>
        <td class="p-2 align-top">
          <button data-id="${a.id}" class="edit-btn text-sm px-2 py-1 bg-yellow-400 rounded mr-2">Edit</button>
          <button data-id="${a.id}" class="del-btn text-sm px-2 py-1 bg-red-500 text-white rounded">Hapus</button>
        </td>
      `;
                    tbody.appendChild(tr);
                });

                // bind actions
                document.querySelectorAll('.del-btn').forEach(b => b.addEventListener('click', async (ev) => {
                    const id = ev.currentTarget.dataset.id;
                    if (!confirm('Hapus artikel ini?')) return;
                    try {
                        const fd = new FormData();
                        fd.append('token', token);
                        fd.append('id', id);
                        fd.append('action', 'delete');
                        const res = await fetch('api/manage_article.php', { method: 'POST', body: fd });
                        const body = await res.json();
                        if (!res.ok) throw body;
                        fetchArticles();
                    } catch (e) {
                        alert((e && e.error) ? e.error : 'Gagal menghapus.');
                        console.error(e);
                    }
                }));

                document.querySelectorAll('.edit-btn').forEach(b => b.addEventListener('click', async (ev) => {
                    const id = ev.currentTarget.dataset.id;
                    openEdit(id);
                }));
            }

            // create / upload article
            document.getElementById('submit-btn').addEventListener('click', async () => {
                msg.textContent = ''; msg.className = '';
                const title = document.getElementById('title').value.trim();
                const content = document.getElementById('content').value.trim();
                const image = document.getElementById('image').files[0];
                if (!title || !content) { msg.className = 'text-red-600'; msg.textContent = 'Judul dan isi wajib diisi.'; return; }

                const fd = new FormData();
                fd.append('token', token);
                fd.append('title', title);
                fd.append('content', content);
                if (image) fd.append('image', image);

                try {
                    const res = await fetch('api/upload_article.php', { method: 'POST', body: fd });
                    const body = await res.json();
                    if (!res.ok) throw body;
                    msg.className = 'text-green-600';
                    msg.textContent = body.message || 'Berhasil';
                    document.getElementById('article-form').reset();
                    fetchArticles();
                } catch (e) {
                    msg.className = 'text-red-600';
                    msg.textContent = (e && e.error) ? e.error : 'Gagal menyimpan.';
                    console.error(e);
                }
            });

            // Edit modal logic
            const editModal = document.getElementById('edit-modal');
            document.getElementById('close-edit').addEventListener('click', () => { editModal.classList.add('hidden'); });

            async function openEdit(id) {
                try {
                    const res = await fetch(`api/manage_article.php?action=get&id=${encodeURIComponent(id)}`);
                    const body = await res.json();
                    if (!res.ok) throw body;
                    const a = body.article;
                    document.getElementById('edit-id-2').value = a.id;
                    document.getElementById('edit-title').value = a.title;
                    document.getElementById('edit-content').value = a.content;
                    document.getElementById('current-image').innerHTML = a.image_url ? `<img src="${encodeURI(a.image_url)}" class="max-h-32 rounded">` : '';
                    const editModal = document.getElementById('edit-modal');
                    editModal.classList.remove('hidden');
                    // lock background scroll while modal open
                    document.body.style.overflow = 'hidden';
                } catch (e) {
                    alert((e && e.error) ? e.error : 'Gagal mengambil data artikel.');
                    console.error(e);
                }
            }

            function hideEditModal() {
                const editModal = document.getElementById('edit-modal');
                editModal.classList.add('hidden');
                // restore scrolling
                document.body.style.overflow = '';
                // clear file input to allow same-file re-upload
                const fi = document.getElementById('edit-image');
                if (fi) fi.value = '';
            }

            // attach modal close handlers
            document.getElementById('close-edit').addEventListener('click', hideEditModal);
            document.getElementById('cancel-edit').addEventListener('click', hideEditModal);
            // close when clicking on backdrop (outside panel)
            document.getElementById('edit-modal').addEventListener('click', function (e) {
                if (e.target && e.target.id === 'edit-modal') hideEditModal();
            });

            document.getElementById('save-edit').addEventListener('click', async () => {
                const id = document.getElementById('edit-id-2').value;
                const title = document.getElementById('edit-title').value.trim();
                const content = document.getElementById('edit-content').value.trim();
                const image = document.getElementById('edit-image').files[0];
                if (!title || !content) { alert('Judul & isi wajib.'); return; }

                const fd = new FormData();
                fd.append('token', token);
                fd.append('action', 'update');
                fd.append('id', id);
                fd.append('title', title);
                fd.append('content', content);
                if (image) fd.append('image', image);

                try {
                    const res = await fetch('api/manage_article.php', { method: 'POST', body: fd });
                    const body = await res.json();
                    if (!res.ok) throw body;
                    editModal.classList.add('hidden');
                    fetchArticles();
                } catch (e) {
                    alert((e && e.error) ? e.error : 'Gagal menyimpan perubahan.');
                    console.error(e);
                }
            });

            // helpers
            function escapeHtml(s) { return String(s || '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m]); }

            // initial load
            fetchArticles();
        });
    </script>
</body>

</html>