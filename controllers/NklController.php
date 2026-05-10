<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KpiModel.php';

class NklController extends BaseController {
    private KpiModel $kpi;
    public function __construct() { parent::__construct(); $this->kpi = new KpiModel(); }

    public function index(): void {
        $this->requireLogin();
        $kode = $this->getKodeToko();
        $bulan = $this->getBulan(); $tahun = $this->getTahun();
        if (!$kode) { $this->redirect(BASE_URL.'/index.php?page=dashboard'); }
        $toko      = $this->kpi->getToko($kode);
        $nkl       = $this->kpi->getNkl($kode, $tahun, $bulan);
        $kpiData   = $this->kpi->hitungKpiLengkap($kode, $tahun, $bulan);
        $semuaToko = $this->isAdmin() ? $this->kpi->getAllToko() : [];
        $this->view('nkl/index', compact('toko','nkl','kpiData','kode','bulan','tahun','semuaToko') + ['flash'=>$this->getFlash()]);
    }

    public function save(): void {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect(BASE_URL.'/index.php?page=nkl'); }
        $this->requireOtp($this->getKodeToko());
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        try {
            $this->kpi->saveNkl($kode, $tahun, $bulan, [
                'is_audit'       => (int)($_POST['is_audit'] ?? 0),
                'sales_gross_all'=> (int)str_replace(['.', ','], '', $_POST['sales_gross_all'] ?? 0),
                'nkl_all'        => (int)str_replace(['.', ','], '', $_POST['aktual_nkl_all'] ?? 0),
                'sales_gross_buah'=> (int)str_replace(['.', ','], '', $_POST['sales_gross_buah'] ?? 0),
                'nkl_buah'       => (int)str_replace(['.', ','], '', $_POST['aktual_nkl_buah'] ?? 0),
                'catatan'        => sanitize($_POST['catatan'] ?? ''),
            ]);
            $this->setFlash('success', 'Data NKL berhasil disimpan.');
        } catch (Exception $e) { $this->setFlash('error', 'Gagal: '.$e->getMessage()); }
        $this->redirect(BASE_URL."/index.php?page=nkl&kode_toko=$kode&bulan=$bulan&tahun=$tahun");
    }
}
