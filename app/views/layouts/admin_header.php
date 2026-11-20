<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pengelola - <?= h(villa_name()) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>">
</head>

<body class="admin-body">

    <div class="admin-wrapper">

        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-logo">
                    <img src="assets/img/logo%20dr-jionsi.jpeg" alt="Logo <?= h(villa_name()) ?>">
                </div>
                <div class="sidebar-brand-text">
                    <h6><?= h(villa_brand()) ?></h6>
                    <small style="color: #A09C98; font-size: 0.65rem;">Portal Pengelola</small>
                </div>
            </div>

            <div class="sidebar-menu">
                <a href="admin.php" class="sidebar-link <?= ($current_page == 'admin.php') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>

                <a href="admin_reservasi.php" class="sidebar-link <?= ($current_page == 'admin_reservasi.php') ? 'active' : '' ?>">
                    <i class="bi bi-calendar2-check"></i> Reservasi
                    <span id="adminPendingBadge" class="badge bg-danger rounded-pill ms-auto d-none" aria-live="polite"></span>
                </a>

                <a href="admin_refund.php" class="sidebar-link <?= ($current_page == 'admin_refund.php') ? 'active' : '' ?>">
                    <i class="bi bi-arrow-counterclockwise"></i> Pengajuan Refund
                    <span id="adminRefundBadge" class="badge bg-danger rounded-pill ms-auto d-none" aria-live="polite"></span>
                </a>

                <a href="admin_kamar.php" class="sidebar-link <?= ($current_page == 'admin_kamar.php') ? 'active' : '' ?>">
                    <i class="bi bi-door-open"></i> Kelola Kamar
                </a>

                <a href="admin_fasilitas.php" class="sidebar-link <?= ($current_page == 'admin_fasilitas.php') ? 'active' : '' ?>">
                    <i class="bi bi-stars"></i> Master Fasilitas
                </a>

                <a href="admin_pengaturan.php" class="sidebar-link <?= ($current_page == 'admin_pengaturan.php') ? 'active' : '' ?>">
                    <i class="bi bi-gear"></i> Pengaturan
                </a>
            </div>

            <div class="sidebar-footer">
                <a href="logout.php" class="text-decoration-none text-light d-flex align-items-center gap-2 mb-3"><i class="bi bi-box-arrow-left"></i> Logout</a>
                <div>&copy; 2026 <?= h(villa_brand()) ?> Batu</div>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <!-- Tombol Hamburger -->
                <button class="btn btn-light border-0 me-2 btn-sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="input-group" style="max-width: 400px;">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-0" placeholder="Cari tamu, kode booking, villa...">
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" id="adminEnableNotifications" class="btn btn-light border d-none"
                            title="Aktifkan notifikasi desktop" aria-label="Aktifkan notifikasi desktop">
                        <i class="bi bi-bell"></i>
                    </button>
                    <div class="text-end d-none d-md-block">
                        <h6 class="mb-0 fw-bold small">Pengelola Vila</h6>
                    </div>
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 40px; height: 40px; background-color: #A0522D;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/></svg>
                    </div>
                </div>
            </header>