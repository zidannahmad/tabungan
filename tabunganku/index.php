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

$data = mysqli_query($conn, "SELECT * FROM tabungan ORDER BY tanggal DESC, id DESC LIMIT 5");

// Hitung stroke-dasharray untuk SVG progress circles
$nominalDashArray = (($progressNominal / 100) * 282.6) . ' 282.6';
$waktuDashArray = (($progressWaktu / 100) * 282.6) . ' 282.6';
$targetProgressBar = ($progressNominal / 100) * 100;

// Hitung persentase transaksi masuk dan keluar
$persenMasuk = $jumlahTransaksi > 0 ? ceil(($totalMasuk > 0 ? 18 : 0) / $jumlahTransaksi * 100) : 0;
$persenKeluar = $jumlahTransaksi > 0 ? ($jumlahTransaksi - 18) : 0;
?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Tabunganku 2027 Dashboard</title>
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
        .neon-glow {
            text-shadow: 0 0 10px rgba(0, 82, 255, 0.8), 0 0 20px rgba(0, 82, 255, 0.4);
        }
        .neon-border-glow {
            box-shadow: inset 0 0 15px rgba(0, 82, 255, 0.2), 0 0 10px rgba(0, 82, 255, 0.1);
        }
        .holographic-divider {
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
            height: 1px;
            width: 100%;
        }
        .mobile-safe-bottom {
            padding-bottom: max(1rem, env(safe-area-inset-bottom));
        }
    </style>
</head>
<body class="bg-surface-container-lowest text-on-background font-body-md min-h-screen overflow-x-hidden selection:bg-primary-container selection:text-white">
<!-- Mobile Backdrop -->
<div id="sidebarBackdrop" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"></div>

<!-- SideNavBar -->
<aside id="sidebar" class="fixed left-0 top-0 z-50 flex h-screen w-[85vw] max-w-72 -translate-x-full flex-col border-r border-white/10 bg-[#0C0E17]/95 py-6 shadow-[4px_0_24px_rgba(0,0,0,0.5)] backdrop-blur-lg transition-transform duration-300 lg:translate-x-0">
    <div class="px-5 mb-8 flex items-center justify-between gap-4 lg:px-6">
        <div class="flex items-center gap-4">
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
        <button type="button" onclick="closeSidebar()" class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/10 text-slate-300 transition-colors hover:bg-white/5 hover:text-white lg:hidden">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <nav class="flex-1 space-y-2 px-3">
        <a class="flex items-center gap-4 rounded-xl bg-[#0052FF]/10 px-4 py-4 text-[#0052FF] shadow-[inset_-10px_0_20px_rgba(0,82,255,0.05)] font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold lg:rounded-none lg:border-r-2 lg:border-[#0052FF] lg:px-6" href="index.php">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>
        <a class="flex items-center gap-4 rounded-xl px-4 py-4 text-slate-400 transition-all duration-300 hover:bg-white/5 hover:text-white font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold lg:px-6" href="tambah.php">
            <span class="material-symbols-outlined">settings</span> Kelola
        </a>
        <a class="flex items-center gap-4 rounded-xl px-4 py-4 text-slate-400 transition-all duration-300 hover:bg-white/5 hover:text-white font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold lg:px-6" href="laporan.php">
            <span class="material-symbols-outlined">monitoring</span> Laporan
        </a>
    </nav>
    <div class="px-5 mt-auto mb-6 lg:px-6">
        <a href="tambah.php" class="w-full py-3 bg-primary-container text-white rounded-lg font-label-caps text-label-caps uppercase tracking-wider hover:bg-[#0038b6] transition-colors shadow-[0_0_15px_rgba(0,82,255,0.5)] flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">add</span> Tambah Transaksi
        </a>
    </div>
    <div class="border-t border-white/10 pt-4">
        <a class="flex items-center gap-4 px-5 py-3 text-slate-400 hover:text-rose-400 transition-colors font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold lg:px-6" href="logout.php" onclick="return confirm('Anda yakin ingin logout?')">
            <span class="material-symbols-outlined">logout</span> Logout
        </a>
    </div>
</aside>

