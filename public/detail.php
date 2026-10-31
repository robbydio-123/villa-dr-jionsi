<?php
require_once __DIR__ . '/../app/public_helpers.php';

$pdo = db();
expire_keeps($pdo);
$roomId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM kamar WHERE id = :id');
$stmt->execute(['id' => $roomId ?: 0]);
$room = $stmt->fetch();
if (!$room) {
    http_response_code(404);
    $room = null;
    $pageTitle = 'Kamar tidak ditemukan';
    require_once __DIR__ . '/../app/views/layouts/header.php';
    echo '<main class="container py-5"><div class="alert alert-warning">Kamar tidak ditemukan.</div><a href="index.php">Kembali ke katalog</a></main>';
    require_once __DIR__ . '/../app/views/layouts/footer.php';
    exit;
}

$checkinInput = trim((string)($_GET['checkin'] ?? ''));
$checkin = booking_datetime($checkinInput);
$duration = filter_input(INPUT_GET, 'durasi', FILTER_VALIDATE_INT) ?: 24;
$duration = valid_duration_hours((int)$duration) ? (int)$duration : 24;
$checkout = $checkin ? $checkin->modify('+' . $duration . ' hours') : null;
$available = $room['status'] === 'Tersedia';
if ($available && $checkin && $checkout) {
    $availability = $pdo->prepare("
        SELECT NOT EXISTS (
            SELECT 1 FROM reservasi
            WHERE kamar_id = :room_id
              AND status_booking IN ('Di-Keep', 'Menunggu Konfirmasi', 'Dibayar')
              AND tsrange(waktu_checkin, waktu_checkout) && tsrange(:checkin::timestamp, :checkout::timestamp)
        )
    ");
    $availability->execute([
        'room_id' => $room['id'],
        'checkin' => $checkin->format('Y-m-d H:i:s'),
        'checkout' => $checkout->format('Y-m-d H:i:s'),
    ]);
    $available = filter_var($availability->fetchColumn(), FILTER_VALIDATE_BOOLEAN);
}
$packagePrices = short_package_prices($pdo);
$settings = get_public_settings($pdo);
$pageTitle = 'Kamar ' . $room['nomor_kamar'] . ' - ' . $room['tipe_kamar'];
require_once __DIR__ . '/../app/views/layouts/header.php';
require __DIR__ . '/../app/views/kamar/detail.php';
require_once __DIR__ . '/../app/views/layouts/footer.php';