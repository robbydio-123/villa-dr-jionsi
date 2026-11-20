<?php
require_once __DIR__ . '/../app/admin_fasilitas.php';
require_admin();

$pdo = db();
fasilitas_pastikan_tabel_kamar($pdo);
$kamarPilihan = fasilitas_kamar_pilihan($pdo);

if (isset($_GET['unduh'])) {
    fasilitas_kirim_laporan($pdo, (string)$_GET['unduh']);
}

$formErrors = [];
$formOld    = null;
$formMode   = 'tambah';
$formId     = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = (string)($_POST['aksi'] ?? '');

    try {
        if ($aksi === 'tambah' || $aksi === 'ubah') {
            $formMode = $aksi;
            $formId   = $aksi === 'ubah' ? (int)($_POST['id'] ?? 0) : 0;
            [$formErrors, $clean] = fasilitas_validate($pdo, $_POST, $formId);

            if (!$formErrors) {
                if ($aksi === 'tambah') {
                    fasilitas_tambah($pdo, $clean);
                } else {
                    fasilitas_ubah($pdo, $formId, $clean);
                }
                $kamarTeks = fasilitas_kamar_label($kamarPilihan, $clean['kamar_ids']);
                $untuk     = $kamarTeks !== '' ? ' untuk ' . $kamarTeks : '';
                set_flash('success', $aksi === 'tambah'
                    ? 'Fasilitas ' . $clean['nama'] . ' berhasil ditambahkan' . $untuk . '.'
                    : 'Perubahan fasilitas ' . $clean['nama'] . ' berhasil disimpan' . $untuk . '.');
                redirect_to('admin_fasilitas.php');
            }
            $formOld = $clean;
        } elseif ($aksi === 'hapus') {
            $nama = fasilitas_hapus($pdo, (int)($_POST['id'] ?? 0));
            set_flash('success', 'Fasilitas ' . $nama . ' dihapus dari tampilan. Datanya tetap tersimpan dan bisa dipulihkan dari daftar fasilitas terhapus.');
            redirect_to('admin_fasilitas.php');
        } elseif ($aksi === 'pulihkan') {
            $nama = fasilitas_pulihkan($pdo, (int)($_POST['id'] ?? 0));
            set_flash('success', 'Fasilitas ' . $nama . ' berhasil dipulihkan dan tampil kembali.');
            redirect_to('admin_fasilitas.php');
        }
    } catch (InvalidArgumentException $e) {
        set_flash('danger', $e->getMessage());
        redirect_to('admin_fasilitas.php');
    } catch (Throwable $e) {
        error_log('[admin_fasilitas] ' . $e->getMessage());
        set_flash('danger', 'Terjadi kesalahan di server. Perubahan tidak tersimpan. Pastikan database/migrasi_fasilitas.sql sudah dijalankan.');
        redirect_to('admin_fasilitas.php');
    }
}

$filters = fasilitas_filter($_GET);
[$facilities, $total, $page, $pages] = fasilitas_list($pdo, $filters);
$filters['page'] = $page;
$kamarMap   = fasilitas_kamar_map($pdo, array_column($facilities, 'id'));
$stats      = fasilitas_stats($pdo);
$arsipCount = fasilitas_arsip_count($pdo);
$flash      = take_flash();

require_once __DIR__ . '/../app/views/layouts/admin_header.php';
require_once __DIR__ . '/../app/views/admin/fasilitas.php';
require_once __DIR__ . '/../app/views/layouts/admin_footer.php';