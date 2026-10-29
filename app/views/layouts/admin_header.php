<?php
// Mendapatkan nama file yang sedang diakses (misal: admin.php atau admin_reservasi.php)
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pengelola - Villa D-R Jionsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="admin-body">

    <div class="admin-wrapper">

        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <!-- SIDEBAR KIRI -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <div>
                    <img src="assets/img/logo%20dr-jionsi.jpeg" alt="Logo Villa D-R Jionsi" class="sidebar-brand-logo">
                    <small style="color: #A09C98; font-size: 0.65rem;">Villa D-R Jionsi</small>
                </div>
            </div>

            <div class="sidebar-menu">
                <a href="admin.php" class="sidebar-link <?= ($current_page == 'admin.php') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>

                <a href="admin_reservasi.php" class="sidebar-link <?= ($current_page == 'admin_reservasi.php') ? 'active' : '' ?>">
                    <i class="bi bi-calendar2-check"></i> Reservasi
                </a>

                <a href="admin_refund.php" class="sidebar-link <?= ($current_page == 'admin_refund.php') ? 'active' : '' ?>">
                    <i class="bi bi-arrow-counterclockwise"></i> Pengajuan Refund
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
                <a href="index.php" class="text-decoration-none text-light d-flex align-items-center gap-2 mb-3"><i class="bi bi-box-arrow-left"></i> Logout</a>
                <div>&copy; 2026 D-R Jionsi Batu</div>
            </div>
        </aside>

        <!-- AREA KANAN UTAMA -->
        <main class="admin-main">
            <!-- TOPBAR -->
            <header class="admin-topbar">
                <!-- Tombol Hamburger (Hanya muncul di HP) -->
                <button class="btn btn-light border-0 me-2 btn-sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="input-group" style="max-width: 400px;">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-0" placeholder="Cari tamu, kode booking, villa...">
                </div>

                <div class="d-flex align-items-center gap-4">
                    <span class="badge bg-light text-dark border rounded-pill py-2 px-3 fw-normal">
                        <i class="bi bi-circle-fill text-success me-2" style="font-size: 0.4rem;"></i> Kota Batu &bull; Operasional Normal
                    </span>

                    <div class="position-relative">
                        <i class="bi bi-bell fs-5 text-muted"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end d-none d-md-block">
                            <h6 class="mb-0 fw-bold small">Bpk. Hendra</h6>
                            <small class="text-muted" style="font-size: 0.7rem;">Pengelola Villa</small>
                        </div>
                        <div class="rounded-circle bg-brown text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                </div>
            </header>