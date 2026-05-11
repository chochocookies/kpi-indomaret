<?php
// views/dashboard/summary.php
$page     = 'summary';
$bln      = getNamaBulan($bulan);
$d        = $kpiData;
$spd      = $d['spd'];
$nkl      = $d['nkl'];
$nbr      = $d['nbr'];
$std      = $d['std'];
$to       = $d['turnover'];
$grade    = $d['grade'];
$namaToko = $toko['nama_toko'] ?? '';

// ── Build rich WA text ──
function waLine($emoji, $label, $aktual, $target, $ach, $poin, $maks) {
    $bar = $ach >= 100 ? '✅' : ($ach >= 95 ? '⚠️' : '❌');
    return "{$bar} *{$label}*\n"
         . "   Aktual  : {$aktual}\n"
         . "   Target  : {$target}\n"
         . "   ACH     : {$ach}%\n"
         . "   Poin    : {$poin}/{$maks}\n";
}

$hari = $d['hari_berjalan'];
$tgl  = date('d/m/Y');
$waText  = "╔══════════════════════╗\n";
$waText .= "  📊 *LAPORAN KPI TOKO*\n";
$waText .= "  {$namaToko}\n";
$waText .= "╚══════════════════════╝\n";
$waText .= "📅 Periode  : {$bln} {$tahun}\n";
$waText .= "🕐 Update   : {$tgl} (Hari ke-{$hari}/{$d['hari_total']})\n";
$waText .= "────────────────────────\n\n";

// SPD
$waText .= "📦 *POIN 1 – SPD SALES*\n";
$waText .= waLine('', 'a. Offline',
    formatRupiah($spd['aktual_offline'], true), formatRupiah($spd['target_prop_off'], true),
    round($spd['ach_offline'],1), $spd['poin_offline'], KPI_POINTS['spd_offline']);
$waText .= waLine('', 'b. Online (Klik)',
    formatRupiah($spd['aktual_online'], true), formatRupiah($spd['target_prop_on'], true),
    round($spd['ach_online'],1), $spd['poin_online'], KPI_POINTS['spd_online']);
if ($spd['ada_khusus']) {
    $waText .= waLine('', 'e. Produk Khusus',
        formatRupiah($spd['aktual_khusus'], true), formatRupiah($spd['target_prop_khusus'], true),
        round($spd['ach_khusus'],1), $spd['poin_khusus'], KPI_POINTS['spd_khusus']);
}
$waText .= "   *Sub-total SPD : {$spd['poin']}/{$spd['poin_maks']} poin*\n\n";

// NKL
$auditLabel = $nkl['is_audit'] ? '🔍 Audit' : '📋 Proporsional';
$waText .= "🧾 *POIN 2 – NKL ({$auditLabel})*\n";
if ($nkl['is_audit']) {
    $nklStatus = $nkl['nkl_all'] < 0 ? '❌ OVER(negatif)' : ($nkl['poin_all']>0 ? '✅ Dalam budget' : '❌ OVER budget');
    $buahStatus = $nkl['nkl_buah'] < 0 ? '❌ OVER' : '✅ OK';
    $waText .= "❶ NKL ALL Produk\n";
    $waText .= "   Aktual : ".formatRupiah($nkl['nkl_all'],true)."\n";
    $waText .= "   Budget : ".formatRupiah($nkl['budget_all'],true)." (0.20%)\n";
    $waText .= "   Status : {$nklStatus}\n";
    $waText .= "   Poin   : {$nkl['poin_all']}/".KPI_POINTS['nkl_all']."\n";
    $waText .= "❷ NKL Buah\n";
    $waText .= "   Aktual : ".formatRupiah($nkl['nkl_buah'],true)."\n";
    $waText .= "   Status : {$buahStatus}\n";
    $waText .= "   Poin   : {$nkl['poin_buah']}/".KPI_POINTS['nkl_buah']."\n";
} else {
    $waText .= "   Poin otomatis penuh (tidak diaudit)\n";
}
$waText .= "   *Sub-total NKL : {$nkl['poin']}/".KPI_POINTS['nkl_total']." poin*\n\n";

