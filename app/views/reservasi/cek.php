<section class="container mt-5 mb-5" style="min-height: 60vh;">
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 col-lg-6 text-center">
            <h2 class="fw-bolder mb-3" style="color: var(--drj-primary);">Cek Status Reservasi</h2>
            <p class="text-muted mb-4">Masukkan Kode Booking yang Anda dapatkan saat melakukan pemesanan untuk melihat status, tagihan, atau e-tiket Anda.</p>
            
            <!-- Form Pencarian Kode Booking -->
            <form class="card p-2 shadow-sm border-0 bg-white rounded-pill">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-0 ps-4 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-0 shadow-none bg-white" placeholder="Contoh: DRJ-8A9X2Z" style="font-size: 1rem;">
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold">Lacak Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- HASIL PENCARIAN (Contoh Tampilan Jika Kode Ditemukan) -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 overflow-hidden">
                <!-- Header Status -->
                <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block mb-1">KODE BOOKING</small>
                        <h4 class="fw-bolder mb-0 font-monospace">DRJ-8A9X2Z</h4>
                    </div>
                    <!-- Badge Status (Bisa diganti: Menunggu Pembayaran, Lunas, Dibatalkan, Selesai) -->
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill border border-warning shadow-sm"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi Pembayaran</span>
                </div>

                <div class="card-body p-4 bg-light border-top border-bottom">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-3">Rincian Tamu</h6>
                            <p class="mb-1 fw-semibold">Muhammad Afif Naufal</p>
                            <p class="small text-muted mb-1"><i class="bi bi-telephone text-secondary me-2"></i> 0812-3456-7890</p>
                            <p class="small text-muted mb-0"><i class="bi bi-envelope text-secondary me-2"></i> afif@example.com</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-3">Rincian Menginap</h6>
                            <p class="mb-1 fw-bold text-dark">Kamar 201 · Family Suite Panderman</p>
                            <div class="d-flex justify-content-between small text-muted mb-1 mt-2">
                                <span>Check-in:</span>
                                <span class="fw-semibold text-dark">26 Okt 2026, 14:00</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Check-out:</span>
                                <span class="fw-semibold text-dark">28 Okt 2026, 12:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white p-4 border-top-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <small class="text-muted fw-bold d-block mb-1">TOTAL TAGIHAN</small>
                            <h4 class="fw-bolder mb-0 text-dark">Rp 1.700.000</h4>
                        </div>
                        <div class="d-flex gap-2">
                            <!-- Tombol ini muncul jika butuh upload bukti TF -->
                            <button class="btn btn-outline-primary px-4 fw-semibold"><i class="bi bi-cloud-arrow-up me-1"></i> Upload Bukti Transfer</button>
                            <button class="btn btn-secondary px-4 fw-semibold" disabled><i class="bi bi-download me-1"></i> Unduh E-Tiket</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>