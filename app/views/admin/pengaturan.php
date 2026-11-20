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

    <form method="post" action="admin_pengaturan.php" id="formPengaturan" enctype="multipart/form-data" novalidate>
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
                        <input type="text" name="nama_properti" class="form-control" maxlength="100" required readonly
                               value="<?= h($settings['nama_properti']) ?>">
                        <button type="button" class="btn-lock" data-lock aria-label="Buka kunci untuk mengubah" title="Klik untuk membuka kunci"><i class="bi bi-lock-fill"></i></button>
                    </div>
                    <small class="drj-hint"><i class="bi bi-info-circle"></i> Tampil di beranda, judul halaman, footer, dan portal pengelola.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Nomor WhatsApp Utama / Refund <span class="text-danger">*</span></label>
                    <div class="input-group drj-ig ig-green">
                        <span class="input-group-text ig-icon"><i class="bi bi-whatsapp"></i></span>
                        <input type="text" name="whatsapp_utama" class="form-control" maxlength="20" required readonly
                               value="<?= h($settings['whatsapp_utama']) ?>">
                        <button type="button" class="btn-lock" data-lock aria-label="Buka kunci untuk mengubah" title="Klik untuk membuka kunci"><i class="bi bi-lock-fill"></i></button>
                    </div>
                    <small class="drj-hint"><i class="bi bi-info-circle"></i> Nomor kontak resmi WhatsApp pengajuan refund pelanggan.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Email Operasional</label>
                    <div class="input-group drj-ig ig-blue">
                        <span class="input-group-text ig-icon"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email_operasional" class="form-control" maxlength="100" readonly
                               value="<?= h($settings['email_operasional'] ?? '') ?>">
                        <button type="button" class="btn-lock" data-lock aria-label="Buka kunci untuk mengubah" title="Klik untuk membuka kunci"><i class="bi bi-lock-fill"></i></button>
                    </div>
                    <small class="drj-hint"><i class="bi bi-info-circle"></i> Laporan harian dan konfirmasi sistem dikirim ke email ini.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Alamat Lengkap</label>
                    <div class="input-group drj-ig ig-red">
                        <span class="input-group-text ig-icon"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" name="alamat_lengkap" class="form-control" maxlength="500" readonly
                               value="<?= h($settings['alamat_lengkap'] ?? '') ?>">
                        <button type="button" class="btn-lock" data-lock aria-label="Buka kunci untuk mengubah" title="Klik untuk membuka kunci"><i class="bi bi-lock-fill"></i></button>
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
                        <input type="number" name="batas_waktu_keep_menit" class="form-control" min="1" max="180" required readonly
                               value="<?= (int)$settings['batas_waktu_keep_menit'] ?>">
                        <span class="input-group-text ig-suffix">Menit</span>
                        <button type="button" class="btn-lock" data-lock aria-label="Buka kunci untuk mengubah" title="Klik untuk membuka kunci"><i class="bi bi-lock-fill"></i></button>
                    </div>
                    <small class="drj-hint"><i class="bi bi-stopwatch"></i> Kamar otomatis dilepas ke publik jika pembayaran tidak diverifikasi dalam <?= (int)$settings['batas_waktu_keep_menit'] ?> menit. Berlaku untuk reservasi baru.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Batas Maksimal Pengajuan Refund</label>
                    <div class="input-group drj-ig ig-amber">
                        <span class="input-group-text ig-icon"><i class="bi bi-calendar-x"></i></span>
                        <input type="number" name="batas_refund_hari_sebelum" class="form-control" min="0" max="30" required readonly
                               value="<?= (int)$settings['batas_refund_hari_sebelum'] ?>">
                        <span class="input-group-text ig-suffix">Hari</span>
                        <button type="button" class="btn-lock" data-lock aria-label="Buka kunci untuk mengubah" title="Klik untuk membuka kunci"><i class="bi bi-lock-fill"></i></button>
                    </div>
                    <small class="drj-hint"><i class="bi bi-calendar-check"></i> Tamu bisa mengajukan refund sampai <?= (int)$settings['batas_refund_hari_sebelum'] ?> hari (<?= (int)$settings['batas_refund_hari_sebelum'] * 24 ?> jam) setelah pemesanan dibuat, atau sampai waktu check-in, mana yang lebih dulu. Isi 0 untuk menutup pengajuan refund.</small>
                </div>

                <div class="col-12">
                    <div class="drj-note drj-note-wide">
                        <i class="bi bi-exclamation-triangle"></i>
                        <small><strong>Catatan Operasional:</strong> Sistem refund dilakukan manual via WhatsApp. Sistem admin hanya mencatat status dan log persetujuan.</small>
                    </div>
                </div>
            </div>
        </div>

    <div class="settings-section sec-green mb-4">
        <div class="d-flex gap-3 mb-4">
            <div class="settings-icon-box ico-green"><i class="bi bi-image"></i></div>
            <div>
                <h5 class="fw-bold mb-1">Foto Profil Villa</h5>
                <small class="text-muted">Foto ini hanya digunakan di halaman Profil &amp; Fasilitas.</small>
            </div>
        </div>

        <?php $fotoProfil = profile_photo_url(); ?>
        <?php $fotoProfilFile = dirname(__DIR__, 3) . '/public/' . $fotoProfil; ?>
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <img id="fotoProfilPreview" src="<?= h($fotoProfil) ?>" alt="Pratinjau foto profil villa" class="img-fluid rounded-3 border" style="width: 100%; max-height: 240px; object-fit: cover;" onerror="this.classList.add('d-none')">
                <?php if (!is_file($fotoProfilFile)): ?><div class="alert alert-warning small mb-0">Belum ada foto profil. Unggah foto villa untuk ditampilkan.</div><?php endif; ?>
            </div>
            <div class="col-md-7">
                <label class="form-label small fw-bold" for="fotoProfilInput">Pilih foto villa</label>
                <input type="file" name="foto_profil" id="fotoProfilInput" class="form-control" accept="image/jpeg,image/png,image/webp">
                <div id="fotoProfilStatus" class="small text-success mt-2 d-none" role="status" aria-live="polite"></div>
                <small class="d-block text-muted mt-2">JPG, PNG, atau WEBP. Maksimal 5 MB. Foto hanya berubah setelah Simpan Perubahan ditekan.</small>
            </div>
        </div>
    </div>

    <div class="settings-section sec-blue mb-4">
        <div class="d-flex gap-3 mb-4">
            <div class="settings-icon-box ico-blue"><i class="bi bi-images"></i></div>
            <div>
                <h5 class="fw-bold mb-1">Foto Hero Beranda</h5>
                <small class="text-muted">Foto latar utama katalog kamar. Pengaturan ini terpisah dari foto profil villa.</small>
            </div>
        </div>

        <?php
        $fotoHeroList = home_hero_photo_urls();
        $heroAda = hero_photos_configured();
        ?>
        <div class="hero-manager" id="heroManager" data-min="<?= HERO_MIN_PHOTOS ?>" data-max="<?= HERO_MAX_PHOTOS ?>">
            <div class="hero-grid" id="heroGrid">
                <?php if ($heroAda): foreach ($fotoHeroList as $index => $fotoHero): ?>
                    <div class="hero-tile" data-file="<?= h(basename($fotoHero)) ?>">
                        <img src="<?= h($fotoHero) ?>" alt="Foto hero <?= $index + 1 ?>">
                        <button type="button" class="hero-remove" title="Hapus foto ini" aria-label="Hapus foto hero <?= $index + 1 ?>">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <div class="hero-empty d-none" id="heroEmpty">Belum ada foto hero. Tambahkan minimal <?= HERO_MIN_PHOTOS ?> foto.</div>
            <div id="heroDeleted"></div>

            <div class="hero-uploader">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <label class="form-label small fw-bold mb-0" for="fotoHeroInput">Tambah foto hero</label>
                    <span class="hero-counter" id="heroCounter" role="status" aria-live="polite"></span>
                </div>
                <input type="file" name="foto_hero[]" id="fotoHeroInput" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
                <div id="fotoHeroStatus" class="small text-primary mt-2 d-none" role="status" aria-live="polite"></div>
                <small class="d-block text-muted mt-2">
                    JPG, PNG, atau WEBP, maksimal 5 MB per foto. Total foto hero <?= HERO_MIN_PHOTOS ?>-<?= HERO_MAX_PHOTOS ?>.
                    Klik tanda <i class="bi bi-x-lg"></i> pada foto untuk menghapus. Perubahan baru diterapkan setelah Simpan Perubahan.
                </small>
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

        <div class="qris-panel">
            <div class="qris-preview">
                <?php if ($qris && $qrisFile && is_file($qrisFile)): ?>
                    <img src="<?= h(ltrim($qris['gambar'], '/')) ?>" alt="QRIS aktif">
                <?php else: ?>
                    <div class="qris-placeholder"><i class="bi bi-qr-code-scan"></i></div>
                <?php endif; ?>
            </div>

            <div class="qris-info">
                <div class="qris-title">QRIS Statis Aktif</div>

                <?php if ($qris && $qrisFile && is_file($qrisFile)): ?>
                    <div class="qris-text text-muted">Diunggah <?= h(date('d/m/Y H:i', strtotime((string)$qris['created_at']))) ?></div>
                <?php else: ?>
                    <div class="alert alert-warning qris-text py-2 mt-2 mb-0">Belum ada file QRIS aktif. Tamu tidak bisa melihat barcode pembayaran sampai kamu mengunggahnya.</div>
                <?php endif; ?>

                <form method="post" action="admin_pengaturan.php" enctype="multipart/form-data" id="formQris" class="mt-3">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="aksi" value="upload_qris">
                    <label class="qris-text fw-semibold mb-2 d-block" for="qrisInput">Ganti QRIS</label>
                    <input type="file" name="qris" id="qrisInput" class="form-control mb-2" accept="image/jpeg,image/png,image/webp" required>
                    <div class="qris-text text-muted">JPG, PNG, atau WEBP. Maksimal 2 MB. QRIS lama otomatis disimpan sebagai riwayat. QRIS baru langsung aktif setelah file dipilih.</div>
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

