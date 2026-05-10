<?php
// helpers/functions.php

function formatRupiah(int|float $angka, bool $short = false): string {
    if ($short) {
        if (abs($angka) >= 1_000_000_000) return 'Rp ' . number_format($angka / 1_000_000_000, 1) . 'M';
        if (abs($angka) >= 1_000_000)     return 'Rp ' . number_format($angka / 1_000_000, 1) . 'jt';
        if (abs($angka) >= 1_000)         return 'Rp ' . number_format($angka / 1_000, 1) . 'rb';
    }
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function formatAngka(int|float $angka): string {
    return number_format($angka, 0, ',', '.');
}

function formatPersen(float $persen, int $decimal = 2): string {
    return number_format($persen, $decimal) . '%';
}

function hitungAchievement(float $aktual, float $target): float {
    if ($target == 0) return 0;
    return round(($aktual / $target) * 100, 2);
}

function hitungTargetProporsional(float $targetBulan, int $tgtHari, int $hariIni): float {
    if ($tgtHari == 0) return 0;
    return round(($targetBulan / $tgtHari) * $hariIni, 0);
}

function getStatusBadge(float $ach, string $type = 'spd'): array {
    if ($type === 'nkl' || $type === 'nbr') {
        // Lower is better
        if ($ach <= 100) return ['class' => 'badge-success', 'text' => 'TERCAPAI', 'icon' => '✓'];
        return ['class' => 'badge-danger', 'text' => 'OVER', 'icon' => '✗'];
    }
    if ($ach >= 100)  return ['class' => 'badge-success', 'text' => 'TERCAPAI', 'icon' => '✓'];
    if ($ach >= 95)   return ['class' => 'badge-warning', 'text' => 'HAMPIR', 'icon' => '~'];
    return ['class' => 'badge-danger', 'text' => 'BELUM', 'icon' => '✗'];
}

function getColorClass(float $ach, string $type = 'spd'): string {
    if ($type === 'nkl' || $type === 'nbr') {
        return $ach <= 100 ? 'text-green-600' : 'text-red-600';
    }
    if ($ach >= 100) return 'text-green-600';
    if ($ach >= 95)  return 'text-yellow-600';
    return 'text-red-600';
}

function getBgClass(float $ach, string $type = 'spd'): string {
    if ($type === 'nkl' || $type === 'nbr') {
        return $ach <= 100 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
    }
    if ($ach >= 100) return 'bg-green-50 border-green-200';
    if ($ach >= 95)  return 'bg-yellow-50 border-yellow-200';
    return 'bg-red-50 border-red-200';
}

function hitungPoinSPD(float $ach, float $poinMax): float {
    if ($ach >= 100) return $poinMax;
    if ($ach >= 95)  return round($ach / 100 * $poinMax, 2);
    return 0;
}

function hitungPoinNKL(float $pctNkl, float $threshold, float $poinMax): float {
    return ($pctNkl <= $threshold * 100) ? $poinMax : 0;
}

function hitungPoinNBRDry(float $pctNbr): float {
    return ($pctNbr <= 0.1) ? 5 : 0;
}

function hitungPoinNBRKhusus(int $modulMain, int $modulAch): float {
    if ($modulMain == 0) return 0;
    $ach = ($modulAch / $modulMain) * 100;
    if ($ach >= 100) return 10;
    return round($ach / 100 * 10, 2);
}

function hitungPoinSTD(float $aktual, float $maxL3m, float $poinMax): float {
    if ($maxL3m == 0) return 0;
    return $aktual >= $maxL3m ? $poinMax : 0;
}

function hitungPoinTurnover(int $jumlahKeluar): float {
    return $jumlahKeluar == 0 ? 4 : 0;
}

function getGradeInsentif(float $poinFinal, float $poinMaks): array {
    if ($poinMaks == 0) return ['grade' => 'N/A', 'class' => 'text-gray-500', 'bg' => 'bg-gray-100'];
    $pct = ($poinFinal / $poinMaks) * 100;
    if ($pct >= 90) return ['grade' => 'INSENTIF GRADE 1', 'class' => 'text-yellow-700', 'bg' => 'bg-yellow-100'];
    if ($pct >= 70) return ['grade' => 'INSENTIF GRADE 2', 'class' => 'text-blue-700', 'bg' => 'bg-blue-100'];
    return ['grade' => 'ZONK', 'class' => 'text-red-700', 'bg' => 'bg-red-100'];
}

function getJumlahHariBerjalan(int $tahun, int $bulan): int {
    $today = new DateTime();
    $lastDay = new DateTime("$tahun-$bulan-01");
    $lastDay->modify('last day of this month');

    if ($today->format('Y') == $tahun && $today->format('m') == $bulan) {
        return (int)$today->format('j');
    }
    if ($today > $lastDay) {
        return (int)$lastDay->format('j');
    }
    return 1;
}

function getJumlahHariBulan(int $tahun, int $bulan): int {
    return cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect(BASE_URL . '/index.php?page=login');
    }
}

