# Panduan Kontribusi

Dokumen ini mengatur cara tim 5 orang bekerja di repositori Sistem Reservasi Villa D-R Jionsi. Tujuannya agar `main` selalu bisa dijalankan, perubahan bisa ditelusuri, dan kontribusi setiap anggota tercatat. Instalasi dan cara menjalankan aplikasi ada di [README.md](README.md). Aturan alur kerja Git dan format commit yang berlaku sama dengan bagian "Panduan Kerja Tim" dan "Format Pesan Commit" di README.

## Daftar Isi

1. [Peran dalam Repositori](#peran-dalam-repositori)
2. [Persiapan Lingkungan](#persiapan-lingkungan)
3. [Alur Kerja Git](#alur-kerja-git)
4. [Format Pesan Commit](#format-pesan-commit)
5. [Kerja Bersama Live Share dan Pencatatan Kontribusi](#kerja-bersama-live-share-dan-pencatatan-kontribusi)
6. [Peninjauan Perubahan](#peninjauan-perubahan)
7. [Standar Kode](#standar-kode)
8. [Aturan Basis Data](#aturan-basis-data)
9. [Pengujian Sebelum Push](#pengujian-sebelum-push)
10. [Dokumentasi dan Logbook](#dokumentasi-dan-logbook)
11. [Rilis dan Tag](#rilis-dan-tag)
12. [Keamanan dan Privasi](#keamanan-dan-privasi)
13. [Penggunaan AI](#penggunaan-ai)
14. [Melaporkan Masalah dan Meminta Fitur](#melaporkan-masalah-dan-meminta-fitur)

## Peran dalam Repositori

| Anggota | Peran di repositori |
|---|---|
| Dewi Muji Rahayu | Maintainer dan lead backend. Memantau kondisi `main`, memegang keputusan skema database, dan membuat tag milestone. |
| Robby Dio Ramadhani | Reviewer dan QA. Menguji hasil kerja di `main` dan memverifikasi ulang perbaikan bug. |
| Gendis Kumala Ariga, Muhammad Afif Naufal, Daffa' Azzi Maulana A | Kontributor. Commit dan push ke `main` dengan mengikuti aturan di dokumen ini. |

Dewi dan Robby bersama-sama menjaga aturan repositori dan kualitas `main`.

## Persiapan Lingkungan

1. Ikuti bagian Instalasi di [README.md](README.md).
2. Setiap kali menarik perubahan terbaru, cek apakah folder `database/` berubah:

   ```bash
   git pull origin main
   ```

   Bila skema berubah, buat ulang database lokal dari nol:

   ```bash
   dropdb -U postgres jionsi_db
   createdb -U postgres jionsi_db
   psql -U postgres -d jionsi_db -f database/schema.sql
   psql -U postgres -d jionsi_db -f database/seed.sql
   psql -U postgres -d jionsi_db -f database/harga_paket.sql
   psql -U postgres -d jionsi_db -f database/migrasi_fasilitas.sql
   ```

   Untuk mengosongkan data reservasi saja, jalankan `database/bersihkan_reservasi.sql`.

## Alur Kerja Git

Repositori ini hanya memakai **satu branch, yaitu `main`**. Tidak ada branch `develop` maupun branch fitur, dan tidak memakai Pull Request. Semua anggota commit dan push langsung ke `main`.

```bash
git checkout main
git pull origin main          # sebelum mulai
git status                    # periksa berkas yang berubah
git add <berkas yang dikerjakan>   # hindari .env
git commit -m "feat(katalog): tampilkan kamar dari database"
git pull --rebase origin main # ambil perubahan terbaru sebelum push
git push origin main
```

Aturan:

- Jalankan `git pull origin main` sebelum mulai dan sebelum push.
- Tes dulu di lokal. Jangan push kode yang membuat aplikasi error.
- Commit kecil dan sering, satu commit untuk satu perubahan yang jelas.
- Beri tahu di grup bila akan mengubah berkas yang sering dipakai bersama (misalnya `style.css`, `schema.sql`, `db.php`).
- Bila ada konflik saat `git pull --rebase`, selesaikan konfliknya, jalankan `git add` pada berkas yang diperbaiki, lalu `git rebase --continue`. Bila ragu, tanyakan ke Project Manager.
- Dilarang `git push --force` ke `main`.

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

Contoh dari proyek ini: `feat(keep): tambah proteksi race condition pada keep kamar`.

## Kerja Bersama Live Share dan Pencatatan Kontribusi

Tim sering bekerja lewat VS Code Live Share. Perubahan semua peserta tersimpan di komputer host, sehingga hanya akun host yang tampil sebagai pembuat commit dan pelaku push di GitHub. Agar kontribusi anggota lain tetap tercatat:

1. Commit hasil sesi bersama mencantumkan peserta lewat baris `Co-authored-by` di akhir pesan commit, dipisah satu baris kosong dari isi pesan. Baris pertama tetap mengikuti format di atas.

   ```bash
   git commit -m "feat(katalog): tambah filter fasilitas cepat" -m "Co-authored-by: Daffa' Azzi Maulana A <email-github-daffa@example.com>
   Co-authored-by: Muhammad Afif Naufal <email-github-afif@example.com>"
   ```

   Ganti alamat email contoh dengan email yang terhubung ke akun GitHub masing-masing agar nama mereka tampil sebagai ko-penulis.
2. Logbook mingguan tetap menjadi catatan utama: kolom "Dikerjakan oleh" diisi anggota yang benar-benar mengerjakan, bukan nama host.
3. Jumlah commit di GitHub bukan ukuran kontribusi. Lihat catatan di README.

## Peninjauan Perubahan

Karena tidak memakai Pull Request, peninjauan dilakukan setelah push:

- Robby menjalankan uji manual pada `main` terbaru dan mencatat temuan sebagai Issue.
- Dewi memantau kondisi `main` dan memeriksa perubahan pada berkas bersama (`schema.sql`, `db.php`, `style.css`).
- Pembuat perubahan memperbaiki temuan dengan commit `fix(...)` baru, bukan dengan menulis ulang riwayat.

Hal yang diperiksa: prepared statement, escaping keluaran, validasi di server, tidak ada kredensial, tampilan responsif, dan skrip untuk perubahan database.

## Standar Kode

Berdasarkan pola yang dipakai di kode:

- **Struktur:** halaman di `public/*.php` hanya menerima permintaan dan memuat view. Logika bisnis dan akses database ada di fungsi `app/*.php`. Tampilan di `app/views/`. Jangan menaruh query SQL di view.
- **Penamaan:** berkas dan fungsi baru memakai huruf kecil dengan garis bawah, berkas admin berawalan `admin_`. Ikuti gaya berkas di sekitarnya.
- **PHP:** `declare(strict_types=1);` di berkas baru.
- **Database:** selalu PDO prepared statement, dilarang menyambung string SQL. Gunakan `db()` dari `config/db.php`. Operasi yang mengubah beberapa tabel dilakukan dalam transaksi.
- **Validasi:** semua input divalidasi di server (contoh: `create_booking()`), form POST memakai `verify_csrf()`.
- **Keluaran:** escape dengan `h()` (pembungkus `htmlspecialchars`) pada setiap variabel yang dicetak.
- **Kata sandi:** `password_hash` dan `password_verify`, tidak pernah disimpan polos.
- **Unggahan:** periksa tipe dari isi berkas, batasi ukuran, dan beri nama acak.
- **Frontend:** Bootstrap 5 dan Bootstrap Icons, CSS di `public/assets/css/style.css`, JS di `public/assets/js/`. Tampilan responsif pada 360, 768, dan 1280 px dan mengikuti desain Figma.
- **Larangan:** tidak ada kredensial atau URL rahasia di kode. Semua lewat `.env` dan fungsi `env()`.

## Aturan Basis Data

- Perubahan skema wajib diinfokan ke seluruh tim sebelum push, dan disetujui Dewi sebagai pemegang database.
- Sertakan skrip migrasi di `database/` (misalnya `migrasi_<topik>.sql`, ditulis idempoten dengan `IF NOT EXISTS`) dan perbarui `schema.sql` agar instalasi baru ikut mendapat perubahan. Perbarui `seed.sql` bila data uji berubah.
- Catat perubahan di Governance Log atau CHANGELOG proyek.
- Tulis di pesan commit atau grup urutan menjalankan skrip dan dampaknya pada data.
- Data uji hanya dummy, tanpa reservasi atau bukti transfer asli.

## Pengujian Sebelum Push

Belum ada pengujian otomatis, jadi uji manual alur berikut:

- [ ] Keep kamar: hitung mundur berjalan, kamar tidak bisa dipesan ganda pada jadwal yang sama.
- [ ] Keep habis tanpa bukti: status menjadi Kedaluwarsa dan kamar bebas.
- [ ] Unggah bukti: format dan ukuran salah ditolak, bukti valid membuat status Menunggu Konfirmasi.
- [ ] Verifikasi admin: setujui menjadi Dibayar, tolak memungkinkan unggah ulang.
- [ ] Refund: pengajuan sebelum batas berhasil, setelah batas ditolak, admin dapat menyetujui atau menolak.
- [ ] Halaman admin tidak dapat dibuka tanpa login.

Pencatatan bug (GitHub Issue atau lembar QA): ID `BUG-001`, `BUG-002`, dan seterusnya; tingkat keparahan Kritis, Tinggi, Sedang, atau Rendah; langkah reproduksi; hasil diharapkan dan hasil sebenarnya; tangkapan layar. Robby memverifikasi ulang perbaikan sebelum Issue ditutup.

## Dokumentasi dan Logbook

- Setiap kontribusi dicatat di logbook mingguan dengan kolom "Dikerjakan oleh".
- Sebutkan kode WBS tugas di pesan commit bila ada.
- Gendis memeriksa kelengkapan dokumentasi tiap milestone.

## Rilis dan Tag

- Tag dibuat oleh Project Manager di `main`, hanya dari commit yang sudah diuji.
- Setelah batas pengumpulan, tag tidak dipindahkan atau ditimpa.

```bash
git checkout main && git pull origin main
git tag -a milestone-2 -m "Milestone 2: Sistem Reservasi Villa D-R Jionsi"
git push origin milestone-2
```

## Keamanan dan Privasi

Jangan pernah meng-commit: `.env`, kredensial atau kata sandi, data pelanggan asli, bukti transfer asli, maupun nomor rekening nyata. Gunakan data dummy. `.env` dan `storage/bukti-pembayaran/` sudah ada di `.gitignore`; jangan menghapusnya. Bila tidak sengaja meng-commit rahasia, segera beri tahu Dewi dan ganti kredensial tersebut.

## Penggunaan AI

AI boleh dipakai sebagai alat bantu. Pembuat commit harus memahami dan sanggup menjelaskan kode yang dikirim. Setiap penggunaan AI dicatat untuk Lampiran H laporan.

## Melaporkan Masalah dan Meminta Fitur

Gunakan GitHub Issues. Label yang disarankan: `bug`, `fitur`, `dokumentasi`, `database`, `frontend`, `backend`, `pertanyaan`, `prioritas-tinggi`. Isi judul yang jelas, langkah reproduksi (untuk bug) atau tujuan dan manfaat (untuk fitur), dan tangkapan layar bila perlu.