<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.gc_maxlifetime', (string)(14 * 86400));
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
    session_start();
}

const DURATION_PACKAGES  = [3, 6, 12, 24];
const MAX_BOOKING_NIGHTS = 30;

function valid_duration_hours(int $hours): bool
{
    if (in_array($hours, DURATION_PACKAGES, true)) {
        return true;
    }
    return $hours > 24 && $hours % 24 === 0 && $hours <= MAX_BOOKING_NIGHTS * 24;
}

function short_package_prices(PDO $pdo): array
{
    $rows = $pdo->query('SELECT durasi_jam, harga FROM harga_paket_jam')->fetchAll(PDO::FETCH_KEY_PAIR);
    return array_map('floatval', $rows);
}

function booking_total(float $nightRate, int $hours, array $shortPrices): float
{
    if ($hours >= 24) {
        return intdiv($hours, 24) * $nightRate;
    }
    if (!isset($shortPrices[$hours])) {
        throw new DomainException('Harga untuk durasi ini belum diatur. Silakan hubungi pengelola.');
    }
    return $shortPrices[$hours];
}

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function text_length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }
    $length = preg_match_all('/./us', $value);
    return $length === false ? PHP_INT_MAX : $length;
}

/**
 * Foto sampul kamar: foto_url di database, atau foto pertama di public/uploads/kamar/<nomor_kamar>/.
 * Mengembalikan jalur relatif terhadap folder public, atau '' bila belum ada foto.
 */
function room_cover_url(array $room): string
{
    $public = dirname(__DIR__) . '/public';
    $foto   = (string)($room['foto_url'] ?? '');
    if ($foto !== '' && is_file($public . $foto)) {
        return ltrim($foto, '/');
    }

    $folder = preg_replace('/[^A-Za-z0-9_-]/', '', (string)($room['nomor_kamar'] ?? ''));
    $dir    = $public . '/uploads/kamar/' . $folder;

    if ($folder !== '' && is_dir($dir)) {
        $files = scandir($dir) ?: [];
        natcasesort($files);

        foreach ($files as $f) {
            if (preg_match('/\.(jpe?g|png|webp)$/i', $f)) {
                return 'uploads/kamar/' . $folder . '/' . rawurlencode($f);
            }
        }
    }

    return '';
}

function getFasilitasKamar(string $deskripsi): array
{
    $master = [
        ['icon' => 'bi-droplet-half', 'label' => 'Air Panas', 'pattern' => '/air panas/i'],
        ['icon' => 'bi-tv', 'label' => 'Smart TV', 'pattern' => '/smart tv/i'],
        ['icon' => 'bi-wifi', 'label' => 'Wi-Fi', 'pattern' => '/wi-?fi/i'],
        ['icon' => 'bi-door-closed', 'label' => 'KM Dalam', 'pattern' => '/kamar mandi dalam|km dalam/i'],
        ['icon' => 'bi-cup-hot', 'label' => 'Dapur Bersama', 'pattern' => '/dapur bersama|dapur/i'],
        ['icon' => 'bi-image', 'label' => 'Pemandangan', 'pattern' => '/pemandangan|pegunungan|gunung/i'],
    ];

    $facilities = [];
    foreach ($master as $facility) {
        if (preg_match($facility['pattern'], $deskripsi)) {
            $facilities[] = ['icon' => $facility['icon'], 'label' => $facility['label']];
        }
    }

    if (!$facilities) {
        $facilities = [
            ['icon' => 'bi-droplet-half', 'label' => 'Air Panas'],
            ['icon' => 'bi-tv', 'label' => 'Smart TV'],
        ];
    }

    return $facilities;
}

