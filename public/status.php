<?php
require_once __DIR__ . '/../app/public_helpers.php';
$error = take_public_error();
$pageTitle = 'Cek Status Reservasi - Villa D-R Jionsi';
require_once __DIR__ . '/../app/views/layouts/header.php';
require __DIR__ . '/../app/views/reservasi/index.php';
require_once __DIR__ . '/../app/views/layouts/footer.php';
