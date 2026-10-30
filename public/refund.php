<?php
require_once __DIR__ . '/../app/public_helpers.php';

$pdo = db();
$code = strtoupper(trim((string)($_GET['kode'] ?? $_POST['kode'] ?? '')));
$authorizedId = (int)($_SESSION['authorized_booking_id'] ?? 0);
$booking = $authorizedId > 0 ? booking_by_id($pdo, $authorizedId) : null;
if (!$booking || strcasecmp((string)$booking['kode_booking'], $code) !== 0) {
    public_error('Verifikasi kode booking dan nomor WhatsApp Anda sebelum mengajukan refund.');
    redirect_to('status.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        submit_refund($pdo, $booking);
        redirect_to('hasil_status.php?kode=' . rawurlencode($booking['kode_booking']));
    } catch (InvalidArgumentException | DomainException $e) {
        $error = $e->getMessage();
    } catch (Throwable $e) {
        error_log('[refund] Gagal membuat pengajuan: ' . $e->getMessage());
        $error = 'Pengajuan refund tidak dapat diproses saat ini. Silakan coba beberapa saat lagi.';
    }
}

$settings = get_public_settings($pdo);
$refundDeadline = (new DateTimeImmutable((string)$booking['waktu_checkin']))
    ->modify('-' . max(0, (int)$settings['batas_refund_hari_sebelum']) . ' days')
    ->setTime(23, 59, 59);
$pageTitle = 'Ajukan Refund - Villa D-R Jionsi';
require_once __DIR__ . '/../app/views/layouts/header.php';
require __DIR__ . '/../app/views/reservasi/refund.php';
require_once __DIR__ . '/../app/views/layouts/footer.php';
