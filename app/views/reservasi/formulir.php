<?php
/* ==========================================================================
   Formulir pemesanan (Langkah 1 dari 3), mengikuti desain Figma.
   Variabel dari public/formulir.php: $room, $checkin, $duration, $packagePrices, $error, $pdo
   ========================================================================== */
$settings   = get_public_settings($pdo);
$keepMenit  = max(1, (int)($settings['batas_waktu_keep_menit'] ?? 15));
$refundHari = max(1, (int)($settings['batas_refund_hari_sebelum'] ?? 1));

$bln   = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$tgl   = fn(DateTimeInterface $d) => $d->format('j') . ' ' . $bln[(int)$d->format('n') - 1] . ' ' . $d->format('Y');
$now   = new DateTimeImmutable('now');
$nowTxt = $now->format('j') . ' ' . $bln[(int)$now->format('n') - 1] . ', ' . $now->format('H.i');

$checkout = $checkin->modify('+' . (int)$duration . ' hours');
$lama     = $duration >= 24 ? intdiv($duration, 24) . ' malam' : (int)$duration . ' jam';

try {
    $total    = booking_total((float)$room['harga_per_malam'], (int)$duration, $packagePrices);
    $totalTxt = 'Rp ' . number_format($total, 0, ',', '.');
} catch (DomainException $e) {
    $totalTxt = '-';
}

