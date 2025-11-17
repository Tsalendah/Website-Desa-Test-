<?php
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin - Hukum Tua</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100">
  <?php include __DIR__ . '/header.php'; ?>
  <main class="pt-28 container mx-auto px-6">
    <div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
      <h1 class="text-xl font-bold mb-4">Kelola Daftar Hukum Tua</h1>

      <form id="form" class="space-y-2 mb-4">
        <input type="hidden" id="id" />
        <div><label class="block text-sm">Periode</label><input id="periode" class="w-full border p-2 rounded" /></div>
        <div><label class="block text-sm">Nama</label><input id="nama" class="w-full border p-2 rounded" /></div>
        <div><label class="block text-sm">Keterangan</label><input id="keterangan" class="w-full border p-2 rounded" /></div>
        <div class="flex space-x-2">
          <button type="button" id="save" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
          <button type="button" id="clear" class="border px-4 py-2 rounded">Reset</button>
        </div>
      </form>

      <div id="msg" class="text-sm mb-3"></div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead><tr class="bg-gray-100"><th class="p-2">Periode</th><th class="p-2">Nama</th><th class="p-2">Keterangan</th><th class="p-2">Aksi</th></tr></thead>
          <tbody id="list"></tbody>
        </table>
      </div>
    </div>
  </main>

<script>
const token = localStorage.getItem('auth_token') || '';
if (!token) { alert('Login sebagai admin terlebih dahulu'); location.href='index.php'; }

async function load(){
  const res = await fetch('api/manage_hukum.php?action=list');
  const j = await res.json();
  const list = document.getElementById('list');
  list.innerHTML = '';
  (j.items||[]).forEach(i=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td class="p-2 border-b">${escapeHtml(i.periode)}</td>
                    <td class="p-2 border-b">${escapeHtml(i.nama)}</td>
                    <td class="p-2 border-b">${escapeHtml(i.keterangan||'')}</td>
                    <td class="p-2 border-b">
                      <button data-id="${i.id}" class="edit bg-yellow-300 px-2 py-1 rounded mr-2">Edit</button>
                      <button data-id="${i.id}" class="del bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </td>`;
    list.appendChild(tr);
  });
  bindActions();
}

function bindActions(){
  document.querySelectorAll('.edit').forEach(b=>{
    b.onclick = async ()=> {
      const id = b.dataset.id;
      const res = await fetch(`api/manage_hukum.php?action=list`);
      const j = await res.json();
      const item = j.items.find(x=>x.id==id);
      if (!item) return;
      document.getElementById('id').value = item.id;
      document.getElementById('periode').value = item.periode;
      document.getElementById('nama').value = item.nama;
      document.getElementById('keterangan').value = item.keterangan || '';
      window.scrollTo({top:0,behavior:'smooth'});
    };
  });
  document.querySelectorAll('.del').forEach(b=>{
    b.onclick = async ()=> {
      if (!confirm('Hapus item?')) return;
      const fd = new FormData();
      fd.append('token', token);
      fd.append('action','delete');
      fd.append('id', b.dataset.id);
      const res = await fetch('api/manage_hukum.php',{method:'POST',body:fd});
      const j = await res.json();
      if (!res.ok) alert(j.error||'Gagal');
      else load();
    };
  });
}

document.getElementById('save').addEventListener('click', async ()=>{
  const id = document.getElementById('id').value;
  const periode = document.getElementById('periode').value.trim();
  const nama = document.getElementById('nama').value.trim();
  const keterangan = document.getElementById('keterangan').value.trim();
  if (!periode || !nama) { alert('Periode & Nama wajib'); return; }
  const fd = new FormData();
  fd.append('token', token);
  fd.append('periode', periode);
  fd.append('nama', nama);
  fd.append('keterangan', keterangan);
  if (id) { fd.append('action','update'); fd.append('id', id); }
  else fd.append('action','create');

  const res = await fetch('api/manage_hukum.php',{ method:'POST', body: fd });
  const j = await res.json();
  if (!res.ok) alert(j.error||'Gagal');
  else {
    document.getElementById('form').reset();
    load();
  }
});

document.getElementById('clear').addEventListener('click', ()=> document.getElementById('form').reset());

function escapeHtml(s){ return (s||'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

load();
</script>
</body>
</html>