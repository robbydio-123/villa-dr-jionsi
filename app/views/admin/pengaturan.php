<?php
if (!isset($settings) || !is_array($settings)) {
    echo '<div class="alert alert-danger m-4">Halaman Pengaturan harus dibuka lewat <code>public/admin_pengaturan.php</code> versi terbaru. Perbarui file tersebut.</div>';
    return;
}

$qrisFile = $qris ? dirname(__DIR__, 3) . '/public' . $qris['gambar'] : null;
?>
<div class="admin-content" style="max-width: 900px; margin: 0 auto;">

    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bolder mb-1">Pengaturan Sistem</h2>
            <p class="text-muted small mb-0">Konfigurasi operasional villa, batas waktu reservasi, dan kontak WhatsApp.</p>
        </div>
        <button type="submit" form="formPengaturan" class="btn btn-brown fw-bold px-4 py-2"><i class="bi bi-save me-2"></i> Simpan Perubahan</button>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?> small"><?= h($flash['message']) ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger small">
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="admin_pengaturan.php" id="formPengaturan" novalidate>
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="aksi" value="simpan">

        <div class="settings-section sec-brown">
            <div class="d-flex gap-3 mb-4">
                <div class="settings-icon-box ico-brown"><i class="bi bi-buildings"></i></div>
                <div>
                    <h5 class="fw-bold mb-1">Informasi Villa</h5>
                    <small class="text-muted">Informasi identitas properti dan saluran kontak resmi untuk tamu &amp; pengajuan.</small>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Nama Properti <span class="text-danger">*</span></label>
                    <div class="input-group drj-ig ig-brown">
                        <span class="input-group-text ig-icon"><i class="bi bi-building"></i></span>
                        <input type="text" name="nama_properti" class="form-control" maxlength="100" required
                               value="<?= h($settings['nama_properti']) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Nomor WhatsApp Utama / Refund <span class="text-danger">*</span></label>
                    <div class="input-group drj-ig ig-green">
                        <span class="input-group-text ig-icon"><i class="bi bi-whatsapp"></i></span>
                        <input type="text" name="whatsapp_utama" class="form-control" maxlength="20" required
                               value="<?= h($settings['whatsapp_utama']) ?>">
                    </div>
                    <small class="drj-hint"><i class="bi bi-info-circle"></i> Nomor kontak resmi WhatsApp pengajuan refund pelanggan.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Email Operasional</label>
                    <div class="input-group drj-ig ig-blue">
                        <span class="input-group-text ig-icon"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email_operasional" class="form-control" maxlength="100"
                               value="<?= h($settings['email_operasional'] ?? '') ?>">
                    </div>
                    <small class="drj-hint"><i class="bi bi-info-circle"></i> Laporan harian dan konfirmasi sistem dikirim ke email ini.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Alamat Lengkap</label>
                    <div class="input-group drj-ig ig-red">
                        <span class="input-group-text ig-icon"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" name="alamat_lengkap" class="form-control" maxlength="500"
                               value="<?= h($settings['alamat_lengkap'] ?? '') ?>">
                    </div>
                    <small class="drj-hint"><i class="bi bi-info-circle"></i> Lokasi fisik properti yang tercantum pada voucher tamu.</small>
                </div>
            </div>
        </div>

        <div class="settings-section sec-amber">
            <div class="d-flex gap-3 mb-4">
                <div class="settings-icon-box ico-amber"><i class="bi bi-clock-history"></i></div>
                <div>
                    <h5 class="fw-bold mb-1">Kebijakan Reservasi</h5>
                    <small class="text-muted">Aturan otomatisasi durasi keep kamar dan regulasi batas pembatalan sewa.</small>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Batas Waktu Pembayaran (Sesi Keep)</label>
                    <div class="input-group drj-ig ig-amber">
                        <span class="input-group-text ig-icon"><i class="bi bi-hourglass-split"></i></span>
                        <input type="number" name="batas_waktu_keep_menit" class="form-control" min="1" max="180" required
                               value="<?= (int)$settings['batas_waktu_keep_menit'] ?>">
                        <span class="input-group-text ig-suffix">Menit</span>
                    </div>
                    <small class="drj-hint"><i class="bi bi-stopwatch"></i> Kamar otomatis dilepas ke publik jika pembayaran tidak diverifikasi dalam <?= (int)$settings['batas_waktu_keep_menit'] ?> menit. Berlaku untuk reservasi baru.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Batas Maksimal Pengajuan Refund</label>
                    <div class="input-group drj-ig ig-amber">
                        <span class="input-group-text ig-icon"><i class="bi bi-calendar-x"></i></span>
                        <input type="text" class="form-control" value="H-1 setelah pemesanan dibuat (24 jam)" readonly>
                    </div>

                    <small class="drj-hint"><i class="bi bi-lock"></i> Aturan tetap, tidak dapat diubah.</small>
                    <div class="drj-note mt-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <small><strong>Catatan Operasional:</strong> Sistem refund dilakukan manual via WhatsApp. Sistem admin hanya mencatat status dan log persetujuan.</small>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="settings-section sec-red mb-4">
        <div class="d-flex gap-3 mb-4">
            <div class="settings-icon-box ico-red"><i class="bi bi-wallet2"></i></div>
            <div>
                <h5 class="fw-bold mb-1">Pembayaran QRIS</h5>
                <small class="text-muted">QRIS statis yang ditampilkan kepada tamu saat pembayaran. Hanya satu QRIS yang aktif.</small>
            </div>
        </div>

        <div class="account-box acc-qris">
            <div class="acc-ico"><i class="bi bi-qr-code-scan"></i></div>
            <div class="flex-fill">
                <span class="acc-chip">QRIS</span>
                <strong class="d-block small mt-2">QRIS Statis Aktif</strong>

                <?php if ($qris && $qrisFile && is_file($qrisFile)): ?>
                    <img src="<?= h(ltrim($qris['gambar'], '/')) ?>" alt="QRIS aktif" class="img-fluid my-2" style="max-width: 200px;">
                    <small class="d-block text-muted" style="font-size: 0.72rem;">Diunggah <?= h(date('d/m/Y H:i', strtotime((string)$qris['created_at']))) ?></small>
                <?php else: ?>
                    <div class="alert alert-warning small py-2 my-2">Belum ada file QRIS aktif. Tamu tidak bisa melihat barcode pembayaran sampai kamu mengunggahnya.</div>
                <?php endif; ?>

                <form method="post" action="admin_pengaturan.php" enctype="multipart/form-data" class="mt-2">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="aksi" value="upload_qris">
                    <div class="input-group input-group-sm">
                        <input type="file" name="qris" class="form-control" accept="image/jpeg,image/png,image/webp" required>
                        <button type="submit" class="btn btn-outline-brown fw-semibold"><i class="bi bi-upload me-1"></i> Ganti QRIS</button>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">JPG, PNG, atau WEBP. Maksimal 2 MB. QRIS lama otomatis disimpan sebagai riwayat.</small>
                </form>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center pt-3 border-top pb-5">
        <small class="text-muted" style="font-size: 0.75rem;">
            <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.4rem;"></i>
            Terakhir diperbarui <?= h(date('d/m/Y H:i', strtotime((string)($settings['updated_at'] ?? 'now')))) ?>
        </small>
        <div class="d-flex gap-2">
            <a href="admin_pengaturan.php" class="btn btn-light border fw-bold px-4">Batal</a>
            <button type="submit" form="formPengaturan" class="btn btn-brown fw-bold px-4">Simpan Perubahan</button>
        </div>
    </div>
</div>