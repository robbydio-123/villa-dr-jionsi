<?php
require_once __DIR__ . '/../app/public_helpers.php';
$booking = current_public_booking(db());
if ($booking) {
    redirect_to('hasil_status.php?kode=' . rawurlencode($booking['kode_booking']));
}
public_error('Waktu keep reservasi telah berakhir.');
redirect_to('status.php');
