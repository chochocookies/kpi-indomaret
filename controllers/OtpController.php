<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KpiModel.php';

class OtpController extends BaseController {
    private KpiModel $kpi;

    public function __construct() {
        parent::__construct();
        $this->kpi = new KpiModel();
    }

    public function index(): void {
        $this->requireLogin();
        $kode  = $_GET['kode_toko'] ?? $_SESSION['kode_toko'] ?? '';
        $toko  = $this->kpi->getToko($kode);
        $flash = $this->getFlash();
        require_once __DIR__ . '/../views/otp/verify.php';
    }

    public function verify(): void {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/index.php?page=dashboard');
        }

        $kode     = sanitize($_POST['kode_toko'] ?? '');
        $inputOtp = trim($_POST['otp'] ?? '');

        if ($this->kpi->validateOtp($kode, $inputOtp)) {
            $this->markOtpOk($kode);
            $redirect = $_SESSION['otp_redirect'] ?? (BASE_URL . '/index.php?page=dashboard&kode_toko=' . $kode);
            unset($_SESSION['otp_redirect'], $_SESSION['otp_kode']);
            $this->redirect($redirect);
        } else {
            $this->setFlash('error', 'Kode OTP salah. Silakan coba lagi.');
            $this->redirect(BASE_URL . '/index.php?page=otp&kode_toko=' . $kode);
        }
    }

    // Admin: lihat/ubah OTP toko
    public function manage(): void {
        $this->requireRole(['superadmin', 'admin']);
        $semuaToko = $this->kpi->getAllToko();
        $otpList   = $this->db->fetchAll("SELECT kode_toko, kode_otp, otp_updated_at FROM toko WHERE aktif=1 ORDER BY kode_toko");
        require_once __DIR__ . '/../views/otp/manage.php';
    }

    public function update(): void {
        $this->requireRole(['superadmin', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/index.php?page=otp&action=manage');
        }
        $kode   = sanitize($_POST['kode_toko'] ?? '');
        $newOtp = trim($_POST['new_otp'] ?? '');
        if (strlen($newOtp) === 6 && ctype_digit($newOtp)) {
            $this->kpi->updateOtp($kode, $newOtp);
            $this->setFlash('success', "OTP toko {$kode} berhasil diperbarui.");
        } else {
            $this->setFlash('error', 'OTP harus 6 digit angka.');
        }
        $this->redirect(BASE_URL . '/index.php?page=otp&action=manage');
    }
}
