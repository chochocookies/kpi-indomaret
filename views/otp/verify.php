<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP – KPI Monitor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/tailwind.css">
    <style>
        *{font-family:'Plus Jakarta Sans',sans-serif;}
        body{background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 55%,#3b82f6 100%);min-height:100vh;}
        .otp-input{width:2.75rem;height:3.2rem;text-align:center;font-size:1.4rem;font-weight:800;border:2px solid #bfdbfe;border-radius:.75rem;color:#1e3a8a;background:#eff6ff;transition:border-color .15s,box-shadow .15s,transform .1s;outline:none;}
        .otp-input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.2);transform:scale(1.05);}
        .otp-input.filled{border-color:#22c55e;background:#f0fdf4;}
    </style>
</head>
<body class="flex items-center justify-center p-4 min-h-screen">
<div class="w-full max-w-sm">
    <!-- Icon -->
    <div class="text-center mb-6">
        <div class="inline-flex w-16 h-16 rounded-2xl items-center justify-center mb-3" style="background:rgba(255,255,255,.15)">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-white font-extrabold text-2xl">Verifikasi OTP</h1>
        <p class="text-blue-200 text-sm mt-1">Toko <strong class="text-yellow-300"><?= htmlspecialchars($toko['nama_toko'] ?? $kode) ?></strong></p>
        <?php if (!empty($sisaMenit)): ?>
        <p class="text-green-300 text-xs mt-1">✅ OTP aktif – sisa <?= $sisaMenit ?> menit</p>
        <?php endif; ?>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-2xl p-6">
        <?php if (!empty($flash) && $flash['type'] === 'error'): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4 flex items-center gap-2 animate-pulse">
            <span class="text-base flex-shrink-0">❌</span>
            <span><?= htmlspecialchars($flash['msg']) ?></span>
        </div>
        <?php endif; ?>

        <p class="text-slate-600 text-sm mb-5 text-center leading-relaxed">
            Masukkan kode <strong>6 digit OTP</strong> dari supervisor/admin untuk menyimpan data.
        </p>

        <form method="POST" action="<?= BASE_URL ?>/index.php?page=otp&action=verify" id="otp-form">
            <input type="hidden" name="kode_toko" value="<?= htmlspecialchars($kode) ?>">
            <input type="hidden" name="otp" id="otp-hidden">

            <!-- 6 boxes -->
            <div class="flex justify-center gap-2 mb-6" id="otp-boxes">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <input type="text" maxlength="1" class="otp-input" id="otp<?= $i ?>"
                       inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code">
                <?php endfor; ?>
            </div>

            <!-- Progress dots -->
            <div class="flex justify-center gap-1.5 mb-5" id="otp-dots">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div id="dot<?= $i ?>" style="width:8px;height:8px;border-radius:50%;background:#e2e8f0;transition:background .15s;"></div>
                <?php endfor; ?>
            </div>

            <button type="submit" id="submit-btn"
                    class="w-full text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-lg disabled:opacity-40 disabled:cursor-not-allowed"
                    style="background:#2563eb" disabled>
                Verifikasi & Simpan Data
            </button>
        </form>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center space-y-2">
            <p class="text-xs text-slate-400">Belum punya kode? Hubungi supervisor atau admin.</p>
            <a href="javascript:history.back()" class="text-sm text-blue-600 hover:text-blue-800 font-semibold block">
                ← Kembali ke halaman sebelumnya
            </a>
        </div>
    </div>

    <p class="text-center text-blue-300 text-xs mt-5">🔒 OTP berlaku 30 menit setelah verifikasi</p>
</div>

<script>
var inputs = [];
for (var i = 1; i <= 6; i++) inputs.push(document.getElementById('otp'+i));

inputs.forEach(function(inp, idx) {
    inp.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace') {
            if (this.value) { this.value = ''; updateDot(idx+1, false); }
            else if (idx > 0) { inputs[idx-1].focus(); inputs[idx-1].value=''; updateDot(idx, false); }
            updateHidden(); checkReady();
        }
        if (e.key === 'ArrowLeft' && idx > 0) inputs[idx-1].focus();
        if (e.key === 'ArrowRight' && idx < 5) inputs[idx+1].focus();
    });
    inp.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g,'').slice(-1);
        if (this.value) {
            this.classList.add('filled');
            updateDot(idx+1, true);
            if (idx < 5) inputs[idx+1].focus();
        } else {
            this.classList.remove('filled');
            updateDot(idx+1, false);
        }
        updateHidden(); checkReady();
    });
    inp.addEventListener('paste', function(e) {
        e.preventDefault();
        var text = (e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
        for (var j=0;j<text.length;j++) {
            if (inputs[j]) { inputs[j].value=text[j]; inputs[j].classList.add('filled'); updateDot(j+1,true); }
        }
        if (text.length===6) inputs[5].focus();
        updateHidden(); checkReady();
    });
});

function updateDot(i, filled) {
    var d = document.getElementById('dot'+i);
    if (d) d.style.background = filled ? '#2563eb' : '#e2e8f0';
}
function updateHidden() {
    var val=''; inputs.forEach(function(i){ val+=i.value||''; });
    document.getElementById('otp-hidden').value = val;
}
function checkReady() {
    var val=''; inputs.forEach(function(i){ val+=i.value||''; });
    var btn = document.getElementById('submit-btn');
    btn.disabled = val.length < 6;
    btn.style.background = val.length < 6 ? '#93c5fd' : '#2563eb';
}
// Auto-focus
inputs[0].focus();
</script>
</body>
</html>
