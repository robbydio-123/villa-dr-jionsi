<?php
// ===== Data ulasan (ubah di sini kalau rating/ulasan berubah) =====
$ulasanMapsUrl = 'https://maps.app.goo.gl/H8mi6mZidgHW24ML6';
$ulasanRating  = '4,8';
$ulasanTotal   = 28;
// Persentase lebar bar per bintang (perkiraan dari tampilan Google Maps)
$ulasanBar = [5 => 90, 4 => 8, 3 => 1, 2 => 4, 1 => 1];
$ulasanList = [
    ['inisial' => '', 'warna' => '#1a73e8', 'teks' => 'Tempat nya nyaman, ada <strong>air</strong> panasnya, <strong>pelayanan</strong> super ramah, istimewa 👍🏽👍🏽👍🏽'],
    ['inisial' => 'S', 'warna' => '#8e5bc7', 'teks' => 'Langganan setiap ke <strong>batu</strong> malang'],
    ['inisial' => 't', 'warna' => '#0f4c3a', 'teks' => 'Pelayanannya <strong>bagus</strong>, yg punya ramah, kamarnya bersih 👍🏽👍🏽👍🏽'],
];
?>
<section class="ulasan-tamu" aria-labelledby="ulasanTamuJudul">
    <div class="container">
        <div class="row align-items-center gy-4 gx-xl-5">
            <div class="col-lg-4">
                <h2 id="ulasanTamuJudul" class="ulasan-judul fw-bolder mb-3">Kata tamu yang sudah menginap</h2>
                <p class="ulasan-teks mb-3">Kamar bersih, air panas tersedia, dan pemilik yang ramah adalah hal yang paling sering disebut tamu kami.</p>
                <p class="ulasan-sumber mb-0"><i class="bi bi-patch-check-fill me-2"></i>Penilaian asli dari tamu di Google Maps</p>
            </div>

            <div class="col-lg-8">
                <div class="ulasan-panel">
                    <div class="row g-4 g-md-5">
                        <div class="col-md-5">
                            <a href="<?= h($ulasanMapsUrl) ?>" class="ulasan-rating-link" target="_blank" rel="noopener noreferrer"
                               aria-label="Rating <?= h($ulasanRating) ?> dari 5 berdasarkan <?= (int)$ulasanTotal ?> ulasan. Buka di Google Maps (tab baru)">
                                <span class="d-flex align-items-center gap-4 mb-4">
                                    <span class="ulasan-skor"><?= h($ulasanRating) ?></span>
                                    <span>
                                        <span class="ulasan-bintang d-block">
                                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                        </span>
                                        <span class="ulasan-jumlah d-block mt-1"><?= (int)$ulasanTotal ?> ulasan</span>
                                    </span>
                                </span>
                                <span class="ulasan-bars d-block" aria-hidden="true">
                                    <?php foreach ($ulasanBar as $bintang => $lebar): ?>
                                        <span class="ulasan-bar-row">
                                            <span class="ulasan-bar-label"><?= (int)$bintang ?></span>
                                            <span class="ulasan-bar"><span style="width: <?= (int)$lebar ?>%"></span></span>
                                        </span>
                                    <?php endforeach; ?>
                                </span>
                                <span class="ulasan-cta">
                                    <i class="bi bi-geo-alt-fill"></i> Lihat di Google Maps <i class="bi bi-box-arrow-up-right ulasan-cta-ikon"></i>
                                </span>
                            </a>
                        </div>

                        <div class="col-md-7">
                            <ul class="list-unstyled ulasan-daftar mb-0">
                                <?php foreach ($ulasanList as $u): ?>
                                    <li class="d-flex gap-3">
                                        <span class="ulasan-avatar" style="background: <?= h($u['warna']) ?>;">
                                            <?= $u['inisial'] !== '' ? h($u['inisial']) : '<i class="bi bi-person-fill"></i>' ?>
                                        </span>
                                        <blockquote class="mb-0">&ldquo;<?= $u['teks'] ?>&rdquo;</blockquote>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="ulasan-panel-foot">Ulasan tamu di Google Maps</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>