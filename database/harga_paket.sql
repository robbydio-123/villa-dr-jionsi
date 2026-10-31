CREATE TABLE IF NOT EXISTS harga_paket_jam (
    durasi_jam INT PRIMARY KEY CHECK (durasi_jam IN (3, 6, 12)),
    harga      DECIMAL(12,2) NOT NULL CHECK (harga >= 0)
);

INSERT INTO harga_paket_jam (durasi_jam, harga) VALUES
    (3,   80000),
    (6,  100000),
    (12, 125000)
ON CONFLICT (durasi_jam) DO UPDATE SET harga = EXCLUDED.harga;

UPDATE kamar SET harga_per_malam = 250000;

-- Cek hasilnya:
SELECT * FROM harga_paket_jam ORDER BY durasi_jam;
SELECT nomor_kamar, tipe_kamar, harga_per_malam FROM kamar ORDER BY nomor_kamar;
