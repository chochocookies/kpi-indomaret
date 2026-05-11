<?php
// views/spd/index.php
$page     = 'spd';
$bln      = getNamaBulan($bulan);
$spd      = $kpiData['spd'];
$hariTot  = $kpiData['hari_total'];
$hariBjl  = $kpiData['hari_berjalan'];
$namaToko = $toko['nama_toko'] ?? '';
$filledDates = array_column($harian, 'tanggal');
$harianMap   = [];
foreach ($harian as $h) $harianMap[$h['tanggal']] = $h;
?>

<?php include __DIR__ . '/../layout/flash.php'; ?>
<?php include __DIR__ . '/../layout/period_selector.php'; ?>

<!-- Header -->
<div class="flex items-center gap-3 mb-4">
    <a href="<?= BASE_URL ?>/index.php?page=dashboard&kode_toko=<?= $kode_toko ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
       class="text-blue-500 hover:text-blue-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h1 class="text-xl font-extrabold text-blue-900">Poin 1 – SPD Sales</h1>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($namaToko) ?> · <?= $bln ?> <?= $tahun ?></p>
    </div>
</div>

<!-- Panduan -->
<div class="info-box mb-4">
    <div class="font-bold mb-1">📖 Cara Pengisian SPD</div>
    <ul class="list-disc list-inside space-y-0.5" style="font-size:.78rem">
        <li>Lihat data di <strong>POS Main → Laporan Performance</strong> setiap hari</li>
        <li>Klik tanggal di kalender → isi nilai Sales Offline, Online, dan Produk Khusus</li>
        <li>Tanggal <span style="background:#22c55e;color:#fff;border-radius:4px;padding:1px 5px;font-size:.65rem">✓</span> hijau = sudah diisi &nbsp;|&nbsp; <span style="background:#fee2e2;border:1px solid #fca5a5;border-radius:4px;padding:1px 5px;font-size:.65rem;color:#dc2626">merah</span> = belum</li>
    </ul>
</div>

<!-- Summary bar -->
<div class="kpi-card p-4 mb-4">
    <div class="grid grid-cols-3 gap-3 text-center mb-3">
        <div>
            <div class="text-xs text-slate-400 mb-1">Aktual Berjalan</div>
            <div class="font-extrabold text-blue-700 text-base"><?= formatRupiah($spd['aktual_berjalan'], true) ?></div>
        </div>
        <div>
            <div class="text-xs text-slate-400 mb-1">Target Proporsional</div>
            <div class="font-extrabold text-slate-700 text-base"><?= formatRupiah($spd['target_prop'], true) ?></div>
        </div>
        <div>
            <div class="text-xs text-slate-400 mb-1">Achievement</div>
            <div class="font-extrabold text-base <?= getColorClass($spd['ach_total']) ?>"><?= formatPersen($spd['ach_total']) ?></div>
        </div>
    </div>
    <div class="progress-bar">
        <div class="progress-fill <?= $spd['ach_total']>=100?'bg-green-500':($spd['ach_total']>=95?'bg-yellow-400':'bg-red-400') ?>"
             style="width:<?= min($spd['ach_total'],100) ?>%"></div>
    </div>
    <div class="text-xs text-right text-slate-400 mt-1">Hari ke-<?= $hariBjl ?> dari <?= $hariTot ?></div>
</div>

<!-- Tab buttons -->
<div class="flex gap-2 mb-4 flex-wrap">
    <button onclick="spdTab('kalender')" id="spd-btn-kalender"
            class="btn-primary text-xs flex items-center gap-1.5">📅 Input Harian</button>
    <button onclick="spdTab('target')" id="spd-btn-target"
            class="btn-secondary text-xs flex items-center gap-1.5">⚙️ Setting Target</button>
    <button onclick="spdTab('rincian')" id="spd-btn-rincian"
            class="btn-secondary text-xs flex items-center gap-1.5">📋 Rincian (<?= count($harian) ?> hari)</button>
