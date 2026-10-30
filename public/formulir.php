<?php
require_once __DIR__ . '/../app/public_helpers.php';

$pdo = db();
expire_keeps($pdo);
$roomId = filter_input(INPUT_GET, 'kamar_id', FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT * FROM kamar WHERE id = :id AND status = 'Tersedia'");
$stmt->execute(['id' => $roomId ?: 0]);
$room = $stmt->fetch();
if (!$room) {
    public_error('Kamar tidak ditemukan atau sedang tidak tersedia.');
    redirect_to('index.php');
}

$checkinInput = trim((string)($_GET['checkin'] ?? ''));
$checkin = booking_datetime($checkinInput);
$duration = filter_input(INPUT_GET, 'durasi', FILTER_VALIDATE_INT);
$duration = in_array($duration, [3, 6, 12, 24, 48, 72], true) ? $duration : 24;
$checkoutInput = $checkin ? $checkin->modify('+' . $duration . ' hours')->format('Y-m-d\TH:i') : '';
$error = take_public_error();
$pageTitle = 'Formulir Reservasi - Villa D-R Jionsi';
require_once __DIR__ . '/../app/views/layouts/header.php';
require __DIR__ . '/../app/views/reservasi/formulir.php';
require_once __DIR__ . '/../app/views/layouts/footer.php';
