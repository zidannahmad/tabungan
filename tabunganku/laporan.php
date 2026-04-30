<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

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

// Get all transactions
$data = mysqli_query($conn, "SELECT * FROM tabungan ORDER BY tanggal DESC, id DESC");
?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Laporan Transaksi | Tabunganku</title>
    <?php include 'pwa-head.php'; ?>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700;900&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-primary": "#002682",
                        "primary": "#b7c4ff",
                        "surface-tint": "#b7c4ff",
                        "on-tertiary-fixed-variant": "#5300cd",
                        "secondary-fixed": "#c2e8ff",
                        "on-surface": "#e1e1ef",
                        "inverse-primary": "#004ced",
                        "surface-bright": "#373943",
                        "surface-container-lowest": "#0c0e17",
                        "on-tertiary": "#3a0093",
                        "on-secondary-fixed-variant": "#004d67",
                        "on-surface-variant": "#c3c5d9",
                        "surface-container-low": "#191b25",
                        "surface-container-highest": "#32343e",
                        "surface-variant": "#32343e",
                        "secondary-container": "#00c1fd",
                        "surface-container-high": "#282933",
                        "primary-container": "#0052ff",
                        "on-primary-fixed-variant": "#0038b6",
                        "tertiary": "#cfbdff",
                        "background": "#11131c",
                        "primary-fixed-dim": "#b7c4ff",
                        "on-secondary": "#003548",
                        "outline-variant": "#434656",
                        "error-container": "#93000a",
                        "on-tertiary-fixed": "#22005d",
                        "on-secondary-container": "#004b65",
                        "primary-fixed": "#dde1ff",
                        "on-tertiary-container": "#ebe0ff",
                        "secondary": "#8fd8ff",
                        "surface-dim": "#11131c",
                        "inverse-on-surface": "#2e303a",
                        "on-error": "#690005",
                        "tertiary-fixed-dim": "#cfbdff",
                        "surface": "#11131c",
                        "on-primary-fixed": "#001452",
                        "on-primary-container": "#dfe3ff",
                        "on-error-container": "#ffdad6",
                        "on-secondary-fixed": "#001e2b",
                        "on-background": "#e1e1ef",
                        "surface-container": "#1d1f29",
                        "tertiary-container": "#7536fa",
                        "secondary-fixed-dim": "#75d1ff",
                        "error": "#ffb4ab",
                        "tertiary-fixed": "#e8ddff",
                        "outline": "#8d90a2",
                        "inverse-surface": "#e1e1ef"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "sm": "8px",
                        "margin": "32px",
                        "lg": "24px",
                        "unit": "4px",
                        "container-max": "1440px",
                        "md": "16px",
                        "xl": "40px",
                        "xs": "4px",
                        "gutter": "24px"
                    },
                    "fontFamily": {
                        "label-caps": ["Inter"],
                        "data-mono": ["Space Grotesk"],
                        "headline-lg": ["Space Grotesk"],
                        "display-xl": ["Space Grotesk"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "headline-md": ["Space Grotesk"]
                    },
                    "fontSize": {
                        "label-caps": ["12px", { "lineHeight": "1.0", "letterSpacing": "0.1em", "fontWeight": "600" }],
                        "data-mono": ["14px", { "lineHeight": "1.0", "letterSpacing": "0.02em", "fontWeight": "500" }],
                        "headline-lg": ["40px", { "lineHeight": "1.2", "letterSpacing": "-0.03em", "fontWeight": "600" }],
                        "display-xl": ["72px", { "lineHeight": "1.1", "letterSpacing": "-0.04em", "fontWeight": "700" }],
                        "body-md": ["16px", { "lineHeight": "1.6", "letterSpacing": "-0.01em", "fontWeight": "400" }],
                        "body-lg": ["18px", { "lineHeight": "1.6", "letterSpacing": "-0.01em", "fontWeight": "400" }],
                        "headline-md": ["32px", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "600" }]
                    }
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            background: rgba(12, 14, 23, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
    </style>
</head>
<body class="bg-surface-container-lowest text-on-background font-body-md min-h-screen overflow-x-hidden selection:bg-primary-container selection:text-white">

<!-- SideNavBar -->
<aside class="h-screen w-72 fixed left-0 top-0 border-r border-white/10 bg-[#0C0E17]/80 backdrop-blur-lg shadow-[4px_0_24px_rgba(0,0,0,0.5)] flex flex-col h-full py-8 z-50">
    <div class="px-6 mb-8 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-surface-container-high overflow-hidden border border-white/10">
            <div class="w-full h-full bg-primary-container/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary-container">account_circle</span>
            </div>
        </div>
        <div>
            <h2 class="text-xl font-bold tracking-tighter text-[#0052FF] drop-shadow-[0_0_8px_rgba(0,82,255,0.8)] font-['Space_Grotesk']">Tabunganku</h2>
            <p class="text-xs text-slate-400 font-data-mono">Premium Tier</p>
        </div>
    </div>
    <nav class="flex-1 space-y-2">
        <a class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-white transition-colors hover:bg-white/5 transition-all duration-300 font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold" href="index.php">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>
        <a class="flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-white transition-colors hover:bg-white/5 transition-all duration-300 font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold" href="tambah.php">
            <span class="material-symbols-outlined">settings</span> Kelola
        </a>
        <a class="flex items-center gap-4 px-6 py-4 text-[#0052FF] bg-[#0052FF]/10 border-r-2 border-[#0052FF] shadow-[inset_-10px_0_20px_rgba(0,82,255,0.05)] font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold" href="#">
            <span class="material-symbols-outlined">monitoring</span> Laporan
        </a>
    </nav>
    <div class="border-t border-white/10 pt-4">
        <a class="flex items-center gap-4 px-6 py-3 text-slate-400 hover:text-white transition-colors font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold" href="#">
            <span class="material-symbols-outlined">logout</span> Keluar
        </a>
    </div>
</aside>

<!-- TopAppBar -->
<nav class="fixed top-0 right-0 left-72 h-20 z-40 bg-[#0C0E17]/60 backdrop-blur-md border-b border-white/10 flex items-center justify-between px-12">
    <h1 class="text-2xl font-black text-white font-['Space_Grotesk'] tracking-tight drop-shadow-[0_0_8px_rgba(0,82,255,0.8)]">Tabunganku</h1>
    <p class="text-slate-400 font-data-mono text-label-caps">Laporan Transaksi</p>
</nav>

<!-- Main Content Canvas -->
<main class="ml-72 pt-24 p-8 min-h-screen relative">
    <!-- Background Ambient Glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-primary-container/5 rounded-full blur-[120px] pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 right-0 w-[600px] h-[600px] bg-tertiary-container/5 rounded-full blur-[100px] pointer-events-none -z-10"></div>

    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Header -->
        <div>
            <p class="font-label-caps text-label-caps text-primary mb-2 tracking-[0.2em] uppercase">Laporan</p>
            <h1 class="text-4xl font-black text-white font-['Space_Grotesk'] tracking-tighter">Riwayat Semua Transaksi</h1>
            <p class="text-slate-400 mt-2 max-w-2xl">Lihat daftar lengkap semua transaksi tabungan Anda untuk <?php echo htmlspecialchars($pengaturan['nama_target']); ?></p>
        </div>

        <!-- Summary Stats -->
        <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="glass-panel rounded-xl p-6">
                <p class="font-label-caps text-label-caps text-slate-400 uppercase tracking-wider mb-2">Total Masuk</p>
                <h3 class="font-headline-md text-headline-md text-emerald-400 mb-2"><?php echo rupiah($totalMasuk); ?></h3>
                <p class="text-xs text-slate-500">Uang yang masuk</p>
            </div>

            <div class="glass-panel rounded-xl p-6">
                <p class="font-label-caps text-label-caps text-slate-400 uppercase tracking-wider mb-2">Total Keluar</p>
                <h3 class="font-headline-md text-headline-md text-rose-400 mb-2"><?php echo rupiah($totalKeluar); ?></h3>
                <p class="text-xs text-slate-500">Uang yang keluar</p>
            </div>

            <div class="glass-panel rounded-xl p-6">
                <p class="font-label-caps text-label-caps text-slate-400 uppercase tracking-wider mb-2">Saldo Bersih</p>
                <h3 class="font-headline-md text-headline-md text-primary mb-2"><?php echo rupiah($saldo); ?></h3>
                <p class="text-xs text-slate-500">Saldo saat ini</p>
            </div>

            <div class="glass-panel rounded-xl p-6">
                <p class="font-label-caps text-label-caps text-slate-400 uppercase tracking-wider mb-2">Total Transaksi</p>
                <h3 class="font-headline-md text-headline-md text-white mb-2"><?php echo $jumlahTransaksi; ?></h3>
                <p class="text-xs text-slate-500">Jumlah transaksi</p>
            </div>
        </section>

        <!-- Transactions Table -->
        <section class="glass-panel rounded-xl p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-headline-lg text-headline-lg text-white text-2xl">Daftar Transaksi</h2>
                <a href="tambah.php" class="px-4 py-2 bg-primary-container text-white rounded-lg font-label-caps text-label-caps uppercase tracking-wider hover:bg-[#0038b6] transition-colors shadow-[0_0_15px_rgba(0,82,255,0.5)] flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-sm">add</span> Tambah
                </a>
            </div>

            <?php if (mysqli_num_rows($data) > 0) { ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="text-left px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase">Tanggal</th>
                                <th class="text-left px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase">Keterangan</th>
                                <th class="text-left px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase">Tipe</th>
                                <th class="text-right px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase">Jumlah</th>
                                <th class="text-center px-4 py-3 font-label-caps text-label-caps text-slate-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($data)) { ?>
                                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-3 text-white font-data-mono"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                                    <td class="px-4 py-3 text-white"><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-label-caps uppercase <?php echo $row['tipe'] === 'masuk' ? 'bg-emerald-400/20 text-emerald-400' : 'bg-rose-400/20 text-rose-400'; ?>">
                                            <?php echo $row['tipe'] === 'masuk' ? 'Masuk' : 'Keluar'; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right <?php echo $row['tipe'] === 'masuk' ? 'text-emerald-400' : 'text-rose-400'; ?> font-data-mono font-medium">
                                        <?php echo $row['tipe'] === 'masuk' ? '+' : '-'; ?> <?php echo rupiah($row['jumlah']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="hapus.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus transaksi ini?')" class="text-slate-400 hover:text-rose-400 transition-colors">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-6xl text-slate-600 mb-4 block">receipt</span>
                    <p class="text-slate-400 text-lg">Belum ada transaksi</p>
                    <p class="text-slate-500 text-sm mt-2">Mulai tambahkan transaksi untuk melihat laporan di sini</p>
                </div>
            <?php } ?>
        </section>
    </div>
</main>

<?php include 'pwa-install-ui.php'; ?>
</body>
</html>