function requireRole(array $roles): void {
    requireLogin();
    if (!in_array($_SESSION['role'], $roles)) {
        redirect(BASE_URL . '/index.php?page=dashboard');
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): bool {
    return isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token']);
}

function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function generateWhatsappSummary(array $data, string $namaToko, int $bulan, int $tahun): string {
    $blnArr = BULAN_ID;
    $bln = $blnArr[$bulan] ?? 'Bulan';
    $hari = $data['hari_berjalan'];
    $tglHari = $data['tgl_hari_ini'];

    $msg = "📊 *LAPORAN KPI {$namaToko}*\n";
    $msg .= "Periode: {$bln} {$tahun} (Hari ke-{$hari})\n";
    $msg .= "Update: {$tglHari}\n";
    $msg .= "─────────────────────\n";

    // SPD
    $spdAch = $data['spd']['ach'] ?? 0;
    $spdEmoji = $spdAch >= 100 ? '✅' : ($spdAch >= 95 ? '⚠️' : '❌');
    $msg .= "\n*1. SPD (SALES)*\n";
    $msg .= "{$spdEmoji} Aktual Berjalan: " . formatRupiah($data['spd']['aktual_berjalan'], true) . "\n";
    $msg .= "   Target Proporsional: " . formatRupiah($data['spd']['target_prop'], true) . "\n";
    $msg .= "   Achievement: " . formatPersen($spdAch) . "\n";
    $msg .= "   Poin: " . ($data['spd']['poin'] ?? 0) . "/" . KPI_POINTS['spd_all'] . "\n";

    // NKL
    $nklPoin = ($data['nkl']['poin_all'] ?? 0) + ($data['nkl']['poin_buah'] ?? 0);
    $msg .= "\n*2. NKL*\n";
    $msg .= ($data['nkl']['is_audit'] ? "🔍 Status: AUDIT\n" : "📋 Status: Proporsional\n");
    $msg .= "   Poin: {$nklPoin}/" . KPI_POINTS['nkl_total'] . "\n";

    // NBR
    $nbrPoin = ($data['nbr']['poin_dry'] ?? 0) + ($data['nbr']['poin_khusus'] ?? 0);
    $msg .= "\n*3. NBR*\n";
    $msg .= "   Poin: {$nbrPoin}/" . KPI_POINTS['nbr_total'] . "\n";

    // STD
    $stdPoin = ($data['std']['poin_poinku'] ?? 0) + ($data['std']['poin_2item'] ?? 0) +
               ($data['std']['poin_ipayment'] ?? 0) + ($data['std']['poin_nontunai'] ?? 0);
    $msg .= "\n*4. PENAWARAN STORE CREW*\n";
    $msg .= "   Poin: {$stdPoin}/" . KPI_POINTS['std_total'] . "\n";

    // Turnover
    $toPoin = $data['turnover']['poin'] ?? 0;
    $msg .= "\n*5. TURN OVER*\n";
    $msg .= ($toPoin == 4 ? "✅ Tidak ada karyawan keluar\n" : "❌ Ada karyawan keluar\n");
    $msg .= "   Poin: {$toPoin}/" . KPI_POINTS['turnover'] . "\n";

    // Total
    $totalPoin = ($data['spd']['poin'] ?? 0) + $nklPoin + $nbrPoin + $stdPoin + $toPoin;
    $totalMaks = $data['poin_maks'] ?? 100;
    $poinFinal = $totalMaks > 0 ? round(($totalPoin / $totalMaks) * 100, 2) : 0;
    $grade = getGradeInsentif($totalPoin, $totalMaks);

    $msg .= "\n─────────────────────\n";
    $msg .= "🏆 *TOTAL POIN: {$totalPoin}/{$totalMaks}*\n";
    $msg .= "📈 *KPI SCORE: " . formatPersen($poinFinal) . "*\n";
    $msg .= "🎯 *STATUS: " . $grade['grade'] . "*\n";
    $msg .= "─────────────────────\n";
    $msg .= "_Generated by KPI Monitor_";

    return $msg;
}
