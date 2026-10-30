<?php
// Pastikan koneksi database tersedia
$pdo = db();

// Tangkap parameter filter & pencarian
$filterCheckin   = trim($_GET['checkin'] ?? '');
$filterDurasi    = (int)($_GET['durasi'] ?? 0);
$filterCheckout  = trim($_GET['checkout'] ?? '');
$filterKapasitas = (int)($_GET['kapasitas'] ?? 0);
$filterSort      = trim($_GET['sort'] ?? 'harga_asc');

// Jika check-in dan durasi diisi tapi checkout belum terisi, hitung otomatis
if (!empty($filterCheckin) && $filterDurasi > 0 && empty($filterCheckout)) {
    $timeCheckin = strtotime($filterCheckin);
    if ($timeCheckin !== false) {
        $filterCheckout = date('Y-m-d\TH:i', $timeCheckin + ($filterDurasi * 3600));
    }
}

// Opsi pengurutan
$orderSql = 'k.harga_per_malam ASC';
if ($filterSort === 'harga_desc') {
    $orderSql = 'k.harga_per_malam DESC';
} elseif ($filterSort === 'kapasitas_desc') {
    $orderSql = 'k.kapasitas DESC, k.harga_per_malam ASC';
} elseif ($filterSort === 'nomor_asc') {
    $orderSql = 'k.nomor_kamar ASC';
}

// Query mengambil data kamar dengan pengecekan tumpang tindih reservasi
$params = [];
$whereClauses = [];

if ($filterKapasitas > 0) {
    $whereClauses[] = 'k.kapasitas >= :kapasitas';
    $params['kapasitas'] = $filterKapasitas;
}

$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
$adaCariWaktu = (!empty($filterCheckin) && !empty($filterCheckout));

if ($adaCariWaktu) {
    // Cari tumpang tindih dengan rentang pencarian pengunjung
    $sqlKamar = "
        SELECT 
            k.*,
            res.status_booking AS active_status,
            res.waktu_checkout AS active_checkout,
            res.batas_waktu_keep AS active_keep
        FROM kamar k
        LEFT JOIN LATERAL (
            SELECT r.status_booking, r.waktu_checkout, r.batas_waktu_keep
            FROM reservasi r
            WHERE r.kamar_id = k.id
              AND r.status_booking IN ('Di-Keep', 'Menunggu Konfirmasi', 'Dibayar')
              AND tsrange(r.waktu_checkin, r.waktu_checkout) && tsrange(:cari_checkin::timestamp, :cari_checkout::timestamp)
            ORDER BY r.created_at DESC
            LIMIT 1
        ) res ON TRUE
        $whereSql
        ORDER BY $orderSql
    ";
    $params['cari_checkin'] = date('Y-m-d H:i:s', strtotime($filterCheckin));
    $params['cari_checkout'] = date('Y-m-d H:i:s', strtotime($filterCheckout));
} else {
    // Jika belum ada rentang waktu pencarian, periksa kondisi saat ini (real-time)
    $sqlKamar = "
        SELECT 
            k.*,
            res.status_booking AS active_status,
            res.waktu_checkout AS active_checkout,
            res.batas_waktu_keep AS active_keep
        FROM kamar k
        LEFT JOIN LATERAL (
            SELECT r.status_booking, r.waktu_checkout, r.batas_waktu_keep
            FROM reservasi r
            WHERE r.kamar_id = k.id
              AND r.status_booking IN ('Di-Keep', 'Menunggu Konfirmasi', 'Dibayar')
              AND tsrange(r.waktu_checkin, r.waktu_checkout) @> LOCALTIMESTAMP
            ORDER BY r.created_at DESC
            LIMIT 1
        ) res ON TRUE
        $whereSql
        ORDER BY $orderSql
    ";
}

$stmtKamar = $pdo->prepare($sqlKamar);
$stmtKamar->execute($params);
$daftarKamar = $stmtKamar->fetchAll();

// Hitung total unit yang siap huni (status kamar != 'Perbaikan' dan tidak sedang aktif)
$totalSiapHuni = 0;
foreach ($daftarKamar as $item) {
    if ($item['status'] === 'Tersedia' && empty($item['active_status'])) {
        $totalSiapHuni++;
    }
}

// Fungsi bantu waktu relatif pembaruan kondisi kamar
function formatWaktuRelatif(?string $datetime): string {
    if (!$datetime) return 'Kondisi diperbarui baru saja';
    $time = strtotime($datetime);
    if (!$time) return 'Kondisi diperbarui baru saja';
    $diff = time() - $time;
    if ($diff < 0 || $diff < 3600) return 'Kondisi diperbarui hari ini';
    if ($diff < 86400) {
        $jam = floor($diff / 3600);
        return "Kondisi diperbarui $jam jam lalu";
    }
    $hari = floor($diff / 86400);
    if ($hari == 1) return 'Kondisi diperbarui kemarin';
    if ($hari < 30) return "Kondisi diperbarui $hari hari lalu";
    return 'Kondisi diperbarui ' . date('d M Y', $time);
}
?>

