<?php
// views/nkl/index.php
$page     = 'nkl';
$bln      = getNamaBulan($bulan);
$nklData  = $kpiData['nkl'];
$namaToko = $toko['nama_toko'] ?? '';
?>

<?php if ($flash): ?>
<div id="flash-msg" class="fixed top-4 right-4 z-50 px-5 py-3 rounded-xl shadow-lg text-sm font-semibold transition-opacity duration-500
    <?= $flash['type']==='success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
    <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout/period_selector.php'; ?>

<div class="flex items-center gap-3 mb-5">
    <a href="<?= BASE_URL ?>/index.php?page=dashboard&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="text-blue-500 hover:text-blue-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="text-xl font-extrabold text-blue-900">Poin 2 – NKL (Nota Kurang Lebih)</h1>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($namaToko) ?> &middot; <?= $bln ?> <?= $tahun ?></p>
    </div>
</div>

<!-- Panduan -->
<div class="info-box mb-4">
    <div class="font-bold mb-1.5 text-blue-800">📖 Panduan Pengisian – NKL (Nota Kurang Lebih)</div>
    <ul class="space-y-1 list-disc list-inside text-blue-700" style="font-size:.78rem">
        <li><strong>Jika toko diaudit (SO BIC)</strong>: Masukkan Sales Gross aktual berjalan dan nilai NKL dari laporan audit</li>
        <li>Budget NKL = <strong>0,20% × Sales Gross aktual berjalan</strong> (dihitung otomatis)</li>
        <li>NKL harus <strong>≥ 0</strong> dan <strong>≤ Budget</strong> agar mendapat poin. NKL negatif = OVER (jelek)</li>
        <li><strong>a. NKL ALL Produk (Exc. Buah)</strong> – Semua produk kecuali buah-buahan</li>
        <li><strong>b. NKL Buah</strong> – Khusus buah, hanya jika audit. NKL Buah ≥ 0 = poin penuh</li>
        <li><strong>Jika tidak diaudit</strong>: Poin otomatis penuh (proporsional), tidak perlu input data NKL</li>
        <li>Data NKL dapat dilihat dari laporan SO BIC / Stok Opname</li>
    </ul>
</div>

<!-- Status & Poin -->
<div class="grid grid-cols-2 gap-3 mb-5">
    <div class="kpi-card p-4 text-center">
        <div class="text-xs text-slate-400 mb-1">Status Audit</div>
        <div class="font-bold text-sm <?= $nklData['is_audit'] ? 'text-orange-600' : 'text-blue-600' ?>">
            <?= $nklData['is_audit'] ? '🔍 Diaudit' : '📋 Proporsional' ?>
        </div>
    </div>
    <div class="kpi-card p-4 text-center">
        <div class="text-xs text-slate-400 mb-1">Total Poin NKL</div>
        <div class="font-extrabold text-xl text-blue-700"><?= $nklData['poin'] ?>/<?= KPI_POINTS['nkl_total'] ?></div>
    </div>
</div>

<!-- Mekanisme Info -->
<div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-5 text-xs text-blue-800">
    <div class="font-bold mb-1">📌 Mekanisme NKL:</div>
    <ul class="space-y-1 list-disc list-inside">
        <li>Jika toko <strong>diaudit (SO BIC)</strong>: budget NKL = <strong>0,20% × sales aktual berjalan</strong>. NKL harus ≥ 0 dan ≤ budget.</li>
        <li>NKL <strong>negatif = OVER</strong> (jelek) → poin 0</li>
        <li>Jika toko <strong>tidak diaudit</strong>: poin otomatis penuh (proporsional)</li>
    </ul>
</div>

