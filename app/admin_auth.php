<?php
declare(strict_types=1);

// public_helpers sudah memuat db(), session_start(), csrf, h(), redirect_to()
require_once __DIR__ . '/public_helpers.php';

function admin_login(PDO $pdo, string $wa, string $password): bool
{
    $stmt = $pdo->prepare(
        'SELECT id, nama, password FROM admin_users WHERE nomor_whatsapp = :wa'
    );
    $stmt->execute(['wa' => $wa]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password'])) {
        return false;
    }

    session_regenerate_id(true); // cegah session fixation
    $_SESSION['admin_id']   = (int)$admin['id'];
    $_SESSION['admin_nama'] = $admin['nama'];
    return true;
}

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect_to('login.php');
    }
}