// NBR
$waText .= "🗑️ *POIN 3 – NBR*\n";
$nbrStatus = $nbr['pct_dry'] <= 0.1 ? '✅ OK (≤0.1%)' : '❌ OVER (>0.1%)';
$waText .= "❶ NBR Dry\n";
$waText .= "   Aktual : ".formatRupiah($nbr['nbr_dry'],true)." (".formatPersen($nbr['pct_dry'],3).")\n";
$waText .= "   Status : {$nbrStatus}\n";
$waText .= "   Poin   : {$nbr['poin_dry']}/".KPI_POINTS['nbr_dry']."\n";
$waText .= "❷ NBR Produk Khusus & Perishable\n";
$waText .= "   Modul  : {$nbr['modul_ach']}/{$nbr['modul_main']}\n";
$waText .= "   Poin   : {$nbr['poin_khusus']}/".KPI_POINTS['nbr_khusus']."\n";
$waText .= "   *Sub-total NBR : {$nbr['poin']}/".KPI_POINTS['nbr_total']." poin*\n\n";

// STD
$waText .= "👥 *POIN 4 – STD STORE CREW*\n";
$stdRows2 = [
    ['lbl'=>'a. STD Member Poinku','akt'=>$std['akt_poinku'],'max'=>$std['max_poinku'],'poin'=>$std['poin_poinku'],'maks'=>KPI_POINTS['std_poinku']],
    ['lbl'=>'b. Belanja >2 Item',  'akt'=>$std['akt_2item'], 'max'=>$std['max_2item'], 'poin'=>$std['poin_2item'], 'maks'=>KPI_POINTS['std_2item']],
    ['lbl'=>'c. TRX I-Payment',    'akt'=>$std['akt_ipay'],  'max'=>$std['max_ipay'],  'poin'=>$std['poin_ipay'],  'maks'=>KPI_POINTS['trx_ipayment']],
    ['lbl'=>'d. TRX Non Tunai',    'akt'=>$std['akt_nontunai'],'max'=>$std['max_nontunai'],'poin'=>$std['poin_nontunai'],'maks'=>KPI_POINTS['trx_nontunai']],
];
foreach ($stdRows2 as $sr) {
    $ok2 = $sr['akt'] >= $sr['max'] && $sr['max'] > 0;
    $ic  = $ok2 ? '✅' : '❌';
    $waText .= "{$ic} {$sr['lbl']}\n";
    $waText .= "   Aktual : ".formatPersen($sr['akt'])." | Target L3M: ".formatPersen($sr['max'])."\n";
    $waText .= "   Poin   : {$sr['poin']}/{$sr['maks']}\n";
}
$waText .= "   *Sub-total STD : {$std['poin']}/".KPI_POINTS['std_total']." poin*\n\n";

// TO
$toIc = $to['jumlah_keluar'] == 0 ? '✅' : '❌';
$waText .= "🔄 *POIN 5 – TURN OVER*\n";
$waText .= "{$toIc} Karyawan keluar : {$to['jumlah_keluar']} orang\n";
$waText .= "   Poin : {$to['poin']}/".KPI_POINTS['turnover']."\n\n";

// Total
$waText .= "════════════════════════\n";
$waText .= "📊 *REKAPITULASI POIN*\n";
$waText .= sprintf("   SPD      : %s/%s\n", $d['poin_spd'], $spd['poin_maks']);
$waText .= sprintf("   NKL      : %s/%s\n", $d['poin_nkl'], KPI_POINTS['nkl_total']);
$waText .= sprintf("   NBR      : %s/%s\n", $d['poin_nbr'], KPI_POINTS['nbr_total']);
$waText .= sprintf("   STD      : %s/%s\n", $d['poin_std'], KPI_POINTS['std_total']);
$waText .= sprintf("   Turn Over: %s/%s\n", $d['poin_to'], KPI_POINTS['turnover']);
$waText .= "────────────────────────\n";
$waText .= "*TOTAL   : {$d['poin_total']}/{$d['poin_maks']} poin*\n";
$waText .= "*SCORE   : ".formatPersen($d['poin_pct'])."*\n";
$waText .= "*STATUS  : {$grade['grade']}*\n";
$waText .= "════════════════════════\n";
$waText .= "_Generated by KPI Monitor Indomaret_";

$waUrl = 'https://wa.me/?text=' . rawurlencode($waText);
?>

<?php include __DIR__ . '/../layout/flash.php'; ?>

<?php include __DIR__ . '/../layout/period_selector.php'; ?>

