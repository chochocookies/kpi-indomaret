<?php
// views/admin/users.php
$page = 'admin';
?>
<?php include __DIR__ . '/../layout/flash.php'; ?>

<div class="mb-5 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-extrabold text-blue-900">Kelola User</h1>
        <p class="text-sm text-slate-500"><?= count($users) ?> user aktif</p>
    </div>
    <button onclick="document.getElementById('modal-user').classList.remove('hidden')" class="btn-primary text-xs">+ Tambah User</button>
</div>

<div class="flex gap-2 mb-4">
    <a href="<?= BASE_URL ?>/index.php?page=admin&action=toko" class="btn-secondary text-xs">🏪 Toko</a>
    <a href="<?= BASE_URL ?>/index.php?page=admin&action=users" class="btn-primary text-xs">👤 Users</a>
</div>

<div class="space-y-3">
<?php foreach ($users as $u): ?>
<div class="kpi-card p-4">
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center font-bold text-blue-700 text-sm">
                <?= strtoupper(substr($u['nama_lengkap'], 0, 1)) ?>
            </div>
            <div>
                <div class="font-bold text-blue-900 text-sm"><?= htmlspecialchars($u['nama_lengkap']) ?></div>
                <div class="text-xs text-slate-500">@<?= htmlspecialchars($u['username']) ?></div>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold
                        <?= $u['role'] === 'superadmin' ? 'bg-purple-100 text-purple-700' : ($u['role'] === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') ?>">
                        <?= ucfirst(str_replace('_', ' ', $u['role'])) ?>
                    </span>
                    <?php if ($u['kode_toko']): ?>
                    <span class="text-xs font-mono text-slate-400"><?= $u['kode_toko'] ?> – <?= htmlspecialchars($u['nama_toko'] ?? '') ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)" class="btn-secondary text-xs px-3">Edit</button>
            <?php if ($_SESSION['role'] === 'superadmin' && $u['id'] != $_SESSION['user_id']): ?>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=admin&action=deleteUser" onsubmit="return confirm('Nonaktifkan user ini?')">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button type="submit" class="btn-danger text-xs px-3">Hapus</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Modal Tambah/Edit User -->
<div id="modal-user" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6 max-h-[90vh] overflow-y-auto">
        <h3 id="modal-user-title" class="font-bold text-blue-900 text-lg mb-4">Tambah User</h3>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=admin&action=saveUser">
            <input type="hidden" name="id" id="user-id" value="0">
            <div class="space-y-3">
                <div id="uname-field">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Username</label>
                    <input type="text" name="username" id="user-uname" class="input-field" placeholder="username" autocomplete="off">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="user-nama" class="input-field" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Role</label>
                    <select name="role" id="user-role" class="input-field" onchange="toggleTokoField(this.value)">
                        <option value="kepala_toko">Kepala Toko</option>
                        <option value="admin">Admin</option>
                        <?php if ($_SESSION['role'] === 'superadmin'): ?>
                        <option value="superadmin">Super Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div id="toko-select-field">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Toko</label>
                    <select name="kode_toko" id="user-toko" class="input-field">
                        <option value="">-- Tidak ada (admin/superadmin) --</option>
                        <?php foreach ($semuaToko as $t): ?>
                        <option value="<?= $t['kode_toko'] ?>"><?= htmlspecialchars($t['kode_toko'] . ' – ' . $t['nama_toko']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Password <span id="pass-hint" class="text-slate-400 font-normal">(wajib untuk user baru)</span></label>
                    <input type="password" name="password" id="user-pass" class="input-field" autocomplete="new-password" placeholder="Kosongkan untuk tidak mengubah">
                </div>
            </div>
            <div class="flex gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modal-user').classList.add('hidden')" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(u) {
    document.getElementById('modal-user-title').textContent = 'Edit User';
    document.getElementById('user-id').value = u.id;
    document.getElementById('user-uname').value = u.username;
    document.getElementById('user-nama').value = u.nama_lengkap;
    document.getElementById('user-role').value = u.role;
    document.getElementById('user-toko').value = u.kode_toko || '';
    document.getElementById('user-pass').placeholder = 'Kosongkan untuk tidak mengubah';
    document.getElementById('pass-hint').textContent = '(kosongkan jika tidak diubah)';
    document.getElementById('uname-field').style.display = 'none';
    toggleTokoField(u.role);
    document.getElementById('modal-user').classList.remove('hidden');
}
function toggleTokoField(role) {
    const show = role === 'kepala_toko';
    document.getElementById('toko-select-field').style.display = show ? 'block' : 'none';
}
toggleTokoField('kepala_toko');
</script>
