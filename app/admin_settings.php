<?php

declare(strict_types=1);
require_once __DIR__ . '/admin_auth.php';
function set_flash(string $type, string $message, ?int $waPembayaranId = null, string $waLabel = ''): void
{
    $_SESSION['admin_flash'] = [
        'type' => $type,
        'message' => $message,
        'wa_pembayaran_id' => $waPembayaranId,
        'wa_label' => $waLabel,
    ];
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
    $refund = filter_var($in['batas_refund_hari_sebelum'] ?? null, FILTER_VALIDATE_INT);

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
    if ($refund === false || $refund < 0 || $refund > 30) {
        $errors[] = 'Batas pengajuan refund harus berupa angka 0-30 hari.';
    }

    return [$errors, [
        'nama_properti'             => $nama,
        'whatsapp_utama'            => $wa,
        'email_operasional'         => $email,
        'alamat_lengkap'            => $alamat,
        'batas_waktu_keep_menit'    => $keep === false ? 15 : $keep,
        'batas_refund_hari_sebelum' => $refund === false ? 1 : $refund,
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
            batas_refund_hari_sebelum = :refund,
            updated_at                = CURRENT_TIMESTAMP
        WHERE id = 1
    ');
    $stmt->execute([
        'nama'   => $d['nama_properti'],
        'wa'     => $d['whatsapp_utama'],
        'email'  => $d['email_operasional'] === '' ? null : $d['email_operasional'],
        'alamat' => $d['alamat_lengkap'] === '' ? null : $d['alamat_lengkap'],
        'keep'   => $d['batas_waktu_keep_menit'],
        'refund' => $d['batas_refund_hari_sebelum'],
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

function upload_site_photo(array $file, string $type): void
{
    $targets = [
        'profil' => ['directory' => 'profil-villa', 'metadata' => 'profil-villa.json', 'prefix' => 'profil'],
        'hero' => ['directory' => 'hero-beranda', 'metadata' => 'hero-beranda.json', 'prefix' => 'hero'],
    ];
    if (!isset($targets[$type])) {
        throw new InvalidArgumentException('Jenis foto tidak dikenali.');
    }
    $target = $targets[$type];
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE) {
        throw new InvalidArgumentException('Pilih foto terlebih dahulu.');
    }
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE || (int)($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new InvalidArgumentException('Ukuran foto terlalu besar (maksimal 5 MB).');
    }
    if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'] ?? '')) {
        throw new InvalidArgumentException('Upload foto gagal. Silakan coba lagi.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if ($ext === null || @getimagesize($file['tmp_name']) === false) {
        throw new InvalidArgumentException('Format foto harus JPG, PNG, atau WEBP yang valid.');
    }

    $projectDir = dirname(__DIR__);
    $uploadDir = $projectDir . '/public/uploads/' . $target['directory'];
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        throw new RuntimeException('Folder foto tidak bisa dibuat.');
    }

    $filename = $target['prefix'] . '-' . bin2hex(random_bytes(8)) . '.' . $ext;
    $path = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new RuntimeException('Foto gagal disimpan.');
    }

    $storageDir = $projectDir . '/storage';
    if (!is_dir($storageDir) && !mkdir($storageDir, 0755, true)) {
        @unlink($path);
        throw new RuntimeException('Folder pengaturan foto villa tidak bisa dibuat.');
    }
    $metadataPath = $storageDir . '/' . $target['metadata'];
    $previous = is_file($metadataPath) ? json_decode((string)file_get_contents($metadataPath), true) : null;
    $saved = file_put_contents($metadataPath, json_encode(['file' => $filename], JSON_UNESCAPED_SLASHES), LOCK_EX);
    if ($saved === false) {
        @unlink($path);
        throw new RuntimeException('Pengaturan foto villa gagal disimpan.');
    }

    $oldFilename = is_array($previous) ? (string)($previous['file'] ?? '') : '';
    $oldPattern = '/^' . preg_quote($target['prefix'], '/') . '-[a-f0-9]{16}\.(jpg|png|webp)$/';
    if (preg_match($oldPattern, $oldFilename)) {
        @unlink($uploadDir . '/' . $oldFilename);
    }
}

function upload_profile_photo(array $file): void
{
    upload_site_photo($file, 'profil');
}

function update_home_hero_photos(array $files, array $deleteNames): void
{
    $namePattern = '/^hero-[a-f0-9]{16}\.(jpg|png|webp)$/';
    $projectDir  = dirname(__DIR__);
    $uploadDir   = $projectDir . '/public/uploads/hero-beranda';
    $storageDir  = $projectDir . '/storage';
    $metadataPath = $storageDir . '/hero-beranda.json';

    // Foto hero yang tersimpan saat ini (hanya yang valid dan filenya ada)
    $previous = is_file($metadataPath) ? json_decode((string)file_get_contents($metadataPath), true) : null;
    $current = [];
    foreach ((array)(is_array($previous) ? ($previous['files'] ?? [$previous['file'] ?? '']) : []) as $name) {
        if (is_string($name) && preg_match($namePattern, $name) && is_file($uploadDir . '/' . $name)) {
            $current[] = $name;
        }
    }

    // Foto yang dihapus: hanya nama yang memang ada di daftar saat ini
    $toDelete = [];
    foreach ($deleteNames as $name) {
        if (is_string($name) && in_array($name, $current, true)) {
            $toDelete[$name] = true;
        }
    }
    $kept = array_values(array_filter($current, static fn($n) => !isset($toDelete[$n])));

    // Normalisasi dan validasi unggahan baru
    $names = $files['name'] ?? [];
    if (!is_array($names)) {
        $files = [
            'name' => [$files['name'] ?? ''],
            'tmp_name' => [$files['tmp_name'] ?? ''],
            'error' => [$files['error'] ?? UPLOAD_ERR_NO_FILE],
            'size' => [$files['size'] ?? 0],
        ];
        $names = $files['name'];
    }
    $selected = [];
    foreach ($names as $index => $_) {
        $error = (int)($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE || (int)($files['size'][$index] ?? 0) > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('Ukuran setiap foto maksimal 5 MB.');
        }
        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($files['tmp_name'][$index] ?? '')) {
            throw new InvalidArgumentException('Upload foto hero gagal. Silakan coba lagi.');
        }
        $selected[] = $index;
    }

    $total = count($kept) + count($selected);
    if ($total < HERO_MIN_PHOTOS) {
        throw new InvalidArgumentException('Foto hero minimal ' . HERO_MIN_PHOTOS . ' gambar (sekarang ' . $total . ').');
    }
    if ($total > HERO_MAX_PHOTOS) {
        throw new InvalidArgumentException('Foto hero maksimal ' . HERO_MAX_PHOTOS . ' gambar (sekarang ' . $total . ').');
    }

    foreach ([$uploadDir, $storageDir] as $directory) {
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
            throw new RuntimeException('Folder penyimpanan foto hero tidak bisa dibuat.');
        }
    }

    $savedFiles = [];
    try {
        foreach ($selected as $index) {
            $tmpName = $files['tmp_name'][$index];
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmpName);
            $extension = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
            if ($extension === null || @getimagesize($tmpName) === false) {
                throw new InvalidArgumentException('Format foto harus JPG, PNG, atau WEBP yang valid.');
            }
            $filename = 'hero-' . bin2hex(random_bytes(8)) . '.' . $extension;
            if (!move_uploaded_file($tmpName, $uploadDir . '/' . $filename)) {
                throw new RuntimeException('Foto hero gagal disimpan.');
            }
            $savedFiles[] = $filename;
        }

        $metadata = json_encode(['files' => array_merge($kept, $savedFiles)], JSON_UNESCAPED_SLASHES);
        if ($metadata === false || file_put_contents($metadataPath, $metadata, LOCK_EX) === false) {
            throw new RuntimeException('Pengaturan foto hero gagal disimpan.');
        }
    } catch (Throwable $e) {
        foreach ($savedFiles as $filename) {
            @unlink($uploadDir . '/' . $filename);
        }
        throw $e;
    }

    // Hapus file fisik foto yang dihapus, setelah metadata berhasil disimpan
    foreach (array_keys($toDelete) as $name) {
        @unlink($uploadDir . '/' . $name);
    }
}