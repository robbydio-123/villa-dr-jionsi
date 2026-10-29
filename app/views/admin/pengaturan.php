<div class="admin-content" style="max-width: 900px; margin: 0 auto;">
    
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bolder mb-1">Pengaturan Sistem</h2>
            <p class="text-muted small mb-0">Konfigurasi operasional villa, batas waktu reservasi, dan kontak WhatsApp.</p>
        </div>
        <button class="btn btn-brown fw-bold px-4 py-2"><i class="bi bi-save me-2"></i> Simpan Perubahan</button>
    </div>

    <!-- Section 1: Informasi Villa -->
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
                    <input type="text" class="form-control" value="Villa D-R Jionsi">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Nomor WhatsApp Utama / Refund</label>
                <div class="input-group drj-ig ig-green">
                    <span class="input-group-text ig-icon"><i class="bi bi-whatsapp"></i></span>
                    <input type="text" class="form-control" value="+62 812-3344-9081">
                </div>
                <small class="drj-hint"><i class="bi bi-info-circle"></i> Nomor kontak resmi WhatsApp pengajuan refund pelanggan.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Email Operasional</label>
                <div class="input-group drj-ig ig-blue">
                    <span class="input-group-text ig-icon"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" value="info@drjionsi.com">
                </div>
                <small class="drj-hint"><i class="bi bi-info-circle"></i> Laporan harian dan konfirmasi sistem dikirim ke email ini.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Alamat Lengkap</label>
                <div class="input-group drj-ig ig-red">
                    <span class="input-group-text ig-icon"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" class="form-control" value="Batu Highland Retreat, Jl. Songgoriti No. 18, Songgokerto">
                </div>
                <small class="drj-hint"><i class="bi bi-info-circle"></i> Lokasi fisik properti yang tercantum pada voucher tamu.</small>
            </div>
        </div>
    </div>

    <!-- Section 2: Kebijakan Reservasi -->
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
                    <input type="number" class="form-control" value="15">
                    <span class="input-group-text ig-suffix">Menit</span>
                </div>
                <small class="drj-hint"><i class="bi bi-stopwatch"></i> Kamar otomatis dilepas ke publik jika pembayaran tidak diverifikasi dalam 15 menit.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Batas Maksimal Pengajuan Refund</label>
                <div class="input-group drj-ig ig-amber">
                    <span class="input-group-text ig-icon"><i class="bi bi-calendar-x"></i></span>
                    <select class="form-select">
                        <option selected>H-1 Check-in pukul 23.59 WIB</option>
                        <option>H-2 Check-in pukul 23.59 WIB</option>
                        <option>H-3 Check-in pukul 23.59 WIB</option>
                        <option>Non-Refundable (Tidak bisa batal)</option>
                    </select>
                </div>

                <div class="drj-note mt-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    <small><strong>Catatan Operasional:</strong> Sistem refund dilakukan manual via WhatsApp. Sistem admin hanya mencatat status dan log persetujuan.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Rekening Penerima -->
    <div class="settings-section sec-red mb-4">
        <div class="d-flex gap-3 mb-4">
            <div class="settings-icon-box ico-red"><i class="bi bi-wallet2"></i></div>
            <div>
                <h5 class="fw-bold mb-1">Rekening Penerima</h5>
                <small class="text-muted">Daftar kanal pembayaran resmi yang ditampilkan kepada tamu saat checkout.</small>
            </div>
        </div>

        <!-- Akun BCA -->
        <div class="account-box acc-bca">
            <div class="acc-ico"><i class="bi bi-bank2"></i></div>
            <div class="flex-fill">
                <span class="acc-chip">BCA</span>
                <div class="row g-2 mt-1">
                    <div class="col-md-6">
                        <label class="acc-label">Nomor Rekening</label>
                        <input type="text" class="form-control form-control-sm" value="1234567890">
                    </div>
                    <div class="col-md-6">
                        <label class="acc-label">Nama Pemilik</label>
                        <input type="text" class="form-control form-control-sm" value="PT Jionsi Retreat">
                    </div>
                </div>
            </div>
            <button type="button" class="btn-acc-del" title="Hapus"><i class="bi bi-trash"></i></button>
        </div>

        <!-- Akun QRIS -->
        <div class="account-box acc-qris">
            <div class="acc-ico"><i class="bi bi-qr-code-scan"></i></div>
            <div class="flex-fill">
                <span class="acc-chip">QRIS</span>
                <strong class="d-block small mt-2">QRIS Statis Interaktif</strong>
                <small class="text-muted" style="font-size: 0.72rem;">Semua E-Wallet &amp; Mobile Banking (BCA, GoPay, OVO, Dana)</small>
                <div class="mt-2">
                    <button type="button" class="btn btn-outline-brown btn-sm fw-semibold"><i class="bi bi-eye me-1"></i> Lihat / Ganti Barcode QRIS</button>
                </div>
            </div>
            <button type="button" class="btn-acc-del" title="Hapus"><i class="bi bi-trash"></i></button>
        </div>

        <!-- Tombol Tambah Rekening -->
        <button type="button" class="btn-dashed-add mt-2"><i class="bi bi-plus-circle me-2"></i> Tambah Rekening / Metode Baru</button>
    </div>
    
    <!-- Footer Aksi -->
    <div class="d-flex justify-content-between align-items-center pt-3 border-top pb-5">
        <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.4rem;"></i> Versi Pengaturan: 1.4.2 &bull; Terakhir diperbarui 24 Okt 2026</small>
        <div class="d-flex gap-2">
            <button class="btn btn-light border fw-bold px-4">Batal</button>
            <button class="btn btn-brown fw-bold px-4">Simpan Perubahan</button>
        </div>
    </div>
</div>