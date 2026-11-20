<?php

$publicDir = dirname(__DIR__, 3) . '/public';
$hasFile   = fn($u) => !empty($u) && is_file($publicDir . $u);

/* ---------- Galeri ---------- */
$photos = [];
$addPhoto = function ($url, $label = '') use (&$photos, $hasFile) {
    if ($hasFile($url)) $photos[] = ['url' => $url, 'label' => $label];
};

// 1) Dari database
if (!empty($room['galeri']) && is_array($room['galeri'])) {
    foreach ($room['galeri'] as $g) $addPhoto($g['url'] ?? '', $g['label'] ?? '');
}

// 2) Otomatis dari folder: public/uploads/kamar/<nomor_kamar>/
if (!$photos) {
    $folderName = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$room['nomor_kamar']);
    $folderUrl  = '/uploads/kamar/' . $folderName;
    $folderPath = $publicDir . $folderUrl;
    if ($folderName !== '' && is_dir($folderPath)) {
        $files = scandir($folderPath) ?: [];
        natcasesort($files);
        foreach ($files as $f) {
            if (!preg_match('/\.(jpe?g|png|webp)$/i', $f)) continue;
            $name  = pathinfo($f, PATHINFO_FILENAME);
            $name  = preg_replace('/^\d+[\s_-]*/', '', $name);
            $label = ucwords(trim(str_replace(['-', '_'], ' ', $name)));
            $addPhoto($folderUrl . '/' . $f, $label);
        }
    }
}

// 3) Cadangan: foto tunggal lama
if (!$photos) $addPhoto($room['foto_url'] ?? '', 'Kamar Utama');

// URL aman untuk <img>
$photoSrc = fn($u) => implode('/', array_map('rawurlencode', explode('/', ltrim($u, '/'))));
$nPhotos  = count($photos);

/* ---------- Harga & estimasi ---------- */
$price  = (float)$room['harga_per_malam'];
$nights = 1;
$total  = $price;
if ($checkin && $checkout) {
    $hours  = max(1, ($checkout->getTimestamp() - $checkin->getTimestamp()) / 3600);
    $nights = max(1, (int)ceil($hours / 24));
    try {
        $total = booking_total($price, (int)$duration, $packagePrices);
    } catch (DomainException $e) {
        $total = $price * $nights;
    }
}
$rp = fn($n) => 'Rp ' . number_format((float)$n, 0, ',', '.');

$bln   = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$tglId = fn($d) => $d ? $d->format('d') . ' ' . $bln[(int)$d->format('n') - 1] . ' ' . $d->format('Y') : 'Pilih tanggal';

/* Bila belum ada jadwal di URL, tampilkan contoh jadwal (besok 14.00, 2 malam). */
$defaultsUsed = !$checkin;
if ($defaultsUsed) {
    $durView = 48;
    $ciView  = new DateTimeImmutable('tomorrow 14:00');
    $coView  = $ciView->modify('+48 hours');
    try { $total = booking_total($price, 48, $packagePrices); } catch (DomainException $e) { $total = $price * 2; }
    if ($available && isset($pdo)) {
        $cek = $pdo->prepare("SELECT NOT EXISTS (SELECT 1 FROM reservasi WHERE kamar_id = :r
            AND status_booking IN ('Di-Keep','Menunggu Konfirmasi','Dibayar')
            AND tsrange(waktu_checkin, waktu_checkout) && tsrange(:ci::timestamp, :co::timestamp))");
        $cek->execute(['r' => $room['id'], 'ci' => $ciView->format('Y-m-d H:i:s'), 'co' => $coView->format('Y-m-d H:i:s')]);
        $available = filter_var($cek->fetchColumn(), FILTER_VALIDATE_BOOLEAN);
    }
} else {
    $durView = (int)$duration;
    $ciView  = $checkin;
    $coView  = $checkout;
}
$calcLabel = $durView < 24
    ? 'Paket ' . $durView . ' jam'
    : $rp($price) . ' &times; ' . max(1, (int)ceil($durView / 24)) . ' malam';
$rateIsPackage = isset($packagePrices[$durView]);
$rateAmount = $rateIsPackage ? $rp($packagePrices[$durView]) : $rp($price);
$rateUnit = $rateIsPackage ? '/ ' . $durView . ' jam' : '/ malam';

