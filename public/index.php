<?php

$pageTitle = 'Beranda';
ob_start();
require __DIR__ . '/../app/views/home/index.php';
$content = ob_get_clean();
require __DIR__ . '/../app/views/layouts/main.php';
