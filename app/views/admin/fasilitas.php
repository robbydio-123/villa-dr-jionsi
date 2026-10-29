<div class="admin-content">
    
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill"><i class="bi bi-stars me-1"></i> MASTER DATA INVENTORY</span>
                <small class="text-muted fw-bold" style="font-size: 0.7rem;">Batu Highland Retreat</small>
            </div>
            <h3 class="fw-bolder mb-1">Master Fasilitas</h3>
            <p class="text-muted small mb-0">Kelola daftar fasilitas yang tersedia untuk unit kamar maupun area umum villa.</p>
            <small class="text-muted" style="font-size: 0.7rem;">Data terhubung otomatis ke landing page publik dan voucher reservasi tamu.</small>
        </div>
        <div class="d-flex gap-2 text-md-end mt-2 mt-md-0">
            <button class="btn btn-light border fw-semibold"><i class="bi bi-download me-1"></i> Unduh Laporan</button>
            <button class="btn btn-brown fw-bold"><i class="bi bi-plus me-1"></i> Tambah Fasilitas</button>
        </div>
    </div>

    <!-- 4 Kartu Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card border-secondary-subtle py-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">TOTAL FASILITAS</small>
                    <i class="bi bi-box text-brown fs-5"></i>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bolder mb-0">24</h2>
                    <span class="text-muted small mb-1"><i class="bi bi-check2-all text-success"></i> Siap Digunakan</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-secondary-subtle py-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">FASILITAS KAMAR</small>
                    <i class="bi bi-door-open text-brown fs-5"></i>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bolder mb-0 text-brown">14</h2>
                    <span class="text-muted small mb-1" style="font-size: 0.65rem;">58% dari total item</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-secondary-subtle py-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">AREA UMUM</small>
                    <i class="bi bi-tree text-success fs-5"></i>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bolder mb-0">7</h2>
                    <span class="text-muted small mb-1" style="font-size: 0.65rem;">29% dari total item</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-secondary-subtle py-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">LAYANAN TAMBAHAN</small>
                    <i class="bi bi-bell text-primary fs-5"></i>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bolder mb-0">3</h2>
                    <span class="text-muted small mb-1" style="font-size: 0.65rem;">13% opsional berbayar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-white border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
            <input type="text" class="form-control bg-white border-secondary-subtle" placeholder="Cari nama fasilitas...">
        </div>
        <div class="d-flex gap-2">
            <select class="form-select bg-white border-secondary-subtle text-muted" style="min-width: 150px;">
                <option>Kategori: Semua</option>
                <option>Fasilitas Kamar</option>
                <option>Area Umum</option>
            </select>
            <select class="form-select bg-white border-secondary-subtle text-muted" style="min-width: 130px;">
                <option>Status: Semua</option>
                <option>Aktif</option>
                <option>Nonaktif</option>
            </select>
            <button class="btn btn-light border"><i class="bi bi-funnel"></i></button>
        </div>
    </div>

    <!-- Tabel Data Fasilitas -->
    <div class="table-responsive bg-white rounded-3 shadow-sm border mb-4">
        <table class="admin-table">
            <thead class="bg-light">
                <tr>
                    <th class="text-center" style="width: 60px;">Ikon</th>
                    <th>Nama Fasilitas</th>
                    <th>Kategori</th>
                    <th>Deskripsi Singkat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Item 1 -->
                <tr>
                    <td class="text-center"><div class="bg-light text-brown p-2 rounded d-inline-block"><i class="bi bi-wifi fs-5"></i></div></td>
                    <td><strong class="text-dark">Wi-Fi Kecepatan Tinggi</strong></td>
                    <td><span class="text-danger small fw-semibold">Fasilitas Kamar</span></td>
                    <td class="text-muted small">Koneksi internet stabil hingga 50 Mbps.</td>
                    <td><span class="badge bg-light text-dark border rounded-pill py-1 px-3 fw-normal"><i class="bi bi-circle-fill text-success me-1" style="font-size:0.4rem;"></i> Aktif</span></td>
                    <td>
                        <button class="btn btn-sm text-muted"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm text-muted"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <!-- Item 2 -->
                <tr>
                    <td class="text-center"><div class="bg-light text-brown p-2 rounded d-inline-block"><i class="bi bi-cup-hot fs-5"></i></div></td>
                    <td><strong class="text-dark">Sarapan Pagi</strong></td>
                    <td><span class="text-success small fw-semibold">Area Umum</span></td>
                    <td class="text-muted small">Sarapan prasmanan di area resto utama.</td>
                    <td><span class="badge bg-light text-dark border rounded-pill py-1 px-3 fw-normal"><i class="bi bi-circle-fill text-success me-1" style="font-size:0.4rem;"></i> Aktif</span></td>
                    <td>
                        <button class="btn btn-sm text-muted"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm text-muted"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <!-- Item 3 -->
                <tr>
                    <td class="text-center"><div class="bg-light text-brown p-2 rounded d-inline-block"><i class="bi bi-droplet-half fs-5"></i></div></td>
                    <td><strong class="text-dark">Pemanas Air / Water Heater</strong></td>
                    <td><span class="text-danger small fw-semibold">Fasilitas Kamar</span></td>
                    <td class="text-muted small">Pemanas air gas otomatis 24 jam di seluruh kamar mandi.</td>
                    <td><span class="badge bg-light text-dark border rounded-pill py-1 px-3 fw-normal"><i class="bi bi-circle-fill text-success me-1" style="font-size:0.4rem;"></i> Aktif</span></td>
                    <td>
                        <button class="btn btn-sm text-muted"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm text-muted"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <!-- Item 4 (Nonaktif Example) -->
                <tr>
                    <td class="text-center"><div class="bg-light text-muted p-2 rounded d-inline-block"><i class="bi bi-car-front fs-5"></i></div></td>
                    <td><strong class="text-dark">Antar-Jemput Stasiun / Bandara</strong></td>
                    <td><span class="text-primary small fw-semibold">Layanan Tambahan</span></td>
                    <td class="text-muted small">Layanan transportasi Songgokerto - Malang PP.</td>
                    <td><span class="badge bg-light text-muted border rounded-pill py-1 px-3 fw-normal"><i class="bi bi-circle-fill text-secondary me-1" style="font-size:0.4rem;"></i> Nonaktif</span></td>
                    <td>
                        <button class="btn btn-sm text-muted"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm text-muted"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav class="d-flex justify-content-end">
        <ul class="pagination pagination-sm">
            <li class="page-item disabled"><a class="page-link text-muted" href="#"><i class="bi bi-chevron-left"></i></a></li>
            <li class="page-item active"><a class="page-link bg-brown border-brown" href="#">1</a></li>
            <li class="page-item"><a class="page-link text-dark" href="#">2</a></li>
            <li class="page-item"><a class="page-link text-dark" href="#">3</a></li>
            <li class="page-item"><a class="page-link text-dark" href="#"><i class="bi bi-chevron-right"></i></a></li>
        </ul>
    </nav>
</div>