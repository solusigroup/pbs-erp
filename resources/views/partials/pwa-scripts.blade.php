<!-- PWA Toast Notifications & Install Modal -->
<div id="pwa-toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none max-w-sm w-full px-4 sm:px-0">
    <!-- Offline Alert -->
    <div id="pwa-offline-toast" class="hidden pointer-events-auto bg-rose-950/90 text-rose-200 border border-rose-500/40 rounded-xl p-3.5 shadow-2xl backdrop-blur-md flex items-center justify-between gap-3 transform transition-all duration-300">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                <i class="fas fa-wifi-slash text-sm"></i>
            </span>
            <div>
                <h5 class="text-xs font-bold text-white">Mode Offline</h5>
                <p class="text-[11px] text-rose-300">Koneksi internet terputus. Cache aktif.</p>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('pwa-offline-toast').classList.add('hidden')" class="text-rose-400 hover:text-white p-1">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>

    <!-- Online Alert -->
    <div id="pwa-online-toast" class="hidden pointer-events-auto bg-emerald-950/90 text-emerald-200 border border-emerald-500/40 rounded-xl p-3.5 shadow-2xl backdrop-blur-md flex items-center justify-between gap-3 transform transition-all duration-300">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <i class="fas fa-wifi text-sm"></i>
            </span>
            <div>
                <h5 class="text-xs font-bold text-white">Terhubung Kembali</h5>
                <p class="text-[11px] text-emerald-300">Koneksi internet Anda telah normal.</p>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('pwa-online-toast').classList.add('hidden')" class="text-emerald-400 hover:text-white p-1">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>

    <!-- Update Available Alert -->
    <div id="pwa-update-toast" class="hidden pointer-events-auto bg-slate-900/95 text-slate-200 border border-amber-500/40 rounded-xl p-3.5 shadow-2xl backdrop-blur-md flex items-center justify-between gap-3 transform transition-all duration-300">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 animate-pulse">
                <i class="fas fa-arrows-rotate text-sm"></i>
            </span>
            <div>
                <h5 class="text-xs font-bold text-white">Pembaruan Tersedia</h5>
                <p class="text-[11px] text-slate-300">Versi baru PBS-ERP siap digunakan.</p>
            </div>
        </div>
        <button type="button" id="pwa-update-btn" class="px-2.5 py-1 text-xs font-bold bg-[#ff8c00] hover:bg-[#e07b00] text-white rounded-lg shadow-sm transition">
            Perbarui
        </button>
    </div>
</div>

<!-- Floating Install Prompt Banner -->
<div id="pwa-install-banner" class="hidden fixed bottom-5 right-5 left-5 sm:left-auto sm:max-w-md z-50 bg-[#0d1e38]/95 backdrop-blur-md border border-slate-700/80 rounded-2xl p-4 shadow-2xl text-slate-200 transition-all duration-300">
    <div class="flex items-start gap-3.5">
        <div class="h-12 w-12 rounded-xl bg-slate-800 p-1.5 flex items-center justify-center shrink-0 border border-amber-500/30 shadow-md">
            <img src="{{ asset('icons/icon-96x96.png') }}" alt="PBS-ERP Icon" class="h-full w-full object-contain">
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <h4 class="text-sm font-extrabold text-white tracking-tight">Pasang Aplikasi PBS-ERP</h4>
                <button type="button" onclick="dismissPwaBanner()" class="text-slate-400 hover:text-white p-1 text-xs">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                Nikmati akses cepat tanpa membuka browser, performa lebih stabil, dan tampilan penuh seperti aplikasi native di HP atau Komputer Anda.
            </p>
            <div class="flex items-center gap-2 mt-3">
                <button type="button" onclick="installPwaApp()" class="flex-1 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-xs py-2 px-3 rounded-xl shadow-lg shadow-orange-500/20 transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-download text-xs"></i>
                    <span>Pasang Sekarang</span>
                </button>
                <button type="button" onclick="dismissPwaBanner()" class="bg-slate-800/80 hover:bg-slate-700 text-slate-300 font-semibold text-xs py-2 px-3 rounded-xl border border-slate-700 transition">
                    Nanti Saja
                </button>
            </div>
        </div>
    </div>
</div>

<!-- iOS Install Instruction Modal -->
<div id="pwa-ios-modal" class="hidden fixed inset-0 z-[10000] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#0a1628] border border-slate-700 rounded-2xl max-w-sm w-full p-5 text-slate-200 shadow-2xl relative">
        <button type="button" onclick="closeIosModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white">
            <i class="fas fa-times text-base"></i>
        </button>
        <div class="text-center mb-4">
            <div class="inline-flex h-12 w-12 rounded-xl bg-slate-800 p-2 items-center justify-center border border-amber-500/30 mb-2">
                <img src="{{ asset('icons/icon-96x96.png') }}" alt="PBS Logo" class="h-full w-full object-contain">
            </div>
            <h3 class="text-base font-bold text-white">Pasang di iPhone / iPad</h3>
            <p class="text-xs text-slate-400 mt-1">Ikuti 2 langkah mudah berikut di Safari:</p>
        </div>
        <div class="space-y-3 text-xs bg-slate-900/60 p-3.5 rounded-xl border border-slate-800">
            <div class="flex items-start gap-2.5">
                <div class="h-6 w-6 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold shrink-0 text-[11px]">1</div>
                <p>Tekan tombol <strong>Bagikan (Share)</strong> <i class="fas fa-arrow-up-from-bracket text-sky-400 mx-1"></i> di menu bawah Safari.</p>
            </div>
            <div class="flex items-start gap-2.5">
                <div class="h-6 w-6 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold shrink-0 text-[11px]">2</div>
                <p>Gulir ke bawah dan pilih <strong>"Tambah ke Layar Utama" (Add to Home Screen)</strong> <i class="fas fa-plus-square text-amber-400 mx-1"></i>.</p>
            </div>
        </div>
        <button type="button" onclick="closeIosModal()" class="w-full mt-4 bg-slate-800 hover:bg-slate-700 text-white font-semibold py-2 px-4 rounded-xl text-xs transition">
            Saya Mengerti
        </button>
    </div>
