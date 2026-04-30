<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simple authentication (untuk production, gunakan database)
    // Default: Zidan / Zidan_12
    if ($username === 'Zidan' && $password === 'Zidan_12') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login | Tabunganku</title>
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
    </style>
</head>
<body class="bg-surface-container-lowest text-on-background font-body-md min-h-screen overflow-x-hidden selection:bg-primary-container selection:text-white flex items-center justify-center p-4">

<!-- Background Ambient Glow -->
<div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-primary-container/5 rounded-full blur-[120px] pointer-events-none -z-10"></div>
<div class="fixed bottom-0 right-0 w-[600px] h-[600px] bg-tertiary-container/5 rounded-full blur-[100px] pointer-events-none -z-10"></div>

<!-- Login Container -->
<div class="w-full max-w-md">
    <!-- Logo / Branding -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-black text-white font-['Space_Grotesk'] tracking-tighter neon-glow mb-2">Tabunganku</h1>
        <p class="text-slate-400 text-sm">Kelola tabungan Anda dengan mudah</p>
    </div>

    <!-- Login Card -->
    <div class="glass-panel rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-32 h-32 bg-primary-container/10 rounded-full blur-3xl"></div>

        <div class="relative z-10">
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-full bg-primary-container/20 flex items-center justify-center border border-primary-container/30 mx-auto mb-4">
                    <span class="material-symbols-outlined text-primary-container text-3xl">login</span>
                </div>
                <h2 class="text-2xl font-black text-white font-['Space_Grotesk'] tracking-tight">Masuk Admin</h2>
                <p class="text-slate-400 text-sm mt-2">Masukkan kredensial Anda untuk mengakses dashboard</p>
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-rose-500/20 border border-rose-500/50 rounded-lg">
                    <p class="text-rose-400 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">error</span>
                        <?php echo htmlspecialchars($error); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="" method="POST" class="space-y-5">
                <!-- Username Field -->
                <div>
                    <label class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-3">Username</label>
                    <input type="text" name="username" required
                           class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white placeholder-slate-500 focus:border-primary-container focus:outline-none transition-colors focus:ring-2 focus:ring-primary-container/20"
                           placeholder="Zidan">
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block font-label-caps text-label-caps text-slate-300 uppercase tracking-wider mb-3">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 bg-surface-container-high border border-white/10 rounded-lg text-white placeholder-slate-500 focus:border-primary-container focus:outline-none transition-colors focus:ring-2 focus:ring-primary-container/20"
                           placeholder="••••••••">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" 
                           class="w-4 h-4 rounded bg-surface-container-high border-white/10 text-primary-container focus:ring-2 focus:ring-primary-container/20 cursor-pointer">
                    <label for="remember" class="ml-3 text-sm text-slate-400 cursor-pointer hover:text-white transition-colors">
                        Ingat saya
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit"
                        class="w-full py-3 bg-primary-container text-white rounded-lg font-label-caps text-label-caps uppercase tracking-wider hover:bg-[#0038b6] transition-colors shadow-[0_0_15px_rgba(0,82,255,0.5)] flex items-center justify-center gap-2 font-semibold mt-6">
                    <span class="material-symbols-outlined">login</span> Masuk
                </button>
            </form>

        </div>
    </div>

    <!-- Footer Info -->
    <div class="text-center mt-8">
        <p class="text-slate-500 text-xs">
            © 2026 Tabunganku. Semua hak cipta dilindungi.
        </p>
    </div>
</div>

<?php include 'pwa-install-ui.php'; ?>
</body>
</html>
