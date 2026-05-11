<?php
// views/std/index.php
$page     = 'std';
$bln      = getNamaBulan($bulan);
$stdData  = $kpiData['std'];
$namaToko = $toko['nama_toko'] ?? '';
$bulanNama = getSemuaBulan();
$b1 = $bulan==1?12:$bulan-1; $b2=$b1==1?12:$b1-1; $b3=$b2==1?12:$b2-1;
// Compute aktual from harian if available
$aktPoinku=$aktItem2=$aktIpay=$aktNontunai=0;
$totalTrxPoinku=$totalTrxItem2=$totalTrxNontunai=0;
foreach ($stdHarian as $h) {
    $aktPoinku     += $h['poinku_trx'];
    $totalTrxPoinku+= $h['poinku_total_trx'];
    $aktItem2      += $h['item2_trx'];
    $totalTrxItem2 += $h['item2_total_trx'];
    $aktIpay       += $h['ipayment_trx'];
    $aktNontunai   += $h['nontunai_trx'];
    $totalTrxNontunai += $h['nontunai_total_trx'];
}
$pctPoinkuH  = $totalTrxPoinku>0 ? round($aktPoinku/$totalTrxPoinku*100,2) : 0;
$pctItem2H   = $totalTrxItem2>0  ? round($aktItem2/$totalTrxItem2*100,2)   : 0;
$pctNontunaiH= $totalTrxNontunai>0 ? round($aktNontunai/$totalTrxNontunai*100,2) : 0;
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
        <h1 class="text-xl font-extrabold text-blue-900">Poin 4 – STD / Penawaran Store Crew</h1>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($namaToko) ?> &middot; <?= $bln ?> <?= $tahun ?></p>
    </div>
</div>

<!-- Panduan -->
<div class="info-box mb-4">
    <div class="font-bold mb-1.5">📖 Panduan Pengisian – STD</div>
    <ul class="space-y-1 list-disc list-inside" style="font-size:.78rem">
        <li><strong>a. STD Member Poinku</strong> – TRX member yang belanja pakai Poinku + iSaku. Lihat total TRX di menu Reversal</li>
        <li><strong>b. STD Belanja >2 Item</strong> – Proporsi belanja lebih dari 2 item. Update jika ada data, jika tidak ada biarkan 0</li>
        <li><strong>c. TRX I-Payment (Fee Base)</strong> – Transaksi payment point dengan admin fee. Catat TRX per hari</li>
        <li><strong>d. TRX Non Tunai</strong> – Pembayaran non-tunai. Lihat menu Reversal → transaksi belanja (atau transaksi debit)</li>
        <li>Target = nilai <strong>tertinggi</strong> dari B-1, B-2, B-3 (L3M). Aktual ≥ Target = poin penuh</li>
    </ul>
</div>

<!-- Poin Summary Cards -->
<div class="grid grid-cols-2 gap-3 mb-4">
    <?php
    $stdSubs = [
        ['label'=>'a. STD Poinku','poin'=>$stdData['poin_poinku'],'maks'=>KPI_POINTS['std_poinku'],'akt'=>$stdData['akt_poinku'],'max'=>$stdData['max_poinku']],
        ['label'=>'b. Belanja >2 Item','poin'=>$stdData['poin_2item'],'maks'=>KPI_POINTS['std_2item'],'akt'=>$stdData['akt_2item'],'max'=>$stdData['max_2item']],
        ['label'=>'c. TRX I-Payment','poin'=>$stdData['poin_ipay'],'maks'=>KPI_POINTS['trx_ipayment'],'akt'=>$stdData['akt_ipay'],'max'=>$stdData['max_ipay']],
        ['label'=>'d. TRX Non Tunai','poin'=>$stdData['poin_nontunai'],'maks'=>KPI_POINTS['trx_nontunai'],'akt'=>$stdData['akt_nontunai'],'max'=>$stdData['max_nontunai']],
    ];
    foreach ($stdSubs as $s):
        $ok = $s['akt'] >= $s['max'] && $s['max'] > 0;
    ?>
    <div class="<?= $ok?'bg-green-50 border-green-200':'bg-red-50 border-red-200' ?> border rounded-2xl p-3">
        <div class="flex justify-between items-start mb-1">
            <span class="text-xs font-semibold text-slate-600"><?= $s['label'] ?></span>
            <span class="poin-badge <?= $s['poin']>0?'badge-success':'badge-danger' ?>"><?= $s['poin'] ?>/<?= $s['maks'] ?></span>
        </div>
        <div class="<?= $ok?'text-green-700':'text-red-600' ?> font-extrabold text-xl"><?= formatPersen($s['akt']) ?></div>
        <div class="text-xs text-slate-500">Target L3M: <?= formatPersen($s['max']) ?></div>
        <div class="progress-bar mt-1.5"><div class="progress-fill <?= $ok?'bg-green-500':'bg-red-400' ?>" style="width:<?= $s['max']>0?min(($s['akt']/$s['max'])*100,100):0 ?>%"></div></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Tabs -->
