<?php
require_once __DIR__ . '/../app/public_helpers.php';

$pdo = db();
$booking = require_public_booking($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        store_payment_proof($pdo, $booking);
        redirect_to('hasil_status.php?kode=' . rawurlencode($booking['kode_booking']));
    } catch (InvalidArgumentException | DomainException $e) {
        public_error($e->getMessage());
        redirect_to('keep.php');
    } catch (Throwable $e) {
        error_log('[pembayaran] Gagal menyimpan bukti: ' . $e->getMessage());
        public_error('Bukti pembayaran tidak dapat disimpan saat ini. Silakan coba kembali.');
        redirect_to('keep.php');
    }
}

redirect_to('keep.php');
