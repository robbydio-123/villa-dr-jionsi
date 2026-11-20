<?php

declare(strict_types=1);

require_once __DIR__ . '/admin_kamar.php';

const FASILITAS_KATEGORI = ['Fasilitas Kamar', 'Area Umum', 'Layanan Tambahan'];
const FASILITAS_STATUS   = ['Aktif', 'Nonaktif'];
const FASILITAS_PER_HALAMAN = 10;

const FASILITAS_IKON = [
    'bi-stars'             => 'Umum',
    'bi-wifi'              => 'Wi-Fi',
    'bi-tv'                => 'TV',
    'bi-droplet-half'      => 'Air panas',
    'bi-door-closed'       => 'Kamar mandi',
    'bi-cup-hot'           => 'Makan & minum',
    'bi-image'             => 'Pemandangan',
    'bi-car-front'         => 'Parkir / antar-jemput',
    'bi-tree'              => 'Taman',
    'bi-snow'              => 'Pendingin',
    'bi-wind'              => 'Kipas / angin',
    'bi-fire'              => 'Perapian / BBQ',
    'bi-shield-check'      => 'Keamanan',
    'bi-camera-video'      => 'CCTV',
    'bi-bell'              => 'Layanan',
    'bi-basket'            => 'Laundry',
    'bi-moon-stars'        => 'Ketenangan',
    'bi-music-note-beamed' => 'Karaoke / musik',
    'bi-controller'        => 'Permainan',
    'bi-bicycle'           => 'Sepeda',
    'bi-umbrella'          => 'Payung',
    'bi-heart'             => 'Spa / relaksasi',
    'bi-lightning-charge'  => 'Listrik',
    'bi-house-door'        => 'Ruang bersama',
    'bi-key'               => 'Akses / kunci',
];

function fasilitas_url(array $filter, array $ubah = []): string
{
    $q = array_merge([
        'q'        => $filter['q'] ?? '',
        'kategori' => $filter['kategori'] ?? '',
        'status'   => $filter['status'] ?? '',
        'sort'     => $filter['sort'] ?? 'nama_asc',
        'page'     => $filter['page'] ?? 1,
        'arsip'    => !empty($filter['arsip']) ? 1 : '',
    ], $ubah);

    if (($q['sort'] ?? '') === 'nama_asc') {
        $q['sort'] = '';
    }
    if ((int)($q['page'] ?? 1) <= 1) {
        $q['page'] = '';
    }

    $q = array_filter($q, static function ($v) {
        return $v !== '' && $v !== null;
    });

    return 'admin_fasilitas.php' . ($q ? '?' . http_build_query($q) : '');
}

function fasilitas_filter(array $get): array
{
    $sortOk   = ['nama_asc', 'kategori', 'terbaru'];
    $sort     = kamar_str($get['sort'] ?? '');
    $kategori = kamar_str($get['kategori'] ?? '');
    $status   = kamar_str($get['status'] ?? '');

    return [
        'q'        => kamar_str($get['q'] ?? ''),
        'kategori' => in_array($kategori, FASILITAS_KATEGORI, true) ? $kategori : '',
        'status'   => in_array($status, FASILITAS_STATUS, true) ? $status : '',
        'sort'     => in_array($sort, $sortOk, true) ? $sort : 'nama_asc',
        'page'     => max(1, (int)kamar_str($get['page'] ?? '1')),
        'arsip'    => !empty($get['arsip']),
    ];
}

/** @return array{0: array, 1: int, 2: int, 3: int} */
function fasilitas_list(PDO $pdo, array $f): array
{
    $where  = [$f['arsip'] ? 'dihapus_pada IS NOT NULL' : 'dihapus_pada IS NULL'];
    $params = [];

    if ($f['q'] !== '') {
        $like = '%' . addcslashes($f['q'], '%_\\') . '%';
        $where[] = '(nama ILIKE :q1 OR deskripsi ILIKE :q2)';
        $params['q1'] = $like;
        $params['q2'] = $like;
    }
    if ($f['kategori'] !== '') {
        $where[] = 'kategori = :kategori';
        $params['kategori'] = $f['kategori'];
    }
    if ($f['status'] !== '') {
        $where[] = $f['status'] === 'Aktif' ? 'aktif IS TRUE' : 'aktif IS FALSE';
    }

    $whereSql = 'WHERE ' . implode(' AND ', $where);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fasilitas $whereSql");
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    $jumlahHalaman = max(1, (int)ceil($total / FASILITAS_PER_HALAMAN));
    $halaman       = min($f['page'], $jumlahHalaman);

    $urutan = [
        'nama_asc' => 'nama ASC',
        'kategori' => 'kategori ASC, nama ASC',
        'terbaru'  => 'created_at DESC, id DESC',
    ][$f['sort']];

    $offset = ($halaman - 1) * FASILITAS_PER_HALAMAN;
    $stmt = $pdo->prepare(
        "SELECT * FROM fasilitas $whereSql ORDER BY $urutan LIMIT " . FASILITAS_PER_HALAMAN . " OFFSET $offset"
    );
    $stmt->execute($params);

    return [$stmt->fetchAll(), $total, $halaman, $jumlahHalaman];
}

