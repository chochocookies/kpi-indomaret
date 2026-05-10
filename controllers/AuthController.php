<?php
require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController {

    public function index(): void {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect(BASE_URL . '/index.php?page=dashboard');
        }
        $flash = $this->getFlash();
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/index.php?page=login');
        }

        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Username dan password wajib diisi.');
            $this->redirect(BASE_URL . '/index.php?page=login');
        }

        $user = $this->db->fetch(
            "SELECT u.*, t.nama_toko FROM users u 
             LEFT JOIN toko t ON u.kode_toko = t.kode_toko 
             WHERE u.username = ? AND u.aktif = 1",
            [$username]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            $this->setFlash('error', 'Username atau password salah.');
            $this->redirect(BASE_URL . '/index.php?page=login');
        }

        // Set session
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['username']   = $user['username'];
        $_SESSION['nama']       = $user['nama_lengkap'];
        $_SESSION['role']       = $user['role'];
        $_SESSION['kode_toko']  = $user['kode_toko'];
        $_SESSION['nama_toko']  = $user['nama_toko'];

        // Update last login
        $this->db->execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);

        $this->redirect(BASE_URL . '/index.php?page=dashboard');
    }

    public function logout(): void {
        session_destroy();
        $this->redirect(BASE_URL . '/index.php?page=login');
    }
}
