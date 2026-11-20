<?php

declare(strict_types=1);
require_once __DIR__ . '/admin_settings.php'; 

const HARI_ID  = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
const BULAN_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                  'Agustus', 'September', 'Oktober', 'November', 'Desember'];


function tanggal_id(DateTimeInterface $dt): string
{
    return HARI_ID[(int)$dt->format('N') - 1] . ', ' . (int)$dt->format('j') . ' '
        . BULAN_ID[(int)$dt->format('n') - 1] . ' ' . $dt->format('Y');
}

function sapaan_waktu(int $jam): string
{
    if ($jam < 11) {
        return 'pagi';
    }
    if ($jam < 15) {
        return 'siang';
    }
    return $jam < 18 ? 'sore' : 'malam';
}

function rupiah($angka): string
{
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

function nama_singkat(string $nama): string
{
    $bagian = preg_split('/\s+/', trim($nama)) ?: [];
    if (count($bagian) < 2) {
        return trim($nama);
    }
    $akhir = end($bagian);
    return $bagian[0] . ' ' . mb_strtoupper(mb_substr($akhir, 0, 1, 'UTF-8'), 'UTF-8') . '.';
}

function waktu_lalu(int $menit): string
{
    if ($menit < 1) {
        return 'baru saja';
    }
    if ($menit < 60) {
        return $menit . ' menit lalu';
    }
    if ($menit < 1440) {
        return intdiv($menit, 60) . ' jam lalu';
    }
    return intdiv($menit, 1440) . ' hari lalu';
}

function chip_unit(array $nomor, int $maks = 4): string
{
    if (!$nomor) {
        return '<span class="stat-note">Tidak ada</span>';
    }
    $html = '';
    foreach (array_slice($nomor, 0, $maks) as $n) {
        $html .= '<span class="unit-chip">' . h($n) . '</span>';
    }
    $sisa = array_slice($nomor, $maks);
    if ($sisa) {
        $html .= '<span class="unit-chip unit-chip-more" title="Unit ' . h(implode(', ', $sisa)) . '">+'
            . count($sisa) . ' lainnya</span>';
    }
    return $html;
}

function dash_unit_status(PDO $pdo): array
{
    $rows = $pdo->query("
        SELECT k.*,
               EXISTS (
                   SELECT 1 FROM reservasi r
                   WHERE r.kamar_id = k.id
                     AND r.status_booking IN ('Menunggu Konfirmasi', 'Dibayar')
                     AND tsrange(r.waktu_checkin, r.waktu_checkout) @> LOCALTIMESTAMP
               ) AS terisi_sekarang,
               EXISTS (
                   SELECT 1 FROM reservasi r
                   WHERE r.kamar_id = k.id
                     AND r.status_booking IN ('Di-Keep', 'Menunggu Konfirmasi', 'Dibayar')
                     AND r.waktu_checkin > LOCALTIMESTAMP
               ) AS dipesan_mendatang,
               (
                   SELECT MIN(CEIL(EXTRACT(EPOCH FROM (r.batas_waktu_keep - LOCALTIMESTAMP))))::int
                   FROM reservasi r
                   WHERE r.kamar_id = k.id
                     AND r.status_booking = 'Di-Keep'
                     AND r.batas_waktu_keep > LOCALTIMESTAMP
               ) AS sisa_keep
        FROM kamar k
        WHERE k.dihapus_pada IS NULL
        ORDER BY LENGTH(k.nomor_kamar), k.nomor_kamar
    ")->fetchAll();

    foreach ($rows as &$r) {
        if ($r['status'] === 'Perbaikan') {
            $r['label'] = 'Perbaikan';
        } elseif (!empty($r['terisi_sekarang']) || $r['status'] === 'Terisi') {
            $r['label'] = 'Terisi';
        } elseif ($r['sisa_keep'] !== null) {
            $r['label'] = 'Di-Keep';
        } elseif (!empty($r['dipesan_mendatang'])) {
            $r['label'] = 'Terisi'; // sudah dipesan untuk tanggal mendatang, tidak lagi dihitung Tersedia
        } else {
            $r['label'] = 'Tersedia';
        }
    }
    unset($r);
    return $rows;
}

function dash_data(PDO $pdo): array
{
    $units = dash_unit_status($pdo);
    $per = ['Tersedia' => [], 'Di-Keep' => [], 'Terisi' => [], 'Perbaikan' => []];
    $sisaKeepMin = null;
    foreach ($units as $u) {
        $per[$u['label']][] = $u['nomor_kamar'];
        if ($u['sisa_keep'] !== null && ($sisaKeepMin === null || (int)$u['sisa_keep'] < $sisaKeepMin)) {
            $sisaKeepMin = (int)$u['sisa_keep'];
        }
    }

    $aksiCepat = $pdo->query("
        SELECT r.id AS reservasi_id, r.kode_booking, r.nama_pemesan, r.whatsapp_pemesan, r.total_tagihan,
               r.waktu_checkin, r.waktu_checkout,
               k.nomor_kamar, k.tipe_kamar,
               p.id AS pembayaran_id, p.nominal, p.created_at AS bukti_at,
               GREATEST(0, FLOOR(EXTRACT(EPOCH FROM (LOCALTIMESTAMP - p.created_at)) / 60))::int AS menit_lalu
        FROM reservasi r
        JOIN kamar k ON k.id = r.kamar_id
        JOIN LATERAL (
            SELECT * FROM pembayaran WHERE reservasi_id = r.id ORDER BY created_at DESC, id DESC LIMIT 1
        ) p ON TRUE
        WHERE r.status_booking = 'Menunggu Konfirmasi' AND p.status_verifikasi = 'Menunggu'
          AND r.dihapus_pada IS NULL AND k.dihapus_pada IS NULL
        ORDER BY p.created_at ASC
    ")->fetchAll();

    $kedaluwarsa = $pdo->query("
        SELECT r.id AS reservasi_id, r.kode_booking, r.nama_pemesan, r.whatsapp_pemesan, r.total_tagihan,
               k.nomor_kamar, k.tipe_kamar,
               CEIL(EXTRACT(EPOCH FROM (r.batas_waktu_keep - LOCALTIMESTAMP)))::int AS sisa_detik
        FROM reservasi r
        JOIN kamar k ON k.id = r.kamar_id
        WHERE r.status_booking = 'Di-Keep' AND r.batas_waktu_keep > LOCALTIMESTAMP
          AND r.dihapus_pada IS NULL AND k.dihapus_pada IS NULL
        ORDER BY r.batas_waktu_keep ASC
    ")->fetchAll();

    $refund = (int)$pdo->query("SELECT COUNT(*) FROM refund WHERE status = 'Diajukan' AND dihapus_pada IS NULL")->fetchColumn();

    $refundList = $pdo->query("
        SELECT rf.id, rf.alasan, rf.diajukan_at, r.kode_booking, r.nama_pemesan, r.total_tagihan,
               k.nomor_kamar, k.tipe_kamar,
               GREATEST(0, FLOOR(EXTRACT(EPOCH FROM (LOCALTIMESTAMP - rf.diajukan_at)) / 60))::int AS menit_lalu
        FROM refund rf
        JOIN reservasi r ON r.id = rf.reservasi_id
        JOIN kamar k ON k.id = r.kamar_id
        WHERE rf.status = 'Diajukan' AND rf.dihapus_pada IS NULL
        ORDER BY rf.diajukan_at ASC
        LIMIT 5
    ")->fetchAll();

    return [
        'refund_list' => $refundList,
        'per'         => $per,
        'sisa_keep'   => $sisaKeepMin,
        'aksi_cepat'  => $aksiCepat,
        'kedaluwarsa' => $kedaluwarsa,
        'refund'      => $refund,
    ];
}

function dash_notification_data(PDO $pdo): array
{
    $row = $pdo->query("
        SELECT COUNT(*) AS pending_count, COALESCE(MAX(p.id), 0) AS latest_payment_id
        FROM reservasi r
        JOIN LATERAL (
            SELECT id, status_verifikasi
            FROM pembayaran
            WHERE reservasi_id = r.id
            ORDER BY created_at DESC, id DESC
            LIMIT 1
        ) p ON TRUE
        JOIN kamar k ON k.id = r.kamar_id
        WHERE r.status_booking = 'Menunggu Konfirmasi'
          AND p.status_verifikasi = 'Menunggu'
          AND r.dihapus_pada IS NULL
          AND k.dihapus_pada IS NULL
    ")->fetch();

    $rf = $pdo->query("
        SELECT COUNT(*) AS refund_count, COALESCE(MAX(id), 0) AS latest_refund_id
        FROM refund
        WHERE status = 'Diajukan' AND dihapus_pada IS NULL
    ")->fetch();

    return [
        'pending_count' => (int)$row['pending_count'],
        'latest_payment_id' => (int)$row['latest_payment_id'],
        'refund_count' => (int)$rf['refund_count'],
        'latest_refund_id' => (int)$rf['latest_refund_id'],
    ];
}

function dash_proses_bukti(PDO $pdo, int $reservasiId, int $pembayaranId, string $aksi, int $adminId): string
{
    if (!in_array($aksi, ['konfirmasi', 'tolak'], true)) {
        throw new InvalidArgumentException('Aksi tidak dikenali.');
    }

    $pdo->beginTransaction();
    try {
        $res = $pdo->prepare('SELECT status_booking FROM reservasi WHERE id = :id FOR UPDATE');
        $res->execute(['id' => $reservasiId]);
        $statusLama = $res->fetchColumn();

        $pay = $pdo->prepare('SELECT status_verifikasi FROM pembayaran WHERE id = :pid AND reservasi_id = :rid FOR UPDATE');
        $pay->execute(['pid' => $pembayaranId, 'rid' => $reservasiId]);
        $statusBukti = $pay->fetchColumn();

        if ($statusLama !== 'Menunggu Konfirmasi' || $statusBukti !== 'Menunggu') {
            throw new DomainException('Bukti ini sudah diproses atau statusnya sudah berubah. Muat ulang halaman reservasi.');
        }

        $log = $pdo->prepare('
            INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru, diubah_oleh)
            VALUES (:id, :lama, :baru, :admin)
        ');

        if ($aksi === 'konfirmasi') {
            $pdo->prepare("
                UPDATE pembayaran
                   SET status_verifikasi = 'Terverifikasi', diverifikasi_oleh = :admin, diverifikasi_at = CURRENT_TIMESTAMP
                 WHERE id = :pid
            ")->execute(['admin' => $adminId, 'pid' => $pembayaranId]);
            $pdo->prepare("UPDATE reservasi SET status_booking = 'Dibayar' WHERE id = :id")
                ->execute(['id' => $reservasiId]);
            $log->execute(['id' => $reservasiId, 'lama' => 'Menunggu Konfirmasi', 'baru' => 'Dibayar', 'admin' => $adminId]);
            $pesan = 'Pembayaran dikonfirmasi. Reservasi berstatus Dibayar.';
        } else {
            $pdo->prepare("
                UPDATE pembayaran
                   SET status_verifikasi = 'Ditolak', diverifikasi_oleh = :admin, diverifikasi_at = CURRENT_TIMESTAMP
                 WHERE id = :pid
            ")->execute(['admin' => $adminId, 'pid' => $pembayaranId]);
            $log->execute(['id' => $reservasiId, 'lama' => 'Menunggu Konfirmasi', 'baru' => 'Menunggu Konfirmasi', 'admin' => $adminId]);
            $pesan = 'Bukti ditolak. Tamu bisa mengunggah ulang selama waktu keep belum habis.';
        }

        $pdo->commit();
        return $pesan;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function dash_konfirmasi_wa_url(PDO $pdo, int $reservasiId): ?string
{
    $stmt = $pdo->prepare(" 
        SELECT r.kode_booking, r.nama_pemesan, r.whatsapp_pemesan, r.waktu_checkin, r.waktu_checkout,
               k.nomor_kamar, k.tipe_kamar
        FROM reservasi r
        JOIN kamar k ON k.id = r.kamar_id
        WHERE r.id = :id AND r.status_booking = 'Dibayar'
    ");
    $stmt->execute(['id' => $reservasiId]);
    $booking = $stmt->fetch();

    if (!$booking || trim((string)$booking['whatsapp_pemesan']) === '') {
        return null;
    }

    return wa_booking_confirmation_link($booking);
}

function dash_batalkan_keep(PDO $pdo, int $reservasiId, int $adminId): void
{
    $pdo->beginTransaction();
    try {
        $res = $pdo->prepare('SELECT status_booking FROM reservasi WHERE id = :id FOR UPDATE');
        $res->execute(['id' => $reservasiId]);
        if ($res->fetchColumn() !== 'Di-Keep') {
            throw new DomainException('Keep ini sudah tidak aktif (mungkin sudah dibayar, kedaluwarsa, atau dibatalkan).');
        }
        $pdo->prepare("UPDATE reservasi SET status_booking = 'Dibatalkan' WHERE id = :id")
            ->execute(['id' => $reservasiId]);
        $pdo->prepare("
            INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru, diubah_oleh)
            VALUES (:id, 'Di-Keep', 'Dibatalkan', :admin)
        ")->execute(['id' => $reservasiId, 'admin' => $adminId]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}