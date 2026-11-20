ALTER TABLE kamar ADD COLUMN IF NOT EXISTS dihapus_pada TIMESTAMP;

ALTER TABLE kamar ADD COLUMN IF NOT EXISTS catatan_perbaikan VARCHAR(150);

CREATE INDEX IF NOT EXISTS kamar_dihapus_pada_idx ON kamar (dihapus_pada);

CREATE TABLE IF NOT EXISTS kamar_foto (
    id        SERIAL PRIMARY KEY,
    kamar_id  INT NOT NULL REFERENCES kamar(id) ON DELETE CASCADE,
    url       VARCHAR(255) NOT NULL,
    label     VARCHAR(50),
    urutan    INT NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS kamar_foto_kamar_idx ON kamar_foto (kamar_id, urutan);

-- pindahkan foto lama jadi foto pertama di galeri
INSERT INTO kamar_foto (kamar_id, url, label, urutan)
SELECT id, foto_url, 'Kamar Utama', 0 FROM kamar
WHERE foto_url IS NOT NULL AND foto_url <> ''
  AND NOT EXISTS (SELECT 1 FROM kamar_foto f WHERE f.kamar_id = kamar.id);