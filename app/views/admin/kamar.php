<?php
if (!isset($rooms) || !is_array($rooms)) {
    echo '<div class="alert alert-danger m-4">Halaman Kelola Kamar harus dibuka lewat <code>public/admin_kamar.php</code> versi terbaru. Perbarui file tersebut.</div>';
    return;
}

$arsip      = !empty($filters['arsip']);
$modalBuka  = ($formOld !== null || $formErrors);
$formData   = $formOld ?? [];
$fv = static function (string $key, $default = '') use ($formData) {
    return array_key_exists($key, $formData) ? $formData[$key] : $default;
};

$publicDir = dirname(__DIR__, 3) . '/public';
$fotoMap   = kamar_foto_map($pdo, array_column($rooms, 'id'));
$fotoJson  = static function (array $daftar) use ($publicDir): array {
    $hasil = [];
    foreach ($daftar as $f) {
        if (is_file($publicDir . $f['url'])) {
            $hasil[] = ['id' => (int)$f['id'], 'url' => ltrim((string)$f['url'], '/'), 'label' => (string)($f['label'] ?? '')];
        }
    }
    return $hasil;
};
$fotoForm = ($formOld !== null && $formMode === 'ubah' && $formId > 0)
    ? $fotoJson(kamar_foto_list($pdo, $formId)) : [];

