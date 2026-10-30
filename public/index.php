<?php
require_once __DIR__ . '/../app/public_helpers.php';
expire_keeps(db());
$error = take_public_error();

// Memuat Konfigurasi Database
require_once __DIR__ . '/../config/db.php';

// Memuat Navbar Transparan
require_once __DIR__ . '/../app/views/layouts/header.php';

// Memuat Konten Halaman Utama (Beranda)
require_once __DIR__ . '/../app/views/home/index.php';

// Memuat Footer
require_once __DIR__ . '/../app/views/layouts/footer.php';
?>
