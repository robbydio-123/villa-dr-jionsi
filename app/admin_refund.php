<?php

declare(strict_types=1);
require_once __DIR__ . '/admin_settings.php'; // admin_auth + public_helpers + flash

const REFUND_STATUS_FILTER = ['Diajukan', 'Disetujui', 'Ditolak'];
const REFUND_SORT          = ['terbaru', 'terlama', 'nominal_desc', 'nominal_asc'];

/** Status yang boleh dihapus dari daftar (hapus lunak, data tetap tersimpan dan bisa dipulihkan). */
const REFUND_STATUS_BOLEH_HAPUS = ['Diajukan', 'Disetujui', 'Ditolak', 'Selesai'];

/** Nomor tiket tampilan, contoh: RFD-0001. */
function refund_kode(int $id): string
{
    return 'RFD-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
}

/** Baca & bersihkan filter dari URL (q, status, sort, arsip). */
function refund_filter(array $get): array
{
    $status = (string)($get['status'] ?? '');
    $sort   = (string)($get['sort'] ?? 'terbaru');

    return [
        'q'      => trim((string)($get['q'] ?? '')),
        'status' => in_array($status, REFUND_STATUS_FILTER, true) ? $status : '',
        'sort'   => in_array($sort, REFUND_SORT, true) ? $sort : 'terbaru',
        'arsip'  => !empty($get['arsip']),
    ];
}

/** Bangun URL halaman refund dari filter; $ubah menimpa sebagian nilai. */
function refund_url(array $filter, array $ubah = []): string
{
    $q = array_merge([
        'q'      => $filter['q'] ?? '',
        'status' => $filter['status'] ?? '',
        'sort'   => $filter['sort'] ?? 'terbaru',
        'arsip'  => !empty($filter['arsip']) ? 1 : '',
    ], $ubah);

    if (($q['sort'] ?? '') === 'terbaru') {
        $q['sort'] = '';
    }
    $q = array_filter($q, static function ($v) {
        return $v !== '' && $v !== null;
    });

    return 'admin_refund.php' . ($q ? '?' . http_build_query($q) : '');
}

