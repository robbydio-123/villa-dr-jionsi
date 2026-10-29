<div class="container mb-5">
    <!-- Bagian Hero Image -->
    <div class="hero-section" style="background-image: url('data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="badge bg-secondary bg-opacity-50 text-light mb-2 rounded-pill px-3 py-2"><i class="bi bi-geo-alt-fill me-1 text-warning"></i> SONGGOKERTO HIGHLAND · KOTA BATU</span>
            <h1 class="display-4 fw-bolder mb-3">Villa D-R Jionsi</h1>
            <p class="lead mb-4 fw-light" style="max-width: 600px;">Menginap nyaman di Batu, lihat kondisi kamar yang sebenarnya.</p>
            <div class="d-flex flex-wrap gap-3 mb-4">
                <span class="bg-white bg-opacity-10 px-3 py-2 rounded border border-secondary border-opacity-50 small"><i class="bi bi-droplet-half text-brown me-2"></i> Air Panas di setiap kamar</span>
                <span class="bg-white bg-opacity-10 px-3 py-2 rounded border border-secondary border-opacity-50 small"><i class="bi bi-tv text-brown me-2"></i> Smart TV di setiap kamar</span>
            </div>
            <a href="#" class="btn btn-brown px-4 py-2 fw-semibold rounded-pill">Lihat Profil Villa <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>

    <!-- Form Pencarian Melayang -->
    <div class="card p-3 shadow-sm border-0 bg-white floating-search-form mb-4">
        <form class="row g-3 align-items-end">

            <!-- BAGIAN YANG DIUBAH: Waktu Check-in -->
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-calendar-event me-1"></i> WAKTU CHECK-IN</label>
                <input type="datetime-local" class="form-control form-control-sm border-secondary-subtle" required>
            </div>

            <!-- BAGIAN YANG DIUBAH: Durasi Sewa -->
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-stopwatch me-1"></i> DURASI</label>
                <select class="form-select form-select-sm border-secondary-subtle" required>
                    <option value="" selected disabled>Pilih Jam</option>
                    <option value="3">3 Jam (Transit)</option>
                    <option value="6">6 Jam (Half Day)</option>
                    <option value="12">12 Jam</option>
                    <option value="24">24 Jam (Full Day)</option>
                </select>
            </div>

            <!-- BAGIAN KAPASITAS (Tetap) -->
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-people me-1"></i> KAPASITAS</label>
                <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary" type="button">-</button>
                    <input type="text" class="form-control text-center" value="2 Tamu" readonly>
                    <button class="btn btn-outline-secondary" type="button">+</button>
                </div>
            </div>

            <!-- BAGIAN TARIF (Tetap) -->
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-wallet2 me-1"></i> RENTANG TARIF / MALAM</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">MIN</span>
                    <input type="text" class="form-control" placeholder="Rp 400.000">
                    <span class="input-group-text bg-light">MAKS</span>
                    <input type="text" class="form-control" placeholder="Rp 900.000">
                </div>
            </div>

            <!-- BAGIAN TOMBOL CARI (Tetap) -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-brown btn-sm w-100 fw-bold py-2"><i class="bi bi-search me-1"></i> Cari Kamar</button>
            </div>
        </form>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex flex-wrap align-items-center gap-3 mb-5 mt-3 small text-muted">
            <span class="fw-bold">Fasilitas:</span>
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" checked id="fas1">
                <label class="form-check-label" for="fas1">Air Panas</label>
            </div>
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" checked id="fas2">
                <label class="form-check-label" for="fas2">Smart TV</label>
            </div>
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" checked id="fas3">
                <label class="form-check-label" for="fas3">Wi-Fi</label>
            </div>
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" checked id="fas4">
                <label class="form-check-label" for="fas4">Kamar Mandi Dalam</label>
            </div>
        </div>
    </div>

    <hr class="text-muted opacity-25 my-3">
</div>

