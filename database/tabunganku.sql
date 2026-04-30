CREATE TABLE IF NOT EXISTS tabungan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    jumlah BIGINT NOT NULL,
    tipe ENUM('masuk', 'keluar') NOT NULL
);

CREATE TABLE IF NOT EXISTS pengaturan_tabungan (
    id INT PRIMARY KEY,
    nama_target VARCHAR(120) NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_target DATE NOT NULL,
    target_nominal BIGINT NOT NULL,
    catatan VARCHAR(255) DEFAULT NULL
);

INSERT INTO pengaturan_tabungan (
    id,
    nama_target,
    tanggal_mulai,
    tanggal_target,
    target_nominal,
    catatan
)
VALUES (
    1,
    'Tabungan Lebaran 2027',
    '2026-04-27',
    '2027-03-10',
    12000000,
    'Target dana persiapan Hari Raya Idul Fitri 2027'
)
ON DUPLICATE KEY UPDATE
    nama_target = VALUES(nama_target),
    tanggal_mulai = VALUES(tanggal_mulai),
    tanggal_target = VALUES(tanggal_target),
    target_nominal = VALUES(target_nominal),
    catatan = VALUES(catatan);
