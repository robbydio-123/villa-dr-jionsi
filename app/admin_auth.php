<?php
declare(strict_types=1);

// public_helpers sudah memuat db(), session_start(), csrf, h(), redirect_to()
require_once __DIR__ . '/public_helpers.php';

const LOGIN_MAKS_GAGAL   = 3;     
const LOGIN_KUNCI_MENIT  = 5;    
const SESI_IDLE_DETIK    = 1800;  
const SESI_INGAT_HARI    = 14;    

function login_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

/** Sisa detik penguncian untuk IP ini. 0 = tidak terkunci. */
function login_sisa_kunci(PDO $pdo): int
{
    $stmt = $pdo->prepare(
        'SELECT GREATEST(0, CEIL(EXTRACT(EPOCH FROM (terkunci_sampai - NOW()))))::int
           FROM login_percobaan
          WHERE ip = :ip AND terkunci_sampai IS NOT NULL'
    );
    $stmt->execute(['ip' => login_ip()]);
    return (int)($stmt->fetchColumn() ?: 0);
}

function login_catat_gagal(PDO $pdo): void
{
    $ip = login_ip();

    // Bersihkan penguncian yang sudah lewat dan hitungan lama (>30 menit)
    $pdo->prepare(
        "DELETE FROM login_percobaan
          WHERE ip = :ip
            AND (terkunci_sampai <= NOW()
                 OR (terkunci_sampai IS NULL AND terakhir < NOW() - INTERVAL '30 minutes'))"
    )->execute(['ip' => $ip]);

    $pdo->prepare(
        'INSERT INTO login_percobaan (ip, gagal, terakhir) VALUES (:ip, 1, NOW())
         ON CONFLICT (ip) DO UPDATE
            SET gagal = login_percobaan.gagal + 1, terakhir = NOW()'
    )->execute(['ip' => $ip]);

    // Sudah mencapai batas -> kunci
    $pdo->prepare(
        sprintf(
            "UPDATE login_percobaan
                SET terkunci_sampai = NOW() + INTERVAL '%d minutes'
              WHERE ip = :ip AND gagal >= :maks AND terkunci_sampai IS NULL",
            LOGIN_KUNCI_MENIT
        )
    )->execute(['ip' => $ip, 'maks' => LOGIN_MAKS_GAGAL]);
}

/**
 * @return string 'ok' | 'gagal' | 'terkunci'
 */
function admin_login(PDO $pdo, string $username, string $password, bool $ingat = false): string
{
    if (login_sisa_kunci($pdo) > 0) {
        return 'terkunci';
    }

    $stmt = $pdo->prepare('SELECT id, nama, password FROM admin_users WHERE username = :u');
    $stmt->execute(['u' => $username]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password'])) {
        login_catat_gagal($pdo);
        return login_sisa_kunci($pdo) > 0 ? 'terkunci' : 'gagal';
    }

    // Berhasil: hapus catatan gagal
    $pdo->prepare('DELETE FROM login_percobaan WHERE ip = :ip')->execute(['ip' => login_ip()]);

    session_regenerate_id(true); // cegah session fixation
    $_SESSION['admin_id']    = (int)$admin['id'];
    $_SESSION['admin_nama']  = $admin['nama'];
    $_SESSION['admin_aktif'] = time();
    $_SESSION['admin_ingat'] = $ingat;

    if ($ingat) {
        setcookie(session_name(), session_id(), [
            'expires'  => time() + SESI_INGAT_HARI * 86400,
            'path'     => '/',
            'httponly' => true,
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax',
        ]);
    }
    return 'ok';
}

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect_to('login.php');
    }

    $sekarang = time();
    $idle = $sekarang - (int)($_SESSION['admin_aktif'] ?? $sekarang);
    if (empty($_SESSION['admin_ingat']) && $idle > SESI_IDLE_DETIK) {
        $_SESSION = [];
        session_destroy();
        redirect_to('login.php?sesi=habis');
    }
    $_SESSION['admin_aktif'] = $sekarang;
}