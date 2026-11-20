<?php

declare(strict_types=1);

require_once __DIR__ . '/admin_settings.php';

/** Status yang boleh dihapus dari daftar (reservasi yang sudah tidak berjalan). */
const RESERVASI_STATUS_BOLEH_HAPUS = ['Kedaluwarsa', 'Dibatalkan'];

/** Daftar reservasi untuk tabel admin. $arsip = true menampilkan yang sudah dihapus. */
function reservasi_list(PDO $pdo, bool $arsip): array
{
    $kondisiHapus = $arsip ? 'r.dihapus_pada IS NOT NULL' : 'r.dihapus_pada IS NULL';
    $urut         = $arsip ? 'r.dihapus_pada DESC, r.id DESC' : 'r.created_at DESC, r.id DESC';

    return $pdo->query("
        SELECT r.*, k.nomor_kamar, k.tipe_kamar, k.lantai,
               p.status_verifikasi AS status_verifikasi_terakhir,
               p.id AS pembayaran_id_terakhir,
               p.nominal AS nominal_terakhir,
               p.wa_dikirim_at AS wa_dikirim_terakhir,
               GREATEST(0, CEIL(EXTRACT(EPOCH FROM (r.batas_waktu_keep - LOCALTIMESTAMP))))::int AS sisa_keep,
               (
                   r.status_booking IN ('Kedaluwarsa', 'Dibatalkan')
                   OR (r.status_booking = 'Dibayar' AND r.waktu_checkout <= LOCALTIMESTAMP)
               ) AS boleh_dihapus
        FROM reservasi r
        LEFT JOIN kamar k ON k.id = r.kamar_id
        LEFT JOIN LATERAL (
            SELECT id, status_verifikasi, nominal, wa_dikirim_at
            FROM pembayaran
            WHERE reservasi_id = r.id
            ORDER BY created_at DESC, id DESC
            LIMIT 1
        ) p ON TRUE
        WHERE $kondisiHapus
        ORDER BY $urut
    ")->fetchAll();
}

function reservasi_arsip_count(PDO $pdo): int
{
    return (int)$pdo->query('SELECT COUNT(*) FROM reservasi WHERE dihapus_pada IS NOT NULL')->fetchColumn();
}

/** Hapus lunak satu reservasi. Mengembalikan kode booking. */
function reservasi_hapus(PDO $pdo, int $id): string
{
    $pdo->beginTransaction();
    try {
        $cari = $pdo->prepare('
            SELECT kode_booking, status_booking, waktu_checkout, (waktu_checkout <= LOCALTIMESTAMP) AS sudah_selesai
            FROM reservasi
            WHERE id = :id AND dihapus_pada IS NULL
            FOR UPDATE
        ');
        $cari->execute(['id' => $id]);
        $r = $cari->fetch();
        if (!$r) {
            throw new InvalidArgumentException('Reservasi tidak ditemukan atau sudah dihapus.');
        }

        $status = (string)$r['status_booking'];
        $boleh  = in_array($status, RESERVASI_STATUS_BOLEH_HAPUS, true)
            || ($status === 'Dibayar' && $r['sudah_selesai']);
        if (!$boleh) {
            throw new InvalidArgumentException(
                'Reservasi ' . $r['kode_booking'] . ' masih berstatus ' . $status
                . ' dan belum bisa dihapus. Hanya reservasi Kedaluwarsa, Dibatalkan, atau Dibayar yang masa menginapnya sudah lewat yang dapat dihapus.'
            );
        }

        $upd = $pdo->prepare('UPDATE reservasi SET dihapus_pada = CURRENT_TIMESTAMP WHERE id = :id');
        $upd->execute(['id' => $id]);
        $pdo->commit();

        return (string)$r['kode_booking'];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/** Pulihkan reservasi dari daftar terhapus. Mengembalikan kode booking. */
function reservasi_pulihkan(PDO $pdo, int $id): string
{
    $cari = $pdo->prepare('SELECT kode_booking FROM reservasi WHERE id = :id AND dihapus_pada IS NOT NULL');
    $cari->execute(['id' => $id]);
    $kode = $cari->fetchColumn();
    if ($kode === false) {
        throw new InvalidArgumentException('Reservasi tidak ditemukan di daftar reservasi terhapus.');
    }

    $upd = $pdo->prepare('UPDATE reservasi SET dihapus_pada = NULL WHERE id = :id');
    $upd->execute(['id' => $id]);

    return (string)$kode;
}