function fasilitas_stats(PDO $pdo): array
{
    $stats = ['total' => 0, 'aktif' => 0, 'nonaktif' => 0];
    foreach (FASILITAS_KATEGORI as $k) {
        $stats[$k] = 0;
    }

    $rows = $pdo->query(
        'SELECT kategori, aktif, COUNT(*) AS n FROM fasilitas WHERE dihapus_pada IS NULL GROUP BY kategori, aktif'
    )->fetchAll();

    foreach ($rows as $r) {
        $n = (int)$r['n'];
        $stats['total'] += $n;
        $stats[$r['aktif'] ? 'aktif' : 'nonaktif'] += $n;
        if (isset($stats[$r['kategori']])) {
            $stats[$r['kategori']] += $n;
        }
    }

    return $stats;
}

function fasilitas_persen(int $bagian, int $total): int
{
    return $total > 0 ? (int)round($bagian / $total * 100) : 0;
}

function fasilitas_arsip_count(PDO $pdo): int
{
    return (int)$pdo->query('SELECT COUNT(*) FROM fasilitas WHERE dihapus_pada IS NOT NULL')->fetchColumn();
}

/** Buat tabel penghubung fasilitas <-> kamar bila belum ada (untuk database lama). */
function fasilitas_pastikan_tabel_kamar(PDO $pdo): void
{
    if ($pdo->query("SELECT to_regclass('fasilitas_kamar')")->fetchColumn()) {
        return;
    }
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS fasilitas_kamar (
            fasilitas_id INT NOT NULL REFERENCES fasilitas(id) ON DELETE CASCADE,
            kamar_id     INT NOT NULL REFERENCES kamar(id) ON DELETE CASCADE,
            PRIMARY KEY (fasilitas_id, kamar_id)
        );
        CREATE INDEX IF NOT EXISTS fasilitas_kamar_kamar_idx ON fasilitas_kamar (kamar_id);
    ');
}

/** Daftar kamar (yang belum dihapus) untuk pilihan di form. */
function fasilitas_kamar_pilihan(PDO $pdo): array
{
    return $pdo->query(
        'SELECT id, nomor_kamar, tipe_kamar FROM kamar WHERE dihapus_pada IS NULL ORDER BY nomor_kamar'
    )->fetchAll();
}

/** @return array<int, array> fasilitas_id => daftar kamar yang dipasangi fasilitas itu */
function fasilitas_kamar_map(PDO $pdo, array $fasilitasIds): array
{
    $peta = [];
    $ids  = array_values(array_filter(array_map('intval', $fasilitasIds)));
    if (!$ids) {
        return $peta;
    }
    $in   = implode(',', $ids);
    $rows = $pdo->query(
        "SELECT fk.fasilitas_id, k.id, k.nomor_kamar, k.tipe_kamar
         FROM fasilitas_kamar fk
         JOIN kamar k ON k.id = fk.kamar_id
         WHERE fk.fasilitas_id IN ($in) AND k.dihapus_pada IS NULL
         ORDER BY k.nomor_kamar"
    )->fetchAll();
    foreach ($rows as $r) {
        $peta[(int)$r['fasilitas_id']][] = $r;
    }
    return $peta;
}

/** Teks "Kamar 101, Kamar 102" dari daftar id kamar. */
function fasilitas_kamar_label(array $pilihan, array $ids): string
{
    $nama = [];
    foreach ($pilihan as $k) {
        if (in_array((int)$k['id'], $ids, true)) {
            $nama[] = 'Kamar ' . $k['nomor_kamar'];
        }
    }
    return implode(', ', $nama);
}

/** Simpan ulang kamar milik fasilitas. Kamar yang sedang dihapus tidak disentuh agar kaitannya aman saat dipulihkan. */
function fasilitas_sinkron_kamar(PDO $pdo, int $fasilitasId, array $kamarIds): void
{
    $pdo->prepare(
        'DELETE FROM fasilitas_kamar
         WHERE fasilitas_id = :f AND kamar_id IN (SELECT id FROM kamar WHERE dihapus_pada IS NULL)'
    )->execute(['f' => $fasilitasId]);

    $ins = $pdo->prepare('INSERT INTO fasilitas_kamar (fasilitas_id, kamar_id) VALUES (:f, :k)');
    foreach ($kamarIds as $k) {
        $ins->execute(['f' => $fasilitasId, 'k' => (int)$k]);
    }
}

