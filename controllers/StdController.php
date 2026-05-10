<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KpiModel.php';

class StdController extends BaseController {
    private KpiModel $kpi;
    public function __construct() { parent::__construct(); $this->kpi = new KpiModel(); }

    public function index(): void {
        $this->requireLogin();
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        if (!$kode) { $this->redirect(BASE_URL.'/index.php?page=dashboard'); }
        $toko      = $this->kpi->getToko($kode);
        $std       = $this->kpi->getStd($kode, $tahun, $bulan);
        $stdHarian = $this->kpi->getStdHarian($kode, $tahun, $bulan);
        $kpiData   = $this->kpi->hitungKpiLengkap($kode, $tahun, $bulan);
        $semuaToko = $this->isAdmin() ? $this->kpi->getAllToko() : [];
        $this->view('std/index', compact('toko','std','stdHarian','kpiData','kode','bulan','tahun','semuaToko') + ['flash'=>$this->getFlash()]);
    }

    public function save(): void {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect(BASE_URL.'/index.php?page=std'); }
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        $this->requireOtp($kode);
        $saveType = $_POST['save_type'] ?? 'summary';
        try {
            if ($saveType === 'harian') {
                $this->kpi->saveStdHarian($kode, $_POST['tanggal'] ?? date('Y-m-d'), [
                    'poinku_trx'        => (int)($_POST['poinku_trx'] ?? 0),
                    'poinku_total_trx'  => (int)($_POST['poinku_total_trx'] ?? 0),
                    'item2_trx'         => (int)($_POST['item2_trx'] ?? 0),
                    'item2_total_trx'   => (int)($_POST['item2_total_trx'] ?? 0),
                    'ipayment_trx'      => (int)($_POST['ipayment_trx'] ?? 0),
                    'nontunai_trx'      => (int)($_POST['nontunai_trx'] ?? 0),
                    'nontunai_total_trx'=> (int)($_POST['nontunai_total_trx'] ?? 0),
                    'catatan'           => sanitize($_POST['catatan'] ?? ''),
                ]);
                $this->setFlash('success', 'Data STD harian berhasil disimpan.');
            } else {
                $fields = ['std_poinku_b3','std_poinku_b2','std_poinku_b1','aktual_std_poinku',
                           'std_2item_b3','std_2item_b2','std_2item_b1','aktual_std_2item',
                           'trx_ipayment_b3','trx_ipayment_b2','trx_ipayment_b1','aktual_trx_ipayment',
                           'trx_nontunai_b3','trx_nontunai_b2','trx_nontunai_b1','aktual_trx_nontunai','catatan'];
                $data = [];
                foreach ($fields as $f) $data[$f] = $f === 'catatan' ? sanitize($_POST[$f] ?? '') : (float)($_POST[$f] ?? 0);
                $this->kpi->saveStd($kode, $tahun, $bulan, $data);
                $this->setFlash('success', 'Data STD berhasil disimpan.');
            }
        } catch (Exception $e) { $this->setFlash('error', 'Gagal: '.$e->getMessage()); }
        $this->redirect(BASE_URL."/index.php?page=std&kode_toko=$kode&bulan=$bulan&tahun=$tahun");
    }

    public function deleteHarian(): void {
        $this->requireLogin();
        $id   = (int)($_POST['id'] ?? 0);
        $kode = $this->getKodeToko(); $bulan = $this->getBulan(); $tahun = $this->getTahun();
        $this->requireOtp($kode);
        if ($id > 0) { $this->kpi->deleteStdHarian($id); $this->setFlash('success','Data harian dihapus.'); }
        $this->redirect(BASE_URL."/index.php?page=std&kode_toko=$kode&bulan=$bulan&tahun=$tahun");
    }
}