<!-- Header -->
<div class="flex items-center gap-3 mb-4">
    <a href="<?= BASE_URL ?>/index.php?page=dashboard&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
       class="text-blue-500 hover:text-blue-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="text-xl font-extrabold text-blue-900">Rekap KPI & Bagikan</h1>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($namaToko) ?> &middot; <?= $bln ?> <?= $tahun ?></p>
    </div>
</div>

<!-- Grade banner -->
<div class="<?= $grade['bg'] ?> rounded-2xl p-4 mb-4 text-center">
    <div class="text-2xl mb-1">🏆</div>
    <div class="font-extrabold text-xl <?= $grade['class'] ?>"><?= $grade['grade'] ?></div>
    <div class="font-bold text-lg <?= $grade['class'] ?>"><?= formatPersen($d['poin_pct']) ?> &nbsp; <span class="text-sm font-normal">(<?= $d['poin_total'] ?>/<?= $d['poin_maks'] ?> poin)</span></div>
    <div class="progress-bar mt-3 mx-auto" style="max-width:280px;background:rgba(255,255,255,.4)">
        <div class="progress-fill <?= $d['poin_pct']>=90?'bg-green-500':($d['poin_pct']>=70?'bg-yellow-400':'bg-red-400') ?>"
             style="width:<?= min($d['poin_pct'],100) ?>%"></div>
    </div>
    <div class="text-xs <?= $grade['class'] ?> mt-2 opacity-80">Hari ke-<?= $d['hari_berjalan'] ?>/<?= $d['hari_total'] ?> &middot; Update: <?= date('d M Y H:i') ?></div>
</div>