</div>

<script>
    (function() {
        let deferredPrompt = null;
        let newWorker = null;

        // 1. Detect if running standalone (PWA Installed)
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                             window.navigator.standalone === true;

        if (isStandalone) {
            document.body.classList.add('pwa-standalone');
            // Hide install triggers if already running as installed app
            document.querySelectorAll('.pwa-install-trigger').forEach(el => {
                el.classList.add('hidden');
            });
        }

        // 2. Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => {
                        // Check for updates to the service worker
                        reg.addEventListener('updatefound', () => {
                            newWorker = reg.installing;
                            if (newWorker) {
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        showUpdateToast();
                                    }
                                });
                            }
                        });
                    })
                    .catch((err) => {
                        console.warn('[PWA] Service Worker registration failed:', err);
                    });

                // Listen for controllerchange to reload on update
                let refreshing = false;
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    if (!refreshing) {
                        refreshing = true;
                        window.location.reload();
                    }
                });
            });
        }

        // 3. Before Install Prompt (Chrome, Edge, Android)
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Show manual install triggers across UI
            document.querySelectorAll('.pwa-install-trigger').forEach(el => {
                el.classList.remove('hidden');
            });

            // Check if user previously dismissed banner in last 3 days
            const dismissedAt = localStorage.getItem('pbs_pwa_dismissed');
            const now = Date.now();
            const threeDays = 3 * 24 * 60 * 60 * 1000;

            if (!isStandalone && (!dismissedAt || (now - parseInt(dismissedAt)) > threeDays)) {
                // Show banner after short delay for good user experience
                setTimeout(() => {
                    const banner = document.getElementById('pwa-install-banner');
                    if (banner && deferredPrompt) {
                        banner.classList.remove('hidden');
                    }
                }, 3000);
            }
        });

        // 4. App Installed Event
        window.addEventListener('appinstalled', () => {
            deferredPrompt = null;
            const banner = document.getElementById('pwa-install-banner');
            if (banner) banner.classList.add('hidden');
            document.querySelectorAll('.pwa-install-trigger').forEach(el => {
                el.classList.add('hidden');
            });
            console.log('[PWA] PBS-ERP berhasil dipasang.');
        });

        // 5. Global helper functions exposed
        window.installPwaApp = function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('[PWA] User accepted installation prompt');
                    }
                    deferredPrompt = null;
                    const banner = document.getElementById('pwa-install-banner');
                    if (banner) banner.classList.add('hidden');
                });
            } else {
                // Check if iOS
                const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                if (isIos) {
                    const iosModal = document.getElementById('pwa-ios-modal');
                    if (iosModal) iosModal.classList.remove('hidden');
                } else if (!isStandalone) {
                    alert('Untuk memasang aplikasi ini, klik ikon titik tiga (Menu) di browser Anda lalu pilih "Install / Pasang Aplikasi PBS-ERP".');
                }
            }
        };

        window.dismissPwaBanner = function() {
            const banner = document.getElementById('pwa-install-banner');
            if (banner) banner.classList.add('hidden');
            localStorage.setItem('pbs_pwa_dismissed', Date.now().toString());
        };

        window.closeIosModal = function() {
            const iosModal = document.getElementById('pwa-ios-modal');
            if (iosModal) iosModal.classList.add('hidden');
        };

        // 6. Online / Offline Event Listeners
        window.addEventListener('offline', () => {
            const offlineToast = document.getElementById('pwa-offline-toast');
            const onlineToast = document.getElementById('pwa-online-toast');
            if (onlineToast) onlineToast.classList.add('hidden');
            if (offlineToast) offlineToast.classList.remove('hidden');
        });

        window.addEventListener('online', () => {
            const offlineToast = document.getElementById('pwa-offline-toast');
            const onlineToast = document.getElementById('pwa-online-toast');
            if (offlineToast) offlineToast.classList.add('hidden');
            if (onlineToast) {
                onlineToast.classList.remove('hidden');
                setTimeout(() => {
                    onlineToast.classList.add('hidden');
                }, 4000);
            }
        });

        // 7. Update toast action
        function showUpdateToast() {
            const updateToast = document.getElementById('pwa-update-toast');
            const updateBtn = document.getElementById('pwa-update-btn');
            if (updateToast) updateToast.classList.remove('hidden');
            if (updateBtn) {
                updateBtn.onclick = function() {
                    if (newWorker) {
                        newWorker.postMessage({ action: 'skipWaiting' });
                    }
                };
            }
        }
    })();
</script>
