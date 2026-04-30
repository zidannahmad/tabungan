<?php
include 'koneksi.php';

$aksi = $_POST['aksi'] ?? '';

if ($aksi === 'simpan_target') {
    $namaTarget = trim($_POST['nama_target'] ?? '');
    $tanggalMulai = $_POST['tanggal_mulai'] ?? '';
    $tanggalTarget = $_POST['tanggal_target'] ?? '';
    $targetNominal = (int) ($_POST['target_nominal'] ?? 0);
    $catatan = trim($_POST['catatan'] ?? '');

    if (
        $namaTarget !== '' &&
        $tanggalMulai !== '' &&
        $tanggalTarget !== '' &&
        $targetNominal > 0 &&
        strtotime($tanggalMulai) !== false &&
        strtotime($tanggalTarget) !== false &&
        $tanggalMulai <= $tanggalTarget
    ) {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE pengaturan_tabungan
            SET nama_target = ?, tanggal_mulai = ?, tanggal_target = ?, target_nominal = ?, catatan = ?
            WHERE id = 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            'sssis',
            $namaTarget,
            $tanggalMulai,
            $tanggalTarget,
            $targetNominal,
            $catatan
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header('Location: index.php');
    exit;
}

if ($aksi === 'simpan_transaksi') {
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $jumlah = (int) ($_POST['jumlah'] ?? 0);
    $tipe = $_POST['tipe'] ?? '';

    if (
        $tanggal !== '' &&
        $keterangan !== '' &&
        $jumlah > 0 &&
        in_array($tipe, ['masuk', 'keluar'], true)
    ) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO tabungan (tanggal, keterangan, jumlah, tipe)
            VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'ssis', $tanggal, $keterangan, $jumlah, $tipe);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header('Location: index.php');
exit;
?>