/** @return array{0: string[], 1: array} */
function fasilitas_validate(PDO $pdo, array $in, int $editId = 0): array
{
    $errors = [];

    $nama      = kamar_str($in['nama'] ?? '');
    $kategori  = kamar_str($in['kategori'] ?? '');
    $status    = kamar_str($in['status'] ?? 'Aktif');
    $ikon      = kamar_str($in['ikon'] ?? 'bi-stars');
    $deskripsi = kamar_str($in['deskripsi'] ?? '');

    // kamar yang dipilih: hanya id kamar yang benar-benar ada & belum dihapus
    $kamarIds = [];
    if (is_array($in['kamar_id'] ?? null)) {
        $valid = array_map('intval', array_column(fasilitas_kamar_pilihan($pdo), 'id'));
        foreach ($in['kamar_id'] as $v) {
            $v = (int)$v;
            if ($v > 0 && in_array($v, $valid, true) && !in_array($v, $kamarIds, true)) {
                $kamarIds[] = $v;
            }
        }
    }

    if ($nama === '' || text_length($nama) > 80) {
        $errors[] = 'Nama fasilitas wajib diisi (maksimal 80 karakter).';
    } else {
        $cek = $pdo->prepare('SELECT id, dihapus_pada FROM fasilitas WHERE LOWER(nama) = LOWER(:n) AND id <> :id');
        $cek->execute(['n' => $nama, 'id' => $editId]);
        $ada = $cek->fetch();
        if ($ada) {
            $errors[] = $ada['dihapus_pada'] !== null
                ? 'Nama "' . $nama . '" dipakai fasilitas yang sudah dihapus. Pulihkan fasilitas itu dari daftar fasilitas terhapus, atau pakai nama lain.'
                : 'Nama "' . $nama . '" sudah dipakai fasilitas lain.';
        }
    }
    if (!in_array($kategori, FASILITAS_KATEGORI, true)) {
        $errors[] = 'Kategori fasilitas tidak valid.';
        $kategori = '';
    }
    if (!in_array($status, FASILITAS_STATUS, true)) {
        $errors[] = 'Status fasilitas tidak valid.';
        $status = 'Aktif';
    }
    if (!array_key_exists($ikon, FASILITAS_IKON)) {
        $errors[] = 'Ikon fasilitas tidak valid.';
        $ikon = 'bi-stars';
    }
    if (text_length($deskripsi) > 200) {
        $errors[] = 'Deskripsi singkat maksimal 200 karakter.';
    }

    return [$errors, [
        'nama'      => $nama,
        'kategori'  => $kategori,
        'status'    => $status,
        'ikon'      => $ikon,
        'deskripsi' => $deskripsi,
        'kamar_ids' => $kamarIds,
    ]];
}

function fasilitas_tambah(PDO $pdo, array $d): int
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('
            INSERT INTO fasilitas (nama, kategori, deskripsi, ikon, aktif, diperbarui_at)
            VALUES (:nama, :kategori, :deskripsi, :ikon, :aktif, CURRENT_TIMESTAMP)
        ');
        $stmt->bindValue('nama', $d['nama']);
        $stmt->bindValue('kategori', $d['kategori']);
        $stmt->bindValue('deskripsi', $d['deskripsi'] !== '' ? $d['deskripsi'] : null);
        $stmt->bindValue('ikon', $d['ikon']);
        $stmt->bindValue('aktif', $d['status'] === 'Aktif', PDO::PARAM_BOOL);
        $stmt->execute();

        $id = (int)$pdo->lastInsertId('fasilitas_id_seq');
        fasilitas_sinkron_kamar($pdo, $id, $d['kamar_ids']);
        $pdo->commit();

        return $id;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        throw $e;
    }
}

function fasilitas_ubah(PDO $pdo, int $id, array $d): void
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('
            UPDATE fasilitas SET
                nama = :nama, kategori = :kategori, deskripsi = :deskripsi,
                ikon = :ikon, aktif = :aktif, diperbarui_at = CURRENT_TIMESTAMP
            WHERE id = :id AND dihapus_pada IS NULL
        ');
        $stmt->bindValue('nama', $d['nama']);
        $stmt->bindValue('kategori', $d['kategori']);
        $stmt->bindValue('deskripsi', $d['deskripsi'] !== '' ? $d['deskripsi'] : null);
        $stmt->bindValue('ikon', $d['ikon']);
        $stmt->bindValue('aktif', $d['status'] === 'Aktif', PDO::PARAM_BOOL);
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException('Fasilitas tidak ditemukan atau sudah dihapus.');
        }
        fasilitas_sinkron_kamar($pdo, $id, $d['kamar_ids']);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        throw $e;
    }
}

