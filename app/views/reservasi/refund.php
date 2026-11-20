<main class="container refund-user-page py-4 py-lg-5">
    <nav aria-label="Breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="beranda.php">Beranda</a></li>
            <li class="breadcrumb-item"><a href="hasil_status.php?kode=<?= rawurlencode($booking['kode_booking']) ?>">Status reservasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengajuan refund</li>
        </ol>
    </nav>

    <header class="refund-user-intro mb-4">
        <div class="refund-user-icon" aria-hidden="true"><i class="bi bi-arrow-counterclockwise"></i></div>
        <div>
            <span class="refund-user-eyebrow">LAYANAN TAMU</span>
            <h1 class="refund-user-title">Ajukan refund</h1>
            <p class="text-muted mb-0">Sampaikan alasan dan rekening tujuan pengembalian dana untuk reservasi Anda.</p>
        </div>
    </header>

    <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill mt-1" aria-hidden="true"></i>
            <div><?= h($error) ?></div>
        </div>
    <?php endif; ?>

    <div class="row g-4 align-items-stretch">
        <aside class="col-lg-5">
            <section class="refund-user-panel h-100" aria-labelledby="refundBookingTitle">
                <div class="refund-user-panel-head">
                    <span class="refund-user-section-label">RINGKASAN RESERVASI</span>
                    <h2 id="refundBookingTitle" class="h5 fw-bold mb-0">Detail booking</h2>
                </div>
                <div class="refund-user-panel-body">
                    <div class="refund-user-booking-code mb-4">
                        <span class="small text-muted d-block mb-1">KODE BOOKING</span>
                        <strong><?= h($booking['kode_booking']) ?></strong>
                    </div>

                    <div class="refund-user-room mb-4">
                        <span class="refund-user-room-icon"><i class="bi bi-door-closed"></i></span>
                        <div>
                            <span class="small text-muted d-block">Kamar</span>
                            <strong><?= h($booking['nomor_kamar']) ?> · <?= h($booking['tipe_kamar']) ?></strong>
                        </div>
                    </div>

                    <dl class="refund-user-details">
                        <div>
                            <dt><i class="bi bi-box-arrow-in-right me-2"></i>Check-in</dt>
                            <dd><?= h(date('d M Y, H:i', strtotime((string)$booking['waktu_checkin']))) ?></dd>
                        </div>
                        <div>
                            <dt><i class="bi bi-box-arrow-right me-2"></i>Check-out</dt>
                            <dd><?= h(date('d M Y, H:i', strtotime((string)$booking['waktu_checkout']))) ?></dd>
                        </div>
                        <div>
                            <dt><i class="bi bi-moon me-2"></i>Durasi</dt>
                            <dd><?= (int)$booking['durasi_jam'] ?> jam</dd>
                        </div>
                        <div class="refund-user-total">
                            <dt>Total pembayaran</dt>
                            <dd>Rp <?= number_format((float)$booking['total_tagihan'], 0, ',', '.') ?></dd>
                        </div>
                    </dl>

                    <div class="refund-user-flow">
                        <span class="refund-user-section-label">PROSES PENGEMBALIAN DANA</span>
                        <ol>
                            <li><span>1</span><div><strong>Pengajuan ditinjau</strong><small>Pengelola memeriksa alasan dan data reservasi Anda.</small></div></li>
                            <li><span>2</span><div><strong>Dana ditransfer</strong><small>Jika disetujui, dana dikirim ke rekening yang Anda isi.</small></div></li>
                            <li><span>3</span><div><strong>Bukti dikirim</strong><small>Bukti transfer dikirim ke WhatsApp Anda.</small></div></li>
                        </ol>
                    </div>

                    <div class="refund-user-deadline mt-4">
                        <i class="bi bi-clock-history" aria-hidden="true"></i>
                        <div>
                            <strong>Batas pengajuan</strong>
                            <span><?= h($refundDeadline->format('d M Y, H:i')) ?> WIB</span>
                        </div>
                    </div>
                </div>
            </section>
        </aside>

        <div class="col-lg-7">
            <section class="refund-user-panel h-100" aria-labelledby="refundFormTitle">
                <div class="refund-user-panel-head">
                    <span class="refund-user-section-label">FORMULIR</span>
                    <h2 id="refundFormTitle" class="h5 fw-bold mb-0">Alasan &amp; rekening pengembalian</h2>
                </div>
                <div class="refund-user-panel-body">
                    <div class="refund-user-guidance mb-4">
                        <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                        <p class="mb-0">Pengajuan akan ditinjau oleh pengelola. Pengembalian dana diproses secara manual sesuai ketentuan yang berlaku.</p>
                    </div>

                    <form method="POST" action="refund.php?kode=<?= rawurlencode($booking['kode_booking']) ?>" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                        <input type="hidden" name="kode" value="<?= h($booking['kode_booking']) ?>">

                        <div class="refund-user-field">
                            <label for="rfKet" class="form-label fw-semibold">Alasan refund</label>
                            <textarea
                                id="rfKet"
                                name="alasan"
                                class="form-control refund-user-textarea"
                                rows="5"
                                minlength="5"
                                maxlength="2000"
                                aria-describedby="refundReasonHelp refundReasonCount"
                                placeholder="Tuliskan alasan pengajuan dengan jelas..."
                                required><?= h($_POST['alasan'] ?? '') ?></textarea>
                            <div class="d-flex justify-content-between gap-3 mt-2">
                                <small id="refundReasonHelp" class="text-muted">Minimal 5 karakter. Maksimal 2.000 karakter.</small>
                                <small id="refundReasonCount" class="text-muted text-nowrap"><span id="rfKetCount"><?= text_length((string)($_POST['alasan'] ?? '')) ?></span>/2000</small>
                            </div>
                        </div>

                        <fieldset class="refund-user-bank">
                            <legend><i class="bi bi-bank2" aria-hidden="true"></i> Rekening tujuan refund</legend>
                            <p class="refund-user-bank-note">Dana dikembalikan lewat transfer ke rekening atas nama Anda. Pastikan datanya benar.</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="rfBank" class="form-label fw-semibold">Bank / e-wallet</label>
                                    <select id="rfBank" name="bank_tujuan" class="form-select" required>
                                        <option value="" disabled hidden <?= empty($_POST['bank_tujuan']) ? 'selected' : '' ?>>Pilih bank atau e-wallet</option>
                                        <?php foreach (refund_bank_options() as $opt): ?>
                                            <option value="<?= h($opt) ?>" <?= (($_POST['bank_tujuan'] ?? '') === $opt) ? 'selected' : '' ?>><?= h($opt) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="rfNoRek" class="form-label fw-semibold">Nomor rekening</label>
                                    <input id="rfNoRek" name="no_rekening" type="text" inputmode="numeric" autocomplete="off"
                                           class="form-control" maxlength="24" placeholder="Contoh: 1234567890"
                                           value="<?= h($_POST['no_rekening'] ?? '') ?>" required>
                                </div>
                                <div class="col-12 d-none" id="rfBankLainWrap">
                                    <label for="rfBankLain" class="form-label fw-semibold">Nama bank</label>
                                    <input id="rfBankLain" name="bank_lain" type="text" maxlength="50" class="form-control"
                                           placeholder="Tuliskan nama bank Anda" value="<?= h($_POST['bank_lain'] ?? '') ?>">
                                </div>
                                <div class="col-12">
                                    <label for="rfNamaRek" class="form-label fw-semibold">Nama pemilik rekening</label>
                                    <input id="rfNamaRek" name="nama_pemilik_rekening" type="text" maxlength="100" autocomplete="off"
                                           class="form-control" placeholder="Sesuai nama di buku tabungan / aplikasi"
                                           value="<?= h($_POST['nama_pemilik_rekening'] ?? '') ?>" required>
                                </div>
                            </div>
                        </fieldset>

                        <div class="refund-user-actions">
                            <button class="btn btn-brown fw-bold" type="submit">
                                <i class="bi bi-send me-2" aria-hidden="true"></i>Kirim pengajuan
                            </button>
                            <a class="btn btn-outline-secondary" href="hasil_status.php?kode=<?= rawurlencode($booking['kode_booking']) ?>">
                                Kembali ke status
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</main>
<script src="assets/js/refund_tamu.js"></script>