</div>

<!-- ════ TAB KALENDER ════ -->
<div id="spd-kalender">
    <div class="kpi-card p-4 mb-4">
        <div class="font-bold text-blue-900 text-sm mb-3">📅 Kalender <?= $bln ?> <?= $tahun ?></div>
        <div id="spd-calendar"></div>
    </div>

    <!-- Form input harian -->
    <div id="spd-input-box" class="kpi-card p-5 mb-4" style="display:none">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="font-bold text-blue-900" id="spd-input-title">Input Aktual</div>
                <div class="text-xs text-slate-400" id="spd-input-subtitle"></div>
            </div>
            <button type="button" onclick="document.getElementById('spd-input-box').style.display='none'"
                    class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=spd&action=save">
            <input type="hidden" name="save_type" value="aktual">
            <input type="hidden" name="kode_toko" value="<?= $kode_toko ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">
            <input type="hidden" name="tanggal" id="spd-tanggal-input">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">💼 Sales Offline (Rp)</label>
                    <input type="text" name="aktual_offline" id="spd-off" value="0"
                           class="input-field num-input text-right text-lg font-bold">
                    <div class="text-xs text-blue-500 mt-0.5">Target/hari: <span id="spd-tgt-off-day"></span></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">📱 Sales Online / Klik (Rp)</label>
                    <input type="text" name="aktual_online" id="spd-on" value="0"
                           class="input-field num-input text-right text-lg font-bold">
                    <div class="text-xs text-blue-500 mt-0.5">Target/hari: <span id="spd-tgt-on-day"></span></div>
                </div>
                <?php if ($spd['ada_khusus']): ?>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">⭐ Sales Produk Khusus (Rp)</label>
                    <input type="text" name="aktual_khusus" id="spd-khs" value="0"
                           class="input-field num-input text-right text-lg font-bold">
                    <div class="text-xs text-blue-500 mt-0.5">Target/hari: <span id="spd-tgt-khs-day"></span></div>
                </div>
                <?php else: ?>
                <input type="hidden" name="aktual_khusus" value="0">
                <?php endif; ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">🔵 Sales Dry (Rp)</label>
                    <input type="text" name="aktual_dry" id="spd-dry" value="0"
                           class="input-field num-input text-right">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">🧊 Perishable (Rp)</label>
                    <input type="text" name="aktual_perishable" id="spd-per" value="0"
                           class="input-field num-input text-right">
                </div>
                <div class="sm:col-span-2">
                    <div class="bg-blue-50 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-xs text-blue-700 font-semibold">Total Offline + Online:</span>
                        <span class="font-extrabold text-blue-800" id="spd-total-preview">Rp 0</span>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                    <input type="text" name="catatan" id="spd-cat" class="input-field" placeholder="Opsional">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4">
                <button type="button"
                        onclick="document.getElementById('spd-input-box').style.display='none'"
                        class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">💾 Simpan</button>
            </div>
        </form>
    </div>
</div><!-- end tab kalender -->

