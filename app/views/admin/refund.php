<div class="admin-content">
 <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill"><i class="bi bi-arrow-counterclockwise me-1"></i> MANAJEMEN PEMBATALAN</span>
            </div>
            <h3 class="fw-bolder mb-1">Pengajuan Refund</h3>
            <p class="text-muted small mb-0">Kelola dan verifikasi permintaan pembatalan dan pengembalian dana dari tamu.</p>
        </div>
        <div class="d-flex gap-2 text-md-end mt-2 mt-md-0">
            <button class="btn btn-light border fw-semibold"><i class="bi bi-download me-1"></i> Unduh Rekap</button>
        </div>
    </div>

    <!-- 3 Kartu Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card border-warning-subtle py-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">MENUNGGU TINJAUAN</small>
                    <i class="bi bi-hourglass-split text-warning fs-5"></i>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bolder mb-0 text-brown">1</h2>
                    <span class="text-muted small mb-1">Pengajuan Baru</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-success-subtle py-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">REFUND SELESAI</small>
                    <i class="bi bi-check-circle text-success fs-5"></i>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bolder mb-0 text-success">12</h2>
                    <span class="text-muted small mb-1">Bulan Ini</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-danger-subtle py-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <small class="text-muted fw-bold" style="font-size: 0.7rem;">DITOLAK / HANGUS</small>
                    <i class="bi bi-x-circle text-danger fs-5"></i>
                </div>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-bolder mb-0 text-danger">3</h2>
                    <span class="text-muted small mb-1">Lewat Batas H-1</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-secondary-subtle" placeholder="Cari kode booking atau nama tamu...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select bg-light border-secondary-subtle text-muted">
                        <option>Status: Menunggu Tinjauan</option>
                        <option>Status: Selesai Ditransfer</option>
                        <option>Status: Ditolak</option>
                        <option>Semua Status</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-light border-secondary-subtle w-100 text-muted"><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Refund -->
    <div class="table-responsive bg-white rounded-3 shadow-sm border mb-4">
        <table class="admin-table">
            <thead class="bg-light">
                <tr>
                    <th>Info Tiket & Kode Booking</th>
                    <th>Detail Tamu</th>
                    <th>Alasan Pembatalan</th>
                    <th>Nominal Refund</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Item 1: Menunggu -->
                <tr>
                    <td>
                        <strong class="text-danger d-block">RFD-20261026-004</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">Booking: DRJ-20261026-K103</small>
                        <small class="text-muted d-block mt-1" style="font-size: 0.65rem;"><i class="bi bi-clock"></i> Diajukan: 26 Okt, 09.30 WIB</small>
                    </td>
                    <td>
                        <strong class="d-block text-dark small">Budi Santoso</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">+62 812-3344-5566</small>
                    </td>
                    <td style="max-width: 200px;">
                        <span class="d-block text-truncate small text-muted" title="Keluarga mendadak sakit dan harus dirawat.">Keluarga mendadak sakit dan harus dirawat.</span>
                        <span class="badge bg-light text-dark border mt-1" style="font-size: 0.6rem;"><i class="bi bi-paperclip me-1"></i> Ada Lampiran</span>
                    </td>
                    <td>
                        <strong class="d-block text-brown">Rp 650.000</strong>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">BCA - 0987654321</small>
                    </td>
                    <td><span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill py-1 px-3"><i class="bi bi-hourglass-split text-warning me-1" style="font-size:0.5rem;"></i> Menunggu Tinjauan</span></td>
                    <td>
                        <button class="btn btn-sm btn-brown fw-bold" style="font-size: 0.7rem;">Tinjau</button>
                    </td>
                </tr>

                <!-- Item 2: Selesai -->
                <tr>
                    <td>
                        <strong class="text-dark d-block">RFD-20261022-003</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">Booking: DRJ-20261019-K201</small>
                        <small class="text-muted d-block mt-1" style="font-size: 0.65rem;"><i class="bi bi-check-all"></i> Selesai: 22 Okt, 14.00 WIB</small>
                    </td>
                    <td>
                        <strong class="d-block text-dark small">Siti Aminah</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">+62 813-9988-7766</small>
                    </td>
                    <td style="max-width: 200px;">
                        <span class="d-block text-truncate small text-muted">Perubahan jadwal cuti kerja dari kantor.</span>
                    </td>
                    <td>
                        <strong class="d-block text-muted">Rp 1.700.000</strong>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Mandiri - 1122334455</small>
                    </td>
                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill py-1 px-3"><i class="bi bi-check-circle-fill text-success me-1" style="font-size:0.5rem;"></i> Selesai Ditransfer</span></td>
                    <td>
                        <button class="btn btn-sm text-muted"><i class="bi bi-eye"></i></button>
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
            <li class="page-item"><a class="page-link text-dark" href="#"><i class="bi bi-chevron-right"></i></a></li>
        </ul>
    </nav>
</div>