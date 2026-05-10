<?php
// views/nbr/index.php
$page     = 'nbr';
$bln      = getNamaBulan($bulan);
$nbrData  = $kpiData['nbr'];
$namaToko = $toko['nama_toko'] ?? '';
$isAdmin  = in_array($_SESSION['role'] ?? '', ['superadmin','admin']);
// Aggregate from harian for display
$totalNbrDryH = 0; $totalNbrKhususH = 0;
foreach ($nbrHarian as $h) {
    if ($h['jenis'] === 'dry') $totalNbrDryH += $h['nilai'];
    else $totalNbrKhususH += $h['nilai'];
}
?>

<?php if ($flash): ?>
<div id="flash-msg" class="fixed top-4 right-4 z-50 px-5 py-3 rounded-xl shadow-lg text-sm font-semibold
    <?= $flash['type']==='success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
    <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout/period_selector.php'; ?>

<!-- Header -->
<div class="flex items-center gap-3 mb-4">
    <a href="<?= BASE_URL ?>/index.php?page=dashboard&kode_toko=<?= $kode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
       class="text-blue-500 hover:text-blue-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="text-xl font-extrabold text-blue-900">Poin 3 – NBR (Nota Barang Rusak)</h1>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($namaToko) ?> &middot; <?= $bln ?> <?= $tahun ?></p>
    </div>
</div>

<!-- Panduan -->
<div class="info-box mb-4">
    <div class="font-bold mb-1.5">📖 Panduan Pengisian – NBR</div>
    <ul class="space-y-1 list-disc list-inside" style="font-size:.78rem">
        <li><strong>a. NBR Dry</strong> – Produk belum putus, telat retur, produk tag-N tidak ada fisik. Target ≤ 0,1% dari Sales Nett Dry</li>
        <li><strong>b. NBR Produk Khusus & Perishable</strong> – Proporsional per modul. Modul Main = nilai tetap sistem; Modul ACH = capaian aktual</li>
        <li>Catat setiap <strong>Nomor Nota NBR</strong> per hari untuk kemudahan monitoring dan audit</li>
        <li>Input harian: masukkan No. NBR, jenis produk, nama produk, dan nilainya</li>
        <li>Setelah semua nota dicatat, update total di tab <strong>Summary NBR</strong> untuk kalkulasi poin</li>
    </ul>
</div>

<!-- Poin Cards -->
<div class="grid grid-cols-3 gap-3 mb-4">
    <div class="kpi-card p-3 text-center">
        <div class="text-xs text-slate-400 mb-1">NBR Dry</div>
        <div class="font-extrabold text-lg <?= $nbrData['poin_dry']>0?'text-green-600':'text-red-500' ?>"><?= $nbrData['poin_dry'] ?>/<?= KPI_POINTS['nbr_dry'] ?></div>
        <div class="text-xs text-slate-500 mt-0.5"><?= formatPersen($nbrData['pct_dry'],3) ?></div>
    </div>
    <div class="kpi-card p-3 text-center">
        <div class="text-xs text-slate-400 mb-1">NBR Khusus</div>
        <div class="font-extrabold text-lg <?= $nbrData['poin_khusus']>0?'text-green-600':'text-red-500' ?>"><?= $nbrData['poin_khusus'] ?>/<?= KPI_POINTS['nbr_khusus'] ?></div>
        <div class="text-xs text-slate-500 mt-0.5"><?= $nbrData['modul_ach'] ?>/<?= $nbrData['modul_main'] ?> modul</div>
    </div>
    <div class="kpi-card p-3 text-center">
        <div class="text-xs text-slate-400 mb-1">Total Poin</div>
        <div class="font-extrabold text-xl text-blue-700"><?= $nbrData['poin'] ?>/<?= KPI_POINTS['nbr_total'] ?></div>
    </div>
</div>

