<section class="container mt-4 mb-5">
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Beranda</a></li>
                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Cek Status Reservasi</li>
            </ol>
        </nav>
        <span class="badge bg-light text-muted border rounded-pill fw-normal" style="font-size: 0.7rem;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.4rem;"></i> TRANSPARANSI RESERVASI MANDIRI</span>
    </div>

    <!-- Kotak Pencarian Utama -->
    <div class="card border-0 shadow-sm rounded-4 mx-auto mb-4" style="max-width: 600px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex justify-content-center align-items-center bg-brown bg-opacity-10 text-brown rounded-circle mb-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
                <h3 class="fw-bolder">Cek Status Reservasi Anda</h3>
                <p class="text-muted small">Masukkan kode referensi untuk melihat status booking</p>
            </div>

            <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= h($error) ?></div><?php endif; ?>
            <form action="hasil_status.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <label class="form-label small fw-bold text-dark mb-0">Kode Referensi</label>
                        <small class="text-muted" style="font-size: 0.65rem;"><i class="bi bi-ticket-detailed me-1"></i> Format resmi Villa</small>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control border-secondary-subtle" name="kode" placeholder="Contoh: DRJ-20261026-K201-7QF4" maxlength="50" required>
                        <span class="input-group-text bg-white border-secondary-subtle text-muted"><i class="bi bi-ticket"></i></span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <label class="form-label small fw-bold text-dark mb-0">4 digit terakhir nomor WhatsApp</label>
                        <small class="text-muted" style="font-size: 0.65rem;">Verifikasi Keamanan</small>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control border-secondary-subtle" name="phone" placeholder="Contoh: 9081" required maxlength="4">
                        <span class="input-group-text bg-white border-secondary-subtle text-muted"><i class="bi bi-shield-check"></i></span>
                    </div>
                </div>

                <p class="text-muted mb-4" style="font-size: 0.7rem;">
                    <i class="bi bi-lock-fill me-1"></i> Digunakan untuk memvalidasi kepemilikan reservasi Anda secara aman tanpa kata sandi.
                </p>

                <button type="submit" class="btn btn-brown w-100 fw-bold py-2 rounded-pill"><i class="bi bi-search me-2"></i> Cari Reservasi</button>
            </form>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top small">
                <span class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-info-circle me-1"></i> Kode dikirim ke WhatsApp Anda setelah bukti pembayaran diunggah</span>
                <a href="#" class="text-brown text-decoration-none fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-whatsapp me-1"></i> Lupa kode? Hubungi pengelola</a>
            </div>
        </div>
    </div>

    <!-- Informasi Fitur Tambahan (Bawah Form) -->
    <div class="row g-3 mx-auto mb-4" style="max-width: 800px;">
        <div class="col-md-4">
            <div class="border rounded p-3 bg-white h-100 shadow-sm">
                <h6 class="fw-bold small mb-2 text-brown"><i class="bi bi-check2-square me-1"></i> Pencarian Real-Time</h6>
                <p class="small text-muted mb-0" style="font-size: 0.75rem;">Terkoneksi langsung ke sistem pengelolaan kamar Villa D-R Jionsi.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3 bg-white h-100 shadow-sm">
                <h6 class="fw-bold small mb-2 text-brown"><i class="bi bi-shield-lock me-1"></i> Privasi Terjaga</h6>
                <p class="small text-muted mb-0" style="font-size: 0.75rem;">Verifikasi 4 digit nomor WhatsApp menjaga kerahasiaan data tamu.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3 bg-white h-100 shadow-sm">
                <h6 class="fw-bold small mb-2 text-brown"><i class="bi bi-headset me-1"></i> Bantuan Cepat</h6>
                <p class="small text-muted mb-0" style="font-size: 0.75rem;">Tim operasional siap membantu setiap hari pukul 08.00 - 21.00 WIB.</p>
            </div>
        </div>
    </div>
    
    <!-- Banner Standar Villa -->
    <div class="mx-auto" style="max-width: 800px;">
        <div class="bg-light border rounded p-3 d-flex justify-content-between align-items-center shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted"><i class="bi bi-shield-check fs-4"></i></div>
                <div>
                    <h6 class="fw-bold small mb-0">Jaminan Transparansi Kamar Villa</h6>
                    <small class="text-muted" style="font-size: 0.7rem;">Foto fisik aktual tanpa rekayasa sudut lensa atau filter warna</small>
                </div>
            </div>
            <a href="profil.php" class="text-danger text-decoration-none small fw-bold">Pelajari Standar Villa <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>