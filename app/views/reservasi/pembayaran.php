<?php

$projectPublic = dirname(__DIR__, 3) . '/public';
$keepMenit  = (int)($settings['batas_waktu_keep_menit'] ?? 15);
$deadlineTs = (int)strtotime((string)$booking['batas_waktu_keep']);
$total      = (float)$booking['total_tagihan'];
$rp         = fn($n) => 'Rp ' . number_format((float)$n, 0, ',', '.');
$hasQris    = $activeQris && is_file($projectPublic . $activeQris);
$qrisUrl    = $hasQris ? ltrim($activeQris, '/') : '';

// Durasi menginap
$durasiLabel = '';
if (!empty($booking['waktu_checkin']) && !empty($booking['waktu_checkout'])) {
    $jam = max(1, (int)round((strtotime($booking['waktu_checkout']) - strtotime($booking['waktu_checkin'])) / 3600));
    $durasiLabel = $jam >= 24 ? intdiv($jam, 24) . ' malam' : $jam . ' jam';
}
$fasilitas = getFasilitasKamar($booking['deskripsi'] ?? '');
$fotoOk    = !empty($booking['foto_url']) && is_file($projectPublic . $booking['foto_url']);
$dibuat    = !empty($booking['created_at']) ? date('d M Y, H.i', strtotime($booking['created_at'])) : '';

?>

