<?php
date_default_timezone_set('Asia/Bangkok');

$conn = mysqli_connect('localhost', 'root', '', '');

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS tabunganku");
mysqli_select_db($conn, 'tabunganku');

mysqli_query(
    $conn,
    "CREATE TABLE IF NOT EXISTS tabungan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tanggal DATE NOT NULL,
        keterangan VARCHAR(255) NOT NULL,
        jumlah BIGINT NOT NULL,
        tipe ENUM('masuk','keluar') NOT NULL
    )"
);

mysqli_query(
    $conn,
    "CREATE TABLE IF NOT EXISTS pengaturan_tabungan (
        id INT PRIMARY KEY,
        nama_target VARCHAR(120) NOT NULL,
        tanggal_mulai DATE NOT NULL,
        tanggal_target DATE NOT NULL,
        target_nominal BIGINT NOT NULL,
        catatan VARCHAR(255) DEFAULT NULL
    )"
);

$cekPengaturan = mysqli_query($conn, "SELECT id FROM pengaturan_tabungan WHERE id = 1");

if ($cekPengaturan && mysqli_num_rows($cekPengaturan) === 0) {
    mysqli_query(
        $conn,
        "INSERT INTO pengaturan_tabungan (
            id,
            nama_target,
            tanggal_mulai,
            tanggal_target,
            target_nominal,
            catatan
        ) VALUES (
            1,
            'Tabungan Lebaran 2027',
            '2026-04-27',
            '2027-03-10',
            12000000,
            'Target dana persiapan Hari Raya Idul Fitri 2027'
        )"
    );
}
?>
