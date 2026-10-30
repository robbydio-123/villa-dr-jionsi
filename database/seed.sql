-- =====================================================================
-- Data dummy jionsi_db (jalankan SETELAH schema.sql)
-- =====================================================================

-- pgcrypto hanya dipakai untuk membuat hash bcrypt password dummy
-- (kompatibel dengan password_verify() di PHP). Password admin: admin123
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ---------------------------------------------------------------------
-- Admin
-- ---------------------------------------------------------------------
INSERT INTO admin_users (nama, nomor_whatsapp, password) VALUES
('Bpk. Hendra', '081233449081', crypt('admin123', gen_salt('bf', 10))),
('Ibu Ratna',   '081233449082', crypt('admin123', gen_salt('bf', 10)))
ON CONFLICT (nomor_whatsapp) DO NOTHING;

-- ---------------------------------------------------------------------
-- Pengaturan villa
-- ---------------------------------------------------------------------
INSERT INTO pengaturan_villa
    (id, nama_properti, whatsapp_utama, email_operasional, alamat_lengkap)
VALUES
    (1, 'Villa D-R Jionsi', '+62 812-3344-9081', 'info@drjionsi.com',
     'Batu Highland Retreat, Jl. Songgoriti No. 18, Batu')
ON CONFLICT (id) DO UPDATE SET
    nama_properti = EXCLUDED.nama_properti,
    whatsapp_utama = EXCLUDED.whatsapp_utama,
    email_operasional = EXCLUDED.email_operasional,
    alamat_lengkap = EXCLUDED.alamat_lengkap;

-- ---------------------------------------------------------------------
-- QRIS (yang lama nonaktif, yang baru aktif)
-- ---------------------------------------------------------------------
INSERT INTO qris_statis (gambar, aktif, diunggah_oleh, created_at) VALUES
('/uploads/qris/qris-lama.png',  FALSE, 1, CURRENT_TIMESTAMP - INTERVAL '60 days'),
('/uploads/qris/qris-aktif.png', TRUE,  1, CURRENT_TIMESTAMP - INTERVAL '10 days');

-- ---------------------------------------------------------------------
-- Kamar (6 unit)
-- ---------------------------------------------------------------------
INSERT INTO kamar (nomor_kamar, tipe_kamar, lantai, kapasitas, harga_per_malam, status, deskripsi, foto_url, kondisi_diperbarui_at) VALUES
('201', 'Family Suite Panderman', 2, 6, 850000, 'Tersedia',
 'Kamar keluarga yang luas di lantai 2, nyaman untuk hingga 6 tamu. Dilengkapi Wi-Fi, Smart TV, dan air panas.',
 '/uploads/kamar/201.jpg', CURRENT_TIMESTAMP - INTERVAL '3 days'),
('101', 'Deluxe Arjuno', 1, 2, 450000, 'Tersedia',
 'Kamar nyaman untuk 2 tamu di lantai 1 dengan air panas, Smart TV, dan kamar mandi dalam.',
 '/uploads/kamar/101.jpg', CURRENT_TIMESTAMP - INTERVAL '5 days'),
('102', 'Twin Welirang', 1, 3, 550000, 'Tersedia',
 'Kamar twin untuk hingga 3 tamu dengan air panas, Smart TV, dan akses dapur bersama.',
 '/uploads/kamar/102.jpg', CURRENT_TIMESTAMP - INTERVAL '2 days'),
('202', 'Family Suite Semeru', 2, 6, 850000, 'Tersedia',
 'Kamar keluarga di lantai 2 dengan pemandangan pegunungan, muat hingga 6 tamu. Dilengkapi Wi-Fi, Smart TV, dan air panas.',
 '/uploads/kamar/202.jpg', CURRENT_TIMESTAMP - INTERVAL '1 day'),
('103', 'Deluxe Kawi', 1, 2, 450000, 'Tersedia',
 'Kamar hangat untuk 2 tamu dengan air panas, Smart TV, dan kamar mandi dalam, cocok untuk pasangan.',
 '/uploads/kamar/103.jpg', CURRENT_TIMESTAMP - INTERVAL '7 days'),
('104', 'Twin Butak', 1, 3, 550000, 'Tersedia',
 'Kamar twin untuk hingga 3 tamu dengan air panas, Smart TV, dan akses dapur bersama.',
 '/uploads/kamar/104.jpg', CURRENT_TIMESTAMP - INTERVAL '4 days')
ON CONFLICT (nomor_kamar) DO NOTHING;

-- ---------------------------------------------------------------------
-- Reservasi (berbagai status; whatsapp_4_digit terisi otomatis)
-- ---------------------------------------------------------------------
INSERT INTO reservasi
 (kode_booking, kamar_id, nama_pemesan, whatsapp_pemesan, jumlah_tamu, catatan,
  waktu_checkin, durasi_jam, waktu_checkout, total_tagihan,
  status_booking, batas_waktu_keep, setuju_kebijakan, created_at)
