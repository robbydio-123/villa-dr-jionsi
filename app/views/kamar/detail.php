<section class="container mt-4 mb-5" style="max-width: 1000px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i>Katalog</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kamar <?= h($room['nomor_kamar']) ?></li>
        </ol>
    </nav>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <?php if (!empty($room['foto_url']) && is_file(dirname(__DIR__, 3) . '/public' . $room['foto_url'])): ?>
                <img src="<?= h(ltrim($room['foto_url'], '/')) ?>" alt="Kamar <?= h($room['nomor_kamar']) ?>"
                     class="gallery-main rounded-4 shadow-sm w-100" style="height: 360px; object-fit: cover;">
            <?php else: ?>
                <div class="rounded-4 d-flex flex-column align-items-center justify-content-center bg-light text-brown"
                     style="height: 360px;"><i class="bi bi-door-open fs-1"></i><strong>Kamar <?= h($room['nomor_kamar']) ?></strong></div>
            <?php endif; ?>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="badge bg-light text-dark border"><i class="bi bi-layers me-1"></i>Lantai <?= (int)$room['lantai'] ?></span>
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>Maks. <?= (int)$room['kapasitas'] ?> tamu</span>
                <?php foreach (getFasilitasKamar($room['deskripsi'] ?? '') as $facility): ?>
                    <span class="badge bg-white text-muted border"><i class="bi <?= h($facility['icon']) ?> me-1"></i><?= h($facility['label']) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="drj-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted fw-bold">KAMAR <?= h($room['nomor_kamar']) ?></small>
                        <h2 class="fw-bolder mb-2"><?= h($room['tipe_kamar']) ?></h2>
                    </div>
                    <span class="badge <?= $available ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $available ? 'Tersedia' : h($room['status'] === 'Perbaikan' ? 'Perbaikan' : 'Tidak tersedia') ?>
                    </span>
                </div>
                <p class="text-muted" style="white-space: pre-line;"><?= h($room['deskripsi'] ?: 'Informasi kamar belum tersedia.') ?></p>
                <div class="drj-price text-brown fw-bolder fs-3">
                    Rp <?= number_format((float)$room['harga_per_malam'], 0, ',', '.') ?> <small class="fs-6 text-muted">/ malam</small>
                </div>
                <?php if (!empty($packagePrices)): ?>
                    <ul class="list-unstyled small text-muted mb-0 mt-2">
                        <?php foreach ($packagePrices as $hours => $price): ?>
                            <li><?= (int)$hours ?> jam: <strong>Rp <?= number_format((float)$price, 0, ',', '.') ?></strong></li>
                        <?php endforeach; ?>
                        <li>24 jam / 1 malam: <strong>Rp <?= number_format((float)$room['harga_per_malam'], 0, ',', '.') ?></strong></li>
                    </ul>
                <?php endif; ?>

                <form action="detail.php" method="GET" class="border-top mt-3 pt-3">
                    <input type="hidden" name="id" value="<?= (int)$room['id'] ?>">
                    <label class="form-label small fw-bold" for="detailCheckin">PILIH JADWAL MENGINAP</label>
                    <input type="datetime-local" id="detailCheckin" name="checkin" class="form-control mb-2"
                           min="<?= h(date('Y-m-d\TH:i')) ?>" value="<?= h($checkin ? $checkin->format('Y-m-d\TH:i') : '') ?>">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="flex-fill">
                    <?php $pickerId = 'detailDuration'; $pickerName = 'durasi'; $pickerSmall = false; $pickerPlaceholder = false; require __DIR__ . '/../partials/durasi_picker.php'; ?>
                        </div>
                        <button class="btn btn-outline-secondary" type="submit">Cek</button>
                    </div>
                    <?php if ($checkin && $checkout): ?>
                        <div class="alert <?= $available ? 'alert-success' : 'alert-warning' ?> py-2 mt-3 mb-0 small">
                            <?= $available ? 'Jadwal tersedia.' : 'Kamar tidak tersedia pada jadwal tersebut.' ?>
                            Check-out <?= h($checkout->format('d M Y H:i')) ?>.
                        </div>
                    <?php endif; ?>
                </form>

                <div class="d-grid gap-2 mt-3">
                    <?php if ($available): ?>
                        <a class="btn btn-brown fw-bold py-3"
                           href="formulir.php?<?= h(http_build_query([
                               'kamar_id' => $room['id'],
                               'checkin' => $checkin ? $checkin->format('Y-m-d\TH:i') : '',
                               'durasi' => $duration,
                           ])) ?>">
                            <i class="bi bi-lock-fill me-2"></i>Ajukan Reservasi
                            <span class="d-block fw-normal small">Kamar ditahan <?= (int)$settings['batas_waktu_keep_menit'] ?> menit setelah booking</span>
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary" disabled>Kamar tidak tersedia</button>
                    <?php endif; ?>
                </div>
                <small class="text-muted d-block mt-3"><i class="bi bi-shield-check me-1"></i>Ketersediaan dan tagihan akan divalidasi ulang oleh server saat pemesanan.</small>
            </div>
        </div>
    </div>
</section>