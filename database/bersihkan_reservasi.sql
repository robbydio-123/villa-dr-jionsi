-- Hapus SEMUA data reservasi di database yang sedang berjalan (kamar, admin, QRIS tetap).
-- Jalankan: psql -U postgres -d jionsi_db -f database/bersihkan_reservasi.sql
BEGIN;
TRUNCATE log_status_reservasi, refund, pembayaran, reservasi RESTART IDENTITY CASCADE;
UPDATE kamar SET status = 'Tersedia';
COMMIT;
