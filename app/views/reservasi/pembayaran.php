<section class="container mt-4 mb-5" style="max-width: 850px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Katalog</a></li>
            <li class="breadcrumb-item"><a href="detail.php" class="text-decoration-none text-muted">Kamar 201</a></li>
            <li class="breadcrumb-item"><a href="formulir.php" class="text-decoration-none text-muted">Formulir Reservasi</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><span class="text-muted fw-normal me-1">/</span> Pesan & pembayaran</li>
        </ol>
    </nav>

    <!-- Card Tahapan Reservasi -->
    <div class="card p-4 border-0 shadow-sm rounded-4 mb-4 text-center">
        <h6 class="text-muted fw-bold mb-3 small text-uppercase">Tahapan Reservasi</h6>

        <div class="tracker-container">

            <!-- Fase 1: Aktif saat ini -->
            <!-- Gunakan class "completed" jika sudah lewat, atau "active" jika sedang di tahap ini -->
            <div class="tracker-step completed">
                <div class="tracker-circle">1</div>
                <div class="tracker-text">Pemesanan</div>
                <small class="text-muted" style="font-size: 0.75rem;">26 Okt, 10.00</small>
            </div>

            <!-- Fase 2: Belum Dilalui (Inactive) -->
            <div class="tracker-step active">
                <div class="tracker-circle">2</div>
                <div class="tracker-text text-dark">Pembayaran</div>
                <small class="text-muted" style="font-size: 0.75rem;">-</small>
            </div>

            <!-- Fase 3: Belum Dilalui (Inactive) -->
            <div class="tracker-step inactive">
                <div class="tracker-circle">3</div>
                <div class="tracker-text text-dark">Status</div>
                <small class="text-muted" style="font-size: 0.75rem;">-</small>
            </div>
        </div>
    </div>
    <!-- Konten Utama Pembayaran -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4 p-md-5">
            <!-- Header Langkah -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-brown text-white mb-2" style="font-size: 0.65rem;">LANGKAH 2 DARI 3</span>
                    <h3 class="fw-bolder mb-0">Pesan & Bayar</h3>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill px-3 py-2"><i class="bi bi-stopwatch me-1"></i> Pesan 15 Menit</span>
            </div>

            <!-- Ringkasan Singkat -->
            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center bg-light bg-opacity-50 p-3 rounded border mb-4">
                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="rounded" style="width: 80px; height: 60px; object-fit: cover; background-color: #E5E0D8;">
                <div class="flex-fill">
                    <div class="mb-1">
                        <small class="text-muted fw-bold" style="font-size: 0.65rem;">Kamar 201</small>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle ms-2" style="font-size: 0.6rem;">Di-Pesan</span>
                    </div>
                    <h6 class="fw-bold mb-0">Family Suite Panderman</h6>
                    <small class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-droplet-half me-1"></i> Air Panas &nbsp; <i class="bi bi-tv me-1"></i> Smart TV &nbsp; <i class="bi bi-wifi me-1"></i> Wi-Fi</small>
                </div>
                <div class="text-md-end border-start-md ps-md-4 mt-3 mt-md-0">
                    <small class="text-muted d-block" style="font-size: 0.7rem;">TOTAL PEMBAYARAN</small>
                    <h4 class="fw-bolder text-brown mb-0">Rp 1.700.000</h4>
                    <small class="text-muted" style="font-size: 0.7rem;">2 malam - Dibayar penuh</small>
                </div>
            </div>

            <!-- Timer Mundur -->
            <div class="timer-box rounded p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2 text-danger">
                        <i class="bi bi-stopwatch fs-4"></i>
                        <div>
                            <small class="fw-bold d-block" style="font-size: 0.7rem; color: #E65100;">SISA WAKTU PEMESANAN</small>
                            <h4 class="fw-bolder mb-0" style="color: #E65100;">14:25 <span class="fs-6 fw-normal">menit tersisa</span></h4>
                        </div>
                    </div>
                    <div class="text-end">
                        <small style="font-size: 0.7rem; color: #856404;">Batas Pembayaran: <strong>26 Okt 2026, 10.15 WIB</strong></small>
                    </div>
                </div>
                <div class="timer-progress">
                    <div class="timer-progress-bar"></div>
                </div>
                <small class="d-block mt-2" style="font-size: 0.7rem; color: #856404;">Jika waktu habis, status keep kamar akan dibatalkan otomatis oleh sistem dan unit dibuka kembali untuk tamu lain.</small>
            </div>

            <!-- Bagian 1: Pilih Metode -->
            <h6 class="fw-bold mb-3"><span class="badge bg-light text-dark border rounded-circle me-2 p-2">1</span> Pilih Metode & Lakukan Pembayaran</h6>

            <ul class="nav nav-pills nav-pills-custom mb-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-2 d-flex align-items-center justify-content-center w-100" id="qris-tab" data-bs-toggle="pill" data-bs-target="#qris" type="button" role="tab"><i class="bi bi-qr-code-scan me-2"></i> QRIS</button>
                </li>
            </ul>

            <div class="tab-content mb-5" id="pills-tabContent">

                <!-- Tab QRIS -->
                <div class="tab-pane fade" id="qris" role="tabpanel">
                    <div class="border rounded p-4 d-flex flex-column flex-md-row gap-4">
                        <div class="bg-light p-3 rounded text-center border" style="min-width: 200px;">
                            <small class="fw-bold text-brown d-block mb-2" style="font-size: 0.65rem;">QRIS • STANDAR PEMBAYARAN NASIONAL</small>
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="img-fluid bg-white p-2 border rounded" style="width: 150px; height: 150px;" alt="QR Code QRIS">
                            <small class="text-muted d-block mt-2" style="font-size: 0.65rem;">NMID: ID1029384756102</small>
                        </div>
                        <div class="flex-fill">
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle mb-2">QRIS STATIS RESMI</span>
                            <h5 class="fw-bold mb-1">a.n. Villa D-R Jionsi</h5>

                            <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded border mt-3 mb-3">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">NOMINAL PEMBAYARAN TEPAT</small>
                                    <span class="fw-bold text-brown">Rp 1.700.000</span>
                                </div>
                                <button class="btn copy-btn bg-white"><i class="bi bi-clipboard me-1"></i> Salin Nominal</button>
                            </div>

                            <small class="fw-bold d-block mb-1" style="font-size: 0.7rem;">PANDUAN PEMBAYARAN:</small>
                            <ul class="text-muted small ps-3 mb-0" style="font-size: 0.75rem;">
                                <li>Buka aplikasi m-Banking atau e-Wallet (GoPay, OVO, Dana, ShopeePay).</li>
                                <li>Pindai kode QR di samping.</li>
                                <li>Periksa nama merchant "<strong>Villa D-R Jionsi</strong>" dan input nominal <strong>Rp 1.700.000</strong>.</li>
                                <li>Simpan bukti transaksi (screenshot / struk) untuk diunggah di bawah.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Form Upload -->
            <form action="verifikasi.php" method="POST" enctype="multipart/form-data">
                <h6 class="fw-bold mb-3"><span class="badge bg-light text-dark border rounded-circle me-2 p-2">2</span> Unggah Bukti Pembayaran</h6>

                <div class="upload-area mb-4">
                    <div class="text-brown mb-2"><i class="bi bi-cloud-arrow-up fs-2"></i></div>
                    <h6 class="fw-bold small mb-1">Seret berkas ke sini atau pilih berkas</h6>
                    <small class="text-muted" style="font-size: 0.7rem;">Format file: JPG, PNG, atau WEBP - Ukuran maksimal 2 MB</small>
                    <!-- Input file disembunyikan agar desain area drop terlihat rapi, namun tetap fungsional jika diklik -->
                    <input type="file" name="bukti_transfer" class="form-control mt-3 mx-auto" style="max-width: 300px;" accept="image/*" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Metode Pembayaran *</label>
                        <select class="form-select border-secondary-subtle" name="metode" required>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Nominal yang Dibayarkan (Rp) *</label>
                        <input type="text" class="form-control border-secondary-subtle bg-light" name="nominal" value="Rp 1.700.000" readonly>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.65rem;">Nominal disesuaikan dengan total tagihan reservasi.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Tanggal Pembayaran *</label>
                        <div class="input-group">
                            <input type="text" class="form-control border-secondary-subtle bg-light" name="tanggal" value="26 Okt 2026" readonly>
                            <span class="input-group-text bg-white border-secondary-subtle"><i class="bi bi-calendar"></i></span>
                        </div>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.65rem;">Waktu transaksi tercatat real-time di sistem booking.</small>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-brown w-100 fw-bold py-3 mb-3 rounded-3">Kirim Bukti & Verifikasi Pembayaran <i class="bi bi-arrow-right ms-1"></i></button>
                <div class="text-center mb-4">
                    <small class="text-muted" style="font-size: 0.7rem;">Pembayaran diverifikasi otomatis. Kode referensi reservasi akan tampil di langkah berikutnya.</small>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="formulir.php" class="text-decoration-none text-muted small fw-semibold"><i class="bi bi-arrow-left me-1"></i> Kembali ke langkah sebelumnya</a>
                    <span class="text-muted small" style="font-size: 0.65rem;"><i class="bi bi-lock me-1"></i> Sistem Enkripsi Aman Villa D-R Jionsi</span>
                </div>
            </form>
        </div>
    </div>
</section>