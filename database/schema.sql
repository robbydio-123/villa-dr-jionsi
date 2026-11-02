-- =====================================================================
-- jionsi_db : Skema Sistem Booking Kamar Villa D-R Jionsi (PostgreSQL 12+)
-- Murni struktur database. Seluruh logika (kode booking, kedaluwarsa keep,
-- ganti QRIS, cek status, pencatatan log) dikerjakan di script PHP.
--
-- Buat database dulu (jalankan terpisah, di luar transaksi):
--   CREATE DATABASE jionsi_db WITH OWNER = postgres ENCODING = 'UTF8';
-- Lalu sambungkan ke jionsi_db dan jalankan file ini.
-- =====================================================================

-- Dibutuhkan constraint anti double booking (gist pada kolom integer)
CREATE EXTENSION IF NOT EXISTS btree_gist;

-- ---------------------------------------------------------------------
-- 1. ADMIN_USERS (hanya admin/owner yang login; pelanggan tanpa akun)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id              SERIAL PRIMARY KEY,
    nama            VARCHAR(100) NOT NULL,
    username        VARCHAR(50)  UNIQUE NOT NULL,
    nomor_whatsapp  VARCHAR(20)  UNIQUE NOT NULL,
    password        VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- Pencatat percobaan login gagal (3x salah -> terkunci 5 menit, per IP)
