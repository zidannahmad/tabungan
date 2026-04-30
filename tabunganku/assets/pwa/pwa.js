let deferredInstallPrompt = null;

function showInstallUI() {
    const installButton = document.getElementById('pwa-install-button');
    const installToast = document.getElementById('pwa-install-toast');

    if (installButton) {
        installButton.classList.remove('hidden');
        installButton.classList.add('inline-flex');
    }

    if (installToast) {
        installToast.classList.remove('hidden');
        window.setTimeout(function () {
            installToast.classList.add('hidden');
        }, 5000);
    }
}

function hideInstallUI() {
    const installButton = document.getElementById('pwa-install-button');

    if (installButton) {
        installButton.classList.add('hidden');
        installButton.classList.remove('inline-flex');
    }
}

if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('./sw.js');
    });
}

window.addEventListener('beforeinstallprompt', function (event) {
    event.preventDefault();
    deferredInstallPrompt = event;
    showInstallUI();
});

window.addEventListener('appinstalled', function () {
    deferredInstallPrompt = null;
    hideInstallUI();
});

window.addEventListener('DOMContentLoaded', function () {
    const installButton = document.getElementById('pwa-install-button');

    if (!installButton) {
        return;
    }

    installButton.addEventListener('click', async function () {
        if (!deferredInstallPrompt) {
            showInstallUI();
            return;
        }

        deferredInstallPrompt.prompt();
        await deferredInstallPrompt.userChoice;
        deferredInstallPrompt = null;
        hideInstallUI();
    });
});