VALUES
-- Menunggu Konfirmasi (contoh dari halaman Bukti Pembayaran)
('DRJ-20261026-K201-7QF4', (SELECT id FROM kamar WHERE nomor_kamar='201'),
 'Budi Wijaya', '081234567891', 4, 'Tiba sekitar pukul 14.30 WIB bersama keluarga.',
 '2026-10-26 14:00', 46, '2026-10-28 12:00', 1700000,
 'Menunggu Konfirmasi', '2026-10-26 10:23', TRUE, '2026-10-26 10:08'),

-- Dibayar (sudah lewat)
('DRJ-20261020-K101-A3C9', (SELECT id FROM kamar WHERE nomor_kamar='101'),
 'Sari Lestari', '081345678902', 2, NULL,
 '2026-10-20 14:00', 46, '2026-10-22 12:00', 900000,
 'Dibayar', '2026-10-18 09:15', TRUE, '2026-10-18 09:00'),

-- Dibayar (mendatang)
('DRJ-20261105-K102-B8D2', (SELECT id FROM kamar WHERE nomor_kamar='102'),
 'Agus Pratama', '081356789013', 3, 'Mohon disiapkan 1 extra bed.',
 '2026-11-05 14:00', 46, '2026-11-07 12:00', 1100000,
 'Dibayar', '2026-10-28 13:15', TRUE, '2026-10-28 13:00'),

-- Dibayar, 3 malam akhir tahun
('DRJ-20261224-K201-N4UF', (SELECT id FROM kamar WHERE nomor_kamar='201'),
 'Bambang Hartono', '081367890125', 5, 'Liburan Natal bersama keluarga besar.',
 '2026-12-24 14:00', 70, '2026-12-27 12:00', 2550000,
 'Dibayar', '2026-10-27 15:45', TRUE, '2026-10-27 15:30'),

-- Di-Keep (masih dalam sesi 15 menit, berguna untuk uji coba)
('DRJ-20261114-K202-5MX1', (SELECT id FROM kamar WHERE nomor_kamar='202'),
 'Rina Kusuma', '081378901236', 6, NULL,
 '2026-11-14 14:00', 46, '2026-11-16 12:00', 1700000,
 'Di-Keep', CURRENT_TIMESTAMP + INTERVAL '15 minutes', TRUE, CURRENT_TIMESTAMP),

-- Kedaluwarsa (tidak membayar dalam 15 menit)
('DRJ-20261026-K201-Q9ZT', (SELECT id FROM kamar WHERE nomor_kamar='201'),
 'Dewi Anggraini', '081389012347', 3, NULL,
 '2026-10-26 14:00', 22, '2026-10-27 12:00', 850000,
 'Kedaluwarsa', '2026-10-25 20:15', TRUE, '2026-10-25 20:00'),

-- Dibatalkan
('DRJ-20261101-K103-L2PW', (SELECT id FROM kamar WHERE nomor_kamar='103'),
 'Hadi Santoso', '081390123458', 2, NULL,
 '2026-11-01 14:00', 22, '2026-11-02 12:00', 450000,
 'Dibatalkan', '2026-10-27 08:15', TRUE, '2026-10-27 08:00'),

-- Refund Diajukan
('DRJ-20261110-K104-R7HE', (SELECT id FROM kamar WHERE nomor_kamar='104'),
 'Maya Putri', '081301234569', 3, NULL,
 '2026-11-10 14:00', 46, '2026-11-12 12:00', 1100000,
 'Refund Diajukan', '2026-10-26 11:15', TRUE, '2026-10-26 11:00')
ON CONFLICT (kode_booking) DO NOTHING;

-- ---------------------------------------------------------------------
-- Pembayaran (memakai QRIS aktif)
-- ---------------------------------------------------------------------
INSERT INTO pembayaran
 (reservasi_id, qris_id, nominal, tanggal_pembayaran, bukti_file,
  status_verifikasi, diverifikasi_oleh, diverifikasi_at, created_at)
VALUES
((SELECT id FROM reservasi WHERE kode_booking='DRJ-20261026-K201-7QF4'),
 2, 1700000, '2026-10-26', '/uploads/bukti/bukti-transfer-qris-budi.jpg',
 'Menunggu', NULL, NULL, '2026-10-26 10:08'),
((SELECT id FROM reservasi WHERE kode_booking='DRJ-20261020-K101-A3C9'),
 2, 900000, '2026-10-18', '/uploads/bukti/bukti-sari.png',
 'Terverifikasi', 1, '2026-10-18 09:30', '2026-10-18 09:10'),
