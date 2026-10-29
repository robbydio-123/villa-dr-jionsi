<section class="container mt-4 mb-5" style="max-width: 850px;">
    <!-- Bagian Atas: Breadcrumb & Indikator Server -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Beranda</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Cek Status Reservasi</a></li>
                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Hasil Pencarian</li>
            </ol>
        </nav>
        <span class="badge bg-light text-muted border rounded-pill fw-normal" style="font-size: 0.7rem;"><i class="bi bi-circle-fill text-secondary me-1" style="font-size: 0.4rem;"></i> Terhubung ke Server Kota Batu</span>
    </div>

    <!-- Judul & Kode Booking -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
        <div>
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill mb-2 px-3 py-2"><i class="bi bi-search me-1"></i> HASIL PENCARIAN</span>
            <h2 class="fw-bolder mb-1">Status Reservasi</h2>
            <p class="text-muted small mb-0">Pantau verifikasi jadwal, masa keep kamar, dan kesiapan kedatangan di Songgokerto Kota Batu.</p>
        </div>
        <div class="card border border-success-subtle shadow-sm mt-3 mt-md-0" style="min-width: 250px;">
            <!-- Pembungkus berlatar hijau dengan teks ke tengah (text-center) -->
            <div class="bg-success bg-opacity-10 border-bottom border-success-subtle p-3 text-center rounded-top">
                <span class="text-success fw-bold small">
                    <i class="bi bi-check-circle-fill me-1"></i> Dibayar (Terkonfirmasi)
                </span>
            </div>
            <div class="card-body py-2 bg-white rounded-bottom">
                <small class="text-muted d-block" style="font-size: 0.65rem;">KODE BOOKING RESMI</small>
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bolder mb-0 font-monospace">DRJ-20261026-K201-7QF4</h5>
                    <button class="btn btn-sm text-brown border-0"><i class="bi bi-files"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Ringkasan Kamar -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3 d-flex flex-column flex-md-row gap-3 align-items-md-center">
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="rounded" style="width: 100px; height: 80px; object-fit: cover; background-color: #E5E0D8;" alt="Kamar 201">
            <div class="flex-fill">
                <small class="text-muted fw-bold" style="font-size: 0.65rem;">VILLA D-R JIONSI SONGGOKERTO</small>
                <h6 class="fw-bold mb-1">Kamar 201 • Family Suite Panderman</h6>
                <div class="text-muted" style="font-size: 0.75rem;">
                    <i class="bi bi-droplet-half me-1"></i> Air Panas &nbsp; <i class="bi bi-tv me-1"></i> Smart TV &nbsp; <i class="bi bi-wifi me-1"></i> Wi-Fi Highland &nbsp; <i class="bi bi-mountain me-1"></i> Pemandangan Gunung
                </div>
            </div>
            <div class="text-md-end border-start-md ps-md-4 mt-3 mt-md-0">
                <small class="text-muted d-block" style="font-size: 0.7rem;">TOTAL NILAI SEWA</small>
                <h4 class="fw-bolder text-brown mb-0">Rp 1.700.000</h4>
                <small class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-check-circle text-success me-1"></i> Termasuk pajak • 2 malam</small>
            </div>
        </div>
    </div>

    <!-- Data Tamu Tersamar -->
    <div class="d-flex justify-content-between align-items-center bg-white border rounded p-3 mb-4 shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-light p-2 rounded text-muted"><i class="bi bi-person-fill"></i></div>
            <div class="fw-semibold small">Budi W*** &nbsp;<span class="text-muted fw-normal">, +62 81278****34</span></div>
        </div>
        <small class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-lock"></i> Data disamarkan demi keamanan publik</small>
    </div>

    <!-- Jadwal Check-in/Out & Kapasitas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="border rounded p-3 bg-white h-100 d-flex gap-3 align-items-center shadow-sm">
                <div class="bg-light p-2 rounded text-brown"><i class="bi bi-box-arrow-in-right fs-5"></i></div>
                <div>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">CHECK-IN</small>
                    <span class="fw-bold d-block small">26 Okt 2026</span>
                    <span class="text-muted" style="font-size: 0.7rem;">Pukul 14.00 WIB</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3 bg-white h-100 d-flex gap-3 align-items-center shadow-sm">
                <div class="bg-light p-2 rounded text-brown"><i class="bi bi-box-arrow-right fs-5"></i></div>
                <div>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">CHECK-OUT</small>
                    <span class="fw-bold d-block small">28 Okt 2026</span>
                    <span class="text-muted" style="font-size: 0.7rem;">Pukul 12.00 WIB</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3 bg-white h-100 d-flex gap-3 align-items-center shadow-sm">
                <div class="bg-light p-2 rounded text-muted"><i class="bi bi-people-fill fs-5"></i></div>
                <div>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">KAPASITAS TAMU</small>
                    <span class="fw-bold d-block small">4 Tamu Dewasa</span>
                    <span class="text-muted" style="font-size: 0.7rem;">Maksimal Suite Panderman</span>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Alert Sukses Utama -->
    <div class="alert bg-success bg-opacity-10 border border-success border-opacity-25 d-flex gap-3 align-items-start p-4 mb-4">
        <i class="bi bi-check-circle-fill text-success fs-3 mt-1"></i>
        <div class="w-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="fw-bold text-success mb-0">Pembayaran Lunas & Kamar Resmi Terkunci</h6>
                <span class="badge bg-white text-success border border-success border-opacity-50 fw-normal"><i class="bi bi-shield-check me-1"></i> Terverifikasi Otomatis</span>
            </div>
            <p class="small text-success mb-0" style="opacity: 0.9;">Pembayaran sebesar Rp 1.700.000 telah diverifikasi oleh tim pengelola pada 26 Okt 2026, 10.15 WIB via Transfer Bank BCA / QRIS.</p>
        </div>
    </div>

    <!-- Alert Info Kedatangan -->
    <div class="alert bg-light-yellow text-yellow-dark d-flex gap-3 align-items-start p-3 mb-4">
        <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
        <div>
            <h6 class="fw-bold mb-1 small">Informasi Penting Kedatangan</h6>
            <p class="small mb-0" style="font-size: 0.75rem;">Simpan kode ini. Tunjukkan kode booking <strong>DRJ-20261026-K201-7QF4</strong> atau pesan WhatsApp resmi kepada resepsionis saat tiba di Songgokerto untuk pengambilan kunci kamar.</p>
        </div>
    </div>

    <!-- Grid Panduan Check-in & Lokasi -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold small mb-3"><i class="bi bi-clock-history text-brown me-2"></i> Jadwal & Prosedur Check-in</h6>
                    <ul class="list-unstyled small text-muted mb-0" style="font-size: 0.75rem;">
                        <li class="mb-2"><i class="bi bi-calendar-check me-2"></i> <strong>Jadwal Check-in:</strong> 26 Okt 2026 mulai pukul 14.00 WIB (Early check-in tergantung ketersediaan).</li>
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> <strong>Lokasi:</strong> Villa D-R Jionsi, Songgokerto, Kec. Batu, Kota Batu.</li>
                        <li><i class="bi bi-arrow-counterclockwise me-2"></i> <strong>Kebijakan Refund:</strong> Refund dapat diajukan lewat WhatsApp paling lambat <strong>25 Okt 2026, pukul 23.59 WIB</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold small mb-3"><i class="bi bi-signpost-split text-brown me-2"></i> Panduan Menuju Lokasi</h6>
                    <p class="small text-muted mb-3" style="font-size: 0.75rem;">Akses mudah dan aspal mulus pegunungan Batu. Area parkir luas tepat di depan lobby Family Suite Panderman.</p>
                    <div class="d-flex gap-2">
                        <button class="btn btn-brown btn-sm flex-fill fw-bold py-2"><i class="bi bi-geo-alt-fill me-1"></i> Buka Petunjuk Arah<br><span style="font-size: 0.6rem; font-weight:normal;">(Google Maps)</span></button>
                        <button class="btn btn-light border btn-sm flex-fill fw-bold py-2 text-dark"><i class="bi bi-download me-1"></i> Unduh Bukti<br><span style="font-size: 0.6rem; font-weight:normal;">Reservasi (PDF)</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opsi Pembatalan -->
    <div class="card border border-danger-subtle shadow-sm mb-5">
        <div class="card-header bg-white border-bottom-0 p-4 pb-2 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-danger mb-0"><i class="bi bi-journal-x me-2"></i> Perlu membatalkan reservasi?</h6>
            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle fw-normal"><i class="bi bi-check-circle me-1"></i> Ketentuan Refund H-1</span>
        </div>
        <div class="card-body p-4 pt-0">
            <p class="small text-muted mb-4">Refund <strong class="text-success">100% penuh</strong> dapat diajukan paling lambat 25 Okt 2026 pukul 23.59 WIB (H-1 sebelum waktu check-in tanpa potongan biaya admin).</p>

           <div class="row g-3 mb-4 text-center">
        <div class="col-4">
            <div class="step-card bg-light rounded p-3 h-100">
                <span class="step-badge badge bg-white text-danger border rounded-circle p-2 mb-2">1</span>
                <h6 class="fw-bold small mb-1">Klik Tombol WhatsApp</h6>
                <p class="small text-muted mb-0" style="font-size: 0.7rem;">Hubungi tim resepsionis resmi Villa D-R Jionsi via WhatsApp.</p>
            </div>
        </div>
        <div class="col-4">
            <div class="step-card bg-light rounded p-3 h-100">
                <span class="step-badge badge bg-white text-danger border rounded-circle p-2 mb-2">2</span>
                <h6 class="fw-bold small mb-1">Sebutkan Kode Booking</h6>
                <p class="small text-muted mb-0" style="font-size: 0.7rem;">Kirim kode booking &amp; nama pemesan terdaftar.</p>
            </div>
        </div>
        <div class="col-4">
            <div class="step-card bg-light rounded p-3 h-100">
                <span class="step-badge badge bg-white text-danger border rounded-circle p-2 mb-2">3</span>
                <h6 class="fw-bold small mb-1">Pengelola Memproses</h6>
                <p class="small text-muted mb-0" style="font-size: 0.7rem;">Dana ditransfer 100% tanpa potongan dalam kurun 1-3 hari kerja.</p>
            </div>
        </div>
    </div>

            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-danger fw-bold px-4"><i class="bi bi-x-circle me-1"></i> Ajukan Pembatalan via WhatsApp</button>
                <small class="text-muted"><i class="bi bi-clock me-1"></i> Waktu respons rata-rata: < 15 menit</small>
            </div>
        </div>
    </div>
</section>