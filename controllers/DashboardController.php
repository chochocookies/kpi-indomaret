<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KpiModel.php';

class DashboardController extends BaseController {

    private KpiModel $kpi;

    public function __construct() {
        parent::__construct();
        $this->kpi = new KpiModel();
    }

    public function index(): void {
        $this->requireLogin();

        $bulan = $this->getBulan();
        $tahun = $this->getTahun();
        $role  = $_SESSION['role'];

        if ($this->isAdmin()) {
            // Admin/superadmin: lihat semua toko
            $semuaToko = $this->kpi->getAllToko();
            $ringkasan = [];
            foreach ($semuaToko as $t) {
                $ringkasan[] = $this->kpi->hitungKpiLengkap($t['kode_toko'], $tahun, $bulan);
            }
            $this->view('dashboard/admin', [
                'ringkasan' => $ringkasan,
                'bulan'     => $bulan,
                'tahun'     => $tahun,
                'flash'     => $this->getFlash(),
            ]);
        } else {
            // Kepala toko: hanya tokonya sendiri
            $kode = $_SESSION['kode_toko'];
            // Blokir admin/superadmin dari akses halaman detail toko
            if ($this->isAdmin()) {
                $this->redirect(BASE_URL . '/index.php?page=dashboard');
            }
            if (!$kode) {
                $this->setFlash('error', 'Akun tidak terhubung ke toko manapun.');
                require_once __DIR__ . '/../views/auth/login.php';
                return;
            }
            $kpiData = $this->kpi->hitungKpiLengkap($kode, $tahun, $bulan);
            $this->view('dashboard/toko', [
                'kpiData'  => $kpiData,
                'kpiModel' => $this->kpi,
                'kode'     => $kode,
                'bulan'    => $bulan,
                'tahun'    => $tahun,
                'flash'    => $this->getFlash(),
            ]);
        }
    }
}
