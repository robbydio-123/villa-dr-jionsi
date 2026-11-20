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
                $fotoProfil = $_FILES['foto_profil'] ?? [];
                $fotoHero = $_FILES['foto_hero'] ?? [];
                $hasHeroUpload = isset($fotoHero['error']) && array_filter((array)$fotoHero['error'], static fn($error) => (int)$error !== UPLOAD_ERR_NO_FILE) !== [];
                $heroDelete = array_values(array_filter((array)($_POST['hapus_hero'] ?? []), 'is_string'));
                $hasHeroChange = $hasHeroUpload || $heroDelete !== [];
                if (($fotoProfil['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    upload_profile_photo($fotoProfil);
                }
                if ($hasHeroChange) {
                    update_home_hero_photos($fotoHero, $heroDelete);
                }
                save_settings($pdo, $clean);
                $savedMessage = 'Pengaturan berhasil disimpan';
                if (($fotoProfil['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    $savedMessage .= ' beserta foto profil villa';
                }
                if ($hasHeroChange) {
                    $savedMessage .= ' dan foto hero beranda';
                }
                set_flash('success', $savedMessage . '.');
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

require_once __DIR__ . '/../app/views/layouts/admin_header.php';
require_once __DIR__ . '/../app/views/admin/pengaturan.php';
require_once __DIR__ . '/../app/views/layouts/admin_footer.php';