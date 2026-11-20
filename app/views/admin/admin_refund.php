<?php
$arsip      = !empty($filters['arsip']);
$statusAktif = $filters['status'];
$adaFilter  = $filters['q'] !== '' || $statusAktif !== '' || $filters['sort'] !== 'terbaru';
$cardAktif  = static function (bool $on): string {
    return $on ? ' shadow border-2' : '';
};
?>
<div class="admin-content">

    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill"><i class="bi bi-arrow-counterclockwise me-1"></i> MANAJEMEN PEMBATALAN</span>
            </div>
            <h3 class="fw-bolder mb-1"><?= $arsip ? 'Pengajuan Refund Terhapus' : 'Pengajuan Refund' ?></h3>
            <p class="text-muted small mb-0">Kelola dan verifikasi permintaan pembatalan dan pengembalian dana dari tamu.</p>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?> small"><?= h($flash['message']) ?></div>
    <?php endif; ?>

    <!-- 4 Kartu Ringkasan (klik untuk memfilter) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(refund_url($filters, ['status' => 'Diajukan', 'arsip' => ''])) ?>">
                <div class="stat-card border-warning-subtle<?= $cardAktif(!$arsip && $statusAktif === 'Diajukan') ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">MENUNGGU TINJAUAN</small>
                        <div class="bg-warning bg-opacity-10 p-2 rounded text-brown"><i class="bi bi-hourglass-split fs-5"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h3 class="fw-bolder mb-0 text-brown"><?= (int)$stats['menunggu'] ?></h3>
                        <span class="text-muted small mb-1">Pengajuan Baru</span>
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;"><i class="bi bi-circle-fill text-warning me-1" style="font-size: 0.4rem;"></i> Perlu diputuskan admin</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(refund_url($filters, ['status' => 'Disetujui', 'arsip' => ''])) ?>">
                <div class="stat-card border-success-subtle<?= $cardAktif(!$arsip && $statusAktif === 'Disetujui') ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">REFUND DISETUJUI</small>
                        <div class="bg-success bg-opacity-10 p-2 rounded text-success"><i class="bi bi-check-circle fs-5"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h3 class="fw-bolder mb-0 text-success"><?= (int)$stats['disetujui'] ?></h3>
                        <span class="text-muted small mb-1">Disetujui</span>
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.4rem;"></i> Dana ditransfer manual</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(refund_url($filters, ['status' => 'Ditolak', 'arsip' => ''])) ?>">
                <div class="stat-card border-danger-subtle bg-danger bg-opacity-10<?= $cardAktif(!$arsip && $statusAktif === 'Ditolak') ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <small class="text-danger fw-bold" style="font-size: 0.7rem;">DITOLAK</small>
                        <div class="bg-white p-2 rounded text-danger border border-danger-subtle"><i class="bi bi-x-circle fs-5"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h3 class="fw-bolder mb-0 text-danger"><?= (int)$stats['ditolak'] ?></h3>
                        <span class="text-danger small mb-1">Pengajuan Ditolak</span>
                    </div>
                    <small class="text-danger mt-2 d-block fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-x-circle me-1"></i> Tamu boleh mengajukan ulang</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="stat-link" href="<?= h(refund_url($filters, ['status' => '', 'arsip' => 1])) ?>">
                <div class="stat-card border-secondary-subtle<?= $cardAktif($arsip) ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">TERHAPUS</small>
                        <div class="bg-light p-2 rounded text-muted"><i class="bi bi-trash3 fs-5"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <h3 class="fw-bolder mb-0"><?= (int)$stats['terhapus'] ?></h3>
                        <span class="text-muted small mb-1">Pengajuan</span>
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;"><i class="bi bi-arrow-counterclockwise me-1"></i> Bisa dipulihkan</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="get" action="admin_refund.php" class="row g-2">
                <?php if ($arsip): ?><input type="hidden" name="arsip" value="1"><?php endif; ?>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" value="<?= h($filters['q']) ?>" class="form-control bg-light border-secondary-subtle" placeholder="Cari kode booking, nama, atau WhatsApp tamu...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select bg-light border-secondary-subtle text-muted" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Diajukan" <?= $statusAktif === 'Diajukan' ? 'selected' : '' ?>>Menunggu Tinjauan</option>
                        <option value="Disetujui" <?= $statusAktif === 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="Ditolak" <?= $statusAktif === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select bg-light border-secondary-subtle text-muted" onchange="this.form.submit()" aria-label="Urutkan">
                        <option value="terbaru" <?= $filters['sort'] === 'terbaru' ? 'selected' : '' ?>>Urutkan: Terbaru</option>
                        <option value="terlama" <?= $filters['sort'] === 'terlama' ? 'selected' : '' ?>>Terlama</option>
                        <option value="nominal_desc" <?= $filters['sort'] === 'nominal_desc' ? 'selected' : '' ?>>Nominal terbesar</option>
                        <option value="nominal_asc" <?= $filters['sort'] === 'nominal_asc' ? 'selected' : '' ?>>Nominal terkecil</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <?php if ($adaFilter): ?>
                        <a href="<?= h(refund_url([], ['arsip' => $arsip ? 1 : ''])) ?>" class="btn btn-light border" title="Reset filter"><i class="bi bi-x-lg"></i></a>
                    <?php else: ?>
                        <button type="submit" class="btn btn-light border" title="Cari"><i class="bi bi-search"></i></button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if ($arsip): ?>
        <div class="alert alert-secondary small d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span><i class="bi bi-archive me-1"></i> Pengajuan di sini disembunyikan dari daftar utama, tetapi datanya tetap tersimpan dan status refund tamu tidak berubah. Klik <strong>Pulihkan</strong> untuk menampilkannya lagi.</span>
            <a href="admin_refund.php" class="btn btn-sm btn-light border">Kembali ke daftar refund</a>
        </div>
    <?php elseif ($stats['terhapus'] > 0): ?>
        <div class="mb-3">
            <a href="admin_refund.php?arsip=1" class="small text-muted text-decoration-none"><i class="bi bi-archive me-1"></i> Lihat pengajuan terhapus (<?= (int)$stats['terhapus'] ?>)</a>
        </div>
    <?php endif; ?>

    <!-- Tabel Pengajuan -->
    <div class="table-responsive bg-white rounded-3 shadow-sm border mb-4">
        <table class="admin-table">
            <thead class="bg-light">
                <tr>
                    <th>Info Tiket &amp; Kode Booking</th>
                    <th>Detail Tamu</th>
                    <th>Alasan Pembatalan</th>
                    <th>Nominal Refund</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$refunds): ?>
                    <tr><td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        <?= $arsip ? 'Tidak ada pengajuan terhapus.' : ($adaFilter ? 'Tidak ada pengajuan yang cocok. Ubah atau reset filter.' : 'Belum ada pengajuan refund.') ?>
                    </td></tr>
                <?php endif; ?>
                <?php foreach ($refunds as $r): [$cls, $ico, $label] = refund_badge($r['status']); ?>
                <tr>
                    <td>
                        <strong class="<?= $r['status'] === 'Diajukan' ? 'text-danger' : 'text-dark' ?> d-block"><?= h(refund_kode((int)$r['id'])) ?></strong>
                        <small class="text-muted" style="font-size: 0.65rem;">Booking: <?= h($r['kode_booking']) ?></small>
                        <small class="text-muted d-block mt-1" style="font-size: 0.65rem;"><i class="bi bi-clock"></i> Diajukan: <?= h(date('d M Y, H.i', strtotime($r['diajukan_at']))) ?> WIB</small>
                        <?php if ($arsip && $r['dihapus_pada']): ?>
                            <small class="text-danger d-block" style="font-size: 0.65rem;"><i class="bi bi-trash3"></i> Dihapus: <?= h(date('d M Y, H.i', strtotime($r['dihapus_pada']))) ?> WIB</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong class="d-block text-dark small"><?= h($r['nama_pemesan']) ?></strong>
                        <small class="text-muted" style="font-size: 0.65rem;"><?= h($r['whatsapp_pemesan']) ?></small>
                    </td>
                    <td style="max-width: 200px;">
                        <span class="d-block text-truncate small text-muted" title="<?= h($r['alasan']) ?>"><?= h($r['alasan']) ?></span>
                    </td>
                    <td><strong class="d-block text-brown"><?= h(rupiah($r['total_tagihan'])) ?></strong></td>
                    <td><span class="badge <?= $cls ?> rounded-pill py-1 px-3"><i class="bi <?= $ico ?> me-1" style="font-size:0.5rem;"></i> <?= $label ?></span></td>
                    <td class="text-nowrap">
                        <?php if ($arsip): ?>
                            <a href="admin_refund_tinjau.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm text-muted" title="Lihat detail" aria-label="Lihat detail <?= h(refund_kode((int)$r['id'])) ?>"><i class="bi bi-eye"></i></a>
                            <form method="post" action="admin_refund.php" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="aksi" value="pulihkan">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-brown"><i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan</button>
                            </form>
                        <?php else: ?>
                            <?php if ($r['status'] === 'Diajukan'): ?>
                                <a href="admin_refund_tinjau.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-brown fw-bold" style="font-size: 0.7rem;">Tinjau</a>
                            <?php else: ?>
                                <a href="admin_refund_tinjau.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm text-muted" title="Lihat detail" aria-label="Lihat detail <?= h(refund_kode((int)$r['id'])) ?>"><i class="bi bi-eye"></i></a>
                            <?php endif; ?>
                            <?php if ($r['boleh_dihapus']): ?>
                                <button type="button" class="btn btn-sm text-muted" title="Hapus pengajuan" aria-label="Hapus Pengajuan <?= h(refund_kode((int)$r['id'])) ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusRefund"
                                        data-id="<?= (int)$r['id'] ?>" data-kode="<?= h(refund_kode((int)$r['id'])) ?>" data-nama="<?= h($r['nama_pemesan']) ?>" data-status="<?= h($r['status']) ?>"><i class="bi bi-trash"></i></button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($refunds): ?>
        <small class="text-muted">Menampilkan <?= count($refunds) ?> pengajuan</small>
    <?php endif; ?>
