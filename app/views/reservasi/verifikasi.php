<section class="container mt-4 mb-5" style="max-width: 850px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="beranda.php" class="text-decoration-none text-muted">Katalog</a></li>
            <li class="breadcrumb-item"><a href="detail.php" class="text-decoration-none text-muted">Kamar 201</a></li>
            <li class="breadcrumb-item"><a href="formulir.php" class="text-decoration-none text-muted">Formulir Reservasi</a></li>
            <li class="breadcrumb-item"><a href="keep.php" class="text-decoration-none text-muted">Pesan & pembayaran</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><span class="text-muted fw-normal me-1">/</span> Verifikasi Pembayaran</li>
        </ol>
    </nav>

    <!-- Card Tahapan Reservasi -->
    <div class="tracker-container">
    
    <!-- Fase 1: Sudah Dilewati (Completed) -->
    <div class="tracker-step completed">
        <div class="tracker-circle">1</div>
        <div class="tracker-text">Pemesanan</div>
        <small class="text-muted" style="font-size: 0.75rem;">26 Okt, 10.00</small>
    </div>
    
    <!-- Fase 2: Aktif Saat Ini (Active) -->
    <div class="tracker-step completed">
        <div class="tracker-circle">2</div>
        <div class="tracker-text text-dark">Pembayaran</div>
        <small class="text-muted" style="font-size: 0.75rem;">Diproses</small>
    </div>
    
    <!-- Fase 3: Belum Dilalui (Inactive) -->
    <div class="tracker-step active">
        <div class="tracker-circle">3</div>
        <div class="tracker-text text-dark">Status</div>
        <small class="text-muted" style="font-size: 0.75rem;">-</small>
    </div>

</div>

    <!-- Judul & Status -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-white text-muted border px-3 py-2" style="font-size: 0.7rem;">LANGKAH 3 DARI 3</span>
        <span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill px-3 py-2"><i class="bi bi-circle-fill text-warning me-1" style="font-size: 0.5rem;"></i> Status: Menunggu Konfirmasi</span>
    </div>
    
    <h3 class="fw-bolder mb-3">Bukti Pembayaran Terkirim & Menunggu Verifikasi</h3>
    <p class="text-muted mb-4">Terima kasih! Bukti pembayaranmu berhasil kami terima. Pengelola Villa D-R Jionsi Songgokerto akan memverifikasi mutasi bank/QRIS dalam estimasi 5-15 menit.</p>

    <!-- Kode Reservasi Penting -->
    <div class="card border border-warning-subtle shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0 p-4 pb-2">
            <small class="fw-bold text-muted d-block mb-1" style="font-size: 0.7rem;"><i class="bi bi-bookmark-star text-brown me-1"></i> KODE RESERVASI RESMI ANDA (SIMPAN KODE INI)</small>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="d-flex justify-content-between align-items-center bg-light border rounded p-3 mb-3">
                <div class="d-flex gap-3 align-items-center">
                    <div class="bg-white p-2 rounded border text-brown"><i class="bi bi-ticket-detailed fs-4"></i></div>
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Nomor Registrasi Unik</small>
                        <h4 class="fw-bolder mb-0 font-monospace">DRJ-20261026-K201-7QF4</h4>
                    </div>
                </div>
                <button class="btn btn-outline-secondary bg-white btn-sm fw-bold"><i class="bi bi-clipboard me-1"></i> Salin Kode</button>
            </div>
            
            <div class="bg-warning bg-opacity-10 text-brown p-3 rounded d-flex gap-3 align-items-start border border-warning-subtle">
                <i class="bi bi-exclamation-circle-fill mt-1"></i>
                <small style="font-size: 0.8rem;"><strong>Perhatian:</strong> Simpan kode ini. Kode dipakai untuk memantau status reservasi di menu "<strong>Cek Status Reservasi</strong>" dan wajib ditunjukkan saat proses check-in di front office Villa D-R Jionsi Songgokerto.</small>
            </div>
        </div>
    </div>

    <!-- Status Verifikasi Real-Time -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-3 align-items-center">
                    <div class="text-muted"><i class="bi bi-hourglass-split fs-3 spin-icon"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">Proses Verifikasi Real-Time</h6>
                        <small class="text-muted" style="font-size: 0.7rem;">Estimasi waktu penyelesaian: 5 - 15 Menit</small>
                    </div>
                </div>
                <span class="badge bg-light text-muted border fw-normal"><i class="bi bi-shield-check me-1"></i> Sistem Audit Aktif</span>
            </div>
            
            <p class="small text-muted mb-3" style="font-size: 0.8rem;">Pemeriksaan otomatis mutasi perbankan & konfirmasi manual staf operasional Songgokerto sedang berjalan. Anda tidak perlu mengunggah ulang bukti pembayaran. Sistem akan memperbarui halaman ini secara instan saat dana terkonfirmasi.</p>
            
            <div class="dashed-box p-3 rounded d-flex justify-content-between align-items-center">
                <div class="d-flex gap-3 align-items-center">
                    <div class="bg-white border rounded p-2 text-primary"><i class="bi bi-image"></i></div>
                    <div>
                        <small class="fw-bold d-block text-dark" style="font-size: 0.75rem;">bukti-transfer-qris-budi.jpg</small>
                        <small class="text-muted" style="font-size: 0.65rem;">1.4 MB • Format Gambar JPEG</small>
                    </div>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle"><i class="bi bi-cloud-check me-1"></i> Terkirim ke Server</span>
            </div>
        </div>
    </div>

    <!-- Ringkasan Reservasi Lengkap -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                <h6 class="fw-bold mb-0">Ringkasan Reservasi Kamar</h6>
                <small class="text-muted" style="font-size: 0.7rem;">Detail Transaksi #DRJ-20261026</small>
            </div>
            
            <div class="d-flex flex-column flex-md-row gap-4 mb-3">
                <div class="position-relative" style="width: 100%; max-width: 250px;">
                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="rounded w-100" style="height: 150px; object-fit: cover; background-color: #E5E0D8;">
                    <span class="badge bg-white text-dark position-absolute top-0 start-0 m-2 border opacity-75" style="font-size: 0.6rem;"><i class="bi bi-camera me-1"></i> Kondisi Kamar Terverifikasi</span>
                    <span class="badge bg-dark text-white position-absolute bottom-0 end-0 m-2 bg-opacity-75" style="font-size: 0.65rem;">Lantai 2 • View Panderman</span>
                </div>
                
                <div class="flex-fill">
                    <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.65rem;">UNIT FAMILY SUITE</small>
                    <h6 class="fw-bold mb-2">Kamar 201 • Family Suite Panderman</h6>
                    <small class="text-muted d-block mb-3" style="font-size: 0.75rem;"><i class="bi bi-people me-1"></i> 4 Tamu Dewasa &nbsp;•&nbsp; <i class="bi bi-moon me-1"></i> 1 King + 2 Single</small>
                    
                    //masih tak edit
                    <div class="row g-3 mb-3">
    <div class="col-6">
        <div class="info-card h-100">
            <div class="info-icon"><i class="bi bi-box-arrow-in-right"></i></div>
            <small class="info-label d-block">Check-in</small>
            <span class="fw-semibold small d-block">Sen, 26 Okt 2026</span>
            <small class="info-sub">(Mulai 14.00 WIB)</small>
        </div>
    </div>
    <div class="col-6">
        <div class="info-card h-100">
            <div class="info-icon"><i class="bi bi-box-arrow-right"></i></div>
            <small class="info-label d-block">Check-out</small>
            <span class="fw-semibold small d-block">Rab, 28 Okt 2026</span>
            <small class="info-sub">(Maks 12.00 WIB)</small>
        </div>
    </div>
    <div class="col-6">
        <div class="info-card h-100">
            <div class="info-icon"><i class="bi bi-person"></i></div>
            <small class="info-label d-block">Nama Pemesan</small>
            <span class="fw-semibold small d-block">Budi Wijaya</span>
            <small class="info-sub">+62 812-7890-1234</small>
        </div>
    </div>
    <div class="col-6">
        <div class="info-card h-100">
            <div class="info-icon"><i class="bi bi-clock-history"></i></div>
            <small class="info-label d-block">Waktu Pengunggahan</small>
            <span class="fw-semibold small d-block">26 Okt 2026, 10.08 WIB</span>
            <small class="info-sub">Via Jalur QRIS Statis</small>
        </div>
    </div>