/** Daftar pengajuan refund sesuai filter. Filter 'arsip' = tampilkan yang sudah dihapus. */
function refund_admin_list(PDO $pdo, array $filter): array
{
    $arsip  = !empty($filter['arsip']);
    $where  = [$arsip ? 'rf.dihapus_pada IS NOT NULL' : 'rf.dihapus_pada IS NULL'];
    $params = [];

    $status = (string)($filter['status'] ?? '');
    if ($status === 'Disetujui') {
        $where[] = "rf.status IN ('Disetujui', 'Selesai')";
    } elseif ($status !== '') {
        $where[] = 'rf.status = :status';
        $params['status'] = $status;
    }

    $q = (string)($filter['q'] ?? '');
    if ($q !== '') {
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $where[] = '(r.kode_booking ILIKE :q1 OR r.nama_pemesan ILIKE :q2 OR r.whatsapp_pemesan ILIKE :q3)';
        $params['q1'] = $params['q2'] = $params['q3'] = $like;
    }

    $order = match ($filter['sort'] ?? 'terbaru') {
        'terlama'      => 'rf.diajukan_at ASC, rf.id ASC',
        'nominal_desc' => 'r.total_tagihan DESC, rf.id DESC',
        'nominal_asc'  => 'r.total_tagihan ASC, rf.id DESC',
        default        => $arsip
            ? 'rf.dihapus_pada DESC, rf.id DESC'
            : "(rf.status = 'Diajukan') DESC, rf.diajukan_at DESC, rf.id DESC",
    };

    $stmt = $pdo->prepare("
        SELECT rf.id, rf.alasan, rf.status, rf.diajukan_at, rf.diputuskan_at, rf.dihapus_pada,
               rf.bank_tujuan, rf.no_rekening, rf.nama_pemilik_rekening, rf.ditransfer_at,
               r.kode_booking, r.nama_pemesan, r.whatsapp_pemesan, r.total_tagihan,
               (rf.status IN ('Diajukan', 'Disetujui', 'Ditolak', 'Selesai')) AS boleh_dihapus
        FROM refund rf
        JOIN reservasi r ON r.id = rf.reservasi_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY $order
    ");
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/** Angka untuk kartu ringkasan (hanya pengajuan yang belum dihapus, kecuali 'terhapus'). */
function refund_stats(PDO $pdo): array
{
    $row = $pdo->query("
        SELECT
            COUNT(*) FILTER (WHERE dihapus_pada IS NULL AND status = 'Diajukan')                AS menunggu,
            COUNT(*) FILTER (WHERE dihapus_pada IS NULL AND status IN ('Disetujui', 'Selesai')) AS disetujui,
            COUNT(*) FILTER (WHERE dihapus_pada IS NULL AND status = 'Ditolak')                 AS ditolak,
            COUNT(*) FILTER (WHERE dihapus_pada IS NOT NULL)                                    AS terhapus
        FROM refund
    ")->fetch();

    return [
        'menunggu'  => (int)$row['menunggu'],
        'disetujui' => (int)$row['disetujui'],
        'ditolak'   => (int)$row['ditolak'],
        'terhapus'  => (int)$row['terhapus'],
    ];
}

/** Detail satu pengajuan refund beserta data reservasi, kamar, dan pembayaran. */
function refund_admin_detail(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("
        SELECT rf.id, rf.reservasi_id, rf.alasan, rf.status, rf.catatan_admin,
               rf.diajukan_at, rf.diputuskan_at, rf.dihapus_pada,
               rf.bank_tujuan, rf.no_rekening, rf.nama_pemilik_rekening, rf.ditransfer_at,
               r.kode_booking, r.nama_pemesan, r.whatsapp_pemesan, r.jumlah_tamu,
               r.waktu_checkin, r.waktu_checkout, r.durasi_jam, r.total_tagihan,
               r.status_booking,
               k.nomor_kamar, k.tipe_kamar,
               p.nominal AS nominal_pembayaran, p.tanggal_pembayaran, p.bukti_file
        FROM refund rf
        JOIN reservasi r ON r.id = rf.reservasi_id
        JOIN kamar k ON k.id = r.kamar_id
        LEFT JOIN LATERAL (
            SELECT * FROM pembayaran WHERE reservasi_id = r.id ORDER BY created_at DESC, id DESC LIMIT 1
        ) p ON TRUE
        WHERE rf.id = :id
    ");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch() ?: null;
}

/**
 * Putuskan pengajuan refund.
 * - setuju: refund 'Disetujui', reservasi 'Dibatalkan'
 * - tolak : refund 'Ditolak',   reservasi kembali 'Dibayar' (tamu boleh mengajukan ulang)
 */
function refund_putuskan(PDO $pdo, int $refundId, string $aksi, string $catatan, int $adminId): void
{
    if (!in_array($aksi, ['setuju', 'tolak'], true)) {
        throw new InvalidArgumentException('Aksi tidak dikenal.');
    }
    $catatan = trim($catatan);
    if ($aksi === 'tolak' && text_length($catatan) < 5) {
        throw new InvalidArgumentException('Alasan penolakan wajib diisi (minimal 5 karakter).');
    }
    if (text_length($catatan) > 1000) {
        throw new InvalidArgumentException('Catatan maksimal 1.000 karakter.');
    }

    $statusRefund  = $aksi === 'setuju' ? 'Disetujui' : 'Ditolak';
    $statusBooking = $aksi === 'setuju' ? 'Dibatalkan' : 'Dibayar';

    $pdo->beginTransaction();
    try {
        $cek = $pdo->prepare('SELECT reservasi_id, status FROM refund WHERE id = :id AND dihapus_pada IS NULL FOR UPDATE');
        $cek->execute(['id' => $refundId]);
        $rf = $cek->fetch();
        if (!$rf) {
            throw new DomainException('Pengajuan refund tidak ditemukan.');
        }
        if ($rf['status'] !== 'Diajukan') {
            throw new DomainException('Pengajuan ini sudah diputuskan sebelumnya.');
        }

        $pdo->prepare('
            UPDATE refund
               SET status = :s, catatan_admin = :c, diputuskan_at = NOW()
             WHERE id = :id
        ')->execute(['s' => $statusRefund, 'c' => $catatan !== '' ? $catatan : null, 'id' => $refundId]);

        $pdo->prepare('UPDATE reservasi SET status_booking = :s WHERE id = :id')
            ->execute(['s' => $statusBooking, 'id' => $rf['reservasi_id']]);

        $pdo->prepare("
            INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru, diubah_oleh)
            VALUES (:id, 'Refund Diajukan', :baru, :admin)
        ")->execute(['id' => $rf['reservasi_id'], 'baru' => $statusBooking, 'admin' => $adminId]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/** Tandai refund yang sudah disetujui sebagai sudah ditransfer ke tamu (status Selesai). */
function refund_tandai_selesai(PDO $pdo, int $refundId, int $adminId): string
{
    $pdo->beginTransaction();
    try {
        $cek = $pdo->prepare('SELECT reservasi_id, status FROM refund WHERE id = :id AND dihapus_pada IS NULL FOR UPDATE');
        $cek->execute(['id' => $refundId]);
        $rf = $cek->fetch();
        if (!$rf) {
            throw new DomainException('Pengajuan refund tidak ditemukan.');
        }
        if ($rf['status'] !== 'Disetujui') {
            throw new DomainException('Hanya refund berstatus Disetujui yang bisa ditandai sudah ditransfer.');
        }

        $pdo->prepare("UPDATE refund SET status = 'Selesai', ditransfer_at = NOW() WHERE id = :id")
            ->execute(['id' => $refundId]);
        $pdo->commit();

        return refund_kode($refundId);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/** Ubah nomor WhatsApp lokal (08xx / +62 / 62) menjadi format wa.me (62xxx). Kosong bila tidak valid. */
function wa_nomor(string $raw): string
{
    $n = preg_replace('/\D+/', '', $raw);
    if ($n === '') {
        return '';
    }
    if (str_starts_with($n, '0')) {
        $n = '62' . substr($n, 1);
    } elseif (str_starts_with($n, '8')) {
        $n = '62' . $n;
    }
    return strlen($n) >= 10 ? $n : '';
}

/** Samarkan nomor rekening untuk pesan WhatsApp: hanya 4 digit terakhir yang terlihat. */
function rekening_samar(string $no): string
{
    $len = strlen($no);
    return $len <= 4 ? $no : str_repeat('*', $len - 4) . substr($no, -4);
}

/** Tautan WhatsApp berisi pesan pengiriman bukti transfer refund. Null bila nomor tamu tidak valid. */
function refund_wa_url(array $r): ?string
{
    $nomor = wa_nomor((string)($r['whatsapp_pemesan'] ?? ''));
    if ($nomor === '') {
        return null;
    }

    $pesan = "Halo " . $r['nama_pemesan'] . ",\n\n"
        . "Refund untuk booking " . $r['kode_booking'] . " (" . refund_kode((int)$r['id']) . ") sebesar "
        . rupiah($r['total_tagihan']) . " sudah kami transfer";
    if (!empty($r['bank_tujuan']) && !empty($r['no_rekening'])) {
        $pesan .= " ke " . $r['bank_tujuan'] . " " . rekening_samar((string)$r['no_rekening'])
            . (!empty($r['nama_pemilik_rekening']) ? " a.n. " . $r['nama_pemilik_rekening'] : '');
    }
    $pesan .= ".\n\nBerikut bukti transfernya. Mohon dicek ya, terima kasih.\n- " . villa_name();

    return 'https://wa.me/' . $nomor . '?text=' . rawurlencode($pesan);
}

/** Hapus lunak satu pengajuan (hanya yang sudah diputuskan). Mengembalikan kode tiket, mis. RFD-0001. */
function refund_hapus(PDO $pdo, int $id): string
{
    $pdo->beginTransaction();
    try {
        $cari = $pdo->prepare('SELECT status FROM refund WHERE id = :id AND dihapus_pada IS NULL FOR UPDATE');
        $cari->execute(['id' => $id]);
        $status = $cari->fetchColumn();
        if ($status === false) {
            throw new InvalidArgumentException('Pengajuan refund tidak ditemukan atau sudah dihapus.');
        }

        $kode = refund_kode($id);
        if (!in_array((string)$status, REFUND_STATUS_BOLEH_HAPUS, true)) {
            throw new InvalidArgumentException('Pengajuan ' . $kode . ' berstatus tidak dikenal sehingga belum bisa dihapus.');
        }

        $pdo->prepare('UPDATE refund SET dihapus_pada = CURRENT_TIMESTAMP WHERE id = :id')
            ->execute(['id' => $id]);
        $pdo->commit();

        return $kode;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/** Pulihkan pengajuan dari daftar terhapus. Mengembalikan kode tiket. */
function refund_pulihkan(PDO $pdo, int $id): string
{
    $cari = $pdo->prepare('SELECT 1 FROM refund WHERE id = :id AND dihapus_pada IS NOT NULL');
    $cari->execute(['id' => $id]);
    if ($cari->fetchColumn() === false) {
        throw new InvalidArgumentException('Pengajuan refund tidak ditemukan di daftar pengajuan terhapus.');
    }

    $pdo->prepare('UPDATE refund SET dihapus_pada = NULL WHERE id = :id')
        ->execute(['id' => $id]);

    return refund_kode($id);
}

function refund_badge(string $status): array
{
    return match ($status) {
        'Diajukan'  => ['bg-warning bg-opacity-10 text-brown border border-warning-subtle', 'bi-hourglass-split', 'Menunggu Tinjauan'],
        'Disetujui' => ['bg-success bg-opacity-10 text-success border border-success-subtle', 'bi-check-circle-fill', 'Disetujui'],
        'Selesai'   => ['bg-success bg-opacity-10 text-success border border-success-subtle', 'bi-check-all', 'Selesai Ditransfer'],
        default     => ['bg-danger bg-opacity-10 text-danger border border-danger-subtle', 'bi-x-circle-fill', 'Ditolak'],
    };
}

function rupiah($n): string
{
    return 'Rp ' . number_format((float)$n, 0, ',', '.');
}

/**
 * Pastikan kolom hapus lunak ada di tabel refund. Aman dijalankan berulang:
 * hanya mengubah tabel bila kolomnya belum ada.
 */
function refund_pastikan_kolom(PDO $pdo): void
{
    refund_pastikan_kolom_rekening($pdo);

    static $sudah = false;
    if ($sudah) {
        return;
    }
    $sudah = true;

    try {
        $ada = $pdo->query("
            SELECT 1 FROM information_schema.columns
            WHERE table_schema = current_schema() AND table_name = 'refund' AND column_name = 'dihapus_pada'
        ")->fetchColumn();
        if ($ada === false) {
            $pdo->exec('ALTER TABLE refund ADD COLUMN dihapus_pada TIMESTAMP');
            $pdo->exec('CREATE INDEX IF NOT EXISTS refund_dihapus_pada_idx ON refund (dihapus_pada)');
        }
    } catch (Throwable $e) {
        error_log('[refund] Gagal menyiapkan kolom dihapus_pada: ' . $e->getMessage());
    }
}

refund_pastikan_kolom(db());