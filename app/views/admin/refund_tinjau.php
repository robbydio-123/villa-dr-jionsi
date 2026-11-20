<?php
[$cls, $ico, $label] = refund_badge($refund['status']);
$menunggu  = $refund['status'] === 'Diajukan';
$disetujui = $refund['status'] === 'Disetujui';
$selesai   = $refund['status'] === 'Selesai';
$waUrl     = refund_wa_url($refund);
$nominal   = rupiah($refund['nominal_pembayaran'] ?? $refund['total_tagihan']);
?>
<div class="admin-content rf-page">
    <a href="admin_refund.php" class="rf-back"><i class="bi bi-arrow-left"></i> Kembali ke daftar refund</a>

    <div class="rf-head">
        <div>
            <span class="rf-eyebrow"><i class="bi bi-arrow-counterclockwise me-1"></i> TINJAU PENGAJUAN</span>
            <h3 class="rf-title"><?= h(refund_kode((int)$refund['id'])) ?></h3>
            <p class="rf-sub">Booking <strong><?= h($refund['kode_booking']) ?></strong> &middot; Diajukan <?= h(date('d M Y, H.i', strtotime($refund['diajukan_at']))) ?> WIB</p>
        </div>
        <span class="badge <?= $cls ?> rounded-pill py-2 px-3 align-self-start"><i class="bi <?= $ico ?> me-1"></i> <?= $label ?></span>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?> small"><?= h($flash['message']) ?></div>
    <?php endif; ?>

    <div class="row g-4 rf-grid">
        <!-- Kolom kiri -->
        <div class="col-lg-7 rf-col">
            <section class="rf-card">
                <header class="rf-card-head"><span class="rf-card-icon"><i class="bi bi-chat-left-text"></i></span><h6>Alasan Pembatalan</h6></header>
                <div class="rf-card-body">
                    <p class="rf-reason"><?= nl2br(h($refund['alasan'])) ?></p>
                </div>
            </section>

            <section class="rf-card rf-grow">
                <header class="rf-card-head"><span class="rf-card-icon"><i class="bi bi-calendar2-check"></i></span><h6>Detail Reservasi</h6></header>
                <div class="rf-card-body">
                    <dl class="rf-info">
                        <div><dt>Nama tamu</dt><dd><?= h($refund['nama_pemesan']) ?></dd></div>
                        <div><dt>WhatsApp</dt><dd><?= h($refund['whatsapp_pemesan']) ?></dd></div>
                        <div><dt>Kamar</dt><dd><?= h($refund['nomor_kamar']) ?> &middot; <?= h($refund['tipe_kamar']) ?></dd></div>
                        <div><dt>Check-in</dt><dd><?= h(date('d M Y, H.i', strtotime($refund['waktu_checkin']))) ?> WIB</dd></div>
                        <div><dt>Check-out</dt><dd><?= h(date('d M Y, H.i', strtotime($refund['waktu_checkout']))) ?> WIB</dd></div>
                        <div><dt>Jumlah tamu</dt><dd><?= (int)$refund['jumlah_tamu'] ?> orang</dd></div>
                        <div><dt>Total dibayar</dt><dd class="rf-money"><?= h($nominal) ?></dd></div>
                    </dl>
                </div>
            </section>
        </div>

        <!-- Kolom kanan -->
        <div class="col-lg-5 rf-col">
            <section class="rf-card">
                <header class="rf-card-head"><span class="rf-card-icon"><i class="bi bi-bank2"></i></span><h6>Rekening Tujuan Refund</h6></header>
                <div class="rf-card-body">
                    <?php if (!empty($refund['no_rekening'])): ?>
                        <div class="rf-amount">
                            <span>Nominal transfer</span>
                            <strong><?= h($nominal) ?></strong>
                        </div>
                        <dl class="rf-info">
                            <div><dt>Bank / e-wallet</dt><dd><?= h($refund['bank_tujuan']) ?></dd></div>
                            <div>
                                <dt>Nomor rekening</dt>
                                <dd>
                                    <span class="rf-norek" id="noRekTujuan"><?= h($refund['no_rekening']) ?></span>
                                    <button type="button" class="rf-copy" data-copy="<?= h($refund['no_rekening']) ?>" title="Salin nomor rekening"><i class="bi bi-files"></i> Salin</button>
                                </dd>
                            </div>
                            <div><dt>Atas nama</dt><dd><?= h($refund['nama_pemilik_rekening']) ?></dd></div>
                        </dl>
                    <?php else: ?>
                        <p class="rf-empty mb-0">Tamu belum mencantumkan rekening pada pengajuan ini. Hubungi tamu lewat WhatsApp untuk meminta data rekening.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="rf-card rf-grow">
                <?php if ($menunggu): ?>
                    <header class="rf-card-head"><span class="rf-card-icon"><i class="bi bi-clipboard-check"></i></span><h6>Keputusan</h6></header>
                    <div class="rf-card-body">
                        <form method="POST" action="admin_refund_tinjau.php?id=<?= (int)$refund['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int)$refund['id'] ?>">
                            <label for="catatan" class="rf-label">Catatan <span>(wajib jika menolak)</span></label>
                            <textarea id="catatan" name="catatan" class="form-control rf-textarea" rows="4" maxlength="1000"
                                      placeholder="Contoh: dana akan ditransfer maksimal 3 hari kerja / alasan penolakan"></textarea>
                            <div class="rf-actions">
                                <button type="submit" name="aksi" value="setuju" class="btn btn-success fw-bold"
                                        onclick="return confirm('Setujui refund ini? Status reservasi akan menjadi Dibatalkan.');">
                                    <i class="bi bi-check-circle me-1"></i> Setuju Refund
                                </button>
                                <button type="submit" name="aksi" value="tolak" class="btn btn-outline-danger fw-bold"
                                        onclick="var c=document.getElementById('catatan'); if(c.value.trim().length<5){c.classList.add('is-invalid');c.focus();return false;} return confirm('Tolak refund ini?');">
                                    <i class="bi bi-x-circle me-1"></i> Tolak Refund
                                </button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($disetujui || $selesai): ?>
                    <header class="rf-card-head"><span class="rf-card-icon"><i class="bi bi-send-check"></i></span><h6><?= $selesai ? 'Refund Selesai' : 'Proses Transfer Dana' ?></h6></header>
                    <div class="rf-card-body">
                        <?php if ($refund['catatan_admin']): ?>
                            <div class="rf-note"><span>Catatan</span><?= nl2br(h($refund['catatan_admin'])) ?></div>
                        <?php endif; ?>
                        <?php if ($selesai && $refund['ditransfer_at']): ?>
                            <p class="rf-meta"><i class="bi bi-clock-history me-1"></i> Ditransfer <?= h(date('d M Y, H.i', strtotime($refund['ditransfer_at']))) ?> WIB</p>
                        <?php endif; ?>

                        <ol class="rf-steps">
                            <li><span>1</span><div>Transfer <strong><?= h($nominal) ?></strong> ke rekening tamu.</div></li>
                            <li><span>2</span><div>Kirim bukti transfer ke tamu lewat WhatsApp (lampirkan screenshot-nya).</div></li>
                            <li><span>3</span><div>Tandai refund sebagai sudah ditransfer.</div></li>
                        </ol>

                        <div class="rf-actions">
                            <?php if ($waUrl): ?>
                                <a href="<?= h($waUrl) ?>" target="_blank" rel="noopener" class="btn btn-success fw-bold"><i class="bi bi-whatsapp me-1"></i> Kirim Bukti via WhatsApp</a>
                            <?php else: ?>
                                <div class="alert alert-warning small mb-0">Nomor WhatsApp tamu tidak valid, tautan tidak dapat dibuat.</div>
                            <?php endif; ?>
                            <?php if ($disetujui): ?>
                                <form method="POST" action="admin_refund_tinjau.php?id=<?= (int)$refund['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$refund['id'] ?>">
                                    <button type="submit" name="aksi" value="selesai" class="btn btn-brown fw-bold w-100"
                                            onclick="return confirm('Tandai refund ini sudah ditransfer ke tamu?');">
                                        <i class="bi bi-check2-circle me-1"></i> Tandai Sudah Ditransfer
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <header class="rf-card-head"><span class="rf-card-icon"><i class="bi bi-x-octagon"></i></span><h6>Hasil Keputusan</h6></header>
                    <div class="rf-card-body">
                        <?php if ($refund['diputuskan_at']): ?>
                            <p class="rf-meta"><i class="bi bi-clock-history me-1"></i> Diputuskan <?= h(date('d M Y, H.i', strtotime($refund['diputuskan_at']))) ?> WIB</p>
                        <?php endif; ?>
                        <?php if ($refund['catatan_admin']): ?>
                            <div class="rf-note"><span>Catatan</span><?= nl2br(h($refund['catatan_admin'])) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
        if (navigator.clipboard) navigator.clipboard.writeText(b.dataset.copy);
        var i = b.querySelector('i'); if (!i) return;
        i.className = 'bi bi-check2';
        setTimeout(function () { i.className = 'bi bi-files'; }, 1200);
    });
});
</script>