CREATE TABLE IF NOT EXISTS fasilitas (
    id            SERIAL PRIMARY KEY,
    nama          VARCHAR(80)  NOT NULL,
    kategori      VARCHAR(30)  NOT NULL
                  CHECK (kategori IN ('Fasilitas Kamar', 'Area Umum', 'Layanan Tambahan')),
    deskripsi     VARCHAR(200),
    ikon          VARCHAR(40)  NOT NULL DEFAULT 'bi-stars',
    aktif         BOOLEAN      NOT NULL DEFAULT TRUE,
    dihapus_pada  TIMESTAMP,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    diperbarui_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX IF NOT EXISTS fasilitas_nama_uq ON fasilitas (LOWER(nama));
CREATE INDEX IF NOT EXISTS fasilitas_dihapus_pada_idx ON fasilitas (dihapus_pada);
CREATE INDEX IF NOT EXISTS fasilitas_kategori_idx ON fasilitas (kategori);

INSERT INTO fasilitas (nama, kategori, deskripsi, ikon, aktif)
SELECT v.nama, v.kategori, v.deskripsi, v.ikon, v.aktif
FROM (VALUES
  ('Wi-Fi Kecepatan Tinggi',        'Fasilitas Kamar',  'Koneksi internet stabil hingga 50 Mbps.',                   'bi-wifi',         TRUE),
  ('Smart TV',                      'Fasilitas Kamar',  'Smart TV dengan akses aplikasi streaming di setiap kamar.', 'bi-tv',           TRUE),
  ('Pemanas Air / Water Heater',    'Fasilitas Kamar',  'Pemanas air gas otomatis 24 jam di seluruh kamar mandi.',   'bi-droplet-half', TRUE),
  ('Kamar Mandi Dalam',             'Fasilitas Kamar',  'Kamar mandi pribadi di dalam setiap unit kamar.',           'bi-door-closed',  TRUE),
  ('Pemandangan Pegunungan',        'Fasilitas Kamar',  'Jendela dan balkon menghadap lereng Gunung Panderman.',     'bi-image',        TRUE),
  ('Sarapan Pagi',                  'Area Umum',        'Sarapan prasmanan di area resto utama.',                    'bi-cup-hot',      TRUE),
  ('Area Parkir Aman',              'Area Umum',        'Lahan parkir luas di dalam pagar villa, cukup untuk 6-8 mobil keluarga dengan pengawasan CCTV 24 jam.', 'bi-car-front', TRUE),
  ('Dapur & Ruang Makan',           'Area Umum',        'Dapur bersama yang dilengkapi kompor, kulkas besar, dispenser, dan peralatan makan untuk kebutuhan ringan tamu.', 'bi-cup-hot', TRUE),
  ('Area Santai Terbuka',           'Area Umum',        'Taman kecil dan bangku santai di lantai dasar untuk menikmati udara pegunungan atau berkumpul santai.', 'bi-tree', TRUE),
  ('Antar-Jemput Stasiun / Bandara','Layanan Tambahan', 'Layanan transportasi Songgokerto - Malang PP.',             'bi-car-front',    FALSE)
) AS v(nama, kategori, deskripsi, ikon, aktif)
WHERE NOT EXISTS (SELECT 1 FROM fasilitas f WHERE LOWER(f.nama) = LOWER(v.nama));