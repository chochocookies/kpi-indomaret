<?php
// views/admin/toko.php
$page = 'admin';
?>
<?php include __DIR__ . '/../layout/flash.php'; ?>

<div class="mb-5 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-extrabold text-blue-900">Kelola Toko</h1>
        <p class="text-sm text-slate-500"><?= count($semuaToko) ?> toko aktif</p>
    </div>
    <button onclick="document.getElementById('modal-toko').classList.remove('hidden')" class="btn-primary text-xs">+ Tambah Toko</button>
</div>

<!-- Tabs Admin -->
<div class="flex gap-2 mb-4">
    <a href="<?= BASE_URL ?>/index.php?page=admin&action=toko" class="btn-primary text-xs">🏪 Toko</a>
    <a href="<?= BASE_URL ?>/index.php?page=admin&action=users" class="btn-secondary text-xs">👤 Users</a>
</div>

<div class="space-y-3">
<?php foreach ($semuaToko as $t): ?>
<div class="kpi-card p-4">
    <div class="flex items-start justify-between">
        <div>
            <div class="font-bold text-blue-900"><?= htmlspecialchars($t['nama_toko']) ?></div>
            <div class="text-xs font-mono text-slate-500 mt-0.5"><?= $t['kode_toko'] ?></div>
            <?php if ($t['alamat']): ?>
            <div class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($t['alamat']) ?></div>
            <?php endif; ?>
            <div class="text-xs text-slate-500 mt-1">👥 <?= $t['jumlah_karyawan'] ?> karyawan</div>
        </div>
        <div class="flex gap-2">
            <button onclick="editToko(<?= htmlspecialchars(json_encode($t)) ?>)" class="btn-secondary text-xs px-3">Edit</button>
            <?php if ($_SESSION['role'] === 'superadmin'): ?>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=admin&action=deleteToko" onsubmit="return confirm('Nonaktifkan toko ini?')">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <button type="submit" class="btn-danger text-xs px-3">Nonaktifkan</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Modal Tambah/Edit Toko -->
<div id="modal-toko" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6">
        <h3 id="modal-toko-title" class="font-bold text-blue-900 text-lg mb-4">Tambah Toko</h3>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=admin&action=saveToko">
            <input type="hidden" name="id" id="toko-id" value="0">
            <div class="space-y-3">
                <div id="kode-field">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kode Toko</label>
                    <input type="text" name="kode_toko" id="toko-kode" class="input-field" placeholder="Misal: FDNP" maxlength="10">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Toko</label>
                    <input type="text" name="nama_toko" id="toko-nama" class="input-field" required placeholder="Nama toko">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat</label>
                    <input type="text" name="alamat" id="toko-alamat" class="input-field" placeholder="Opsional">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Karyawan</label>
                    <input type="number" name="jumlah_karyawan" id="toko-karyawan" value="5" class="input-field" min="1">
                </div>
            </div>
            <div class="flex gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modal-toko').classList.add('hidden')" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editToko(t) {
    document.getElementById('modal-toko-title').textContent = 'Edit Toko';
    document.getElementById('toko-id').value = t.id;
    document.getElementById('toko-kode').value = t.kode_toko;
    document.getElementById('toko-nama').value = t.nama_toko;
    document.getElementById('toko-alamat').value = t.alamat || '';
    document.getElementById('toko-karyawan').value = t.jumlah_karyawan;
    document.getElementById('kode-field').style.display = 'none';
    document.getElementById('modal-toko').classList.remove('hidden');
}
</script>
