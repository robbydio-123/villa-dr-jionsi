<?php
require_once __DIR__ . '/../app/public_helpers.php';

$pdo = db();
expire_keeps($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $code = strtoupper(trim((string)($_POST['kode'] ?? '')));
    $lastFour = trim((string)($_POST['phone'] ?? ''));
    if (!preg_match('/^[A-Z0-9-]{5,50}$/', $code) || !preg_match('/^\d{4}$/', $lastFour)) {
        public_error('Kode booking atau empat digit nomor WhatsApp tidak valid.');
        redirect_to('status.php');
    }
    $booking = booking_by_credentials($pdo, $code, $lastFour);
    if (!$booking) {
        public_error('Reservasi tidak ditemukan. Periksa kembali kode booking dan empat digit WhatsApp Anda.');
        redirect_to('status.php');
    }
    set_public_booking((int)$booking['id']);
    redirect_to('hasil_status.php?kode=' . rawurlencode($booking['kode_booking']));
}

$code = strtoupper(trim((string)($_GET['kode'] ?? '')));
$error = take_public_error();
$authorizedId = (int)($_SESSION['authorized_booking_id'] ?? 0);
$booking = $authorizedId > 0 ? booking_by_id($pdo, $authorizedId) : null;
if (!$booking || strcasecmp((string)$booking['kode_booking'], $code) !== 0) {
    public_error('Masukkan kode booking dan empat digit terakhir nomor WhatsApp untuk melihat status reservasi.');
    redirect_to('status.php');
}

$settings = get_public_settings($pdo);
$whatsapp = preg_replace('/\D+/', '', (string)$settings['whatsapp_utama']);
$historyStmt = $pdo->prepare("
    SELECT status_lama, status_baru, created_at
    FROM log_status_reservasi
    WHERE reservasi_id = :id
    ORDER BY created_at ASC, id ASC
");
$historyStmt->execute(['id' => $booking['id']]);
$statusHistory = $historyStmt->fetchAll();
$refundDeadline = (new DateTimeImmutable((string)$booking['waktu_checkin']))
    ->modify('-' . max(0, (int)$settings['batas_refund_hari_sebelum']) . ' days')
    ->setTime(23, 59, 59);
$canRefund = $booking['status_booking'] === 'Dibayar'
    && $booking['status_verifikasi'] === 'Terverifikasi'
    && (empty($booking['refund_id']) || $booking['refund_status'] === 'Ditolak')
    && new DateTimeImmutable('now') <= $refundDeadline;
$pageTitle = 'Status Reservasi ' . $booking['kode_booking'];
require_once __DIR__ . '/../app/views/layouts/header.php';
require __DIR__ . '/../app/views/reservasi/status.php';
require_once __DIR__ . '/../app/views/layouts/footer.php';
