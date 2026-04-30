<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

$pengaturanResult = mysqli_query($conn, "SELECT * FROM pengaturan_tabungan WHERE id = 1 LIMIT 1");
$pengaturan = mysqli_fetch_assoc($pengaturanResult);
?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Kelola Tabungan | Tabunganku</title>
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
        <a class="flex items-center gap-4 rounded-xl px-4 py-4 text-slate-400 transition-all duration-300 hover:bg-white/5 hover:text-white font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold lg:px-6" href="index.php">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>
        <a class="flex items-center gap-4 rounded-xl bg-[#0052FF]/10 px-4 py-4 text-[#0052FF] shadow-[inset_-10px_0_20px_rgba(0,82,255,0.05)] font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold lg:rounded-none lg:border-r-2 lg:border-[#0052FF] lg:px-6" href="#">
            <span class="material-symbols-outlined">settings</span> Kelola
        </a>
        <a class="flex items-center gap-4 rounded-xl px-4 py-4 text-slate-400 transition-all duration-300 hover:bg-white/5 hover:text-white font-['Space_Grotesk'] tracking-wider uppercase text-xs font-semibold lg:px-6" href="laporan.php">
            <span class="material-symbols-outlined">monitoring</span> Laporan
        </a>
    </nav>
    <div class="px-5 mt-auto mb-6 lg:px-6">
        <button onclick="openTransactionModal()" class="w-full py-3 bg-primary-container text-white rounded-lg font-label-caps text-label-caps uppercase tracking-wider hover:bg-[#0038b6] transition-colors shadow-[0_0_15px_rgba(0,82,255,0.5)] flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">add</span> Tambah Transaksi
        </button>
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
            <p class="text-[11px] uppercase tracking-[0.24em] text-slate-400 sm:hidden">Kelola Tabungan</p>
        </div>
    </div>
    <p class="hidden text-slate-400 font-data-mono text-label-caps lg:block">Kelola Tabungan Anda</p>
</nav>

<!-- Main Content Canvas -->
<main class="relative min-h-screen px-4 pb-8 pt-20 sm:px-6 lg:ml-72 lg:px-8 lg:pt-24">
    <!-- Background Ambient Glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-primary-container/5 rounded-full blur-[120px] pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 right-0 w-[600px] h-[600px] bg-tertiary-container/5 rounded-full blur-[100px] pointer-events-none -z-10"></div>

    <div class="mx-auto max-w-4xl space-y-6 lg:space-y-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 lg:mb-8 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="font-label-caps text-label-caps text-primary mb-2 tracking-[0.2em] uppercase">Kelola Tabungan</p>
                <h1 class="text-3xl font-black text-white tracking-tighter font-['Space_Grotesk'] sm:text-4xl">Atur target dan catat transaksi</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-400 sm:text-base">Kelola pengaturan target tabungan dan catat setiap transaksi untuk memantau progres menabung Anda.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
            <button type="button" onclick="openTransactionModal()" class="flex w-full items-center justify-center gap-2 rounded-lg bg-secondary-container px-4 py-3 text-white shadow-[0_0_15px_rgba(0,193,253,0.35)] transition-colors hover:bg-[#0099cc] lg:hidden">
                <span class="material-symbols-outlined text-sm">add</span> Tambah Transaksi
            </button>
            <a href="index.php" class="flex items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-primary-container px-6 py-3 text-white shadow-[0_0_15px_rgba(0,82,255,0.5)] transition-colors hover:bg-[#0038b6] font-label-caps text-label-caps uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Dashboard
            </a>
            </div>
        </div>

        <!-- Forms Grid -->
        <section class="grid grid-cols-1 gap-8">
            <!-- Pengaturan Target -->
            <div class="glass-panel rounded-xl p-5 sm:p-6 lg:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-primary-container/30 bg-primary-container/20">
                        <span class="material-symbols-outlined text-primary-container">flag</span>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-xl font-headline-lg text-white sm:text-2xl">Pengaturan Target</h2>
                        <p class="text-slate-400 text-sm mt-1">Atur nama, periode, dan nominal target tabungan Anda.</p>
                    </div>
                </div>

                <form action="proses.php" method="POST" class="space-y-5">
                    <input type="hidden" name="aksi" value="simpan_target">

                    <label class="block">
                        <span class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-2">Nama Target</span>
                        <input type="text" name="nama_target" value="<?php echo htmlspecialchars($pengaturan['nama_target']); ?>" 
                               class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white placeholder-slate-500 focus:border-primary-container focus:outline-none transition-colors"
                               placeholder="Contoh: Liburan ke Bali" required>
                    </label>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-2">Tanggal Mulai</span>
                            <input type="date" name="tanggal_mulai" value="<?php echo htmlspecialchars($pengaturan['tanggal_mulai']); ?>" 
                                   class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white focus:border-primary-container focus:outline-none transition-colors"
                                   required>
                        </label>

                        <label class="block">
                            <span class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-2">Tanggal Target</span>
                            <input type="date" name="tanggal_target" value="<?php echo htmlspecialchars($pengaturan['tanggal_target']); ?>" 
                                   class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white focus:border-primary-container focus:outline-none transition-colors"
                                   required>
                        </label>
                    </div>

                    <label class="block">
                        <span class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-2">Target Nominal (Rp)</span>
                        <input type="number" name="target_nominal" min="1000" step="1000" value="<?php echo htmlspecialchars($pengaturan['target_nominal']); ?>" 
                               class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white placeholder-slate-500 focus:border-primary-container focus:outline-none transition-colors"
                               placeholder="50000000" required>
                    </label>

                    <label class="block">
                        <span class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-2">Catatan Singkat</span>
                        <textarea name="catatan" rows="3" 
                                  class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white placeholder-slate-500 focus:border-primary-container focus:outline-none transition-colors resize-none"
                                  placeholder="Contoh: Dana baju lebaran, hampers, THR keluarga, dan mudik."><?php echo htmlspecialchars($pengaturan['catatan']); ?></textarea>
                    </label>

                    <button type="submit" class="w-full py-3 bg-primary-container text-white rounded-lg font-label-caps text-label-caps uppercase tracking-wider hover:bg-[#0038b6] transition-colors shadow-[0_0_15px_rgba(0,82,255,0.5)] flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">save</span> Simpan Pengaturan
                    </button>
                </form>
            </div>
        </section>
    </div>
