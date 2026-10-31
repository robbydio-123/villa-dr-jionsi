<?php

declare(strict_types=1);
require_once __DIR__ . '/admin_auth.php';
function set_flash(string $type, string $message): void
{
    $_SESSION['admin_flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $flash = $_SESSION['admin_flash'] ?? null;
    unset($_SESSION['admin_flash']);
    return is_array($flash) ? $flash : null;
}

function validate_settings(array $in): array
{
    $errors = [];

    $nama   = trim((string)($in['nama_properti'] ?? ''));
    $wa     = trim((string)($in['whatsapp_utama'] ?? ''));
    $email  = trim((string)($in['email_operasional'] ?? ''));
    $alamat = trim((string)($in['alamat_lengkap'] ?? ''));
    $keep   = filter_var($in['batas_waktu_keep_menit'] ?? null, FILTER_VALIDATE_INT);

    if ($nama === '' || text_length($nama) > 100) {
        $errors[] = 'Nama properti wajib diisi (maksimal 100 karakter).';
    }
    if (!preg_match('/^\+?[0-9][0-9\s\-]{7,18}$/', $wa)) {
        $errors[] = 'Nomor WhatsApp tidak valid (8-20 karakter, hanya angka, spasi, atau tanda minus).';
    }
    if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || text_length($email) > 100)) {
        $errors[] = 'Format email operasional tidak valid.';
    }
    if (text_length($alamat) > 500) {
        $errors[] = 'Alamat terlalu panjang (maksimal 500 karakter).';
    }
    if ($keep === false || $keep < 1 || $keep > 180) {
        $errors[] = 'Batas waktu keep harus berupa angka 1-180 menit.';
    }

    return [$errors, [
        'nama_properti'             => $nama,
        'whatsapp_utama'            => $wa,
        'email_operasional'         => $email,
        'alamat_lengkap'            => $alamat,
        'batas_waktu_keep_menit'    => $keep === false ? 15 : $keep,
    ]];
}

function save_settings(PDO $pdo, array $d): void
{
    $stmt = $pdo->prepare('
        UPDATE pengaturan_villa SET
            nama_properti             = :nama,
            whatsapp_utama            = :wa,
            email_operasional         = :email,
            alamat_lengkap            = :alamat,
            batas_waktu_keep_menit    = :keep,
            updated_at                = CURRENT_TIMESTAMP
        WHERE id = 1
    ');
    $stmt->execute([
        'nama'   => $d['nama_properti'],
        'wa'     => $d['whatsapp_utama'],
        'email'  => $d['email_operasional'] === '' ? null : $d['email_operasional'],
        'alamat' => $d['alamat_lengkap'] === '' ? null : $d['alamat_lengkap'],
        'keep'   => $d['batas_waktu_keep_menit'],
    ]);
}

function get_active_qris(PDO $pdo): ?array
{
    $row = $pdo->query('SELECT id, gambar, created_at FROM qris_statis WHERE aktif = TRUE LIMIT 1')->fetch();
    return $row ?: null;
}

function upload_qris(PDO $pdo, array $file, int $adminId): void
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE) {
        throw new InvalidArgumentException('Pilih gambar QRIS terlebih dahulu.');
    }
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        throw new InvalidArgumentException('Ukuran gambar terlalu besar (maksimal 2 MB).');
    }
    if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        throw new InvalidArgumentException('Upload gagal. Silakan coba lagi.');
    }
    if ((int)$file['size'] > 2 * 1024 * 1024) {
        throw new InvalidArgumentException('Ukuran gambar terlalu besar (maksimal 2 MB).');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if ($ext === null) {
        throw new InvalidArgumentException('Format gambar harus JPG, PNG, atau WEBP.');
    }

    $dir = dirname(__DIR__) . '/public/uploads/qris';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('Folder upload QRIS tidak bisa dibuat.');
    }

    $name = 'qris-' . bin2hex(random_bytes(8)) . '.' . $ext;
    $path = $dir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new RuntimeException('Gagal menyimpan file QRIS.');
    }

    try {
        $pdo->beginTransaction();
        $pdo->exec('UPDATE qris_statis SET aktif = FALSE WHERE aktif = TRUE');
        $stmt = $pdo->prepare('
            INSERT INTO qris_statis (gambar, aktif, diunggah_oleh)
            VALUES (:gambar, TRUE, :admin)
        ');
        $stmt->execute(['gambar' => '/uploads/qris/' . $name, 'admin' => $adminId]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        @unlink($path); 
        throw $e;
    }
}