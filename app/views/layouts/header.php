<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Villa D-R Jionsi - Katalog Kamar' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <img src="assets/img/logo%20dr-jionsi.jpeg" alt="Logo Villa D-R Jionsi" class="brand-logo">
                <div class="d-flex flex-column">
                    <span class="fw-bolder fs-5 text-dark" style="line-height: 1;">D-R Jionsi</span>
                    <span class="text-muted" style="font-size: 0.65rem; font-weight: 500;">Katalog &amp; Reservasi Villa Kota Batu</span>
                </div>
            </a>

            <button class="navbar-toggler shadow-none border-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-lg-3">
                    <li class="nav-item"><a class="nav-link fw-semibold text-muted hover-brown" href="index.php">Katalog Kamar</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-muted hover-brown" href="status.php">Cek Status Reservasi</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-muted hover-brown" href="profil.php">Profil Villa &amp; Fasilitas</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="login.php" class="btn btn-portal bg-cream-dark border-0 rounded-pill px-3 py-2 small fw-semibold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-headset"></i> Portal Pengelola
                    </a>
                </div>
            </div>
        </div>
    </nav>
