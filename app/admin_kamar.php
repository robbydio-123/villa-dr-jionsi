<?php

declare(strict_types=1);

require_once __DIR__ . '/admin_settings.php';

const KAMAR_STATUS = ['Tersedia', 'Terisi', 'Perbaikan'];
const KAMAR_PER_HALAMAN = 10;
const KAMAR_FOTO_MAKS = 2 * 1024 * 1024;

function kamar_str($nilai): string
{
    return is_string($nilai) ? trim($nilai) : '';
}

function kamar_rupiah($nilai): string
{
    return 'Rp ' . number_format((float)$nilai, 0, ',', '.');
}

function kamar_url(array $filter, array $ubah = []): string
{
    $q = array_merge([
        'q'      => $filter['q'] ?? '',
        'tipe'   => $filter['tipe'] ?? '',
        'status' => $filter['status'] ?? '',
        'sort'   => $filter['sort'] ?? 'nomor_asc',
        'page'   => $filter['page'] ?? 1,
        'arsip'  => !empty($filter['arsip']) ? 1 : '',
    ], $ubah);

    if (($q['sort'] ?? '') === 'nomor_asc') {
        $q['sort'] = '';
    }
    if ((int)($q['page'] ?? 1) <= 1) {
        $q['page'] = '';
    }

    $q = array_filter($q, static function ($v) {
        return $v !== '' && $v !== null;
    });

    return 'admin_kamar.php' . ($q ? '?' . http_build_query($q) : '');
}

function kamar_base_sql(bool $arsip = false): string
{
    $kondisiHapus = $arsip ? 'k.dihapus_pada IS NOT NULL' : 'k.dihapus_pada IS NULL';

    return "
        SELECT k.*,
               CASE
                   WHEN k.status = 'Perbaikan' THEN 'Perbaikan'
                   WHEN aktif.kamar_id IS NOT NULL THEN 'Terisi'
                   WHEN mendatang.kamar_id IS NOT NULL THEN 'Dipesan'
                   ELSE COALESCE(k.status, 'Tersedia')
               END AS status_efektif
        FROM kamar k
        LEFT JOIN LATERAL (
            SELECT r.kamar_id
            FROM reservasi r
            WHERE r.kamar_id = k.id
              AND r.status_booking IN ('Di-Keep', 'Menunggu Konfirmasi', 'Dibayar')
              AND tsrange(r.waktu_checkin, r.waktu_checkout) @> LOCALTIMESTAMP
            LIMIT 1
        ) aktif ON TRUE
        LEFT JOIN LATERAL (
            SELECT r.kamar_id
            FROM reservasi r
            WHERE r.kamar_id = k.id
              AND r.status_booking IN ('Di-Keep', 'Menunggu Konfirmasi', 'Dibayar')
              AND r.waktu_checkin > LOCALTIMESTAMP
            LIMIT 1
        ) mendatang ON TRUE
        WHERE $kondisiHapus
    ";
}

function kamar_filter(array $get): array
{
    $sortOk = ['nomor_asc', 'harga_asc', 'harga_desc', 'kapasitas_desc', 'terbaru'];
    $sort   = kamar_str($get['sort'] ?? '');
    $status = kamar_str($get['status'] ?? '');

    return [
        'q'      => kamar_str($get['q'] ?? ''),
        'tipe'   => kamar_str($get['tipe'] ?? ''),
        'status' => in_array($status, KAMAR_STATUS, true) ? $status : '',
        'sort'   => in_array($sort, $sortOk, true) ? $sort : 'nomor_asc',
        'page'   => max(1, (int)kamar_str($get['page'] ?? '1')),
        'arsip'  => !empty($get['arsip']),
    ];
}

