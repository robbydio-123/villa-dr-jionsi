<?php
if (!isset($facilities) || !is_array($facilities)) {
    echo '<div class="alert alert-danger m-4">Halaman Master Fasilitas harus dibuka lewat <code>public/admin_fasilitas.php</code> versi terbaru. Perbarui file tersebut.</div>';
    return;
}

// Perbaikan error "Undefined variable $stats": hitung sendiri bila belum disediakan controller.
if (!isset($stats) || !is_array($stats)) {
    $stats = fasilitas_stats(db());
}

$arsip     = !empty($filters['arsip']);
$modalBuka = ($formOld !== null || $formErrors);
$formData  = $formOld ?? [];
$fv = static function (string $key, $default = '') use ($formData) {
    return array_key_exists($key, $formData) ? $formData[$key] : $default;
};

$warnaKategori = [
    'Fasilitas Kamar'  => 'text-danger',
    'Area Umum'        => 'text-success',
    'Layanan Tambahan' => 'text-primary',
];
$ikonTerpilih = (string)$fv('ikon', 'bi-stars');
$kamarDipilih = array_map('intval', (array)$fv('kamar_ids', []));

$kartu = [
    ['Fasilitas Kamar',  'FASILITAS KAMAR',  'bi-door-open', 'text-brown',   'text-brown'],
    ['Area Umum',        'AREA UMUM',        'bi-tree',      'text-success', ''],
    ['Layanan Tambahan', 'LAYANAN TAMBAHAN', 'bi-bell',      'text-primary', ''],
];
?>

