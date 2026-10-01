# villa-dr-jionsi

## Menjalankan di Laragon dengan PostgreSQL

1. Letakkan folder proyek di direktori `www` Laragon.
2. Jalankan Apache atau Nginx dari Laragon. Atur document root virtual host ke folder `public` proyek.
3. Pastikan server PostgreSQL berjalan. PostgreSQL tidak selalu disertakan dalam instalasi Laragon; pasang PostgreSQL terpisah atau tambahkan melalui fitur Quick add jika tersedia.
4. Buat database bernama `villa_dr_jionsi`, lalu jalankan skema dari terminal:

	```powershell
	createdb -U postgres villa_dr_jionsi
	psql -U postgres -d villa_dr_jionsi -f database/schema.sql
	```

5. Pastikan PHP yang dipilih di Laragon mengaktifkan ekstensi `pdo_pgsql` (dan `pgsql` bila diperlukan). Setelah mengubah `php.ini`, restart Apache/Nginx.
6. Sesuaikan kredensial koneksi di `config/config.php`, atau atur environment variable `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, dan `DB_PASSWORD`.

Konfigurasi default memakai `127.0.0.1:5432`, database `villa_dr_jionsi`, dan user `postgres` tanpa password. Sesuaikan dengan pengaturan PostgreSQL lokal Anda.