/** @return array{0: array, 1: int, 2: int, 3: int} [baris, total, halaman, jumlahHalaman] */
function kamar_list(PDO $pdo, array $f): array
{
    $where  = [];
    $params = [];

    if ($f['q'] !== '') {
        $like = '%' . addcslashes($f['q'], '%_\\') . '%';
        $where[] = '(x.nomor_kamar ILIKE :q1 OR x.tipe_kamar ILIKE :q2)';
        $params['q1'] = $like;
        $params['q2'] = $like;
    }
    if ($f['tipe'] !== '') {
        $where[] = 'x.tipe_kamar = :tipe';
        $params['tipe'] = $f['tipe'];
    }
    if ($f['status'] !== '') {
        if ($f['status'] === 'Terisi') {
            // "Terisi" mencakup kamar yang sedang dihuni dan yang sudah dipesan untuk tanggal mendatang.
            $where[] = "x.status_efektif IN ('Terisi', 'Dipesan')";
        } else {
            $where[] = 'x.status_efektif = :status';
            $params['status'] = $f['status'];
        }
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $from     = '(' . kamar_base_sql($f['arsip']) . ') x';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $from $whereSql");
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    $jumlahHalaman = max(1, (int)ceil($total / KAMAR_PER_HALAMAN));
    $halaman       = min($f['page'], $jumlahHalaman);

    $urutan = [
        'nomor_asc'      => 'x.nomor_kamar ASC',
        'harga_asc'      => 'x.harga_per_malam ASC, x.nomor_kamar ASC',
        'harga_desc'     => 'x.harga_per_malam DESC, x.nomor_kamar ASC',
        'kapasitas_desc' => 'x.kapasitas DESC, x.nomor_kamar ASC',
        'terbaru'        => 'x.created_at DESC, x.id DESC',
    ][$f['sort']];

    $offset = ($halaman - 1) * KAMAR_PER_HALAMAN;
    $stmt = $pdo->prepare(
        "SELECT x.* FROM $from $whereSql ORDER BY $urutan LIMIT " . KAMAR_PER_HALAMAN . " OFFSET $offset"
    );
    $stmt->execute($params);

    return [$stmt->fetchAll(), $total, $halaman, $jumlahHalaman];
}

function kamar_stats(PDO $pdo): array
{
    $rows = $pdo->query(
        'SELECT x.nomor_kamar, x.catatan_perbaikan, x.status_efektif FROM (' . kamar_base_sql(false) . ') x ORDER BY x.nomor_kamar'
    )->fetchAll();

    $stats = ['total' => count($rows), 'Tersedia' => 0, 'Terisi' => 0, 'Perbaikan' => 0, 'catatan_perbaikan' => []];
    foreach ($rows as $r) {
        $s = (string)$r['status_efektif'];
        if ($s === 'Dipesan') {
            $s = 'Terisi';
        }
        if (isset($stats[$s])) {
            $stats[$s]++;
        }
        if ($s === 'Perbaikan') {
            $catatan = trim((string)($r['catatan_perbaikan'] ?? ''));
            $stats['catatan_perbaikan'][] = 'Kamar ' . $r['nomor_kamar'] . ($catatan !== '' ? ' ' . $catatan : ' dalam perbaikan');
        }
    }

    return $stats;
}

function kamar_tipe_list(PDO $pdo): array
{
    return $pdo->query(
        'SELECT DISTINCT tipe_kamar FROM kamar WHERE dihapus_pada IS NULL ORDER BY tipe_kamar'
    )->fetchAll(PDO::FETCH_COLUMN);
}

function kamar_arsip_count(PDO $pdo): int
{
    return (int)$pdo->query('SELECT COUNT(*) FROM kamar WHERE dihapus_pada IS NOT NULL')->fetchColumn();
}

/** @return array{0: string[], 1: array} [daftarError, dataBersih] */
function kamar_validate(PDO $pdo, array $in, int $editId = 0): array
{
    $errors = [];

    $nomor     = kamar_str($in['nomor_kamar'] ?? '');
    $tipe      = kamar_str($in['tipe_kamar'] ?? '');
    $status    = kamar_str($in['status'] ?? 'Tersedia');
    $deskripsi = kamar_str($in['deskripsi'] ?? '');
    $catatan   = kamar_str($in['catatan_perbaikan'] ?? '');
    $lantai    = filter_var(kamar_str($in['lantai'] ?? ''), FILTER_VALIDATE_INT);
    $kapasitas = filter_var(kamar_str($in['kapasitas'] ?? ''), FILTER_VALIDATE_INT);
    $harga     = filter_var(kamar_str($in['harga_per_malam'] ?? ''), FILTER_VALIDATE_INT);

    if ($nomor === '' || text_length($nomor) > 10 || !preg_match('/^[A-Za-z0-9\-]+$/', $nomor)) {
        $errors[] = 'Nomor kamar wajib diisi (maksimal 10 karakter, hanya huruf, angka, dan tanda minus).';
    } else {
        $cek = $pdo->prepare('SELECT id, dihapus_pada FROM kamar WHERE nomor_kamar = :n AND id <> :id');
        $cek->execute(['n' => $nomor, 'id' => $editId]);
        $ada = $cek->fetch();
        if ($ada) {
            $errors[] = $ada['dihapus_pada'] !== null
                ? 'Nomor kamar ' . $nomor . ' dipakai kamar yang sudah dihapus. Pulihkan kamar itu dari daftar kamar terhapus, atau pakai nomor lain.'
                : 'Nomor kamar ' . $nomor . ' sudah dipakai kamar lain.';
        }
    }
    if ($tipe === '' || text_length($tipe) > 50) {
        $errors[] = 'Tipe kamar wajib diisi (maksimal 50 karakter).';
    }
    if ($lantai === false || $lantai < 1 || $lantai > 20) {
        $errors[] = 'Lantai harus berupa angka 1-20.';
    }
    if ($kapasitas === false || $kapasitas < 1 || $kapasitas > 50) {
        $errors[] = 'Kapasitas harus berupa angka 1-50 tamu.';
    }
    if ($harga === false || $harga < 0 || $harga > 100000000) {
        $errors[] = 'Harga per malam harus berupa angka bulat tanpa titik, antara 0 dan 100.000.000.';
    }
    // "Dipesan" = tamu offline. Bukan nilai kolom kamar.status: disimpan sebagai reservasi.
    $statusForm = $status;
    $offline    = null;
    if ($status === 'Dipesan') {
        [$errOff, $offline] = kamar_validate_offline($in, is_int($kapasitas) ? $kapasitas : 0);
        $errors = array_merge($errors, $errOff);
        $status = 'Tersedia';
    } elseif (!in_array($status, KAMAR_STATUS, true)) {
        $errors[] = 'Status kamar tidak valid.';
        $status = 'Tersedia';
        $statusForm = 'Tersedia';
    }
    if (text_length($deskripsi) > 2000) {
        $errors[] = 'Deskripsi maksimal 2000 karakter.';
    }
    if (text_length($catatan) > 150) {
        $errors[] = 'Catatan perbaikan maksimal 150 karakter.';
    }

    $clean = [
        'nomor_kamar'       => $nomor,
        'tipe_kamar'        => $tipe,
        'lantai'            => $lantai === false ? kamar_str($in['lantai'] ?? '') : $lantai,
        'kapasitas'         => $kapasitas === false ? kamar_str($in['kapasitas'] ?? '') : $kapasitas,
        'harga_per_malam'   => $harga === false ? kamar_str($in['harga_per_malam'] ?? '') : $harga,
        'status'            => $status,
        'status_form'       => $statusForm,
        'deskripsi'         => $deskripsi,
        'catatan_perbaikan' => $status === 'Perbaikan' ? $catatan : '',
        'offline'           => $offline,
        'off_nama'          => kamar_str($in['off_nama'] ?? ''),
        'off_whatsapp'      => kamar_str($in['off_whatsapp'] ?? ''),
        'off_tamu'          => kamar_str($in['off_tamu'] ?? ''),
        'off_checkin'       => kamar_str($in['off_checkin'] ?? ''),
        'off_checkout'      => kamar_str($in['off_checkout'] ?? ''),
    ];

    return [$errors, $clean];
}

/** @return array{0: string[], 1: ?array} [daftarError, dataOffline] */
function kamar_validate_offline(array $in, int $kapasitas): array
{
    $errors   = [];
    $nama     = kamar_str($in['off_nama'] ?? '');
    $wa       = preg_replace('/[\s\-]/', '', kamar_str($in['off_whatsapp'] ?? ''));
    $tamuRaw  = kamar_str($in['off_tamu'] ?? '');
    $tamu     = $tamuRaw === '' ? 1 : filter_var($tamuRaw, FILTER_VALIDATE_INT);
    $masuk    = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', kamar_str($in['off_checkin'] ?? ''));
    $keluar   = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', kamar_str($in['off_checkout'] ?? ''));

    if (text_length($nama) > 100) {
        $errors[] = 'Nama tamu maksimal 100 karakter.';
    }
    if ($wa !== '' && !preg_match('/^\+?\d{9,15}$/', $wa)) {
        $errors[] = 'Nomor WhatsApp tamu tidak valid (9-15 digit), atau kosongkan saja.';
    }
    if ($tamu === false || $tamu < 1 || ($kapasitas > 0 && $tamu > $kapasitas)) {
        $errors[] = 'Jumlah tamu harus 1 sampai ' . max(1, $kapasitas) . ' orang.';
    }
    if (!$masuk) {
        $errors[] = 'Tanggal dan jam check-in wajib diisi.';
    }
    if (!$keluar) {
        $errors[] = 'Tanggal dan jam check-out wajib diisi.';
    }
    if ($masuk && $keluar && $keluar <= $masuk) {
        $errors[] = 'Waktu check-out harus setelah waktu check-in.';
    }
    if ($masuk && $keluar && ($keluar->getTimestamp() - $masuk->getTimestamp()) > 90 * 86400) {
        $errors[] = 'Rentang menginap maksimal 90 hari.';
    }
    if ($errors) {
        return [$errors, null];
    }

    if ($wa !== '') {
        $wa = ltrim($wa, '+');
        if (strpos($wa, '0') === 0) {
            $wa = '62' . substr($wa, 1);
        } elseif (strpos($wa, '62') !== 0) {
            $wa = '62' . $wa;
        }
    }

    return [[], [
        'nama'     => $nama !== '' ? $nama : 'Tamu Offline',
        'whatsapp' => $wa !== '' ? $wa : '-',
        'tamu'     => (int)$tamu,
        'checkin'  => $masuk,
        'checkout' => $keluar,
    ]];
}

/** Catat tamu offline sebagai reservasi "Dibayar" agar tampil sebagai "Dipesan" di beranda & admin. */
function kamar_buat_reservasi_offline(PDO $pdo, int $kamarId, array $o): int
{
    $kamar = $pdo->prepare('SELECT nomor_kamar, harga_per_malam FROM kamar WHERE id = :id AND dihapus_pada IS NULL FOR UPDATE');
    $kamar->execute(['id' => $kamarId]);
    $k = $kamar->fetch();
    if (!$k) {
        throw new InvalidArgumentException('Kamar tidak ditemukan atau sudah dihapus.');
    }

    $detik  = $o['checkout']->getTimestamp() - $o['checkin']->getTimestamp();
    $durasi = max(1, (int)ceil($detik / 3600));
    $tarif  = (float)$k['harga_per_malam'];
    if ($durasi >= 24) {
        $total = ceil($durasi / 24) * $tarif;
    } else {
        $paket = short_package_prices($pdo);
        $total = $paket[$durasi] ?? $tarif;
    }

    $insert = $pdo->prepare("
        INSERT INTO reservasi
            (kode_booking, kamar_id, nama_pemesan, whatsapp_pemesan, jumlah_tamu, catatan,
             waktu_checkin, durasi_jam, waktu_checkout, total_tagihan, status_booking,
             batas_waktu_keep, setuju_kebijakan)
        VALUES
            (:kode, :kamar, :nama, :wa, :tamu, 'Pesanan offline (dicatat admin)',
             :masuk, :durasi, :keluar, :total, 'Dibayar',
             LOCALTIMESTAMP, TRUE)
        RETURNING id
    ");

    $nomor = preg_replace('/[^A-Za-z0-9]/', '', (string)$k['nomor_kamar']);
    $id = 0;
    try {
        for ($i = 0; $i < 3; $i++) {
            try {
                $insert->execute([
                    'kode'   => sprintf('OFF-%s-K%s-%s', $o['checkin']->format('Ymd'), $nomor, strtoupper(bin2hex(random_bytes(2)))),
                    'kamar'  => $kamarId,
                    'nama'   => $o['nama'],
                    'wa'     => $o['whatsapp'],
                    'tamu'   => $o['tamu'],
                    'masuk'  => $o['checkin']->format('Y-m-d H:i:s'),
                    'durasi' => $durasi,
                    'keluar' => $o['checkout']->format('Y-m-d H:i:s'),
                    'total'  => $total,
                ]);
                $id = (int)$insert->fetchColumn();
                break;
            } catch (PDOException $e) {
                if ($e->getCode() === '23505' && $i < 2) {
                    continue;
                }
                throw $e;
            }
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23P01') {
            throw new InvalidArgumentException('Jadwal itu bentrok dengan reservasi lain di kamar ini. Pilih tanggal/jam yang lain.');
        }
        throw $e;
    }

    $pdo->prepare("
        INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru, diubah_oleh)
        VALUES (:id, NULL, 'Dibayar', :admin)
    ")->execute(['id' => $id, 'admin' => $_SESSION['admin_id'] ?? null]);

    return $id;
}

function kamar_foto_dir(): string
{
    return dirname(__DIR__) . '/public/uploads/kamar';
}

function kamar_simpan_foto(array $file): ?string
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        throw new InvalidArgumentException('Ukuran foto terlalu besar (maksimal 2 MB).');
    }
    if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string)$file['tmp_name'])) {
        throw new InvalidArgumentException('Upload foto gagal. Silakan coba lagi.');
    }
    if ((int)$file['size'] > KAMAR_FOTO_MAKS) {
        throw new InvalidArgumentException('Ukuran foto terlalu besar (maksimal 2 MB).');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string)$file['tmp_name']);
    $ext  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if ($ext === null || @getimagesize((string)$file['tmp_name']) === false) {
        throw new InvalidArgumentException('Format foto harus JPG, PNG, atau WEBP.');
    }

    $dir = kamar_foto_dir();
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('Folder upload foto kamar tidak bisa dibuat.');
    }

    $nama = 'kamar-' . bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file((string)$file['tmp_name'], $dir . '/' . $nama)) {
        throw new RuntimeException('Gagal menyimpan foto kamar.');
    }

    return '/uploads/kamar/' . $nama;
}

