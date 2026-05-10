<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KpiModel.php';

class NbrController extends BaseController {
    private KpiModel $kpi;
    public function __construct() { parent::__construct(); $this->kpi = new KpiModel(); }

    public function index(): void {
        $this->requireLogin();
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        if (!$kode) { $this->redirect(BASE_URL.'/index.php?page=dashboard'); }
        $toko      = $this->kpi->getToko($kode);
        $nbr       = $this->kpi->getNbr($kode, $tahun, $bulan);
        $nbrHarian = $this->kpi->getNbrHarian($kode, $tahun, $bulan);
        $kpiData   = $this->kpi->hitungKpiLengkap($kode, $tahun, $bulan);
        $semuaToko = $this->isAdmin() ? $this->kpi->getAllToko() : [];
        $this->view('nbr/index', compact('toko','nbr','nbrHarian','kpiData','kode','bulan','tahun','semuaToko') + ['flash'=>$this->getFlash()]);
    }

    public function save(): void {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect(BASE_URL.'/index.php?page=nbr'); }
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        $this->requireOtp($kode);
        $saveType = $_POST['save_type'] ?? 'summary';
        try {
            if ($saveType === 'harian') {
                $this->kpi->saveNbrHarian($kode, [
                    'tanggal'      => $_POST['tanggal'] ?? date('Y-m-d'),
                    'no_nbr'       => sanitize($_POST['no_nbr'] ?? ''),
                    'jenis'        => sanitize($_POST['jenis'] ?? 'dry'),
                    'nama_produk'  => sanitize($_POST['nama_produk'] ?? ''),
                    'nilai'        => (int)str_replace(['.', ','], '', $_POST['nilai'] ?? 0),
                    'catatan'      => sanitize($_POST['catatan'] ?? ''),
                ]);
                $this->setFlash('success', 'Nota NBR berhasil ditambahkan.');
            } else {
                $this->kpi->saveNbr($kode, $tahun, $bulan, [
                    'sales_nett_dry' => (int)str_replace(['.', ','], '', $_POST['sales_nett_dry'] ?? 0),
                    'nbr_dry'        => (int)str_replace(['.', ','], '', $_POST['aktual_nbr_dry'] ?? 0),
                    'modul_main'     => (int)($_POST['modul_main'] ?? 0),
                    'modul_ach'      => (int)($_POST['modul_ach'] ?? 0),
                    'catatan'        => sanitize($_POST['catatan'] ?? ''),
                ]);
                $this->setFlash('success', 'Data NBR berhasil disimpan.');
            }
        } catch (Exception $e) { $this->setFlash('error', 'Gagal: '.$e->getMessage()); }
        $this->redirect(BASE_URL."/index.php?page=nbr&kode_toko=$kode&bulan=$bulan&tahun=$tahun");
    }

    public function delete(): void {
        $this->requireLogin();
        $id   = (int)($_POST['id'] ?? 0);
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        $this->requireOtp($kode);
        if ($id > 0) { $this->kpi->deleteNbrHarian($id); $this->setFlash('success','Nota NBR dihapus.'); }
        $this->redirect(BASE_URL."/index.php?page=nbr&kode_toko=$kode&bulan=$bulan&tahun=$tahun");
    }
}