<!-- TopAppBar -->
<nav class="fixed top-0 right-0 left-0 z-30 flex h-16 items-center justify-between border-b border-white/10 bg-[#0C0E17]/80 px-4 backdrop-blur-md sm:px-6 lg:left-72 lg:h-20 lg:px-12">
    <div class="flex min-w-0 items-center gap-3">
        <button type="button" onclick="openSidebar()" class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/10 text-slate-200 transition-colors hover:bg-white/5 hover:text-white lg:hidden">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <div class="min-w-0">
            <h1 class="truncate text-lg font-black text-white tracking-tight drop-shadow-[0_0_8px_rgba(0,82,255,0.8)] font-['Space_Grotesk'] sm:text-2xl">Tabunganku</h1>
            <p class="text-[11px] uppercase tracking-[0.24em] text-slate-400 sm:hidden">Dashboard Tabungan</p>
        </div>
    </div>
    <div class="hidden items-center gap-4 lg:flex">
        <div class="relative focus-within:ring-1 focus-within:ring-[#0052FF]/50 rounded-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
            <input class="bg-surface-container-high border-none rounded-full pl-10 pr-4 py-2 text-sm text-white placeholder-slate-400 focus:ring-0 focus:outline-none w-48" placeholder="Cari transaksi..." type="text"/>
        </div>
        <button class="text-slate-400 hover:text-white transition-colors"><span class="material-symbols-outlined">notifications</span></button>
        <button class="text-slate-400 hover:text-white transition-colors"><span class="material-symbols-outlined">account_circle</span></button>
    </div>
</nav>