<!-- Poin Detail Table -->
<div class="kpi-card overflow-hidden mb-4">
    <div class="px-4 py-3 bg-blue-700 text-white font-bold text-sm">Rincian Poin Per Komponen</div>
    <table class="w-full text-xs">
        <thead>
            <tr class="bg-blue-50 text-blue-800 font-bold">
                <th class="px-3 py-2 text-left">Komponen</th>
                <th class="px-3 py-2 text-center">Target Prop.</th>
                <th class="px-3 py-2 text-center">Aktual</th>
                <th class="px-3 py-2 text-center">ACH</th>
                <th class="px-3 py-2 text-center">Poin</th>
            </tr>
        </thead>
        <tbody>
        <!-- SPD -->
        <tr class="bg-blue-50/60"><td class="px-3 py-2 font-bold text-blue-900" colspan="5">1. SPD Sales</td></tr>
        <?php
        $spdRows = [
            ['lbl'=>'a. Offline','t'=>$spd['target_prop_off'],'a'=>$spd['aktual_offline'],'ach'=>$spd['ach_offline'],'p'=>$spd['poin_offline'],'m'=>KPI_POINTS['spd_offline']],
            ['lbl'=>'b. Online (Klik)','t'=>$spd['target_prop_on'],'a'=>$spd['aktual_online'],'ach'=>$spd['ach_online'],'p'=>$spd['poin_online'],'m'=>KPI_POINTS['spd_online']],
        ];
        if ($spd['ada_khusus']) $spdRows[] = ['lbl'=>'e. Produk Khusus','t'=>$spd['target_prop_khusus'],'a'=>$spd['aktual_khusus'],'ach'=>$spd['ach_khusus'],'p'=>$spd['poin_khusus'],'m'=>KPI_POINTS['spd_khusus']];
        foreach ($spdRows as $r):
            $cls = $r['ach']>=100?'text-green-600':($r['ach']>=95?'text-yellow-600':'text-red-600');
        ?>
        <tr class="border-t border-blue-50 hover:bg-blue-50/40">
            <td class="px-3 py-2 pl-6"><?= $r['lbl'] ?></td>
            <td class="px-3 py-2 text-center"><?= formatRupiah($r['t'],true) ?></td>
            <td class="px-3 py-2 text-center"><?= formatRupiah($r['a'],true) ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $cls ?>"><?= formatPersen($r['ach'],1) ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $r['p']>0?'text-green-600':'text-red-500' ?>"><?= $r['p'] ?>/<?= $r['m'] ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="bg-blue-100/60 font-bold">
            <td class="px-3 py-2 pl-6 text-blue-800" colspan="4">Sub-total SPD</td>
            <td class="px-3 py-2 text-center text-blue-800"><?= $spd['poin'] ?>/<?= $spd['poin_maks'] ?></td>
        </tr>

        <!-- NKL -->
        <tr class="bg-blue-50/60"><td class="px-3 py-2 font-bold text-blue-900" colspan="5">2. NKL – <?= $nkl['is_audit']?'Audit':'Proporsional' ?></td></tr>
        <tr class="border-t border-blue-50 hover:bg-blue-50/40">
            <td class="px-3 py-2 pl-6">a. NKL ALL</td>
            <td class="px-3 py-2 text-center">≤ <?= formatRupiah($nkl['budget_all'],true) ?></td>
            <td class="px-3 py-2 text-center <?= $nkl['nkl_all']<0?'text-red-600':'' ?>"><?= formatRupiah($nkl['nkl_all'],true) ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $nkl['poin_all']>0?'text-green-600':'text-red-600' ?>"><?= $nkl['is_audit']?$nkl['status_all']:'Prop.' ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $nkl['poin_all']>0?'text-green-600':'text-red-500' ?>"><?= $nkl['poin_all'] ?>/<?= KPI_POINTS['nkl_all'] ?></td>
        </tr>
        <tr class="border-t border-blue-50 hover:bg-blue-50/40">
            <td class="px-3 py-2 pl-6">b. NKL Buah</td>
            <td class="px-3 py-2 text-center">≥ 0</td>
            <td class="px-3 py-2 text-center <?= $nkl['nkl_buah']<0?'text-red-600':'' ?>"><?= formatRupiah($nkl['nkl_buah'],true) ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $nkl['poin_buah']>0?'text-green-600':'text-red-600' ?>"><?= $nkl['is_audit']?($nkl['nkl_buah']>=0?'OK':'OVER'):'Prop.' ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $nkl['poin_buah']>0?'text-green-600':'text-red-500' ?>"><?= $nkl['poin_buah'] ?>/<?= KPI_POINTS['nkl_buah'] ?></td>
        </tr>
        <tr class="bg-blue-100/60 font-bold">
            <td class="px-3 py-2 pl-6 text-blue-800" colspan="4">Sub-total NKL</td>
            <td class="px-3 py-2 text-center text-blue-800"><?= $nkl['poin'] ?>/<?= KPI_POINTS['nkl_total'] ?></td>
        </tr>

        <!-- NBR -->
        <tr class="bg-blue-50/60"><td class="px-3 py-2 font-bold text-blue-900" colspan="5">3. NBR Nota Barang Rusak</td></tr>
        <tr class="border-t border-blue-50 hover:bg-blue-50/40">
            <td class="px-3 py-2 pl-6">a. NBR Dry</td>
            <td class="px-3 py-2 text-center">≤ 0,1%</td>
            <td class="px-3 py-2 text-center"><?= formatPersen($nbr['pct_dry'],3) ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $nbr['pct_dry']<=0.1?'text-green-600':'text-red-600' ?>"><?= $nbr['pct_dry']<=0.1?'OK':'OVER' ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $nbr['poin_dry']>0?'text-green-600':'text-red-500' ?>"><?= $nbr['poin_dry'] ?>/<?= KPI_POINTS['nbr_dry'] ?></td>
        </tr>
        <tr class="border-t border-blue-50 hover:bg-blue-50/40">
            <td class="px-3 py-2 pl-6">b. Produk Khusus & Perishable</td>
            <td class="px-3 py-2 text-center"><?= $nbr['modul_main'] ?> modul</td>
            <td class="px-3 py-2 text-center"><?= $nbr['modul_ach'] ?> modul</td>
            <td class="px-3 py-2 text-center font-bold <?= $nbr['poin_khusus']>0?'text-green-600':'text-red-600' ?>"><?= $nbr['modul_main']>0?formatPersen(($nbr['modul_ach']/$nbr['modul_main'])*100,0):'-' ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $nbr['poin_khusus']>0?'text-green-600':'text-red-500' ?>"><?= $nbr['poin_khusus'] ?>/<?= KPI_POINTS['nbr_khusus'] ?></td>
        </tr>
        <tr class="bg-blue-100/60 font-bold">
            <td class="px-3 py-2 pl-6 text-blue-800" colspan="4">Sub-total NBR</td>
            <td class="px-3 py-2 text-center text-blue-800"><?= $nbr['poin'] ?>/<?= KPI_POINTS['nbr_total'] ?></td>
        </tr>

        <!-- STD -->
        <tr class="bg-blue-50/60"><td class="px-3 py-2 font-bold text-blue-900" colspan="5">4. STD Penawaran Store Crew</td></tr>
        <?php foreach ($stdRows2 as $sr):
            $ok2 = $sr['akt'] >= $sr['max'] && $sr['max'] > 0;
        ?>
        <tr class="border-t border-blue-50 hover:bg-blue-50/40">
            <td class="px-3 py-2 pl-6"><?= $sr['lbl'] ?></td>
            <td class="px-3 py-2 text-center"><?= formatPersen($sr['max']) ?></td>
            <td class="px-3 py-2 text-center"><?= formatPersen($sr['akt']) ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $ok2?'text-green-600':'text-red-600' ?>"><?= $ok2?'✓':'✗' ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $sr['poin']>0?'text-green-600':'text-red-500' ?>"><?= $sr['poin'] ?>/<?= $sr['maks'] ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="bg-blue-100/60 font-bold">
            <td class="px-3 py-2 pl-6 text-blue-800" colspan="4">Sub-total STD</td>
            <td class="px-3 py-2 text-center text-blue-800"><?= $std['poin'] ?>/<?= KPI_POINTS['std_total'] ?></td>
        </tr>

        <!-- TO -->
        <tr class="bg-blue-50/60"><td class="px-3 py-2 font-bold text-blue-900" colspan="5">5. Turn Over</td></tr>
        <tr class="border-t border-blue-50 hover:bg-blue-50/40">
            <td class="px-3 py-2 pl-6">Karyawan Keluar</td>
            <td class="px-3 py-2 text-center">0 orang</td>
            <td class="px-3 py-2 text-center"><?= $to['jumlah_keluar'] ?> orang</td>
            <td class="px-3 py-2 text-center font-bold <?= $to['jumlah_keluar']==0?'text-green-600':'text-red-600' ?>"><?= $to['jumlah_keluar']==0?'OK':'ADA' ?></td>
            <td class="px-3 py-2 text-center font-bold <?= $to['poin']>0?'text-green-600':'text-red-500' ?>"><?= $to['poin'] ?>/<?= KPI_POINTS['turnover'] ?></td>
        </tr>
        </tbody>
        <tfoot>
            <tr class="bg-blue-700 text-white">
                <td class="px-3 py-3 font-extrabold" colspan="4">TOTAL POIN KPI</td>
                <td class="px-3 py-3 font-extrabold text-center"><?= $d['poin_total'] ?>/<?= $d['poin_maks'] ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- WA Share -->