</div>
            
            <div class="bg-light p-3 rounded d-flex justify-content-between align-items-center border">
                <div>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Total Pembayaran (2 Malam)</small>
                    <small class="text-dark fw-bold" style="font-size: 0.75rem;">Lunas via QRIS</small>
                </div>
                <h5 class="fw-bolder text-brown mb-0">Rp 1.700.000</h5>
            </div>
        </div>
    </div>

    <!-- Ketentuan Kedatangan -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white border-bottom-0 p-4 pb-2">
            <small class="fw-bold text-muted d-block" style="font-size: 0.7rem;"><i class="bi bi-shield-exclamation text-brown me-1"></i> KETENTUAN KEBIJAKAN REFUND & PROSEDUR KEDATANGAN</small>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-arrow-counterclockwise text-brown"></i>
                            <h6 class="fw-bold small mb-0">Batas Waktu Pengajuan Refund</h6>
                        </div>
<p class="small text-muted mb-0" style="font-size: 0.75rem;">Pembatalan dengan pengembalian dana 100% dapat diajukan paling lambat <strong>1 hari (24 jam) setelah pemesanan dibuat</strong> (H-1 sejak pemesanan), dan sebelum waktu check-in.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-key text-brown"></i>
                            <h6 class="fw-bold small mb-0">Waktu Check-in & Kunci Kamar</h6>
                        </div>
                        <p class="small text-muted mb-0" style="font-size: 0.75rem;">Check-in dimulai pukul 14.00 WIB tanggal 26 Okt 2026. Tunjukkan kode reservasi resmi kepada resepsionis di Songgokerto untuk serah terima kunci fisik.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi Akhir -->
    <div class="d-flex flex-column flex-md-row gap-3 mb-4">
        <!-- Mengarahkan ke halaman hasil status yang telah kita buat sebelumnya -->
        <a href="hasil_status.php" class="btn btn-brown flex-fill fw-bold py-3"><i class="bi bi-search me-2"></i> Pantau Status di Cek Status Reservasi</a>
        <button class="btn btn-outline-secondary flex-fill fw-bold py-3 bg-white"><i class="bi bi-download me-2"></i> Unduh Ringkasan Reservasi (PDF)</button>
    </div>
    
    <div class="text-center">
        <a href="#" class="text-decoration-none text-muted small border-bottom border-secondary pb-1"><i class="bi bi-whatsapp me-1"></i> Hubungi WhatsApp Pengelola (Jika Perlu Konfirmasi Cepat)</a>
    </div>
</section>