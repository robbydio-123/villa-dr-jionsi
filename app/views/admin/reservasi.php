<?php
?>


<style>
    .wa-chat-link:hover { background-color: #198754 !important; color: #fff !important; }
</style>

<div class="admin-content">

    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-shield-check text-brown"></i>
                <small class="text-muted fw-bold" style="font-size: 0.7rem;">SISTEM RESERVASI HIGHLAND BATU</small>
            </div>
            <h3 class="fw-bolder mb-1"><?= $arsip ? 'Reservasi Terhapus' : 'Manajemen Reservasi Tamu' ?></h3>
            <p class="text-muted small mb-0">Pantau seluruh alur keep, verifikasi bukti transfer, dan status pembayaran kamar secara real-time.</p>
        </div>
        <div class="d-flex flex-column gap-2 text-md-end">
            <div class="d-flex gap-3 bg-white border p-2 rounded shadow-sm">
                <div class="px-2 border-end text-start">
                    <small class="text-muted d-block" style="font-size: 0.65rem;"><i class="bi bi-journal-check me-1"></i> Total Reservasi</small>
                    <span class="fw-bold"><?= count($reservasiRows) ?> Booking</span>
                </div>
                <div class="px-2 text-start">
                    <small class="text-warning fw-bold d-block" style="font-size: 0.65rem;"><i class="bi bi-exclamation-circle me-1"></i> Perlu Verifikasi</small>
                    <span class="fw-bold text-danger"><?php
                        echo count(array_filter($reservasiRows, static fn(array $row): bool =>
                            $row['status_booking'] === 'Menunggu Konfirmasi'
                            && $row['status_verifikasi_terakhir'] === 'Menunggu'
                        ));
                    ?> Booking</span>
                </div>
            </div>
            <!-- [DIUBAH 7] tombol ekspor: diberi id + type="button", teks jadi "Excel (.xlsx)" -->
            <button type="button" id="btnEksporExcel" class="btn btn-light border btn-sm fw-semibold">
                <i class="bi bi-file-earmark-excel text-success me-1"></i> Ekspor Laporan (Excel)
            </button>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?> alert-dismissible fade show small" role="alert">
            <div class="d-flex flex-wrap align-items-center gap-2 pe-4">
                <span><?= h($flash['message']) ?></span>
                <?php if (!empty($flash['wa_pembayaran_id'])): ?>
                    <?= wa_sekali_form((int)$flash['wa_pembayaran_id'], (string)($flash['wa_label'] ?: 'Kirim WhatsApp'), 'btn btn-outline-success btn-sm bg-white') ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Tab Status -->
    <!-- [TAMBAH 9] tiap tab diberi id="filterStatus" + data-filter (angka dihitung otomatis oleh script) -->
    <div class="d-flex flex-wrap gap-2 mb-4" id="filterStatus">
        <a href="#" data-filter="semua" class="admin-filter-tab active">Semua <span class="badge-count">0</span></a>
        <a href="#" data-filter="keep" class="admin-filter-tab">Di-Keep <span class="badge-count">0</span></a>
        <a href="#" data-filter="menunggu" class="admin-filter-tab border-warning text-warning bg-warning bg-opacity-10"><i class="bi bi-circle-fill" style="font-size:0.4rem;"></i> Menunggu Konfirmasi <span class="badge-count bg-warning text-white">0</span></a>
        <a href="#" data-filter="dibayar" class="admin-filter-tab">Dibayar <span class="badge-count">0</span></a>
        <a href="#" data-filter="kedaluwarsa" class="admin-filter-tab">Kedaluwarsa <span class="badge-count">0</span></a>
        <a href="#" data-filter="dibatalkan" class="admin-filter-tab">Dibatalkan <span class="badge-count">0</span></a>
        <a href="#" data-filter="refund" class="admin-filter-tab border-danger text-danger bg-danger bg-opacity-10">Refund Diajukan <span class="badge-count bg-danger text-white">0</span></a>
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
        <!-- [DIUBAH 6] tombol reset urutan: diberi id + type="button" -->
        <button type="button" id="btnResetUrutan" class="btn btn-sm text-brown fw-semibold"><i class="bi bi-arrow-clockwise me-1"></i> Reset Urutan</button>
    </div>

    <div class="table-responsive bg-white rounded-3 shadow-sm border mb-4">
        <!-- [DIUBAH 1] tabel diberi id="tabelReservasi" -->
        <table class="admin-table" id="tabelReservasi">
            <thead class="bg-light">
                <tr>
                    <!-- [DIUBAH 6] tiap judul kolom (kecuali Keep) bisa diklik untuk mengurutkan -->
                    <th class="sortable">Kode Booking <i class="bi bi-arrow-down-up sort-ico"></i></th>
                    <th class="sortable">Pemesan <i class="bi bi-arrow-down-up sort-ico"></i></th>
                    <th class="sortable">WhatsApp <i class="bi bi-arrow-down-up sort-ico"></i></th>
                    <th class="sortable">Unit Kamar <i class="bi bi-arrow-down-up sort-ico"></i></th>
                    <!-- [DIUBAH 8] "Jadwal Menginap" -> "Check In" + "Check Out" -->
                    <th class="sortable">Check In <i class="bi bi-arrow-down-up sort-ico"></i></th>
                    <th class="sortable">Check Out <i class="bi bi-arrow-down-up sort-ico"></i></th>
                    <th class="sortable" data-type="num">Total Tagihan <i class="bi bi-arrow-down-up sort-ico"></i></th>
                    <th>Keep / Sisa Waktu</th>
                    <th class="sortable">Status Booking <i class="bi bi-arrow-down-up sort-ico"></i></th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $statusMeta = [
                    'Di-Keep' => ['keep', 'bg-light text-dark border', 'text-secondary'],
                    'Menunggu Konfirmasi' => ['menunggu', 'bg-warning bg-opacity-10 text-brown border border-warning-subtle', 'text-warning'],
                    'Dibayar' => ['dibayar', 'bg-success bg-opacity-10 text-success border border-success-subtle', 'text-success'],
                    'Kedaluwarsa' => ['kedaluwarsa', 'bg-light text-dark border', 'text-secondary'],
                    'Dibatalkan' => ['dibatalkan', 'bg-light text-dark border', 'text-secondary'],
                    'Refund Diajukan' => ['refund', 'bg-danger bg-opacity-10 text-danger border border-danger-subtle', 'text-danger'],
                ];
                foreach ($reservasiRows as $row):
                    $bookingStatus = (string)$row['status_booking'];
                    [$filterStatus, $badgeClass, $iconClass] = $statusMeta[$bookingStatus]
                        ?? ['semua', 'bg-light text-dark border', 'text-secondary'];
                    $nameParts = preg_split('/\s+/', trim((string)$row['nama_pemesan'])) ?: [];
                    $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                    $durationLabel = (int)$row['durasi_jam'] < 24
                        ? (int)$row['durasi_jam'] . ' jam'
                        : (int)ceil((int)$row['durasi_jam'] / 24) . ' malam';
                    $checkinSort = date('Y-m-d', strtotime((string)$row['waktu_checkin']));
                    $checkoutSort = date('Y-m-d', strtotime((string)$row['waktu_checkout']));
                    $remainingSeconds = (int)$row['sisa_keep'];
                ?>
                    <tr data-malam="<?= (int)ceil((int)$row['durasi_jam'] / 24) ?>" data-status="<?= h($filterStatus) ?>">
                        <td>
                            <strong class="<?= $bookingStatus === 'Dibayar' ? 'text-success' : 'text-brown' ?> d-block"><?= h($row['kode_booking']) ?></strong>
                            <small class="text-muted"><?= $row['status_verifikasi_terakhir'] ? 'Bukti: ' . h($row['status_verifikasi_terakhir']) : 'Belum ada pembayaran' ?></small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initial"><?= h($initials) ?></div>
                                <div>
                                    <strong class="d-block"><?= h($row['nama_pemesan']) ?></strong>
                                    <small class="text-muted"><?= (int)$row['jumlah_tamu'] ?> Tamu</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if (trim((string)$row['whatsapp_pemesan']) !== ''): ?>
                                <a class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1 text-decoration-none wa-chat-link"
                                   href="<?= h(wa_link((string)$row['whatsapp_pemesan'])) ?>" target="_blank" rel="noopener noreferrer"
                                   title="Buka chat WhatsApp dengan <?= h($row['nama_pemesan']) ?>">
                                    <i class="bi bi-whatsapp me-1"></i> <?= h($row['whatsapp_pemesan']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong class="d-block">Unit <?= h($row['nomor_kamar'] ?? '-') ?> <?= h($row['tipe_kamar'] ?? '') ?></strong>
                            <small class="text-muted">Lantai <?= (int)($row['lantai'] ?? 0) ?></small>
                        </td>
                        <td data-sort="<?= h($checkinSort) ?>"><strong><?= date('d/m/Y H:i', strtotime((string)$row['waktu_checkin'])) ?></strong></td>
                        <td data-sort="<?= h($checkoutSort) ?>"><strong><?= date('d/m/Y H:i', strtotime((string)$row['waktu_checkout'])) ?></strong> <small class="text-muted">(<?= h($durationLabel) ?>)</small></td>
                        <td data-sort="<?= (float)$row['total_tagihan'] ?>"><strong>Rp <?= number_format((float)$row['total_tagihan'], 0, ',', '.') ?></strong></td>
                        <td>
                            <?php if ($bookingStatus === 'Di-Keep' && $remainingSeconds > 0): ?>
                                <span class="badge bg-white text-danger border border-danger-subtle"><i class="bi bi-stopwatch"></i> <?= intdiv($remainingSeconds, 60) ?>:<?= str_pad((string)($remainingSeconds % 60), 2, '0', STR_PAD_LEFT) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge <?= h($badgeClass) ?> rounded-pill py-2 px-3"><i class="bi bi-circle-fill <?= h($iconClass) ?> me-1" style="font-size:0.4rem;"></i> <?= h($bookingStatus) ?></span></td>
                        <td class="text-nowrap">
                            <?php if (!$arsip && $bookingStatus === 'Menunggu Konfirmasi' && $row['status_verifikasi_terakhir'] === 'Menunggu' && !empty($row['pembayaran_id_terakhir'])): ?>
                                <button type="button" class="btn btn-sm btn-brown fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#modalBukti"
                                        data-reservasi="<?= (int)$row['id'] ?>"
                                        data-pembayaran="<?= (int)$row['pembayaran_id_terakhir'] ?>"
                                        data-kode="<?= h($row['kode_booking']) ?>"
                                        data-nama="<?= h($row['nama_pemesan']) ?>"
                                        data-kamar="<?= h('Kamar ' . ($row['nomor_kamar'] ?? '-') . ' ' . ($row['tipe_kamar'] ?? '')) ?>"
                                        data-nominal="<?= h('Rp ' . number_format((float)$row['nominal_terakhir'], 0, ',', '.')) ?>"
                                        data-tagihan="<?= h('Rp ' . number_format((float)$row['total_tagihan'], 0, ',', '.')) ?>"
                                        data-menginap="<?= h(date('d M Y H:i', strtotime((string)$row['waktu_checkin'])) . ' - ' . date('d M Y H:i', strtotime((string)$row['waktu_checkout']))) ?>"
                                        data-bukti="admin_bukti.php?id=<?= (int)$row['pembayaran_id_terakhir'] ?>">
                                    <i class="bi bi-shield-check me-1"></i> Periksa Bukti
                                </button>
                            <?php elseif (!$arsip && $bookingStatus === 'Di-Keep'): ?>
                                <form method="post" action="admin_reservasi.php" class="d-inline"
                                      onsubmit="return confirm('Batalkan keep untuk reservasi <?= h($row['kode_booking']) ?>?');">
                                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                    <input type="hidden" name="aksi" value="batalkan">
                                    <input type="hidden" name="reservasi_id" value="<?= (int)$row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">Batalkan Keep</button>
                                </form>
                            <?php endif; ?>
                            <?php
                            // WhatsApp hanya tampil selama belum pernah dikirim untuk bukti terakhir (sekali saja).
                            $waBelum = !$arsip && !empty($row['whatsapp_pemesan']) && !empty($row['pembayaran_id_terakhir'])
                                && empty($row['wa_dikirim_terakhir']);
                            $labelWa = null;
                            if ($bookingStatus === 'Dibayar' && $row['status_verifikasi_terakhir'] === 'Terverifikasi') {
                                $labelWa = 'WA Konfirmasi';
                            } elseif ($bookingStatus === 'Menunggu Konfirmasi' && $row['status_verifikasi_terakhir'] === 'Ditolak') {
                                $labelWa = 'WA Penolakan';
                            }
                            if ($waBelum && $labelWa !== null) {
                                echo wa_sekali_form((int)$row['pembayaran_id_terakhir'], $labelWa, 'btn btn-sm btn-outline-success');
                            } elseif (!$arsip && !empty($row['wa_dikirim_terakhir']) && in_array($row['status_verifikasi_terakhir'], ['Ditolak', 'Terverifikasi'], true) && in_array($bookingStatus, ['Menunggu Konfirmasi', 'Dibayar'], true)) {
                                echo '<span class="small text-success"><i class="bi bi-check2-circle me-1"></i>WA terkirim</span>';
                            } elseif (!$arsip && $bookingStatus === 'Menunggu Konfirmasi' && $row['status_verifikasi_terakhir'] === 'Ditolak' && empty($row['whatsapp_pemesan'])) {
                                echo '<span class="small text-muted">Bukti ditolak, no. WA kosong</span>';
                            }
                            ?>
                            <?php if ($arsip): ?>
                                <form method="post" action="admin_reservasi.php" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                    <input type="hidden" name="aksi" value="pulihkan">
                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-brown"><i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan</button>
                                </form>
                            <?php elseif ($row['boleh_dihapus']): ?>
                                <button type="button" class="btn btn-sm text-muted" title="Hapus reservasi" aria-label="Hapus Reservasi <?= h($row['kode_booking']) ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusReservasi"
                                        data-id="<?= (int)$row['id'] ?>" data-kode="<?= h($row['kode_booking']) ?>" data-nama="<?= h($row['nama_pemesan']) ?>"><i class="bi bi-trash"></i></button>
                            <?php elseif (!in_array($bookingStatus, ['Dibayar', 'Menunggu Konfirmasi', 'Di-Keep'], true)): ?>
                                <span class="text-muted" title="Reservasi yang masih berjalan tidak bisa dihapus">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- [TAMBAH 9] pesan jika tidak ada data pada status yang dipilih -->
        <div id="pesanKosong" class="text-center text-muted small py-4" style="display:none;">Tidak ada reservasi dengan status ini.</div>
    </div>

    <div class="mb-4">
        <?php if ($arsip): ?>
            <a href="admin_reservasi.php" class="small text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Kembali ke daftar reservasi</a>
        <?php else: ?>
            <a href="admin_reservasi.php?arsip=1" class="small text-muted text-decoration-none"><i class="bi bi-archive me-1"></i> Lihat reservasi terhapus (<?= (int)$arsipCount ?>)</a>
        <?php endif; ?>
    </div>

<!-- ===================== Modal Hapus Reservasi ===================== -->
<div class="modal fade" id="modalHapusReservasi" tabindex="-1" aria-labelledby="judulModalHapusReservasi" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="admin_reservasi.php" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id" id="hapusReservasiId" value="">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="judulModalHapusReservasi">Hapus Reservasi <span id="hapusReservasiKode"></span>?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body small">
                <p class="mb-2">Reservasi atas nama <strong id="hapusReservasiNama"></strong> akan <strong>disembunyikan</strong> dari daftar ini.</p>
                <p class="text-muted mb-0"><i class="bi bi-archive me-1"></i> Datanya tidak benar-benar dihapus: bukti pembayaran dan riwayat statusnya tetap tersimpan, dan reservasi bisa dipulihkan dari "Lihat reservasi terhapus".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold"><i class="bi bi-trash me-1"></i> Hapus dari Tampilan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== Modal Periksa Bukti ===================== -->
<div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="modalBuktiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="post" action="admin_reservasi.php">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="reservasi_id" id="bReservasi">
                <input type="hidden" name="pembayaran_id" id="bPembayaran">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalBuktiLabel">Periksa Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a id="bLink" href="#" target="_blank" rel="noopener" class="bukti-frame">
                                <img id="bImg" src="" alt="Bukti pembayaran">
                                <span id="bGagal" class="text-muted small d-none">Gambar bukti tidak dapat dimuat.</span>
                            </a>
                            <small class="text-muted d-block mt-1">Klik gambar untuk membuka ukuran penuh.</small>
                        </div>
                        <div class="col-md-6 small">
                            <dl class="mb-0 bukti-detail">
                                <dt>Kode booking</dt><dd id="bKode"></dd>
                                <dt>Pemesan</dt><dd id="bNama"></dd>
                                <dt>Kamar</dt><dd id="bKamar"></dd>
                                <dt>Jadwal menginap</dt><dd id="bMenginap"></dd>
                                <dt>Total tagihan</dt><dd id="bTagihan" class="fw-bold"></dd>
                                <dt>Nominal tercatat</dt><dd id="bNominal"></dd>
                            </dl>
                            <div class="alert alert-light border small mt-3 mb-0">
                                Cocokkan nominal dan waktu transfer di gambar dengan tagihan. <strong>Tolak</strong> akan meminta tamu
                                mengunggah ulang selama waktu keep belum habis.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="aksi" value="tolak" class="btn btn-outline-danger fw-bold"
                            onclick="return confirm('Tolak bukti pembayaran ini?');">Tolak Bukti</button>
                    <button type="submit" name="aksi" value="konfirmasi" class="btn btn-brown fw-bold"
                            onclick="return confirm('Konfirmasi pembayaran ini sebagai LUNAS?');">Konfirmasi Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- [DIUBAH 7] Library Excel (SheetJS) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="assets/js/admin_reservasi.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/admin_reservasi.js') ?>"></script>