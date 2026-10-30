<section class="container mt-4 mb-5" style="max-width: 850px;">
    <?php if ($error): ?><div class="alert alert-warning" role="alert"><?= h($error) ?></div><?php endif; ?>
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
            <li class="breadcrumb-item"><a href="status.php">Cek Status</a></li>
            <li class="breadcrumb-item active">Detail Reservasi</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
        <div>
            <span class="badge bg-light text-dark border mb-2"><?= h($booking['status_booking']) ?></span>
            <h2 class="fw-bolder">Status Reservasi</h2>
        </div>
        <div class="text-md-end">
            <small class="text-muted d-block">KODE BOOKING</small>
            <strong class="font-monospace fs-5"><?= h($booking['kode_booking']) ?></strong>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-copy="<?= h($booking['kode_booking']) ?>">
                <i class="bi bi-clipboard me-1"></i>Salin kode
            </button>
        </div>
    </div>

    <?php if ($booking['status_verifikasi'] === 'Ditolak' && $booking['status_booking'] === 'Menunggu Konfirmasi'): ?>
        <div class="alert alert-danger">
            Bukti pembayaran ditolak. Anda dapat mengunggah ulang sebelum batas keep:
            <?= h(date('d M Y H:i', strtotime($booking['batas_waktu_keep']))) ?>.
            <a href="keep.php" class="alert-link">Unggah bukti baru</a>.
        </div>
    <?php elseif ($booking['status_booking'] === 'Dibayar'): ?>
        <div class="alert alert-success">Pembayaran terverifikasi. Reservasi Anda telah dikonfirmasi.</div>
    <?php elseif ($booking['status_booking'] === 'Menunggu Konfirmasi'): ?>
        <div class="alert alert-warning">Bukti pembayaran diterima dan sedang menunggu verifikasi pengelola.</div>
    <?php elseif ($booking['status_booking'] === 'Di-Keep'): ?>
        <div class="alert alert-info">
            Kamar ditahan sampai <?= h(date('d M Y H:i', strtotime($booking['batas_waktu_keep']))) ?>.
            <a href="keep.php" class="alert-link">Lanjutkan pembayaran</a>.
        </div>
    <?php elseif ($booking['status_booking'] === 'Refund Diajukan'): ?>
        <div class="alert alert-secondary">Pengajuan refund Anda sedang diproses.</div>
    <?php elseif ($booking['status_booking'] === 'Kedaluwarsa'): ?>
        <div class="alert alert-danger">Waktu keep berakhir. Kamar tidak lagi ditahan.</div>
    <?php elseif ($booking['status_booking'] === 'Dibatalkan'): ?>
        <div class="alert alert-secondary">Reservasi ini telah dibatalkan.</div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Rincian Menginap</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Pemesan</small>
                    <strong><?= h($booking['nama_pemesan']) ?></strong>
                    <div class="small text-muted">WhatsApp berakhir <?= h($booking['whatsapp_4_digit']) ?></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Kamar</small>
                    <strong>Kamar <?= h($booking['nomor_kamar']) ?> · <?= h($booking['tipe_kamar']) ?></strong>
                    <div class="small text-muted"><?= (int)$booking['jumlah_tamu'] ?> dari maks. <?= (int)$booking['kapasitas_kamar'] ?> tamu</div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Check-in</small>
                    <strong><?= h(date('d M Y H:i', strtotime($booking['waktu_checkin']))) ?></strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Check-out</small>
                    <strong><?= h(date('d M Y H:i', strtotime($booking['waktu_checkout']))) ?></strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Durasi</small>
                    <strong><?= (int)$booking['durasi_jam'] ?> jam</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Total tagihan</small>
                    <strong class="text-brown">Rp <?= number_format((float)$booking['total_tagihan'], 0, ',', '.') ?></strong>
                </div>
            </div>
            <?php if ($booking['catatan']): ?>
                <hr><small class="text-muted">Catatan</small><p class="mb-0"><?= nl2br(h($booking['catatan'])) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-brown"></i>Riwayat Reservasi</h5>
            <?php if ($statusHistory): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($statusHistory as $history): ?>
                        <div class="list-group-item px-0 d-flex justify-content-between gap-3">
                            <div>
                                <strong><?= h($history['status_baru']) ?></strong>
                                <?php if ($history['status_lama']): ?><small class="text-muted"> · dari <?= h($history['status_lama']) ?></small><?php endif; ?>
                            </div>
                            <small class="text-muted text-nowrap"><?= h(date('d M Y H:i', strtotime($history['created_at']))) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0">Belum ada riwayat status.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($booking['pembayaran_id']): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold">Pembayaran</h5>
                <p class="mb-1">Status bukti: <strong><?= h($booking['status_verifikasi']) ?></strong></p>
                <p class="small text-muted mb-0">Dikirim <?= h(date('d M Y H:i', strtotime($booking['pembayaran_created_at']))) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($booking['refund_id']): ?>
        <div class="alert alert-secondary">
            Refund: <strong><?= h($booking['refund_status']) ?></strong>
            <?php if ($booking['refund_alasan']): ?><div class="small mt-1"><?= nl2br(h($booking['refund_alasan'])) ?></div><?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-column flex-md-row gap-2">
        <?php if ($booking['status_booking'] === 'Di-Keep'): ?>
            <a href="keep.php" class="btn btn-brown flex-fill">Lanjutkan pembayaran</a>
        <?php endif; ?>
        <?php if ($booking['status_verifikasi'] === 'Ditolak' && $booking['status_booking'] === 'Menunggu Konfirmasi'): ?>
            <a href="keep.php" class="btn btn-brown flex-fill">Unggah ulang bukti</a>
        <?php endif; ?>
        <?php if ($canRefund): ?>
            <a href="refund.php?kode=<?= rawurlencode($booking['kode_booking']) ?>" class="btn btn-outline-danger flex-fill">Ajukan refund</a>
        <?php endif; ?>
        <a href="status.php" class="btn btn-outline-secondary flex-fill">Cek reservasi lain</a>
        <button type="button" class="btn btn-outline-secondary flex-fill" onclick="window.print()"><i class="bi bi-printer me-1"></i>Cetak ringkasan</button>
        <?php if ($whatsapp !== ''): ?>
            <a class="btn btn-outline-success flex-fill" href="https://wa.me/<?= h($whatsapp) ?>?text=<?= rawurlencode('Halo, saya ingin bertanya tentang reservasi ' . $booking['kode_booking']) ?>" target="_blank" rel="noopener">
                <i class="bi bi-whatsapp me-1"></i>Hubungi pengelola
            </a>
        <?php endif; ?>
    </div>
    <?php if ($canRefund): ?>
        <small class="text-muted d-block mt-2">Batas pengajuan refund: <?= h($refundDeadline->format('d M Y H:i')) ?></small>
    <?php endif; ?>
</section>
<script>
document.querySelectorAll('[data-copy]').forEach(button => button.addEventListener('click', async () => {
    try {
        await navigator.clipboard.writeText(button.dataset.copy);
        button.innerHTML = '<i class="bi bi-check2 me-1"></i>Tersalin';
    } catch (error) {
        window.prompt('Salin kode booking:', button.dataset.copy);
    }
}));
</script>