<div class="flex gap-2 mb-4 flex-wrap">
    <button onclick="stdTab('harian')" id="std-btn-harian" class="btn-primary text-xs">📅 Input Harian</button>
    <button onclick="stdTab('l3m')" id="std-btn-l3m" class="btn-secondary text-xs">📊 Setting L3M & Aktual</button>
    <button onclick="stdTab('rekap')" id="std-btn-rekap" class="btn-secondary text-xs">📋 Rekap Harian (<?= count($stdHarian) ?>)</button>
</div>

<!-- ── TAB: INPUT HARIAN ── -->
<div id="std-tab-harian">
    <!-- Kalender STD -->
    <div class="kpi-card p-4 mb-4">
        <div class="font-bold text-blue-900 text-sm mb-3">📅 Kalender – Hari dengan Data STD</div>
        <div id="std-calendar"></div>
        <p class="text-xs text-slate-400 mt-2">Klik tanggal merah untuk langsung scroll ke form input</p>
    </div>

    <div class="kpi-card p-5 mb-4">
        <h3 class="font-bold text-blue-900 mb-4">Input Data STD Harian</h3>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=std&action=save">
            <input type="hidden" name="save_type" value="harian">
            <input type="hidden" name="kode_toko" value="<?= $kode ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="input-field" max="<?= date('Y-m-d') ?>" style="max-width:200px">
            </div>
            <div class="space-y-4">
                <!-- Poinku -->
                <div class="bg-blue-50 rounded-xl p-3">
                    <div class="font-semibold text-sm text-blue-900 mb-2">a. STD Member Poinku</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><label class="block text-xs text-slate-500 mb-1">TRX Poinku (yg pakai)</label>
                            <input type="number" name="poinku_trx" value="0" class="input-field" min="0"></div>
                        <div><label class="block text-xs text-slate-500 mb-1">Total TRX Belanja</label>
                            <input type="number" name="poinku_total_trx" value="0" class="input-field" min="0"></div>
                    </div>
                </div>
                <!-- >2 item -->
                <div class="bg-purple-50 rounded-xl p-3">
                    <div class="font-semibold text-sm text-purple-900 mb-2">b. STD Belanja >2 Item</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><label class="block text-xs text-slate-500 mb-1">TRX >2 Item</label>
                            <input type="number" name="item2_trx" value="0" class="input-field" min="0"></div>
                        <div><label class="block text-xs text-slate-500 mb-1">Total TRX Belanja</label>
                            <input type="number" name="item2_total_trx" value="0" class="input-field" min="0"></div>
                    </div>
                </div>
                <!-- I-Payment -->
                <div class="bg-yellow-50 rounded-xl p-3">
                    <div class="font-semibold text-sm text-yellow-900 mb-2">c. TRX I-Payment (Fee Base)</div>
                    <div><label class="block text-xs text-slate-500 mb-1">Jumlah TRX I-Payment hari ini</label>
                        <input type="number" name="ipayment_trx" value="0" class="input-field" min="0"></div>
                </div>
                <!-- Non Tunai -->
                <div class="bg-green-50 rounded-xl p-3">
                    <div class="font-semibold text-sm text-green-900 mb-2">d. TRX Pembayaran Non Tunai</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><label class="block text-xs text-slate-500 mb-1">TRX Non Tunai</label>
                            <input type="number" name="nontunai_trx" value="0" class="input-field" min="0"></div>
                        <div><label class="block text-xs text-slate-500 mb-1">Total TRX Belanja</label>
                            <input type="number" name="nontunai_total_trx" value="0" class="input-field" min="0"></div>
                    </div>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                    <input type="text" name="catatan" class="input-field" placeholder="Opsional"></div>
            </div>
            <div class="mt-4"><button type="submit" class="btn-primary w-full">Simpan Data Harian</button></div>
        </form>
    </div>
    <?php if (!empty($stdHarian)): ?>
    <div class="kpi-card p-4">
        <div class="text-sm font-bold text-blue-900 mb-2">Akumulasi dari data harian</div>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="bg-blue-50 rounded-lg p-2">
                <div class="text-slate-500">Poinku TRX</div>
                <div class="font-bold"><?= $aktPoinku ?>/<?= $totalTrxPoinku ?> = <span class="text-blue-700"><?= formatPersen($pctPoinkuH) ?></span></div>
            </div>
            <div class="bg-purple-50 rounded-lg p-2">
                <div class="text-slate-500">>2 Item TRX</div>
                <div class="font-bold"><?= $aktItem2 ?>/<?= $totalTrxItem2 ?> = <span class="text-purple-700"><?= formatPersen($pctItem2H) ?></span></div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-2">
                <div class="text-slate-500">I-Payment TRX</div>
                <div class="font-bold text-yellow-700"><?= $aktIpay ?> TRX</div>
            </div>
            <div class="bg-green-50 rounded-lg p-2">
                <div class="text-slate-500">Non Tunai TRX</div>
                <div class="font-bold"><?= $aktNontunai ?>/<?= $totalTrxNontunai ?> = <span class="text-green-700"><?= formatPersen($pctNontunaiH) ?></span></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── TAB: L3M & AKTUAL ── -->