<div class="container mb-5">
    <?php if ($error): ?>
        <div class="alert alert-warning" role="alert"><?= h($error) ?></div>
    <?php endif; ?>
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
            <a href="profil.php" class="btn btn-brown px-4 py-2 fw-semibold rounded-pill">Lihat Profil Villa <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>

    <!-- Form Pencarian Melayang -->
    <div class="card p-3 shadow-sm border-0 bg-white floating-search-form mb-4">
        <form class="row g-3 align-items-end" id="formCariKamar" method="GET" action="index.php">

            <!-- Waktu Check-in -->
            <div class="col-12 col-md-3">
                <label for="waktuCheckin" class="form-label small fw-bold text-muted mb-1"><i class="bi bi-calendar-event me-1"></i> WAKTU CHECK-IN</label>
                <input type="datetime-local" id="waktuCheckin" name="checkin" value="<?= htmlspecialchars($filterCheckin) ?>" class="form-control form-control-sm border-secondary-subtle">
            </div>

            <!-- Durasi Sewa -->
            <div class="col-6 col-md-2">
                <label for="durasiSewa" class="form-label small fw-bold text-muted mb-1"><i class="bi bi-stopwatch me-1"></i> DURASI</label>
                <select id="durasiSewa" name="durasi" class="form-select form-select-sm border-secondary-subtle">
                    <option value="" <?= $filterDurasi === 0 ? 'selected' : '' ?> disabled>Pilih Jam</option>
                    <option value="3" <?= $filterDurasi === 3 ? 'selected' : '' ?>>3 Jam (Transit)</option>
                    <option value="6" <?= $filterDurasi === 6 ? 'selected' : '' ?>>6 Jam (Half Day)</option>
                    <option value="12" <?= $filterDurasi === 12 ? 'selected' : '' ?>>12 Jam</option>
                    <option value="24" <?= $filterDurasi === 24 ? 'selected' : '' ?>>24 Jam (Full Day)</option>
                </select>
            </div>

            <!-- Waktu Check-out (Otomatis Dihitung via JS Native) -->
            <div class="col-12 col-md-3">
                <label for="waktuCheckout" class="form-label small fw-bold text-muted mb-1"><i class="bi bi-calendar-check me-1"></i> WAKTU CHECK-OUT</label>
                <input type="datetime-local" id="waktuCheckout" name="checkout" value="<?= htmlspecialchars($filterCheckout) ?>" class="form-control form-control-sm border-secondary-subtle bg-light" readonly placeholder="Otomatis terhitung">
            </div>

            <!-- Kapasitas Tamu -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-people me-1"></i> KAPASITAS</label>
                <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary" type="button" id="btnMinusTamu">-</button>
                    <input type="text" class="form-control text-center" id="labelKapasitas" value="<?= $filterKapasitas > 0 ? (int)$filterKapasitas . ' Tamu' : '2 Tamu' ?>" readonly>
                    <input type="hidden" name="kapasitas" id="inputKapasitas" value="<?= $filterKapasitas > 0 ? (int)$filterKapasitas : 2 ?>">
                    <button class="btn btn-outline-secondary" type="button" id="btnPlusTamu">+</button>
                </div>
            </div>

            <!-- Tombol Cari -->
            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-brown btn-sm flex-fill fw-bold py-2"><i class="bi bi-search me-1"></i> Cari</button>
                <?php if ($adaCariWaktu || $filterKapasitas > 0): ?>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm py-2" title="Reset Pencarian"><i class="bi bi-arrow-counterclockwise"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Filter Fasilitas Cepat -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex flex-wrap align-items-center gap-3 mb-4 mt-2 small text-muted">
            <span class="fw-bold"><i class="bi bi-funnel me-1"></i> Filter Cepat:</span>
            <div class="form-check mb-0">
                <input class="form-check-input filter-facility-check" type="checkbox" id="fasAirPanas" value="Air Panas">
                <label class="form-check-label" for="fasAirPanas">Air Panas</label>
            </div>
            <div class="form-check mb-0">
                <input class="form-check-input filter-facility-check" type="checkbox" id="fasSmartTv" value="Smart TV">
                <label class="form-check-label" for="fasSmartTv">Smart TV</label>
            </div>
            <div class="form-check mb-0">
                <input class="form-check-input filter-facility-check" type="checkbox" id="fasWifi" value="Wi-Fi">
                <label class="form-check-label" for="fasWifi">Wi-Fi</label>
            </div>
            <div class="form-check mb-0">
                <input class="form-check-input filter-facility-check" type="checkbox" id="fasKmDalam" value="KM Dalam">
                <label class="form-check-label" for="fasKmDalam">KM Dalam</label>
            </div>
            <div class="form-check mb-0">
                <input class="form-check-input filter-facility-check" type="checkbox" id="fasDapur" value="Dapur Bersama">
                <label class="form-check-label" for="fasDapur">Dapur Bersama</label>
            </div>
        </div>
    </div>

    <hr class="text-muted opacity-25 my-3">
