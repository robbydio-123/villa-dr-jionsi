<section class="container mt-4 mb-5" style="max-width: 850px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i> Katalog</a></li>
            <li class="breadcrumb-item"><a href="detail.php" class="text-decoration-none text-muted">Kamar 201</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><span class="text-muted fw-normal me-1">/</span> Formulir Reservasi</li>
        </ol>
    </nav>

<div class="card p-4 border-0 shadow-sm rounded-4 mb-4 text-center">
    <h6 class="text-muted fw-bold mb-3 small text-uppercase">Tahapan Reservasi</h6>
    
    <div class="tracker-container">
        
        <!-- Fase 1: Aktif saat ini -->
        <!-- Gunakan class "completed" jika sudah lewat, atau "active" jika sedang di tahap ini -->
        <div class="tracker-step active">
            <div class="tracker-circle">1</div>
            <div class="tracker-text">Pemesanan</div>
            <small class="text-muted" style="font-size: 0.75rem;">26 Okt, 10.00</small>
        </div>
        
        <!-- Fase 2: Belum Dilalui (Inactive) -->
        <div class="tracker-step inactive">
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

    <!-- Formulir Utama -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4 p-md-5">
            <!-- Header Form -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <span class="badge bg-brown text-white mb-2" style="font-size: 0.65rem;">LANGKAH 1 DARI 3</span>
                    <h3 class="fw-bolder mb-0">Data Pemesan</h3>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill px-3 py-2"><i class="bi bi-stopwatch me-1"></i> Keep 15 Menit</span>
            </div>

            <!-- Ringkasan Kamar -->
            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center bg-light bg-opacity-50 p-3 rounded border mb-4">
                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="rounded" style="width: 100px; height: 80px; object-fit: cover; background-color: #E5E0D8;">
                <div class="flex-fill">
                    <div class="mb-1">
                        <span class="badge bg-white border text-brown me-1">Kamar 201</span>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">Tersedia</span>
                    </div>
                    <h6 class="fw-bold mb-1">Family Suite Panderman</h6>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-droplet-half me-1"></i> Air Panas &nbsp; <i class="bi bi-tv me-1"></i> Smart TV &nbsp; <i class="bi bi-wifi me-1"></i> Wi-Fi
                    </div>
                </div>
                <div class="text-md-end border-start-md ps-md-4 mt-3 mt-md-0">
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Total pembayaran</small>
                    <h5 class="fw-bolder text-brown mb-0">Rp 1.700.000</h5>
                    <small class="text-muted" style="font-size: 0.7rem;">2 malam - Dibayar penuh</small>
                </div>
            </div>

            <!-- Form Input -->
            <form action="keep.php" method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Nama Lengkap *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-secondary-subtle text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control border-secondary-subtle" name="nama" value="Budi Wijaya" required>
                        </div>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.65rem;">Minimal 3 karakter. Dipakai untuk mengenali pesananmu saat check-in.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Nomor WhatsApp *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-secondary-subtle text-brown fw-semibold">+62</span>
                            <input type="text" class="form-control border-secondary-subtle" name="whatsapp" value="81278901234" required>
                        </div>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.65rem;">Dipakai pengelola untuk menghubungi bila diperlukan.</small>
                    </div>
                </div>

                <!-- Kartu Tanggal -->
                <div class="drj-date-wrap mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="drj-date-card">
                                <div class="drj-date-icon"><i class="bi bi-box-arrow-in-right"></i></div>
                                <div class="flex-fill">
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">CHECK-IN</small>
                                    <span class="fw-bold d-block small">26 Okt 2026</span>
                                </div>
                                <span class="drj-date-badge">14.00 WIB</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="drj-date-card">
                                <div class="drj-date-icon"><i class="bi bi-box-arrow-right"></i></div>
                                <div class="flex-fill">
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">CHECK-OUT</small>
                                    <span class="fw-bold d-block small">28 Okt 2026</span>
                                </div>
                                <span class="drj-date-badge">12.00 WIB</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted" style="font-size: 0.8rem;">Lama menginap: <strong>2 malam</strong></small>
                        <a href="#" class="text-brown text-decoration-none small fw-semibold">Ubah tanggal</a>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <label class="form-label small fw-bold text-dark mb-0">Jumlah Tamu</label>
                        <small class="text-muted" style="font-size: 0.65rem;">Maks 6 tamu</small>
                    </div>
                    <div class="drj-field-box">
                        <div class="drj-guest-row">
                            <div>
                                <div class="fw-semibold text-dark" id="guestLabel">4 Orang Tamu</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Termasuk kasur &amp; fasilitas</small>
                            </div>
                            <div class="drj-stepper">
                                <button class="btn" type="button" id="guestMinus">-</button>
                                <input type="text" name="jumlah_tamu" id="guestInput" value="4" readonly>
                                <button class="btn" type="button" id="guestPlus">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <label class="form-label small fw-bold text-dark mb-0">Catatan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <small class="text-muted" style="font-size: 0.65rem;">Maks 250 karakter</small>
                    </div>
                    <div class="drj-field-box">
                        <textarea class="form-control" name="catatan" rows="2" maxlength="250">Tiba sekitar pukul 15.30 WIB bersama keluarga.</textarea>
                    </div>
                </div>

    <script src="assets/js/formulir.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/formulir.js') ?>"></script>

                <!-- Persetujuan & Peringatan -->
                <div class="bg-light border border-secondary-subtle rounded p-3 mb-3 d-flex gap-3 align-items-start">
                    <input class="form-check-input mt-1" type="checkbox" checked required>
                    <small class="text-muted" style="font-size: 0.8rem;">Saya memahami bahwa keep berlaku 15 menit, pembayaran dilakukan penuh (100%), dan refund hanya dapat diajukan paling lambat H-1 sampai pukul 23.59 WIB.</small>
                </div>
                <div class="bg-warning bg-opacity-10 border border-warning-subtle text-brown rounded p-3 mb-4 d-flex gap-3 align-items-center">
                    <i class="bi bi-exclamation-circle fs-5"></i>
                    <small style="font-size: 0.8rem;">Setelah kamar dikunci, kamu punya 15 menit untuk transfer dan mengunggah bukti. Rincian rekening tampil di langkah berikutnya.</small>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-brown w-100 fw-bold py-3 mb-2 rounded-3"><i class="bi bi-lock-fill me-2"></i> Kunci Kamar & Lanjut ke Pembayaran</button>
                <div class="text-center mb-4">
                    <small class="text-muted" style="font-size: 0.65rem;">Kamar ditahan selama 15 menit setelah tombol ditekan.</small>
                </div>
                
                <a href="detail.php" class="text-decoration-none text-muted small fw-semibold"><i class="bi bi-arrow-left me-1"></i> Kembali ke detail kamar</a>
            </form>
        </div>
    </div>
</section>