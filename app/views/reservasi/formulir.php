<section class="container mt-4 mb-5" style="max-width: 850px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Katalog</a></li>
            <li class="breadcrumb-item"><a href="detail.php?id=<?= (int)$room['id'] ?>" class="text-decoration-none text-muted"><?= h($room['nomor_kamar']) ?></a></li>
            <li class="breadcrumb-item active fw-bold" aria-current="page">Formulir Reservasi</li>
        </ol>
    </nav>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert"><?= h($error) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 mb-4 text-center">
        <div class="card-body p-3">
            <h6 class="text-muted fw-bold small text-uppercase mb-3">Tahapan Reservasi</h6>
            <div class="tracker-container">
                <div class="tracker-step active"><div class="tracker-circle">1</div><div class="tracker-text">Pemesanan</div></div>
                <div class="tracker-step inactive"><div class="tracker-circle">2</div><div class="tracker-text">Pembayaran</div></div>
                <div class="tracker-step inactive"><div class="tracker-circle">3</div><div class="tracker-text">Status</div></div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <span class="badge bg-brown text-white mb-2">LANGKAH 1 DARI 3</span>
                    <h3 class="fw-bolder mb-0">Data Pemesan</h3>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-brown rounded-pill px-3 py-2">
                    Keep <?= (int)get_public_settings($pdo)['batas_waktu_keep_menit'] ?> menit
                </span>
            </div>

            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center bg-light p-3 rounded border mb-4">
                <?php if (!empty($room['foto_url']) && is_file(dirname(__DIR__, 3) . '/public' . $room['foto_url'])): ?>
                    <img src="<?= h(ltrim($room['foto_url'], '/')) ?>" alt="Kamar <?= h($room['nomor_kamar']) ?>"
                         class="rounded" style="width: 100px; height: 80px; object-fit: cover;">
                <?php endif; ?>
                <div class="flex-fill">
                        <span class="badge bg-white border text-brown">Kamar <?= h($room['nomor_kamar']) ?></span>
                        <h5 class="fw-bold mt-2 mb-1"><?= h($room['tipe_kamar']) ?></h5>
                        <small class="text-muted">Maksimal <?= (int)$room['kapasitas'] ?> tamu · Rp <?= number_format((float)$room['harga_per_malam'], 0, ',', '.') ?>/malam</small>
                </div>
                <div class="text-md-end">
                    <small class="text-muted d-block">Estimasi total</small>
                    <strong class="text-brown fs-5" id="bookingEstimate">Rp <?= number_format(booking_total((float)$room['harga_per_malam'], (int)$duration, $packagePrices), 0, ',', '.') ?></strong>
                </div>
            </div>

            <form action="keep.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="kamar_id" value="<?= (int)$room['id'] ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label fw-semibold">Nama lengkap</label>
                        <input id="nama" name="nama" class="form-control" maxlength="100" minlength="3" autocomplete="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="whatsapp" class="form-label fw-semibold">Nomor WhatsApp</label>
                        <input id="whatsapp" name="whatsapp" class="form-control" inputmode="tel" autocomplete="tel" placeholder="081234567890" required>
                        <small class="text-muted">Gunakan nomor yang aktif.</small>
                    </div>
                    <div class="col-md-6">
                        <label for="checkin" class="form-label fw-semibold">Waktu check-in</label>
                        <input id="checkin" type="datetime-local" name="checkin" class="form-control"
                               min="<?= h(date('Y-m-d\TH:i')) ?>" value="<?= h($checkinInput) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="durasiPaket" class="form-label fw-semibold">Durasi</label>
                        <?php $pickerId = 'durasi'; $pickerName = 'durasi_jam'; $pickerSmall = false; $pickerPlaceholder = false; require __DIR__ . '/../partials/durasi_picker.php'; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="guestInput">Jumlah tamu <small class="text-muted">· Maks. <?= (int)$room['kapasitas'] ?></small></label>
                        <div class="drj-field-box">
                            <div class="drj-guest-row">
                                <span class="fw-semibold" id="guestLabel">1 Orang Tamu</span>
                                <div class="drj-stepper">
                                    <button class="btn" type="button" id="guestMinus" aria-label="Kurangi tamu">−</button>
                                    <input id="guestInput" name="jumlah_tamu" type="number" min="1" max="<?= (int)$room['kapasitas'] ?>" value="1" required>
                                    <button class="btn" type="button" id="guestPlus" aria-label="Tambah tamu">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="catatan" class="form-label fw-semibold">Catatan <span class="text-muted">(opsional)</span></label>
                        <input id="catatan" name="catatan" class="form-control" maxlength="250">
                    </div>
                </div>
                <div class="drj-date-wrap bg-light p-3 rounded mt-4">
                    <div class="row g-2 small">
                        <div class="col-md-6"><span class="text-muted d-block">CHECK-IN</span><strong id="summaryCheckin"><?= $checkin ? h($checkin->format('d M Y H:i')) : 'Pilih waktu di atas' ?></strong></div>
                        <div class="col-md-6"><span class="text-muted d-block">CHECK-OUT</span><strong id="summaryCheckout"><?= $checkoutInput ? h(date('d M Y H:i', strtotime($checkoutInput))) : 'Otomatis dihitung' ?></strong></div>
                    </div>
                </div>
                <div class="alert alert-light border mt-3">
                    Total akhir divalidasi server berdasarkan tarif kamar. Durasi dibulatkan ke atas per 24 jam. Setelah kamar dikunci, pembayaran perlu diunggah sebelum masa keep berakhir.
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" id="setuju" name="setuju_kebijakan" type="checkbox" value="1" required>
                    <label class="form-check-label small" for="setuju">Saya menyetujui kebijakan pemesanan dan refund villa.</label>
                </div>
                <button type="submit" class="btn btn-brown w-100 fw-bold py-3"><i class="bi bi-lock-fill me-2"></i>Kunci Kamar &amp; Lanjut Pembayaran</button>
                <div class="text-center mt-2 mb-3"><small class="text-muted">Kamar ditahan <?= (int)get_public_settings($pdo)['batas_waktu_keep_menit'] ?> menit setelah tombol ditekan.</small></div>
            </form>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const guests = document.getElementById('guestInput');
    const label = document.getElementById('guestLabel');
    const checkin = document.getElementById('checkin');
    const duration = document.getElementById('durasiJam');
    const checkoutSummary = document.getElementById('summaryCheckout');
    const checkinSummary = document.getElementById('summaryCheckin');
    const estimate = document.getElementById('bookingEstimate');
    const rate = <?= json_encode((float)$room['harga_per_malam']) ?>;
    const shortPrices = <?= json_encode($packagePrices) ?>; // harga paket 3/6/12 jam
    function priceFor(hours) {
        if (hours >= 24) return rate * Math.floor(hours / 24);
        return shortPrices[hours] ?? null;
    }
    function updateGuests(value) {
        const max = Number(guests.max) || 1;
        guests.value = Math.max(1, Math.min(max, value));
        label.textContent = guests.value + (Number(guests.value) === 1 ? ' Orang Tamu' : ' Orang Tamu');
    }
    document.getElementById('guestMinus').addEventListener('click', () => updateGuests(Number(guests.value) - 1));
    document.getElementById('guestPlus').addEventListener('click', () => updateGuests(Number(guests.value) + 1));
    function updateSchedule() {
        const start = new Date(checkin.value);
        const hours = Number(duration.value);
        if (!checkin.value || Number.isNaN(start.getTime()) || !hours) return;
        const end = new Date(start.getTime() + hours * 3600000);
        const fmt = new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        checkinSummary.textContent = fmt.format(start);
        checkoutSummary.textContent = fmt.format(end);
        const price = priceFor(hours);
        estimate.textContent = price === null ? '-' : 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
    }
    checkin.addEventListener('change', updateSchedule);
    duration.addEventListener('change', updateSchedule);
    updateSchedule();
});
</script>