$cover      = room_cover_url($room);
$facilities = array_slice(getFasilitasKamar((string)($room['deskripsi'] ?? '')), 0, 3);
$changeUrl  = 'detail.php?' . http_build_query([
    'id'      => (int)$room['id'],
    'checkin' => $checkin->format('Y-m-d\TH:i'),
    'durasi'  => (int)$duration,
]);
?>
<section class="container fm-wrap mt-4 mb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="fm-crumb mb-3">
        <a href="<?= h($changeUrl) ?>" class="fm-back" aria-label="Kembali ke detail kamar"><i class="bi bi-arrow-left"></i></a>
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="beranda.php" class="text-decoration-none text-muted">Katalog</a></li>
            <li class="breadcrumb-item"><a href="<?= h($changeUrl) ?>" class="text-decoration-none text-muted">Kamar <?= h($room['nomor_kamar']) ?></a></li>
            <li class="breadcrumb-item active fw-semibold" aria-current="page">Formulir Reservasi</li>
        </ol>
    </nav>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert"><?= h($error) ?></div>
    <?php endif; ?>

    <!-- Tahapan reservasi -->
    <div class="fm-card p-3 mb-3">
        <div class="fm-panel">
            <div class="fm-eyebrow mb-3">Tahapan Reservasi</div>
            <ol class="fm-steps">
                <li class="active">
                    <span class="fm-dot">1</span><strong>Dipesan</strong><small><?= h($nowTxt) ?></small>
                </li>
                <li>
                    <span class="fm-dot">2</span><strong>Menunggu Konfirmasi</strong><small>-</small>
                </li>
                <li>
                    <span class="fm-dot">3</span><strong>Dibayar</strong><small>-</small>
                </li>
            </ol>
        </div>
    </div>

    <!-- Kartu formulir -->
    <div class="fm-card fm-main p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-start gap-3 pb-3 mb-3 fm-divider">
            <div>
                <div class="fm-step-label"><i></i>Langkah 1 dari 3</div>
                <h1 class="fm-heading">Data Pemesan</h1>
            </div>
            <span class="fm-pill"><i class="bi bi-stopwatch me-1"></i>Keep <?= $keepMenit ?> Menit</span>
        </div>

        <!-- Ringkasan kamar -->
        <div class="fm-room mb-3">
            <?php if ($cover !== ''): ?>
                <img src="<?= h($cover) ?>" alt="Kamar <?= h($room['nomor_kamar']) ?>" class="fm-room-img">
            <?php else: ?>
                <span class="fm-room-img fm-room-ph" aria-hidden="true"><i class="bi bi-door-open"></i></span>
            <?php endif; ?>
            <div class="fm-room-info">
                <div class="d-flex flex-wrap gap-2 mb-1">
                    <span class="fm-tag fm-tag-brown">Kamar <?= h($room['nomor_kamar']) ?></span>
                    <span class="fm-tag fm-tag-grey">Tersedia</span>
                </div>
                <h2 class="fm-room-name"><?= h($room['tipe_kamar']) ?></h2>
                <?php if ($facilities): ?>
                    <div class="fm-fac">
                        <?php foreach ($facilities as $f): ?>
                            <span><i class="bi <?= h($f['icon']) ?>"></i><?= h($f['label']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="fm-fac"><span><i class="bi bi-people"></i>Maks <?= (int)$room['kapasitas'] ?> tamu</span></div>
                <?php endif; ?>
            </div>
            <div class="fm-room-total">
                <small>Total pembayaran</small>
                <strong id="bookingEstimate"><?= h($totalTxt) ?></strong>
                <small><?= h($lama) ?> &middot; Dibayar penuh</small>
            </div>
        </div>

        <form action="keep.php" method="POST" id="fmForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="kamar_id" value="<?= (int)$room['id'] ?>">
            <input type="hidden" name="checkin" value="<?= h($checkin->format('Y-m-d\TH:i')) ?>">
            <input type="hidden" name="durasi_jam" value="<?= (int)$duration ?>">

            <!-- Nama & WhatsApp -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nama" class="fm-label">Nama Lengkap <span class="req">*</span></label>
                    <div class="fm-input">
                        <i class="bi bi-person"></i>
                        <input id="nama" name="nama" type="text" maxlength="100" minlength="3" autocomplete="name"
                               placeholder="Contoh: Budi Wijaya" required aria-describedby="namaHint">
                    </div>
                    <div class="fm-hint" id="namaHint">Minimal 3 karakter. Dipakai untuk mengenali pesananmu saat check-in.</div>
                </div>
                <div class="col-md-6">
                    <label for="whatsapp" class="fm-label">Nomor WhatsApp <span class="req">*</span></label>
                    <div class="fm-input">
                        <i class="bi bi-whatsapp"></i>
                        <span class="fm-prefix">+62</span>
                        <input id="whatsapp" name="whatsapp" type="tel" inputmode="numeric" autocomplete="tel-national"
                               placeholder="81278901234" maxlength="16" required aria-describedby="waHint">
                    </div>
                    <div class="fm-hint" id="waHint">Dipakai pengelola untuk menghubungimu bila diperlukan.</div>
                </div>
            </div>

            <!-- Jadwal -->
            <div class="fm-panel mt-3">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="fm-date">
                            <span class="fm-date-ico"><i class="bi bi-box-arrow-in-right"></i></span>
                            <div class="flex-fill">
                                <small>CHECK-IN</small>
                                <strong><?= h($tgl($checkin)) ?></strong>
                            </div>
                            <span class="fm-time"><?= h($checkin->format('H.i')) ?> WIB</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="fm-date">
                            <span class="fm-date-ico"><i class="bi bi-box-arrow-right"></i></span>
                            <div class="flex-fill">
                                <small>CHECK-OUT</small>
                                <strong><?= h($tgl($checkout)) ?></strong>
                            </div>
                            <span class="fm-time"><?= h($checkout->format('H.i')) ?> WIB</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 small px-1">
                    <span>Lama menginap: <strong><?= h($lama) ?></strong></span>
                    <a href="<?= h($changeUrl) ?>" class="fm-link">Ubah tanggal</a>
                </div>
            </div>

            <!-- Tamu & catatan -->
            <div class="row g-3 mt-0">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <label for="guestInput" class="fm-label mb-0">Jumlah Tamu</label>
                        <span class="fm-hint m-0">Maks <?= (int)$room['kapasitas'] ?> tamu</span>
                    </div>
                    <div class="fm-guest mt-1">
                        <div>
                            <strong id="guestLabel">1 Orang Tamu</strong>
                            <small>Termasuk kasur &amp; fasilitas</small>
                        </div>
                        <div class="fm-count">
                            <button type="button" id="guestMinus" aria-label="Kurangi tamu"><i class="bi bi-dash"></i></button>
                            <input id="guestInput" name="jumlah_tamu" type="number" min="1" max="<?= (int)$room['kapasitas'] ?>" value="1" required>
                            <button type="button" id="guestPlus" aria-label="Tambah tamu"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <label for="catatan" class="fm-label mb-0">Catatan <span class="fm-opt">(Opsional)</span></label>
                        <span class="fm-hint m-0">Maks 250 karakter</span>
                    </div>
                    <textarea id="catatan" name="catatan" class="fm-textarea mt-1" rows="2" maxlength="250"
                              placeholder="Contoh: Tiba sekitar pukul 15.30 WIB bersama keluarga."></textarea>
                </div>
            </div>

            <!-- Persetujuan -->
            <label class="fm-agree mt-3" for="setuju">
                <input id="setuju" name="setuju_kebijakan" type="checkbox" value="1" required>
                <span>Saya memahami bahwa keep berlaku <?= $keepMenit ?> menit, pembayaran dilakukan penuh (100%), dan refund hanya dapat diajukan paling lambat H-<?= $refundHari ?> sampai pukul 23.59 WIB.</span>
            </label>

            <!-- Info keep -->
            <div class="fm-info mt-3" role="note">
                <i class="bi bi-info-circle"></i>
                <span>Setelah kamar dikunci, kamu punya <?= $keepMenit ?> menit untuk transfer dan mengunggah bukti. Rincian rekening tampil di langkah berikutnya.</span>
            </div>

            <button type="submit" class="fm-submit mt-3" id="fmSubmit">
                <i class="bi bi-lock-fill me-2"></i>Kunci Kamar &amp; Lanjut ke Pembayaran
            </button>
            <div class="text-center mt-2"><small class="fm-hint">Kamar ditahan selama <?= $keepMenit ?> menit setelah tombol ditekan.</small></div>
        </form>

        <div class="mt-3">
            <a href="<?= h($changeUrl) ?>" class="fm-link small"><i class="bi bi-arrow-left me-1"></i>Kembali ke detail kamar</a>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form   = document.getElementById('fmForm');
    const guests = document.getElementById('guestInput');
    const label  = document.getElementById('guestLabel');
    const minus  = document.getElementById('guestMinus');
    const plus   = document.getElementById('guestPlus');
    const wa     = document.getElementById('whatsapp');
    const submit = document.getElementById('fmSubmit');
    const submitHtml = submit.innerHTML;

    /* Jumlah tamu */
    function setGuests(v) {
        const max = Number(guests.max) || 1;
        guests.value = Math.max(1, Math.min(max, Number(v) || 1));
        label.textContent = guests.value + ' Orang Tamu';
        minus.disabled = Number(guests.value) <= 1;
        plus.disabled  = Number(guests.value) >= max;
    }
    minus.addEventListener('click', () => setGuests(Number(guests.value) - 1));
    plus.addEventListener('click',  () => setGuests(Number(guests.value) + 1));
    guests.addEventListener('change', () => setGuests(guests.value));
    setGuests(guests.value);

    /* WhatsApp: hanya angka, awalan 0 / 62 dibuang karena +62 sudah tertera */
    function cleanPhone() {
        let d = wa.value.replace(/\D+/g, '');
        if (d.startsWith('62')) d = d.slice(2);
        d = d.replace(/^0+/, '');
        wa.value = d;
    }
    wa.addEventListener('input', cleanPhone);
    wa.addEventListener('blur', cleanPhone);

    /* Validasi ringan sebelum kirim + cegah klik ganda */
    form.addEventListener('submit', function (e) {
        cleanPhone();
        if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity();
            return;
        }
        submit.disabled = true;
        submit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Mengunci kamar...';
    });
    window.addEventListener('pageshow', function (ev) {
        if (ev.persisted) { submit.disabled = false; submit.innerHTML = submitHtml; }
    });
});
</script>