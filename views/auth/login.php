<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KPI Monitor Indomaret</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>* { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-xl mb-4">
                <span class="text-blue-700 font-black text-xl">KPI</span>
            </div>
            <h1 class="text-white font-bold text-2xl">KPI Monitor</h1>
            <p class="text-blue-300 text-sm mt-1">Indomaret – Sistem Monitoring KPI</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-7">
            <h2 class="text-blue-900 font-bold text-lg mb-6">Masuk ke Akun Anda</h2>

            <?php if ($flash && $flash['type'] === 'error'): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <?= htmlspecialchars($flash['msg']) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/index.php?page=login&action=login">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                        <input type="text" name="username" required autocomplete="username"
                            placeholder="Masukkan username"
                            class="w-full px-4 py-3 border-2 border-blue-100 rounded-xl text-sm focus:outline-none focus:border-blue-500 bg-blue-50/50 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" required autocomplete="current-password"
                            placeholder="Masukkan password"
                            class="w-full px-4 py-3 border-2 border-blue-100 rounded-xl text-sm focus:outline-none focus:border-blue-500 bg-blue-50/50 transition-colors">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-blue-200 mt-2">
                        Masuk
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-xs text-slate-400 text-center">Default password: <code class="bg-slate-100 px-2 py-0.5 rounded font-mono text-slate-600">password</code></p>
            </div>
        </div>

        <p class="text-center text-blue-300 text-xs mt-6">© <?= date('Y') ?> KPI Monitor Indomaret</p>
    </div>
</body>
</html>
