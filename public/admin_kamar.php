<?php
require_once __DIR__ . '/../app/admin_kamar.php';
require_admin();

$pdo = db();
expire_keeps($pdo);

// Unduh laporan: admin_kamar.php?unduh=xlsx  atau  ?unduh=csv
if (isset($_GET['unduh'])) {
    kamar_kirim_laporan($pdo, (string)$_GET['unduh']);
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
            [$formErrors, $clean] = kamar_validate($pdo, $_POST, $formId);

            if (!$formErrors) {
                $fotoBaru = [];
                try {
                    $fotoBaru = kamar_simpan_foto_banyak($_FILES['foto'] ?? [], (array)($_POST['label_baru'] ?? []));

                    $pdo->beginTransaction();
                    if ($aksi === 'tambah') {
                        kamar_tambah($pdo, $clean, null);
                        $kamarId = (int)$pdo->lastInsertId('kamar_id_seq');
                    } else {
                        kamar_ubah($pdo, $formId, $clean, null, false);
                        $kamarId = $formId;
                    }
                    $fotoLepas = kamar_foto_sinkron($pdo, $kamarId, $fotoBaru, $_POST);
                    if (!empty($clean['offline'])) {
                        kamar_buat_reservasi_offline($pdo, $kamarId, $clean['offline']);
                    }
                    $pdo->commit();

                    foreach ($fotoLepas as $u) {
                        kamar_hapus_file($u);
                    }
                    $pesan = $aksi === 'tambah'
                        ? 'Kamar ' . $clean['nomor_kamar'] . ' berhasil ditambahkan.'
                        : 'Perubahan Kamar ' . $clean['nomor_kamar'] . ' berhasil disimpan.';
                    if (!empty($clean['offline'])) {
                        $pesan .= ' Tamu offline dicatat: ' . $clean['offline']['checkin']->format('d M H.i')
                            . ' - ' . $clean['offline']['checkout']->format('d M H.i') . '.';
                    }
                    set_flash('success', $pesan);
                    redirect_to('admin_kamar.php');
                } catch (InvalidArgumentException $e) {
                    if ($pdo->inTransaction()) { $pdo->rollBack(); }
                    foreach ($fotoBaru as $f) { kamar_hapus_file($f['url']); }
                    $formErrors[] = $e->getMessage();
                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) { $pdo->rollBack(); }
                    foreach ($fotoBaru as $f) { kamar_hapus_file($f['url']); }
                    throw $e;
                }
            }
            $formOld = $clean;
            $formOld['status'] = $clean['status_form'];
        } elseif ($aksi === 'hapus') {
            $nomor = kamar_hapus($pdo, (int)($_POST['id'] ?? 0));
            set_flash('success', 'Kamar ' . $nomor . ' dihapus dari tampilan. Datanya tetap tersimpan dan bisa dipulihkan dari daftar kamar terhapus.');
            redirect_to('admin_kamar.php');
        } elseif ($aksi === 'pulihkan') {
            $nomor = kamar_pulihkan($pdo, (int)($_POST['id'] ?? 0));
            set_flash('success', 'Kamar ' . $nomor . ' berhasil dipulihkan dan tampil kembali.');
            redirect_to('admin_kamar.php');
        }
    } catch (InvalidArgumentException $e) {
        set_flash('danger', $e->getMessage());
        redirect_to('admin_kamar.php');
    } catch (Throwable $e) {
        error_log('[admin_kamar] ' . $e->getMessage());
        set_flash('danger', 'Terjadi kesalahan di server. Perubahan tidak tersimpan.');
        redirect_to('admin_kamar.php');
    }
}

$filters = kamar_filter($_GET);
[$rooms, $total, $page, $pages] = kamar_list($pdo, $filters);
$filters['page'] = $page;
$stats      = kamar_stats($pdo);
$tipeList   = kamar_tipe_list($pdo);
$arsipCount = kamar_arsip_count($pdo);
$flash      = take_flash();

require_once __DIR__ . '/../app/views/layouts/admin_header.php';
require_once __DIR__ . '/../app/views/admin/kamar.php';
require_once __DIR__ . '/../app/views/layouts/admin_footer.php';