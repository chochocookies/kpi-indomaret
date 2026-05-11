<?php
// views/dashboard/toko.php
$d      = $kpiData;
$spd    = $d['spd'];
$nkl    = $d['nkl'];
$nbr    = $d['nbr'];
$std    = $d['std'];
$to     = $d['turnover'];
$grade  = $d['grade'];
$bln    = getNamaBulan($bulan);
$page   = 'dashboard';
$kode   = $d['toko']['kode_toko'] ?? $_SESSION['kode_toko'];
$namaToko = $d['toko']['nama_toko'] ?? '';
?>

<?php include __DIR__ . '/../layout/flash.php'; ?>

<!-- Period Selector -->
<?php include __DIR__ . '/../layout/period_selector.php'; ?>

<!-- Toko Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Laporan KPI Bulanan</p>
        <h1 class="text-xl font-extrabold text-blue-900"><?= htmlspecialchars($namaToko) ?></h1>
        <p class="text-sm text-slate-500"><?= $bln ?> <?= $tahun ?> &middot; Hari ke-<?= $d['hari_berjalan'] ?> dari <?= $d['hari_total'] ?> hari</p>
    </div>
    <!-- Grade badge -->
    <div class="<?= $grade['bg'] ?> px-5 py-3 rounded-2xl text-center">
        <div class="text-xs font-semibold <?= $grade['class'] ?> mb-0.5">Status Insentif</div>
        <div class="font-extrabold <?= $grade['class'] ?> text-sm"><?= $grade['grade'] ?></div>
        <div class="text-xs <?= $grade['class'] ?> mt-0.5"><?= $d['poin_total'] ?>/<?= $d['poin_maks'] ?> poin (<?= formatPersen($d['poin_pct']) ?>)</div>
    </div>
</div>

<!-- Total Poin Progress -->
<div class="kpi-card p-4 mb-5">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-bold text-slate-700">Total Skor KPI</span>
        <span class="font-extrabold text-blue-700 text-lg"><?= formatPersen($d['poin_pct']) ?></span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill <?= $d['poin_pct'] >= 90 ? 'bg-green-500' : ($d['poin_pct'] >= 70 ? 'bg-yellow-400' : 'bg-red-400') ?>" style="width:<?= min($d['poin_pct'],100) ?>%"></div>
    </div>
    <div class="flex justify-between text-xs text-slate-400 mt-1.5">
        <span><?= $d['poin_total'] ?> poin dari <?= $d['poin_maks'] ?> poin maks</span>
        <span>Target insentif ≥ 70%</span>
    </div>
</div>

<!-- KPI Cards Grid -->
<div class="grid grid-cols-1 gap-4">

<!-- ====== POIN 1: SPD ====== -->
<div class="kpi-card">
    <div class="flex items-center justify-between px-5 py-4 border-b border-blue-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <div class="font-bold text-blue-900">Poin 1 – SPD Sales</div>
                <div class="text-xs text-slate-400">Sales Offline + Online + Produk Khusus</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-slate-400">Poin</div>
            <div class="font-extrabold text-blue-700"><?= $spd['poin'] ?>/<?= $spd['poin_maks'] ?></div>
        </div>
    </div>
    <div class="p-5">
        <!-- SPD Total -->
        <div class="mb-4">
            <div class="flex justify-between items-center mb-1.5">
                <span class="text-sm font-semibold text-slate-700">Total SPD Berjalan</span>
                <span class="<?= getColorClass($spd['ach_total']) ?> font-bold text-sm"><?= formatPersen($spd['ach_total']) ?></span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill <?= $spd['ach_total'] >= 100 ? 'bg-green-500' : ($spd['ach_total'] >= 95 ? 'bg-yellow-400' : 'bg-red-400') ?>"
                    style="width:<?= min($spd['ach_total'],100) ?>%"></div>
            </div>
            <div class="flex justify-between text-xs text-slate-400 mt-1">
                <span>Aktual: <?= formatRupiah($spd['aktual_berjalan'], true) ?></span>
                <span>Target Prop: <?= formatRupiah($spd['target_prop'], true) ?></span>
            </div>
        </div>

        <!-- Sub Offline -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <?php
            $subItems = [
                ['label'=>'a. SPD Offline', 'aktual'=>$spd['aktual_offline'], 'target'=>$spd['target_prop_off'], 'ach'=>$spd['ach_offline'], 'poin'=>$spd['poin_offline'], 'maks'=>KPI_POINTS['spd_offline']],
                ['label'=>'b. SPD Online (Klik)', 'aktual'=>$spd['aktual_online'], 'target'=>$spd['target_prop_on'], 'ach'=>$spd['ach_online'], 'poin'=>$spd['poin_online'], 'maks'=>KPI_POINTS['spd_online']],
            ];
            if ($spd['ada_khusus']) {
                $subItems[] = ['label'=>'e. Produk Khusus', 'aktual'=>$spd['aktual_khusus'], 'target'=>$spd['target_prop_khusus'], 'ach'=>$spd['ach_khusus'], 'poin'=>$spd['poin_khusus'], 'maks'=>KPI_POINTS['spd_khusus']];
            }
            foreach ($subItems as $sub): ?>
            <div class="<?= getBgClass($sub['ach']) ?> border rounded-xl p-3">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-xs font-semibold text-slate-600"><?= $sub['label'] ?></span>
                    <span class="poin-badge <?= $sub['poin'] > 0 ? 'badge-success' : 'badge-danger' ?>"><?= $sub['poin'] ?>/<?= $sub['maks'] ?></span>
                </div>
                <div class="<?= getColorClass($sub['ach']) ?> font-bold text-base"><?= formatPersen($sub['ach']) ?></div>
                <div class="text-xs text-slate-500 mt-0.5"><?= formatRupiah($sub['aktual'], true) ?> / <?= formatRupiah($sub['target'], true) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Detail button -->
        <div class="mt-4 flex gap-2">
            <a href="<?= BASE_URL ?>/index.php?page=spd&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
               class="btn-secondary text-xs flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Lihat Detail & Input
            </a>
        </div>
    </div>
