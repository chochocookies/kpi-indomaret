<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KpiModel.php';

class SummaryController extends BaseController {
    private KpiModel $kpi;
    public function __construct() { parent::__construct(); $this->kpi = new KpiModel(); }

    public function index(): void {
        $this->requireLogin();
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        if (!$kode) { $this->redirect(BASE_URL.'/index.php?page=dashboard'); }
        $toko      = $this->kpi->getToko($kode);
        $kpiData   = $this->kpi->hitungKpiLengkap($kode, $tahun, $bulan);
        $semuaToko = $this->isAdmin() ? $this->kpi->getAllToko() : [];
        $waMsg     = urlencode(generateWhatsappSummary($kpiData, $toko['nama_toko'], $bulan, $tahun));
        $this->view('dashboard/summary', compact('toko','kpiData','kode','bulan','tahun','semuaToko','waMsg') + ['flash'=>$this->getFlash()]);
    }

    public function whatsapp(): void {
        $this->requireLogin();
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        $toko    = $this->kpi->getToko($kode);
        $kpiData = $this->kpi->hitungKpiLengkap($kode, $tahun, $bulan);
        $msg     = generateWhatsappSummary($kpiData, $toko['nama_toko'], $bulan, $tahun);
        $this->json(['message' => $msg, 'encoded' => urlencode($msg)]);
    }
}