</div>

<!-- BUNGKUS DENGAN CONTAINER AGAR KE TENGAH & TIDAK FULL -->
<section class="container mt-4 mb-5">

    <!-- Judul Katalog & Sortir -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h4 class="fw-bolder mb-0">Katalog Pilihan Kamar</h4>
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 border border-success border-opacity-25 small">
                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> <?= (int)$totalSiapHuni ?> dari <?= count($daftarKamar) ?> Unit Siap Huni
            </span>
        </div>
        <div class="d-flex align-items-center gap-2 small">
            <label for="selectUrutkan" class="text-muted text-nowrap mb-0">Urutkan:</label>
            <select id="selectUrutkan" class="form-select form-select-sm border-secondary-subtle fw-semibold" style="min-width: 160px;">
                <option value="harga_asc" <?= $filterSort === 'harga_asc' ? 'selected' : '' ?>>Harga Terendah</option>
                <option value="harga_desc" <?= $filterSort === 'harga_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                <option value="kapasitas_desc" <?= $filterSort === 'kapasitas_desc' ? 'selected' : '' ?>>Kapasitas Terbesar</option>
                <option value="nomor_asc" <?= $filterSort === 'nomor_asc' ? 'selected' : '' ?>>Nomor Kamar</option>
            </select>
        </div>
    </div>

    <!-- Grid Daftar Kamar Dinamis dari Database -->
    <div class="row g-4" id="roomCatalogGrid">

        <?php if (empty($daftarKamar)): ?>
            <div class="col-12 text-center py-5">
                <div class="card p-5 border-0 shadow-sm rounded-4 bg-light">
                    <i class="bi bi-search text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mb-2">Tidak Ada Kamar yang Sesuai</h5>
                    <p class="text-muted small mb-3">Coba sesuaikan tanggal check-in, durasi sewa, atau kurangi filter kapasitas tamu.</p>
                    <div>
                        <a href="index.php" class="btn btn-brown btn-sm px-4 py-2 rounded-pill fw-semibold">Tampilkan Semua Kamar</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($daftarKamar as $kamar): ?>
                <?php
                // Tentukan status ketersediaan kamar
                $statusKamar  = $kamar['status']; // 'Tersedia', 'Terisi', 'Perbaikan'
                $activeStatus = $kamar['active_status'] ?? null;
                
                $badgeClass      = 'bg-tersedia';
                $badgeStyle      = 'background: #198754; color: white;';
                $badgeIcon       = 'bi-check-circle-fill';
                $badgeText       = 'Tersedia';
                $isBisaDipesan   = true;
                $pesanButtonText = 'Pesan Kamar';

                if ($statusKamar === 'Perbaikan') {
                    $badgeClass      = 'bg-secondary';
                    $badgeStyle      = 'background: #6c757d; color: white;';
                    $badgeIcon       = 'bi-tools';
                    $badgeText       = 'Perbaikan';
                    $isBisaDipesan   = false;
                    $pesanButtonText = 'Dalam Perbaikan';
                } elseif ($statusKamar !== 'Tersedia') {
                    $badgeClass      = 'bg-secondary';
                    $badgeStyle      = 'background: #6c757d; color: white;';
                    $badgeIcon       = 'bi-lock-fill';
                    $badgeText       = 'Tidak tersedia';
                    $isBisaDipesan   = false;
                    $pesanButtonText = 'Tidak tersedia';
                } elseif ($activeStatus === 'Di-Keep') {
                    $badgeClass      = 'bg-dikeep';
                    $badgeStyle      = 'background: #fd7e14; color: white;';
                    $badgeIcon       = 'bi-clock-history';
                    $badgeText       = 'Sedang Di-Keep';
                    $isBisaDipesan   = false;
                    $pesanButtonText = 'Sedang Di-Keep';
                } elseif (in_array($activeStatus, ['Menunggu Konfirmasi', 'Dibayar'])) {
                    $badgeClass      = 'bg-terisi';
                    $badgeStyle      = 'background: #dc3545; color: white;';
                    $badgeIcon       = 'bi-lock-fill';
                    $badgeText       = 'Terisi';
                    if (!empty($kamar['active_checkout'])) {
                        $badgeText .= ' s.d ' . date('d M', strtotime($kamar['active_checkout']));
                    }
                    $isBisaDipesan   = false;
                    $pesanButtonText = $badgeText;
                }

                $fasilitasList = getFasilitasKamar($kamar['deskripsi'] ?? '');
                $fasilitasJson = htmlspecialchars(json_encode(array_column($fasilitasList, 'label')));
                $kondisiUpdate = formatWaktuRelatif($kamar['kondisi_diperbarui_at'] ?? null);

                // Bangun parameter query pemesanan
                $bookingQuery = http_build_query(array_filter([
                    'kamar_id'  => $kamar['id'],
                    'checkin'   => $filterCheckin,
                    'durasi'    => $filterDurasi ?: null,
                    'checkout'  => $filterCheckout,
                    'kapasitas' => $filterKapasitas ?: null,
                ]));
                ?>
                <div class="col-md-6 col-lg-4 room-card-item" data-facilities="<?= $fasilitasJson ?>">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden <?= !$isBisaDipesan ? 'opacity-75' : '' ?>">
                        <div class="position-relative">
                            <?php if (!empty($kamar['foto_url']) && file_exists(__DIR__ . '/../../../public' . $kamar['foto_url'])): ?>
                                <img src="<?= htmlspecialchars(ltrim($kamar['foto_url'], '/')) ?>" alt="Kamar <?= htmlspecialchars($kamar['nomor_kamar']) ?>" class="w-100" style="height: 220px; object-fit: cover;">
                            <?php else: ?>
                                <div class="w-100 d-flex flex-column align-items-center justify-content-center text-muted" style="height: 220px; background-color: #E5E0D8;">
                                    <i class="bi bi-door-open fs-1 text-brown opacity-75"></i>
                                    <span class="small fw-bold mt-1 text-brown">Kamar <?= htmlspecialchars($kamar['nomor_kamar']) ?></span>
                                    <span class="badge bg-white bg-opacity-75 text-brown border mt-1" style="font-size: 0.7rem;"><?= htmlspecialchars($kamar['tipe_kamar']) ?></span>
                                </div>
                            <?php endif; ?>
                            <span class="badge-status <?= $badgeClass ?>" style="position: absolute; top: 15px; left: 15px; <?= $badgeStyle ?> padding: 5px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: bold;">
                                <i class="bi <?= $badgeIcon ?> me-1"></i> <?= htmlspecialchars($badgeText) ?>
                            </span>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted fw-bold" style="font-size: 0.65rem;">KAMAR <?= htmlspecialchars($kamar['nomor_kamar']) ?> • LANTAI <?= (int)$kamar['lantai'] ?></small>
                                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i> Maks <?= (int)$kamar['kapasitas'] ?> tamu</span>
                            </div>
                            <h5 class="fw-bolder mb-3"><?= htmlspecialchars($kamar['tipe_kamar']) ?></h5>
                            <div class="mb-3 d-flex flex-wrap gap-2">
                                <?php foreach ($fasilitasList as $fas): ?>
                                    <span class="facility-tag border rounded px-2 py-1 small text-muted"><i class="bi <?= $fas['icon'] ?>"></i> <?= htmlspecialchars($fas['label']) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success p-2 rounded small fw-semibold mb-3 border border-success border-opacity-25 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($kondisiUpdate) ?>
                            </div>
                            <div class="d-flex flex-column gap-3 mt-auto">
                                <div class="d-flex justify-content-between align-items-end">
                                    <small class="text-muted">Tarif Sewa:</small>
                                    <div class="text-end">
                                        <h5 class="fw-bolder text-brown mb-0">Rp <?= number_format($kamar['harga_per_malam'], 0, ',', '.') ?> <span class="fs-6 fw-normal text-muted">/ malam</span></h5>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="detail.php?id=<?= (int)$kamar['id'] ?>" class="btn btn-outline-brown flex-fill fw-bold rounded-pill border-brown text-brown">Lihat Detail</a>
                                    <?php if ($isBisaDipesan): ?>
                                        <a href="formulir.php?<?= $bookingQuery ?>" class="btn btn-brown flex-fill fw-bold rounded-pill bg-brown text-white">Pesan Kamar</a>
                                    <?php else: ?>
                                        <button class="btn btn-light border flex-fill fw-bold rounded-pill text-muted" disabled><i class="bi <?= $badgeIcon ?> me-1"></i> <?= htmlspecialchars($pesanButtonText) ?></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</section>

<script src="assets/js/katalog.js?v=<?= file_exists(__DIR__ . '/../../../public/assets/js/katalog.js') ? filemtime(__DIR__ . '/../../../public/assets/js/katalog.js') : time() ?>"></script>