<!-- ════ TAB TARGET ════ -->
<div id="spd-target" style="display:none">
    <div class="kpi-card p-5 mb-4">
        <div class="font-bold text-blue-900 mb-1">⚙️ Setting Target SPD – <?= $bln ?> <?= $tahun ?></div>
        <p class="text-xs text-slate-500 mb-4">Masukkan total target <strong>seluruh bulan</strong> — target per hari dihitung otomatis</p>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=spd&action=save">
            <input type="hidden" name="save_type" value="target">
            <input type="hidden" name="kode_toko" value="<?= $kode_toko ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Hari Target Bulan Ini</label>
                    <input type="number" name="tgt_hari" id="tgt-hari"
                           value="<?= $target['tgt_hari'] ?? $hariTot ?>"
                           class="input-field int-input text-center" min="1" max="31"
                           oninput="updateTgtPreview()" style="max-width:100px">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">💼 Target SPD Offline (Total Bulan)</label>
                    <input type="text" name="target_offline" id="tgt-off"
                           value="<?= formatAngka($target['target_offline'] ?? 0) ?>"
                           class="input-field num-input" oninput="updateTgtPreview()">
                    <div class="text-xs text-blue-500 mt-0.5">Per hari: <strong id="tgt-off-d">-</strong></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">📱 Target SPD Online (Total Bulan)</label>
                    <input type="text" name="target_online" id="tgt-on"
                           value="<?= formatAngka($target['target_online'] ?? 0) ?>"
                           class="input-field num-input" oninput="updateTgtPreview()">
                    <div class="text-xs text-blue-500 mt-0.5">Per hari: <strong id="tgt-on-d">-</strong></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">⭐ Target Produk Khusus (jika ada)</label>
                    <input type="text" name="target_khusus" id="tgt-khs"
                           value="<?= formatAngka($target['target_produk_khusus'] ?? 0) ?>"
                           class="input-field num-input" oninput="updateTgtPreview()">
                    <div class="text-xs text-blue-500 mt-0.5">Per hari: <strong id="tgt-khs-d">-</strong></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">🔵 Target Dry</label>
                    <input type="text" name="target_dry"
                           value="<?= formatAngka($target['target_dry'] ?? 0) ?>"
                           class="input-field num-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">🧊 Target Perishable</label>
                    <input type="text" name="target_perishable"
                           value="<?= formatAngka($target['target_perishable'] ?? 0) ?>"
                           class="input-field num-input">
                </div>
                <div class="sm:col-span-2">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs">
                        <div class="font-bold text-blue-800 mb-1">📊 Preview Target Per Hari</div>
                        <div class="grid grid-cols-2 gap-1 text-blue-700">
                            <span>Offline/hari: <strong id="tgt-prev-off">-</strong></span>
                            <span>Online/hari: <strong id="tgt-prev-on">-</strong></span>
                            <span>Total/hari: <strong id="tgt-prev-tot">-</strong></span>
                            <span>Khusus/hari: <strong id="tgt-prev-khs">-</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn-primary w-full">💾 Simpan Target</button>
            </div>
        </form>
    </div>
</div><!-- end tab target -->