$badgeStatus = [
    'Tersedia'  => ['bg-success bg-opacity-10 text-success border-success-subtle', 'bi-circle-fill', '0.4rem'],
    'Terisi'    => ['bg-warning bg-opacity-10 text-brown border-warning-subtle', 'bi-circle-fill', '0.4rem'],
    'Dipesan'   => ['bg-info bg-opacity-10 text-info-emphasis border-info-subtle', 'bi-calendar-check', '0.6rem'],
    'Perbaikan' => ['bg-danger bg-opacity-10 text-danger border-danger-subtle', 'bi-tools', '0.6rem'],
];
?>
<div class="admin-content">

    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning bg-opacity-10 text-brown border border-warning-subtle rounded-pill"><i class="bi bi-box me-1"></i> INVENTARIS &amp; PROPERTI</span>
                <small class="text-muted fw-bold" style="font-size: 0.7rem;"><?= h(villa_setting('alamat_lengkap', 'Songgokerto, Batu Highland')) ?></small>
            </div>
            <h3 class="fw-bolder mb-1"><?= $arsip ? 'Kamar Terhapus' : 'Manajemen Kamar' ?></h3>
            <p class="text-muted small mb-0">Kelola inventaris unit kamar, harga, fasilitas, foto, dan status ketersediaan secara real-time.</p>
        </div>
        <div class="d-flex gap-2 text-md-end">
            <div class="dropdown">
                <button class="btn btn-light border fw-semibold dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Unduh Laporan
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item small" href="admin_kamar.php?unduh=xlsx"><i class="bi bi-file-earmark-excel text-success me-2"></i> Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item small" href="admin_kamar.php?unduh=csv"><i class="bi bi-filetype-csv text-secondary me-2"></i> CSV (.csv)</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-brown fw-bold" data-bs-toggle="modal" data-bs-target="#modalKamar" data-mode="tambah">
                <i class="bi bi-plus me-1"></i> Tambah Kamar
            </button>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?> small"><?= h($flash['message']) ?></div>
    <?php endif; ?>

    <!-- 4 Kartu Ringkasan (dihitung langsung dari data kamar; klik untuk memfilter) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(kamar_url(['sort' => $filters['sort']], ['status' => '', 'arsip' => ''])) ?>">
                <div class="stat-card border-secondary-subtle">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">TOTAL UNIT</small>
                        <div class="bg-light p-2 rounded text-muted"><i class="bi bi-door-open fs-5"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h3 class="fw-bolder mb-0"><?= (int)$stats['total'] ?></h3>
                        <span class="text-muted small mb-1">Kamar</span>
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;"><i class="bi bi-check-circle me-1"></i> Terdaftar di sistem</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(kamar_url(['sort' => $filters['sort']], ['status' => 'Tersedia', 'arsip' => ''])) ?>">
                <div class="stat-card border-success-subtle">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">TERSEDIA</small>
                        <div class="bg-success bg-opacity-10 p-2 rounded text-success"><i class="bi bi-check-circle fs-5"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h3 class="fw-bolder mb-0 text-success"><?= (int)$stats['Tersedia'] ?></h3>
                        <span class="text-muted small mb-1">Unit Siap</span>
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.4rem;"></i> Siap Huni Tamu</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(kamar_url(['sort' => $filters['sort']], ['status' => 'Terisi', 'arsip' => ''])) ?>">
                <div class="stat-card border-warning-subtle">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">TERISI / DIPESAN</small>
                        <div class="bg-warning bg-opacity-10 p-2 rounded text-brown"><i class="bi bi-key fs-5"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h3 class="fw-bolder mb-0 text-brown"><?= (int)$stats['Terisi'] ?></h3>
                        <span class="text-muted small mb-1">Unit Aktif</span>
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;"><i class="bi bi-circle-fill text-warning me-1" style="font-size: 0.4rem;"></i> Sedang Menginap / Di-keep</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(kamar_url(['sort' => $filters['sort']], ['status' => 'Perbaikan', 'arsip' => ''])) ?>">
                <div class="stat-card border-danger-subtle bg-danger bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <small class="text-danger fw-bold" style="font-size: 0.7rem;">DALAM PERBAIKAN</small>
                        <div class="bg-white p-2 rounded text-danger border border-danger-subtle"><i class="bi bi-tools fs-5"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h3 class="fw-bolder mb-0 text-danger"><?= (int)$stats['Perbaikan'] ?></h3>
                        <span class="text-danger small mb-1">Maintenance</span>
                    </div>
                    <small class="text-danger mt-2 d-block fw-semibold" style="font-size: 0.7rem;">
                        <i class="bi bi-x-circle me-1"></i>
                        <?= $stats['catatan_perbaikan'] ? h(implode(' • ', array_slice($stats['catatan_perbaikan'], 0, 2))) : 'Tidak ada kamar diperbaiki' ?>
                    </small>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="get" action="admin_kamar.php" id="formFilterKamar" class="row g-2">
                <?php if ($arsip): ?><input type="hidden" name="arsip" value="1"><?php endif; ?>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" value="<?= h($filters['q']) ?>" class="form-control bg-light border-secondary-subtle" placeholder="Cari nomor atau tipe kamar...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="tipe" class="form-select bg-light border-secondary-subtle text-muted" data-autosubmit>
                        <option value="">Semua Tipe Kamar</option>
                        <?php foreach ($tipeList as $tp): ?>
                            <option value="<?= h($tp) ?>" <?= $filters['tipe'] === $tp ? 'selected' : '' ?>><?= h($tp) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select bg-light border-secondary-subtle text-muted" data-autosubmit>
                        <option value="">Semua Status</option>
                        <?php foreach (KAMAR_STATUS as $st): ?>
                            <option value="<?= h($st) ?>" <?= $filters['status'] === $st ? 'selected' : '' ?>><?= h($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select bg-light border-secondary-subtle text-muted" data-autosubmit aria-label="Urutkan">
                        <option value="nomor_asc" <?= $filters['sort'] === 'nomor_asc' ? 'selected' : '' ?>>Urutkan: Nomor kamar</option>
                        <option value="harga_asc" <?= $filters['sort'] === 'harga_asc' ? 'selected' : '' ?>>Harga termurah</option>
                        <option value="harga_desc" <?= $filters['sort'] === 'harga_desc' ? 'selected' : '' ?>>Harga termahal</option>
                        <option value="kapasitas_desc" <?= $filters['sort'] === 'kapasitas_desc' ? 'selected' : '' ?>>Kapasitas terbanyak</option>
                        <option value="terbaru" <?= $filters['sort'] === 'terbaru' ? 'selected' : '' ?>>Terbaru ditambahkan</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if ($arsip): ?>
        <div class="alert alert-secondary small d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span><i class="bi bi-archive me-1"></i> Kamar di sini disembunyikan dari halaman tamu dan daftar utama, tetapi datanya tetap tersimpan. Klik <strong>Pulihkan</strong> untuk menampilkannya lagi.</span>
            <a href="admin_kamar.php" class="btn btn-sm btn-light border">Kembali ke daftar kamar</a>
        </div>
    <?php elseif ($arsipCount > 0): ?>
        <div class="mb-3">
            <a href="admin_kamar.php?arsip=1" class="small text-muted text-decoration-none"><i class="bi bi-archive me-1"></i> Lihat kamar terhapus (<?= (int)$arsipCount ?>)</a>
        </div>
    <?php endif; ?>

    <!-- Tabel Data Kamar -->
    <div class="table-responsive bg-white rounded-3 shadow-sm border mb-4">
        <table class="admin-table">
            <thead class="bg-light">
                <tr>
                    <th>Info Kamar</th>
                    <th>Tipe &amp; Kapasitas</th>
                    <th>Harga Per Malam</th>
                    <th>Fasilitas Utama</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$rooms): ?>
                <tr><td colspan="6" class="text-center text-muted py-5">
                    <i class="bi bi-door-closed fs-3 d-block mb-2"></i>
                    <?= $arsip ? 'Tidak ada kamar terhapus.' : 'Tidak ada kamar yang cocok. Ubah filter atau tambahkan kamar baru.' ?>
                </td></tr>
            <?php endif; ?>
            <?php foreach ($rooms as $r):
                $fotoAda   = !empty($r['foto_url']) && is_file(dirname(__DIR__, 3) . '/public' . $r['foto_url']);
                $fasilitas = getFasilitasKamar((string)($r['deskripsi'] ?? ''));
                $st        = (string)$r['status_efektif'];
                [$kelasBadge, $ikonBadge, $ukuranIkon] = $badgeStatus[$st] ?? $badgeStatus['Tersedia'];
                $dataKamar = [
                    'id'                => (int)$r['id'],
                    'nomor_kamar'       => (string)$r['nomor_kamar'],
                    'tipe_kamar'        => (string)$r['tipe_kamar'],
                    'lantai'            => (int)$r['lantai'],
                    'kapasitas'         => (int)$r['kapasitas'],
                    'harga_per_malam'   => (int)$r['harga_per_malam'],
                    'status'            => (string)($r['status'] ?? 'Tersedia'),
                    'deskripsi'         => (string)($r['deskripsi'] ?? ''),
                    'catatan_perbaikan' => (string)($r['catatan_perbaikan'] ?? ''),
                    'fotos'             => $fotoJson($fotoMap[(int)$r['id']] ?? []),
                ];
            ?>
                <tr class="<?= $arsip ? 'kamar-row-arsip' : '' ?>">
                    <td>
                        <div class="d-flex gap-3 align-items-center">
                            <?php if ($fotoAda): ?>
                                <img src="<?= h(ltrim((string)$r['foto_url'], '/')) ?>" class="kamar-thumb" alt="Foto Kamar <?= h($r['nomor_kamar']) ?>">
                            <?php else: ?>
                                <div class="kamar-thumb kamar-thumb-empty" title="Belum ada foto"><i class="bi bi-image"></i></div>
                            <?php endif; ?>
                            <div>
                                <strong class="d-block text-dark">Kamar <?= h($r['nomor_kamar']) ?></strong>
                                <small class="text-muted" style="font-size: 0.65rem;">Lantai <?= (int)$r['lantai'] ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong class="d-block text-dark small"><?= h($r['tipe_kamar']) ?></strong>
                        <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.65rem;"><i class="bi bi-people"></i> Kapasitas: <?= (int)$r['kapasitas'] ?> tamu</small>
                    </td>
                    <td>
                        <strong class="d-block text-brown"><?= h(kamar_rupiah($r['harga_per_malam'])) ?></strong>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">/ malam</small>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1 text-muted" style="font-size: 0.65rem;">
                            <?php foreach (array_slice($fasilitas, 0, 3) as $f): ?>
                                <span><i class="bi <?= h($f['icon']) ?> me-1"></i> <?= h($f['label']) ?></span>
                            <?php endforeach; ?>
                            <?php if (count($fasilitas) > 3): ?>
                                <span>+<?= count($fasilitas) - 3 ?> lainnya</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="badge <?= h($kelasBadge) ?> border rounded-pill py-1 px-3"><i class="bi <?= h($ikonBadge) ?> me-1" style="font-size: <?= h($ukuranIkon) ?>;"></i> <?= h($st) ?></span>
                        <?php if ($st === 'Perbaikan' && !empty($r['catatan_perbaikan'])): ?>
                            <small class="text-danger d-block mt-1" style="font-size: 0.65rem;"><?= h($r['catatan_perbaikan']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                        <?php if ($arsip): ?>
                            <form method="post" action="admin_kamar.php" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="aksi" value="pulihkan">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-brown"><i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan</button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm text-muted" title="Ubah kamar" aria-label="Ubah Kamar <?= h($r['nomor_kamar']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalKamar" data-mode="ubah"
                                    data-room="<?= h(json_encode($dataKamar, JSON_UNESCAPED_UNICODE)) ?>"><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-sm text-muted" title="Hapus kamar" aria-label="Hapus Kamar <?= h($r['nomor_kamar']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalHapus"
                                    data-id="<?= (int)$r['id'] ?>" data-nomor="<?= h($r['nomor_kamar']) ?>" data-tipe="<?= h($r['tipe_kamar']) ?>"><i class="bi bi-trash"></i></button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
        <nav class="d-flex justify-content-between align-items-center flex-wrap gap-2" aria-label="Halaman kamar">
            <small class="text-muted">Menampilkan <?= count($rooms) ?> dari <?= (int)$total ?> kamar</small>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link text-muted" href="<?= h(kamar_url($filters, ['page' => $page - 1])) ?>"><i class="bi bi-chevron-left"></i></a>
                </li>
                <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link <?= $i === $page ? 'bg-brown border-brown' : 'text-dark' ?>" href="<?= h(kamar_url($filters, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                    <a class="page-link text-dark" href="<?= h(kamar_url($filters, ['page' => $page + 1])) ?>"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- ===================== Modal Tambah / Ubah Kamar ===================== -->
<div class="modal fade" id="modalKamar" tabindex="-1" aria-labelledby="judulModalKamar" aria-hidden="true"
     data-open="<?= $modalBuka ? '1' : '0' ?>" data-fotos="<?= h(json_encode($fotoForm)) ?>">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form method="post" action="admin_kamar.php" enctype="multipart/form-data" class="modal-content" id="formKamar" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="aksi" id="kamarAksi" value="<?= h($formMode) ?>">
            <input type="hidden" name="id" id="kamarId" value="<?= (int)$formId ?>">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="judulModalKamar"><?= $formMode === 'ubah' ? 'Ubah Kamar' : 'Tambah Kamar' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <?php if ($formErrors): ?>
                    <div class="alert alert-danger small">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($formErrors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
                        </ul>
                        <?php if (!empty($_FILES['foto']['name'])): ?>
                            <div class="mt-1">Pilih ulang foto jika ingin mengunggahnya.</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="row g-3">
                    <!-- Baris 1 -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" for="kamarNomor">Nomor Kamar <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kamarNomor" name="nomor_kamar" maxlength="10" required
                               value="<?= h($fv('nomor_kamar')) ?>" placeholder="mis. 105">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" for="kamarTipe">Tipe Kamar <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kamarTipe" name="tipe_kamar" maxlength="50" required
                               value="<?= h($fv('tipe_kamar')) ?>" placeholder="mis. Deluxe Arjuno">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" for="kamarStatus">Status</label>
                        <select class="form-select" id="kamarStatus" name="status" title="Kamar berstatus Tersedia saja yang bisa dipesan tamu">
                            <option value="Tersedia"  <?= $fv('status', 'Tersedia') === 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                            <option value="Dipesan"   <?= $fv('status', 'Tersedia') === 'Dipesan' ? 'selected' : '' ?>>Dipesan (tamu offline)</option>
                            <option value="Perbaikan" <?= $fv('status', 'Tersedia') === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                            <option value="Terisi" id="kamarStatusLama" hidden <?= $fv('status', 'Tersedia') === 'Terisi' ? 'selected' : '' ?>>Terisi (lama)</option>
                        </select>
                    </div>

                    <!-- Baris 2 -->
                    <div class="col-6 col-md-4">
                        <label class="form-label small fw-bold" for="kamarLantai">Lantai <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="kamarLantai" name="lantai" min="1" max="20" required value="<?= h($fv('lantai', 1)) ?>">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label small fw-bold" for="kamarKapasitas">Kapasitas (tamu) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="kamarKapasitas" name="kapasitas" min="1" max="50" required value="<?= h($fv('kapasitas', 2)) ?>">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold" for="kamarHarga">Harga per Malam <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="kamarHarga" name="harga_per_malam" min="0" step="1000" required value="<?= h($fv('harga_per_malam', '')) ?>" placeholder="450000">
                        </div>
                    </div>
                    
                    <!-- Tamu offline (muncul hanya jika status Dipesan) -->
                    <div class="col-12" id="kamarOfflineWrap">
                        <div class="kamar-offline-box">
                            <div class="fw-bold small mb-2"><i class="bi bi-calendar-check me-1"></i> Jadwal Tamu Offline</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold" for="offCheckin">Check-in <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="offCheckin" name="off_checkin" value="<?= h($fv('off_checkin')) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold" for="offCheckout">Check-out <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="offCheckout" name="off_checkout" value="<?= h($fv('off_checkout')) ?>">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold" for="offNama">Nama tamu <span class="text-muted fw-normal">(opsional)</span></label>
                                    <input type="text" class="form-control" id="offNama" name="off_nama" maxlength="100" value="<?= h($fv('off_nama')) ?>" placeholder="Tamu Offline">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold" for="offWa">WhatsApp <span class="text-muted fw-normal">(opsional)</span></label>
                                    <input type="text" class="form-control" id="offWa" name="off_whatsapp" maxlength="16" value="<?= h($fv('off_whatsapp')) ?>" placeholder="08123456789">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold" for="offTamu">Jumlah tamu</label>
                                    <input type="number" class="form-control" id="offTamu" name="off_tamu" min="1" max="50" value="<?= h($fv('off_tamu', 1)) ?>">
                                </div>
                            </div>
                            <small class="drj-hint"><i class="bi bi-info-circle"></i> Dicatat sebagai reservasi <b>Dibayar</b>. Di beranda kamar tampil "Dipesan" dengan rentang tanggalnya, dan jadwal itu tidak bisa dipesan tamu online.</small>
                        </div>
                    </div>

                    <!-- Catatan perbaikan (muncul hanya jika status Perbaikan) -->
                    <div class="col-12" id="kamarCatatanWrap">
                        <label class="form-label small fw-bold" for="kamarCatatan">Catatan Perbaikan</label>
                        <input type="text" class="form-control" id="kamarCatatan" name="catatan_perbaikan" maxlength="150"
                               value="<?= h($fv('catatan_perbaikan')) ?>" placeholder="mis. Servis AC">
                        <small class="drj-hint"><i class="bi bi-info-circle"></i> Tampil di kartu "Dalam Perbaikan".</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold" for="kamarDeskripsi">Deskripsi</label>
                        <textarea class="form-control" id="kamarDeskripsi" name="deskripsi" rows="4" maxlength="2000"
                                  placeholder="Contoh: Kamar nyaman untuk 2 tamu dengan air panas, Smart TV, Wi-Fi, dan kamar mandi dalam."><?= h($fv('deskripsi')) ?></textarea>
                        <div class="drj-hint mt-2"><i class="bi bi-magic"></i>
                            <span>Fasilitas di halaman tamu dibaca otomatis dari deskripsi. Kata kunci yang dikenali:
                                <em>air panas, smart tv, wi-fi, kamar mandi dalam, dapur, pemandangan/pegunungan</em>.</span>
                        </div>
                        <div class="kamar-fas-preview d-flex flex-wrap gap-2 mt-2" id="kamarFasilitasPreview" aria-live="polite"></div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold mb-0">Foto Kamar</label>
                            <small class="text-muted" id="kamarFotoInfo">0 / 10 foto</small>
                        </div>

                        <!-- Foto yang sudah tersimpan (diisi JS saat Ubah) -->
                        <div class="kamar-foto-grid mb-3" id="kamarFotoLama"></div>

                        <!-- Baris upload foto baru -->
                        <div class="d-flex flex-column gap-2" id="kamarFotoBaru"></div>

                        <button type="button" class="btn btn-sm btn-outline-brown mt-2" id="kamarTambahFoto">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Foto
                        </button>
                        <small class="drj-hint"><i class="bi bi-info-circle"></i> Maksimal 10 foto, tiap foto JPG/PNG/WEBP maksimal 2 MB. Label (mis. "Balkon Panderman") tampil di galeri halaman detail. Pilih "Foto utama" untuk menentukan foto sampul di katalog.</small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-brown fw-bold" id="kamarSimpan"><i class="bi bi-save me-1"></i> <span><?= $formMode === 'ubah' ? 'Simpan Perubahan' : 'Tambah Kamar' ?></span></button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== Modal Hapus Kamar ===================== -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="judulModalHapus" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="admin_kamar.php" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id" id="hapusId" value="">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="judulModalHapus">Hapus Kamar <span id="hapusNomor"></span>?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body small">
                <p class="mb-2"><strong id="hapusTipe"></strong> akan <strong>disembunyikan</strong> dari halaman tamu dan dari daftar ini.</p>
                <p class="text-muted mb-0"><i class="bi bi-archive me-1"></i> Datanya tidak benar-benar dihapus: foto, harga, dan riwayat reservasinya tetap tersimpan, dan kamar bisa dipulihkan dari "Lihat kamar terhapus".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold"><i class="bi bi-trash me-1"></i> Hapus dari Tampilan</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/admin_kamar.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/admin_kamar.js') ?>"></script>