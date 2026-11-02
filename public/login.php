<?php
require_once __DIR__ . '/../app/admin_auth.php';

if (!empty($_SESSION['admin_id'])) {
    redirect_to('admin.php');
}

$pdo      = db();
$username = '';
$galat    = false;   
$sesiHabis = (($_GET['sesi'] ?? '') === 'habis');
$sisaKunci = login_sisa_kunci($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $pw       = (string)($_POST['password'] ?? '');
    $ingat    = !empty($_POST['ingat']);

    $hasil = admin_login($pdo, $username, $pw, $ingat);
    if ($hasil === 'ok') {
        redirect_to('admin.php');
    }
    $galat     = ($hasil === 'gagal');
    $sisaKunci = login_sisa_kunci($pdo);
    $sesiHabis = false;
}

$terkunci  = $sisaKunci > 0;
$pageTitle = 'Portal Pengelola - Villa D-R Jionsi';

require_once __DIR__ . '/../app/views/admin/login.php';