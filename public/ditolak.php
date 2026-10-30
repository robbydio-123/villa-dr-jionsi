<?php
require_once __DIR__ . '/../app/public_helpers.php';
$booking = current_public_booking(db());
if ($booking && $booking['status_verifikasi'] === 'Ditolak') {
    redirect_to('keep.php');
}
if ($booking) {
    redirect_to('hasil_status.php?kode=' . rawurlencode($booking['kode_booking']));
}
public_error('Tidak ada bukti pembayaran yang perlu diunggah ulang.');
redirect_to('status.php');
