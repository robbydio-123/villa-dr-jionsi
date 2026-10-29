<?php
// Memanggil Layout Header (yang berisi link CSS dan Navbar)
require_once '../app/views/layouts/header.php';

require_once __DIR__ . '/../config/db.php';
// Memanggil Konten Formulir
require_once '../app/views/reservasi/formulir.php';

// Memanggil Layout Footer
require_once '../app/views/layouts/footer.php';
?>