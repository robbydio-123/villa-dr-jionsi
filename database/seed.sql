-- =====================================================================
-- Data awal jionsi_db (tanpa data reservasi) (jalankan SETELAH schema.sql)
-- =====================================================================

CREATE EXTENSION IF NOT EXISTS pgcrypto;
ALTER TABLE reservasi ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
-- ---------------------------------------------------------------------
-- Admin
-- ---------------------------------------------------------------------
INSERT INTO admin_users (nama, username, nomor_whatsapp, password) VALUES
('Pengelola Vila', 'admin_drjionsi_', '081233449081', crypt('drjionsi@123123', gen_salt('bf', 10)))
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
('/assets/img/Qris-lama.jpeg',  FALSE, 1, CURRENT_TIMESTAMP - INTERVAL '60 days'),
('/assets/img/Qris.jpeg', TRUE,  1, CURRENT_TIMESTAMP - INTERVAL '10 days');

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