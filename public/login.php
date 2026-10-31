<?php
require_once __DIR__ . '/../app/admin_auth.php';

if (!empty($_SESSION['admin_id'])) {
    redirect_to('admin.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $wa = trim((string)($_POST['nomor_whatsapp'] ?? ''));
    $pw = (string)($_POST['password'] ?? '');

    if (admin_login(db(), $wa, $pw)) {
        redirect_to('admin.php');
    }
    $error = 'Nomor WhatsApp atau kata sandi salah.';
}

require_once '../app/views/layouts/header.php';
require_once '../app/views/admin/login.php';
require_once '../app/views/layouts/footer.php';