<?php
require_once __DIR__ . '/../app/public_helpers.php';
$pageTitle = 'Profil & Fasilitas - ' . villa_name();

// Memanggil Navigasi Atas
require_once __DIR__ . '/../app/views/layouts/header.php';

// Memanggil Konten Halaman Profil
require_once __DIR__ . '/../app/views/profil/index.php';

// Memanggil Navigasi Bawah
require_once __DIR__ . '/../app/views/layouts/footer.php';
?>