$keepMenit   = (int)($settings['batas_waktu_keep_menit'] ?? 15);
$facilities  = getFasilitasKamar($room['deskripsi'] ?? '');
$facDesc = [
    'Air Panas'     => 'Shower air panas',
    'Smart TV'      => 'Hiburan layar pintar',
    'Wi-Fi'         => 'Internet nirkabel',
    'KM Dalam'      => 'Kamar mandi pribadi',
    'Dapur Bersama' => 'Akses dapur bersama',
    'Pemandangan'   => 'Panorama pegunungan',
];
$bookUrl = 'formulir.php?' . http_build_query([
    'kamar_id' => $room['id'],
    'checkin'  => $ciView->format('Y-m-d\TH:i'),
    'durasi'   => $durView,
]);

$rules = [
    ['bi-person-slash',            'Bebas Akun & Registrasi',        'Pemesanan instan tanpa buat akun',             '', false],
    ['bi-arrow-counterclockwise',  'Kebijakan Pembatalan / Refund',  'Batas H-1 (23.59 WIB)',
        'Dapat diajukan paling lambat H-1 sampai pukul 23.59 WIB; setelah melewati batas tersebut tidak ada refund (non-refundable).', false],
    ['bi-cash-coin',               'Skema Pembayaran',               'Bayar penuh 100% dalam ' . $keepMenit . ' menit',
        'Setelah tombol keep ditekan, bayar dengan scan qris yang disediakan sebelum batas waktu ' . $keepMenit . ' menit berakhir.', true],
];

$diperbarui = '-';
if (!empty($room['kondisi_diperbarui_at'])) {
    $ts = strtotime((string)$room['kondisi_diperbarui_at']);
    if ($ts) $diperbarui = date('d', $ts) . ' ' . $bln[(int)date('n', $ts) - 1] . ' ' . date('Y, H.i', $ts) . ' WIB';
}
?>

