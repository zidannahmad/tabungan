<?php
include 'koneksi.php';

function rupiah($nilai)
{
    return 'Rp ' . number_format((float) $nilai, 0, ',', '.');
}

$pengaturanResult = mysqli_query($conn, "SELECT * FROM pengaturan_tabungan WHERE id = 1 LIMIT 1");
$pengaturan = mysqli_fetch_assoc($pengaturanResult);

$rekapResult = mysqli_query(
    $conn,
    "SELECT
        COALESCE(SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END), 0) AS total_masuk,
        COALESCE(SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END), 0) AS total_keluar,
        COUNT(*) AS jumlah_transaksi
    FROM tabungan"
);
$rekap = mysqli_fetch_assoc($rekapResult);

$totalMasuk = (int) $rekap['total_masuk'];
$totalKeluar = (int) $rekap['total_keluar'];
$saldo = $totalMasuk - $totalKeluar;
$jumlahTransaksi = (int) $rekap['jumlah_transaksi'];

$tanggalMulai = new DateTime($pengaturan['tanggal_mulai']);
$tanggalTarget = new DateTime($pengaturan['tanggal_target']);
$hariIni = new DateTime();

$totalHari = max(1, (int) $tanggalMulai->diff($tanggalTarget)->format('%a'));
$hariBerjalan = 0;

if ($hariIni >= $tanggalMulai) {
    $acuanHariBerjalan = $hariIni > $tanggalTarget ? $tanggalTarget : $hariIni;
    $hariBerjalan = (int) $tanggalMulai->diff($acuanHariBerjalan)->format('%a');
}

$sisaHari = $hariIni > $tanggalTarget ? 0 : (int) $hariIni->diff($tanggalTarget)->format('%a');
$progressWaktu = min(100, max(0, ($hariBerjalan / $totalHari) * 100));

$targetNominal = (int) $pengaturan['target_nominal'];
$sisaTarget = max(0, $targetNominal - $saldo);
$progressNominal = $targetNominal > 0 ? min(100, max(0, ($saldo / $targetNominal) * 100)) : 0;
$tabunganHarian = $sisaHari > 0 ? ceil($sisaTarget / $sisaHari) : $sisaTarget;
$tabunganMingguan = $sisaHari > 0 ? ceil($sisaTarget / max(1, ceil($sisaHari / 7))) : $sisaTarget;

