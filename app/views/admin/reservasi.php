<div class="admin-content">
    
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-shield-check text-brown"></i>
                <small class="text-muted fw-bold" style="font-size: 0.7rem;">SISTEM RESERVASI HIGHLAND BATU</small>
            </div>
            <h3 class="fw-bolder mb-1">Manajemen Reservasi Tamu</h3>
            <p class="text-muted small mb-0">Pantau seluruh alur keep, verifikasi bukti transfer, dan status pembayaran kamar secara real-time.</p>
        </div>
        <div class="d-flex flex-column gap-2 text-md-end">
            <div class="d-flex gap-3 bg-white border p-2 rounded shadow-sm">
                <div class="px-2 border-end text-start">
                    <small class="text-muted d-block" style="font-size: 0.65rem;"><i class="bi bi-door-open me-1"></i> Total Unit Aktif</small>
                    <span class="fw-bold">6 Kamar</span>
                </div>
                <div class="px-2 text-start">
                    <small class="text-warning fw-bold d-block" style="font-size: 0.65rem;"><i class="bi bi-exclamation-circle me-1"></i> Perlu Verifikasi</small>
                    <span class="fw-bold text-danger">2 Tamu</span>
                </div>
            </div>
            <button class="btn btn-light border btn-sm fw-semibold"><i class="bi bi-download me-1"></i> Ekspor Laporan (Excel/CSV)</button>
        </div>
    </div>

    <!-- Filter Tab Status -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="#" class="admin-filter-tab active">Semua <span class="badge-count">48</span></a>
        <a href="#" class="admin-filter-tab">Di-Keep <span class="badge-count">1</span></a>
        <a href="#" class="admin-filter-tab border-warning text-warning bg-warning bg-opacity-10"><i class="bi bi-circle-fill" style="font-size:0.4rem;"></i> Menunggu Konfirmasi <span class="badge-count bg-warning text-white">2</span></a>
        <a href="#" class="admin-filter-tab">Dibayar <span class="badge-count">35</span></a>
        <a href="#" class="admin-filter-tab">Kedaluwarsa <span class="badge-count">6</span></a>
        <a href="#" class="admin-filter-tab">Dibatalkan <span class="badge-count">3</span></a>
        <a href="#" class="admin-filter-tab border-danger text-danger bg-danger bg-opacity-10">Refund Diajukan <span class="badge-count bg-danger text-white">1</span></a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-secondary-subtle" placeholder="Cari kode booking, nama tamu, no. WhatsApp...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select bg-light border-secondary-subtle">
                        <option>Semua Unit Kamar</option>
                        <option>Kamar 101 - Deluxe</option>
                        <option>Kamar 201 - Family Suite</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-calendar3 text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-secondary-subtle" value="26 Okt 2026 - 02 Nov 2026">
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-light border-secondary-subtle w-100"><i class="bi bi-sliders"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted" style="font-size: 0.75rem;">Menampilkan reservasi terkini musim highland Kota Batu. <span class="badge bg-light text-dark border ms-2">Auto-sync: Setiap 30 detik</span></small>
        <button class="btn btn-sm text-brown fw-semibold"><i class="bi bi-arrow-clockwise me-1"></i> Reset Urutan</button>
    </div>

    <div class="table-responsive bg-white rounded-3 shadow-sm border mb-4">
        <table class="admin-table">
            <thead class="bg-light">
                <tr>
                    <th>Kode Booking</th>
                    <th>Pemesan</th>
                    <th>WhatsApp</th>
                    <th>Unit Kamar</th>
                    <th>Jadwal Menginap</th>
                    <th>Total Tagihan</th>
                    <th>Keep / Sisa Waktu</th>
                    <th>Status Booking</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris 1: Menunggu Konfirmasi -->
                <tr>
                    <td>
                        <strong class="text-brown d-block">DRJ-20261026-K201</strong>
                        <small class="text-muted">BCA Virtual Account</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initial">BW</div>
                            <div>
                                <strong class="d-block">Budi Wicaksono</strong>
                                <small class="text-muted">2 Tamu Dewasa</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="bi bi-whatsapp me-1"></i> +62 812-7890-1234</span></td>
                    <td>
                        <strong class="d-block">Unit 201 Family Suite</strong>
                        <small class="text-muted">Lantai 2 &bull; Mountain View</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2 small">
                            <div class="text-center"><strong>26</strong><br><span class="text-muted" style="font-size:0.65rem;">Okt</span></div>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <div class="text-center"><strong>28</strong><br><span class="text-muted" style="font-size:0.65rem;">Okt 2026</span></div>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size:0.65rem;">2 Malam</small>
                    </td>
                    <td><strong>Rp 1.700.000</strong></td>
                    <td class="text-center text-muted">-</td>
                    <td><span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill py-2 px-3"><i class="bi bi-circle-fill text-warning me-1" style="font-size:0.4rem;"></i> Menunggu Konfirmasi</span></td>
                </tr>

                <!-- Baris 2: Di-Keep (Kritis) -->
                <tr>
                    <td>
                        <strong class="text-dark d-block">DRJ-20261026-K202</strong>
                        <small class="text-muted">Instant QRIS</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initial">RP</div>
                            <div>
                                <strong class="d-block">Reza Pratama</strong>
                                <small class="text-muted">2 Tamu</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="bi bi-whatsapp me-1"></i> +62 856-1122-3344</span></td>
                    <td>
                        <strong class="d-block">Unit 202 Superior</strong>
                        <small class="text-muted">Lantai 2 &bull; Sunset Deck</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2 small">
                            <div class="text-center"><strong>27</strong><br><span class="text-muted" style="font-size:0.65rem;">Okt</span></div>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <div class="text-center"><strong>29</strong><br><span class="text-muted" style="font-size:0.65rem;">Okt 2026</span></div>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size:0.65rem;">2 Malam</small>
                    </td>
                    <td><strong>Rp 1.700.000</strong></td>
                    <td><span class="badge bg-white text-danger border border-danger-subtle"><i class="bi bi-stopwatch"></i> 02:45 (Kritis)</span></td>
                    <td><span class="badge bg-light text-dark border rounded-pill py-2 px-3"><i class="bi bi-circle-fill text-secondary me-1" style="font-size:0.4rem;"></i> Di-Keep</span></td>
                </tr>

                <!-- Baris 3: Dibayar -->
                <tr>
                    <td>
                        <strong class="text-dark d-block">DRJ-20261025-K102</strong>
                        <small class="text-muted">Manual Settlement</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initial">MK</div>
                            <div>
                                <strong class="d-block">Maya Kusuma</strong>
                                <small class="text-muted">4 Tamu (Keluarga)</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="bi bi-whatsapp me-1"></i> +62 811-2233-4455</span></td>
                    <td>
                        <strong class="d-block">Unit 102 Deluxe Garden</strong>
                        <small class="text-muted">Lantai 1 &bull; Direct Garden</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2 small">
                            <div class="text-center"><strong>25</strong><br><span class="text-muted" style="font-size:0.65rem;">Okt</span></div>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <div class="text-center"><strong>27</strong><br><span class="text-muted" style="font-size:0.65rem;">Okt 2026</span></div>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size:0.65rem;">2 Malam</small>
                    </td>
                    <td><strong>Rp 1.700.000</strong></td>
                    <td class="text-muted small">Selesai</td>
                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill py-2 px-3"><i class="bi bi-circle-fill text-success me-1" style="font-size:0.4rem;"></i> Dibayar</span></td>
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