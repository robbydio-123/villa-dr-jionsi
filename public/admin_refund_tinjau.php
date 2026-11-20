<?php
require_once __DIR__ . '/../app/admin_refund.php';
require_admin();
// public/admin_refund_tinjau.php?id=...
$pdo = db();
$id  = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$refund = $id > 0 ? refund_admin_detail($pdo, $id) : null;
if (!$refund) {
    set_flash('danger', 'Pengajuan refund tidak ditemukan.');
    redirect_to('admin_refund.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $aksi = (string)($_POST['aksi'] ?? '');
        if ($aksi === 'selesai') {
            $kode = refund_tandai_selesai($pdo, $id, (int)$_SESSION['admin_id']);
            set_flash('success', 'Refund ' . $kode . ' ditandai sudah ditransfer.');
            redirect_to('admin_refund.php');
        }
        refund_putuskan($pdo, $id, $aksi, (string)($_POST['catatan'] ?? ''), (int)$_SESSION['admin_id']);
        if ($aksi === 'setuju') {
            set_flash('success', 'Refund disetujui. Transfer dana ke rekening tamu, lalu kirim bukti lewat WhatsApp.');
            redirect_to('admin_refund_tinjau.php?id=' . $id);
        }
        set_flash('success', 'Pengajuan refund ditolak.');
        redirect_to('admin_refund.php');
    } catch (InvalidArgumentException | DomainException $e) {
        set_flash('danger', $e->getMessage());
        redirect_to('admin_refund_tinjau.php?id=' . $id);
    } catch (Throwable $e) {
        error_log('[admin_refund_tinjau] ' . $e->getMessage());
        set_flash('danger', 'Terjadi kesalahan di server. Keputusan tidak tersimpan.');
        redirect_to('admin_refund_tinjau.php?id=' . $id);
    }
}

$flash = take_flash();
require_once __DIR__ . '/../app/views/layouts/admin_header.php';
require_once __DIR__ . '/../app/views/admin/refund_tinjau.php';
require_once __DIR__ . '/../app/views/layouts/admin_footer.php';