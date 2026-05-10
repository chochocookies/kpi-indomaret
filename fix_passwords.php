<?php
// fix_passwords.php - Jalankan SEKALI saja untuk memperbaiki hash password
// Akses: http://localhost/kpi-indomaret/fix_passwords.php
// HAPUS file ini setelah dijalankan!

require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$password = 'password'; // Password default
$hash = password_hash($password, PASSWORD_DEFAULT);

$db->execute("UPDATE users SET password = ?", [$hash]);

$count = $db->fetch("SELECT COUNT(*) as c FROM users")['c'];
echo "<h2>✅ Password berhasil diupdate untuk $count user</h2>";
echo "<p>Password default: <strong>password</strong></p>";
echo "<p style='color:red'><strong>HAPUS file ini setelah dijalankan!</strong></p>";
echo "<p><a href='index.php?page=login'>→ Ke halaman Login</a></p>";
