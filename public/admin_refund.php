<?php
require_once __DIR__ . '/../app/admin_refund.php';
require_admin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = (string)($_POST['aksi'] ?? '');

    try {
        if ($aksi === 'hapus') {
            $kode = refund_hapus($pdo, (int)($_POST['id'] ?? 0));
            set_flash('success', 'Pengajuan ' . $kode . ' dihapus dari tampilan. Datanya tetap tersimpan dan bisa dipulihkan dari daftar pengajuan terhapus.');
        } elseif ($aksi === 'pulihkan') {
            $kode = refund_pulihkan($pdo, (int)($_POST['id'] ?? 0));
            set_flash('success', 'Pengajuan ' . $kode . ' berhasil dipulihkan dan tampil kembali.');
        }
    } catch (InvalidArgumentException $e) {
        set_flash('danger', $e->getMessage());
    } catch (Throwable $e) {
        error_log('[admin_refund] ' . $e->getMessage());
        set_flash('danger', 'Terjadi kesalahan di server. Perubahan tidak tersimpan.');
    }
    redirect_to('admin_refund.php');
}

$filters = refund_filter($_GET);
$refunds = refund_admin_list($pdo, $filters);
$stats   = refund_stats($pdo);
$flash   = take_flash();

require_once __DIR__ . '/../app/views/layouts/admin_header.php';
require_once __DIR__ . '/../app/views/admin/refund.php';
require_once __DIR__ . '/../app/views/layouts/admin_footer.php';