<!-- BUNGKUS DENGAN CONTAINER AGAR KE TENGAH & TIDAK FULL -->
<section class="container mt-5 mb-5">

    <!-- Judul Katalog -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h4 class="fw-bolder mb-0">Katalog Pilihan Kamar</h4>
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 border border-success border-opacity-25 small"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> 6 Unit Siap Huni</span>
        </div>
        <div class="d-flex align-items-center gap-2 small">
            <span class="text-muted text-nowrap">Urutkan:</span>
            <select class="form-select form-select-sm border-secondary-subtle fw-semibold" style="min-width: 150px;">
                <option>Harga Terendah</option>
            </select>
        </div>
    </div>

    <!-- Grid Daftar Kamar -->
    <div class="row g-4">

        <!-- Kamar 201 (Status Di-Keep) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="position-relative">
                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="w-100" style="height: 220px; object-fit: cover; background-color: #E5E0D8;">
                    <span class="badge-status bg-dikeep" style="position: absolute; top: 15px; left: 15px; background: #fd7e14; color: white; padding: 5px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: bold;"><i class="bi bi-clock-history me-1"></i> Di-Pesan - 09:42</span>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted fw-bold" style="font-size: 0.65rem;">KAMAR 201 • LANTAI 2</small>
                        <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i> Maks 6 tamu</span>
                    </div>
                    <h5 class="fw-bolder mb-3">Family Suite Panderman</h5>
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi bi-wifi"></i> Wi-Fi</span>
                        <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi bi-tv"></i> Smart TV</span>
                        <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi bi-droplet-half"></i> Air Panas</span>
                        <span class="facility-tag bg-light border rounded px-2 py-1 small text-muted">+2</span>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded small fw-semibold mb-3 border border-success border-opacity-25 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> Kondisi diperbarui 3 hari lalu
                    </div>
                    <div class="d-flex flex-column gap-3 mt-auto">
                        <div class="d-flex justify-content-between align-items-end">
                            <small class="text-muted">Tarif Sewa:</small>
                            <div class="text-end">
                                <h5 class="fw-bolder text-brown mb-0">Rp 850.000 <span class="fs-6 fw-normal text-muted">/ malam</span></h5>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="detail.php" class="btn btn-outline-brown flex-fill fw-bold rounded-pill border-brown text-brown">Lihat Detail</a>
                            <button class="btn btn-light border flex-fill fw-bold rounded-pill text-muted" disabled><i class="bi bi-slash-circle me-1"></i> Sedang Di-Pesan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kamar 101 (Status Tersedia) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="position-relative">
                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="w-100" style="height: 220px; object-fit: cover; background-color: #E5E0D8;">
                    <span class="badge-status bg-tersedia" style="position: absolute; top: 15px; left: 15px; background: #198754; color: white; padding: 5px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: bold;"><i class="bi bi-check-circle-fill me-1"></i> Tersedia</span>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted fw-bold" style="font-size: 0.65rem;">KAMAR 101 • LANTAI 1</small>
                        <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i> Maks 2 tamu</span>
                    </div>
                    <h5 class="fw-bolder mb-3">Deluxe Arjuno</h5>
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi bi-droplet-half"></i> Air Panas</span>
                        <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi bi-tv"></i> Smart TV</span>
                        <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi bi-door-closed"></i> KM Dalam</span>
                        <span class="facility-tag bg-light border rounded px-2 py-1 small text-muted">+1</span>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded small fw-semibold mb-3 border border-success border-opacity-25 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> Kondisi diperbarui 1 hari lalu
                    </div>
                    <div class="d-flex flex-column gap-3 mt-auto">
                        <div class="d-flex justify-content-between align-items-end">
                            <small class="text-muted">Tarif Sewa:</small>
                            <div class="text-end">
                                <h5 class="fw-bolder text-brown mb-0">Rp 450.000 <span class="fs-6 fw-normal text-muted">/ malam</span></h5>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="detail.php" class="btn btn-outline-brown flex-fill fw-bold rounded-pill border-brown text-brown">Lihat Detail</a>
                            <a href="formulir.php" class="btn btn-brown flex-fill fw-bold rounded-pill bg-brown text-white">Pesan Kamar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kamar 102 (Status Terisi) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden opacity-75">
                <div class="position-relative">
                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="w-100" style="height: 220px; object-fit: cover; background-color: #E5E0D8;">
                    <span class="badge-status bg-terisi" style="position: absolute; top: 15px; left: 15px; background: #dc3545; color: white; padding: 5px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: bold;"><i class="bi bi-lock-fill me-1"></i> Terisi</span>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted fw-bold" style="font-size: 0.65rem;">KAMAR 102 • LANTAI 1</small>
                        <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i> Maks 3 tamu</span>
                    </div>
                    <h5 class="fw-bolder mb-3">Twin Welirang</h5>
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi bi-droplet-half"></i> Air Panas</span>
                        <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi bi-tv"></i> Smart TV</span>
                        <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi bi-cup-hot"></i> Dapur Bersama</span>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded small fw-semibold mb-3 border border-success border-opacity-25 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> Kondisi diperbarui 2 hari lalu
                    </div>
                    <div class="d-flex flex-column gap-3 mt-auto">
                        <div class="d-flex justify-content-between align-items-end">
                            <small class="text-muted">Tarif Sewa:</small>
                            <div class="text-end">
                                <h5 class="fw-bolder text-brown mb-0">Rp 550.000 <span class="fs-6 fw-normal text-muted">/ malam</span></h5>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="detail.php" class="btn btn-outline-brown flex-fill fw-bold rounded-pill border-brown text-brown">Lihat Detail</a>
                            <button class="btn btn-light border flex-fill fw-bold rounded-pill text-muted" disabled><i class="bi bi-slash-circle me-1"></i> Terisi s.d 28 Okt</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
