CREATE EXTENSION IF NOT EXISTS pgcrypto;

ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS username VARCHAR(50);

UPDATE admin_users
   SET nama = 'Pengelola Vila', username = 'admin_drjionsi_'
 WHERE id = (SELECT MIN(id) FROM admin_users);

UPDATE qris_statis          SET diunggah_oleh    = (SELECT MIN(id) FROM admin_users) WHERE diunggah_oleh    IS NOT NULL;
UPDATE pembayaran           SET diverifikasi_oleh = (SELECT MIN(id) FROM admin_users) WHERE diverifikasi_oleh IS NOT NULL;
UPDATE log_status_reservasi SET diubah_oleh      = (SELECT MIN(id) FROM admin_users) WHERE diubah_oleh      IS NOT NULL;
DELETE FROM admin_users WHERE id <> (SELECT MIN(id) FROM admin_users);

ALTER TABLE admin_users ALTER COLUMN username SET NOT NULL;
CREATE UNIQUE INDEX IF NOT EXISTS admin_users_username_key ON admin_users (username);

UPDATE admin_users SET password = crypt('drjionsi@123123', gen_salt('bf', 10));

CREATE TABLE IF NOT EXISTS login_percobaan (
    ip               VARCHAR(45) PRIMARY KEY,
    gagal            INT         NOT NULL DEFAULT 0,
    terkunci_sampai  TIMESTAMP,
    terakhir         TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
);