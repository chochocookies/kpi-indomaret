<?php
// views/otp/manage.php
$page = 'admin';
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="mb-5">
    <h1 class="text-xl font-extrabold text-blue-900">Kelola Kode OTP Toko</h1>
    <p class="text-sm text-slate-500">Kode OTP digunakan untuk memverifikasi setiap aksi input/update data</p>
</div>

<?php if (isset($flash) && $flash): ?>
<div id="flash-msg" class="fixed top-4 right-4 z-50 px-5 py-3 rounded-xl shadow-lg text-sm font-semibold
    <?= $flash['type']==='success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
    <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif; ?>

<div class="info-box mb-5">
    <strong>📌 Panduan OTP:</strong> Setiap kepala toko harus memasukkan kode OTP 6 digit sebelum bisa input/update data.
    OTP berlaku 30 menit. Ganti kode OTP secara berkala untuk keamanan.
</div>

<div class="kpi-card overflow-hidden">
    <div class="px-4 py-3 bg-blue-700 text-white font-bold text-sm">Daftar OTP Per Toko</div>
    <div class="divide-y divide-blue-50">
    <?php foreach ($otpList as $o): ?>
    <div class="flex items-center justify-between px-4 py-3">
        <div>
            <div class="font-bold text-blue-900 text-sm"><?= htmlspecialchars($o['kode_toko']) ?></div>
            <?php if ($o['otp_updated_at']): ?>
            <div class="text-xs text-slate-400">Diupdate: <?= date('d/m/Y H:i', strtotime($o['otp_updated_at'])) ?></div>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-3">
            <div class="font-mono font-bold text-blue-700 text-lg tracking-widest bg-blue-50 px-3 py-1 rounded-xl">
                <?= $o['kode_otp'] ?? '------' ?>
            </div>
            <button onclick="openEdit('<?= $o['kode_toko'] ?>','<?= $o['kode_otp'] ?>')" class="btn-secondary text-xs px-3">Ubah</button>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
</div>

<!-- Modal edit OTP -->
<div id="modal-otp" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6">
        <h3 class="font-bold text-blue-900 mb-4">Ubah OTP Toko <span id="otp-kode-label"></span></h3>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=otp&action=update">
            <input type="hidden" name="kode_toko" id="otp-kode-input">
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kode OTP Baru (6 digit)</label>
                <input type="text" name="new_otp" id="otp-new" maxlength="6" pattern="[0-9]{6}"
                       class="input-field text-center text-2xl font-mono tracking-widest" placeholder="000000" inputmode="numeric">
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('modal-otp').classList.add('hidden')" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1">Simpan</button>
            </div>
        </form>
    </div>
</div>
<script>
function openEdit(kode, current) {
    document.getElementById('otp-kode-label').textContent = kode;
    document.getElementById('otp-kode-input').value = kode;
    document.getElementById('otp-new').value = current || '';
    document.getElementById('modal-otp').classList.remove('hidden');
    setTimeout(function(){ document.getElementById('otp-new').focus(); }, 100);
}
document.getElementById('otp-new')?.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g,'').slice(0,6);
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