<!-- ════ TAB RINCIAN ════ -->
<div id="spd-rincian" style="display:none">
    <div class="kpi-card overflow-hidden">
        <div class="px-4 py-3 bg-blue-700 text-white font-bold text-sm flex justify-between">
            <span>Rincian Harian – <?= $bln ?> <?= $tahun ?></span>
            <span class="text-blue-200 text-xs"><?= count($harian) ?>/<?= $hariTot ?> hari</span>
        </div>
        <?php if (empty($harian)): ?>
        <div class="p-8 text-center text-slate-400 text-sm">
            Belum ada data. Klik tanggal di kalender untuk mulai mengisi.
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full" style="font-size:.75rem">
                <thead>
                    <tr class="bg-blue-50 text-blue-800 font-bold">
                        <th class="px-3 py-2 text-left">Tgl</th>
                        <th class="px-3 py-2 text-right">Offline</th>
                        <th class="px-3 py-2 text-right">Online</th>
                        <?php if ($spd['ada_khusus']): ?><th class="px-3 py-2 text-right">Khusus</th><?php endif; ?>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($harian as $h):
                    $tot = $h['aktual_offline'] + $h['aktual_online'];
                ?>
                <tr class="border-t border-blue-50 hover:bg-blue-50/40">
                    <td class="px-3 py-2 font-semibold text-slate-700"><?= date('d/m', strtotime($h['tanggal'])) ?></td>
                    <td class="px-3 py-2 text-right"><?= formatRupiah($h['aktual_offline'], true) ?></td>
                    <td class="px-3 py-2 text-right"><?= formatRupiah($h['aktual_online'], true) ?></td>
                    <?php if ($spd['ada_khusus']): ?>
                    <td class="px-3 py-2 text-right"><?= formatRupiah($h['aktual_produk_khusus'], true) ?></td>
                    <?php endif; ?>
                    <td class="px-3 py-2 text-right font-bold text-blue-700"><?= formatRupiah($tot, true) ?></td>
                    <td class="px-3 py-2 text-center">
                        <button type="button"
                                onclick="spdEditRow('<?= $h['tanggal'] ?>',<?= $h['aktual_offline'] ?>,<?= $h['aktual_online'] ?>,<?= $h['aktual_produk_khusus'] ?>,<?= $h['aktual_dry'] ?>,<?= $h['aktual_perishable'] ?>,'<?= htmlspecialchars($h['catatan'] ?? '') ?>')"
                                class="text-blue-500 hover:text-blue-700 mr-1" title="Edit">✏️</button>
                        <form method="POST" action="<?= BASE_URL ?>/index.php?page=spd&action=delete"
                              style="display:inline"
                              onsubmit="return confirm('Hapus data tanggal ini?')">
                            <input type="hidden" name="id" value="<?= $h['id'] ?>">
                            <input type="hidden" name="kode_toko" value="<?= $kode_toko ?>">
                            <input type="hidden" name="bulan" value="<?= $bulan ?>">
                            <input type="hidden" name="tahun" value="<?= $tahun ?>">
                            <button type="submit" class="text-red-400 hover:text-red-600" title="Hapus">🗑</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-blue-100 font-bold text-blue-800">
                        <td class="px-3 py-2">TOTAL</td>
                        <td class="px-3 py-2 text-right"><?= formatRupiah($spd['aktual_offline'], true) ?></td>
                        <td class="px-3 py-2 text-right"><?= formatRupiah($spd['aktual_online'], true) ?></td>
                        <?php if ($spd['ada_khusus']): ?>
                        <td class="px-3 py-2 text-right"><?= formatRupiah($spd['aktual_khusus'], true) ?></td>
                        <?php endif; ?>
                        <td class="px-3 py-2 text-right"><?= formatRupiah($spd['aktual_berjalan'], true) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div><!-- end tab rincian -->

<script>
// ── Tab data ──
var SPD_FILLED   = <?= json_encode($filledDates) ?>;
var SPD_HARIAN   = <?= json_encode($harianMap, JSON_HEX_TAG) ?>;
var SPD_TAHUN    = <?= (int)$tahun ?>;
var SPD_BULAN    = <?= (int)$bulan ?>;
var SPD_TGT_HARI = <?= (int)($target['tgt_hari'] ?? $hariTot) ?>;
var SPD_TGT_OFF  = <?= (float)($target['target_offline'] ?? 0) ?>;
var SPD_TGT_ON   = <?= (float)($target['target_online'] ?? 0) ?>;
var SPD_TGT_KHS  = <?= (float)($target['target_produk_khusus'] ?? 0) ?>;
var ADA_KHUSUS   = <?= $spd['ada_khusus'] ? 'true' : 'false' ?>;

// ── Tab switcher (dedicated, no prefix magic) ──
var SPD_TABS = ['kalender','target','rincian'];
function spdTab(name) {
    SPD_TABS.forEach(function(t) {
        var el = document.getElementById('spd-'+t);
        if (el) el.style.display = (t === name) ? 'block' : 'none';
        var btn = document.getElementById('spd-btn-'+t);
        if (btn) {
            if (t === name) {
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-secondary');
            }
        }
    });
}

