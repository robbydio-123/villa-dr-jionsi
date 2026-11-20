<section class="container mt-4 mb-5" style="max-width: 850px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="beranda.php" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="formulir.php" class="text-decoration-none text-muted">Formulir Reservasi</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><span class="text-muted fw-normal me-1">/</span> Verifikasi Bukti Pembayaran</li>
        </ol>
    </nav>

    <!-- Card Tahapan Reservasi -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="form-stepper">
                <div class="step-item active">
                    <div class="step-circle bg-success text-white border-0"><i class="bi bi-check-lg fs-5"></i></div>
                    <span class="title">1. Data Tamu</span>
                    <span class="subtitle text-dark mt-1">Terisi Lengkap</span>
                </div>
                <div class="step-item active">
                    <div class="step-circle bg-success text-white border-0"><i class="bi bi-check-lg fs-5"></i></div>
                    <span class="title">2. Keep & Bayar</span>
                    <span class="subtitle text-dark mt-1">Slot Diamankan</span>
                </div>
                <div class="step-item active">
                    <div class="step-circle bg-danger text-white border-0"><i class="bi bi-exclamation-lg fs-5"></i></div>
                    <span class="title text-danger">3. Verifikasi Bukti</span>
                    <span class="subtitle text-danger fw-bold mt-1">Perlu Tindakan Segera</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Bukti Ditolak -->
    <div class="alert bg-danger bg-opacity-10 border border-danger-subtle rounded-4 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="fw-bolder text-danger mb-0"><i class="bi bi-x-octagon-fill me-2"></i> Bukti Pembayaran Belum Dapat Diterima</h5>
            <span class="badge bg-danger text-white rounded-pill px-3 py-1 small">Gagal Verifikasi</span>
        </div>
        <p class="text-muted small mb-3">
            Mohon maaf, bukti pembayaran yang Anda kirimkan tidak dapat diverifikasi oleh pengelola. Hal ini biasanya disebabkan oleh nominal mutasi yang tidak sesuai, struk/screenshot terpotong, atau transaksi perbankan belum tercatat.
        </p>

        <!-- Kotak File Bukti yang Ditolak -->
        <div class="file-rejected-box bg-white">
            <div class="bg-danger bg-opacity-10 text-danger p-2 rounded border border-danger-subtle"><i class="bi bi-file-earmark-x fs-4"></i></div>
            <div class="flex-fill">
                <small class="fw-bold d-block text-dark" style="font-size: 0.8rem;">bukti_transfer_terakhir.jpg</small>
                <small class="text-danger" style="font-size: 0.7rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i> Catatan Verifikator: Nominal atau nomor referensi tidak sesuai dengan tagihan.</small>
            </div>
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">Ditolak</span>
        </div>
    </div>

    <!-- Card Form Unggah Ulang Bukti Pembayaran -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-danger text-white mb-2" style="font-size: 0.65rem;">TINDAKAN DIPERLUKAN</span>
                    <h3 class="fw-bolder mb-0">Unggah Ulang Bukti Transfer</h3>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill px-3 py-2">
                    <i class="bi bi-stopwatch me-1"></i> Kamar Masih Di-Keep
                </span>
            </div>

            <!-- Ringkasan Singkat Pesanan -->
            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center bg-light bg-opacity-50 p-3 rounded border mb-4">
                <div class="flex-fill">
                    <small class="text-muted fw-bold d-block" style="font-size: 0.65rem;">KODE RESERVASI</small>
                    <span class="fw-bold font-monospace text-dark">DRJ-20261026-K201-7QF4</span>
                    <div class="text-muted mt-1" style="font-size: 0.75rem;">Kamar 201 &bull; Family Suite Panderman</div>
                </div>
                <div class="text-md-end border-start-md ps-md-4 mt-2 mt-md-0">
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Nominal yang harus dibayarkan</small>
                    <h5 class="fw-bolder text-brown mb-0">Rp 1.700.000</h5>
                </div>
            </div>

            <!-- Form Upload Ulang -->
            <form action="verifikasi.php" method="POST" enctype="multipart/form-data">
                <div class="upload-area mb-4">
                    <div class="text-brown mb-2"><i class="bi bi-cloud-arrow-up fs-2"></i></div>
                    <h6 class="fw-bold small mb-1">Pilih berkas bukti pembayaran baru</h6>
                    <small class="text-muted" style="font-size: 0.7rem;">Format file: JPG, PNG, atau WEBP - Ukuran maksimal 2 MB</small>
                    <input type="file" name="bukti_transfer" class="form-control mt-3 mx-auto" style="max-width: 320px;" accept="image/*" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Metode Pembayaran *</label>
                        <input type="text" class="form-control border-secondary-subtle" value="QRIS Resmi Villa D-R Jionsi" readonly>
                        <input type="hidden" name="metode" value="QRIS">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Nominal yang Ditransfer (Rp) *</label>
                        <input type="text" class="form-control border-secondary-subtle bg-light" name="nominal" value="Rp 1.700.000" readonly>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-brown w-100 fw-bold py-3 mb-3 rounded-3">
                    <i class="bi bi-arrow-repeat me-1"></i> Kirim Ulang Bukti Pembayaran
                </button>
            </form>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top">
                <a href="beranda.php" class="text-decoration-none text-muted small fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog Kamar
                </a>
                <a href="https://wa.me/6281233449081" target="_blank" class="btn btn-outline-success btn-sm fw-bold rounded-pill px-3">
                    <i class="bi bi-whatsapp me-1"></i> Konfirmasi ke Pengelola (+62 812-3344-9081)
                </a>
            </div>
        </div>
    </div>
</section>