<div id="std-tab-l3m" style="display:none">
    <div class="kpi-card p-5">
        <h3 class="font-bold text-blue-900 mb-1">Setting L3M & Aktual Bulanan</h3>
        <p class="text-xs text-slate-500 mb-4">B-3 = <?= $bulanNama[$b3] ?>, B-2 = <?= $bulanNama[$b2] ?>, B-1 = <?= $bulanNama[$b1] ?></p>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=std&action=save">
            <input type="hidden" name="save_type" value="summary">
            <input type="hidden" name="kode_toko" value="<?= $kode ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">
            <?php
            $stdFields = [
                ['key'=>'poinku',   'label'=>'a. STD Member Poinku (%)','color'=>'bg-blue-50'],
                ['key'=>'2item',    'label'=>'b. STD Belanja >2 Item (%)','color'=>'bg-purple-50'],
                ['key'=>'ipayment', 'label'=>'c. TRX I-Payment (%)','color'=>'bg-yellow-50'],
                ['key'=>'nontunai', 'label'=>'d. TRX Non Tunai (%)','color'=>'bg-green-50'],
            ];
            $fieldMap = [
                'poinku'   => ['b3'=>'std_poinku_b3','b2'=>'std_poinku_b2','b1'=>'std_poinku_b1','akt'=>'aktual_std_poinku'],
                '2item'    => ['b3'=>'std_2item_b3','b2'=>'std_2item_b2','b1'=>'std_2item_b1','akt'=>'aktual_std_2item'],
                'ipayment' => ['b3'=>'trx_ipayment_b3','b2'=>'trx_ipayment_b2','b1'=>'trx_ipayment_b1','akt'=>'aktual_trx_ipayment'],
                'nontunai' => ['b3'=>'trx_nontunai_b3','b2'=>'trx_nontunai_b2','b1'=>'trx_nontunai_b1','akt'=>'aktual_trx_nontunai'],
            ];
            foreach ($stdFields as $f):
                $fm = $fieldMap[$f['key']];
                // Suggest from harian
                $suggest = 0;
                if ($f['key']==='poinku') $suggest=$pctPoinkuH;
                elseif ($f['key']==='2item') $suggest=$pctItem2H;
                elseif ($f['key']==='nontunai') $suggest=$pctNontunaiH;
            ?>
            <div class="<?= $f['color'] ?> rounded-xl p-3 mb-3">
                <div class="font-semibold text-sm text-slate-700 mb-2"><?= $f['label'] ?></div>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                    <div><label class="block text-xs text-slate-500 mb-1">B-3 (<?= $bulanNama[$b3] ?>)</label>
                        <input type="number" step="0.01" name="<?= $fm['b3'] ?>" value="<?= $std[$fm['b3']]??0 ?>" class="input-field text-center"></div>
                    <div><label class="block text-xs text-slate-500 mb-1">B-2 (<?= $bulanNama[$b2] ?>)</label>
                        <input type="number" step="0.01" name="<?= $fm['b2'] ?>" value="<?= $std[$fm['b2']]??0 ?>" class="input-field text-center"></div>
                    <div><label class="block text-xs text-slate-500 mb-1">B-1 (<?= $bulanNama[$b1] ?>)</label>
                        <input type="number" step="0.01" name="<?= $fm['b1'] ?>" value="<?= $std[$fm['b1']]??0 ?>" class="input-field text-center"></div>
                    <div><label class="block text-xs font-bold text-blue-700 mb-1">Aktual Bulan Ini</label>
                        <input type="number" step="0.01" name="<?= $fm['akt'] ?>" id="std-akt-<?= $f['key'] ?>" value="<?= $std[$fm['akt']]??0 ?>" class="input-field text-center border-blue-400">
                        <?php if ($suggest > 0): ?>
                        <button type="button" onclick="setSuggest('<?= $f['key'] ?>',<?= $suggest ?>)" class="text-xs text-blue-600 underline mt-1">Pakai harian: <?= formatPersen($suggest) ?></button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <div><label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                <input type="text" name="catatan" value="<?= htmlspecialchars($std['catatan']??'') ?>" class="input-field"></div>
            <div class="mt-4"><button type="submit" class="btn-primary w-full">Simpan Data STD</button></div>
        </form>
    </div>
