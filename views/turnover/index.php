<?php
// views/turnover/index.php
$page     = 'turnover';
$bln      = getNamaBulan($bulan);
$toData   = $kpiData['turnover'];
$namaToko = $toko['nama_toko'] ?? '';
?>

<?php include __DIR__ . '/../layout/flash.php'; ?>

<?php include __DIR__ . '/../layout/period_selector.php'; ?>

<div class="flex items-center gap-3 mb-5">
    <a href="<?= BASE_URL ?>/index.php?page=dashboard&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="text-blue-500 hover:text-blue-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="text-xl font-extrabold text-blue-900">Poin 5 – Turn Over Karyawan</h1>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($namaToko) ?> &middot; <?= $bln ?> <?= $tahun ?></p>
    </div>
</div>

<!-- Panduan -->
<div class="info-box mb-4">
    <div class="font-bold mb-1.5 text-blue-800">📖 Panduan Pengisian – Turn Over Karyawan</div>
    <ul class="space-y-1 list-disc list-inside text-blue-700" style="font-size:.78rem">
        <li>Poin <strong>otomatis penuh (4 poin)</strong> jika dalam satu bulan <strong>tidak ada karyawan yang resign atau keluar</strong></li>
        <li>Jika ada karyawan yang keluar/resign dalam bulan berjalan → poin = <strong>0</strong></li>
        <li>Masukkan jumlah karyawan aktif dan jumlah yang keluar/resign di bulan ini</li>
        <li>Gunakan kolom Catatan untuk mencatat nama karyawan yang keluar dan alasannya</li>
    </ul>
</div>

<!-- Status Card -->
<div class="kpi-card p-6 mb-5 text-center">
    <div class="text-5xl mb-3"><?= $toData['jumlah_keluar'] == 0 ? '🎉' : '😔' ?></div>
    <div class="font-extrabold text-xl <?= $toData['jumlah_keluar'] == 0 ? 'text-green-600' : 'text-red-600' ?>">
        <?= $toData['jumlah_keluar'] == 0 ? 'Tidak Ada Karyawan Keluar' : $toData['jumlah_keluar'] . ' Karyawan Keluar' ?>
    </div>
    <div class="text-slate-500 text-sm mt-1">Jumlah karyawan aktif: <strong><?= $toData['jumlah_karyawan'] ?></strong> orang</div>
    <div class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm
        <?= $toData['poin'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
        Poin Turn Over: <?= $toData['poin'] ?>/<?= KPI_POINTS['turnover'] ?>
    </div>
</div>

<!-- Info -->
<div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-5 text-xs text-blue-800">
    <div class="font-bold mb-1">📌 Mekanisme Turn Over:</div>
    <ul class="list-disc list-inside space-y-1">
        <li>Poin <strong>otomatis penuh (4 poin)</strong> jika dalam satu bulan tidak ada karyawan yang resign/keluar</li>
        <li>Jika ada karyawan keluar: poin = <strong>0</strong></li>
    </ul>
</div>

<!-- Input Form -->
<div class="kpi-card p-5">
    <h3 class="font-bold text-blue-900 mb-4">Input / Edit Data Turn Over</h3>
    <form method="POST" action="<?= BASE_URL ?>/index.php?page=turnover&action=save">
        <input type="hidden" name="kode_toko" value="<?= $kode ?>">
        <input type="hidden" name="bulan" value="<?= $bulan ?>">
        <input type="hidden" name="tahun" value="<?= $tahun ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Karyawan Aktif</label>
                <input type="number" name="jumlah_karyawan" value="<?= $to['jumlah_karyawan'] ?? 0 ?>" class="input-field" min="0">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Karyawan Keluar/Resign</label>
                <input type="number" name="jumlah_keluar" id="keluar-input" value="<?= $to['jumlah_keluar'] ?? 0 ?>" class="input-field" min="0" oninput="previewPoin(this.value)">
            </div>
            <div class="sm:col-span-2">
                <div id="poin-preview" class="rounded-xl p-3 text-sm font-semibold text-center <?= ($to['jumlah_keluar'] ?? 0) == 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= ($to['jumlah_keluar'] ?? 0) == 0 ? '✅ Poin: 4/4 – Tidak ada yang keluar' : '❌ Poin: 0/4 – Ada karyawan keluar' ?>
                </div>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                <input type="text" name="catatan" value="<?= htmlspecialchars($to['catatan'] ?? '') ?>" class="input-field" placeholder="Nama karyawan yang keluar, alasan, dll.">
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn-primary w-full">Simpan Data Turn Over</button>
        </div>
    </form>
</div>

<script>
function previewPoin(val) {
    const el = document.getElementById('poin-preview');
    const keluar = parseInt(val) || 0;
    if (keluar === 0) {
        el.className = 'rounded-xl p-3 text-sm font-semibold text-center bg-green-100 text-green-700';
        el.textContent = '✅ Poin: 4/4 – Tidak ada yang keluar';
    } else {
        el.className = 'rounded-xl p-3 text-sm font-semibold text-center bg-red-100 text-red-700';
        el.textContent = '❌ Poin: 0/4 – Ada karyawan keluar';
    }
}
document.addEventListener("DOMContentLoaded", initNumberInputs);
</script>
