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
        body{background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 60%,#3b82f6 100%);min-height:100vh;}
        .otp-input{width:3rem;height:3.5rem;text-align:center;font-size:1.5rem;font-weight:700;border:2px solid #bfdbfe;border-radius:.75rem;color:#1e3a8a;background:#eff6ff;transition:border-color .15s,box-shadow .15s;outline:none;}
        .otp-input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.2);}
    </style>
</head>
<body class="flex items-center justify-center p-4 min-h-screen">
    <div class="w-full max-w-sm">
        <!-- Icon -->
        <div class="text-center mb-6">
            <div class="inline-flex w-16 h-16 bg-white/20 backdrop-blur rounded-2xl items-center justify-center mb-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-white font-extrabold text-2xl">Verifikasi OTP</h1>
            <p class="text-blue-200 text-sm mt-1">
                Masukkan kode OTP toko <strong class="text-yellow-300"><?= htmlspecialchars($toko['nama_toko'] ?? $kode) ?></strong>
            </p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-6">
            <?php if ($flash && $flash['type'] === 'error'): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <?= htmlspecialchars($flash['msg']) ?>
            </div>
            <?php endif; ?>

            <p class="text-slate-600 text-sm mb-5 text-center">
                Setiap aksi input/update data memerlukan kode OTP untuk keamanan. Hubungi supervisor untuk mendapatkan kode.
            </p>

            <form method="POST" action="<?= BASE_URL ?>/index.php?page=otp&action=verify" id="otp-form">
                <input type="hidden" name="kode_toko" value="<?= htmlspecialchars($kode) ?>">

                <!-- OTP 6 digit inputs -->
                <div class="flex justify-center gap-2 mb-6">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" maxlength="1" class="otp-input" id="otp<?= $i ?>"
                           inputmode="numeric" pattern="[0-9]"
                           oninput="moveNext(this, <?= $i ?>)" onkeydown="movePrev(event, <?= $i ?>)">
                    <?php endfor; ?>
                    <input type="hidden" name="otp" id="otp-hidden">
                </div>

                <button type="submit" id="submit-btn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl text-sm transition-all shadow-lg shadow-blue-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    Verifikasi
                </button>
            </form>

            <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    ← Kembali ke Dashboard
                </a>
            </div>
        </div>

        <p class="text-center text-blue-300 text-xs mt-5">OTP berlaku 30 menit setelah verifikasi</p>
    </div>

<script>
function moveNext(el, idx) {
    el.value = el.value.replace(/[^0-9]/g,'');
    if (el.value && idx < 6) {
        document.getElementById('otp' + (idx+1)).focus();
    }
    checkComplete();
}
function movePrev(e, idx) {
    if (e.key === 'Backspace' && !document.getElementById('otp'+idx).value && idx > 1) {
        document.getElementById('otp'+(idx-1)).focus();
    }
}
function checkComplete() {
    var full = '';
    for (var i=1;i<=6;i++) full += (document.getElementById('otp'+i).value||'');
    document.getElementById('otp-hidden').value = full;
    document.getElementById('submit-btn').disabled = full.length < 6;
}
// Auto-focus first input
document.getElementById('otp1').focus();
// Handle paste
document.getElementById('otp1').addEventListener('paste', function(e) {
    e.preventDefault();
    var text = (e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
    for (var i=0;i<text.length;i++) {
        var el = document.getElementById('otp'+(i+1));
        if (el) el.value = text[i];
    }
    if (text.length === 6) document.getElementById('otp6').focus();
    checkComplete();
});
</script>
</body>
</html>
