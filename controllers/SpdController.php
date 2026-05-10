<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KpiModel.php';

class SpdController extends BaseController {
    private KpiModel $kpi;

    public function __construct() {
        parent::__construct();
        $this->kpi = new KpiModel();
    }

    public function index(): void {
        $this->requireLogin();
        $kode  = $this->getKodeToko();
        $bulan = $this->getBulan();
        $tahun = $this->getTahun();

        if (!$kode) {
            $this->setFlash('error', 'Pilih toko terlebih dahulu.');
            $this->redirect(BASE_URL . '/index.php?page=dashboard');
        }

        $toko        = $this->kpi->getToko($kode);
        $target      = $this->kpi->getTargetSpd($kode, $tahun, $bulan);
        $harian      = $this->kpi->getAktualSpdBulanan($kode, $tahun, $bulan);
        $aggregate   = $this->kpi->getAktualSpdAggregate($kode, $tahun, $bulan);
        $kpiData     = $this->kpi->hitungKpiLengkap($kode, $tahun, $bulan);
        $semuaToko   = $this->isAdmin() ? $this->kpi->getAllToko() : [];

        $this->view('spd/index', [
            'toko'       => $toko,
            'target'     => $target,
            'harian'     => $harian,
            'aggregate'  => $aggregate,
            'kpiData'    => $kpiData,
            'kode_toko'  => $kode,
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'semuaToko'  => $semuaToko,
            'flash'      => $this->getFlash(),
        ]);
    }

    public function save(): void {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/index.php?page=spd');
        }

        $kode  = $this->getKodeToko();
        $bulan = $this->getBulan();
        $tahun = $this->getTahun();
        $type  = $_POST['save_type'] ?? 'aktual';
        $this->requireOtp($kode);

        try {
            if ($type === 'target') {
                $this->kpi->saveTargetSpd($kode, $tahun, $bulan, [
                    'tgt_hari'   => (int)($_POST['tgt_hari'] ?? 28),
                    'offline'    => (int)str_replace(['.', ','], '', $_POST['target_offline'] ?? 0),
                    'online'     => (int)str_replace(['.', ','], '', $_POST['target_online'] ?? 0),
                    'dry'        => (int)str_replace(['.', ','], '', $_POST['target_dry'] ?? 0),
                    'perishable' => (int)str_replace(['.', ','], '', $_POST['target_perishable'] ?? 0),
                    'khusus'     => (int)str_replace(['.', ','], '', $_POST['target_khusus'] ?? 0),
                    'virtual'    => (int)str_replace(['.', ','], '', $_POST['target_virtual'] ?? 0),
                ]);
                $this->setFlash('success', 'Target SPD berhasil disimpan.');
            } else {
                $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
                $this->kpi->saveAktualSpd($kode, $tanggal, [
                    'offline'    => (int)str_replace(['.', ','], '', $_POST['aktual_offline'] ?? 0),
                    'online'     => (int)str_replace(['.', ','], '', $_POST['aktual_online'] ?? 0),
                    'khusus'     => (int)str_replace(['.', ','], '', $_POST['aktual_khusus'] ?? 0),
                    'dry'        => (int)str_replace(['.', ','], '', $_POST['aktual_dry'] ?? 0),
                    'perishable' => (int)str_replace(['.', ','], '', $_POST['aktual_perishable'] ?? 0),
                    'virtual'    => (int)str_replace(['.', ','], '', $_POST['aktual_virtual'] ?? 0),
                    'catatan'    => sanitize($_POST['catatan'] ?? ''),
                ]);
                $this->setFlash('success', 'Aktual SPD berhasil disimpan.');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        $this->redirect(BASE_URL . "/index.php?page=spd&kode_toko=$kode&bulan=$bulan&tahun=$tahun");
    }

    public function detail(): void {
        $this->requireLogin();
        $this->index();
    }

    public function delete(): void {
        $this->requireLogin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->db->execute("DELETE FROM aktual_spd_harian WHERE id=?", [$id]);
            $this->setFlash('success', 'Data berhasil dihapus.');
        }
        $kode  = $this->getKodeToko();
        $bulan = $this->getBulan();
        $tahun = $this->getTahun();
        $this->redirect(BASE_URL . "/index.php?page=spd&kode_toko=$kode&bulan=$bulan&tahun=$tahun");
    }
}