<!-- Main Content Canvas -->
<main class="relative min-h-screen px-4 pb-8 pt-20 sm:px-6 lg:ml-72 lg:px-8 lg:pt-24">
    <!-- Background Ambient Glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-primary-container/5 rounded-full blur-[120px] pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 right-0 w-[600px] h-[600px] bg-tertiary-container/5 rounded-full blur-[100px] pointer-events-none -z-10"></div>

    <div class="mx-auto max-w-container-max space-y-5 sm:space-y-6 lg:space-y-8">
        <!-- Hero Header Section -->
        <header class="glass-panel relative flex flex-col items-start justify-between gap-5 overflow-hidden rounded-xl border-t border-white/20 p-5 sm:p-7 lg:flex-row lg:items-center lg:gap-8 lg:p-10">
            <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-primary-container/20 blur-3xl"></div>
            <div class="z-10 flex-1">
                <p class="mb-2 font-label-caps text-label-caps uppercase tracking-[0.2em] text-primary">Target Tujuan</p>
                <h1 class="mb-3 max-w-[8ch] text-4xl font-black leading-[0.92] tracking-tighter text-white neon-glow font-['Space_Grotesk'] sm:max-w-none sm:text-5xl lg:text-display-xl"><?php echo strtoupper(htmlspecialchars($pengaturan['nama_target'])); ?></h1>
                <p class="max-w-md text-sm text-slate-400 sm:text-base"><?php echo htmlspecialchars($pengaturan['catatan'] ?: 'Pantau progres menabung Anda dengan sistematis dan konsisten.'); ?></p>
            </div>
            <div class="z-10 flex w-full max-w-[260px] flex-col items-center justify-center rounded-xl border border-primary/30 bg-surface-container-lowest/80 p-5 neon-border-glow shadow-[0_0_30px_rgba(0,82,255,0.1)] sm:p-6">
                <p class="mb-2 font-label-caps text-label-caps uppercase tracking-widest text-slate-400">Sisa Waktu</p>
                <div class="text-4xl font-['Space_Grotesk'] font-black tracking-tighter text-white tabular-nums drop-shadow-[0_0_12px_rgba(255,255,255,0.5)] sm:text-5xl">
                    <?php echo $sisaHari; ?> <span class="ml-1 text-xl font-bold tracking-normal text-primary sm:text-2xl">HARI</span>
                </div>
            </div>
        </header>

        <!-- Bento Grid - Key Metrics -->
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
            <div class="glass-panel group relative overflow-hidden rounded-xl p-4 sm:p-5 lg:p-6">
                <div class="absolute inset-0 bg-gradient-to-b from-primary-container/5 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="mb-4 flex items-start justify-between sm:mb-6">
                    <p class="font-label-caps text-label-caps uppercase tracking-wider text-slate-400">Saldo Total</p>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full border border-primary-container/30 bg-primary-container/20 sm:h-10 sm:w-10">
                        <span class="material-symbols-outlined text-primary-container">account_balance_wallet</span>
                    </div>
                </div>
                <h3 class="mb-2 text-3xl font-headline-md text-white tabular-nums sm:text-headline-md"><?php echo rupiah($saldo); ?></h3>
                <div class="flex items-center gap-2 text-xs font-data-mono text-emerald-400 sm:text-data-mono">
                    <span class="material-symbols-outlined text-sm">trending_up</span>
                    <span><?php echo number_format($totalMasuk > 0 ? (($saldo / $totalMasuk) * 100) : 0, 1); ?>% dari target</span>
                </div>
            </div>

            <div class="glass-panel group relative flex flex-col justify-between overflow-hidden rounded-xl p-4 sm:p-5 lg:p-6">
                <div class="absolute inset-0 bg-gradient-to-b from-primary-container/5 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="mb-4 flex items-start justify-between">
                    <p class="font-label-caps text-label-caps uppercase tracking-wider text-slate-400">Target Tabungan</p>
                    <span class="material-symbols-outlined text-slate-500">flag</span>
                </div>
                <div>
                    <h3 class="mb-3 text-3xl font-headline-md text-white tabular-nums sm:mb-4 sm:text-headline-md"><?php echo rupiah($targetNominal); ?></h3>
                    <div class="h-2 w-full overflow-hidden rounded-full border border-white/5 bg-surface-container-high">
                        <div class="relative h-full rounded-full bg-gradient-to-r from-primary-container to-[#00c1fd] shadow-[0_0_10px_rgba(0,82,255,0.8)]" style="width: <?php echo $targetProgressBar; ?>%">
                            <div class="absolute right-0 top-0 bottom-0 w-4 bg-white/30 blur-[2px]"></div>
                        </div>
                    </div>
                    <div class="mt-2 flex justify-between gap-3 text-[11px] font-data-mono text-slate-400 sm:text-xs">
                        <span><?php echo number_format($progressNominal, 1); ?>% Tercapai</span>
                        <span><?php echo rupiah($sisaTarget); ?> Sisa</span>
                    </div>
                </div>
            </div>

            <div class="glass-panel group relative overflow-hidden rounded-xl p-4 sm:p-5 lg:p-6">
                <div class="absolute inset-0 bg-gradient-to-b from-primary-container/5 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="mb-4 flex items-start justify-between sm:mb-6">
                    <p class="font-label-caps text-label-caps uppercase tracking-wider text-slate-400">Setoran Ideal</p>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-surface-container-high sm:h-10 sm:w-10">
                        <span class="material-symbols-outlined text-slate-300">update</span>
                    </div>
                </div>
                <h3 class="mb-1 text-3xl font-headline-md text-white tabular-nums sm:mb-2 sm:text-headline-md"><?php echo rupiah($tabunganHarian); ?></h3>
                <p class="text-sm text-slate-400">/ Hari</p>
                <div class="mt-3 flex h-6 items-end gap-1 opacity-60 sm:mt-4 sm:h-8">
                    <div class="w-full rounded-t-sm bg-primary-container/40" style="height: 40%"></div>
                    <div class="w-full rounded-t-sm bg-primary-container/40" style="height: 60%"></div>
                    <div class="w-full rounded-t-sm bg-primary-container/40" style="height: 80%"></div>
                    <div class="w-full rounded-t-sm bg-primary-container/80 shadow-[0_0_8px_rgba(0,82,255,0.5)]" style="height: 100%"></div>
                    <div class="w-full rounded-t-sm bg-primary-container/40" style="height: 60%"></div>
                </div>
            </div>

            <div class="glass-panel group relative overflow-hidden rounded-xl p-4 sm:p-5 lg:p-6">
                <div class="absolute inset-0 bg-gradient-to-b from-primary-container/5 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="mb-4 flex items-start justify-between sm:mb-6">
                    <p class="font-label-caps text-label-caps uppercase tracking-wider text-slate-400">Total Transaksi</p>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-surface-container-high sm:h-10 sm:w-10">
                        <span class="material-symbols-outlined text-slate-300">receipt_long</span>
                    </div>
                </div>
                <h3 class="mb-3 text-3xl font-headline-md text-white tabular-nums sm:mb-4 sm:text-headline-md"><?php echo $jumlahTransaksi; ?></h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-slate-400"><span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_5px_#34d399]"></span> Masuk</span>
                        <span class="font-data-mono text-white"><?php echo $jumlahTransaksi - $persenKeluar; ?></span>
                    </div>
                    <div class="holographic-divider opacity-50"></div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-slate-400"><span class="h-2 w-2 rounded-full bg-rose-400 shadow-[0_0_5px_#fb7185]"></span> Keluar</span>
                        <span class="font-data-mono text-white"><?php echo $persenKeluar; ?></span>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
            <div class="space-y-4 sm:space-y-5 lg:col-span-2 lg:space-y-6">
                <section class="glass-panel rounded-xl p-5 sm:p-6 lg:p-8">
                    <div class="mb-5 flex items-center justify-between sm:mb-8">
                        <h2 class="text-xl font-headline-lg text-white sm:text-2xl">Progress Metrics</h2>
                        <button class="text-slate-400 transition-colors hover:text-primary"><span class="material-symbols-outlined">more_horiz</span></button>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:gap-5 md:grid-cols-2 md:gap-6 lg:gap-8">
                        <div class="relative rounded-lg border border-white/5 bg-surface-container-lowest/50 p-4 sm:p-5 lg:p-6">
                            <p class="mb-4 text-center font-label-caps text-label-caps uppercase text-slate-400">Progress Nominal</p>
                            <div class="relative mx-auto flex h-32 w-32 items-center justify-center sm:h-36 sm:w-36 lg:h-40 lg:w-40">
                                <svg class="absolute inset-0 h-full w-full -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" fill="none" r="45" stroke="rgba(255,255,255,0.05)" stroke-dasharray="2 4" stroke-width="4"></circle>
                                    <circle class="drop-shadow-[0_0_8px_rgba(0,82,255,0.8)]" cx="50" cy="50" fill="none" r="45" stroke="#0052FF" stroke-dasharray="<?php echo $nominalDashArray; ?>" stroke-linecap="round" stroke-width="4"></circle>
                                </svg>
                                <div class="text-center">
                                    <span class="text-2xl font-black text-white font-['Space_Grotesk'] sm:text-3xl"><?php echo number_format($progressNominal, 1); ?>%</span>
                                </div>
                            </div>
                            <div class="mt-4 text-center text-xs font-data-mono text-slate-400 sm:mt-6 sm:text-sm">
                                Target <?php echo rupiah($targetNominal); ?>
                            </div>
                        </div>

                        <div class="relative rounded-lg border border-white/5 bg-surface-container-lowest/50 p-4 sm:p-5 lg:p-6">
                            <p class="mb-4 text-center font-label-caps text-label-caps uppercase text-slate-400">Progress Waktu</p>
                            <div class="relative mx-auto flex h-32 w-32 items-center justify-center sm:h-36 sm:w-36 lg:h-40 lg:w-40">
                                <svg class="absolute inset-0 h-full w-full -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" fill="none" r="45" stroke="rgba(255,255,255,0.05)" stroke-dasharray="2 4" stroke-width="4"></circle>
                                    <circle class="drop-shadow-[0_0_8px_rgba(0,193,253,0.8)]" cx="50" cy="50" fill="none" r="45" stroke="#00c1fd" stroke-dasharray="<?php echo $waktuDashArray; ?>" stroke-linecap="round" stroke-width="4"></circle>
                                </svg>
                                <div class="text-center">
                                    <span class="text-2xl font-black text-white font-['Space_Grotesk'] sm:text-3xl"><?php echo number_format($progressWaktu, 1); ?>%</span>
                                </div>
                            </div>
                            <div class="mt-4 text-center text-xs font-data-mono text-slate-400 sm:mt-6 sm:text-sm">
                                <?php echo $sisaHari; ?> hari tersisa
                            </div>
                        </div>
                    </div>
                </section>

                <section class="glass-panel rounded-xl p-5 sm:p-6 lg:p-8">
                    <div class="mb-5 flex items-center justify-between sm:mb-6">
                        <h2 class="text-xl font-headline-lg text-white sm:text-2xl">Riwayat Transaksi</h2>
                        <a class="text-sm font-label-caps uppercase tracking-wider text-primary transition-colors hover:text-white" href="#">Lihat Semua</a>
                    </div>
                    <div class="space-y-4">
                        <?php if (mysqli_num_rows($data) > 0) { ?>
                            <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                                <div class="group flex flex-col gap-3 rounded-lg border border-white/5 bg-surface-container-low/40 p-4 transition-colors hover:bg-white/5 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-3 sm:gap-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full border transition-colors sm:h-12 sm:w-12 <?php echo $d['tipe'] === 'masuk' ? 'bg-primary-container/10 border-primary-container/20 group-hover:border-primary-container/50' : 'bg-surface-container-high border-white/10 group-hover:border-white/30'; ?>">
                                            <span class="material-symbols-outlined <?php echo $d['tipe'] === 'masuk' ? 'text-primary-container' : 'text-slate-300'; ?>">
                                                <?php echo $d['tipe'] === 'masuk' ? 'arrow_downward' : 'arrow_upward'; ?>
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="mb-1 truncate text-sm font-medium text-white sm:text-base"><?php echo htmlspecialchars($d['keterangan']); ?></h4>
                                            <p class="text-xs text-slate-400 font-data-mono"><?php echo date('d M Y • H:i', strtotime($d['tanggal'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="<?php echo $d['tipe'] === 'masuk' ? 'text-emerald-400' : 'text-white'; ?> font-data-mono font-medium">
                                            <?php echo $d['tipe'] === 'masuk' ? '+ ' : '- '; ?><?php echo rupiah($d['jumlah']); ?>
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">Berhasil</p>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="py-8 text-center text-slate-400">
                                <span class="material-symbols-outlined mb-2 block text-4xl">receipt</span>
                                <p>Belum ada transaksi</p>
                            </div>
                        <?php } ?>
                    </div>
                </section>
            </div>

            <div class="space-y-4 sm:space-y-5 lg:space-y-6">
                <div class="glass-panel rounded-xl p-5 sm:p-6 lg:p-8">
                    <h3 class="mb-4 text-xl font-headline-lg text-white">Informasi Target</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <span class="text-slate-400">Mulai:</span>
                            <span class="font-data-mono text-white"><?php echo $tanggalMulai->format('d M Y'); ?></span>
                        </div>
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <span class="text-slate-400">Target:</span>
                            <span class="font-data-mono text-white"><?php echo $tanggalTarget->format('d M Y'); ?></span>
                        </div>
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <span class="text-slate-400">Total Hari:</span>
                            <span class="font-data-mono text-white"><?php echo $totalHari; ?> hari</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <span class="text-slate-400">Setoran Mingguan:</span>
                            <span class="font-data-mono text-white"><?php echo rupiah($tabunganMingguan); ?></span>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-slate-400">Sisa Bulan Ini:</span>
                            <span class="font-data-mono font-bold text-primary"><?php echo ceil($sisaTarget / 4); ?> hari</span>
                        </div>
                    </div>

                    <a href="tambah.php" class="mt-6 block w-full rounded-lg bg-primary-container py-3 text-center font-label-caps text-label-caps uppercase tracking-wider text-white shadow-[0_0_15px_rgba(0,82,255,0.5)] transition-colors hover:bg-[#0038b6]">
                        Tambah Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebarBackdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarBackdrop.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    sidebarBackdrop.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebarBackdrop.classList.add('hidden');
            sidebar.classList.remove('-translate-x-full');
            document.body.style.overflow = 'auto';
        } else if (sidebarBackdrop.classList.contains('hidden')) {
            sidebar.classList.add('-translate-x-full');
        }
    });
</script>

<?php include 'pwa-install-ui.php'; ?>
</body>
</html>