</div>

<!-- ====== POIN 2: NKL ====== -->
<div class="kpi-card">
    <div class="flex items-center justify-between px-5 py-4 border-b border-blue-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            </div>
            <div>
                <div class="font-bold text-blue-900">Poin 2 – NKL</div>
                <div class="text-xs text-slate-400">Nota Kurang Lebih</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-slate-400">Poin</div>
            <div class="font-extrabold text-blue-700"><?= $nkl['poin'] ?>/<?= KPI_POINTS['nkl_total'] ?></div>
        </div>
    </div>
    <div class="p-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold <?= $nkl['is_audit'] ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' ?>">
                <?= $nkl['is_audit'] ? '🔍 Toko Audit' : '📋 Proporsional' ?>
            </span>
            <?php if (!$nkl['is_audit']): ?>
            <span class="text-xs text-slate-400">Poin otomatis penuh karena tidak diaudit</span>
            <?php endif; ?>
        </div>

        <?php if ($nkl['is_audit']): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- NKL All -->
            <div class="<?= $nkl['nkl_all'] >= 0 && $nkl['nkl_all'] <= $nkl['budget_all'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> border rounded-xl p-3">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-semibold text-slate-600">a. NKL ALL Produk (Exc. Buah)</span>
                    <span class="poin-badge <?= $nkl['poin_all'] > 0 ? 'badge-success' : 'badge-danger' ?>"><?= $nkl['poin_all'] ?>/<?= KPI_POINTS['nkl_all'] ?></span>
                </div>
                <div class="<?= $nkl['nkl_all'] < 0 ? 'text-red-600' : 'text-green-600' ?> font-bold text-base mt-1"><?= formatRupiah($nkl['nkl_all'], true) ?></div>
                <div class="text-xs text-slate-500">Budget: <?= formatRupiah($nkl['budget_all'], true) ?> (0.20% sales)</div>
                <div class="text-xs font-semibold mt-1 <?= $nkl['nkl_all'] < 0 ? 'text-red-600' : 'text-green-600' ?>"><?= $nkl['status_all'] ?></div>
            </div>
            <!-- NKL Buah -->
            <div class="<?= $nkl['nkl_buah'] >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> border rounded-xl p-3">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-semibold text-slate-600">b. NKL Buah</span>
                    <span class="poin-badge <?= $nkl['poin_buah'] > 0 ? 'badge-success' : 'badge-danger' ?>"><?= $nkl['poin_buah'] ?>/<?= KPI_POINTS['nkl_buah'] ?></span>
                </div>
                <div class="<?= $nkl['nkl_buah'] < 0 ? 'text-red-600' : 'text-green-600' ?> font-bold text-base mt-1"><?= formatRupiah($nkl['nkl_buah'], true) ?></div>
                <div class="text-xs <?= $nkl['nkl_buah'] < 0 ? 'text-red-500' : 'text-green-600' ?> font-semibold mt-1"><?= $nkl['nkl_buah'] < 0 ? 'OVER' : 'OK' ?></div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
            <div class="text-blue-700 font-semibold text-sm">Poin Penuh – Toko Tidak Diaudit</div>
            <div class="text-blue-500 text-xs mt-1">NKL bersifat proporsional untuk bulan ini</div>
        </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="<?= BASE_URL ?>/index.php?page=nkl&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
               class="btn-secondary text-xs flex items-center gap-1.5 inline-flex">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Input/Edit NKL
            </a>
        </div>
    </div>