CREATE TABLE IF NOT EXISTS login_percobaan (
    ip               VARCHAR(45) PRIMARY KEY,
    gagal            INT         NOT NULL DEFAULT 0,
    terkunci_sampai  TIMESTAMP,
    terakhir         TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- ---------------------------------------------------------------------
-- 2. PENGATURAN VILLA (satu baris saja)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaturan_villa (
    id                        SMALLINT PRIMARY KEY DEFAULT 1 CHECK (id = 1),
    nama_properti             VARCHAR(100) NOT NULL,
    kode_properti             VARCHAR(10)  NOT NULL DEFAULT 'DRJ',
    whatsapp_utama            VARCHAR(20)  NOT NULL,
    email_operasional         VARCHAR(100),
    alamat_lengkap            TEXT,
    batas_waktu_keep_menit    INT  NOT NULL DEFAULT 15,
    batas_refund_hari_sebelum INT  NOT NULL DEFAULT 1,
    updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- 3. QRIS STATIS (satu-satunya metode pembayaran)
--    Gambar lama disimpan sebagai riwayat; hanya SATU yang boleh aktif.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS qris_statis (
    id            SERIAL PRIMARY KEY,
    gambar        VARCHAR(255) NOT NULL,
    aktif         BOOLEAN   NOT NULL DEFAULT FALSE,
    diunggah_oleh INT REFERENCES admin_users(id),
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX IF NOT EXISTS hanya_satu_qris_aktif ON qris_statis (aktif) WHERE aktif;

-- ---------------------------------------------------------------------
-- 4. KAMAR
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS kamar (
    id                    SERIAL PRIMARY KEY,
    nomor_kamar           VARCHAR(10)  UNIQUE NOT NULL,
    tipe_kamar            VARCHAR(50)  NOT NULL,
    lantai                SMALLINT     NOT NULL DEFAULT 1,
    kapasitas             INT          NOT NULL CHECK (kapasitas > 0),
    harga_per_malam       DECIMAL(12,2) NOT NULL CHECK (harga_per_malam >= 0),
    status                VARCHAR(20)  DEFAULT 'Tersedia'
                          CHECK (status IN ('Tersedia', 'Terisi', 'Perbaikan')),
    deskripsi             TEXT,
    foto_url              VARCHAR(255),
    kondisi_diperbarui_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_at            TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- 5. RESERVASI
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reservasi (
    id                SERIAL PRIMARY KEY,
    kode_booking      VARCHAR(50) UNIQUE NOT NULL,
    kamar_id          INT REFERENCES kamar(id) ON DELETE CASCADE,

    nama_pemesan      VARCHAR(100) NOT NULL,
    whatsapp_pemesan  VARCHAR(20)  NOT NULL,
    whatsapp_4_digit  CHAR(4) GENERATED ALWAYS AS (RIGHT(whatsapp_pemesan, 4)) STORED,

    jumlah_tamu       INT NOT NULL CHECK (jumlah_tamu > 0),
    catatan           TEXT,

    waktu_checkin     TIMESTAMP NOT NULL,
    durasi_jam        INT NOT NULL CHECK (durasi_jam > 0),
    waktu_checkout    TIMESTAMP NOT NULL,
    total_tagihan     DECIMAL(12,2) NOT NULL,

    status_booking    VARCHAR(30) DEFAULT 'Di-Keep'
                      CHECK (status_booking IN ('Di-Keep', 'Menunggu Konfirmasi', 'Dibayar',
                                                'Kedaluwarsa', 'Dibatalkan', 'Refund Diajukan')),
    batas_waktu_keep  TIMESTAMP NOT NULL,
    setuju_kebijakan  BOOLEAN NOT NULL DEFAULT FALSE,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CHECK (waktu_checkout > waktu_checkin),

    -- Cegah double booking: kamar yang sama tidak boleh punya rentang waktu
    -- bertabrakan selama status masih aktif.
    CONSTRAINT no_double_booking EXCLUDE USING gist (
        kamar_id WITH =,
        tsrange(waktu_checkin, waktu_checkout) WITH &&
    ) WHERE (status_booking IN ('Di-Keep', 'Menunggu Konfirmasi', 'Dibayar'))
);

CREATE INDEX IF NOT EXISTS idx_reservasi_status ON reservasi (status_booking, batas_waktu_keep);
CREATE INDEX IF NOT EXISTS idx_reservasi_cek    ON reservasi (kode_booking, whatsapp_4_digit);

-- ---------------------------------------------------------------------
-- 6. PEMBAYARAN (bukti pembayaran QRIS & verifikasi admin)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pembayaran (
    id                 SERIAL PRIMARY KEY,
    reservasi_id       INT NOT NULL REFERENCES reservasi(id) ON DELETE CASCADE,
    qris_id            INT REFERENCES qris_statis(id),
    nominal            DECIMAL(12,2) NOT NULL,
    tanggal_pembayaran DATE NOT NULL DEFAULT CURRENT_DATE,
    bukti_file         VARCHAR(255) NOT NULL,
    status_verifikasi  VARCHAR(20) NOT NULL DEFAULT 'Menunggu'
                       CHECK (status_verifikasi IN ('Menunggu', 'Terverifikasi', 'Ditolak')),
    diverifikasi_oleh  INT REFERENCES admin_users(id),
    diverifikasi_at    TIMESTAMP,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- 7. REFUND (diproses manual via WhatsApp, sistem hanya mencatat)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS refund (
    id            SERIAL PRIMARY KEY,
    reservasi_id  INT NOT NULL REFERENCES reservasi(id) ON DELETE CASCADE,
    alasan        TEXT,
    status        VARCHAR(20) NOT NULL DEFAULT 'Diajukan'
                  CHECK (status IN ('Diajukan', 'Disetujui', 'Ditolak', 'Selesai')),
    catatan_admin TEXT,
    diajukan_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diputuskan_at TIMESTAMP
);

-- ---------------------------------------------------------------------
-- 8. LOG STATUS RESERVASI (diisi oleh PHP setiap status berubah)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS log_status_reservasi (
    id            BIGSERIAL PRIMARY KEY,
    reservasi_id  INT NOT NULL REFERENCES reservasi(id) ON DELETE CASCADE,
    status_lama   VARCHAR(30),
    status_baru   VARCHAR(30) NOT NULL,
    diubah_oleh   INT REFERENCES admin_users(id),
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
