<?php
require_once __DIR__ . '/../app/public_helpers.php';

$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        expire_keeps($pdo);
        $id = create_booking($pdo);
        set_public_booking($id);
        redirect_to('keep.php');
    } catch (InvalidArgumentException | DomainException $e) {
        public_error($e->getMessage());
        $query = http_build_query([
            'kamar_id' => (int)($_POST['kamar_id'] ?? 0),
            'checkin' => (string)($_POST['checkin'] ?? ''),
            'durasi' => (int)($_POST['durasi_jam'] ?? 24),
        ]);
        redirect_to('formulir.php?' . $query);
    } catch (Throwable $e) {
        error_log('[reservasi] Gagal membuat booking: ' . $e->getMessage());
        public_error('Reservasi tidak dapat diproses saat ini. Silakan coba beberapa saat lagi.');
        redirect_to('beranda.php');
    }
}

$booking = require_public_booking($pdo);
if (($booking['status_booking'] === 'Di-Keep'
        || ($booking['status_booking'] === 'Menunggu Konfirmasi' && $booking['status_verifikasi'] === 'Ditolak'))
    && strtotime((string)$booking['batas_waktu_keep']) <= time()) {
    expire_keeps($pdo);
    redirect_to('hasil_status.php?kode=' . rawurlencode($booking['kode_booking']));
}
if ($booking['status_booking'] !== 'Di-Keep'
    && !($booking['status_booking'] === 'Menunggu Konfirmasi' && $booking['status_verifikasi'] === 'Ditolak')) {
    redirect_to('hasil_status.php?kode=' . rawurlencode($booking['kode_booking']));
}
$error = take_public_error();
$settings = get_public_settings($pdo);
$whatsapp = preg_replace('/\D+/', '', (string)$settings['whatsapp_utama']);
$qrisStmt = $pdo->query('SELECT gambar FROM qris_statis WHERE aktif = TRUE LIMIT 1');
$activeQris = $qrisStmt->fetchColumn();
$pageTitle = 'Pembayaran Reservasi - Villa D-R Jionsi';
require_once __DIR__ . '/../app/views/layouts/header.php';
require __DIR__ . '/../app/views/reservasi/pembayaran.php';
require_once __DIR__ . '/../app/views/layouts/footer.php';
