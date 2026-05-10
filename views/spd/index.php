<?php
// views/spd/index.php
$page    = 'spd';
$bln     = getNamaBulan($bulan);
$spd     = $kpiData['spd'];
$hariTot = $kpiData['hari_total'];
$hariBjl = $kpiData['hari_berjalan'];
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
    <a href="<?= BASE_URL ?>/index.php?page=dashboard&kode_toko=<?= $kode_toko ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="text-blue-500 hover:text-blue-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="text-xl font-extrabold text-blue-900">Poin 1 – SPD Sales</h1>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($namaToko) ?> &middot; <?= $bln ?> <?= $tahun ?></p>
    </div>
</div>

<!-- Panduan -->
<div class="info-box mb-4">
    <div class="font-bold mb-1.5 text-blue-800">📖 Panduan Pengisian – SPD Sales</div>
    <ul class="space-y-1 list-disc list-inside text-blue-700" style="font-size:.78rem">
        <li><strong>a. SPD Offline</strong> – Sales semua produk yang belanja di toko Indomaret (transaksi kasir)</li>
        <li><strong>b. SPD Online (Klik)</strong> – Sales pelanggan yang berbelanja via aplikasi Klik Indomaret</li>
        <li><strong>e. Produk Khusus</strong> – Sales produk khusus sesuai item/modul main di toko (lihat POS Main → Laporan Performance)</li>
        <li>Data dapat dipantau <strong>setiap hari</strong> di POS Main menu Laporan Performance</li>
        <li>Input aktual per tanggal. Target proporsional dihitung otomatis: <em>(Target Bulan / Hari Target) × Hari Berjalan</em></li>
        <li>ACH ≥ 100% = poin penuh | 95–99% = poin proporsional | &lt;95% = poin 0</li>
    </ul>
</div>

<!-- Summary -->
<div class="kpi-card p-4 mb-5">
    <div class="grid grid-cols-3 gap-3 text-center">
        <div>
            <div class="text-xs text-slate-400 mb-1">Total Aktual</div>
            <div class="font-extrabold text-blue-700"><?= formatRupiah($spd['aktual_berjalan'], true) ?></div>
        </div>
        <div>
            <div class="text-xs text-slate-400 mb-1">Target Prop.</div>
            <div class="font-extrabold text-slate-700"><?= formatRupiah($spd['target_prop'], true) ?></div>
        </div>
        <div>
            <div class="text-xs text-slate-400 mb-1">Achievement</div>
            <div class="font-extrabold <?= getColorClass($spd['ach_total']) ?>"><?= formatPersen($spd['ach_total']) ?></div>
        </div>
    </div>
    <div class="mt-3 progress-bar">
        <div class="progress-fill <?= $spd['ach_total'] >= 100 ? 'bg-green-500' : ($spd['ach_total'] >= 95 ? 'bg-yellow-400' : 'bg-red-400') ?>"
            style="width:<?= min($spd['ach_total'],100) ?>%"></div>
    </div>
    <div class="text-xs text-slate-400 text-right mt-1">Hari ke-<?= $hariBjl ?> dari <?= $hariTot ?> hari</div>
</div>

<!-- Tabs -->
<div class="flex gap-2 mb-4" id="tab-btns">
    <button onclick="showTab('tab-target')" id="btn-target" class="tab-btn btn-primary text-xs">⚙️ Input Target</button>
    <button onclick="showTab('tab-aktual')" id="btn-aktual" class="tab-btn btn-secondary text-xs">📥 Input Aktual Harian</button>
    <button onclick="showTab('tab-rincian')" id="btn-rincian" class="tab-btn btn-secondary text-xs">📋 Rincian Harian</button>
</div>

<!-- Tab: Input Target -->
<div id="tab-target" class="kpi-card p-5 mb-4">
    <h3 class="font-bold text-blue-900 mb-4">Setting Target SPD</h3>
    <form method="POST" action="<?= BASE_URL ?>/index.php?page=spd&action=save">
        <input type="hidden" name="save_type" value="target">
        <input type="hidden" name="kode_toko" value="<?= $kode_toko ?>">
        <input type="hidden" name="bulan" value="<?= $bulan ?>">
        <input type="hidden" name="tahun" value="<?= $tahun ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Hari Target</label>
                <input type="number" name="tgt_hari" value="<?= $target['tgt_hari'] ?? 28 ?>" class="input-field" min="1" max="31">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Target SPD Offline</label>
                <input type="text" name="target_offline" value="<?= formatAngka($target['target_offline'] ?? 0) ?>" class="input-field" oninput="formatNumber(this)">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Target SPD Online (Klik)</label>
                <input type="text" name="target_online" value="<?= formatAngka($target['target_online'] ?? 0) ?>" class="input-field" oninput="formatNumber(this)">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Target Produk Khusus (opsional)</label>
                <input type="text" name="target_khusus" value="<?= formatAngka($target['target_produk_khusus'] ?? 0) ?>" class="input-field" oninput="formatNumber(this)">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Target SPD Dry</label>
                <input type="text" name="target_dry" value="<?= formatAngka($target['target_dry'] ?? 0) ?>" class="input-field" oninput="formatNumber(this)">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Target SPD Perishable</label>
                <input type="text" name="target_perishable" value="<?= formatAngka($target['target_perishable'] ?? 0) ?>" class="input-field" oninput="formatNumber(this)">
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn-primary w-full">Simpan Target</button>
        </div>
    </form>
