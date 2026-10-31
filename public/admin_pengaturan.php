<?php
require_once __DIR__ . '/../app/admin_settings.php';
require_admin();

$pdo    = db();
$errors = [];
$old    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $aksi = (string)($_POST['aksi'] ?? '');

    try {
        if ($aksi === 'simpan') {
            [$errors, $clean] = validate_settings($_POST);
            if (!$errors) {
                save_settings($pdo, $clean);
                set_flash('success', 'Pengaturan berhasil disimpan.');
                redirect_to('admin_pengaturan.php');
            }
            $old = $clean; 
        } elseif ($aksi === 'upload_qris') {
            upload_qris($pdo, $_FILES['qris'] ?? [], (int)$_SESSION['admin_id']);
            set_flash('success', 'QRIS baru berhasil diunggah dan langsung aktif.');
            redirect_to('admin_pengaturan.php');
        }
    } catch (InvalidArgumentException $e) {
        set_flash('danger', $e->getMessage());
        redirect_to('admin_pengaturan.php');
    } catch (Throwable $e) {
        error_log('[admin_pengaturan] ' . $e->getMessage());
        set_flash('danger', 'Terjadi kesalahan di server. Perubahan tidak tersimpan.');
        redirect_to('admin_pengaturan.php');
    }
}

$settings = get_public_settings($pdo);
if ($old) {
    $settings = array_merge($settings, $old);
}
$qris  = get_active_qris($pdo);
$flash = take_flash();

require_once '../app/views/layouts/admin_header.php';
require_once '../app/views/admin/pengaturan.php';
require_once '../app/views/layouts/admin_footer.php';