<!-- Tabs -->
<div class="flex gap-2 mb-4 flex-wrap">
    <button onclick="showTab('tab-nbr-harian','nbr-')" id="nbr-btn-harian" class="btn-primary text-xs">📋 Catat Nota Harian</button>
    <button onclick="showTab('tab-nbr-summary','nbr-')" id="nbr-btn-summary" class="btn-secondary text-xs">⚙️ Summary & Poin</button>
    <button onclick="showTab('tab-nbr-list','nbr-')" id="nbr-btn-list" class="btn-secondary text-xs">📑 Daftar Nota (<?= count($nbrHarian) ?>)</button>
</div>

<!-- ── TAB: CATAT NOTA HARIAN ── -->
<div id="tab-nbr-harian">
    <div class="kpi-card p-5 mb-4">
        <h3 class="font-bold text-blue-900 mb-4">Tambah Nota NBR</h3>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=nbr&action=save">
            <input type="hidden" name="save_type" value="harian">
            <input type="hidden" name="kode_toko" value="<?= $kode ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="input-field" max="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Nota NBR <span class="text-red-500">*</span></label>
                    <input type="text" name="no_nbr" required class="input-field" placeholder="Contoh: NBR-2026050901">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Produk</label>
                    <select name="jenis" class="input-field">
                        <option value="dry">Dry</option>
                        <option value="khusus">Produk Khusus</option>
                        <option value="perishable">Perishable</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Produk</label>
                    <input type="text" name="nama_produk" class="input-field" placeholder="Nama produk yang rusak">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nilai NBR (Rp)</label>
                    <input type="text" name="nilai" value="0" class="input-field" oninput="formatNumber(this)">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                    <input type="text" name="catatan" class="input-field" placeholder="Keterangan tambahan">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn-primary w-full">+ Tambah Nota NBR</button>
            </div>
        </form>
    </div>
    <!-- Quick summary harian -->
    <?php if (!empty($nbrHarian)): ?>
    <div class="kpi-card p-4">
        <div class="text-sm font-bold text-blue-900 mb-2">Ringkasan Nota Bulan Ini</div>
        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="bg-blue-50 rounded-xl p-3">
                <div class="text-xs text-slate-400">Total Nota</div>
                <div class="font-bold text-blue-700 text-lg"><?= count($nbrHarian) ?></div>
            </div>
            <div class="bg-red-50 rounded-xl p-3">
                <div class="text-xs text-slate-400">Nilai Dry</div>
                <div class="font-bold text-red-700 text-sm"><?= formatRupiah($totalNbrDryH,true) ?></div>
            </div>
            <div class="bg-orange-50 rounded-xl p-3">
                <div class="text-xs text-slate-400">Nilai Khusus</div>
                <div class="font-bold text-orange-700 text-sm"><?= formatRupiah($totalNbrKhususH,true) ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── TAB: SUMMARY & POIN ── -->
