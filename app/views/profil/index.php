<section class="container mt-4 mb-5">
    <?php $fotoProfilVilla = profile_photo_url(); ?>
    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-4">
            <li class="breadcrumb-item"><a href="beranda.php" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item active fw-semibold" aria-current="page" style="color: var(--drj-primary);">Profil & Fasilitas</li>
        </ol>
    </nav>

    <div class="row align-items-center mb-5 pb-4 border-bottom">
        <div class="col-lg-6 pe-lg-5 mb-4 mb-lg-0">
            <h6 class="text-muted fw-bold mb-2" style="letter-spacing: 0.5px;">TENTANG KAMI</h6>
            <h2 class="fw-bolder mb-3" style="color: #333333; font-size: calc(1.1rem + 1.5vw);">Highland Sanctuary Kota Batu</h2>            
            <p class="text-muted" style="line-height: 1.8; font-size: 1.05rem;">
                Villa D-R Jionsi mengusung konsep <em>alpine modern</em> yang berpadu dengan sejuknya lereng Gunung Panderman, Songgokerto. Kami bukan sekadar tempat menginap, melainkan ruang peristirahatan yang tenang dari hiruk-pikuk perkotaan.
            </p>
            <p class="text-muted" style="line-height: 1.8; font-size: 1.05rem;">
                Kami menjunjung tinggi <strong>Transparansi Penuh</strong>. Seluruh dokumentasi yang Anda lihat di katalog adalah kondisi nyata (<em>unedited</em>), diperbarui secara berkala oleh pengelola lokal kami. Tidak ada manipulasi ekspektasi, hanya kenyamanan otentik untuk keluarga Anda.
            </p>
            <a href="beranda.php" class="btn btn-brown px-4 py-2 fw-semibold">
                Lihat Beranda &amp; Katalog Kamar <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="col-lg-6">
            <div class="position-relative">
                <img src="<?= h($fotoProfilVilla) ?>" class="w-100 rounded-3 shadow-sm" style="height: 400px; object-fit: cover; background-color: #E5E0D8;" alt="Foto Villa D-R Jionsi">
                <span class="badge bg-white text-dark position-absolute bottom-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm fw-semibold"><i class="bi bi-camera-fill me-1 text-muted"></i> Villa D-R Jionsi</span>
            </div>
        </div>
    </div>

    <!-- FASILITAS BERSAMA (Di luar kamar) -->
    <div class="mb-5">
        <h3 class="fw-bolder mb-4 text-center">Fasilitas Bersama Tamu</h3>
        <?php $fasUmum = fasilitas_publik('Area Umum'); ?>
        <?php if ($fasUmum): ?>
        <!-- Dari Master Fasilitas (admin): kategori Area Umum, status Aktif -->
        <div class="row g-4 justify-content-center">
            <?php foreach ($fasUmum as $fas): ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm bg-transparansi p-4 text-center">
                    <i class="bi <?= h($fas['ikon']) ?> mb-3" style="font-size: 2.5rem; color: var(--drj-primary);"></i>
                    <h5 class="fw-bold"><?= h($fas['nama']) ?></h5>
                    <p class="small text-muted mb-0"><?= h($fas['deskripsi'] ?? '') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- Cadangan: tampil bila tabel fasilitas belum ada atau belum ada Area Umum yang aktif -->
        <div class="row g-4">
            <!-- Fasilitas 1 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm bg-transparansi p-4 text-center">
                    <i class="bi bi-car-front-fill mb-3" style="font-size: 2.5rem; color: var(--drj-primary);"></i>
                    <h5 class="fw-bold">Area Parkir Aman</h5>
                    <p class="small text-muted mb-0">Lahan parkir luas di dalam pagar villa, cukup untuk 6-8 mobil keluarga dengan pengawasan CCTV 24 jam.</p>
                </div>
            </div>
            <!-- Fasilitas 2 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm bg-transparansi p-4 text-center">
                    <i class="bi bi-cup-hot-fill mb-3" style="font-size: 2.5rem; color: var(--drj-primary);"></i>
                    <h5 class="fw-bold">Dapur & Ruang Makan</h5>
                    <p class="small text-muted mb-0">Dapur bersama yang dilengkapi kompor, kulkas besar, dispenser, dan peralatan makan untuk kebutuhan ringan tamu.</p>
                </div>
            </div>
            <!-- Fasilitas 3 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm bg-transparansi p-4 text-center">
                    <i class="bi bi-tree-fill mb-3" style="font-size: 2.5rem; color: var(--drj-primary);"></i>
                    <h5 class="fw-bold">Area Santai Terbuka</h5>
                    <p class="small text-muted mb-0">Taman kecil dan bangku santai di lantai dasar untuk menikmati udara pegunungan atau berkumpul santai.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- LOKASI & PETA -->
    <div class="card border-0 shadow-sm overflow-hidden mb-4">
        <div class="row g-0">
            <div class="col-md-5 p-4 p-lg-5 bg-white d-flex flex-column justify-content-center">
                <h4 class="fw-bold mb-3">Lokasi Kami</h4>
                <p class="text-muted small mb-4">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> Jl. Jeruk No. 21, Songgokerto, Kec. Batu, Kota Batu, Jawa Timur 65312
                </p>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 10 menit ke Alun-Alun Batu</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 15 menit ke Jatim Park 1 & 2</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> 5 menit ke Paralayang (Lewat jalur pintas)</li>
                </ul>
            </div>
            <div class="col-md-7">
                <iframe
                    class="w-100 d-block"
                    style="height: 320px; border: 0;"
                    src="https://www.google.com/maps?q=Jl.%20Jeruk%20No.%2021%2C%20Songgokerto%2C%20Kec.%20Batu%2C%20Kota%20Batu%2C%20Jawa%20Timur%2065312&output=embed"
                    title="Google Maps - Jl. Jeruk No. 21, Songgokerto, Kota Batu"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</section>