<section class="container mt-4 mb-5 rdp" style="max-width: 1000px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="beranda.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i>Katalog</a></li>
            <li class="breadcrumb-item"><a href="detail.php?id=<?= (int)$room['id'] ?>" class="text-decoration-none text-brown fw-semibold">Kamar <?= h($room['nomor_kamar']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= h($room['tipe_kamar']) ?></li>
        </ol>
    </nav>

    <!-- ========== GALERI ========== -->
    <?php if ($photos): ?>
        <div class="rd-gallery shadow-sm" id="rdGallery" tabindex="0" aria-roledescription="carousel"
             aria-label="Galeri foto Kamar <?= h($room['nomor_kamar']) ?>">
            <div class="rd-stage" id="rdStage">
                <?php foreach ($photos as $i => $p): ?>
                    <img class="rd-slide<?= $i === 0 ? ' active' : '' ?>"
                         src="<?= h($photoSrc($p['url'])) ?>"
                         data-label="<?= h($p['label'] ?: 'Kamar') ?>"
                         alt="<?= h(($p['label'] ?: 'Foto') . ' - Kamar ' . $room['nomor_kamar']) ?>"
                         draggable="false" <?= $i > 0 ? 'loading="lazy"' : '' ?>>
                <?php endforeach; ?>
            </div>

            <span class="rd-chip" id="rdLabel" style="top:12px;left:12px">
                <i class="bi bi-door-open me-1"></i><span><?= h($photos[0]['label'] ?: 'Kamar Utama') ?></span>
            </span>
            <span class="rd-chip d-none d-sm-inline-flex" style="top:12px;right:12px">
                <i class="bi bi-patch-check me-1"></i>Dokumentasi Nyata Fisik
            </span>

            <?php if ($nPhotos > 1): ?>
                <button type="button" class="rd-nav prev" id="rdPrev" aria-label="Foto sebelumnya"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="rd-nav next" id="rdNext" aria-label="Foto berikutnya"><i class="bi bi-chevron-right"></i></button>
            <?php endif; ?>

            <button type="button" class="rd-chip border-0" id="rdFull" style="bottom:12px;right:12px;background:rgba(40,30,25,.8);color:#fff">
                <i class="bi bi-arrows-fullscreen me-1" style="color:#fff"></i><span id="rdCount">1 / <?= $nPhotos ?></span><span class="d-none d-sm-inline">&nbsp;&bull; Layar Penuh</span>
            </button>
        </div>

        <?php if ($nPhotos > 1): ?>
            <div class="rd-thumbs" id="rdThumbs">
                <?php foreach ($photos as $i => $p): ?>
                    <button type="button" class="rd-thumb<?= $i === 0 ? ' active' : '' ?>" data-index="<?= $i ?>" aria-label="Lihat foto <?= $i + 1 ?>">
                        <img src="<?= h($photoSrc($p['url'])) ?>" alt="" loading="lazy" draggable="false">
                        <?php if ($p['label']): ?><span><?= h($p['label']) ?></span><?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="rd-empty">
            <i class="bi bi-image"></i>
            <strong>Kamar <?= h($room['nomor_kamar']) ?></strong>
            <small class="text-muted">Foto kamar belum diunggah</small>
        </div>
    <?php endif; ?>

    <!-- ========== JUDUL ========== -->
    <div class="d-flex justify-content-between align-items-start gap-3 mt-4">
        <div>
            <h1 class="fw-bolder h2 mb-1"><?= h($room['nomor_kamar']) ?> &middot; <?= h($room['tipe_kamar']) ?></h1>
            <div class="small text-muted d-flex flex-wrap gap-3">
                <span><i class="bi bi-layers me-1"></i>Lantai <?= (int)$room['lantai'] ?></span>
                <span><i class="bi bi-people me-1"></i>Maks <?= (int)$room['kapasitas'] ?> tamu</span>
                <?php if (!empty($room['luas_m2'])): ?><span><i class="bi bi-rulers me-1"></i><?= h($room['luas_m2']) ?> m&sup2;</span><?php endif; ?>
                <?php if (!empty($room['fitur_unggulan'])): ?><span><i class="bi bi-building me-1"></i><?= h($room['fitur_unggulan']) ?></span><?php endif; ?>
            </div>
        </div>
    </div>

    <p class="text-muted mt-3" style="white-space:pre-line"><?= h($room['deskripsi'] ?: 'Informasi kamar belum tersedia.') ?></p>

    <!-- ========== KARTU BOOKING: tarif, tanggal, lalu reservasi ========== -->
    <form action="detail.php" method="GET" id="rdBookForm" class="drj-card drj-booking mb-4"
          data-price="<?= (float)$price ?>" data-room="<?= (int)$room['id'] ?>"
          data-packages="<?= h(json_encode($packagePrices ?: new stdClass())) ?>">
        <input type="hidden" name="id" value="<?= (int)$room['id'] ?>">

        <?php if (!empty($jadwalTerpesan)): ?>
            <div class="rdp-booked-schedule mb-3 small">
                <div class="rdp-booked-head">
                    <i class="bi bi-calendar-x"></i>
                    <div class="fw-bold">Jadwal sudah dipesan</div>
                </div>
                <div class="table-responsive rdp-booked-table">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Check-in</th>
                                <th scope="col">Check-out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwalTerpesan as $jadwal): ?>
                                <tr>
                                    <td><?= h(date('d M Y, H.i', strtotime($jadwal['waktu_checkin']))) ?> WIB</td>
                                    <td><?= h(date('d M Y, H.i', strtotime($jadwal['waktu_checkout']))) ?> WIB</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-muted mt-2">Pilih tanggal yang belum dipesan.</div>
            </div>
        <?php endif; ?>

        <div class="row g-3">
            <!-- Tarif -->
            <div class="col-md-6 order-2">
                <div class="drj-inner-card box-tarif">
                    <div class="drj-label"><i class="bi bi-tag"></i> Tarif Sewa Kamar</div>
                    <div class="drj-price"><span id="rdRate"><?= $rateAmount ?></span> <small id="rdRateUnit"><?= h($rateUnit) ?></small></div>

                    <div class="d-flex justify-content-between small text-muted mt-3">
                        <span id="rdCalcLabel"><?= $calcLabel ?></span>
                        <span id="rdSub"><?= $rp($total) ?></span>
                    </div>

                    <ul class="rdp-pkg">
                        <?php foreach ($packagePrices as $hours => $pp): ?>
                            <li data-package-hours="<?= (int)$hours ?>"<?= (int)$hours === $durView ? ' class="is-selected" aria-current="true"' : '' ?>><span><?= (int)$hours ?> jam</span><strong><?= $rp($pp) ?></strong></li>
                        <?php endforeach; ?>
                        <li data-package-hours="24"<?= $durView === 24 ? ' class="is-selected" aria-current="true"' : '' ?>><span>24 jam</span><strong><?= $rp($price) ?></strong></li>
                    </ul>

                    <div class="drj-total-box d-flex justify-content-between align-items-center mt-3">
                        <span class="small fw-semibold">Estimasi total</span>
                        <span class="drj-price" style="font-size:1.35rem" id="rdTotal"><?= $rp($total) ?></span>
                    </div>
                </div>
            </div>

            <!-- Tanggal -->
            <div class="col-md-6 order-1">
                <div class="drj-inner-card box-tanggal">
                    <div class="drj-label"><i class="bi bi-calendar-event"></i> Pilih Tanggal Menginap</div>

                    <div class="drj-field mb-2">
                        <small>Check-in</small>
                        <div class="drj-date-control">
                            <span id="rdDateDisplay"><?= h($tglId($ciView)) ?>, <?= h($ciView->format('H:i')) ?> WIB</span>
                            <i class="bi bi-calendar3" aria-hidden="true"></i>
                            <input type="date" id="rdDate" aria-label="Tanggal check-in"
                                   min="<?= h(date('Y-m-d')) ?>" value="<?= h($ciView->format('Y-m-d')) ?>">
                        </div>
                    </div>

                    <div class="rd-time-picker mb-2" aria-label="Waktu check-in">
                        <label class="rd-time-part">
                            <small>Jam</small>
                            <select id="rdHour" aria-label="Jam check-in">
                                <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                    <option value="<?= sprintf('%02d', $hour) ?>" <?= (int)$ciView->format('H') === $hour ? 'selected' : '' ?>><?= sprintf('%02d', $hour) ?></option>
                                <?php endfor; ?>
                            </select>
                        </label>
                        <span class="rd-time-separator" aria-hidden="true">:</span>
                        <label class="rd-time-part">
                            <small>Menit</small>
                            <select id="rdMinute" aria-label="Menit check-in">
                                <?php for ($minute = 0; $minute < 60; $minute++): ?>
                                    <option value="<?= sprintf('%02d', $minute) ?>" <?= (int)$ciView->format('i') === $minute ? 'selected' : '' ?>><?= sprintf('%02d', $minute) ?></option>
                                <?php endfor; ?>
                            </select>
                        </label>
                    </div>
                    <input type="hidden" id="rdCi" name="checkin" value="<?= h($ciView->format('Y-m-d\TH:i')) ?>">

                    <div class="drj-field mb-2">
                        <small>Check-out</small>
                        <div class="fw-bold" id="rdCheckout"><?= h($tglId($coView)) ?>, <?= h($coView->format('H:i')) ?> WIB</div>
                    </div>

                    <?php $isNightsView = $durView > 24; ?>
                    <select id="rdDur" class="form-select form-select-sm" aria-label="Durasi sewa">
                        <option value="3"  <?= $durView === 3  ? 'selected' : '' ?>>3 Jam</option>
                        <option value="6"  <?= $durView === 6  ? 'selected' : '' ?>>6 Jam</option>
                        <option value="12" <?= $durView === 12 ? 'selected' : '' ?>>12 Jam</option>
                        <option value="24" <?= $durView === 24 ? 'selected' : '' ?>>24 Jam</option>
                        <option value="malam" <?= $isNightsView ? 'selected' : '' ?>>Lebih dari 24 jam (Menginap)</option>
                    </select>
                    <div id="rdMalamWrap" class="input-group input-group-sm mt-2<?= $isNightsView ? '' : ' d-none' ?>">
                        <input type="number" id="rdMalam" class="form-control" min="2" max="<?= (int)MAX_BOOKING_NIGHTS ?>" step="1"
                               value="<?= $isNightsView ? intdiv($durView, 24) : 2 ?>" aria-label="Jumlah hari">
                        <span class="input-group-text">hari</span>
                    </div>
                    <input type="hidden" name="durasi" id="rdDurVal" value="<?= (int)$durView ?>">

                    <div id="rdMsg" class="small text-danger mt-2" style="display:none"></div>
                    <div id="rdAlert" class="alert alert-warning py-1 px-2 mt-2 mb-0 small" style="display:none"></div>
                </div>
            </div>
        </div>

        <!-- Reservasi -->
        <div class="rdp-cta mt-3">
            <div class="rdp-cta-info">
                <div class="rdp-cta-ico"><i class="bi bi-lock-fill"></i></div>
                <div>
                    <div class="fw-bold">Kunci kamar ini sekarang</div>
                    <div class="small text-muted">Kamar langsung dikunci eksklusif <?= $keepMenit ?> menit tanpa risiko direbut orang lain selagi Anda menyelesaikan verifikasi transfer.</div>
                </div>
            </div>
            <div id="rdReserveWrap">
                <?php if ($available): ?>
                    <a class="btn btn-brown btn-cta fw-bold rdp-cta-btn" id="rdReserve" href="<?= h($bookUrl) ?>">
                        <i class="bi bi-lock-fill me-2"></i>Dipesan
                    </a>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary fw-bold rdp-cta-btn" id="rdReserve" disabled>Kamar tidak tersedia</button>
                <?php endif; ?>
            </div>
        </div>

        <small class="text-muted d-flex align-items-center gap-1 mt-3">
            <i class="bi bi-shield-check"></i>Ketersediaan dan tagihan akan divalidasi ulang oleh server saat pemesanan.
        </small>
    </form>

    <!-- ========== FASILITAS ========== -->
    <?php if ($facilities): ?>
        <h2 class="rdp-h"><i class="bi bi-stars"></i>Fasilitas Utama Kamar</h2>
        <div class="rdp-fas-grid mb-4">
            <?php foreach ($facilities as $f): ?>
                <div class="fas-card">
                    <i class="bi <?= h($f['icon']) ?> fas-ghost"></i>
                    <span class="fas-check"><i class="bi bi-check-lg"></i></span>
                    <div class="drj-icon-tile mb-3"><i class="bi <?= h($f['icon']) ?>"></i></div>
                    <div class="fas-title fw-bold small"><?= h($f['label']) ?></div>
                    <?php $desc = $f['desc'] ?? ($facDesc[$f['label']] ?? ''); ?>
                    <?php if ($desc !== ''): ?><div class="fas-desc drj-desc"><?= h($desc) ?></div><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ========== TRANSPARANSI KONDISI ========== -->
    <div class="rdp-trans mb-4">
        <div class="rdp-trans-head">
            <div class="rdp-ico-solid"><i class="bi bi-shield-check"></i></div>
            <div>
                <h2 class="h6 fw-bold mb-0">Transparansi Kondisi Kamar</h2>
                <small class="text-muted">Dicatat langsung oleh pengelola villa tanpa manipulasi foto atau rekayasa kondisi</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="rdp-sub">
                    <div class="rdp-sub-title"><i class="bi bi-droplet"></i>Catatan Kebersihan</div>
                    <p><?= h($room['catatan_kebersihan'] ?? 'Sprei, sarung bantal, dan handuk diganti setelah tamu terakhir check-out. Kamar mandi dibersihkan dan didisinfeksi menyeluruh.') ?></p>
                    <span class="rdp-pill"><i class="bi bi-check-circle"></i>Linen 100% Baru Terganti</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="rdp-sub">
                    <div class="rdp-sub-title"><i class="bi bi-sliders"></i>Kondisi Fisik &amp; Utilitas</div>
                    <p><?= h($room['kondisi_fisik'] ?? 'Air panas shower stabil, remote dan aplikasi Smart TV normal, stopkontak aman, tidak ada retak atau goresan parah pada perabot kayu.') ?></p>
                    <span class="rdp-pill"><i class="bi bi-check-circle"></i>Semua Stopkontak &amp; Air Lolos Uji</span>
                </div>
            </div>
        </div>

        <div class="rdp-trans-foot">
            <span><i class="bi bi-clock-history me-1"></i>Diperbarui <?= h($diperbarui) ?> oleh Pengelola</span>
            <span>Dokumentasi fisik diperiksa berkala sebelum tamu check-in</span>
        </div>
    </div>

    <!-- ========== KETENTUAN MENGINAP ========== -->
    <h2 class="rdp-h"><i class="bi bi-journal-check"></i>Ketentuan Menginap</h2>
    <div class="drj-card px-3 px-md-4 py-2">
        <?php foreach ($rules as $idx => [$icon, $judul, $nilai, $ket, $sorot]): ?>
            <div class="drj-rule<?= $idx < count($rules) - 1 ? ' drj-rule-border' : '' ?> d-flex align-items-start gap-3">
                <div class="drj-icon-tile drj-icon-sm flex-shrink-0"><i class="bi <?= h($icon) ?>"></i></div>
                <div class="flex-fill">
                    <div class="rdp-rule-row">
                        <strong class="small"><?= h($judul) ?></strong>
                        <span class="small <?= $sorot ? 'fw-semibold text-brown' : 'text-muted' ?>"><?= h($nilai) ?></span>
                    </div>
                    <?php if ($ket !== ''): ?><div class="drj-desc text-muted mt-1"><?= h($ket) ?></div><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