<div id="tab-nbr-summary" class="hidden">
    <!-- Visual kalkulasi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
        <div class="<?= $nbrData['pct_dry']<=0.1?'bg-green-50 border-green-200':'bg-red-50 border-red-200' ?> border rounded-2xl p-4">
            <div class="flex justify-between mb-2">
                <span class="font-bold text-sm text-slate-700">a. NBR Dry</span>
                <span class="poin-badge <?= $nbrData['poin_dry']>0?'badge-success':'badge-danger' ?>"><?= $nbrData['poin_dry'] ?>/<?= KPI_POINTS['nbr_dry'] ?></span>
            </div>
            <div class="<?= $nbrData['pct_dry']<=0.1?'text-green-600':'text-red-600' ?> font-bold text-2xl"><?= formatPersen($nbrData['pct_dry'],3) ?></div>
            <div class="text-xs text-slate-500 mt-1">Aktual: <?= formatRupiah($nbrData['nbr_dry'],true) ?></div>
            <div class="text-xs text-slate-500">Sales Nett Dry: <?= formatRupiah($nbrData['sales_dry'],true) ?></div>
            <div class="progress-bar mt-2"><div class="progress-fill <?= $nbrData['pct_dry']<=0.1?'bg-green-500':'bg-red-400' ?>" style="width:<?= min($nbrData['pct_dry']*100/0.1,100) ?>%"></div></div>
            <div class="text-xs mt-1 font-bold <?= $nbrData['pct_dry']<=0.1?'text-green-600':'text-red-600' ?>"><?= $nbrData['pct_dry']<=0.1?'✅ OK – di bawah 0,1%':'❌ OVER – melebihi 0,1%' ?></div>
        </div>
        <div class="<?= $nbrData['poin_khusus']>0?'bg-green-50 border-green-200':'bg-red-50 border-red-200' ?> border rounded-2xl p-4">
            <div class="flex justify-between mb-2">
                <span class="font-bold text-sm text-slate-700">b. Produk Khusus & Perishable</span>
                <span class="poin-badge <?= $nbrData['poin_khusus']>0?'badge-success':'badge-danger' ?>"><?= $nbrData['poin_khusus'] ?>/<?= KPI_POINTS['nbr_khusus'] ?></span>
            </div>
            <?php if ($nbrData['modul_main']>0): ?>
            <div class="flex items-center gap-3">
                <div class="text-center"><div class="font-extrabold text-2xl text-blue-700"><?= $nbrData['modul_ach'] ?></div><div class="text-xs text-slate-400">ACH</div></div>
                <div class="text-slate-300 text-xl">/</div>
                <div class="text-center"><div class="font-extrabold text-2xl text-slate-700"><?= $nbrData['modul_main'] ?></div><div class="text-xs text-slate-400">Main</div></div>
                <div class="flex-1"><div class="progress-bar"><div class="progress-fill <?= $nbrData['poin_khusus']>0?'bg-green-500':'bg-red-400' ?>" style="width:<?= $nbrData['modul_main']>0?min(($nbrData['modul_ach']/$nbrData['modul_main'])*100,100):0 ?>%"></div></div></div>
            </div>
            <?php else: ?><div class="text-xs text-slate-400 mt-2">Belum ada data modul</div><?php endif; ?>
        </div>
    </div>
    <!-- Input Summary Form -->
    <div class="kpi-card p-5">
        <h3 class="font-bold text-blue-900 mb-4">Update Summary NBR Bulanan</h3>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=nbr&action=save">
            <input type="hidden" name="save_type" value="summary">
            <input type="hidden" name="kode_toko" value="<?= $kode ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sales Nett Dry (Rp)</label>
                    <input type="text" name="sales_nett_dry" value="<?= formatAngka($nbr['sales_nett_dry']??0) ?>" class="input-field" oninput="formatNumber(this);hitungNbr()">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Total Nilai NBR Dry (Rp)</label>
                    <input type="text" name="aktual_nbr_dry" id="nbr_dry_val" value="<?= formatAngka($nbr['aktual_nbr_dry']??0) ?>" class="input-field" oninput="formatNumber(this);hitungNbr()">
                    <p class="text-xs text-blue-600 mt-1">💡 Bisa diisi otomatis dari total nota harian: <strong><?= formatRupiah($totalNbrDryH,true) ?></strong>
                        <button type="button" onclick="setDryFromHarian()" class="underline text-blue-700 ml-1">Pakai nilai ini</button>
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Modul Main (nilai tetap sistem)</label>
                    <input type="number" name="modul_main" value="<?= $nbr['modul_main']??0 ?>" class="input-field" min="0">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Modul ACH (capaian aktual)</label>
                    <input type="number" name="modul_ach" value="<?= $nbr['modul_ach']??0 ?>" class="input-field" min="0">
                </div>
                <div class="sm:col-span-2">
                    <div id="nbr-preview" class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-800">
                        Persentase NBR Dry: <span id="nbr-pct" class="font-bold">0.000%</span>
                        <span id="nbr-status" class="ml-2 font-bold text-green-600">✅ OK</span>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                    <input type="text" name="catatan" value="<?= htmlspecialchars($nbr['catatan']??'') ?>" class="input-field">
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn-primary w-full">Simpan Summary NBR</button></div>
        </form>
    </div>
</div>

