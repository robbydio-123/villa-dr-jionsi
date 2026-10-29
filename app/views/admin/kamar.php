<div class="admin-content">
    
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill"><i class="bi bi-box me-1"></i> INVENTARIS & PROPERTI</span>
                <small class="text-muted fw-bold" style="font-size: 0.7rem;">Songgokerto, Batu Highland</small>
            </div>
            <h3 class="fw-bolder mb-1">Manajemen Kamar</h3>
            <p class="text-muted small mb-0">Kelola inventaris unit kamar, harga, fasilitas, dan status ketersediaan secara real-time.</p>
        </div>
        <div class="d-flex gap-2 text-md-end">
            <button class="btn btn-light border fw-semibold"><i class="bi bi-download me-1"></i> Unduh Laporan</button>
            <button class="btn btn-brown fw-bold"><i class="bi bi-plus me-1"></i> Tambah Kamar</button>
        </div>
    </div>

    <!-- 4 Kartu Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card border-secondary-subtle">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">TOTAL UNIT</small>
                    <div class="bg-light p-2 rounded text-muted"><i class="bi bi-door-open fs-5"></i></div>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h3 class="fw-bolder mb-0">8</h3>
                    <span class="text-muted small mb-1">Kamar</span>
                </div>
                <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;"><i class="bi bi-check-circle me-1"></i> 100% Terdaftar</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-success-subtle">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">TERSEDIA</small>
                    <div class="bg-success bg-opacity-10 p-2 rounded text-success"><i class="bi bi-check-circle fs-5"></i></div>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h3 class="fw-bolder mb-0 text-success">3</h3>
                    <span class="text-muted small mb-1">Unit Siap</span>
                </div>
                <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.4rem;"></i> Siap Huni Tamu</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-warning-subtle">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">TERISI</small>
                    <div class="bg-warning bg-opacity-10 p-2 rounded text-brown"><i class="bi bi-key fs-5"></i></div>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h3 class="fw-bolder mb-0 text-brown">4</h3>
                    <span class="text-muted small mb-1">Unit Aktif</span>
                </div>
                <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;"><i class="bi bi-circle-fill text-warning me-1" style="font-size: 0.4rem;"></i> Sedang Menginap / Di-keep</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-danger-subtle bg-danger bg-opacity-10">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-danger fw-bold" style="font-size: 0.7rem;">DALAM PERBAIKAN</small>
                    <div class="bg-white p-2 rounded text-danger border border-danger-subtle"><i class="bi bi-tools fs-5"></i></div>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h3 class="fw-bolder mb-0 text-danger">1</h3>
                    <span class="text-danger small mb-1">Maintenance</span>
                </div>
                <small class="text-danger mt-2 d-block fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-x-circle me-1"></i> Kamar 103 Servis AC</small>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-secondary-subtle" placeholder="Cari nama kamar atau tipe...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select bg-light border-secondary-subtle text-muted">
                        <option>Semua Tipe Kamar</option>
                        <option>Family Suite</option>
                        <option>Deluxe Room</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-secondary-subtle text-muted">
                        <option>Semua Status</option>
                        <option>Tersedia</option>
                        <option>Terisi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-light border-secondary-subtle w-100 text-muted"><i class="bi bi-sort-down me-1"></i> Urutkan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Kamar -->
    <div class="table-responsive bg-white rounded-3 shadow-sm border mb-4">
        <table class="admin-table">
            <thead class="bg-light">
                <tr>
                    <th>Info Kamar</th>
                    <th>Tipe & Kapasitas</th>
                    <th>Harga Per Malam</th>
                    <th>Fasilitas Utama</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Kamar 201 -->
                <tr>
                    <td>
                        <div class="d-flex gap-3 align-items-center">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="rounded" style="width: 50px; height: 50px; object-fit: cover; background-color: #E5E0D8;">
                            <div>
                                <strong class="d-block text-dark">Kamar 201</strong>
                                <small class="text-muted" style="font-size: 0.65rem;">Lantai 2 &bull; Mountain View</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong class="d-block text-dark small">Family Suite</strong>
                        <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.65rem;"><i class="bi bi-people"></i> Kapasitas: 4 Dewasa</small>
                    </td>
                    <td>
                        <strong class="d-block text-brown">Rp 1.700.000</strong>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">/ malam</small>
                        <small class="text-muted" style="font-size: 0.65rem;">(termasuk sarapan)</small>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1 text-muted" style="font-size: 0.65rem;">
                            <span><i class="bi bi-droplet-half me-1"></i> Air Panas</span>
                            <span><i class="bi bi-tv me-1"></i> Smart TV</span>
                            <span><i class="bi bi-wifi me-1"></i> Wi-Fi</span>
                        </div>
                    </td>
                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill py-1 px-3"><i class="bi bi-circle-fill text-success me-1" style="font-size:0.4rem;"></i> Tersedia</span></td>
                    <td>
                        <button class="btn btn-sm text-muted"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm text-muted"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>

                <!-- Kamar 103 -->
                <tr>
                    <td>
                        <div class="d-flex gap-3 align-items-center">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="rounded" style="width: 50px; height: 50px; object-fit: cover; background-color: #E5E0D8;">
                            <div>
                                <strong class="d-block text-dark">Kamar 103</strong>
                                <small class="text-muted" style="font-size: 0.65rem;">Lantai 1 &bull; Courtyard View</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong class="d-block text-dark small">Standard Room</strong>
                        <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.65rem;"><i class="bi bi-people"></i> Kapasitas: 2 Dewasa</small>
                    </td>
                    <td>
                        <strong class="d-block text-brown">Rp 650.000</strong>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">/ malam</small>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1 text-muted" style="font-size: 0.65rem;">
                            <span><i class="bi bi-droplet-half me-1"></i> Air Panas</span>
                            <span><i class="bi bi-wifi me-1"></i> Wi-Fi</span>
                        </div>
                    </td>
                    <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill py-1 px-3"><i class="bi bi-tools text-danger me-1" style="font-size:0.6rem;"></i> Perbaikan</span></td>
                    <td>
                        <button class="btn btn-sm text-muted"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm text-muted"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>

                <!-- Kamar 101 -->
                <tr>
                    <td>
                        <div class="d-flex gap-3 align-items-center">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" class="rounded" style="width: 50px; height: 50px; object-fit: cover; background-color: #E5E0D8;">
                            <div>
                                <strong class="d-block text-dark">Kamar 101</strong>
                                <small class="text-muted" style="font-size: 0.65rem;">Lantai 1 &bull; Garden Terrace</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong class="d-block text-dark small">Deluxe Room</strong>
                        <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.65rem;"><i class="bi bi-people"></i> Kapasitas: 2 Dewasa</small>
                    </td>
                    <td>
                        <strong class="d-block text-brown">Rp 950.000</strong>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">/ malam</small>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1 text-muted" style="font-size: 0.65rem;">
                            <span><i class="bi bi-droplet-half me-1"></i> Air Panas</span>
                            <span><i class="bi bi-snow me-1"></i> AC</span>
                            <span><i class="bi bi-wifi me-1"></i> Wi-Fi</span>
                        </div>
                    </td>
                    <td><span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill py-1 px-3"><i class="bi bi-circle-fill text-warning me-1" style="font-size:0.4rem;"></i> Terisi</span></td>
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