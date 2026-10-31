<?php
require_once __DIR__ . '/../app/admin_auth.php';
require_admin();
// public/admin_reservasi.php
require_once '../app/views/layouts/admin_header.php';
require_once __DIR__ . '/../config/db.php';
require_once '../app/views/admin/reservasi.php';
require_once '../app/views/layouts/admin_footer.php';
?>