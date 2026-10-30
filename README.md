# villa-dr-jionsi

## Menjalankan di Laragon dengan PostgreSQL

1. Letakkan folder proyek di direktori `www` Laragon.
2. Jalankan Apache atau Nginx dan arahkan document root virtual host ke folder `public`.
3. Jalankan PostgreSQL. Buat database `jionsi_db`, lalu dari root proyek jalankan:

   ```powershell
   createdb -U postgres jionsi_db
   psql -U postgres -d jionsi_db -f database/schema.sql
   psql -U postgres -d jionsi_db -f database/seed.sql
   ```

4. Aktifkan ekstensi PHP `pdo_pgsql` dan `fileinfo`. Pastikan direktori proyek dapat membuat `storage/bukti-pembayaran`; berkas bukti disimpan di luar document root `public`.
5. Salin `.env.example` menjadi `.env`, lalu sesuaikan `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, dan `DB_PASS`. Variabel `APP_TIMEZONE` menentukan zona waktu aplikasi dan koneksi database.

Nilai default database di `config/db.php` adalah `jionsi_db` pada `localhost:5432` dengan user `postgres`. Sesuaikan dengan instalasi PostgreSQL lokal Anda.

## Alur pengunjung

- Katalog dan halaman detail kamar membaca data kamar dari PostgreSQL.
- Pemesanan memvalidasi jadwal, jumlah tamu, dan ketersediaan di server. Tagihan dihitung dari tarif kamar per malam; durasi dibulatkan ke atas per 24 jam.
- Booking ditahan selama `batas_waktu_keep_menit` dari `pengaturan_villa`. Pengunjung mengunggah bukti pembayaran QRIS sebelum batas waktu tersebut.
- Status reservasi dicari dengan kode booking dan empat digit terakhir nomor WhatsApp.
- Refund hanya dapat diajukan pada reservasi terverifikasi yang masih memenuhi batas hari dari `batas_refund_hari_sebelum`. Sistem mencatat pengajuan untuk diproses pengelola.