</div>

<!-- ====== POIN 3: NBR ====== -->
<div class="kpi-card">
    <div class="flex items-center justify-between px-5 py-4 border-b border-blue-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <div class="font-bold text-blue-900">Poin 3 – NBR</div>
                <div class="text-xs text-slate-400">Nota Barang Rusak</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-slate-400">Poin</div>
            <div class="font-extrabold text-blue-700"><?= $nbr['poin'] ?>/<?= KPI_POINTS['nbr_total'] ?></div>
        </div>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- NBR Dry -->
            <div class="<?= $nbr['pct_dry'] <= 0.1 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> border rounded-xl p-3">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-semibold text-slate-600">a. NBR Dry</span>
                    <span class="poin-badge <?= $nbr['poin_dry'] > 0 ? 'badge-success' : 'badge-danger' ?>"><?= $nbr['poin_dry'] ?>/<?= KPI_POINTS['nbr_dry'] ?></span>
                </div>
                <div class="<?= $nbr['pct_dry'] <= 0.1 ? 'text-green-600' : 'text-red-600' ?> font-bold text-base mt-1"><?= formatPersen($nbr['pct_dry']) ?></div>
                <div class="text-xs text-slate-500">Aktual: <?= formatRupiah($nbr['nbr_dry'], true) ?> / Target ≤ 0.1%</div>
            </div>
            <!-- NBR Khusus/Perishable -->
            <div class="<?= $nbr['poin_khusus'] > 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> border rounded-xl p-3">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-semibold text-slate-600">b. NBR Produk Khusus & Perishable</span>
                    <span class="poin-badge <?= $nbr['poin_khusus'] > 0 ? 'badge-success' : 'badge-danger' ?>"><?= $nbr['poin_khusus'] ?>/<?= KPI_POINTS['nbr_khusus'] ?></span>
                </div>
                <div class="font-bold text-base mt-1 text-slate-700"><?= $nbr['modul_ach'] ?>/<?= $nbr['modul_main'] ?> Modul</div>
                <div class="text-xs text-slate-500"><?= $nbr['modul_main'] > 0 ? formatPersen(($nbr['modul_ach']/$nbr['modul_main'])*100) . ' tercapai' : 'Belum ada data' ?></div>
            </div>
        </div>
        <div class="mt-3">
            <a href="<?= BASE_URL ?>/index.php?page=nbr&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
               class="btn-secondary text-xs flex items-center gap-1.5 inline-flex">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Input/Edit NBR
            </a>
        </div>
    </div>
</div>

