ALTER TABLE reservasi ADD COLUMN IF NOT EXISTS dihapus_pada TIMESTAMP;

CREATE INDEX IF NOT EXISTS reservasi_dihapus_pada_idx ON reservasi (dihapus_pada);