</main>

<!-- Modal Backdrop -->
<div id="modalBackdrop" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50" onclick="closeTransactionModal()"></div>

<!-- Modal Tambah Transaksi -->
<div id="transactionModal" class="hidden fixed inset-0 flex items-center justify-center z-50 p-4">
    <div class="glass-panel mobile-safe-bottom relative z-50 w-full max-w-md rounded-xl p-5 sm:p-6 lg:p-8 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-secondary-container/30 bg-secondary-container/20">
                    <span class="material-symbols-outlined text-secondary-container">receipt_long</span>
                </div>
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-white text-lg">Tambah Transaksi</h2>
                </div>
            </div>
            <button onclick="closeTransactionModal()" class="text-slate-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="proses.php" method="POST" class="space-y-4">
            <input type="hidden" name="aksi" value="simpan_transaksi">

            <label class="block">
                <span class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-2">Tanggal Transaksi</span>
                <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" 
                       class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white focus:border-primary-container focus:outline-none transition-colors"
                       required>
            </label>

            <label class="block">
                <span class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-2">Tipe Transaksi</span>
                <select name="tipe" 
                        class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white focus:border-primary-container focus:outline-none transition-colors appearance-none cursor-pointer"
                        required>
                    <option value="masuk" class="bg-surface-container-highest">Uang Masuk</option>
                    <option value="keluar" class="bg-surface-container-highest">Uang Keluar</option>
                </select>
            </label>

            <label class="block">
                <span class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-2">Keterangan</span>
                <input type="text" name="keterangan" 
                       class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white placeholder-slate-500 focus:border-primary-container focus:outline-none transition-colors"
                       placeholder="Contoh: Setoran mingguan" required>
            </label>

            <label class="block">
                <span class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-2">Jumlah (Rp)</span>
                <input type="number" name="jumlah" min="1000" step="1000" 
                       class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white placeholder-slate-500 focus:border-primary-container focus:outline-none transition-colors"
                       placeholder="500000" required>
            </label>

            <div class="flex flex-col gap-3 pt-4 sm:flex-row">
                <button type="button" onclick="closeTransactionModal()" class="flex-1 py-3 bg-surface-container-high border border-white/10 text-white rounded-lg font-label-caps text-label-caps uppercase tracking-wider hover:bg-white/5 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 bg-secondary-container text-white rounded-lg font-label-caps text-label-caps uppercase tracking-wider hover:bg-[#0099cc] transition-colors shadow-[0_0_15px_rgba(0,193,253,0.5)] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">add</span> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

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
        document.body.style.overflow = document.getElementById('transactionModal').classList.contains('hidden') ? 'auto' : 'hidden';
    }

    function openTransactionModal() {
        document.getElementById('transactionModal').classList.remove('hidden');
        document.getElementById('modalBackdrop').classList.remove('hidden');
        closeSidebar();
        document.body.style.overflow = 'hidden';
    }

    function closeTransactionModal() {
        document.getElementById('transactionModal').classList.add('hidden');
        document.getElementById('modalBackdrop').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    sidebarBackdrop.addEventListener('click', closeSidebar);

    // Close modal when clicking outside
    document.getElementById('modalBackdrop').addEventListener('click', closeTransactionModal);

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeTransactionModal();
            closeSidebar();
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebarBackdrop.classList.add('hidden');
            sidebar.classList.remove('-translate-x-full');
            document.body.style.overflow = document.getElementById('transactionModal').classList.contains('hidden') ? 'auto' : 'hidden';
        } else if (sidebarBackdrop.classList.contains('hidden')) {
            sidebar.classList.add('-translate-x-full');
        }
    });
</script>

<?php include 'pwa-install-ui.php'; ?>
</body>
</html>
