/**
 * Hotel Golden Hill - Offline Workflow & Background Sync Manager
 * Handles Service Worker registration, signal blindspot detection,
 * localStorage draft caching, and auto-sync on network reconnect.
 */

(function () {
    'use strict';

    // 1. Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/work-order/sw.js')
                .then(function (reg) {
                    console.log('[OfflineManager] ServiceWorker registered with scope:', reg.scope);
                })
                .catch(function (err) {
                    console.warn('[OfflineManager] ServiceWorker registration failed:', err);
                });
        });
    }

    // 2. Banner Element Creation & Event Listeners
    function createOfflineBanner() {
        if (document.getElementById('ghh-connectivity-banner')) return;

        const banner = document.createElement('div');
        banner.id = 'ghh-connectivity-banner';
        banner.style.cssText = 'display:none; position:fixed; bottom:20px; right:20px; z-index:9999; max-width:380px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.3); border-radius:12px; transition:all 0.3s ease;';
        document.body.appendChild(banner);
    }

    function updateConnectivityStatus() {
        createOfflineBanner();
        const banner = document.getElementById('ghh-connectivity-banner');
        if (!banner) return;

        if (!navigator.onLine) {
            // Offline state
            banner.className = 'alert alert-warning border-warning border-opacity-50 p-3 mb-0 text-dark';
            banner.style.display = 'block';
            banner.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-wifi-off fs-4 text-danger animate-pulse"></i>
                    <div>
                        <strong class="d-block" style="font-size: 0.9rem;">Mode Offline Terdeteksi</strong>
                        <span style="font-size: 0.78rem;" class="text-muted">Sinyal lemah / basement. Form laporan akan disimpan otomatis sebagai draft lokal.</span>
                    </div>
                </div>
            `;
        } else {
            // Online state
            const pending = getOfflineDrafts();
            if (pending.length > 0) {
                banner.className = 'alert alert-success border-success border-opacity-50 p-3 mb-0 text-dark';
                banner.style.display = 'block';
                banner.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-repeat spin fs-4 text-success"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.9rem;">Koneksi Pulih!</strong>
                                <span style="font-size: 0.78rem;" class="text-muted">Menyinkronkan ${pending.length} draft offline ke server...</span>
                            </div>
                        </div>
                    </div>
                `;
                // Trigger auto-sync
                syncOfflineDrafts();
            } else {
                banner.style.display = 'none';
            }
        }
    }

    window.addEventListener('online', function () {
        console.log('[OfflineManager] Network connection restored');
        updateConnectivityStatus();
    });

    window.addEventListener('offline', function () {
        console.log('[OfflineManager] Network connection lost (offline)');
        updateConnectivityStatus();
    });

    document.addEventListener('DOMContentLoaded', function () {
        updateConnectivityStatus();
        bindOfflineFormHandlers();
    });

    // 3. Draft Storage Helpers
    window.getOfflineDrafts = function () {
        try {
            const raw = localStorage.getItem('ghh_offline_wo_drafts');
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    };

    window.saveOfflineDraft = function (data) {
        try {
            const drafts = getOfflineDrafts();
            data._offlineId = 'draft_' + Date.now();
            data._savedAt = new Date().toLocaleString();
            drafts.push(data);
            localStorage.setItem('ghh_offline_wo_drafts', JSON.stringify(drafts));
            return true;
        } catch (e) {
            console.error('[OfflineManager] Failed to save draft:', e);
            return false;
        }
    };

    window.removeOfflineDraft = function (offlineId) {
        try {
            let drafts = getOfflineDrafts();
            drafts = drafts.filter(d => d._offlineId !== offlineId);
            localStorage.setItem('ghh_offline_wo_drafts', JSON.stringify(drafts));
        } catch (e) {}
    };

    // 4. Background Sync Logic
    window.syncOfflineDrafts = function () {
        const drafts = getOfflineDrafts();
        if (drafts.length === 0) return;

        console.log('[OfflineManager] Starting synchronization of', drafts.length, 'drafts');

        fetch('/work-order/workorder/sync-offline-draft', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ drafts: drafts })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                localStorage.removeItem('ghh_offline_wo_drafts');
                const banner = document.getElementById('ghh-connectivity-banner');
                if (banner) {
                    banner.className = 'alert alert-success border-success p-3 mb-0 text-dark';
                    banner.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.9rem;">Sinkronisasi Selesai!</strong>
                                <span style="font-size: 0.78rem;" class="text-muted">${result.count || drafts.length} Work Order offline telah berhasil dikirim ke server.</span>
                            </div>
                        </div>
                    `;
                    setTimeout(() => {
                        banner.style.display = 'none';
                    }, 5000);
                }
            }
        })
        .catch(err => {
            console.warn('[OfflineManager] Sync failed, will retry on next connection:', err);
        });
    };

    // 5. Intercept Form Submission when Offline
    function bindOfflineFormHandlers() {
        const createForm = document.querySelector('form[action*="workorder/create"]');
        if (createForm) {
            createForm.addEventListener('submit', function (e) {
                if (!navigator.onLine) {
                    e.preventDefault();
                    const formData = new FormData(createForm);
                    const draftObj = {};
                    formData.forEach((val, key) => {
                        if (typeof val === 'string') {
                            draftObj[key] = val;
                        }
                    });

                    const saved = saveOfflineDraft(draftObj);
                    if (saved) {
                        alert("⚠️ Perangkat Anda sedang offline.\n\nLaporan Work Order Anda telah berhasil disimpan sebagai DRAFT LOKAL di perangkat ini. Sistem akan otomatis mengirimkannya ke server saat Anda kembali ke area bersinyal.");
                        createForm.reset();
                    }
                }
            });
        }
    }
})();
