<?php
// public/admin.php
// Memanggil Layout Khusus Admin (Bukan header pengunjung)
require_once '../app/views/layouts/admin_header.php';

// Memanggil Konten Dashboard Admin
require_once '../app/views/admin/dashboard.php';

// Memanggil Layout Khusus Admin
require_once '../app/views/layouts/admin_footer.php';
?>