<section class="container mt-4 mb-5" style="max-width: 880px;">
    <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
    <?php if ($booking['status_verifikasi'] === 'Ditolak'): ?>
        <div class="alert alert-warning">Bukti sebelumnya ditolak. Unggah bukti baru sebelum waktu keep berakhir.</div>
    <?php endif; ?>

    <!-- Tahapan -->
    <div class="pay-track mb-3">
        <div class="small-title">TAHAPAN RESERVASI</div>
        <div class="pay-steps">
            <div class="pay-step completed"><div class="pay-dot">1</div><div class="t">Dipesan</div><div class="s"><?= h($dibuat) ?></div></div>
            <div class="pay-step active"><div class="pay-dot">2</div><div class="t">Pesan &amp; Bayar</div><div class="s">-</div></div>
            <div class="pay-step"><div class="pay-dot">3</div><div class="t">Dibayar</div><div class="s">-</div></div>
        </div>
    </div>

    <div class="pay-card p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="badge bg-brown text-white">LANGKAH 2 DARI 3</span>
            <span class="pay-chip"><i class="bi bi-stopwatch me-1"></i>Keep <?= $keepMenit ?> Menit</span>
        </div>
        <h1 class="h3 fw-bolder mb-3">Pesan &amp; Bayar</h1>

        <!-- Ringkasan kamar -->
        <div class="pay-room mb-3">
            <?php if ($fotoOk): ?>
                <img src="<?= h(ltrim($booking['foto_url'], '/')) ?>" alt="Kamar <?= h($booking['nomor_kamar']) ?>">
            <?php else: ?>
                <div class="ph d-grid place-items-center text-brown" style="display:grid;place-items:center"><i class="bi bi-door-open fs-3"></i></div>
            <?php endif; ?>
            <div class="flex-fill">
                <div class="small"><span class="pay-chip">Kamar <?= h($booking['nomor_kamar']) ?></span>
                    <span class="badge bg-warning-subtle text-warning-emphasis">Di-Keep</span></div>
                <div class="fw-bold fs-5"><?= h($booking['tipe_kamar']) ?></div>
                <div class="small text-muted d-flex flex-wrap gap-2">
                    <?php foreach ($fasilitas as $f): ?><span><i class="bi <?= h($f['icon']) ?> me-1"></i><?= h($f['label']) ?></span><?php endforeach; ?>
                </div>
                <div class="small text-muted"><?= h($booking['nama_pemesan']) ?> &middot; <?= (int)$booking['jumlah_tamu'] ?> tamu &middot; <span class="font-monospace"><?= h($booking['kode_booking']) ?></span></div>
            </div>
            <div class="text-end">
                <small class="text-muted fw-bold">TOTAL PEMBAYARAN</small>
                <div class="fw-bolder fs-4 text-brown"><?= $rp($total) ?></div>
                <small class="text-muted"><?= $durasiLabel ? h($durasiLabel) . ' &middot; ' : '' ?>Dibayar penuh</small>
            </div>
        </div>

        <!-- Timer -->
        <div class="pay-timer mb-4" data-deadline="<?= $deadlineTs ?>" data-total="<?= $keepMenit * 60 ?>">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-danger">
                    <small class="fw-bold d-block">SISA WAKTU KEEP</small>
                    <span class="fs-3 fw-bolder" id="keepCountdown">--:--</span> <small>menit tersisa</small>
                </div>
                <div class="text-end small text-muted">Batas Pembayaran<br><strong><?= h(date('d M Y, H.i', $deadlineTs)) ?> WIB</strong></div>
            </div>
            <div class="pay-bar mt-2"><div id="keepProgress" style="width:100%"></div></div>
            <small class="d-block mt-2 text-muted">Jika waktu habis, sesi keep berakhir dan kamar dilepas kembali ke katalog secara otomatis oleh sistem.</small>
        </div>

        <!-- 1. Metode -->
        <div class="fw-bold mb-2"><span class="pay-secno">1</span>Lakukan Pembayaran dengan QRIS</div>

        <!-- QRIS -->
        <div id="tab-qris" class="row g-3 mb-4">
            <div class="col-md-5">
                <div class="pay-qr h-100 d-flex flex-column justify-content-center align-items-center">
                    <?php if ($hasQris): ?>
                        <img src="<?= h($qrisUrl) ?>" alt="QRIS pembayaran Villa">
                        <a href="<?= h($qrisUrl) ?>" download="QRIS-Villa-DR-Jionsi.<?= h(pathinfo($activeQris, PATHINFO_EXTENSION) ?: 'jpg') ?>"
                           class="btn btn-sm btn-brown fw-semibold mt-2"><i class="bi bi-download me-1"></i>Unduh QRIS</a>
                        <small class="text-muted mt-1">Jika tidak terunduh, tekan lama gambar lalu pilih &ldquo;Simpan gambar&rdquo;.</small>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0 small">QRIS belum tersedia. Silakan hubungi pengelola untuk menyelesaikan pembayaran.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-7">
                <span class="pay-official">QRIS STATIS RESMI</span>
                <div class="small text-muted mt-1">STANDAR BANK INDONESIA</div>
                <div class="fw-bolder fs-5 mb-2">a.n. <?= h(villa_name()) ?></div>
                <div class="pay-amount d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <small class="text-muted fw-bold">NOMINAL PEMBAYARAN TEPAT</small>
                        <div class="fw-bolder fs-4 text-brown" id="paymentAmount"><?= $rp($total) ?></div>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-copy="<?= (int)$total ?>"><i class="bi bi-clipboard me-1"></i>Salin Nominal</button>
                </div>
                <div class="small fw-bold mb-1">PANDUAN PEMBAYARAN:</div>
                <ol class="small text-muted ps-3 mb-0">
                    <li>Buka aplikasi m-Banking (BCA, Mandiri, BRI, BNI) atau e-Wallet (GoPay, OVO, Dana, ShopeePay).</li>
                    <li>Pindai kode QR di samping.</li>
                    <li>Periksa nama merchant &ldquo;<?= h(villa_name()) ?>&rdquo; dan nominal <?= $rp($total) ?>.</li>
                    <li>Simpan bukti transaksi (screenshot / struk) untuk diunggah di bawah.</li>
                </ol>
            </div>
        </div>

        <!-- 2. Unggah -->
        <div class="fw-bold mb-2"><span class="pay-secno">2</span>Unggah Bukti Pembayaran</div>
        <form action="verifikasi.php" method="POST" enctype="multipart/form-data" id="payForm">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <label class="pay-drop w-100 mb-3" id="payDrop" for="bukti_transfer">
                <div id="dropEmpty">
                    <i class="bi bi-cloud-arrow-up fs-2 text-brown"></i>
                    <div class="fw-semibold">Pilih berkas</div>
                    <small class="text-muted">Format file: JPG, PNG, WEBP &middot; Ukuran maksimal 2 MB</small>
                </div>
                <div id="dropPreview" class="d-none">
                    <img id="previewImg" alt="Pratinjau bukti">
                    <div class="small mt-2 fw-semibold" id="previewName"></div>
                    <small class="text-muted">Klik untuk mengganti berkas</small>
                </div>
            </label>
            <input id="bukti_transfer" type="file" name="bukti_transfer" class="d-none" accept="image/jpeg,image/png,image/webp" required>
            <div class="small text-danger d-none mb-2" id="fileError"></div>

            <div class="row g-3 mb-3">
                <div class="col-sm-4">
                    <label class="form-label small fw-bold">Metode Pembayaran</label>
                    <input type="text" class="form-control" id="metodeView" value="QRIS" readonly>
                </div>
                <div class="col-sm-4">
                    <label class="form-label small fw-bold">Nominal yang Dibayarkan</label>
                    <div class="input-group"><span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" value="<?= number_format($total, 0, ',', '.') ?>" readonly></div>
                    <small class="text-muted">Nominal sesuai dengan total tagihan reservasi.</small>
                </div>
                <div class="col-sm-4">
                    <label class="form-label small fw-bold">Tanggal Pembayaran</label>
                    <input type="text" class="form-control" value="<?= h(date('d M Y')) ?>" readonly>
                    <small class="text-muted">Waktu transaksi tercatat real-time di sistem booking.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-brown w-100 fw-bold py-3" id="paySubmit">
                Kirim Bukti &amp; Verifikasi Pembayaran <i class="bi bi-arrow-right ms-1"></i>
            </button>
            <div class="text-center small text-muted mt-2">Pembayaran diverifikasi otomatis. Kode referensi reservasi akan tampil di langkah berikutnya.</div>
        </form>

        <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-4">
            <a class="small text-muted text-decoration-none" href="hasil_status.php?kode=<?= rawurlencode($booking['kode_booking']) ?>"><i class="bi bi-arrow-left me-1"></i>Cek status booking</a>
            <?php if ($whatsapp !== ''): ?>
                <a class="btn btn-sm btn-outline-success" href="https://wa.me/<?= h($whatsapp) ?>?text=<?= rawurlencode('Saya perlu bantuan pembayaran untuk booking ' . $booking['kode_booking']) ?>" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp me-1"></i>Hubungi pengelola
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Countdown + progress */
    var timer = document.querySelector('[data-deadline]');
    var out = document.getElementById('keepCountdown');
    var bar = document.getElementById('keepProgress');
    if (timer && out) {
        var deadline = Number(timer.dataset.deadline) * 1000;
        var total = Math.max(1, Number(timer.dataset.total) * 1000);
        var tick = function () {
            var rem = Math.max(0, deadline - Date.now());
            out.textContent = String(Math.floor(rem / 60000)).padStart(2, '0') + ':' + String(Math.floor(rem % 60000 / 1000)).padStart(2, '0');
            if (bar) bar.style.width = Math.min(100, rem / total * 100) + '%';
            if (rem === 0) window.location.reload();
        };
        tick(); setInterval(tick, 1000);
    }

    /* Salin nominal / rekening */
    document.querySelectorAll('[data-copy]').forEach(function (b) {
        b.addEventListener('click', async function () {
            var old = b.innerHTML;
            try { await navigator.clipboard.writeText(b.dataset.copy); b.innerHTML = '<i class="bi bi-check2 me-1"></i>Tersalin'; setTimeout(function () { b.innerHTML = old; }, 1800); }
            catch (e) { window.prompt('Salin:', b.dataset.copy); }
        });
    });

    /* Upload: drag & drop + pratinjau + validasi */
    var input = document.getElementById('bukti_transfer');
    var drop = document.getElementById('payDrop');
    var err = document.getElementById('fileError');
    function showFile(file) {
        err.classList.add('d-none');
        if (!file) return;
        if (['image/jpeg', 'image/png', 'image/webp'].indexOf(file.type) < 0) { err.textContent = 'Format harus JPG, PNG, atau WEBP.'; err.classList.remove('d-none'); input.value = ''; return; }
        if (file.size > 2 * 1024 * 1024) { err.textContent = 'Ukuran file maksimal 2 MB.'; err.classList.remove('d-none'); input.value = ''; return; }
        document.getElementById('previewImg').src = URL.createObjectURL(file);
        document.getElementById('previewName').textContent = file.name;
        document.getElementById('dropEmpty').classList.add('d-none');
        document.getElementById('dropPreview').classList.remove('d-none');
    }
    input.addEventListener('change', function () { showFile(input.files[0]); });
    ['dragenter', 'dragover'].forEach(function (ev) { drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.add('drag'); }); });
    ['dragleave', 'drop'].forEach(function (ev) { drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.remove('drag'); }); });
    drop.addEventListener('drop', function (e) {
        if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; showFile(input.files[0]); }
    });

    /* Cegah kirim ganda */
    document.getElementById('payForm').addEventListener('submit', function () {
        var b = document.getElementById('paySubmit'); b.disabled = true; b.textContent = 'Mengirim...';
    });
});
</script>