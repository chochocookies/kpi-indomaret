# KPI Monitor Indomaret

Sistem monitoring KPI berbasis PHP MVC + MySQL + Tailwind CSS untuk kepala toko dan admin Indomaret.

---

## 📋 Fitur

- **Dashboard Toko** – Ringkasan semua poin KPI bulan berjalan
- **Poin 1 SPD** – Input aktual harian offline, online, produk khusus + lihat target proporsional
- **Poin 2 NKL** – Input nota kurang lebih, kalkulasi budget otomatis 0,20% dari sales
- **Poin 3 NBR** – Input nota barang rusak, persentase otomatis, modul main & ACH
- **Poin 4 STD** – Input data L3M dan aktual untuk poinku, 2 item, i-payment, non tunai
- **Poin 5 Turn Over** – Catat karyawan keluar, poin otomatis
- **Rekap & WhatsApp** – Ringkasan siap salin/kirim via WhatsApp
- **Admin Dashboard** – Lihat semua toko sekaligus
- **Kelola Toko & User** – Tambah/edit toko dan akun kepala toko

---

## ⚙️ Instalasi (XAMPP)

### 1. Copy folder ke htdocs
```
C:\xampp\htdocs\kpi-indomaret\
```

### 2. Aktifkan mod_rewrite
Buka `C:\xampp\apache\conf\httpd.conf`, cari `AllowOverride None` pada bagian `htdocs`, ubah ke `AllowOverride All`.

### 3. Import database
- Buka **phpMyAdmin**: `http://localhost/phpmyadmin`
- Buat database baru: `kpi_indomaret`
- Import file: `database/kpi_indomaret.sql`

### 4. Konfigurasi koneksi
Edit `config/database.php` sesuai setting XAMPP Anda:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');       // Kosong jika tidak ada password
define('DB_NAME', 'kpi_indomaret');
```

### 5. Fix password hash
Akses: `http://localhost/kpi-indomaret/fix_passwords.php`
Setelah berhasil, **hapus file `fix_passwords.php`**.

### 6. Login
Akses: `http://localhost/kpi-indomaret/`

| Username    | Password   | Role        | Toko              |
|-------------|------------|-------------|-------------------|
| superadmin  | password   | Super Admin | Semua             |
| admin       | password   | Admin       | Semua             |
| fdnp        | password   | Kepala Toko | WIJAYA KUSUMA     |
| t6qa        | password   | Kepala Toko | KRESEK KRANGGAN   |
| t7u3        | password   | Kepala Toko | KRANGGAN JATIS.   |
| tpe9        | password   | Kepala Toko | KRANGGAN PERMAI 2 |
| *(dll...)*  |            |             |                   |

---

## 🏗️ Struktur Folder

```
kpi-indomaret/
├── config/
│   ├── app.php          # Konstanta, nama bulan, poin KPI
│   └── database.php     # Koneksi PDO MySQL
├── controllers/
│   ├── BaseController.php
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── SpdController.php
│   ├── NklController.php
│   ├── NbrController.php
│   ├── StdController.php
│   ├── TurnoverController.php
│   ├── SummaryController.php
│   └── AdminController.php
├── models/
│   └── KpiModel.php     # Model + kalkulasi KPI
├── views/
│   ├── layout/
│   │   ├── header.php   # Sidebar + topbar
│   │   ├── footer.php
│   │   └── period_selector.php
│   ├── auth/login.php
│   ├── dashboard/
│   │   ├── toko.php     # Dashboard kepala toko
│   │   ├── admin.php    # Dashboard semua toko
│   │   └── summary.php  # Rekap + WA
│   ├── spd/index.php
│   ├── nkl/index.php
│   ├── nbr/index.php
│   ├── std/index.php
│   ├── turnover/index.php
│   └── admin/
│       ├── toko.php
│       └── users.php
├── helpers/
│   └── functions.php    # Helper: format, hitung poin, dll.
├── database/
│   └── kpi_indomaret.sql
├── index.php            # Router utama
├── fix_passwords.php    # Jalankan sekali lalu hapus
└── .htaccess
```

---

## 📊 Mekanisme Poin KPI

| No | Komponen | Sub-poin | Poin |
|----|----------|----------|------|
| 1  | SPD Offline | ≥ 100% ACH = penuh, 95-99% = proporsional | 15 |
| 1  | SPD Online  | sama | 10 |
| 1  | SPD Produk Khusus | sama (jika ada modul) | 10 |
| 2  | NKL ALL | Jika audit: NKL ≥ 0 dan ≤ 0,20% sales | 10 |
| 2  | NKL Buah | Jika audit: NKL Buah ≥ 0 | 5 |
| 3  | NBR Dry | Persentase ≤ 0,1% dari sales nett dry | 5 |
| 3  | NBR Khusus & Perishable | Proporsional per modul: ACH/Main × 10 | 10 |
| 4  | STD Member Poinku | Aktual ≥ MAX(L3M) | 4 |
| 4  | STD Belanja >2 Item | Aktual ≥ MAX(L3M) | 4 |
| 4  | TRX I-Payment | Aktual ≥ MAX(L3M) | 4 |
| 4  | TRX Non Tunai | Aktual ≥ MAX(L3M) | 4 |
| 5  | Turn Over | Tidak ada karyawan keluar = 4 poin | 4 |

**Grade Insentif:**
- ≥ 90% → **Grade 1**
- ≥ 70% → **Grade 2**
- < 70% → **Tidak dapat insentif**

---

## 📱 Mobile-Friendly
Desain responsif untuk HP. Sidebar auto-collapse di layar kecil.

---

*Dibuat untuk monitoring KPI harian kepala toko Indomaret.*