<!-- Hasil Kalkulasi (jika audit) -->
<?php if ($nklData['is_audit'] && $nklData['sales_all'] > 0): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
    <div class="<?= $nklData['poin_all'] > 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> border rounded-2xl p-4">
        <div class="flex justify-between items-start mb-2">
            <span class="font-bold text-slate-700 text-sm">a. NKL ALL Produk (Exc. Buah)</span>
            <span class="poin-badge <?= $nklData['poin_all'] > 0 ? 'badge-success' : 'badge-danger' ?>"><?= $nklData['poin_all'] ?>/<?= KPI_POINTS['nkl_all'] ?></span>
        </div>
        <div class="space-y-1 text-xs">
            <div class="flex justify-between"><span class="text-slate-500">Sales Gross:</span><span class="font-semibold"><?= formatRupiah($nklData['sales_all'], true) ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Budget (0,20%):</span><span class="font-semibold text-blue-700"><?= formatRupiah($nklData['budget_all'], true) ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Aktual NKL:</span>
                <span class="font-bold <?= $nklData['nkl_all'] < 0 ? 'text-red-600' : 'text-green-600' ?>"><?= formatRupiah($nklData['nkl_all'], true) ?></span>
            </div>
            <div class="mt-1 pt-1 border-t border-current/20">
                <span class="font-bold <?= $nklData['poin_all'] > 0 ? 'text-green-700' : 'text-red-700' ?>">
                    <?= $nklData['nkl_all'] < 0 ? '❌ OVER (negatif)' : ($nklData['poin_all'] > 0 ? '✅ Dalam Budget' : '❌ OVER Budget') ?>
                </span>
            </div>
        </div>
    </div>
    <div class="<?= $nklData['poin_buah'] > 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> border rounded-2xl p-4">
        <div class="flex justify-between items-start mb-2">
            <span class="font-bold text-slate-700 text-sm">b. NKL Buah</span>
            <span class="poin-badge <?= $nklData['poin_buah'] > 0 ? 'badge-success' : 'badge-danger' ?>"><?= $nklData['poin_buah'] ?>/<?= KPI_POINTS['nkl_buah'] ?></span>
        </div>
        <div class="space-y-1 text-xs">
            <div class="flex justify-between"><span class="text-slate-500">Sales Buah:</span><span class="font-semibold"><?= formatRupiah($nklData['sales_buah'], true) ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Aktual NKL Buah:</span>
                <span class="font-bold <?= $nklData['nkl_buah'] < 0 ? 'text-red-600' : 'text-green-600' ?>"><?= formatRupiah($nklData['nkl_buah'], true) ?></span>
            </div>
            <div class="mt-1 pt-1 border-t border-current/20">
                <span class="font-bold <?= $nklData['poin_buah'] > 0 ? 'text-green-700' : 'text-red-700' ?>">
                    <?= $nklData['nkl_buah'] < 0 ? '❌ OVER (negatif)' : '✅ OK' ?>
                </span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Input Form -->
<div class="kpi-card p-5">
    <h3 class="font-bold text-blue-900 mb-4">Input / Edit Data NKL</h3>
    <form method="POST" action="<?= BASE_URL ?>/index.php?page=nkl&action=save">
        <input type="hidden" name="kode_toko" value="<?= $kode ?>">
        <input type="hidden" name="bulan" value="<?= $bulan ?>">
        <input type="hidden" name="tahun" value="<?= $tahun ?>">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Status Toko Bulan Ini</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_audit" value="0" <?= !$nklData['is_audit'] ? 'checked' : '' ?> class="w-4 h-4 text-blue-600">
                        <span class="text-sm font-medium text-slate-700">📋 Tidak Diaudit (Proporsional)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_audit" value="1" <?= $nklData['is_audit'] ? 'checked' : '' ?> class="w-4 h-4 text-blue-600">
                        <span class="text-sm font-medium text-slate-700">🔍 Diaudit (SO BIC)</span>
                    </label>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="audit-fields">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sales Gross ALL (Exc. Buah)</label>
                    <input type="text" name="sales_gross_all" value="<?= formatAngka($nkl['sales_gross_all'] ?? 0) ?>" class="input-field" oninput="formatNumber(this); hitungBudget()">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Aktual NKL ALL (negatif = over)</label>
                    <input type="text" name="aktual_nkl_all" value="<?= $nkl['aktual_nkl_all'] ?? 0 ?>" class="input-field" placeholder="Bisa negatif">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sales Gross Buah</label>
                    <input type="text" name="sales_gross_buah" value="<?= formatAngka($nkl['sales_gross_buah'] ?? 0) ?>" class="input-field" oninput="formatNumber(this)">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Aktual NKL Buah (negatif = over)</label>
                    <input type="text" name="aktual_nkl_buah" value="<?= $nkl['aktual_nkl_buah'] ?? 0 ?>" class="input-field" placeholder="Bisa negatif">
                </div>
            </div>
            <div id="budget-preview" class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-800 hidden">
                Budget NKL ALL (0,20%): <span id="budget-val" class="font-bold">Rp 0</span>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                <input type="text" name="catatan" value="<?= htmlspecialchars($nkl['catatan'] ?? '') ?>" class="input-field" placeholder="Opsional">
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn-primary w-full">Simpan Data NKL</button>
        </div>
    </form>
</div>

<script>
function hitungBudget() {
    const salesInput = document.querySelector('[name="sales_gross_all"]');
    const sales = parseInt((salesInput.value || '0').replace(/\./g,'')) || 0;
    const budget = sales * 0.002;
    document.getElementById('budget-val').textContent = 'Rp ' + budget.toLocaleString('id-ID');
    document.getElementById('budget-preview').classList.toggle('hidden', sales === 0);
}
document.querySelectorAll('[name="is_audit"]').forEach(r => {
    r.addEventListener('change', function() {
        document.getElementById('audit-fields').style.opacity = this.value === '1' ? '1' : '0.5';
    });
});
// Init
const auditRadio = document.querySelector('[name="is_audit"]:checked');
if (auditRadio && auditRadio.value === '0') {
    document.getElementById('audit-fields').style.opacity = '0.5';
}
hitungBudget();
</script>