</div>

<!-- Tab: Input Aktual Harian -->
<div id="tab-aktual" class="kpi-card p-5 mb-4 hidden">
    <h3 class="font-bold text-blue-900 mb-4">Input Aktual SPD Harian</h3>
    <form method="POST" action="<?= BASE_URL ?>/index.php?page=spd&action=save">
        <input type="hidden" name="save_type" value="aktual">
        <input type="hidden" name="kode_toko" value="<?= $kode_toko ?>">
        <input type="hidden" name="bulan" value="<?= $bulan ?>">
        <input type="hidden" name="tahun" value="<?= $tahun ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="input-field" max="<?= date('Y-m-d') ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Aktual Offline</label>
                <input type="text" name="aktual_offline" value="0" class="input-field" oninput="formatNumber(this)">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Aktual Online (Klik)</label>
                <input type="text" name="aktual_online" value="0" class="input-field" oninput="formatNumber(this)">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Aktual Produk Khusus</label>
                <input type="text" name="aktual_khusus" value="0" class="input-field" oninput="formatNumber(this)">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Aktual Dry</label>
                <input type="text" name="aktual_dry" value="0" class="input-field" oninput="formatNumber(this)">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Aktual Perishable</label>
                <input type="text" name="aktual_perishable" value="0" class="input-field" oninput="formatNumber(this)">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                <input type="text" name="catatan" class="input-field" placeholder="Opsional">
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn-primary w-full">Simpan Aktual</button>
        </div>
    </form>
</div>

<!-- Tab: Rincian Harian -->
<div id="tab-rincian" class="hidden">
    <div class="kpi-card overflow-hidden">
        <div class="px-5 py-4 border-b border-blue-50">
            <h3 class="font-bold text-blue-900">Rincian Data Harian – <?= $bln ?> <?= $tahun ?></h3>
        </div>
        <?php if (empty($harian)): ?>
        <div class="p-8 text-center text-slate-400 text-sm">Belum ada data aktual untuk bulan ini.</div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-blue-50">
                        <th class="px-3 py-2.5 text-left font-bold text-blue-800">Tgl</th>
                        <th class="px-3 py-2.5 text-right font-bold text-blue-800">Offline</th>
                        <th class="px-3 py-2.5 text-right font-bold text-blue-800">Online</th>
                        <th class="px-3 py-2.5 text-right font-bold text-blue-800">Khusus</th>
                        <th class="px-3 py-2.5 text-right font-bold text-blue-800">Total</th>
                        <th class="px-3 py-2.5 text-center font-bold text-blue-800">Hapus</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($harian as $h):
                    $tot = $h['aktual_offline'] + $h['aktual_online'];
                ?>
                <tr class="border-t border-blue-50 hover:bg-blue-50/50">
                    <td class="px-3 py-2 font-semibold text-slate-700"><?= date('d/m', strtotime($h['tanggal'])) ?></td>
                    <td class="px-3 py-2 text-right text-slate-600"><?= formatRupiah($h['aktual_offline'], true) ?></td>
                    <td class="px-3 py-2 text-right text-slate-600"><?= formatRupiah($h['aktual_online'], true) ?></td>
                    <td class="px-3 py-2 text-right text-slate-600"><?= formatRupiah($h['aktual_produk_khusus'], true) ?></td>
                    <td class="px-3 py-2 text-right font-bold text-blue-700"><?= formatRupiah($tot, true) ?></td>
                    <td class="px-3 py-2 text-center">
                        <form method="POST" action="<?= BASE_URL ?>/index.php?page=spd&action=delete" onsubmit="return confirm('Hapus data ini?')">
                            <input type="hidden" name="id" value="<?= $h['id'] ?>">
                            <input type="hidden" name="kode_toko" value="<?= $kode_toko ?>">
                            <input type="hidden" name="bulan" value="<?= $bulan ?>">
                            <input type="hidden" name="tahun" value="<?= $tahun ?>">
                            <button type="submit" class="text-red-400 hover:text-red-600">🗑</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-blue-100 font-bold">
                        <td class="px-3 py-2.5 text-blue-800">TOTAL</td>
                        <td class="px-3 py-2.5 text-right text-blue-800"><?= formatRupiah($spd['aktual_offline'], true) ?></td>
                        <td class="px-3 py-2.5 text-right text-blue-800"><?= formatRupiah($spd['aktual_online'], true) ?></td>
                        <td class="px-3 py-2.5 text-right text-blue-800"><?= formatRupiah($spd['aktual_khusus'], true) ?></td>
                        <td class="px-3 py-2.5 text-right text-blue-800"><?= formatRupiah($spd['aktual_berjalan'], true) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showTab(id) {
    ['tab-target','tab-aktual','tab-rincian'].forEach(t => {
        document.getElementById(t).classList.add('hidden');
    });
    ['btn-target','btn-aktual','btn-rincian'].forEach(b => {
        document.getElementById(b).className = 'tab-btn btn-secondary text-xs';
    });
    document.getElementById(id).classList.remove('hidden');
    const btnId = 'btn-' + id.replace('tab-','');
    document.getElementById(btnId).className = 'tab-btn btn-primary text-xs';
}
</script>