((SELECT id FROM reservasi WHERE kode_booking='DRJ-20261105-K102-B8D2'),
 2, 1100000, '2026-10-28', '/uploads/bukti/bukti-agus.jpg',
 'Terverifikasi', 2, '2026-10-28 13:40', '2026-10-28 13:10'),
((SELECT id FROM reservasi WHERE kode_booking='DRJ-20261224-K201-N4UF'),
 2, 2550000, '2026-10-27', '/uploads/bukti/bukti-bambang.webp',
 'Terverifikasi', 1, '2026-10-27 16:10', '2026-10-27 15:40'),
((SELECT id FROM reservasi WHERE kode_booking='DRJ-20261110-K104-R7HE'),
 2, 1100000, '2026-10-26', '/uploads/bukti/bukti-maya.jpg',
 'Terverifikasi', 2, '2026-10-26 11:30', '2026-10-26 11:10');

-- ---------------------------------------------------------------------
-- Refund
-- ---------------------------------------------------------------------
INSERT INTO refund (reservasi_id, alasan, status, catatan_admin, diajukan_at) VALUES
((SELECT id FROM reservasi WHERE kode_booking='DRJ-20261110-K104-R7HE'),
 'Ada keperluan keluarga mendadak, tidak bisa menginap.', 'Diajukan', NULL,
 '2026-10-28 19:00');

-- ---------------------------------------------------------------------
-- Log status reservasi
-- ---------------------------------------------------------------------
INSERT INTO log_status_reservasi (reservasi_id, status_lama, status_baru, diubah_oleh, created_at)
SELECT r.id, v.lama, v.baru, v.admin_id, v.waktu::timestamp
FROM (VALUES
 ('DRJ-20261026-K201-7QF4', NULL,                  'Di-Keep',             NULL, '2026-10-26 10:08'),
 ('DRJ-20261026-K201-7QF4', 'Di-Keep',             'Menunggu Konfirmasi', NULL, '2026-10-26 10:10'),
 ('DRJ-20261020-K101-A3C9', NULL,                  'Di-Keep',             NULL, '2026-10-18 09:00'),
 ('DRJ-20261020-K101-A3C9', 'Di-Keep',             'Menunggu Konfirmasi', NULL, '2026-10-18 09:10'),
 ('DRJ-20261020-K101-A3C9', 'Menunggu Konfirmasi', 'Dibayar',             1,    '2026-10-18 09:30'),
 ('DRJ-20261105-K102-B8D2', NULL,                  'Di-Keep',             NULL, '2026-10-28 13:00'),
 ('DRJ-20261105-K102-B8D2', 'Di-Keep',             'Menunggu Konfirmasi', NULL, '2026-10-28 13:10'),
 ('DRJ-20261105-K102-B8D2', 'Menunggu Konfirmasi', 'Dibayar',             2,    '2026-10-28 13:40'),
 ('DRJ-20261224-K201-N4UF', NULL,                  'Di-Keep',             NULL, '2026-10-27 15:30'),
 ('DRJ-20261224-K201-N4UF', 'Di-Keep',             'Menunggu Konfirmasi', NULL, '2026-10-27 15:40'),
 ('DRJ-20261224-K201-N4UF', 'Menunggu Konfirmasi', 'Dibayar',             1,    '2026-10-27 16:10'),
 ('DRJ-20261114-K202-5MX1', NULL,                  'Di-Keep',             NULL, CURRENT_TIMESTAMP::text),
 ('DRJ-20261026-K201-Q9ZT', NULL,                  'Di-Keep',             NULL, '2026-10-25 20:00'),
 ('DRJ-20261026-K201-Q9ZT', 'Di-Keep',             'Kedaluwarsa',         NULL, '2026-10-25 20:15'),
 ('DRJ-20261101-K103-L2PW', NULL,                  'Di-Keep',             NULL, '2026-10-27 08:00'),
 ('DRJ-20261101-K103-L2PW', 'Di-Keep',             'Dibatalkan',          NULL, '2026-10-27 08:10'),
 ('DRJ-20261110-K104-R7HE', NULL,                  'Di-Keep',             NULL, '2026-10-26 11:00'),
 ('DRJ-20261110-K104-R7HE', 'Di-Keep',             'Menunggu Konfirmasi', NULL, '2026-10-26 11:10'),
 ('DRJ-20261110-K104-R7HE', 'Menunggu Konfirmasi', 'Dibayar',             2,    '2026-10-26 11:30'),
 ('DRJ-20261110-K104-R7HE', 'Dibayar',             'Refund Diajukan',     NULL, '2026-10-28 19:00')
) AS v(kode, lama, baru, admin_id, waktu)
JOIN reservasi r ON r.kode_booking = v.kode;
