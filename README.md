# Villa D-R Jionsi: Katalog dan Reservasi Villa Kota Batu

Website katalog dan reservasi kamar untuk Villa D-R Jionsi (Songgokerto, Kota Batu) yang dibangun dengan PHP Native dan PostgreSQL. Tamu dapat melihat kondisi kamar yang sebenarnya, menahan (keep) kamar selama 15 menit, mengunggah bukti pembayaran, dan memantau status reservasi tanpa membuat akun. Pengelola memverifikasi pembayaran dan mengelola data kamar lewat panel admin.

Proyek ini dikerjakan oleh Kelompok 3 SIB-2B sebagai Project-Based Learning (PBL) Semester 3, Program Studi D4 Sistem Informasi Bisnis, Politeknik Negeri Malang.

## Daftar Isi

1. [Fitur Utama](#fitur-utama)
2. [Alur Reservasi](#alur-reservasi)
3. [Struktur Folder](#struktur-folder)
4. [Daftar Halaman](#daftar-halaman)
5. [Kebutuhan Sistem](#kebutuhan-sistem)
6. [Instalasi dan Menjalankan Aplikasi](#instalasi-dan-menjalankan-aplikasi)
7. [Keamanan dan Konvensi Kode](#keamanan-dan-konvensi-kode)
8. [Panduan Kerja Tim](#panduan-kerja-tim)
9. [Format Pesan Commit](#format-pesan-commit)
10. [Tim Pengembang](#tim-pengembang)

## Fitur Utama

| Modul | Deskripsi |
|---|---|
| Katalog dan Pencarian Kamar | Menampilkan kamar beserta status Tersedia, Di-Keep, atau Terisi, dilengkapi filter kapasitas, rentang harga, tanggal menginap, dan pencarian kata kunci. |
| Transparansi Kondisi Kamar | Galeri foto kondisi terkini, catatan kebersihan, dan kondisi fisik kamar lengkap dengan tanggal pembaruan. |
| Reservasi dan Keep | Pemesanan tanpa akun, keep kamar 15 menit, kode referensi unik, unggah bukti pembayaran, dan pelepasan otomatis saat waktu keep habis. |
| Cek Status Reservasi | Tamu memantau status reservasi memakai kode referensi. |
| Refund | Tamu mengajukan pembatalan dan refund (batas H-1 sebelum check-in pukul 23.59 WIB), admin meninjau dan mencatat hasilnya. |
| Panel Admin | Login pengelola, dashboard, verifikasi dan pengelolaan reservasi, peninjauan refund, kelola kamar, dan pengaturan. |

> **Catatan:** fasilitas kamar bersifat seragam (air panas, Smart TV, Wi-Fi, dan seterusnya), sehingga ditulis pada deskripsi kamar. Tidak ada halaman master fasilitas di panel admin.

## Alur Reservasi

1. Tamu memilih kamar dan tanggal menginap di katalog.
2. Tamu mengisi data pemesan. Kamar langsung berstatus Di-Keep selama 15 menit.
3. Tamu membayar penuh lalu mengunggah bukti sebelum waktu keep habis.
4. Admin memeriksa bukti. Bukti yang sesuai dikonfirmasi, bukti yang tidak sesuai ditolak.
5. Bila waktu habis tanpa bukti, reservasi menjadi Kedaluwarsa dan kamar kembali tersedia.

## Struktur Folder

```
villa-dr-jionsi/
├── .env.example            # Contoh pengaturan lingkungan (salin menjadi .env)
├── .gitignore
├── README.md
│
├── app/
│   ├── public_helpers.php  # Fungsi bantu yang dipakai halaman publik
│   └── views/              # Potongan tampilan yang dipakai bersama
│       ├── layouts/        # Header, navbar, footer
│       ├── home/           # Bagian beranda
│       ├── kamar/          # Kartu kamar dan detail kamar
│       ├── reservasi/      # Form reservasi dan status
│       ├── profil/         # Profil villa
│       └── admin/          # Tampilan panel admin
│
├── config/
│   └── db.php              # Koneksi PDO ke PostgreSQL
│
├── database/
│   ├── schema.sql          # Skema tabel
│   └── seed.sql            # Data awal
│
└── public/                 # Satu-satunya folder yang diakses browser
    ├── *.php               # Halaman (lihat Daftar Halaman)
    └── assets/
        ├── css/            # style.css, refund.css
        ├── js/             # formulir.js, katalog.js, navbar.js, refund_tamu.js, refund_admin.js
        └── img/            # Logo dan gambar
```

## Daftar Halaman

### Halaman tamu

| Berkas | Fungsi |
|---|---|
| `index.php` | Beranda dan katalog kamar |
| `detail.php` | Detail kamar dan kondisi terkini |
| `formulir.php` | Form reservasi |
| `keep.php` | Keep kamar dan pembayaran |
| `verifikasi.php` | Unggah dan verifikasi bukti pembayaran |
| `ditolak.php` | Informasi bukti pembayaran ditolak |
| `waktu_habis.php` | Informasi waktu keep habis |
| `cek_reservasi.php`, `status.php`, `hasil_status.php` | Cek status reservasi |
| `refund.php` | Pengajuan refund |
| `profil.php` | Profil villa dan kontak |
| `login.php` | Login pengelola |

### Halaman admin

| Berkas | Fungsi |
|---|---|
| `admin.php` | Dashboard |
| `admin_reservasi.php` | Pengelolaan reservasi |
| `admin_kamar.php` | Kelola kamar (data, deskripsi dan fasilitas, foto, catatan kondisi) |
| `admin_refund.php` | Peninjauan refund |
| `admin_pengaturan.php` | Pengaturan villa |

## Kebutuhan Sistem

| Komponen | Spesifikasi Minimal |
|---|---|
| Bahasa Pemrograman | PHP >= 8.0 |
| Database | PostgreSQL >= 13 (port bawaan 5432) |
| Ekstensi PHP wajib | `pdo_pgsql`, `pgsql`, `mbstring`, `fileinfo` |
| Server lokal | Server bawaan PHP (`php -S`), XAMPP atau Laragon opsional |
| Alat bantu | Git, DBeaver atau pgAdmin, Figma |

## Instalasi dan Menjalankan Aplikasi

### 1. Ambil kode

```bash
git clone https://github.com/robbydio-123/villa-dr-jionsi.git
cd villa-dr-jionsi
```

### 2. Salin pengaturan lingkungan

```bash
cp .env.example .env
```

Di Windows PowerShell: `Copy-Item .env.example .env`. Isi `.env` dengan data PostgreSQL di laptopmu (host, port, nama database, pengguna, password). Berkas `.env` tidak boleh di-commit. Dan hilangkan tulisan examplenya.

### 3. Buat dan isi database

Buat database (nama sesuai isi `.env`), lalu impor skema dan data awal dengan `psql`:

```bash
psql -U postgres -d nama_database -f database/schema.sql
psql -U postgres -d nama_database -f database/seed.sql
```

Cek di DBeaver bahwa tabel sudah terbentuk dan terisi.

### 4. Aktifkan ekstensi PostgreSQL di PHP

Pastikan baris berikut di `php.ini` tidak diawali tanda titik koma:

```ini
extension=pdo_pgsql
extension=pgsql
extension=fileinfo
extension=mbstring
```

Cek dengan `php -m`.

### 5. Jalankan aplikasi

```bash
php -S localhost:8000 -t public
```

Buka http://localhost:8000 di browser. Hentikan server dengan `Ctrl+C`.

## Keamanan dan Konvensi Kode

Aturan berikut wajib dipatuhi seluruh anggota tim:

1. **Gunakan prepared statement.** Dilarang menyambung string SQL.

   ```php
   // SALAH
   $pdo->query("SELECT * FROM kamar WHERE nomor_kamar = '$nomor'");

   // BENAR
   $stmt = $pdo->prepare('SELECT * FROM kamar WHERE nomor_kamar = :nomor');
   $stmt->execute(['nomor' => $nomor]);
   ```

2. **Amankan keluaran HTML** dengan `htmlspecialchars($nilai, ENT_QUOTES, 'UTF-8')` pada setiap variabel yang dicetak, untuk mencegah XSS.
3. **Unggahan bukti pembayaran:** maksimal 2 MB, hanya JPG, PNG, atau WEBP. Tipe dicek dari isi berkas, bukan dari ekstensi, dan nama berkas dibuat acak.
4. **Lindungi halaman admin:** semua berkas `admin*.php` harus memeriksa login di bagian paling atas.
5. **Transaksi reservasi:** pemesanan kamar dilakukan dalam satu transaksi database agar tidak terjadi pemesanan ganda.
6. **Waktu keep** dihitung dari jam server, bukan jam peramban.
7. **Jangan commit rahasia.** Berkas `.env`, password, dan berkas unggahan dilarang masuk ke Git.

## Panduan Kerja Tim

Repositori ini hanya memakai **satu branch, yaitu `main`**. Tidak ada branch `develop` maupun branch fitur, dan tidak memakai Pull Request. Semua anggota commit dan push langsung ke `main`. Karena itu, disiplin pada langkah di bawah wajib dijaga supaya `main` selalu bisa dijalankan.

### 1. Sebelum mulai mengerjakan

Selalu ambil perubahan terbaru dari teman setim:

```bash
git checkout main
git pull origin main
```

### 2. Kerjakan, commit, dan push

```bash
# 1. Periksa file yang berubah
git status

# 2. Tambahkan ke staging (pilih file yang dikerjakan, hindari .env)
git add public/index.php app/views/kamar/

# 3. Commit dengan format yang disepakati
git commit -m "feat(katalog): tampilkan kamar dari database"

# 4. Ambil perubahan terbaru lagi sebelum push
git pull --rebase origin main

# 5. Kirim ke GitHub
git push origin main
```

### 3. Aturan kerja langsung di main

- Jalankan `git pull origin main` **sebelum mulai** dan **sebelum push**.
- Tes dulu di lokal. Jangan push kode yang membuat aplikasi error.
- Commit kecil dan sering. Satu commit memuat satu perubahan yang jelas.
- Beri tahu di grup bila akan mengubah berkas yang sering dipakai bersama (misalnya `style.css`, `schema.sql`, `db.php`) agar tidak bentrok.
- Bila terjadi konflik saat `git pull --rebase`, selesaikan konfliknya, jalankan `git add` pada berkas yang diperbaiki lalu `git rebase --continue`. Bila ragu, tanyakan ke Project Manager.
- Dilarang `git push --force` ke `main`.
- Perubahan skema database (`schema.sql`) wajib diinfokan ke seluruh tim, karena setiap anggota perlu mengimpor ulang database lokalnya.
- Pada setiap milestone, Project Manager membuat tag di `main` (contoh `milestone-2` dan `v1.0-final`):

  ```bash
  git tag milestone-2
  git push origin milestone-2
  ```

## Format Pesan Commit

Format: `tipe(scope): deskripsi singkat`. Scope bersifat opsional.

- `feat`: menambah fitur baru.
  - Contoh: `feat(cart): add checkout button to shopping cart`
- `fix`: memperbaiki bug atau error (tambahkan scope di dalam kurung).
  - Contoh: `fix(auth): resolve token expiration issue on login`
- `docs`: mengubah atau menambah dokumentasi.
  - Contoh: `docs(readme): update installation steps`
- `chore`: tugas pemeliharaan, setup, atau hal di luar kode utama.
  - Contoh: `chore: setup struktur folder proyek`
- `refactor`: merapikan atau mengubah struktur kode tanpa mengubah fungsi.
  - Contoh: `refactor(api): simplify user data fetching logic`
- `style`: perubahan format atau spasi yang tidak mengubah logika kode.
  - Contoh: `style: fix indentation in navbar component`

## Tim Pengembang

| No | Nama | Peran dan Tanggung Jawab Utama |
|---|---|---|
| 1 | Dewi Muji Rahayu | **Project Manager dan System Architect**<br>Mengatur alur proyek dan jadwal, merancang struktur aplikasi, memantau kondisi `main`, membuat tag milestone, serta menjaga keselarasan laporan. |
| 2 | Gendis Kumala Ariga | **Sekretaris dan Documenter**<br>Mengelola dokumentasi non-coding: data kamar dan foto, tangkapan layar bukti kemajuan, penulisan manual book, serta penyusunan lampiran laporan. |
| 3 | Muhammad Afif Naufal | **Backend Developer dan Frontend Supporter**<br>Mengembangkan logika server dan transaksi reservasi, mengintegrasikan database PostgreSQL, serta membantu pengembangan tampilan. |
| 4 | Daffa' Azzi Maulana A | **UI/UX Designer dan Lead Frontend Developer**<br>Merancang antarmuka dan prototipe di Figma, memimpin implementasi tampilan agar sesuai desain, serta menjaga konsistensi visual. |
| 5 | Robby Dio Ramadhani | **Quality Assurance dan Frontend Assistant**<br>Menyusun dan menjalankan pengujian (test case, pencatatan bug, uji keamanan dan responsif), serta membantu pengembangan tampilan. |