// ── Format helpers ──
function fmtRpShort(n) {
    n = Math.round(n);
    if (n >= 1e9) return 'Rp '+(n/1e9).toFixed(1)+'M';
    if (n >= 1e6) return 'Rp '+(n/1e6).toFixed(1)+'jt';
    if (n >= 1e3) return 'Rp '+(n/1e3).toFixed(0)+'rb';
    return 'Rp '+n.toLocaleString('id-ID');
}
function fmtRpFull(n) {
    return 'Rp '+Math.round(n).toLocaleString('id-ID');
}
function setTxt(id, v) { var el=document.getElementById(id); if(el) el.textContent=v; }
function setVal(id, n) {
    var el = document.getElementById(id); if (!el) return;
    var num = parseInt(n)||0;
    el.value = num===0 ? '0' : num.toLocaleString('id-ID').replace(/,/g,'.');
}

// ── Calendar click ──
function spdCalClick(ds) {
    var d   = new Date(ds+'T00:00:00');
    var lbl = d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
    setTxt('spd-input-title', '✏️ Input Aktual Sales');
    setTxt('spd-input-subtitle', lbl);
    document.getElementById('spd-tanggal-input').value = ds;

    // Per-day target hints
    var hari = SPD_TGT_HARI || 1;
    setTxt('spd-tgt-off-day', fmtRpShort(SPD_TGT_OFF/hari));
    setTxt('spd-tgt-on-day',  fmtRpShort(SPD_TGT_ON/hari));
    if (ADA_KHUSUS) setTxt('spd-tgt-khs-day', fmtRpShort(SPD_TGT_KHS/hari));

    // Pre-fill existing data
    var ex = SPD_HARIAN[ds];
    setVal('spd-off', ex ? ex.aktual_offline       : 0);
    setVal('spd-on',  ex ? ex.aktual_online         : 0);
    if (ADA_KHUSUS) setVal('spd-khs', ex ? ex.aktual_produk_khusus : 0);
    setVal('spd-dry', ex ? ex.aktual_dry            : 0);
    setVal('spd-per', ex ? ex.aktual_perishable     : 0);
    var cat = document.getElementById('spd-cat');
    if (cat) cat.value = ex ? (ex.catatan||'') : '';

    updateSpdTotal();
    var box = document.getElementById('spd-input-box');
    box.style.display = 'block';
    setTimeout(function(){ box.scrollIntoView({behavior:'smooth',block:'start'}); }, 50);
}

// ── Edit from rincian table ──
function spdEditRow(ds, off, on, khs, dry, per, cat) {
    spdTab('kalender');
    setTimeout(function(){ spdCalClick(ds); }, 80);
}

// ── Live total ──
function updateSpdTotal() {
    var off = getRaw(document.getElementById('spd-off')?.value||'0');
    var on  = getRaw(document.getElementById('spd-on')?.value||'0');
    setTxt('spd-total-preview', fmtRpFull(off+on));
}
['spd-off','spd-on'].forEach(function(id){
    var el = document.getElementById(id);
    if (el) el.addEventListener('input', updateSpdTotal);
});

// ── Target preview ──
function updateTgtPreview() {
    var hari = parseInt(document.getElementById('tgt-hari')?.value)||1;
    var off  = getRaw(document.getElementById('tgt-off')?.value||'0');
    var on   = getRaw(document.getElementById('tgt-on')?.value||'0');
    var khs  = getRaw(document.getElementById('tgt-khs')?.value||'0');
    setTxt('tgt-off-d',    fmtRpShort(off/hari));
    setTxt('tgt-on-d',     fmtRpShort(on/hari));
    setTxt('tgt-khs-d',    fmtRpShort(khs/hari));
    setTxt('tgt-prev-off', fmtRpShort(off/hari));
    setTxt('tgt-prev-on',  fmtRpShort(on/hari));
    setTxt('tgt-prev-tot', fmtRpShort((off+on)/hari));
    setTxt('tgt-prev-khs', fmtRpShort(khs/hari));
}

// ── Init ──
document.addEventListener('DOMContentLoaded', function() {
    // Calendar
    KPI.renderCalendar('spd-calendar', SPD_TAHUN, SPD_BULAN, SPD_FILLED, spdCalClick);
    // Target preview
    updateTgtPreview();
    // Init number inputs
    initNumberInputs();
});
</script>
