<?php
require_once __DIR__ . '/../app/admin_reservasi.php';
require_once __DIR__ . '/../app/admin_dashboard.php';


function wa_link(string $nomor, string $pesan = ''): string
{
	$digit = preg_replace('/\D/', '', $nomor) ?? '';
	if ($digit !== '' && $digit[0] === '0') {
		$digit = '62' . substr($digit, 1);
	}
	return 'https://wa.me/' . $digit . ($pesan !== '' ? '?text=' . rawurlencode($pesan) : '');
}

function wa_booking_confirmation_link(array $booking): string
{
	$checkin = date('d/m/Y H:i', strtotime((string)$booking['waktu_checkin']));
	$checkout = date('d/m/Y H:i', strtotime((string)$booking['waktu_checkout']));
	$message = sprintf(
		'Halo %s, pembayaran reservasi %s di %s sudah kami konfirmasi. Kamar %s (%s), check-in %s, check-out %s. Terima kasih.',
		$booking['nama_pemesan'],
		$booking['kode_booking'],
		villa_name(),
		$booking['nomor_kamar'],
		$booking['tipe_kamar'],
		$checkin,
		$checkout
	);

	return wa_link((string)$booking['whatsapp_pemesan'], $message);
}

function wa_penolakan_link(array $booking): string
{
	$pesan = sprintf(
		'Halo %s, bukti pembayaran untuk reservasi %s di %s belum bisa kami verifikasi karena nominal atau bukti transfer tidak sesuai dengan tagihan.',
		$booking['nama_pemesan'],
		$booking['kode_booking'],
		villa_name()
	);
	if (!empty($booking['batas_waktu_keep']) && strtotime((string)$booking['batas_waktu_keep']) > time()) {
		$pesan .= ' Silakan unggah ulang bukti pembayaran yang benar sebelum ' . date('H:i', strtotime((string)$booking['batas_waktu_keep'])) . ' WIB.';
	} else {
		$pesan .= ' Silakan hubungi kami untuk membuat reservasi ulang.';
	}
	return wa_link((string)$booking['whatsapp_pemesan'], $pesan . ' Terima kasih.');
}

/** Tombol WhatsApp sekali kirim (dipakai view reservasi). Diproses oleh aksi 'wa_kirim' di bawah. */
function wa_sekali_form(int $pembayaranId, string $label, string $class = 'btn btn-sm btn-success'): string
{
	return '<form method="post" action="admin_reservasi.php" target="_blank" class="wa-sekali d-inline">'
		. '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">'
		. '<input type="hidden" name="aksi" value="wa_kirim">'
		. '<input type="hidden" name="pembayaran_id" value="' . $pembayaranId . '">'
		. '<button type="submit" class="' . h($class) . '"><i class="bi bi-whatsapp me-1"></i> ' . h($label) . '</button>'
		. '</form>';
}

/**
 * Tandai WhatsApp terkirim (maksimal 1x per bukti pembayaran) lalu kembalikan URL wa.me.
 * Mengembalikan null (dan mengisi flash) bila tidak boleh dikirim.
 */
