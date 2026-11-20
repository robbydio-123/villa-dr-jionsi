<?php

require_once __DIR__ . '/../app/admin_dashboard.php';
require_admin();

$pdo = db();

// Dashboard hanya untuk memantau. Verifikasi bukti & pembatalan keep dilakukan di admin_reservasi.php.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    redirect_to('admin_reservasi.php');
}

expire_keeps($pdo); 
$data     = dash_data($pdo);
$settings = get_public_settings($pdo);
$flash    = take_flash();
$sekarang = new DateTimeImmutable('now');

require_once __DIR__ . '/../app/views/layouts/admin_header.php';
require_once __DIR__ . '/../app/views/admin/dashboard.php';
require_once __DIR__ . '/../app/views/layouts/admin_footer.php';