</div>

<!-- ── TAB: REKAP HARIAN ── -->
<div id="std-tab-rekap" style="display:none">
    <div class="kpi-card overflow-hidden">
        <div class="px-4 py-3 bg-blue-700 text-white font-bold text-sm">Rekap Harian STD – <?= $bln ?> <?= $tahun ?></div>
        <?php if (empty($stdHarian)): ?>
        <div class="p-8 text-center text-slate-400 text-sm">Belum ada data harian dicatat.</div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full" style="font-size:.72rem">
                <thead>
                    <tr class="bg-blue-50 text-blue-800 font-bold">
                        <th class="px-2 py-2">Tgl</th>
                        <th class="px-2 py-2 text-center">Poinku<br>(%)</th>
                        <th class="px-2 py-2 text-center">>2Item<br>(%)</th>
                        <th class="px-2 py-2 text-center">I-Pay<br>(TRX)</th>
                        <th class="px-2 py-2 text-center">Non Tunai<br>(%)</th>
                        <th class="px-2 py-2 text-center">Hapus</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($stdHarian as $h):
                    $pPk = $h['poinku_total_trx']>0 ? round($h['poinku_trx']/$h['poinku_total_trx']*100,1):0;
                    $pI2 = $h['item2_total_trx']>0  ? round($h['item2_trx']/$h['item2_total_trx']*100,1):0;
                    $pNt = $h['nontunai_total_trx']>0? round($h['nontunai_trx']/$h['nontunai_total_trx']*100,1):0;
                ?>
                <tr class="border-t border-blue-50 hover:bg-blue-50/40">
                    <td class="px-2 py-1.5 font-semibold text-slate-700"><?= date('d/m',strtotime($h['tanggal'])) ?></td>
                    <td class="px-2 py-1.5 text-center text-blue-700 font-semibold"><?= $pPk ?>%</td>
                    <td class="px-2 py-1.5 text-center text-purple-700 font-semibold"><?= $pI2 ?>%</td>
                    <td class="px-2 py-1.5 text-center text-yellow-700 font-semibold"><?= $h['ipayment_trx'] ?></td>
                    <td class="px-2 py-1.5 text-center text-green-700 font-semibold"><?= $pNt ?>%</td>
                    <td class="px-2 py-1.5 text-center">
                        <form method="POST" action="<?= BASE_URL ?>/index.php?page=std&action=deleteHarian" onsubmit="return confirm('Hapus data ini?')">
                            <input type="hidden" name="id" value="<?= $h['id'] ?>">
                            <input type="hidden" name="kode_toko" value="<?= $kode ?>">
                            <input type="hidden" name="bulan" value="<?= $bulan ?>">
                            <input type="hidden" name="tahun" value="<?= $tahun ?>">
                            <button type="submit" class="text-red-400 hover:text-red-600">🗑</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function setSuggest(key, val) {
    var el = document.getElementById('std-akt-'+key);
    if(el) el.value = val.toFixed(2);
}
</script>

<script>
(function(){
    var filledDates = <?php echo json_encode(array_column($stdHarian, 'tanggal')); ?>;
    document.addEventListener('DOMContentLoaded', function(){
        stdTab('harian');
        KPI.renderCalendar('std-calendar', <?php echo $tahun ?>, <?php echo $bulan ?>, filledDates, function(d){
            document.querySelector('[name="tanggal"]').value = d;
            document.querySelector('[name="tanggal"]').scrollIntoView({behavior:'smooth'});
        });
        initNumberInputs();
    });
})();
</script>
<script>
var STD_TABS = ['harian','l3m','rekap'];
function stdTab(name) {
    STD_TABS.forEach(function(t) {
        var el  = document.getElementById('std-tab-'+t);
        var btn = document.getElementById('std-btn-'+t);
        if (el)  el.style.display = (t===name) ? 'block' : 'none';
        if (btn) {
            btn.classList.toggle('btn-primary',   t===name);
            btn.classList.toggle('btn-secondary', t!==name);
        }
    });
}
</script>