function kamar_hapus_file(?string $url): void
{
    if ($url === null || strpos($url, '/uploads/kamar/') !== 0) {
        return;
    }
    $path = kamar_foto_dir() . '/' . basename($url);
    if (is_file($path)) {
        @unlink($path);
    }
}

const KAMAR_FOTO_MAKS_JUMLAH = 10;

function kamar_foto_list(PDO $pdo, int $kamarId): array
{
    $stmt = $pdo->prepare('SELECT id, url, label, urutan FROM kamar_foto WHERE kamar_id = :id ORDER BY urutan, id');
    $stmt->execute(['id' => $kamarId]);
    return $stmt->fetchAll();
}

function kamar_foto_map(PDO $pdo, array $kamarIds): array
{
    $peta = [];
    $kamarIds = array_values(array_filter(array_map('intval', $kamarIds)));
    if (!$kamarIds) {
        return $peta;
    }
    $in = implode(',', $kamarIds);
    foreach ($pdo->query("SELECT id, kamar_id, url, label FROM kamar_foto WHERE kamar_id IN ($in) ORDER BY urutan, id") as $r) {
        $peta[(int)$r['kamar_id']][] = $r;
    }
    return $peta;
}

/** Simpan banyak file sekaligus. Return: [['url' => ..., 'label' => ...], ...] */
function kamar_simpan_foto_banyak(array $files, array $labels): array
{
    $hasil = [];
    $n = is_array($files['name'] ?? null) ? count($files['name']) : 0;
    try {
        for ($i = 0; $i < $n; $i++) {
            $url = kamar_simpan_foto([
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ]);
            if ($url !== null) {
                $hasil[] = ['url' => $url, 'label' => mb_substr(kamar_str($labels[$i] ?? ''), 0, 50)];
            }
        }
    } catch (Throwable $e) {
        foreach ($hasil as $f) {
            kamar_hapus_file($f['url']);
        }
        throw $e;
    }
    return $hasil;
}

