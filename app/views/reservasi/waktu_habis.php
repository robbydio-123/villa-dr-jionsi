<section class="container mt-4 mb-5" style="max-width: 850px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="beranda.php" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="formulir.php" class="text-decoration-none text-muted">Formulir Reservasi</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><span class="text-muted fw-normal me-1">/</span> Waktu Keep Berakhir</li>
        </ol>
    </nav>

    <!-- Card Tahapan Reservasi -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="form-stepper">
                <div class="step-item active">
                    <div class="step-circle bg-success text-white border-0"><i class="bi bi-check-lg fs-5"></i></div>
                    <span class="title text-success">Data Tamu</span>
                    <span class="subtitle fw-semibold mt-1">Selesai</span>
                </div>
                <div class="step-item active">
                    <div class="step-circle bg-warning text-brown border-0"><i class="bi bi-hourglass-bottom fs-5"></i></div>
                    <span class="title text-brown">Keep & Pembayaran</span>
                    <span class="subtitle text-brown fw-semibold mt-1">Sesi Habis</span>
                </div>
                <div class="step-item">
                    <div class="step-circle">3</div>
                    <span class="title">Konfirmasi</span>
                    <span class="subtitle mt-1">Belum Aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Konten Utama Sesi Habis -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4 p-md-5 text-center">
            
            <div class="expired-icon-wrapper text-brown">
                <i class="bi bi-hourglass-bottom fs-1"></i>
            </div>
            
            <div class="d-inline-block mb-3">
                <span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill px-3 py-2"><i class="bi bi-circle-fill text-warning me-1" style="font-size: 0.5rem;"></i> Status: Sesi Keep Kedaluwarsa (15 Menit Berakhir)</span>
            </div>
            
            <h2 class="fw-bolder mb-3">Waktu keep kamar telah berakhir</h2>
            <p class="text-muted mb-5 mx-auto" style="max-width: 500px;">Kamar 201 Family Suite Panderman sudah dilepas kembali ke katalog agar bisa dipesan tamu lain.</p>

            <!-- Detail Kamar yang Dilepas -->
            <div class="card-released rounded p-3 text-start mx-auto mb-4" style="max-width: 600px;">
                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">DETAIL KAMAR YANG DILEPAS</small>
                    <span class="badge bg-light text-muted border"><i class="bi bi-unlock me-1"></i> Unit Terbuka Publik</span>
                </div>
                
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center">
                    <div class="position-relative" style="width: 100px; height: 80px;">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="rounded w-100 h-100" style="object-fit: cover; background-color: #E5E0D8;">
                        <span class="badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 start-0 m-1" style="font-size: 0.6rem;">201</span>
                    </div>
                    
                    <div class="flex-fill">
                        <h6 class="fw-bold mb-1">Kamar 201 - Family Suite</h6>
                        <small class="text-muted d-block" style="font-size: 0.7rem;"><i class="bi bi-people me-1"></i> 4 Tamu • 2 Bed Queen</small>
                        <small class="text-muted d-block" style="font-size: 0.7rem;"><i class="bi bi-layers me-1"></i> Lantai 2 View Gunung</small>
                    </div>
                    
                    <div class="text-md-end mt-2 mt-md-0">
                        <small class="text-muted d-block" style="font-size: 0.7rem;">Tanggal Menginap</small>
                        <span class="fw-semibold small d-block">26 - 28 Okt 2026</span>
                        <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">2 Malam</small>
                        <h6 class="fw-bold text-brown mb-0">Rp 1.500.000</h6>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top bg-danger bg-opacity-10 p-2 rounded">
                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">Ref: <del>DRJ-20261026-K201-7QF4</del></small>
                    <small class="text-danger fw-bold" style="font-size: 0.75rem;"><i class="bi bi-x-circle me-1"></i> Batal Otomatis oleh Sistem</small>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mb-5 mx-auto" style="max-width: 600px;">
                <a href="detail.php" class="btn btn-brown flex-fill fw-bold py-3 rounded-3"><i class="bi bi-arrow-clockwise me-2"></i> Keep Ulang Kamar Ini</a>
                <a href="beranda.php" class="btn btn-light border border-secondary-subtle text-dark flex-fill fw-bold py-3 rounded-3"><i class="bi bi-grid me-2"></i> Kembali ke Katalog</a>
            </div>

            <!-- Box Bantuan WhatsApp -->
            <div class="bg-success bg-opacity-10 border border-success-subtle rounded p-4 text-start mx-auto" style="max-width: 600px;">
                <div class="d-flex gap-3 align-items-start">
                    <div class="bg-white p-2 rounded-circle text-success shadow-sm"><i class="bi bi-whatsapp fs-5"></i></div>
                    <div>
                        <h6 class="fw-bold text-success mb-1">Sudah transfer? Hubungi pengelola via WhatsApp dengan bukti transfer.</h6>
                        <p class="small text-muted mb-3" style="font-size: 0.8rem;">Jika dana Anda sudah terdebit dari rekening atau e-wallet saat sesi berakhir, tim admin kami akan segera membantu verifikasi manual atau menjadwalkan ulang unit kamar untuk Anda.</p>
                        <button class="btn btn-success btn-sm fw-bold px-3 py-2"><i class="bi bi-whatsapp me-1"></i> Hubungi Pengelola via WhatsApp (+62 812-3344-9081)</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>