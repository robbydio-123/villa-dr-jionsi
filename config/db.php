<?php

declare(strict_types=1);
function muat_env(): void
{
    static $sudah_dimuat = false;
    if ($sudah_dimuat) {
        return;
    }
    $sudah_dimuat = true;

    $kandidat = [__DIR__ . '/.env', dirname(__DIR__) . '/.env'];
    foreach ($kandidat as $path) {
        if (!is_readable($path)) {
            continue;
        }
        $baris = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($baris as $b) {
            $b = trim($b);
            if ($b === '' || $b[0] === '#' || strpos($b, '=') === false) {
                continue;
            }
            [$kunci, $nilai] = explode('=', $b, 2);
            $kunci = trim($kunci);
            $nilai = trim($nilai);
            // buang tanda kutip pembungkus jika ada
            if (strlen($nilai) >= 2 && ($nilai[0] === '"' || $nilai[0] === "'")
                && substr($nilai, -1) === $nilai[0]) {
                $nilai = substr($nilai, 1, -1);
            }
            $_ENV[$kunci] = $nilai;
            putenv("$kunci=$nilai");
        }
        return;
    }
}

function env(string $kunci, ?string $bawaan = null): ?string
{
    muat_env();
    if (isset($_ENV[$kunci])) {
        return $_ENV[$kunci];
    }
    $nilai = getenv($kunci);
    return $nilai !== false ? $nilai : $bawaan;
}

date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Jakarta'));

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        env('DB_HOST', 'localhost'),
        env('DB_PORT', '5432'),
        env('DB_NAME', 'jionsi_db')
    );

    try {
        $pdo = new PDO($dsn, env('DB_USER', 'postgres'), env('DB_PASS', ''), [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // Samakan zona waktu sesi database dengan aplikasi (kolom TIMESTAMP tanpa zona)
        $zona = env('APP_TIMEZONE', 'Asia/Jakarta');
        $pdo->exec('SET TIME ZONE ' . $pdo->quote($zona));
    } catch (PDOException $e) {
        // Catat detail di log server, jangan tampilkan kredensial ke pengunjung
        error_log('[koneksi.php] Gagal konek database: ' . $e->getMessage());
        http_response_code(500);
        if (env('APP_ENV', 'production') === 'development') {
            exit('Koneksi database gagal: ' . $e->getMessage());
        }
        exit('Layanan sedang bermasalah. Silakan coba beberapa saat lagi.');
    }

    return $pdo;
}