function kamar_foto_sinkron(PDO $pdo, int $kamarId, array $baru, array $in): array
{
    $ada       = kamar_foto_list($pdo, $kamarId);
    $hapusIds  = array_map('intval', (array)($in['hapus_foto_id'] ?? []));
    $labelLama = (array)($in['label_lama'] ?? []);
    $sampulId  = (int)($in['sampul'] ?? 0);

    $sisa = 0;
    foreach ($ada as $f) {
        if (!in_array((int)$f['id'], $hapusIds, true)) {
            $sisa++;
        }
    }
    if ($sisa + count($baru) > KAMAR_FOTO_MAKS_JUMLAH) {
        throw new InvalidArgumentException('Maksimal ' . KAMAR_FOTO_MAKS_JUMLAH . ' foto per kamar.');
    }

    $lepas = [];
    $upd = $pdo->prepare('UPDATE kamar_foto SET label = :l WHERE id = :id AND kamar_id = :k');
    $del = $pdo->prepare('DELETE FROM kamar_foto WHERE id = :id AND kamar_id = :k');
    foreach ($ada as $f) {
        $id = (int)$f['id'];
        if (in_array($id, $hapusIds, true)) {
            $del->execute(['id' => $id, 'k' => $kamarId]);
            $lepas[] = (string)$f['url'];
        } else {
            $label = mb_substr(kamar_str($labelLama[$id] ?? ''), 0, 50);
            $upd->execute(['l' => $label !== '' ? $label : null, 'id' => $id, 'k' => $kamarId]);
        }
    }

    $urutan = (int)$pdo->query('SELECT COALESCE(MAX(urutan), -1) FROM kamar_foto WHERE kamar_id = ' . $kamarId)->fetchColumn();
    $ins = $pdo->prepare('INSERT INTO kamar_foto (kamar_id, url, label, urutan) VALUES (:k, :u, :l, :o)');
    foreach ($baru as $f) {
        $urutan++;
        $ins->execute(['k' => $kamarId, 'u' => $f['url'], 'l' => $f['label'] !== '' ? $f['label'] : null, 'o' => $urutan]);
    }

    if ($sampulId > 0) {
        $cek = $pdo->prepare('SELECT 1 FROM kamar_foto WHERE id = :id AND kamar_id = :k');
        $cek->execute(['id' => $sampulId, 'k' => $kamarId]);
        if ($cek->fetchColumn()) {
            $pdo->prepare('UPDATE kamar_foto SET urutan = urutan + 1 WHERE kamar_id = :k')->execute(['k' => $kamarId]);
            $pdo->prepare('UPDATE kamar_foto SET urutan = 0 WHERE id = :id')->execute(['id' => $sampulId]);
        }
    }

    $pdo->prepare(
        'UPDATE kamar SET foto_url = (SELECT url FROM kamar_foto WHERE kamar_id = :k ORDER BY urutan, id LIMIT 1) WHERE id = :k2'
    )->execute(['k' => $kamarId, 'k2' => $kamarId]);

    return $lepas;
}

