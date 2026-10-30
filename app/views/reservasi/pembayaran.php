<section class="container mt-4 mb-5" style="max-width: 850px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="index.php">Katalog</a></li>
            <li class="breadcrumb-item active">Pembayaran Reservasi</li>
        </ol>
    </nav>
    <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
    <?php if ($booking['status_verifikasi'] === 'Ditolak'): ?>
        <div class="alert alert-warning">
            Bukti sebelumnya ditolak. Unggah bukti baru sebelum waktu keep berakhir.
        </div>
    <?php endif; ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-center">
        <div class="card-body p-3">
            <h6 class="text-muted fw-bold small text-uppercase mb-3">Tahapan Reservasi</h6>
            <div class="tracker-container">
                <div class="tracker-step completed"><div class="tracker-circle">1</div><div class="tracker-text">Pemesanan</div></div>
                <div class="tracker-step active"><div class="tracker-circle">2</div><div class="tracker-text">Pembayaran</div></div>
                <div class="tracker-step inactive"><div class="tracker-circle">3</div><div class="tracker-text">Status</div></div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <span class="badge bg-brown text-white mb-2">LANGKAH 2 DARI 3</span>
            <h2 class="fw-bolder mb-4">Pesan &amp; Bayar</h2>
            <div class="bg-light rounded p-3 mb-4">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <small class="text-muted">KODE BOOKING</small>
                        <div class="font-monospace fw-bold"><?= h($booking['kode_booking']) ?></div>
                        <strong>Kamar <?= h($booking['nomor_kamar']) ?> · <?= h($booking['tipe_kamar']) ?></strong>
                        <div class="small text-muted"><?= h($booking['nama_pemesan']) ?> · <?= (int)$booking['jumlah_tamu'] ?> tamu</div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">TOTAL TAGIHAN</small>
                        <h4 class="fw-bolder text-brown">Rp <?= number_format((float)$booking['total_tagihan'], 0, ',', '.') ?></h4>
                        <small class="text-muted">Batas keep: <?= h(date('d M Y H:i', strtotime($booking['batas_waktu_keep']))) ?></small>
                    </div>
                </div>
            </div>

            <div class="timer-box rounded p-3 mb-4" data-deadline="<?= (int)strtotime((string)$booking['batas_waktu_keep']) ?>">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-danger">
                        <small class="fw-bold d-block">SISA WAKTU KEEP</small>
                        <strong class="fs-4" id="keepCountdown">--:--</strong>
                    </div>
                    <div class="text-end small text-muted">Batas pembayaran<br><strong><?= h(date('d M Y, H:i', strtotime($booking['batas_waktu_keep']))) ?> WIB</strong></div>
                </div>
                <div class="progress mt-3" style="height: 6px;"><div id="keepProgress" class="progress-bar bg-warning" style="width: 100%"></div></div>
                <small class="d-block mt-2 text-muted">Jika waktu habis, sesi keep berakhir dan kamar dilepas kembali.</small>
            </div>

            <div class="card bg-white border mb-4">
                <div class="card-body text-center">
                    <h5 class="fw-bold">Pembayaran QRIS</h5>
                    <?php if ($activeQris && is_file(dirname(__DIR__, 3) . '/public' . $activeQris)): ?>
                        <img src="<?= h(ltrim($activeQris, '/')) ?>" alt="QRIS pembayaran Villa" class="img-fluid my-2" style="max-width: 240px;">
                    <?php else: ?>
                        <div class="alert alert-warning mb-2">QRIS belum tersedia. Silakan hubungi pengelola untuk menyelesaikan pembayaran.</div>
                    <?php endif; ?>
                    <p class="mb-2">Bayar tepat sebesar</p>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <strong class="fs-5 text-brown" id="paymentAmount">Rp <?= number_format((float)$booking['total_tagihan'], 0, ',', '.') ?></strong>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-copy="<?= (int)$booking['total_tagihan'] ?>" aria-label="Salin nominal pembayaran">
                            <i class="bi bi-clipboard me-1"></i>Salin nominal
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">Pastikan nama merchant sesuai sebelum konfirmasi pembayaran.</small>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Unggah bukti pembayaran</h5>
            <form action="verifikasi.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <label for="bukti_transfer" class="form-label fw-semibold">Unggah bukti pembayaran</label>
                <input id="bukti_transfer" type="file" name="bukti_transfer" class="form-control mb-2"
                       accept="image/jpeg,image/png,image/webp" required>
                <small class="text-muted d-block mb-4">JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                <button type="submit" class="btn btn-brown w-100 fw-bold py-3">Kirim Bukti Pembayaran</button>
            </form>
            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-4">
                <a class="small text-muted" href="hasil_status.php?kode=<?= rawurlencode($booking['kode_booking']) ?>">Cek status booking</a>
                <?php if ($whatsapp !== ''): ?>
                    <a class="btn btn-sm btn-outline-success" href="https://wa.me/<?= h($whatsapp) ?>?text=<?= rawurlencode('Saya perlu bantuan pembayaran untuk booking ' . $booking['kode_booking']) ?>" target="_blank" rel="noopener">
                        <i class="bi bi-whatsapp me-1"></i>Hubungi pengelola
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const timer = document.querySelector('[data-deadline]');
    const output = document.getElementById('keepCountdown');
    const progress = document.getElementById('keepProgress');
    if (timer && output) {
        const deadline = Number(timer.dataset.deadline) * 1000;
        const startedAt = Date.now();
        const total = Math.max(1, deadline - startedAt);
        const tick = () => {
            const remaining = Math.max(0, deadline - Date.now());
            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            output.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            if (progress) progress.style.width = Math.max(0, remaining / total * 100) + '%';
            if (remaining === 0) window.location.reload();
        };
        tick();
        window.setInterval(tick, 1000);
    }
    document.querySelectorAll('[data-copy]').forEach(button => button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copy);
            button.innerHTML = '<i class="bi bi-check2 me-1"></i>Tersalin';
        } catch (error) {
            window.prompt('Salin nominal pembayaran:', button.dataset.copy);
        }
    }));
});
</script>