<script>
(function () {
    var form = document.getElementById('formPengaturan');
    if (!form) return;

    function setLocked(input, btn, locked) {
        input.readOnly = locked;
        btn.classList.toggle('is-open', !locked);
        btn.querySelector('i').className = locked ? 'bi bi-lock-fill' : 'bi bi-unlock-fill';
        btn.title = locked ? 'Klik untuk membuka kunci' : 'Klik untuk mengunci kembali';
        btn.setAttribute('aria-label', btn.title);
    }

    form.querySelectorAll('[data-lock]').forEach(function (btn) {
        var input = btn.parentElement.querySelector('input');
        var original = input.value;
        btn.addEventListener('click', function () {
            if (input.readOnly) {
                setLocked(input, btn, false);
                input.focus();
                if (input.setSelectionRange && input.type !== 'number' && input.type !== 'email') {
                    var n = input.value.length; input.setSelectionRange(n, n);
                }
            } else {
                input.value = original;          // kunci lagi = batalkan perubahan kolom ini
                setLocked(input, btn, true);
            }
        });
    });

    // Enter di kolom tidak boleh langsung menyimpan
    form.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && e.target.tagName === 'INPUT') e.preventDefault();
    });

    [
        ['fotoProfilInput', 'fotoProfilStatus', 'foto profil']
    ].forEach(function (item) {
        var photoInput = document.getElementById(item[0]);
        var photoStatus = document.getElementById(item[1]);
        if (!photoInput || !photoStatus) return;
        photoInput.addEventListener('change', function () {
            if (!photoInput.files.length) return;
            for (var i = 0; i < photoInput.files.length; i++) {
                if (photoInput.files[i].size > 5 * 1024 * 1024) {
                    alert('Ukuran foto terlalu besar (maksimal 5 MB per foto).');
                    photoInput.value = '';
                    photoStatus.classList.add('d-none');
                    return;
                }
            }
            photoStatus.textContent = 'File ' + item[2] + ' dipilih: ' + photoInput.files[0].name + '. Klik Simpan Perubahan untuk menerapkannya.';
            photoStatus.classList.remove('d-none');
        });
    });
    // ---- Foto hero beranda: hapus per foto, tambah foto, maksimal 6 ----
    (function () {
        var box = document.getElementById('heroManager');
        if (!box) return;
        var MIN = parseInt(box.dataset.min, 10), MAX = parseInt(box.dataset.max, 10);
        var MAX_BYTES = 5 * 1024 * 1024;
        var grid = document.getElementById('heroGrid');
        var deletedBox = document.getElementById('heroDeleted');
        var input = document.getElementById('fotoHeroInput');
        var counter = document.getElementById('heroCounter');
        var status = document.getElementById('fotoHeroStatus');
        var empty = document.getElementById('heroEmpty');
        var dt = new DataTransfer();
        var previews = [];
        var changed = false;

        function keptCount() { return grid.querySelectorAll('.hero-tile[data-file]').length; }
        function total() { return keptCount() + dt.files.length; }

        function refresh() {
            var n = total();
            counter.textContent = n + ' / ' + MAX + ' foto';
            counter.classList.toggle('is-bad', changed && (n < MIN || n > MAX));
            input.disabled = n >= MAX;
            empty.classList.toggle('d-none', n > 0);
            if (n >= MAX) {
                status.textContent = 'Batas ' + MAX + ' foto tercapai. Hapus satu foto untuk menambah yang baru.';
                status.classList.remove('d-none');
            } else if (changed && n < MIN) {
                status.textContent = 'Foto hero minimal ' + MIN + '. Tambahkan ' + (MIN - n) + ' foto lagi sebelum menyimpan.';
                status.classList.remove('d-none');
            } else {
                status.classList.add('d-none');
            }
        }

        function renderNew() {
            grid.querySelectorAll('.hero-tile[data-new]').forEach(function (t) { t.remove(); });
            previews.forEach(function (u) { URL.revokeObjectURL(u); });
            previews = [];
            Array.prototype.forEach.call(dt.files, function (file, idx) {
                var url = URL.createObjectURL(file);
                previews.push(url);
                var tile = document.createElement('div');
                tile.className = 'hero-tile';
                tile.setAttribute('data-new', idx);
                tile.innerHTML = '<img alt=""><span class="hero-badge">Baru</span>' +
                    '<button type="button" class="hero-remove" title="Batalkan foto ini" aria-label="Batalkan foto baru">' +
                    '<i class="bi bi-x-lg"></i></button>';
                tile.querySelector('img').src = url;
                tile.querySelector('.hero-remove').addEventListener('click', function () {
                    var next = new DataTransfer();
                    Array.prototype.forEach.call(dt.files, function (f, i) { if (i !== idx) next.items.add(f); });
                    dt = next;
                    input.files = dt.files;
                    renderNew();
                    refresh();
                });
                grid.appendChild(tile);
            });
        }

        grid.querySelectorAll('.hero-tile[data-file]').forEach(function (tile) {
            tile.querySelector('.hero-remove').addEventListener('click', function () {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'hapus_hero[]';
                hidden.value = tile.dataset.file;
                deletedBox.appendChild(hidden);
                tile.remove();
                changed = true;
                refresh();
            });
        });

        input.addEventListener('change', function () {
            var picked = Array.prototype.slice.call(input.files);
            var skipped = 0, tooBig = 0, notImage = 0;
            picked.forEach(function (f) {
                var dup = Array.prototype.some.call(dt.files, function (x) {
                    return x.name === f.name && x.size === f.size && x.lastModified === f.lastModified;
                });
                if (dup) return;
                if (['image/jpeg', 'image/png', 'image/webp'].indexOf(f.type) === -1) { notImage++; return; }
                if (f.size > MAX_BYTES) { tooBig++; return; }
                if (total() >= MAX) { skipped++; return; }
                dt.items.add(f);
            });
            input.files = dt.files;
            changed = true;
            renderNew();
            refresh();
            var msg = [];
            if (notImage) msg.push(notImage + ' file bukan JPG/PNG/WEBP');
            if (tooBig) msg.push(tooBig + ' foto lebih dari 5 MB');
            if (skipped) msg.push(skipped + ' foto melebihi batas ' + MAX);
            if (msg.length) alert('Tidak ditambahkan: ' + msg.join(', ') + '.');
        });

        form.addEventListener('submit', function (e) {
            var n = total();
            if (changed && (n < MIN || n > MAX)) {
                e.preventDefault();
                alert('Foto hero harus ' + MIN + '-' + MAX + ' foto. Saat ini ada ' + n + ' foto.');
                box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        refresh();
    })();
    // QRIS: pilih file langsung diunggah (tanpa tombol terpisah)
    (function () {
        var qrisInput = document.getElementById('qrisInput');
        if (!qrisInput) return;
        qrisInput.addEventListener('change', function () {
            var f = qrisInput.files[0];
            if (!f) return;
            if (['image/jpeg', 'image/png', 'image/webp'].indexOf(f.type) === -1) {
                alert('Format gambar harus JPG, PNG, atau WEBP.');
                qrisInput.value = '';
                return;
            }
            if (f.size > 2 * 1024 * 1024) {
                alert('Ukuran gambar terlalu besar (maksimal 2 MB).');
                qrisInput.value = '';
                return;
            }
            document.getElementById('formQris').submit();
        });
    })();
})();
</script>