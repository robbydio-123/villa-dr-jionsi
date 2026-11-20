<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? 'Portal Pengelola') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>">
</head>

<body class="login-page">
    <main class="login-shell">

        <!-- Sisi kiri: panel branding -->
        <aside class="login-hero">
            <span class="hero-pill"><i></i> PANEL PENGELOLA &amp; OPERASIONAL</span>

            <div class="hero-brand">
                <p class="hero-place"><i class="bi bi-house-door"></i> SONGGOKERTO HIGHLAND</p>
                <h1>Villa D-R Jionsi</h1>
            </div>

            <div class="hero-quote">
                <span class="hero-rule"></span>
                <blockquote>“Kejujuran tata kelola menghadirkan kenyamanan sejati bagi setiap tamu di lereng sejuk Songgokerto.”</blockquote>
                <p>STANDAR PELAYANAN TERBUKA &amp; TERVERIFIKASI</p>
            </div>
        </aside>

        <!-- Sisi kanan: notifikasi + kartu login -->
        <section class="login-side">

            <?php if ($terkunci): ?>
                <div class="login-alert alert-lock" role="alert">
                    <i class="bi bi-lock-fill"></i>
                    <div>
                        <strong>Terlalu Banyak Percobaan</strong>
                        <span>Akses dikunci sementara demi keamanan aset. Coba lagi dalam
                            <b id="countdown" data-sisa="<?= (int)$sisaKunci ?>"><?= sprintf('%02d:%02d', intdiv($sisaKunci, 60), $sisaKunci % 60) ?></b> detik.</span>
                    </div>
                </div>
            <?php elseif ($galat): ?>
                <div class="login-alert alert-error" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>
                        <strong>Kredensial Tidak Cocok</strong>
                        <span>Username atau password salah. Periksa kembali huruf besar/kecil dan capslock keyboard Anda.</span>
                    </div>
                </div>
            <?php elseif ($sesiHabis): ?>
                <div class="login-alert alert-session" role="status">
                    <i class="bi bi-clock-history"></i>
                    <div>
                        <strong>Sesi Kerja Berakhir</strong>
                        <span>Sesi login Anda telah berakhir karena tidak ada aktivitas selama 30 menit demi keamanan sistem. Silakan masuk kembali.</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="login-card<?= $terkunci ? ' is-locked' : '' ?>">
                <div class="card-head">
                    <span class="card-icon"><i class="bi bi-shield-lock-fill"></i></span>
                    <div>
                        <h2>Portal Pengelola</h2>
                        <p>Masuk untuk mengelola reservasi &amp; kamar</p>
                    </div>
                </div>

                <form method="post" action="login.php" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

                    <label class="field-label" for="username">Username</label>
                    <div class="field">
                        <i class="bi bi-person"></i>
                        <input type="text" id="username" name="username" value="<?= h($username) ?>"
                               placeholder="Masukkan username" required autofocus
                               <?= $terkunci ? 'disabled' : '' ?>>
                    </div>

                    <label class="field-label" for="password">Kata Sandi</label>
                    <div class="field">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password"
                               placeholder="Masukkan kata sandi" required
                               <?= $terkunci ? 'disabled' : '' ?>>
                        <button type="button" class="toggle-pw" id="togglePw" aria-label="Tampilkan kata sandi"
                                <?= $terkunci ? 'disabled' : '' ?>><i class="bi bi-eye"></i></button>
                    </div>

                    <label class="remember">
                        <input type="checkbox" name="ingat" value="1" <?= $terkunci ? 'disabled' : '' ?>>
                        <span>Ingat perangkat selama 14 hari</span>
                    </label>

                    <button type="submit" class="btn-login" <?= $terkunci ? 'disabled' : '' ?>>
                        Masuk ke Panel Pengelola <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>

            <a href="beranda.php" class="back-link"><i class="bi bi-arrow-left"></i> Kembali ke Katalog Kamar</a>
        </section>
    </main>

    <script>
        (function () {
            var pw = document.getElementById('password');
            var btn = document.getElementById('togglePw');
            if (btn) {
                btn.addEventListener('click', function () {
                    var show = pw.type === 'password';
                    pw.type = show ? 'text' : 'password';
                    btn.firstElementChild.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }

            var cd = document.getElementById('countdown');
            if (cd) {
                var selesai = Date.now() + parseInt(cd.dataset.sisa, 10) * 1000;
                var tick = function () {
                    var sisa = Math.max(0, Math.ceil((selesai - Date.now()) / 1000));
                    var m = String(Math.floor(sisa / 60)).padStart(2, '0');
                    var s = String(sisa % 60).padStart(2, '0');
                    cd.textContent = m + ':' + s;
                    if (sisa <= 0) { location.href = 'login.php'; }
                };
                setInterval(tick, 1000);
            }
        })();
    </script>
</body>

</html>