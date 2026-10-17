# Aturan Kerja Tim

## Branch
- `main`: versi stabil, siap demo.
- `develop`: gabungan pekerjaan tim.
- Branch fitur dibuat dari `develop` dengan format `fitur/nama-fitur`, misalnya `fitur/login`.

## Aturan
1. Dilarang push langsung ke `main` dan `develop`.
2. Semua perubahan lewat Pull Request ke `develop`.
3. Minimal 1 anggota me-review sebelum merge.
4. `main` hanya diperbarui dari `develop` saat iterasi selesai.
5. Jalankan `git pull` di `develop` sebelum membuat branch baru.

## Format pesan commit
- `feat: tambah halaman daftar kamar`
- `fix: perbaiki validasi form`
- `chore: setup struktur folder`
- `docs: perbarui dokumentasi`