(function () {
    /* ===== Check-in / durasi / check-out (tanpa refresh otomatis) ===== */
    var form   = document.getElementById('rdBookForm');
    var ci     = document.getElementById('rdCi');
    var dateInput = document.getElementById('rdDate');
    var dateDisplay = document.getElementById('rdDateDisplay');
    var hourInput = document.getElementById('rdHour');
    var minuteInput = document.getElementById('rdMinute');
    var out    = document.getElementById('rdCheckout');
    var durSel = document.getElementById('rdDur');
    var wrap   = document.getElementById('rdMalamWrap');
    var malam  = document.getElementById('rdMalam');
    var durVal = document.getElementById('rdDurVal');
    var msg    = document.getElementById('rdMsg');
    var total  = document.getElementById('rdTotal');
    var sub    = document.getElementById('rdSub');
    var rate   = document.getElementById('rdRate');
    var rateUnit = document.getElementById('rdRateUnit');
    var calc   = document.getElementById('rdCalcLabel');
    var packageRows = form.querySelectorAll('[data-package-hours]');
    var alertBox = document.getElementById('rdAlert');
    var reserveWrap = document.getElementById('rdReserveWrap');
    var price  = parseFloat(form.dataset.price) || 0;
    var packages = {};
    try { packages = JSON.parse(form.dataset.packages || '{}'); } catch (e) {}
    var MAX_NIGHTS = <?= (int)MAX_BOOKING_NIGHTS ?>;
    var BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var availabilityRequest = 0;
    var pad = function (n) { return String(n).padStart(2, '0'); };
    var rp  = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };
    var fmtLocal = function (d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes()); };
    var fmtDate = function (value) {
        var parts = value.split('-');
        return parts[2] + ' ' + BULAN[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
    };

    function showMsg(t) { msg.textContent = t || ''; msg.style.display = t ? 'block' : 'none'; }

    function nightsVal() {
        var n = parseInt(malam.value, 10);
        if (isNaN(n)) n = 2;
        return Math.max(2, Math.min(MAX_NIGHTS, n));
    }
    function hours() {
        if (durSel.value === 'malam') return nightsVal() * 24;
        return parseInt(durSel.value, 10);
    }

    function bookingUrl() {
        return 'formulir.php?kamar_id=' + encodeURIComponent(form.dataset.room)
            + '&checkin=' + encodeURIComponent(ci.value)
            + '&durasi=' + hours();
    }

    function setAvailability(available, message) {
        var element;
        if (available) {
            element = document.createElement('a');
            element.href = bookingUrl();
            element.className = 'btn btn-brown btn-cta fw-bold rdp-cta-btn';
            element.innerHTML = '<i class="bi bi-lock-fill me-2"></i>Dipesan';
            alertBox.className = 'alert alert-success py-1 px-2 mt-2 mb-0 small';
            alertBox.textContent = 'Jadwal tersedia.';
        } else {
            element = document.createElement('button');
            element.type = 'button';
            element.disabled = true;
            element.className = 'btn btn-secondary fw-bold rdp-cta-btn';
            element.textContent = 'Kamar tidak tersedia';
            alertBox.className = 'alert alert-warning py-1 px-2 mt-2 mb-0 small';
            alertBox.textContent = message || 'Kamar tidak tersedia pada jadwal tersebut.';
        }
        element.id = 'rdReserve';
        reserveWrap.replaceChildren(element);
        alertBox.style.display = 'block';
    }

    function checkAvailability() {
        var requestId = ++availabilityRequest;
        if (!ci.value) {
            setAvailability(false, 'Pilih tanggal dan jam check-in dahulu.');
            return;
        }
        var pending = document.createElement('button');
        pending.type = 'button';
        pending.disabled = true;
        pending.className = 'btn btn-secondary fw-bold rdp-cta-btn';
        pending.textContent = 'Memeriksa ketersediaan...';
        pending.id = 'rdReserve';
        reserveWrap.replaceChildren(pending);
        alertBox.style.display = 'none';

        var params = new URLSearchParams({
            availability: '1',
            id: form.dataset.room,
            checkin: ci.value,
            durasi: String(hours())
        });
        fetch('detail.php?' + params.toString(), { headers: { 'Accept': 'application/json' } })
            .then(function (response) {
                if (!response.ok) throw new Error('Pemeriksaan ketersediaan gagal.');
                return response.json();
            })
            .then(function (result) {
                if (requestId !== availabilityRequest) return;
                setAvailability(result.available === true);
            })
            .catch(function () {
                if (requestId !== availabilityRequest) return;
                setAvailability(false, 'Ketersediaan belum dapat diperiksa. Coba pilih jadwal lagi.');
            });
    }

    function refresh() {
        var hrs = hours();
        wrap.classList.toggle('d-none', durSel.value !== 'malam');
        durVal.value = hrs;
        packageRows.forEach(function (row) {
            var selected = Number(row.dataset.packageHours) === hrs;
            row.classList.toggle('is-selected', selected);
            if (selected) row.setAttribute('aria-current', 'true');
            else row.removeAttribute('aria-current');
        });

        var t = null, label = '';
        if (packages[hrs] != null) {
            t = parseFloat(packages[hrs]);
            label = 'Paket ' + hrs + ' jam';
            if (rate) rate.textContent = rp(t);
            if (rateUnit) rateUnit.textContent = '/ ' + hrs + ' jam';
        }
        else if (hrs >= 24 && hrs % 24 === 0) {
            t = price * hrs / 24;
            label = rp(price) + ' \u00d7 ' + (hrs / 24) + ' malam';
            if (rate) rate.textContent = rp(price);
            if (rateUnit) rateUnit.textContent = '/ malam';
        }
        if (t !== null) {
            if (calc) calc.textContent = label;
            if (sub) sub.textContent = rp(t);
            if (total) total.textContent = rp(t);
        }
        if (!ci.value) {
            out.textContent = 'Pilih check-in & durasi dahulu';
            checkAvailability();
            return;
        }
        var start = new Date(ci.value);
        if (isNaN(start.getTime())) return;
        var end = new Date(start.getTime() + hrs * 3600000);
        out.textContent = pad(end.getDate()) + ' ' + BULAN[end.getMonth()] + ' ' + end.getFullYear() + ', ' + pad(end.getHours()) + ':' + pad(end.getMinutes()) + ' WIB';
        checkAvailability();
    }

    /* Check-in tidak boleh di masa lalu */
    function clampPast() {
        if (!ci.value) return;
        var now = new Date();
        if (new Date(ci.value) < now) {
            var n = new Date(now.getTime()); n.setMinutes(0, 0, 0); n.setHours(n.getHours() + 1);
            ci.value = fmtLocal(n);
            showMsg('Waktu check-in tidak boleh di masa lalu, disesuaikan ke jam terdekat.');
        } else { showMsg(''); }
    }

    function syncCheckin() {
        if (!dateInput.value) {
            ci.value = '';
            dateDisplay.textContent = 'Pilih tanggal';
            showMsg('');
            refresh();
            return;
        }
        ci.value = dateInput.value + 'T' + hourInput.value + ':' + minuteInput.value;
        clampPast();
        if (ci.value) {
            var parts = ci.value.split('T');
            var timeParts = parts[1].split(':');
            dateInput.value = parts[0];
            hourInput.value = timeParts[0];
            minuteInput.value = timeParts[1];
            dateDisplay.textContent = fmtDate(dateInput.value) + ', ' + hourInput.value + ':' + minuteInput.value + ' WIB';
        }
        refresh();
    }

    dateInput.addEventListener('change', syncCheckin);
    hourInput.addEventListener('change', syncCheckin);
    minuteInput.addEventListener('change', syncCheckin);
    durSel.addEventListener('change', function () { if (durSel.value === 'malam' && !malam.value) malam.value = 2; refresh(); });
    malam.addEventListener('input', refresh);
    malam.addEventListener('change', function () { malam.value = nightsVal(); refresh(); });

    /* Kirim hanya bila check-in sudah diisi */
    form.addEventListener('submit', function (e) {
        refresh();
        if (!ci.value) { e.preventDefault(); showMsg('Pilih tanggal dan jam check-in dahulu.'); dateInput.focus(); }
    });
})();
</script>
<script src="assets/js/galeri-kamar.js?v=<?= file_exists($publicDir . '/assets/js/galeri-kamar.js') ? filemtime($publicDir . '/assets/js/galeri-kamar.js') : time() ?>" defer></script>