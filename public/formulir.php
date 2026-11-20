<?php
require_once __DIR__ . '/../app/public_helpers.php';

$pdo = db();
expire_keeps($pdo);
$roomId = filter_input(INPUT_GET, 'kamar_id', FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT * FROM kamar WHERE id = :id AND status = 'Tersedia' AND dihapus_pada IS NULL");
$stmt->execute(['id' => $roomId ?: 0]);
$room = $stmt->fetch();
if (!$room) {
    public_error('Kamar tidak ditemukan atau sedang tidak tersedia.');
    redirect_to('beranda.php');
}

$checkinInput = trim((string)($_GET['checkin'] ?? ''));
$checkin = booking_datetime($checkinInput);
if (!$checkin) {
    // Belum ada jadwal: arahkan ke detail kamar untuk memilih tanggal dan durasi dahulu.
    redirect_to('detail.php?id=' . (int)$room['id']);
}
$duration = filter_input(INPUT_GET, 'durasi', FILTER_VALIDATE_INT);
$duration = valid_duration_hours((int)$duration) ? (int)$duration : 24;
$checkoutInput = $checkin ? $checkin->modify('+' . $duration . ' hours')->format('Y-m-d\TH:i') : '';
$packagePrices = short_package_prices($pdo);
$error = take_public_error();
$pageTitle = 'Formulir Reservasi - ' . villa_name();
require_once __DIR__ . '/../app/views/layouts/header.php';
require __DIR__ . '/../app/views/reservasi/formulir.php';
require_once __DIR__ . '/../app/views/layouts/footer.php';