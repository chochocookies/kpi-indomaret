<?php
// config/app.php
define('APP_NAME', 'KPI Monitor Indomaret');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/kpi-indomaret');
define('SESSION_NAME', 'kpi_session');

// Bulan dalam bahasa Indonesia
define('BULAN_ID', [
    1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
    4  => 'April',    5  => 'Mei',      6  => 'Juni',
    7  => 'Juli',     8  => 'Agustus',  9  => 'September',
    10 => 'Oktober',  11 => 'November', 12 => 'Desember',
]);

// Helper function agar array constant bisa diakses dengan aman
function getNamaBulan(int $bulan): string {
    $list = BULAN_ID;
    return $list[$bulan] ?? 'Bulan';
}

function getSemuaBulan(): array {
    return BULAN_ID;
}

// KPI Points
define('KPI_POINTS', [
    'spd_all'        => 50,
    'spd_offline'    => 15,
    'spd_online'     => 10,
    'spd_dry'        => 5,
    'spd_perishable' => 5,
    'spd_khusus'     => 10,
    'spd_virtual'    => 5,
    'nkl_total'      => 15,
    'nkl_all'        => 10,
    'nkl_buah'       => 5,
    'nbr_total'      => 15,
    'nbr_dry'        => 5,
    'nbr_khusus'     => 10,
    'std_total'      => 16,
    'std_poinku'     => 4,
    'std_2item'      => 4,
    'trx_ipayment'   => 4,
    'trx_nontunai'   => 4,
    'turnover'       => 4,
    'total'          => 100,
]);

// Threshold insentif
define('KPI_THRESHOLD_INSENTIF', 70);
define('KPI_THRESHOLD_GRADE1', 90);
define('KPI_THRESHOLD_GRADE2', 70);

// NKL budget threshold
define('NKL_ALL_THRESHOLD', 0.002);  // 0.20%
define('NKL_BUAH_THRESHOLD', 0.05);  // 5%
define('NBR_DRY_THRESHOLD', 0.001);  // 0.1%

// SPD proporsional threshold
define('SPD_PROP_LOWER', 0.95); // 95%