<div class="admin-content">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill"><i class="bi bi-stars me-1"></i> MASTER DATA INVENTORY</span>
                <small class="text-muted fw-bold" style="font-size: 0.7rem;"><?= h(villa_setting('alamat_lengkap', 'Songgokerto, Batu Highland')) ?></small>
            </div>
            <h3 class="fw-bolder mb-1"><?= $arsip ? 'Fasilitas Terhapus' : 'Master Fasilitas' ?></h3>
            <p class="text-muted small mb-0">Kelola daftar fasilitas yang tersedia untuk unit kamar maupun area umum villa.</p>
            <small class="text-muted" style="font-size: 0.7rem;">Fasilitas <strong>Area Umum</strong> yang berstatus Aktif tampil otomatis di halaman Profil &amp; Fasilitas untuk tamu.</small>
        </div>
        <div class="d-flex gap-2 text-md-end mt-2 mt-md-0">
            <div class="dropdown">
                <button class="btn btn-light border fw-semibold dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Unduh Laporan
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item small" href="admin_fasilitas.php?unduh=xlsx"><i class="bi bi-file-earmark-excel text-success me-2"></i> Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item small" href="admin_fasilitas.php?unduh=csv"><i class="bi bi-filetype-csv text-secondary me-2"></i> CSV (.csv)</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-brown fw-bold" data-bs-toggle="modal" data-bs-target="#modalFasilitas" data-mode="tambah">
                <i class="bi bi-plus me-1"></i> Tambah Fasilitas
            </button>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?> small"><?= h($flash['message']) ?></div>
    <?php endif; ?>

    <!-- Kartu ringkasan: dihitung dari database, klik untuk memfilter -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(fasilitas_url(['sort' => $filters['sort']], ['kategori' => '', 'arsip' => ''])) ?>">
                <div class="stat-card border-secondary-subtle py-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">TOTAL FASILITAS</small>
                        <i class="bi bi-box text-brown fs-5"></i>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h2 class="fw-bolder mb-0"><?= (int)$stats['total'] ?></h2>
                        <span class="text-muted small mb-1"><i class="bi bi-check2-all text-success"></i> <?= (int)$stats['aktif'] ?> aktif<?= $stats['nonaktif'] > 0 ? ' · ' . (int)$stats['nonaktif'] . ' nonaktif' : '' ?></span>
                    </div>
                </div>
            </a>
        </div>
        <?php foreach ($kartu as [$kat, $judul, $ikon, $warnaIkon, $warnaAngka]): ?>
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(fasilitas_url(['sort' => $filters['sort']], ['kategori' => $kat, 'arsip' => ''])) ?>">
                <div class="stat-card border-secondary-subtle py-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;"><?= h($judul) ?></small>
                        <i class="bi <?= h($ikon) ?> <?= h($warnaIkon) ?> fs-5"></i>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h2 class="fw-bolder mb-0 <?= h($warnaAngka) ?>"><?= (int)$stats[$kat] ?></h2>
                        <span class="text-muted small mb-1" style="font-size: 0.65rem;"><?= fasilitas_persen((int)$stats[$kat], (int)$stats['total']) ?>% dari total item</span>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="get" action="admin_fasilitas.php" id="formFilterFasilitas" class="row g-2">
                <?php if ($arsip): ?><input type="hidden" name="arsip" value="1"><?php endif; ?>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" value="<?= h($filters['q']) ?>" class="form-control bg-light border-secondary-subtle" placeholder="Cari nama fasilitas...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select bg-light border-secondary-subtle text-muted" data-autosubmit>
                        <option value="">Kategori: Semua</option>
                        <?php foreach (FASILITAS_KATEGORI as $k): ?>
                            <option value="<?= h($k) ?>" <?= $filters['kategori'] === $k ? 'selected' : '' ?>><?= h($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select bg-light border-secondary-subtle text-muted" data-autosubmit>
                        <option value="">Status: Semua</option>
                        <?php foreach (FASILITAS_STATUS as $st): ?>
                            <option value="<?= h($st) ?>" <?= $filters['status'] === $st ? 'selected' : '' ?>><?= h($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select bg-light border-secondary-subtle text-muted" data-autosubmit aria-label="Urutkan">
                        <option value="nama_asc" <?= $filters['sort'] === 'nama_asc' ? 'selected' : '' ?>>Urutkan: Nama (A-Z)</option>
                        <option value="kategori" <?= $filters['sort'] === 'kategori' ? 'selected' : '' ?>>Kategori</option>
                        <option value="terbaru" <?= $filters['sort'] === 'terbaru' ? 'selected' : '' ?>>Terbaru ditambahkan</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if ($arsip): ?>
        <div class="alert alert-secondary small d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span><i class="bi bi-archive me-1"></i> Fasilitas di sini disembunyikan dari halaman tamu dan daftar utama, tetapi datanya tetap tersimpan. Klik <strong>Pulihkan</strong> untuk menampilkannya lagi.</span>
            <a href="admin_fasilitas.php" class="btn btn-sm btn-light border">Kembali ke daftar fasilitas</a>
        </div>
    <?php elseif ($arsipCount > 0): ?>
        <div class="mb-3">
            <a href="admin_fasilitas.php?arsip=1" class="small text-muted text-decoration-none"><i class="bi bi-archive me-1"></i> Lihat fasilitas terhapus (<?= (int)$arsipCount ?>)</a>
        </div>
    <?php endif; ?>

    <!-- Tabel -->
    <div class="table-responsive bg-white rounded-3 shadow-sm border mb-4">
        <table class="admin-table">
            <thead class="bg-light">
                <tr>
                    <th class="text-center" style="width: 60px;">Ikon</th>
                    <th>Nama Fasilitas</th>
                    <th>Kategori</th>
                    <th>Kamar</th>
                    <th>Deskripsi Singkat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$facilities): ?>
                <tr><td colspan="7" class="text-center text-muted py-5">
                    <i class="bi bi-stars fs-3 d-block mb-2"></i>
                    <?= $arsip ? 'Tidak ada fasilitas terhapus.' : 'Tidak ada fasilitas yang cocok. Ubah filter atau tambahkan fasilitas baru.' ?>
                </td></tr>
            <?php endif; ?>
            <?php foreach ($facilities as $f):
                $aktif    = (bool)$f['aktif'];
                $kamarFas = $kamarMap[(int)$f['id']] ?? [];
                $dataFasilitas = [
                    'id'        => (int)$f['id'],
                    'nama'      => (string)$f['nama'],
                    'kategori'  => (string)$f['kategori'],
                    'status'    => $aktif ? 'Aktif' : 'Nonaktif',
                    'ikon'      => (string)$f['ikon'],
                    'deskripsi' => (string)($f['deskripsi'] ?? ''),
                    'kamar_ids' => array_map(static function ($k) {
                        return (int)$k['id'];
                    }, $kamarFas),
                ];
            ?>
                <tr class="<?= $arsip ? 'kamar-row-arsip' : '' ?>">
                    <td class="text-center"><div class="bg-light <?= $aktif ? 'text-brown' : 'text-muted' ?> p-2 rounded d-inline-block"><i class="bi <?= h($f['ikon']) ?> fs-5"></i></div></td>
                    <td><strong class="text-dark"><?= h($f['nama']) ?></strong></td>
                    <td><span class="<?= h($warnaKategori[$f['kategori']] ?? 'text-muted') ?> small fw-semibold"><?= h($f['kategori']) ?></span></td>
                    <td>
                        <?php if (!$kamarFas): ?>
                            <span class="text-muted small">&mdash;</span>
                        <?php elseif (count($kamarFas) > 1 && count($kamarFas) === count($kamarPilihan)): ?>
                            <span class="badge fas-kamar-badge" title="<?= h(implode(', ', array_map(static function ($k) { return 'Kamar ' . $k['nomor_kamar']; }, $kamarFas))) ?>">Semua kamar</span>
                        <?php else: ?>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach (array_slice($kamarFas, 0, 3) as $kf): ?>
                                    <span class="badge fas-kamar-badge" title="<?= h($kf['tipe_kamar']) ?>">Kamar <?= h($kf['nomor_kamar']) ?></span>
                                <?php endforeach; ?>
                                <?php if (count($kamarFas) > 3): ?>
                                    <span class="badge fas-kamar-badge" title="<?= h(implode(', ', array_map(static function ($k) { return 'Kamar ' . $k['nomor_kamar']; }, array_slice($kamarFas, 3)))) ?>">+<?= count($kamarFas) - 3 ?> lainnya</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= h($f['deskripsi'] ?? '') ?></td>
                    <td>
                        <?php if ($aktif): ?>
                            <span class="badge bg-light text-dark border rounded-pill py-1 px-3 fw-normal"><i class="bi bi-circle-fill text-success me-1" style="font-size:0.4rem;"></i> Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-light text-muted border rounded-pill py-1 px-3 fw-normal"><i class="bi bi-circle-fill text-secondary me-1" style="font-size:0.4rem;"></i> Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                        <?php if ($arsip): ?>
                            <form method="post" action="admin_fasilitas.php" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="aksi" value="pulihkan">
                                <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-brown"><i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan</button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm text-muted" title="Ubah fasilitas" aria-label="Ubah <?= h($f['nama']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalFasilitas" data-mode="ubah"
                                    data-fasilitas="<?= h(json_encode($dataFasilitas, JSON_UNESCAPED_UNICODE)) ?>"><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-sm text-muted" title="Hapus fasilitas" aria-label="Hapus <?= h($f['nama']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalHapusFasilitas"
                                    data-id="<?= (int)$f['id'] ?>" data-nama="<?= h($f['nama']) ?>" data-kategori="<?= h($f['kategori']) ?>"><i class="bi bi-trash"></i></button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
        <nav class="d-flex justify-content-between align-items-center flex-wrap gap-2" aria-label="Halaman fasilitas">
            <small class="text-muted">Menampilkan <?= count($facilities) ?> dari <?= (int)$total ?> fasilitas</small>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link text-muted" href="<?= h(fasilitas_url($filters, ['page' => $page - 1])) ?>"><i class="bi bi-chevron-left"></i></a>
                </li>
                <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link <?= $i === $page ? 'bg-brown border-brown' : 'text-dark' ?>" href="<?= h(fasilitas_url($filters, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                    <a class="page-link text-dark" href="<?= h(fasilitas_url($filters, ['page' => $page + 1])) ?>"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Modal Tambah / Ubah -->
<div class="modal fade" id="modalFasilitas" tabindex="-1" aria-labelledby="judulModalFasilitas" aria-hidden="true"
     data-open="<?= $modalBuka ? '1' : '0' ?>">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form method="post" action="admin_fasilitas.php" class="modal-content" id="formFasilitas" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="aksi" id="fasAksi" value="<?= h($formMode) ?>">
            <input type="hidden" name="id" id="fasId" value="<?= (int)$formId ?>">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="judulModalFasilitas"><?= $formMode === 'ubah' ? 'Ubah Fasilitas' : 'Tambah Fasilitas' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <?php if ($formErrors): ?>
                    <div class="alert alert-danger small">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($formErrors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" for="fasNama">Nama Fasilitas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fasNama" name="nama" maxlength="80" required
                               value="<?= h($fv('nama')) ?>" placeholder="mis. Wi-Fi Kecepatan Tinggi">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold" for="fasKategori">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="fasKategori" name="kategori" required>
                            <?php foreach (FASILITAS_KATEGORI as $k): ?>
                                <option value="<?= h($k) ?>" <?= $fv('kategori', FASILITAS_KATEGORI[0]) === $k ? 'selected' : '' ?>><?= h($k) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold" for="fasStatus">Status</label>
                        <select class="form-select" id="fasStatus" name="status" title="Hanya fasilitas Aktif yang tampil di halaman tamu">
                            <?php foreach (FASILITAS_STATUS as $st): ?>
                                <option value="<?= h($st) ?>" <?= $fv('status', 'Aktif') === $st ? 'selected' : '' ?>><?= h($st) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold" for="fasDeskripsi">Deskripsi Singkat</label>
                        <textarea class="form-control" id="fasDeskripsi" name="deskripsi" rows="3" maxlength="200"
                                  placeholder="Contoh: Koneksi internet stabil hingga 50 Mbps."><?= h($fv('deskripsi')) ?></textarea>
                        <small class="drj-hint"><i class="bi bi-info-circle"></i> Maksimal 200 karakter. Untuk kategori Area Umum, teks ini tampil sebagai keterangan di halaman tamu.</small>
                    </div>

                    <!-- Nama Kamar + Pilih semua -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <label class="form-label small fw-bold mb-0">Nama Kamar</label>
                            <?php if ($kamarPilihan): ?>
                                <div class="d-flex align-items-center gap-3">
                                    <small class="text-muted" id="fasKamarJumlah" aria-live="polite"></small>
                                    <div class="form-check mb-0 small">
                                        <input class="form-check-input" type="checkbox" id="fasKamarSemua">
                                        <label class="form-check-label fw-semibold" for="fasKamarSemua">Pilih semua kamar</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($kamarPilihan): ?>
                            <div class="fas-kamar-grid" id="fasKamarGrid">
                                <?php foreach ($kamarPilihan as $kp): ?>
                                    <label class="fas-kamar-item">
                                        <input class="form-check-input" type="checkbox" name="kamar_id[]" value="<?= (int)$kp['id'] ?>"
                                               <?= in_array((int)$kp['id'], $kamarDipilih, true) ? 'checked' : '' ?>>
                                        <span class="fas-kamar-teks">
                                            <strong>Kamar <?= h($kp['nomor_kamar']) ?></strong>
                                            <small><?= h($kp['tipe_kamar']) ?></small>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted small">Belum ada kamar. Tambahkan kamar dulu di menu Kelola Kamar.</div>
                        <?php endif; ?>
                        <small class="drj-hint"><i class="bi bi-info-circle"></i> Pilih kamar yang memiliki fasilitas ini. Kosongkan jika tidak terkait kamar tertentu (mis. Area Umum).</small>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold mb-0">Ikon</label>
                            <small class="text-muted"><span class="fas-ikon-pratinjau"><i class="bi <?= h($ikonTerpilih) ?>" id="fasIkonPratinjau"></i></span> <span id="fasIkonNama"></span></small>
                        </div>
                        <div class="fas-ikon-grid" id="fasIkonGrid" role="radiogroup" aria-label="Pilih ikon">
                            <?php foreach (FASILITAS_IKON as $kelas => $nama): ?>
                                <label class="fas-ikon-pilih" title="<?= h($nama) ?>">
                                    <input type="radio" name="ikon" value="<?= h($kelas) ?>" data-nama="<?= h($nama) ?>" <?= $ikonTerpilih === $kelas ? 'checked' : '' ?>>
                                    <span><i class="bi <?= h($kelas) ?>"></i></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-brown fw-bold" id="fasSimpan"><i class="bi bi-save me-1"></i> <span><?= $formMode === 'ubah' ? 'Simpan Perubahan' : 'Tambah Fasilitas' ?></span></button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapusFasilitas" tabindex="-1" aria-labelledby="judulModalHapusFasilitas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="admin_fasilitas.php" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id" id="hapusFasId" value="">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="judulModalHapusFasilitas">Hapus Fasilitas?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body small">
                <p class="mb-2"><strong id="hapusFasNama"></strong> akan <strong>disembunyikan</strong> dari halaman tamu dan dari daftar ini.</p>
                <p class="text-muted mb-0"><i class="bi bi-archive me-1"></i> Datanya tidak benar-benar dihapus dan bisa dipulihkan dari "Lihat fasilitas terhapus".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold"><i class="bi bi-trash me-1"></i> Hapus dari Tampilan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var el = function (id) { return document.getElementById(id); };
    var modalEl = el('modalFasilitas');
    var hapusEl = el('modalHapusFasilitas');
    var filterForm = el('formFilterFasilitas');

    if (filterForm) {
        filterForm.querySelectorAll('[data-autosubmit]').forEach(function (s) {
            s.addEventListener('change', function () { filterForm.submit(); });
        });
    }

    if (hapusEl) {
        hapusEl.addEventListener('show.bs.modal', function (ev) {
            var btn = ev.relatedTarget;
            if (!btn) { return; }
            el('hapusFasId').value = btn.getAttribute('data-id') || '';
            el('hapusFasNama').textContent = (btn.getAttribute('data-nama') || '') +
                ' (' + (btn.getAttribute('data-kategori') || '') + ')';
        });
    }

    if (!modalEl) { return; }

    var fNama = el('fasNama'), fKategori = el('fasKategori'), fStatus = el('fasStatus'),
        fDeskripsi = el('fasDeskripsi'), grid = el('fasIkonGrid'),
        pratinjau = el('fasIkonPratinjau'), namaIkon = el('fasIkonNama');

    /* ---------- Pemilih kamar + "Pilih semua kamar" ---------- */
    var kamarGrid   = el('fasKamarGrid');
    var kamarSemua  = el('fasKamarSemua');
    var kamarJumlah = el('fasKamarJumlah');
    var kamarItems  = kamarGrid ? Array.prototype.slice.call(
        kamarGrid.querySelectorAll('input[name="kamar_id[]"]')) : [];

    function sinkronKamar() {
        var total = kamarItems.length;
        var dipilih = kamarItems.filter(function (c) { return c.checked; }).length;
        if (kamarSemua) {
            kamarSemua.checked = total > 0 && dipilih === total;
            kamarSemua.indeterminate = dipilih > 0 && dipilih < total;
        }
        if (kamarJumlah) {
            kamarJumlah.textContent = dipilih + ' dari ' + total + ' kamar dipilih';
        }
    }

    function setKamar(ids) {
        var set = {};
        (ids || []).forEach(function (id) { set[String(id)] = true; });
        kamarItems.forEach(function (c) { c.checked = !!set[c.value]; });
        sinkronKamar();
    }

    if (kamarSemua) {
        kamarSemua.addEventListener('change', function () {
            var nyala = kamarSemua.checked;
            kamarItems.forEach(function (c) { c.checked = nyala; });
            sinkronKamar();
        });
    }
    kamarItems.forEach(function (c) { c.addEventListener('change', sinkronKamar); });

    /* ---------- Ikon ---------- */
    function tampilIkon() {
        var r = grid.querySelector('input[name="ikon"]:checked');
        if (!r) { return; }
        pratinjau.className = 'bi ' + r.value;
        namaIkon.textContent = r.getAttribute('data-nama') || '';
    }

    function pilihIkon(kelas) {
        var target = grid.querySelector('input[name="ikon"][value="' + kelas + '"]') ||
                     grid.querySelector('input[name="ikon"][value="bi-stars"]');
        if (target) { target.checked = true; }
        tampilIkon();
    }

    /* ---------- Isi form saat modal dibuka ---------- */
    function isi(data, mode) {
        el('fasAksi').value = mode;
        el('fasId').value = data.id || 0;
        el('judulModalFasilitas').textContent = mode === 'ubah' ? 'Ubah ' + (data.nama || 'Fasilitas') : 'Tambah Fasilitas';
        el('fasSimpan').querySelector('span').textContent = mode === 'ubah' ? 'Simpan Perubahan' : 'Tambah Fasilitas';

        fNama.value = data.nama || '';
        fKategori.value = data.kategori || fKategori.options[0].value;
        fStatus.value = data.status || 'Aktif';
        fDeskripsi.value = data.deskripsi || '';
        pilihIkon(data.ikon || 'bi-stars');
        setKamar(data.kamar_ids || []);

        var alert = modalEl.querySelector('.alert-danger');
        if (alert) { alert.remove(); }
    }

    modalEl.addEventListener('show.bs.modal', function (ev) {
        var btn = ev.relatedTarget;
        if (!btn) { tampilIkon(); sinkronKamar(); return; }
        var mode = btn.getAttribute('data-mode') === 'ubah' ? 'ubah' : 'tambah';
        var data = {};
        if (mode === 'ubah') {
            try { data = JSON.parse(btn.getAttribute('data-fasilitas') || '{}'); } catch (e) { data = {}; }
        }
        isi(data, mode);
    });

    modalEl.addEventListener('shown.bs.modal', function () { fNama.focus(); });
    grid.addEventListener('change', tampilIkon);

    tampilIkon();
    sinkronKamar();

    if (modalEl.getAttribute('data-open') === '1' && window.bootstrap) {
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
});
</script>