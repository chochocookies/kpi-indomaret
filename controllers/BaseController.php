<?php
class BaseController {
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function view(string $view, array $data = []): void {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) { die("View not found: $view"); }
        require_once __DIR__ . '/../views/layout/header.php';
        require_once $viewFile;
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    protected function json(array $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void {
        session_write_close(); // Ensure session is saved before redirect
        header("Location: $url");
        exit;
    }

    protected function getKodeToko(): ?string {
        if (in_array($_SESSION['role'] ?? '', ['superadmin', 'admin'])) {
            return $_GET['kode_toko'] ?? $_POST['kode_toko'] ?? null;
        }
        return $_SESSION['kode_toko'] ?? null;
    }

    protected function getBulan(): int {
        return (int)($_GET['bulan'] ?? $_POST['bulan'] ?? date('n'));
    }

    protected function getTahun(): int {
        return (int)($_GET['tahun'] ?? $_POST['tahun'] ?? date('Y'));
    }

    protected function requireLogin(): void {
        if (empty($_SESSION['user_id'])) {
            $this->redirect(BASE_URL . '/index.php?page=login');
        }
    }

    protected function requireRole(array $roles): void {
        $this->requireLogin();
        if (!in_array($_SESSION['role'] ?? '', $roles)) {
            $this->redirect(BASE_URL . '/index.php?page=dashboard');
        }
    }

    protected function isAdmin(): bool {
        return in_array($_SESSION['role'] ?? '', ['superadmin', 'admin']);
    }

    protected function setFlash(string $type, string $msg): void {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    protected function getFlash(): ?array {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Cek OTP. Jika belum valid:
     * - Simpan seluruh POST ke session
     * - Redirect ke halaman OTP
     * Setelah OTP benar, POST data dieksekusi otomatis tanpa ketik ulang.
     */
    protected function requireOtp(string $kode): void {
        if ($this->isAdmin()) return;
        $key = 'otp_ok_' . $kode;
        if (!empty($_SESSION[$key]) && (time() - $_SESSION[$key]) < 1800) return;

        // Simpan POST + URL tujuan ke session
        $_SESSION['otp_pending_post']   = $_POST;
        $_SESSION['otp_pending_action'] = $_SERVER['REQUEST_URI'];
        $_SESSION['otp_kode']           = $kode;

        $this->redirect(BASE_URL . '/index.php?page=otp&kode_toko=' . urlencode($kode));
    }

    protected function markOtpOk(string $kode): void {
        $_SESSION['otp_ok_' . $kode] = time();
    }
}
