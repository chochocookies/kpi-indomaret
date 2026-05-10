<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/KpiModel.php';

class AdminController extends BaseController {
    private KpiModel $kpi;
    public function __construct() { parent::__construct(); $this->kpi = new KpiModel(); }

    public function index(): void {
        $this->requireRole(['superadmin', 'admin']);
        $this->toko();
    }

    public function toko(): void {
        $this->requireRole(['superadmin', 'admin']);
        $semuaToko = $this->kpi->getAllToko();
        $this->view('admin/toko', ['semuaToko' => $semuaToko, 'flash' => $this->getFlash()]);
    }

    public function users(): void {
        $this->requireRole(['superadmin', 'admin']);
        $users     = $this->db->fetchAll("SELECT u.*, t.nama_toko FROM users u LEFT JOIN toko t ON u.kode_toko=t.kode_toko ORDER BY u.role, u.nama_lengkap");
        $semuaToko = $this->kpi->getAllToko();
        $this->view('admin/users', ['users' => $users, 'semuaToko' => $semuaToko, 'flash' => $this->getFlash()]);
    }

    public function saveToko(): void {
        $this->requireRole(['superadmin', 'admin']);
        $id        = (int)($_POST['id'] ?? 0);
        $kode      = strtoupper(sanitize($_POST['kode_toko'] ?? ''));
        $nama      = sanitize($_POST['nama_toko'] ?? '');
        $alamat    = sanitize($_POST['alamat'] ?? '');
        $karyawan  = (int)($_POST['jumlah_karyawan'] ?? 0);

        try {
            if ($id > 0) {
                $this->db->execute(
                    "UPDATE toko SET nama_toko=?, alamat=?, jumlah_karyawan=? WHERE id=?",
                    [$nama, $alamat, $karyawan, $id]
                );
                $this->setFlash('success', 'Toko berhasil diperbarui.');
            } else {
                $this->db->insert(
                    "INSERT INTO toko (kode_toko, nama_toko, alamat, jumlah_karyawan) VALUES (?,?,?,?)",
                    [$kode, $nama, $alamat, $karyawan]
                );
                $this->setFlash('success', 'Toko berhasil ditambahkan.');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Gagal: '.$e->getMessage());
        }
        $this->redirect(BASE_URL.'/index.php?page=admin&action=toko');
    }

    public function deleteToko(): void {
        $this->requireRole(['superadmin']);
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->db->execute("UPDATE toko SET aktif=0 WHERE id=?", [$id]);
            $this->setFlash('success', 'Toko dinonaktifkan.');
        }
        $this->redirect(BASE_URL.'/index.php?page=admin&action=toko');
    }

    public function saveUser(): void {
        $this->requireRole(['superadmin', 'admin']);
        $id       = (int)($_POST['id'] ?? 0);
        $username = sanitize($_POST['username'] ?? '');
        $nama     = sanitize($_POST['nama_lengkap'] ?? '');
        $role     = sanitize($_POST['role'] ?? 'kepala_toko');
        $kode     = sanitize($_POST['kode_toko'] ?? '') ?: null;
        $pass     = $_POST['password'] ?? '';

        try {
            if ($id > 0) {
                if ($pass) {
                    $this->db->execute(
                        "UPDATE users SET nama_lengkap=?, role=?, kode_toko=?, password=? WHERE id=?",
                        [$nama, $role, $kode, password_hash($pass, PASSWORD_DEFAULT), $id]
                    );
                } else {
                    $this->db->execute(
                        "UPDATE users SET nama_lengkap=?, role=?, kode_toko=? WHERE id=?",
                        [$nama, $role, $kode, $id]
                    );
                }
                $this->setFlash('success', 'User berhasil diperbarui.');
            } else {
                if (!$pass) { $this->setFlash('error', 'Password wajib diisi untuk user baru.'); $this->redirect(BASE_URL.'/index.php?page=admin&action=users'); }
                $this->db->insert(
                    "INSERT INTO users (username, password, nama_lengkap, role, kode_toko) VALUES (?,?,?,?,?)",
                    [$username, password_hash($pass, PASSWORD_DEFAULT), $nama, $role, $kode]
                );
                $this->setFlash('success', 'User berhasil ditambahkan.');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Gagal: '.$e->getMessage());
        }
        $this->redirect(BASE_URL.'/index.php?page=admin&action=users');
    }

    public function deleteUser(): void {
        $this->requireRole(['superadmin']);
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->db->execute("UPDATE users SET aktif=0 WHERE id=?", [$id]);
            $this->setFlash('success', 'User dinonaktifkan.');
        }
        $this->redirect(BASE_URL.'/index.php?page=admin&action=users');
    }
}