function kamar_tambah(PDO $pdo, array $d, ?string $foto): void
{
    $stmt = $pdo->prepare('
        INSERT INTO kamar
            (nomor_kamar, tipe_kamar, lantai, kapasitas, harga_per_malam, status,
             deskripsi, foto_url, catatan_perbaikan, kondisi_diperbarui_at)
        VALUES
            (:nomor, :tipe, :lantai, :kapasitas, :harga, :status,
             :deskripsi, :foto, :catatan, CURRENT_TIMESTAMP)
    ');
    $stmt->execute([
        'nomor'     => $d['nomor_kamar'],
        'tipe'      => $d['tipe_kamar'],
        'lantai'    => $d['lantai'],
        'kapasitas' => $d['kapasitas'],
        'harga'     => $d['harga_per_malam'],
        'status'    => $d['status'],
        'deskripsi' => $d['deskripsi'] !== '' ? $d['deskripsi'] : null,
        'foto'      => $foto,
        'catatan'   => $d['catatan_perbaikan'] !== '' ? $d['catatan_perbaikan'] : null,
    ]);
}

function kamar_ubah(PDO $pdo, int $id, array $d, ?string $fotoBaru, bool $hapusFoto): void
{
    $lama = $pdo->prepare('SELECT foto_url FROM kamar WHERE id = :id AND dihapus_pada IS NULL');
    $lama->execute(['id' => $id]);
    $row = $lama->fetch();
    if (!$row) {
        throw new InvalidArgumentException('Kamar tidak ditemukan atau sudah dihapus.');
    }

    $fotoLama  = $row['foto_url'];
    $fotoFinal = $fotoLama;
    if ($fotoBaru !== null) {
        $fotoFinal = $fotoBaru;
    } elseif ($hapusFoto) {
        $fotoFinal = null;
    }

    $stmt = $pdo->prepare('
        UPDATE kamar SET
            nomor_kamar = :nomor, tipe_kamar = :tipe, lantai = :lantai, kapasitas = :kapasitas,
            harga_per_malam = :harga, status = :status, deskripsi = :deskripsi,
            foto_url = :foto, catatan_perbaikan = :catatan,
            kondisi_diperbarui_at = CURRENT_TIMESTAMP
        WHERE id = :id AND dihapus_pada IS NULL
    ');
    $stmt->execute([
        'nomor'     => $d['nomor_kamar'],
        'tipe'      => $d['tipe_kamar'],
        'lantai'    => $d['lantai'],
        'kapasitas' => $d['kapasitas'],
        'harga'     => $d['harga_per_malam'],
        'status'    => $d['status'],
        'deskripsi' => $d['deskripsi'] !== '' ? $d['deskripsi'] : null,
        'foto'      => $fotoFinal,
        'catatan'   => $d['catatan_perbaikan'] !== '' ? $d['catatan_perbaikan'] : null,
        'id'        => $id,
    ]);

    if ($fotoFinal !== $fotoLama) {
        kamar_hapus_file($fotoLama);
    }
}

function kamar_hapus(PDO $pdo, int $id): string
{
    $cari = $pdo->prepare('SELECT nomor_kamar FROM kamar WHERE id = :id AND dihapus_pada IS NULL');
    $cari->execute(['id' => $id]);
    $nomor = $cari->fetchColumn();
    if ($nomor === false) {
        throw new InvalidArgumentException('Kamar tidak ditemukan atau sudah dihapus.');
    }

    $aktif = $pdo->prepare("
        SELECT COUNT(*) FROM reservasi
        WHERE kamar_id = :id
          AND status_booking IN ('Di-Keep', 'Menunggu Konfirmasi', 'Dibayar')
          AND waktu_checkout > LOCALTIMESTAMP
    ");
    $aktif->execute(['id' => $id]);
    if ((int)$aktif->fetchColumn() > 0) {
        throw new InvalidArgumentException(
            'Kamar ' . $nomor . ' masih punya reservasi aktif atau mendatang, jadi belum bisa dihapus. Selesaikan atau batalkan reservasinya dulu.'
        );
    }

    $stmt = $pdo->prepare('UPDATE kamar SET dihapus_pada = CURRENT_TIMESTAMP WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return (string)$nomor;
}

function kamar_pulihkan(PDO $pdo, int $id): string
{
    $cari = $pdo->prepare('SELECT nomor_kamar FROM kamar WHERE id = :id AND dihapus_pada IS NOT NULL');
    $cari->execute(['id' => $id]);
    $nomor = $cari->fetchColumn();
    if ($nomor === false) {
        throw new InvalidArgumentException('Kamar tidak ditemukan di daftar kamar terhapus.');
    }

    $stmt = $pdo->prepare('UPDATE kamar SET dihapus_pada = NULL, kondisi_diperbarui_at = CURRENT_TIMESTAMP WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return (string)$nomor;
}

function kamar_laporan_data(PDO $pdo): array
{
    $rows = $pdo->query(
        'SELECT x.* FROM (' . kamar_base_sql(false) . ') x ORDER BY x.nomor_kamar'
    )->fetchAll();

    $kepala = ['No', 'Nomor Kamar', 'Tipe Kamar', 'Lantai', 'Kapasitas (tamu)', 'Harga per Malam',
               'Status', 'Fasilitas', 'Catatan Perbaikan', 'Deskripsi', 'Diperbarui'];

    $daftar = [];
    $daftar[] = array_map(static function ($t) {
        return [$t, 1];
    }, $kepala);

    $no = 0;
    $hitung = ['Tersedia' => 0, 'Terisi' => 0, 'Perbaikan' => 0];
    foreach ($rows as $r) {
        $no++;
        $status = (string)$r['status_efektif'];
        $kunci  = $status === 'Dipesan' ? 'Terisi' : $status;
        if (isset($hitung[$kunci])) {
            $hitung[$kunci]++;
        }
        $fasilitas = implode(', ', array_column(getFasilitasKamar((string)($r['deskripsi'] ?? '')), 'label'));
        $waktu = !empty($r['kondisi_diperbarui_at']) ? strtotime((string)$r['kondisi_diperbarui_at']) : false;
        $daftar[] = [
            $no,
            (string)$r['nomor_kamar'],
            (string)$r['tipe_kamar'],
            (int)$r['lantai'],
            (int)$r['kapasitas'],
            [(int)$r['harga_per_malam'], 2],
            $status,
            $fasilitas,
            (string)($r['catatan_perbaikan'] ?? ''),
            [(string)($r['deskripsi'] ?? ''), 4],
            $waktu ? date('d/m/Y H:i', $waktu) : '',
        ];
    }

    $ringkasan = [
        [['Laporan Kamar - ' . villa_name(), 3]],
        [],
        ['Dibuat pada', date('d/m/Y H:i')],
        ['Total kamar', $no],
        ['Tersedia', $hitung['Tersedia']],
        ['Terisi', $hitung['Terisi']],
        ['Dalam perbaikan', $hitung['Perbaikan']],
        ['Kamar terhapus (arsip)', kamar_arsip_count($pdo)],
    ];

    return ['daftar' => $daftar, 'ringkasan' => $ringkasan, 'jumlah' => $no];
}

function kamar_xlsx_kolom(int $i): string
{
    $huruf = '';
    $n = $i + 1;
    while ($n > 0) {
        $m = ($n - 1) % 26;
        $huruf = chr(65 + $m) . $huruf;
        $n = intdiv($n - 1, 26);
    }
    return $huruf;
}

function kamar_xml(string $teks): string
{
    $teks = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $teks) ?? '';
    return htmlspecialchars($teks, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function kamar_xlsx_sheet(array $rows, array $lebar, bool $bekukanBaris1): string
{
    $xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
    if ($bekukanBaris1) {
        $xml .= '<sheetViews><sheetView workbookViewId="0">'
              . '<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>'
              . '<selection pane="bottomLeft"/></sheetView></sheetViews>';
    }
    $xml .= '<cols>';
    foreach ($lebar as $i => $w) {
        $n = $i + 1;
        $xml .= '<col min="' . $n . '" max="' . $n . '" width="' . $w . '" customWidth="1"/>';
    }
    $xml .= '</cols><sheetData>';

    $kolomTerjauh = 0;
    foreach ($rows as $r => $sel) {
        $xml .= '<row r="' . ($r + 1) . '">';
        foreach ($sel as $c => $cell) {
            $nilai = $cell;
            $gaya  = 0;
            if (is_array($cell)) {
                $nilai = $cell[0];
                $gaya  = (int)$cell[1];
            }
            $kolomTerjauh = max($kolomTerjauh, $c + 1);
            $ref   = kamar_xlsx_kolom((int)$c) . ($r + 1);
            $atrS  = $gaya > 0 ? ' s="' . $gaya . '"' : '';
            if ($nilai === null || $nilai === '') {
                if ($gaya > 0) {
                    $xml .= '<c r="' . $ref . '"' . $atrS . '/>';
                }
            } elseif (is_int($nilai) || is_float($nilai)) {
                $xml .= '<c r="' . $ref . '"' . $atrS . '><v>' . $nilai . '</v></c>';
            } else {
                $xml .= '<c r="' . $ref . '"' . $atrS . ' t="inlineStr"><is><t xml:space="preserve">'
                      . kamar_xml((string)$nilai) . '</t></is></c>';
            }
        }
        $xml .= '</row>';
    }
    $xml .= '</sheetData>';

    if ($bekukanBaris1 && count($rows) > 1 && $kolomTerjauh > 0) {
        $xml .= '<autoFilter ref="A1:' . kamar_xlsx_kolom($kolomTerjauh - 1) . count($rows) . '"/>';
    }
    $xml .= '</worksheet>';

    return $xml;
}

/**
 * @param array<int, array{nama: string, rows: array, lebar: array, bekukan: bool}> $sheets
 */
function kamar_xlsx_bangun(array $sheets): string
{
    $tmp = tempnam(sys_get_temp_dir(), 'kamar');
    if ($tmp === false) {
        throw new RuntimeException('Tidak bisa membuat berkas sementara.');
    }

    $zip = new ZipArchive();
    if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        @unlink($tmp);
        throw new RuntimeException('Tidak bisa membuat berkas Excel.');
    }

    $head = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    $ns   = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    $nsR  = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    $nsRel = 'http://schemas.openxmlformats.org/package/2006/relationships';

    $tipe = $head . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
          . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
          . '<Default Extension="xml" ContentType="application/xml"/>'
          . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
          . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
    $sheetXml = '';
    $wbRel = '';
    foreach ($sheets as $i => $s) {
        $n = $i + 1;
        $tipe .= '<Override PartName="/xl/worksheets/sheet' . $n . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        $sheetXml .= '<sheet name="' . kamar_xml($s['nama']) . '" sheetId="' . $n . '" r:id="rId' . $n . '"/>';
        $wbRel .= '<Relationship Id="rId' . $n . '" Type="' . $nsR . '/worksheet" Target="worksheets/sheet' . $n . '.xml"/>';
        $zip->addFromString('xl/worksheets/sheet' . $n . '.xml', kamar_xlsx_sheet($s['rows'], $s['lebar'], $s['bekukan']));
    }
    $tipe .= '</Types>';
    $wbRel .= '<Relationship Id="rId' . (count($sheets) + 1) . '" Type="' . $nsR . '/styles" Target="styles.xml"/>';

    $styles = $head . '<styleSheet xmlns="' . $ns . '">'
        . '<numFmts count="1"><numFmt numFmtId="164" formatCode="&quot;Rp&quot; #,##0"/></numFmts>'
        . '<fonts count="3">'
        . '<font><sz val="11"/><name val="Calibri"/></font>'
        . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
        . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
        . '</fonts>'
        . '<fills count="3">'
        . '<fill><patternFill patternType="none"/></fill>'
        . '<fill><patternFill patternType="gray125"/></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FFA0522D"/><bgColor indexed="64"/></patternFill></fill>'
        . '</fills>'
        . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="5">'
        . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
        . '<xf numFmtId="0" fontId="2" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
        . '<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
        . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
        . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
        . '</cellXfs>'
        . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
        . '</styleSheet>';

    $zip->addFromString('[Content_Types].xml', $tipe);
    $zip->addFromString('_rels/.rels', $head . '<Relationships xmlns="' . $nsRel . '">'
        . '<Relationship Id="rId1" Type="' . $nsR . '/officeDocument" Target="xl/workbook.xml"/></Relationships>');
    $zip->addFromString('xl/workbook.xml', $head . '<workbook xmlns="' . $ns . '" xmlns:r="' . $nsR . '"><sheets>'
        . $sheetXml . '</sheets></workbook>');
    $zip->addFromString('xl/_rels/workbook.xml.rels', $head . '<Relationships xmlns="' . $nsRel . '">' . $wbRel . '</Relationships>');
    $zip->addFromString('xl/styles.xml', $styles);

    if (!$zip->close()) {
        @unlink($tmp);
        throw new RuntimeException('Gagal menyelesaikan berkas Excel.');
    }

    $isi = file_get_contents($tmp);
    @unlink($tmp);
    if ($isi === false) {
        throw new RuntimeException('Gagal membaca berkas Excel.');
    }

    return $isi;
}

function kamar_kirim_laporan(PDO $pdo, string $format): void
{
    $data    = kamar_laporan_data($pdo);
    $stempel = date('Ymd-His');

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if ($format !== 'csv' && class_exists('ZipArchive')) {
        $isi = kamar_xlsx_bangun([
            ['nama' => 'Daftar Kamar', 'rows' => $data['daftar'],
             'lebar' => [5, 14, 26, 8, 16, 18, 14, 34, 28, 60, 18], 'bekukan' => true],
            ['nama' => 'Ringkasan', 'rows' => $data['ringkasan'],
             'lebar' => [30, 24], 'bekukan' => false],
        ]);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="laporan-kamar-' . $stempel . '.xlsx"');
        header('Content-Length: ' . strlen($isi));
        header('Cache-Control: no-store');
        echo $isi;
        exit;
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="laporan-kamar-' . $stempel . '.csv"');
    header('Cache-Control: no-store');
    echo "\xEF\xBB\xBF" . "sep=;\r\n";
    $out = fopen('php://output', 'w');
    foreach ($data['daftar'] as $baris) {
        $datar = [];
        foreach ($baris as $cell) {
            $datar[] = is_array($cell) ? $cell[0] : $cell;
        }
        fputcsv($out, $datar, ';', '"', '\\');
    }
    fclose($out);
    exit;
}