<!-- ── TAB: DAFTAR NOTA ── -->
<div id="tab-nbr-list" class="hidden">
    <div class="kpi-card overflow-hidden">
        <div class="px-4 py-3 bg-blue-700 text-white font-bold text-sm flex justify-between items-center">
            <span>Daftar Nota NBR – <?= $bln ?> <?= $tahun ?></span>
            <span class="text-blue-200 text-xs"><?= count($nbrHarian) ?> nota</span>
        </div>
        <?php if (empty($nbrHarian)): ?>
        <div class="p-8 text-center text-slate-400 text-sm">Belum ada nota NBR dicatat bulan ini.</div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full" style="font-size:.75rem">
                <thead>
                    <tr class="bg-blue-50 text-blue-800 font-bold">
                        <th class="px-3 py-2 text-left">Tgl</th>
                        <th class="px-3 py-2 text-left">No. NBR</th>
                        <th class="px-3 py-2 text-left">Jenis</th>
                        <th class="px-3 py-2 text-left">Produk</th>
                        <th class="px-3 py-2 text-right">Nilai</th>
                        <th class="px-3 py-2 text-center">Hapus</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $grandTotal = 0;
                foreach ($nbrHarian as $h):
                    $grandTotal += $h['nilai'];
                    $jenisColor = $h['jenis']==='dry'?'bg-blue-100 text-blue-700':($h['jenis']==='khusus'?'bg-purple-100 text-purple-700':'bg-orange-100 text-orange-700');
                ?>
                <tr class="border-t border-blue-50 hover:bg-blue-50/40">
                    <td class="px-3 py-2 font-semibold text-slate-700"><?= date('d/m',strtotime($h['tanggal'])) ?></td>
                    <td class="px-3 py-2 font-mono text-blue-700 font-bold"><?= htmlspecialchars($h['no_nbr']) ?></td>
                    <td class="px-3 py-2"><span class="poin-badge <?= $jenisColor ?>"><?= ucfirst($h['jenis']) ?></span></td>
                    <td class="px-3 py-2 text-slate-600 max-w-[120px] truncate"><?= htmlspecialchars($h['nama_produk'] ?? '-') ?></td>
                    <td class="px-3 py-2 text-right font-semibold text-red-700"><?= formatRupiah($h['nilai'],true) ?></td>
                    <td class="px-3 py-2 text-center">
                        <form method="POST" action="<?= BASE_URL ?>/index.php?page=nbr&action=delete" onsubmit="return confirm('Hapus nota ini?')">
                            <input type="hidden" name="id" value="<?= $h['id'] ?>">
                            <input type="hidden" name="kode_toko" value="<?= $kode ?>">
                            <input type="hidden" name="bulan" value="<?= $bulan ?>">
                            <input type="hidden" name="tahun" value="<?= $tahun ?>">
                            <button type="submit" class="text-red-400 hover:text-red-600 text-base">🗑</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-red-50 font-bold border-t-2 border-red-200">
                        <td class="px-3 py-2.5 text-red-800" colspan="4">TOTAL NILAI NBR</td>
                        <td class="px-3 py-2.5 text-right text-red-800"><?= formatRupiah($grandTotal) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function hitungNbr() {
    var sales = getRaw(document.querySelector('[name="sales_nett_dry"]')?.value||'0');
    var nbr   = getRaw(document.getElementById('nbr_dry_val')?.value||'0');
    var pct   = sales>0?(nbr/sales*100):0;
    var el=document.getElementById('nbr-pct'); if(el) el.textContent=pct.toFixed(3)+'%';
    var st=document.getElementById('nbr-status');
    if(st){st.textContent=pct<=0.1?'✅ Di bawah 0,1%':'❌ Melebihi 0,1%'; st.className='ml-2 font-bold '+(pct<=0.1?'text-green-600':'text-red-600');}
}
function setDryFromHarian() {
    var el = document.getElementById('nbr_dry_val');
    if(el){ el.value='<?= number_format($totalNbrDryH,0,",",".") ?>'; hitungNbr(); }
}
hitungNbr();
</script>
