<?php
require_once __DIR__ . '/../app/public_helpers.php';

$pdo = db();
expire_keeps($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $phone = preg_replace('/[\s()+.-]/', '', trim((string)($_POST['phone'] ?? '')));
    if (!preg_match('/^\d{9,15}$/', $phone)) {
        public_error('Masukkan nomor WhatsApp yang valid, termasuk kode negara atau awalan 0.');
        redirect_to('status.php');
    }

    if (str_starts_with($phone, '0')) {
        $phone = '62' . substr($phone, 1);
    } elseif (!str_starts_with($phone, '62')) {
        $phone = '62' . $phone;
    }

    if (!preg_match('/^62\d{8,13}$/', $phone)) {
        public_error('Masukkan nomor WhatsApp Indonesia yang valid.');
        redirect_to('status.php');
    }

    $booking = booking_by_whatsapp($pdo, $phone);
    if (!$booking) {
        public_error('Reservasi dengan nomor WhatsApp tersebut tidak ditemukan. Periksa kembali nomor yang digunakan saat memesan.');
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
    public_error('Masukkan nomor WhatsApp yang digunakan saat reservasi untuk melihat statusnya.');
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
$refundDeadline = refund_deadline($booking, $settings);
$canRefund = $booking['status_booking'] === 'Dibayar'
    && $booking['status_verifikasi'] === 'Terverifikasi'
    && (empty($booking['refund_id']) || $booking['refund_status'] === 'Ditolak')
    && new DateTimeImmutable('now') <= $refundDeadline;
$pageTitle = 'Status Reservasi ' . $booking['kode_booking'];
require_once __DIR__ . '/../app/views/layouts/header.php';
require __DIR__ . '/../app/views/reservasi/status.php';
require_once __DIR__ . '/../app/views/layouts/footer.php';
