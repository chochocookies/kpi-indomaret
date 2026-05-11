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
        $kode  = $_GET['kode_toko'] ?? $_SESSION['otp_kode'] ?? $_SESSION['kode_toko'] ?? '';
        $toko  = $this->kpi->getToko($kode);
        $flash = $this->getFlash();
        // Berapa menit tersisa jika sudah ada sesi OTP
        $otpKey     = 'otp_ok_' . $kode;
        $sisaMenit  = 0;
        if (!empty($_SESSION[$otpKey])) {
            $sisaMenit = max(0, 30 - (int)floor((time() - $_SESSION[$otpKey]) / 60));
        }
        require_once __DIR__ . '/../views/otp/verify.php';
    }

    public function verify(): void {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/index.php?page=dashboard');
        }

        $kode     = sanitize($_POST['kode_toko'] ?? '');
        $inputOtp = trim($_POST['otp'] ?? '');

        if (!$this->kpi->validateOtp($kode, $inputOtp)) {
            $this->setFlash('error', 'Kode OTP salah. Pastikan kode 6 digit yang diberikan supervisor.');
            $this->redirect(BASE_URL . '/index.php?page=otp&kode_toko=' . urlencode($kode));
            return;
        }

        // OTP benar
        $this->markOtpOk($kode);

        // Ada pending POST? Re-submit otomatis
        $pendingPost   = $_SESSION['otp_pending_post']   ?? null;
        $pendingAction = $_SESSION['otp_pending_action'] ?? null;
        unset($_SESSION['otp_pending_post'], $_SESSION['otp_pending_action'], $_SESSION['otp_kode']);

        if ($pendingPost && $pendingAction) {
            // Tampilkan form auto-submit dengan data yang tersimpan
            $this->autoSubmit($pendingAction, $pendingPost);
            return;
        }

        $this->setFlash('success', 'OTP terverifikasi! Silakan lanjutkan.');
        $this->redirect(BASE_URL . '/index.php?page=dashboard&kode_toko=' . urlencode($kode));
    }

    /**
     * Render halaman HTML dengan form hidden yang auto-submit
     * sehingga user tidak perlu mengisi data ulang
     */
    private function autoSubmit(string $action, array $postData): void {
        // Parse action URL untuk ambil page & action param
        $fields = '';
        foreach ($postData as $k => $v) {
            $k = htmlspecialchars($k, ENT_QUOTES);
            $v = htmlspecialchars((string)$v, ENT_QUOTES);
            $fields .= "<input type=\"hidden\" name=\"{$k}\" value=\"{$v}\">\n";
        }
        // Rebuild POST URL → keep query string but use index.php
        $actionUrl = BASE_URL . parse_url($action, PHP_URL_QUERY);
        // Full URL with scheme
        $submitUrl = BASE_URL . '/index.php?' . ltrim(parse_url($action, PHP_URL_QUERY) ?? '', '?');
        echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>
<title>Memproses...</title>
<link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&display=swap' rel='stylesheet'>
<style>*{font-family:'Plus Jakarta Sans',sans-serif;}body{background:#eff6ff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
.box{background:#fff;border-radius:1.5rem;padding:2.5rem;text-align:center;max-width:320px;box-shadow:0 4px 24px rgba(37,99,235,.12);}
.spinner{width:48px;height:48px;border:4px solid #bfdbfe;border-top-color:#2563eb;border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 1.5rem;}
@keyframes spin{to{transform:rotate(360deg)}}
h2{color:#1e3a8a;font-size:1.1rem;margin:0 0 .5rem}
p{color:#64748b;font-size:.85rem;margin:0}</style></head>
<body><div class='box'>
<div class='spinner'></div>
<h2>✅ OTP Terverifikasi!</h2>
<p>Menyimpan data Anda...</p>
</div>
<form id='f' method='POST' action='{$submitUrl}'>{$fields}</form>
<script>setTimeout(function(){document.getElementById('f').submit();},600);</script>
</body></html>";
        exit;
    }

    public function manage(): void {
        $this->requireRole(['superadmin', 'admin']);
        $otpList = $this->db->fetchAll(
            "SELECT t.kode_toko, t.nama_toko, t.kode_otp, t.otp_updated_at 
             FROM toko t WHERE t.aktif=1 ORDER BY t.kode_toko"
        );
        $flash = $this->getFlash();
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/otp/manage.php';
        require_once __DIR__ . '/../views/layout/footer.php';
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
            $this->setFlash('success', "OTP toko {$kode} berhasil diperbarui menjadi {$newOtp}.");
        } else {
            $this->setFlash('error', 'OTP harus tepat 6 digit angka.');
        }
        $this->redirect(BASE_URL . '/index.php?page=otp&action=manage');
    }
}