<!-- ====== POIN 4: STD ====== -->
<div class="kpi-card">
    <div class="flex items-center justify-between px-5 py-4 border-b border-blue-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="font-bold text-blue-900">Poin 4 – STD / Penawaran Store Crew</div>
                <div class="text-xs text-slate-400">Poinku, 2 Item, I-Payment, Non Tunai</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-slate-400">Poin</div>
            <div class="font-extrabold text-blue-700"><?= $std['poin'] ?>/<?= KPI_POINTS['std_total'] ?></div>
        </div>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-2 gap-3">
            <?php
            $stdItems = [
                ['label'=>'a. STD Member Poinku','aktual'=>$std['akt_poinku'],'target'=>$std['max_poinku'],'poin'=>$std['poin_poinku'],'maks'=>KPI_POINTS['std_poinku'],'unit'=>'%'],
                ['label'=>'b. Belanja >2 Item','aktual'=>$std['akt_2item'],'target'=>$std['max_2item'],'poin'=>$std['poin_2item'],'maks'=>KPI_POINTS['std_2item'],'unit'=>'%'],
                ['label'=>'c. TRX I-Payment','aktual'=>$std['akt_ipay'],'target'=>$std['max_ipay'],'poin'=>$std['poin_ipay'],'maks'=>KPI_POINTS['trx_ipayment'],'unit'=>'%'],
                ['label'=>'d. TRX Non Tunai','aktual'=>$std['akt_nontunai'],'target'=>$std['max_nontunai'],'poin'=>$std['poin_nontunai'],'maks'=>KPI_POINTS['trx_nontunai'],'unit'=>'%'],
            ];
            foreach ($stdItems as $si):
                $ok = $si['aktual'] >= $si['target'] && $si['target'] > 0;
            ?>
            <div class="<?= $ok ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> border rounded-xl p-3">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-semibold text-slate-600"><?= $si['label'] ?></span>
                    <span class="poin-badge <?= $si['poin'] > 0 ? 'badge-success' : 'badge-danger' ?>"><?= $si['poin'] ?>/<?= $si['maks'] ?></span>
                </div>
                <div class="<?= $ok ? 'text-green-600' : 'text-red-600' ?> font-bold text-base mt-1"><?= formatPersen($si['aktual']) ?></div>
                <div class="text-xs text-slate-500">Target L3M Max: <?= formatPersen($si['target']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-3">
            <a href="<?= BASE_URL ?>/index.php?page=std&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
               class="btn-secondary text-xs flex items-center gap-1.5 inline-flex">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Input/Edit STD
            </a>
        </div>
    </div>
</div>

<!-- ====== POIN 5: TURNOVER ====== -->
<div class="kpi-card">
    <div class="flex items-center justify-between px-5 py-4 border-b border-blue-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div>
                <div class="font-bold text-blue-900">Poin 5 – Turn Over Karyawan</div>
                <div class="text-xs text-slate-400">Otomatis penuh jika tidak ada karyawan keluar</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-slate-400">Poin</div>
            <div class="font-extrabold <?= $to['poin'] > 0 ? 'text-green-600' : 'text-red-600' ?>"><?= $to['poin'] ?>/<?= KPI_POINTS['turnover'] ?></div>
        </div>
    </div>
    <div class="p-5">
        <div class="<?= $to['jumlah_keluar'] == 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> border rounded-xl p-4 flex items-center gap-4">
            <div class="text-3xl"><?= $to['jumlah_keluar'] == 0 ? '✅' : '❌' ?></div>
            <div>
                <div class="font-bold <?= $to['jumlah_keluar'] == 0 ? 'text-green-700' : 'text-red-700' ?>">
                    <?= $to['jumlah_keluar'] == 0 ? 'Tidak Ada Karyawan Keluar' : $to['jumlah_keluar'] . ' Karyawan Keluar' ?>
                </div>
                <div class="text-xs text-slate-500">Total karyawan: <?= $to['jumlah_karyawan'] ?> orang</div>
            </div>
        </div>
        <div class="mt-3">
            <a href="<?= BASE_URL ?>/index.php?page=turnover&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
               class="btn-secondary text-xs flex items-center gap-1.5 inline-flex">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Input/Edit Turn Over
            </a>
        </div>
    </div>
</div>


<!-- ════ MINI KALENDER SPD ════ -->
<div class="kpi-card mt-4">
    <div class="flex items-center justify-between px-5 py-3 border-b border-blue-50">
        <div class="font-bold text-blue-900 text-sm">📅 Status Pengisian Data Harian</div>
        <a href="<?= BASE_URL ?>/index.php?page=spd&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
           class="text-xs text-blue-600 hover:text-blue-800 font-semibold">Input Data →</a>
    </div>
    <div class="p-4">
        <div id="dash-calendar"></div>
        <?php
        $spdHarian   = $kpiModel->getAktualSpdBulanan($kode, $tahun, $bulan);
        $spdFilled   = array_column($spdHarian, 'tanggal');
        $totalTerisi = count($spdFilled);
        $pctTerisi   = $d['hari_berjalan'] > 0 ? round($totalTerisi/$d['hari_berjalan']*100) : 0;
        ?>
        <div class="flex items-center justify-between mt-3 text-xs text-slate-500">
            <span><?= $totalTerisi ?> dari <?= $d['hari_berjalan'] ?> hari terisi</span>
            <span class="font-bold <?= $pctTerisi >= 80 ? 'text-green-600' : 'text-red-500' ?>"><?= $pctTerisi ?>% kelengkapan</span>
        </div>
    </div>
</div>

</div><!-- end grid -->

<!-- Share Button -->
<div class="mt-5 flex gap-3">
    <a href="<?= BASE_URL ?>/index.php?page=summary&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
       class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl text-sm text-center transition-all flex items-center justify-center gap-2 shadow-sm">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.555 4.122 1.528 5.859L0 24l6.335-1.508A11.953 11.953 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
        Bagikan via WhatsApp
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var filled = <?php echo json_encode($spdFilled ?? []); ?>;
    KPI.renderCalendar('dash-calendar', <?php echo $tahun ?>, <?php echo $bulan ?>, filled, function(d){
        window.location.href = '<?= BASE_URL ?>/index.php?page=spd&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>';
    });
    initNumberInputs();
});
</script>
