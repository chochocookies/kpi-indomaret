<?php
// views/layout/period_selector.php
// Params: $kode, $bulan, $tahun, $page, $semuaToko, $action (optional)
$semuaToko = $semuaToko ?? [];
$action    = $action ?? 'index';
$_bulanList = getSemuaBulan();
?>
<div class="bg-white rounded-2xl shadow-sm border border-blue-50 p-4 mb-5">
    <form method="GET" action="<?= BASE_URL ?>/index.php" class="flex flex-wrap gap-3 items-end">
        <input type="hidden" name="page" value="<?= htmlspecialchars($page ?? 'dashboard') ?>">
        <?php if ($action !== 'index'): ?>
        <input type="hidden" name="action" value="<?= htmlspecialchars($action) ?>">
        <?php endif; ?>

        <?php if (!empty($semuaToko)): ?>
        <div class="flex-1 min-w-[140px]">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Toko</label>
            <select name="kode_toko" class="input-field">
                <option value="">-- Pilih Toko --</option>
                <?php foreach ($semuaToko as $t): ?>
                <option value="<?= $t['kode_toko'] ?>" <?= ($kode ?? '') === $t['kode_toko'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['kode_toko']) ?> – <?= htmlspecialchars($t['nama_toko']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php else: ?>
        <input type="hidden" name="kode_toko" value="<?= htmlspecialchars($kode ?? '') ?>">
        <?php endif; ?>

        <div class="w-32">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Bulan</label>
            <select name="bulan" class="input-field">
                <?php foreach ($_bulanList as $num => $nama): ?>
                <option value="<?= $num ?>" <?= ($bulan ?? date('n')) == $num ? 'selected' : '' ?>><?= $nama ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="w-24">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Tahun</label>
            <select name="tahun" class="input-field">
                <?php for ($y = date('Y'); $y >= date('Y')-2; $y--): ?>
                <option value="<?= $y ?>" <?= ($tahun ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <button type="submit" class="btn-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Tampilkan
        </button>
    </form>
</div>
