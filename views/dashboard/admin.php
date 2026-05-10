<?php
// views/dashboard/admin.php
$page = 'dashboard';
$bln  = getNamaBulan($bulan);
?>

<?php if ($flash): ?>
<div id="flash-msg" class="fixed top-4 right-4 z-50 px-5 py-3 rounded-xl shadow-lg text-sm font-semibold transition-opacity duration-500
    <?= $flash['type']==='success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
    <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Period selector (admin tanpa toko) -->
<div class="bg-white rounded-2xl shadow-sm border border-blue-50 p-4 mb-5">
    <form method="GET" action="<?= BASE_URL ?>/index.php" class="flex flex-wrap gap-3 items-end">
        <input type="hidden" name="page" value="dashboard">
        <div class="w-32">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Bulan</label>
            <select name="bulan" class="input-field">
                <?php foreach (getSemuaBulan() as $num => $nama): ?>
                <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>><?= $nama ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-24">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Tahun</label>
            <select name="tahun" class="input-field">
                <?php for ($y = date('Y'); $y >= date('Y')-2; $y--): ?>
                <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="btn-primary">Tampilkan</button>
    </form>
</div>

<div class="mb-5">
    <h1 class="text-xl font-extrabold text-blue-900">Rekap KPI Semua Toko</h1>
    <p class="text-sm text-slate-500"><?= $bln ?> <?= $tahun ?> &middot; <?= count($ringkasan) ?> toko</p>
</div>

<!-- Summary stats -->
<?php
$totalTercapai = 0; $totalBelum = 0;
foreach ($ringkasan as $r) {
    if ($r['poin_pct'] >= 70) $totalTercapai++;
    else $totalBelum++;
}
?>
<div class="grid grid-cols-3 gap-3 mb-5">
    <div class="bg-blue-600 rounded-2xl p-4 text-white text-center">
        <div class="font-extrabold text-2xl"><?= count($ringkasan) ?></div>
        <div class="text-xs text-blue-200">Total Toko</div>
    </div>
    <div class="bg-green-500 rounded-2xl p-4 text-white text-center">
        <div class="font-extrabold text-2xl"><?= $totalTercapai ?></div>
        <div class="text-xs text-green-100">Insentif</div>
    </div>
    <div class="bg-red-400 rounded-2xl p-4 text-white text-center">
        <div class="font-extrabold text-2xl"><?= $totalBelum ?></div>
        <div class="text-xs text-red-100">Belum</div>
    </div>
</div>

<!-- Toko list -->
<div class="space-y-3">
<?php foreach ($ringkasan as $r):
    $pct  = $r['poin_pct'];
    $grd  = $r['grade'];
    $nm   = $r['toko']['nama_toko'] ?? '-';
    $kd   = $r['toko']['kode_toko'] ?? '-';
?>
<div class="kpi-card">
    <div class="p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="font-bold text-blue-900 text-sm"><?= htmlspecialchars($nm) ?></div>
                <div class="text-xs text-slate-400 font-mono"><?= $kd ?></div>
            </div>
            <div class="<?= $grd['bg'] ?> px-3 py-1.5 rounded-xl text-center">
                <div class="font-extrabold <?= $grd['class'] ?> text-xs"><?= $grd['grade'] ?></div>
                <div class="text-xs <?= $grd['class'] ?>"><?= formatPersen($pct) ?></div>
            </div>
        </div>

        <!-- Progress -->
        <div class="progress-bar mb-2">
            <div class="progress-fill <?= $pct >= 90 ? 'bg-green-500' : ($pct >= 70 ? 'bg-yellow-400' : 'bg-red-400') ?>"
                style="width:<?= min($pct,100) ?>%"></div>
        </div>

        <!-- Poin breakdown -->
        <div class="grid grid-cols-5 gap-1 mb-3">
            <?php
            $breakdown = [
                ['label'=>'SPD','poin'=>$r['poin_spd'],'maks'=>$r['spd']['poin_maks']],
                ['label'=>'NKL','poin'=>$r['poin_nkl'],'maks'=>KPI_POINTS['nkl_total']],
                ['label'=>'NBR','poin'=>$r['poin_nbr'],'maks'=>KPI_POINTS['nbr_total']],
                ['label'=>'STD','poin'=>$r['poin_std'],'maks'=>KPI_POINTS['std_total']],
                ['label'=>'TO','poin'=>$r['poin_to'],'maks'=>KPI_POINTS['turnover']],
            ];
            foreach ($breakdown as $b):
                $bAch = $b['maks'] > 0 ? ($b['poin']/$b['maks'])*100 : 0;
            ?>
            <div class="text-center bg-slate-50 rounded-lg p-1.5">
                <div class="text-xs text-slate-400"><?= $b['label'] ?></div>
                <div class="font-bold text-xs <?= $bAch >= 100 ? 'text-green-600' : ($bAch >= 50 ? 'text-yellow-600' : 'text-red-500') ?>"><?= $b['poin'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Admin tidak bisa lihat detail toko -->
        <div class="text-xs text-center text-slate-400 bg-slate-50 rounded-xl py-2 px-3">
            🔒 Detail toko hanya bisa diakses oleh kepala toko masing-masing
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