</div>

<!-- ===================== Modal Hapus Refund ===================== -->
<div class="modal fade" id="modalHapusRefund" tabindex="-1" aria-labelledby="judulModalHapusRefund" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="admin_refund.php" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id" id="hapusRefundId" value="">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="judulModalHapusRefund">Hapus Pengajuan <span id="hapusRefundKode"></span>?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body small">
                <p class="mb-2">Pengajuan refund atas nama <strong id="hapusRefundNama"></strong> akan <strong>disembunyikan</strong> dari daftar ini.</p>
                <p class="text-warning-emphasis bg-warning-subtle rounded-3 px-3 py-2 d-none" id="hapusRefundPeringatan"><i class="bi bi-exclamation-triangle me-1"></i> Pengajuan ini <strong>masih menunggu tinjauan</strong>. Setelah disembunyikan, pengajuan tidak muncul di daftar dan tidak bisa ditinjau sampai dipulihkan. Status refund tamu tetap "Menunggu Tinjauan".</p>
                <p class="text-muted mb-0"><i class="bi bi-archive me-1"></i> Datanya tidak benar-benar dihapus: status refund tamu tidak berubah, dan pengajuan bisa dipulihkan dari "Lihat pengajuan terhapus".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold"><i class="bi bi-trash me-1"></i> Hapus dari Tampilan</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('modalHapusRefund');
    if (!modal) return;
    document.body.appendChild(modal); // pindah ke <body> agar backdrop tidak tertutup layout admin
    modal.addEventListener('show.bs.modal', function (ev) {
        var btn = ev.relatedTarget;
        if (!btn) return;
        document.getElementById('hapusRefundId').value = btn.getAttribute('data-id') || '';
        document.getElementById('hapusRefundKode').textContent = btn.getAttribute('data-kode') || '';
        document.getElementById('hapusRefundNama').textContent = btn.getAttribute('data-nama') || '';
        document.getElementById('hapusRefundPeringatan').classList.toggle('d-none', btn.getAttribute('data-status') !== 'Diajukan');
    });
})();
</script>