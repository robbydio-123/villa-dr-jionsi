ALTER TABLE refund
    ADD COLUMN IF NOT EXISTS bank_tujuan           VARCHAR(60),
    ADD COLUMN IF NOT EXISTS no_rekening           VARCHAR(30),
    ADD COLUMN IF NOT EXISTS nama_pemilik_rekening VARCHAR(100),
    ADD COLUMN IF NOT EXISTS ditransfer_at         TIMESTAMP;