$data = mysqli_query($conn, "SELECT * FROM tabungan ORDER BY tanggal DESC, id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabungan Ku | Lebaran 2027</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="page-shell">
        <div class="ambient ambient-one"></div>
        <div class="ambient ambient-two"></div>

        <main class="container">
            <section class="hero-card">
                <div class="hero-copy">
                    <span class="eyebrow">Tabungan Ku 2026 - 2027</span>
                    <h1><?php echo htmlspecialchars($pengaturan['nama_target']); ?></h1>
                    <p>
                        Mulai dari <strong><?php echo $tanggalMulai->format('d M Y'); ?></strong>
                        sampai target Idul Fitri <strong><?php echo $tanggalTarget->format('d M Y'); ?></strong>.
                        Pantau progres, jaga ritme menabung, dan siapkan Lebaran lebih tenang.
                    </p>
                    <div class="hero-actions">
                        <a href="tambah.php" class="btn btn-primary">Atur Target & Tambah Transaksi</a>
                    </div>
                </div>

                <div class="hero-highlight">
                    <div class="highlight-label">Sisa menuju target</div>
                    <div class="highlight-value"><?php echo $sisaHari; ?> hari</div>
                    <div class="highlight-note">
                        <?php echo $hariIni > $tanggalTarget ? 'Periode target sudah lewat.' : 'Masih ada waktu untuk menabung konsisten.'; ?>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <article class="stat-card stat-balance">
                    <span class="stat-label">Saldo Bersih</span>
                    <strong class="stat-value"><?php echo rupiah($saldo); ?></strong>
                    <small class="stat-note">Total masuk <?php echo rupiah($totalMasuk); ?> | keluar <?php echo rupiah($totalKeluar); ?></small>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Target Tabungan</span>
                    <strong class="stat-value"><?php echo rupiah($targetNominal); ?></strong>
                    <small class="stat-note">Sisa target <?php echo rupiah($sisaTarget); ?></small>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Setoran Harian Ideal</span>
                    <strong class="stat-value"><?php echo rupiah($tabunganHarian); ?></strong>
                    <small class="stat-note">Atau sekitar <?php echo rupiah($tabunganMingguan); ?> per minggu</small>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Jumlah Transaksi</span>
                    <strong class="stat-value"><?php echo $jumlahTransaksi; ?></strong>
                    <small class="stat-note">Semua pemasukan dan pengeluaran tercatat</small>
                </article>
            </section>

            <section class="progress-section">
                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Progress Nominal</h2>
                            <p>Seberapa dekat saldo kamu dengan target Lebaran.</p>
                        </div>
                        <strong><?php echo number_format($progressNominal, 1); ?>%</strong>
                    </div>
                    <div class="progress-track">
                        <div class="progress-bar" style="width: <?php echo $progressNominal; ?>%"></div>
                    </div>
                </article>

                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Progress Waktu</h2>
                            <p>Perjalanan dari 27 April 2026 sampai target Lebaran 2027.</p>
                        </div>
                        <strong><?php echo number_format($progressWaktu, 1); ?>%</strong>
                    </div>
                    <div class="progress-track progress-track-alt">
                        <div class="progress-bar progress-bar-alt" style="width: <?php echo $progressWaktu; ?>%"></div>
                    </div>
                </article>
            </section>

            <section class="content-grid">
                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Rencana Menabung</h2>
                            <p><?php echo htmlspecialchars($pengaturan['catatan'] ?: 'Atur targetmu dan isi transaksi secara rutin.'); ?></p>
                        </div>
                    </div>

                    <div class="timeline">
                        <div class="timeline-item">
                            <span class="timeline-tag">Mulai</span>
                            <div>
                                <strong><?php echo $tanggalMulai->format('d F Y'); ?></strong>
                                <p>Awal periode menabung.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <span class="timeline-tag">Hari ini</span>
                            <div>
                                <strong><?php echo $hariIni->format('d F Y'); ?></strong>
                                <p>Jaga ritme setoran agar target tetap realistis.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <span class="timeline-tag">Target</span>
                            <div>
                                <strong><?php echo $tanggalTarget->format('d F Y'); ?></strong>
                                <p>Estimasi Hari Raya Idul Fitri 2027.</p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Riwayat Transaksi</h2>
                            <p>Masukan dan pengeluaran terbaru untuk tabunganmu.</p>
                        </div>
                    </div>

                    <div class="transaction-list">
                        <?php if (mysqli_num_rows($data) > 0) { ?>
                            <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                                <div class="transaction-item">
                                    <div>
                                        <strong><?php echo htmlspecialchars($d['keterangan']); ?></strong>
                                        <span><?php echo date('d M Y', strtotime($d['tanggal'])); ?></span>
                                    </div>
                                    <div class="transaction-side">
                                        <span class="badge <?php echo $d['tipe']; ?>">
                                            <?php echo $d['tipe'] === 'masuk' ? 'Uang Masuk' : 'Uang Keluar'; ?>
                                        </span>
                                        <strong class="amount <?php echo $d['tipe']; ?>">
                                            <?php echo rupiah($d['jumlah']); ?>
                                        </strong>
                                        <a class="delete-link" href="hapus.php?id=<?php echo $d['id']; ?>" onclick="return confirm('Hapus transaksi ini?')">Hapus</a>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="empty-state">
                                <strong>Belum ada transaksi.</strong>
                                <p>Mulai tambahkan tabungan pertama kamu untuk memantau progres Lebaran 2027.</p>
                            </div>
                        <?php } ?>
                    </div>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