function fasilitas_publik(string $kategori): array
{
    try {
        $stmt = db()->prepare(
            'SELECT nama, deskripsi, ikon FROM fasilitas
             WHERE kategori = :k AND aktif IS TRUE AND dihapus_pada IS NULL
             ORDER BY id'
        );
        $stmt->execute(['k' => $kategori]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log('[fasilitas_publik] ' . $e->getMessage());
        return [];
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(400);
        exit('Permintaan tidak valid. Muat ulang halaman, lalu coba kembali.');
    }
}

function public_error(string $message): void
{
    $_SESSION['public_error'] = $message;
}

function take_public_error(): ?string
{
    $message = $_SESSION['public_error'] ?? null;
    unset($_SESSION['public_error']);
    return is_string($message) ? $message : null;
}

function redirect_to(string $url): void
{
    header('Location: ' . $url, true, 303);
    exit;
}

function expire_keeps(PDO $pdo): void
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->query("
            SELECT r.id, r.status_booking
            FROM reservasi r
            WHERE r.status_booking IN ('Di-Keep', 'Menunggu Konfirmasi')
              AND r.batas_waktu_keep <= LOCALTIMESTAMP
              AND (
                  r.status_booking = 'Di-Keep'
                  OR EXISTS (
                      SELECT 1 FROM pembayaran p
                      WHERE p.reservasi_id = r.id
                        AND p.id = (
                            SELECT p2.id FROM pembayaran p2
                            WHERE p2.reservasi_id = r.id
                            ORDER BY p2.created_at DESC, p2.id DESC LIMIT 1
                        )
                        AND p.status_verifikasi = 'Ditolak'
                  )
              )
            ORDER BY r.id
            FOR UPDATE
        ");

        $expired = $stmt->fetchAll();

        if ($expired) {
            $update = $pdo->prepare("UPDATE reservasi SET status_booking = 'Kedaluwarsa' WHERE id = :id");
            $log = $pdo->prepare("
                INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru)
                VALUES (:id, :old_status, 'Kedaluwarsa')
            ");

            foreach ($expired as $row) {
                $update->execute(['id' => $row['id']]);
                $log->execute(['id' => $row['id'], 'old_status' => $row['status_booking']]);
            }
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function get_public_settings(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM pengaturan_villa WHERE id = 1');
    $settings = $stmt->fetch();

    return $settings ?: [
        'nama_properti' => 'Villa D-R Jionsi',
        'kode_properti' => 'DRJ',
        'batas_waktu_keep_menit' => 15,
        'batas_refund_hari_sebelum' => 1,
        'whatsapp_utama' => '',
    ];
}

/**
 * Pengaturan villa untuk tampilan (di-cache per permintaan).
 * Dipakai header, footer, beranda, dll agar nama/kontak selalu mengikuti Pengaturan Sistem.
 */
function site_settings(): array
{
    static $cache = null;

    if ($cache === null) {
        try {
            $cache = get_public_settings(db());
        } catch (Throwable $e) {
            $cache = ['nama_properti' => 'Villa D-R Jionsi', 'batas_refund_hari_sebelum' => 1];
        }
    }
    return $cache;
}

function profile_photo_url(): string
{
    $projectDir = dirname(__DIR__);
    $fallback = 'uploads/kamar/kamar-3705b736221bcd06.jpg';
    $metadataPath = $projectDir . '/storage/profil-villa.json';

    if (is_file($metadataPath)) {
        $metadata = json_decode((string)file_get_contents($metadataPath), true);
        $filename = is_array($metadata) ? (string)($metadata['file'] ?? '') : '';
        if (preg_match('/^profil-[a-f0-9]{16}\.(jpg|png|webp)$/', $filename)) {
            $candidate = 'uploads/profil-villa/' . $filename;
            if (is_file($projectDir . '/public/' . $candidate)) {
                return $candidate;
            }
        }
    }

    if (is_file($projectDir . '/public/' . $fallback)) {
        return $fallback;
    }

    return 'assets/img/logo dr-jionsi.jpeg';
}

const HERO_MIN_PHOTOS = 4;
const HERO_MAX_PHOTOS = 6;

function home_hero_photo_urls(): array
{
    $projectDir = dirname(__DIR__);
    $metadataPath = $projectDir . '/storage/hero-beranda.json';

    if (is_file($metadataPath)) {
        $metadata = json_decode((string)file_get_contents($metadataPath), true);
        $filenames = is_array($metadata) ? ($metadata['files'] ?? [$metadata['file'] ?? '']) : [];
        $photos = [];
        foreach ((array)$filenames as $filename) {
            if (is_string($filename) && preg_match('/^hero-[a-f0-9]{16}\.(jpg|png|webp)$/', $filename)) {
                $candidate = 'uploads/hero-beranda/' . $filename;
                if (is_file($projectDir . '/public/' . $candidate)) {
                    $photos[] = $candidate;
                }
            }
        }
        if ($photos) {
            return array_slice($photos, 0, HERO_MAX_PHOTOS);
        }
    }

    return [profile_photo_url()];
}

/** True bila foto hero memang diunggah admin (bukan cadangan foto profil). */
function hero_photos_configured(): bool
{
    $metadataPath = dirname(__DIR__) . '/storage/hero-beranda.json';
    if (!is_file($metadataPath)) {
        return false;
    }
    $metadata = json_decode((string)file_get_contents($metadataPath), true);
    $filenames = is_array($metadata) ? ($metadata['files'] ?? [$metadata['file'] ?? '']) : [];
    foreach ((array)$filenames as $filename) {
        if (is_string($filename) && preg_match('/^hero-[a-f0-9]{16}\.(jpg|png|webp)$/', $filename)
            && is_file(dirname(__DIR__) . '/public/uploads/hero-beranda/' . $filename)) {
            return true;
        }
    }
    return false;
}

function home_hero_photo_url(): string
{
    return home_hero_photo_urls()[0];
}

function villa_name(): string
{
    $nama = trim((string)(site_settings()['nama_properti'] ?? ''));
    return $nama !== '' ? $nama : 'Villa D-R Jionsi';
}

/** Nama singkat untuk navbar/sidebar: "Villa D-R Jionsi" -> "D-R Jionsi". */
function villa_brand(): string
{
    $short = trim(preg_replace('/^villa\s+/i', '', villa_name()));
    return $short !== '' ? $short : villa_name();
}

function villa_setting(string $key, string $fallback = ''): string
{
    $v = trim((string)(site_settings()[$key] ?? ''));
    return $v !== '' ? $v : $fallback;
}

function booking_by_id(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("
        SELECT r.*, k.nomor_kamar, k.tipe_kamar, k.lantai, k.kapasitas AS kapasitas_kamar,
               k.foto_url, k.deskripsi,
               p.id AS pembayaran_id, p.bukti_file, p.nominal AS nominal_pembayaran,
               p.tanggal_pembayaran, p.status_verifikasi, p.created_at AS pembayaran_created_at,
               p.qris_id, q.gambar AS qris_gambar,
               rf.id AS refund_id, rf.status AS refund_status, rf.alasan AS refund_alasan,
               rf.diajukan_at AS refund_diajukan_at
        FROM reservasi r
        JOIN kamar k ON k.id = r.kamar_id
        LEFT JOIN LATERAL (
            SELECT * FROM pembayaran WHERE reservasi_id = r.id ORDER BY created_at DESC, id DESC LIMIT 1
        ) p ON TRUE
        LEFT JOIN qris_statis q ON q.id = p.qris_id
        LEFT JOIN LATERAL (
            SELECT * FROM refund WHERE reservasi_id = r.id ORDER BY diajukan_at DESC, id DESC LIMIT 1
        ) rf ON TRUE
        WHERE r.id = :id
    ");

    $stmt->execute(['id' => $id]);
    $booking = $stmt->fetch();

    return $booking ?: null;
}

function booking_by_whatsapp(PDO $pdo, string $phone): ?array
{
    $stmt = $pdo->prepare("
        SELECT id FROM reservasi
        WHERE whatsapp_pemesan = :phone
        ORDER BY id DESC
        LIMIT 1
    ");

    $stmt->execute(['phone' => $phone]);
    $id = $stmt->fetchColumn();

    return $id === false ? null : booking_by_id($pdo, (int)$id);
}

function current_public_booking(PDO $pdo): ?array
{
    $id = (int)($_SESSION['public_booking_id'] ?? 0);
    return $id > 0 ? booking_by_id($pdo, $id) : null;
}

function set_public_booking(int $id): void
{
    session_regenerate_id(true);
    $_SESSION['public_booking_id'] = $id;
    $_SESSION['authorized_booking_id'] = $id;
}

function require_public_booking(PDO $pdo): array
{
    $booking = current_public_booking($pdo);

    if (!$booking) {
        public_error('Sesi reservasi tidak ditemukan. Silakan mulai pemesanan dari katalog kamar.');
        redirect_to('beranda.php');
    }

    return $booking;
}

function booking_datetime(string $value): ?DateTimeImmutable
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $value);
    $errors = DateTimeImmutable::getLastErrors();

    if (
        !$date ||
        ($errors !== false && ($errors['warning_count'] || $errors['error_count'])) ||
        $date->format('Y-m-d\TH:i') !== $value
    ) {
        return null;
    }

    return $date;
}

function create_booking(PDO $pdo): int
{
    verify_csrf();

    $roomId = filter_var($_POST['kamar_id'] ?? null, FILTER_VALIDATE_INT);
    $duration = filter_var($_POST['durasi_jam'] ?? null, FILTER_VALIDATE_INT);
    $guests = filter_var($_POST['jumlah_tamu'] ?? null, FILTER_VALIDATE_INT);
    $name = trim((string)($_POST['nama'] ?? ''));
    $phone = preg_replace('/[\s()+.-]/', '', (string)($_POST['whatsapp'] ?? ''));
    $note = trim((string)($_POST['catatan'] ?? ''));
    $checkin = booking_datetime((string)($_POST['checkin'] ?? ''));

    if (
        !$roomId || !$duration || !valid_duration_hours((int)$duration)
        || !$guests || $guests < 1
        || text_length($name) < 3 || text_length($name) > 100
        || !preg_match('/^\+?\d{9,15}$/', $phone)
        || text_length($note) > 250
        || !isset($_POST['setuju_kebijakan'])
    ) {
        throw new InvalidArgumentException('Periksa kembali nama, WhatsApp, jadwal, jumlah tamu, dan persetujuan kebijakan.');
    }

    $phone = ltrim($phone, '+');

    // Seragamkan ke format internasional: 0812... atau 812... menjadi 62812...
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    } elseif (substr($phone, 0, 2) !== '62') {
        $phone = '62' . $phone;
    }

    if (strlen($phone) < 9 || strlen($phone) > 15) {
        throw new InvalidArgumentException('Nomor WhatsApp tidak valid.');
    }

    if (!$checkin || $checkin <= new DateTimeImmutable('now')) {
        throw new InvalidArgumentException('Waktu check-in harus valid dan berada di masa mendatang.');
    }

    $checkout = $checkin->modify('+' . $duration . ' hours');

    $pdo->beginTransaction();
    try {
        $roomStmt = $pdo->prepare("
            SELECT * FROM kamar WHERE id = :id AND status = 'Tersedia' AND dihapus_pada IS NULL FOR UPDATE
        ");
        $roomStmt->execute(['id' => $roomId]);
        $room = $roomStmt->fetch();

        if (!$room) {
            throw new InvalidArgumentException('Kamar ini sedang tidak tersedia untuk dipesan.');
        }

        if ($guests > (int)$room['kapasitas']) {
            throw new InvalidArgumentException('Jumlah tamu melebihi kapasitas kamar.');
        }

        $settings = get_public_settings($pdo);
        $keepMinutes = max(1, (int)$settings['batas_waktu_keep_menit']);
        $total = booking_total((float)$room['harga_per_malam'], (int)$duration, short_package_prices($pdo));
        $roomCode = preg_replace('/[^A-Za-z0-9]/', '', (string)$room['nomor_kamar']);

        $insert = $pdo->prepare("
            INSERT INTO reservasi
                (kode_booking, kamar_id, nama_pemesan, whatsapp_pemesan, jumlah_tamu, catatan,
                 waktu_checkin, durasi_jam, waktu_checkout, total_tagihan, status_booking,
                 batas_waktu_keep, setuju_kebijakan)
            VALUES
                (:kode, :kamar_id, :nama, :whatsapp, :jumlah_tamu, :catatan,
                 :checkin, :durasi, :checkout, :total, 'Di-Keep',
                 LOCALTIMESTAMP + make_interval(mins => :keep_minutes), TRUE)
            RETURNING id
        ");

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $code = sprintf(
                '%s-%s-K%s-%s',
                preg_replace('/[^A-Z0-9]/i', '', (string)$settings['kode_properti']),
                $checkin->format('Ymd'),
                $roomCode,
                strtoupper(bin2hex(random_bytes(2)))
            );

            try {
                $insert->execute([
                    'kode' => $code,
                    'kamar_id' => $roomId,
                    'nama' => $name,
                    'whatsapp' => $phone,
                    'jumlah_tamu' => $guests,
                    'catatan' => $note !== '' ? $note : null,
                    'checkin' => $checkin->format('Y-m-d H:i:s'),
                    'durasi' => $duration,
                    'checkout' => $checkout->format('Y-m-d H:i:s'),
                    'total' => $total,
                    'keep_minutes' => $keepMinutes,
                ]);
                break;
            } catch (PDOException $e) {
                if ($e->getCode() === '23505' && $attempt < 2) {
                    continue;
                }
                throw $e;
            }
        }

        $id = (int)$insert->fetchColumn();

        $log = $pdo->prepare("
            INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru)
            VALUES (:id, NULL, 'Di-Keep')
        ");
        $log->execute(['id' => $id]);

        $pdo->commit();
        return $id;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if ($e instanceof PDOException && $e->getCode() === '23P01') {
            throw new DomainException('Jadwal kamar tersebut baru saja dipesan orang lain. Silakan pilih jadwal atau kamar lain.');
        }

        throw $e;
    }
}

function store_payment_proof(PDO $pdo, array $booking): void
{
    verify_csrf();

    if (strtotime((string)$booking['batas_waktu_keep']) <= time()) {
        expire_keeps($pdo);
        throw new DomainException('Waktu keep sudah berakhir. Kamar telah dilepas kembali ke katalog.');
    }

    $file = $_FILES['bukti_transfer'] ?? null;

    if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        if ($file && in_array($file['error'] ?? null, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            throw new InvalidArgumentException('Ukuran bukti pembayaran maksimal 2 MB.');
        }
        throw new InvalidArgumentException('Pilih bukti pembayaran yang valid untuk diunggah.');
    }

    if ((int)$file['size'] < 1 || (int)$file['size'] > 2 * 1024 * 1024) {
        throw new InvalidArgumentException('Ukuran bukti pembayaran maksimal 2 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    if (!isset($types[$mime]) || @getimagesize($file['tmp_name']) === false) {
        throw new InvalidArgumentException('Bukti pembayaran harus berupa gambar JPG, PNG, atau WEBP.');
    }

    $lastPayment = $pdo->prepare("
        SELECT status_verifikasi FROM pembayaran
        WHERE reservasi_id = :id ORDER BY created_at DESC, id DESC LIMIT 1
    ");
    $lastPayment->execute(['id' => $booking['id']]);
    $lastStatus = $lastPayment->fetchColumn();

    if ($lastStatus !== false && $lastStatus !== 'Ditolak') {
        throw new DomainException('Bukti pembayaran Anda sedang diproses atau sudah terverifikasi.');
    }

    if (
        $booking['status_booking'] !== 'Di-Keep'
        && !($booking['status_booking'] === 'Menunggu Konfirmasi' && $lastStatus === 'Ditolak')
    ) {
        throw new DomainException('Masa pemesanan sudah berakhir atau pembayaran sedang menunggu verifikasi.');
    }

    $directory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'bukti-pembayaran';

    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        throw new RuntimeException('Folder penyimpanan bukti pembayaran tidak dapat dibuat.');
    }

    $filename = bin2hex(random_bytes(24)) . '.' . $types[$mime];
    $destination = $directory . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Bukti pembayaran gagal disimpan.');
    }

    try {
        $pdo->beginTransaction();

        $reservationLock = $pdo->prepare('SELECT status_booking FROM reservasi WHERE id = :id FOR UPDATE');
        $reservationLock->execute(['id' => $booking['id']]);
        $lockedBookingStatus = $reservationLock->fetchColumn();

        $lastPayment->execute(['id' => $booking['id']]);
        $lockedPaymentStatus = $lastPayment->fetchColumn();

        if (
            strtotime((string)$booking['batas_waktu_keep']) <= time()
            || !($lockedBookingStatus === 'Di-Keep'
                || ($lockedBookingStatus === 'Menunggu Konfirmasi' && $lockedPaymentStatus === 'Ditolak'))
            || ($lockedPaymentStatus !== false && $lockedPaymentStatus !== 'Ditolak')
        ) {
            throw new DomainException('Reservasi tidak lagi dapat menerima bukti pembayaran.');
        }

        $qrisStmt = $pdo->query('SELECT id FROM qris_statis WHERE aktif = TRUE LIMIT 1');
        $qrisId = $qrisStmt->fetchColumn();

        $payment = $pdo->prepare("
            INSERT INTO pembayaran (reservasi_id, qris_id, nominal, tanggal_pembayaran, bukti_file)
            VALUES (:reservasi_id, :qris_id, :nominal, CURRENT_DATE, :bukti_file)
        ");

        $payment->execute([
            'reservasi_id' => $booking['id'],
            'qris_id' => $qrisId === false ? null : $qrisId,
            'nominal' => $booking['total_tagihan'],
            'bukti_file' => 'storage/bukti-pembayaran/' . $filename,
        ]);

        if ($lockedPaymentStatus === 'Ditolak') {
            $oldStatus = 'Menunggu Konfirmasi';
            $log = $pdo->prepare("
                INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru)
                VALUES (:id, :old_status, :new_status)
            ");
            $log->execute([
                'id' => $booking['id'],
                'old_status' => $oldStatus,
                'new_status' => $oldStatus,
            ]);
        } else {
            $pdo->prepare("
                UPDATE reservasi SET status_booking = 'Menunggu Konfirmasi' WHERE id = :id
            ")->execute(['id' => $booking['id']]);

            $pdo->prepare("
                INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru)
                VALUES (:id, 'Di-Keep', 'Menunggu Konfirmasi')
            ")->execute(['id' => $booking['id']]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if (is_file($destination)) {
            unlink($destination);
        }

        throw $e;
    }
}

function refund_deadline(array $booking, array $settings): DateTimeImmutable
{
    $days = max(0, (int)$settings['batas_refund_hari_sebelum']);
    $deadline = (new DateTimeImmutable((string)$booking['created_at']))->modify('+' . $days . ' days');
    $checkin  = new DateTimeImmutable((string)$booking['waktu_checkin']);

    return $deadline < $checkin ? $deadline : $checkin;
}

/** Daftar bank dan dompet digital yang bisa dipilih tamu sebagai tujuan refund. */
function refund_bank_options(): array
{
    return [
        'BCA', 'BRI', 'BNI', 'Mandiri', 'BSI', 'CIMB Niaga', 'Permata', 'Danamon', 'BTN',
        'SeaBank', 'Bank Jago', 'Jenius (BTPN)', 'DANA', 'OVO', 'GoPay', 'ShopeePay', 'Lainnya',
    ];
}

/** Tambahkan kolom rekening tujuan refund bila belum ada. Aman dijalankan berulang. */
function refund_pastikan_kolom_rekening(PDO $pdo): void
{
    static $sudah = false;
    if ($sudah) {
        return;
    }
    $sudah = true;

    try {
        $ada = $pdo->query("
            SELECT 1 FROM information_schema.columns
            WHERE table_schema = current_schema() AND table_name = 'refund' AND column_name = 'no_rekening'
        ")->fetchColumn();
        if ($ada === false) {
            $pdo->exec('ALTER TABLE refund
                ADD COLUMN bank_tujuan           VARCHAR(60),
                ADD COLUMN no_rekening           VARCHAR(30),
                ADD COLUMN nama_pemilik_rekening VARCHAR(100),
                ADD COLUMN ditransfer_at         TIMESTAMP');
        }
    } catch (Throwable $e) {
        error_log('[refund] Gagal menyiapkan kolom rekening: ' . $e->getMessage());
    }
}

function submit_refund(PDO $pdo, array $booking): void
{
    verify_csrf();
    refund_pastikan_kolom_rekening($pdo);

    $reason = trim((string)($_POST['alasan'] ?? ''));
    if (text_length($reason) < 5 || text_length($reason) > 2000) {
        throw new InvalidArgumentException('Alasan refund harus berisi 5 sampai 2.000 karakter.');
    }

    $bank = trim((string)($_POST['bank_tujuan'] ?? ''));
    if (!in_array($bank, refund_bank_options(), true)) {
        throw new InvalidArgumentException('Pilih bank atau dompet digital tujuan refund.');
    }
    if ($bank === 'Lainnya') {
        $bankLain = trim((string)($_POST['bank_lain'] ?? ''));
        if (text_length($bankLain) < 2 || text_length($bankLain) > 50) {
            throw new InvalidArgumentException('Tuliskan nama bank tujuan (2 sampai 50 karakter).');
        }
        $bank = $bankLain;
    }

    $noRek = preg_replace('/[\s.-]/', '', (string)($_POST['no_rekening'] ?? ''));
    if (!preg_match('/^[0-9]{5,20}$/', (string)$noRek)) {
        throw new InvalidArgumentException('Nomor rekening hanya boleh berisi angka (5 sampai 20 digit).');
    }

    $namaRek = trim((string)preg_replace('/\s+/', ' ', (string)($_POST['nama_pemilik_rekening'] ?? '')));
    if (text_length($namaRek) < 3 || text_length($namaRek) > 100) {
        throw new InvalidArgumentException('Nama pemilik rekening harus berisi 3 sampai 100 karakter.');
    }

    if ($booking['status_booking'] !== 'Dibayar' || $booking['status_verifikasi'] !== 'Terverifikasi') {
        throw new DomainException('Refund hanya dapat diajukan untuk reservasi yang sudah dibayar dan terverifikasi.');
    }

    if (!empty($booking['refund_id']) && !in_array($booking['refund_status'], ['Ditolak'], true)) {
        throw new DomainException('Pengajuan refund untuk reservasi ini sudah tercatat.');
    }

    $settings = get_public_settings($pdo);
    $deadline = refund_deadline($booking, $settings);

    if (new DateTimeImmutable('now') > $deadline) {
        throw new DomainException('Batas pengajuan refund telah lewat.');
    }

    $pdo->beginTransaction();
    try {
        $statusStmt = $pdo->prepare('SELECT status_booking FROM reservasi WHERE id = :id FOR UPDATE');
        $statusStmt->execute(['id' => $booking['id']]);
        $currentStatus = $statusStmt->fetchColumn();

        $latestRefund = $pdo->prepare("
            SELECT status FROM refund WHERE reservasi_id = :id ORDER BY diajukan_at DESC, id DESC LIMIT 1
        ");
        $latestRefund->execute(['id' => $booking['id']]);
        $currentRefundStatus = $latestRefund->fetchColumn();

        if ($currentStatus !== 'Dibayar' || !in_array($currentRefundStatus, [false, 'Ditolak'], true)) {
            throw new DomainException('Reservasi tidak lagi memenuhi syarat untuk pengajuan refund.');
        }

        if (new DateTimeImmutable('now') > $deadline) {
            throw new DomainException('Batas pengajuan refund telah lewat.');
        }

        $insert = $pdo->prepare("
            INSERT INTO refund (reservasi_id, alasan, bank_tujuan, no_rekening, nama_pemilik_rekening)
            VALUES (:id, :alasan, :bank, :norek, :nama)
        ");
        $insert->execute([
            'id' => $booking['id'], 'alasan' => $reason,
            'bank' => $bank, 'norek' => $noRek, 'nama' => $namaRek,
        ]);

        $update = $pdo->prepare("UPDATE reservasi SET status_booking = 'Refund Diajukan' WHERE id = :id");
        $update->execute(['id' => $booking['id']]);

        $log = $pdo->prepare("
            INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru)
            VALUES (:id, 'Dibayar', 'Refund Diajukan')
        ");
        $log->execute(['id' => $booking['id']]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}