<div class="kpi-card p-4 mb-4">
    <h3 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.555 4.122 1.528 5.859L0 24l6.335-1.508A11.953 11.953 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
        Pesan WhatsApp (Lengkap)
    </h3>
    <textarea id="wa-text" readonly rows="16"
        class="w-full text-xs font-mono bg-slate-50 border border-blue-200 rounded-xl p-3 resize-none focus:outline-none"
        style="line-height:1.5"
    ><?= htmlspecialchars($waText) ?></textarea>
    <div class="flex gap-2 mt-3">
        <button onclick="copyWA()" class="btn-secondary flex-1 text-xs flex items-center justify-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Salin
        </button>
        <a href="<?= $waUrl ?>" target="_blank"
           class="flex-1 font-bold py-2.5 rounded-xl text-xs text-center flex items-center justify-center gap-1.5 text-white"
           style="background:#25d366">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.555 4.122 1.528 5.859L0 24l6.335-1.508A11.953 11.953 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
            Kirim WA
        </a>
    </div>
    <p id="copy-ok" class="text-green-600 text-xs text-center mt-2 hidden">✅ Disalin!</p>
</div>

<script>
function copyWA() {
    var ta = document.getElementById('wa-text');
    ta.select(); ta.setSelectionRange(0,99999);
    navigator.clipboard ? navigator.clipboard.writeText(ta.value).then(showCopyOk) : (document.execCommand('copy'), showCopyOk());
}
function showCopyOk() {
    var el = document.getElementById('copy-ok');
    el.classList.remove('hidden');
    setTimeout(function(){ el.classList.add('hidden'); }, 2500);
}
</script>