function fasilitas_hapus(PDO $pdo, int $id): string
{
    $cari = $pdo->prepare('SELECT nama FROM fasilitas WHERE id = :id AND dihapus_pada IS NULL');
    $cari->execute(['id' => $id]);
    $nama = $cari->fetchColumn();
    if ($nama === false) {
        throw new InvalidArgumentException('Fasilitas tidak ditemukan atau sudah dihapus.');
    }

    $pdo->prepare('UPDATE fasilitas SET dihapus_pada = CURRENT_TIMESTAMP WHERE id = :id')->execute(['id' => $id]);

    return (string)$nama;
}

function fasilitas_pulihkan(PDO $pdo, int $id): string
{
    $cari = $pdo->prepare('SELECT nama FROM fasilitas WHERE id = :id AND dihapus_pada IS NOT NULL');
    $cari->execute(['id' => $id]);
    $nama = $cari->fetchColumn();
    if ($nama === false) {
        throw new InvalidArgumentException('Fasilitas tidak ditemukan di daftar fasilitas terhapus.');
    }

    $pdo->prepare('UPDATE fasilitas SET dihapus_pada = NULL, diperbarui_at = CURRENT_TIMESTAMP WHERE id = :id')
        ->execute(['id' => $id]);

    return (string)$nama;
}

function fasilitas_laporan_data(PDO $pdo): array
{
    $rows = $pdo->query(
        'SELECT * FROM fasilitas WHERE dihapus_pada IS NULL ORDER BY kategori, nama'
    )->fetchAll();

    $kepala = ['No', 'Nama Fasilitas', 'Kategori', 'Kamar', 'Deskripsi Singkat', 'Status', 'Diperbarui'];
    $daftar = [array_map(static function ($t) {
        return [$t, 1];
    }, $kepala)];

    $kamarMap = fasilitas_kamar_map($pdo, array_column($rows, 'id'));
    $no = 0;
    $hitung = ['Aktif' => 0, 'Nonaktif' => 0];
    $perKategori = array_fill_keys(FASILITAS_KATEGORI, 0);
    foreach ($rows as $r) {
        $no++;
        $status = $r['aktif'] ? 'Aktif' : 'Nonaktif';
        $hitung[$status]++;
        if (isset($perKategori[$r['kategori']])) {
            $perKategori[$r['kategori']]++;
        }
        $waktu = !empty($r['diperbarui_at']) ? strtotime((string)$r['diperbarui_at']) : false;
        $daftar[] = [
            $no,
            (string)$r['nama'],
            (string)$r['kategori'],
            implode(', ', array_map(static function ($k) {
                return 'Kamar ' . $k['nomor_kamar'];
            }, $kamarMap[(int)$r['id']] ?? [])),
            [(string)($r['deskripsi'] ?? ''), 4],
            $status,
            $waktu ? date('d/m/Y H:i', $waktu) : '',
        ];
    }

    $ringkasan = [
        [['Laporan Master Fasilitas - ' . villa_name(), 3]],
        [],
        ['Dibuat pada', date('d/m/Y H:i')],
        ['Total fasilitas', $no],
        ['Aktif', $hitung['Aktif']],
        ['Nonaktif', $hitung['Nonaktif']],
    ];
    foreach ($perKategori as $k => $n) {
        $ringkasan[] = [$k, $n];
    }
    $ringkasan[] = ['Fasilitas terhapus (arsip)', fasilitas_arsip_count($pdo)];

    return ['daftar' => $daftar, 'ringkasan' => $ringkasan, 'jumlah' => $no];
}

function fasilitas_kirim_laporan(PDO $pdo, string $format): void
{
    $data    = fasilitas_laporan_data($pdo);
    $stempel = date('Ymd-His');

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if ($format !== 'csv' && class_exists('ZipArchive')) {
        $isi = kamar_xlsx_bangun([
            ['nama' => 'Daftar Fasilitas', 'rows' => $data['daftar'],
             'lebar' => [5, 34, 20, 30, 60, 12, 18], 'bekukan' => true],
            ['nama' => 'Ringkasan', 'rows' => $data['ringkasan'],
             'lebar' => [32, 24], 'bekukan' => false],
        ]);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="laporan-fasilitas-' . $stempel . '.xlsx"');
        header('Content-Length: ' . strlen($isi));
        header('Cache-Control: no-store');
        echo $isi;
        exit;
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="laporan-fasilitas-' . $stempel . '.csv"');
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