<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KpiModel.php';

class TurnoverController extends BaseController {
    private KpiModel $kpi;
    public function __construct() { parent::__construct(); $this->kpi = new KpiModel(); }

    public function index(): void {
        $this->requireLogin();
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        if (!$kode) { $this->redirect(BASE_URL.'/index.php?page=dashboard'); }
        $toko      = $this->kpi->getToko($kode);
        $to        = $this->kpi->getTurnover($kode, $tahun, $bulan);
        $kpiData   = $this->kpi->hitungKpiLengkap($kode, $tahun, $bulan);
        $semuaToko = $this->isAdmin() ? $this->kpi->getAllToko() : [];
        $this->view('turnover/index', compact('toko','to','kpiData','kode','bulan','tahun','semuaToko') + ['flash'=>$this->getFlash()]);
    }

    public function save(): void {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect(BASE_URL.'/index.php?page=turnover'); }
        $this->requireOtp($this->getKodeToko());
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        try {
            $this->kpi->saveTurnover($kode, $tahun, $bulan, [
                'jumlah_karyawan' => (int)($_POST['jumlah_karyawan'] ?? 0),
                'jumlah_keluar'   => (int)($_POST['jumlah_keluar'] ?? 0),
                'catatan'         => sanitize($_POST['catatan'] ?? ''),
            ]);
            $this->setFlash('success', 'Data Turn Over berhasil disimpan.');
        } catch (Exception $e) { $this->setFlash('error', 'Gagal: '.$e->getMessage()); }
        $this->redirect(BASE_URL."/index.php?page=turnover&kode_toko=$kode&bulan=$bulan&tahun=$tahun");
    }
}
