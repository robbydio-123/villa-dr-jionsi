<?php

$per        = $data['per'];
$aksiCepat  = $data['aksi_cepat'];
$kedaluwarsa = $data['kedaluwarsa'];
$jam        = (int)$sekarang->format('G');
$batasKeep  = (int)($settings['batas_waktu_keep_menit'] ?? 15);
?>
<div class="admin-content">

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= h($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Header Dashboard -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end mb-4 gap-3">
        <div>
            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">
                <i class="bi bi-calendar3 me-1"></i>
                <span id="dashTanggal"><?= h(tanggal_id($sekarang)) ?></span> &bull;
                <span id="dashJam"><?= h($sekarang->format('H.i')) ?></span> WIB
            </small>
            <h3 class="fw-bolder mb-1">Selamat <span id="dashSapaan"><?= h(sapaan_waktu($jam)) ?></span>, Pengelola <?= h(villa_name()) ?></h3>
            <p class="text-muted small mb-0">Pantau transparansi alur okupansi, verifikasi transfer seketika, dan pastikan kenyamanan tamu.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="admin_kamar.php" class="btn btn-light border fw-semibold text-brown"><i class="bi bi-arrow-clockwise me-1"></i> Perbarui Kondisi Kamar</a>
            <a href="admin_kamar.php?aksi=tambah" class="btn btn-brown fw-bold"><i class="bi bi-plus me-1"></i> Tambah Kamar</a>
        </div>
    </div>

    <!-- Kartu Ringkasan (6 kartu, klik untuk membuka daftar yang sesuai) -->
    <div class="dash-stats mb-4">
        <a class="stat-link" href="admin_kamar.php?status=Tersedia" aria-label="Lihat kamar tersedia">
            <div class="stat-card dash-stat border-success-subtle border-2">
                <div class="dash-stat-head">
                    <span class="dash-stat-label">KAMAR TERSEDIA</span>
                    <span class="dash-stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-door-open"></i></span>
                </div>
                <div class="stat-value text-success"><?= count($per['Tersedia']) ?></div>
                <div class="dash-stat-foot"><?= chip_unit($per['Tersedia']) ?></div>
            </div>
        </a>

        <a class="stat-link" href="admin_reservasi.php?filter=keep" aria-label="Lihat reservasi Di-Keep">
            <div class="stat-card dash-stat border-warning-subtle border-2">
                <div class="dash-stat-head">
                    <span class="dash-stat-label">DI-KEEP SEMENTARA</span>
                    <span class="dash-stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
                <div class="stat-value text-warning"><?= count($per['Di-Keep']) ?></div>
                <div class="dash-stat-foot">
                    <?php if ($data['sisa_keep'] !== null): ?>
                        <span class="stat-note text-danger fw-bold" data-sisa="<?= (int)$data['sisa_keep'] ?>" data-format="menit">Sisa waktu <?= max(1, (int)ceil($data['sisa_keep'] / 60)) ?> mnt</span>
                    <?php else: ?>
                        <span class="stat-note">Tidak ada</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>

        <a class="stat-link" href="admin_kamar.php?status=Terisi" aria-label="Lihat kamar terisi">
            <div class="stat-card dash-stat border-danger-subtle border-2">
                <div class="dash-stat-head">
                    <span class="dash-stat-label">KAMAR TERISI / DIPESAN</span>
                    <span class="dash-stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-key"></i></span>
                </div>
                <div class="stat-value text-danger"><?= count($per['Terisi']) ?></div>
                <div class="dash-stat-foot"><?= chip_unit($per['Terisi']) ?></div>
            </div>
        </a>

        <a class="stat-link" href="admin_reservasi.php?filter=menunggu" aria-label="Lihat reservasi menunggu konfirmasi">
            <div class="stat-card dash-stat border-primary-subtle border-2">
                <div class="dash-stat-head">
                    <span class="dash-stat-label">MENUNGGU KONFIRMASI</span>
                    <span class="dash-stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-receipt"></i></span>
                </div>
                <div id="dashboardPendingCount" class="stat-value text-primary"><?= count($aksiCepat) ?></div>
                <div class="dash-stat-foot">
                    <span class="stat-note"><?= $aksiCepat ? count($aksiCepat) . ' struk baru' : 'Tidak ada' ?></span>
                </div>
            </div>
        </a>

        <a class="stat-link" href="admin_refund.php?status=Diajukan" aria-label="Lihat refund menunggu tinjauan">
            <div class="stat-card dash-stat border-secondary-subtle border-2">
                <div class="dash-stat-head">
                    <span class="dash-stat-label">REFUND MENUNGGU</span>
                    <span class="dash-stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-arrow-counterclockwise"></i></span>
                </div>
                <div id="dashboardRefundCount" class="stat-value text-dark"><?= (int)$data['refund'] ?></div>
                <div class="dash-stat-foot">
                    <?php if ($data['refund'] > 0): ?>
                        <span class="stat-note text-danger fw-semibold"><?= (int)$data['refund'] ?> tiket perlu ditinjau</span>
                    <?php else: ?>
                        <span class="stat-note">Tidak ada</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>

        <a class="stat-link" href="admin_kamar.php?status=Perbaikan" aria-label="Lihat kamar dalam perbaikan">
            <div class="stat-card dash-stat border-secondary-subtle border-2">
                <div class="dash-stat-head">
                    <span class="dash-stat-label">DALAM PERBAIKAN</span>
                    <span class="dash-stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-tools"></i></span>
                </div>
                <div class="stat-value text-dark"><?= count($per['Perbaikan']) ?></div>
                <div class="dash-stat-foot"><?= chip_unit($per['Perbaikan']) ?></div>
            </div>
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-receipt text-warning me-2"></i> Bukti Pembayaran Menunggu Verifikasi
        </h6>
        <?php if ($aksiCepat): ?>
            <a href="admin_reservasi.php?filter=menunggu" class="btn btn-brown btn-sm fw-bold">Buka di Reservasi <i class="bi bi-arrow-right ms-1"></i></a>
        <?php endif; ?>
    </div>

    <?php if (!$aksiCepat): ?>
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-body p-3 text-muted small"><i class="bi bi-check-circle text-success me-2"></i> Tidak ada bukti pembayaran yang menunggu diperiksa.</div>
        </div>
    <?php endif; ?>

    <?php foreach ($aksiCepat as $a): ?>
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3 p-3">
                <div class="d-flex gap-3 align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded"><i class="bi bi-shield-check fs-5"></i></div>
                    <div>
                        <span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle mb-1" style="font-size: 0.6rem;">MENUNGGU KONFIRMASI</span>
                        <h6 class="fw-bold small mb-1"><?= h(nama_singkat($a['nama_pemesan'])) ?> &bull; <?= h($a['kode_booking']) ?> &bull; Kamar <?= h($a['nomor_kamar']) ?> <?= h($a['tipe_kamar']) ?></h6>
                        <small class="text-muted" style="font-size: 0.75rem;"><?= h(rupiah($a['nominal'])) ?> &bull; <i class="bi bi-clock"></i> <?= h(waktu_lalu((int)$a['menit_lalu'])) ?></small>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-arrow-counterclockwise text-danger me-2"></i> Pengajuan Refund Menunggu Tinjauan
        </h6>
        <?php if ($data['refund'] > 0): ?>
            <a href="admin_refund.php?status=Diajukan" class="btn btn-brown btn-sm fw-bold">Buka Pengajuan Refund <i class="bi bi-arrow-right ms-1"></i></a>
        <?php endif; ?>
    </div>

    <?php if (!$data['refund_list']): ?>
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-body p-3 text-muted small"><i class="bi bi-check-circle text-success me-2"></i> Tidak ada pengajuan refund yang menunggu keputusan.</div>
        </div>
    <?php endif; ?>

    <?php foreach ($data['refund_list'] as $rf): ?>
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3 p-3">
                <div class="d-flex gap-3 align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded"><i class="bi bi-arrow-counterclockwise fs-5"></i></div>
                    <div>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle mb-1" style="font-size: 0.6rem;">REFUND MENUNGGU</span>
                        <h6 class="fw-bold small mb-1"><?= h(nama_singkat($rf['nama_pemesan'])) ?> &bull; <?= h($rf['kode_booking']) ?> &bull; Kamar <?= h($rf['nomor_kamar']) ?> <?= h($rf['tipe_kamar']) ?></h6>
                        <small class="text-muted" style="font-size: 0.75rem;"><?= h(rupiah($rf['total_tagihan'])) ?> &bull; <i class="bi bi-clock"></i> <?= h(waktu_lalu((int)$rf['menit_lalu'])) ?></small>
                    </div>
                </div>
                <a href="admin_refund_tinjau.php?id=<?= (int)$rf['id'] ?>" class="btn btn-sm btn-brown fw-bold"><i class="bi bi-clipboard-check me-1"></i> Tinjau &amp; Putuskan</a>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($kedaluwarsa): ?>
        <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-stopwatch text-danger me-2"></i> Keep Sedang Berjalan</h6>
        <div class="row g-3 mb-4">
            <?php foreach ($kedaluwarsa as $k): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="expire-card">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <h6 class="fw-bold mb-0">Segera Kedaluwarsa (Expiring Keep)</h6>
                            <span class="expire-limit">Batas Sesi <?= $batasKeep ?> Mnt</span>
                        </div>
                        <div class="expire-inner">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <div class="min-w-0">
                                    <div class="fw-bold small text-break"><?= h($k['kode_booking']) ?></div>
                                    <div class="text-muted small">Kamar <?= h($k['nomor_kamar']) ?> &bull; Tamu: <?= h(nama_singkat($k['nama_pemesan'])) ?></div>
                                </div>
                                <span class="timer-pill">
                                    <i class="bi bi-stopwatch"></i>
                                    <span data-sisa="<?= (int)$k['sisa_detik'] ?>" data-format="jam"><?= sprintf('%02d:%02d', intdiv(max(0, (int)$k['sisa_detik']), 60), max(0, (int)$k['sisa_detik']) % 60) ?></span> menit tersisa
                                </span>
                            </div>
                            <div class="expire-img">
                                <span>Unit <?= h($k['nomor_kamar']) ?> <?= h($k['tipe_kamar']) ?></span>
                                <strong><?= h(rupiah($k['total_tagihan'])) ?></strong>
                            </div>
                            <div class="expire-note">
                                <i class="bi bi-info-circle"></i>
                                <span>Akan otomatis dibatalkan sistem jika tidak ada bukti transfer terunggah saat timer habis. Kamar langsung dialihkan ke status "Tersedia".</span>
                            </div>
                            <a class="btn btn-light border btn-sm w-100 fw-bold" href="admin_reservasi.php?filter=keep">Lihat di Reservasi</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/admin_dashboard..js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/admin_dashboard..js') ?>"></script>