function wa_proses_kirim(PDO $pdo, int $pembayaranId): ?string
{
	$stmt = $pdo->prepare('
		SELECT p.id, p.status_verifikasi, p.wa_dikirim_at,
		       r.kode_booking, r.nama_pemesan, r.whatsapp_pemesan, r.status_booking,
		       r.waktu_checkin, r.waktu_checkout, r.batas_waktu_keep,
		       k.nomor_kamar, k.tipe_kamar
		FROM pembayaran p
		JOIN reservasi r ON r.id = p.reservasi_id
		JOIN kamar k ON k.id = r.kamar_id
		WHERE p.id = :id
	');
	$stmt->execute(['id' => $pembayaranId]);
	$data = $stmt->fetch();

	if (!$data) {
		set_flash('danger', 'Data pembayaran tidak ditemukan.');
		return null;
	}
	if ($data['wa_dikirim_at'] !== null) {
		set_flash('info', 'Pesan WhatsApp untuk bukti ini sudah pernah dikirim.');
		return null;
	}
	if (trim((string)$data['whatsapp_pemesan']) === '') {
		set_flash('warning', 'Nomor WhatsApp tamu kosong.');
		return null;
	}

	if ($data['status_verifikasi'] === 'Terverifikasi') {
		$url = wa_booking_confirmation_link($data);
	} elseif ($data['status_verifikasi'] === 'Ditolak' && $data['status_booking'] === 'Menunggu Konfirmasi') {
		$url = wa_penolakan_link($data);
	} else {
		set_flash('warning', 'Status bukti sudah berubah, pesan WhatsApp tidak dikirim.');
		return null;
	}

	// Atomik: hanya satu permintaan yang berhasil menandai.
	$tandai = $pdo->prepare('UPDATE pembayaran SET wa_dikirim_at = CURRENT_TIMESTAMP WHERE id = :id AND wa_dikirim_at IS NULL');
	$tandai->execute(['id' => $pembayaranId]);
	if ($tandai->rowCount() !== 1) {
		set_flash('info', 'Pesan WhatsApp untuk bukti ini sudah pernah dikirim.');
		return null;
	}
	return $url;
}

require_admin();
$pdo = db();
expire_keeps($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$aksi = (string)($_POST['aksi'] ?? '');

	try {
		if ($aksi === 'konfirmasi' || $aksi === 'tolak') {
			$resId = (int)($_POST['reservasi_id'] ?? 0);
			$pesan = dash_proses_bukti($pdo, $resId, (int)($_POST['pembayaran_id'] ?? 0), $aksi, (int)$_SESSION['admin_id']);
			$pid = (int)($_POST['pembayaran_id'] ?? 0);
			set_flash(
				$aksi === 'konfirmasi' ? 'success' : 'warning',
				$pesan,
				$pid,
				$aksi === 'konfirmasi' ? 'Kirim konfirmasi WhatsApp' : 'Kirim pemberitahuan penolakan WhatsApp'
			);
		} elseif ($aksi === 'wa_kirim') {
			$url = wa_proses_kirim($pdo, (int)($_POST['pembayaran_id'] ?? 0));
			if ($url !== null) {
				header('Location: ' . $url);
				exit;
			}
		} elseif ($aksi === 'batalkan') {
			dash_batalkan_keep($pdo, (int)($_POST['reservasi_id'] ?? 0), (int)$_SESSION['admin_id']);
			set_flash('success', 'Keep dibatalkan. Kamar kembali tersedia.');
		} elseif ($aksi === 'hapus') {
			$kode = reservasi_hapus($pdo, (int)($_POST['id'] ?? 0));
			set_flash('success', 'Reservasi ' . $kode . ' dihapus dari tampilan. Datanya tetap tersimpan dan bisa dipulihkan dari daftar reservasi terhapus.');
		} elseif ($aksi === 'pulihkan') {
			$kode = reservasi_pulihkan($pdo, (int)($_POST['id'] ?? 0));
			set_flash('success', 'Reservasi ' . $kode . ' berhasil dipulihkan dan tampil kembali.');
		}
	} catch (DomainException | InvalidArgumentException $e) {
		set_flash('danger', $e->getMessage());
	} catch (Throwable $e) {
		error_log('[admin_reservasi] ' . $e->getMessage());
		set_flash('danger', 'Terjadi kesalahan di server. Perubahan tidak tersimpan.');
	}
	redirect_to('admin_reservasi.php');
}

$arsip = isset($_GET['arsip']) && $_GET['arsip'] === '1';
$reservasiRows = reservasi_list($pdo, $arsip);
$arsipCount = reservasi_arsip_count($pdo);
$flash = take_flash();
// public/admin_reservasi.php
require_once __DIR__ . '/../app/views/layouts/admin_header.php';
require_once __DIR__ . '/../app/views/admin/reservasi.php';
require_once __DIR__ . '/../app/views/layouts/admin_footer.php';
?>