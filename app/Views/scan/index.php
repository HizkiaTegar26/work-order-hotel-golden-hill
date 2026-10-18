<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-qr-code-scan text-warning me-2"></i><?= lang('Aset.scan_title') ?></h4>
                <p class="text-muted small mb-0"><?= lang('Aset.scan_subtitle') ?></p>
            </div>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <!-- Scanner Card -->
        <div class="card card-custom p-4 mb-4">
            <!-- Camera Status & Error Alert Banner (Hidden by default) -->
            <div id="cameraAlertBanner" class="alert alert-warning border-warning border-opacity-25 d-none mb-3 shadow-sm" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5 mt-1"></i>
                    <div>
                        <strong class="d-block text-dark"><?= lang('Aset.camera_error_denied') ?></strong>
                        <span class="small text-muted" id="cameraErrorMessage"><?= lang('Aset.camera_error_dark') ?></span>
                    </div>
                </div>
            </div>

            <!-- Camera Reader Viewport -->
            <div id="reader-wrapper" class="text-center position-relative">
                <div id="reader" style="width: 100%; max-width: 480px; margin: 0 auto; border-radius: 12px; overflow: hidden; background: #0f172a;" class="border shadow-sm"></div>
            </div>

            <!-- Camera Control Buttons (Touch Target >= 48px) -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary btn-mobile-touch" id="startCameraBtn" style="min-height: 48px;">
                    <i class="bi bi-camera-video me-1"></i> <?= lang('Aset.open_camera') ?>
                </button>
                <button type="button" class="btn btn-outline-danger btn-mobile-touch" id="stopCameraBtn" style="display: none; min-height: 48px;">
                    <i class="bi bi-camera-video-off me-1"></i> <?= lang('Aset.close_camera') ?>
                </button>
                <button type="button" class="btn btn-outline-warning btn-mobile-touch text-dark" id="torchBtn" style="display: none; min-height: 48px;" onclick="toggleTorch()">
                    <i class="bi bi-lightbulb me-1"></i> <?= lang('Aset.torch_toggle') ?>
                </button>
                <button type="button" class="btn btn-outline-secondary btn-mobile-touch" id="switchCameraBtn" style="display: none; min-height: 48px;" onclick="switchCamera()">
                    <i class="bi bi-arrow-repeat me-1"></i> <?= lang('Aset.switch_camera') ?>
                </button>
            </div>

            <!-- Fallback & Alternative Workflow Section (Mitigasi Kamera Rusak / Izin Ditolak / Ruang Gelap) -->
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 me-2">
                        <i class="bi bi-shield-check me-1"></i> Failover / Fallback
                    </span>
                    <span class="small fw-bold text-dark"><?= lang('Aset.fallback_manual_title') ?></span>
                </div>

                <!-- 1. Input Kode Aset Manual -->
                <div class="card bg-light border-0 p-3 mb-3 rounded-3 shadow-none">
                    <label class="form-label small fw-bold text-dark mb-1">
                        <i class="bi bi-keyboard text-warning me-1"></i> <?= lang('Aset.fallback_manual_input') ?>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc-scan text-muted"></i></span>
                        <input type="text" id="manualAssetCodeInput" class="form-control border-start-0" placeholder="Contoh: AST-ENG-001" style="text-transform: uppercase;" onkeyup="if(event.key==='Enter') submitManualCode()">
                        <button class="btn btn-gold fw-bold px-3" type="button" onclick="submitManualCode()" style="min-height: 48px;">
                            <i class="bi bi-search me-1"></i> <?= lang('Aset.btn_process_code') ?>
                        </button>
                    </div>
                </div>

                <!-- 2. Pilih Aset dari Searchable Directory Dropdown -->
                <div class="card bg-light border-0 p-3 mb-3 rounded-3 shadow-none">
                    <label class="form-label small fw-bold text-dark mb-1">
                        <i class="bi bi-list-check text-primary me-1"></i> <?= lang('Aset.fallback_dropdown_select') ?>
                    </label>
                    <select id="assetDropdownSelect" class="form-select form-select-lg" style="font-size: 0.95rem; min-height: 48px;" onchange="if(this.value) processScanCode(this.value)">
                        <option value=""><?= lang('Aset.fallback_select_placeholder') ?></option>
                        <?php foreach ($assets as $a): ?>
                            <option value="<?= esc($a['kode_aset']) ?>">
                                <?= esc($a['kode_aset']) ?> &mdash; <?= esc($a['nama_aset']) ?> (<?= esc($a['nama_departemen']) ?> - <?= esc($a['lokasi_spesifik']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 3. Manual / Quick Asset Selector for Desktop Testing -->
                <div>
                    <label class="small fw-bold text-muted mb-2 d-block">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> <?= lang('Aset.quick_simulation') ?>
                    </label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($assets as $a): ?>
                            <button type="button" class="btn btn-xs btn-outline-dark py-1 px-2 text-start" style="font-size: 0.75rem;" onclick="processScanCode('<?= esc($a['kode_aset']) ?>')">
                                <span class="badge bg-secondary me-1"><?= esc($a['kode_aset']) ?></span>
                                <?= esc($a['nama_aset']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asset Detected -->
<div class="modal fade" id="assetScannedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6"><i class="bi bi-check-circle-fill text-warning me-2"></i> <?= lang('Aset.asset_detected_title') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <span class="badge bg-warning text-dark fs-6 px-3 py-1" id="scannedKodeAset">AST-000</span>
                    <h5 class="fw-bold text-dark mt-2 mb-1" id="scannedNamaAset">Nama Aset</h5>
                    <div class="text-muted small" id="scannedLokasi">Lokasi Spesifik</div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted"><?= lang('Aset.managing_dept') ?></span>
                        <strong id="scannedDept">-</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><?= lang('Aset.current_condition') ?></span>
                        <span class="badge bg-success" id="scannedKondisi">Baik</span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="#" id="scannedReportBtn" class="btn btn-warning text-dark fw-bold py-2 shadow-sm">
                        <i class="bi bi-tools me-1"></i> <?= lang('Aset.btn_report_locked') ?>
                    </a>
                    <a href="#" id="scannedDetailBtn" class="btn btn-outline-dark py-2">
                        <i class="bi bi-clock-history me-1"></i> <?= lang('Aset.btn_view_history') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- HTML5-QRCode Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let availableDevices = [];
    let currentDeviceIndex = 0;
    let isTorchActive = false;
    let isScanning = false;

    const modalEl = document.getElementById('assetScannedModal');
    const assetModal = new bootstrap.Modal(modalEl);

    document.addEventListener("DOMContentLoaded", function() {
        html5QrCode = new Html5Qrcode("reader");

        document.getElementById('startCameraBtn').addEventListener('click', function() {
            startCamera();
        });

        document.getElementById('stopCameraBtn').addEventListener('click', function() {
            stopCamera();
        });

        // Automatically start camera if available
        startCamera();
    });

    function showCameraAlert(msg) {
        const banner = document.getElementById('cameraAlertBanner');
        const errDetail = document.getElementById('cameraErrorMessage');
        if (banner) {
            banner.classList.remove('d-none');
            if (errDetail && msg) {
                errDetail.innerText = msg;
            }
        }
    }

    function hideCameraAlert() {
        const banner = document.getElementById('cameraAlertBanner');
        if (banner) {
            banner.classList.add('d-none');
        }
    }

    function startCamera() {
        hideCameraAlert();
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length > 0) {
                availableDevices = devices;
                // Prefer environment (back) camera if available
                if (currentDeviceIndex >= devices.length) {
                    currentDeviceIndex = 0;
                }
                const cameraId = devices[currentDeviceIndex].id;

                html5QrCode.start(
                    cameraId,
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    (decodedText, decodedResult) => {
                        processScanCode(decodedText);
                    },
                    (errorMessage) => {
                        // ignore normal frame-by-frame decoding misses
                    }
                ).then(() => {
                    isScanning = true;
                    document.getElementById('startCameraBtn').style.display = 'none';
                    document.getElementById('stopCameraBtn').style.display = 'inline-flex';

                    // Show switch camera if more than 1 camera
                    if (availableDevices.length > 1) {
                        document.getElementById('switchCameraBtn').style.display = 'inline-flex';
                    }

                    // Check and show torch button if supported
                    try {
                        const track = html5QrCode.getRunningTrackCameraCapabilities();
                        if (track && track.torchFeature().isSupported()) {
                            document.getElementById('torchBtn').style.display = 'inline-flex';
                        }
                    } catch (e) {
                        // Fallback show torch button anyway for modern mobile browsers
                        document.getElementById('torchBtn').style.display = 'inline-flex';
                    }
                }).catch(err => {
                    console.warn("Camera start error:", err);
                    isScanning = false;
                    showCameraAlert(err.message || "<?= lang('Aset.camera_error_denied') ?>");
                    document.getElementById('startCameraBtn').style.display = 'inline-flex';
                    document.getElementById('stopCameraBtn').style.display = 'none';
                    document.getElementById('torchBtn').style.display = 'none';
                    document.getElementById('switchCameraBtn').style.display = 'none';
                });
            } else {
                showCameraAlert("Tidak ada perangkat kamera yang terdeteksi di perangkat Anda.");
            }
        }).catch(err => {
            console.warn("No cameras detected / Permission denied:", err);
            showCameraAlert(err.name === 'NotAllowedError' 
                ? "Izin akses kamera ditolak. Silakan berikan izin di pengaturan peramban Anda." 
                : (err.message || "Kamera tidak dapat diakses."));
        });
    }

    function stopCamera() {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => {
                isScanning = false;
                isTorchActive = false;
                document.getElementById('startCameraBtn').style.display = 'inline-flex';
                document.getElementById('stopCameraBtn').style.display = 'none';
                document.getElementById('torchBtn').style.display = 'none';
                document.getElementById('switchCameraBtn').style.display = 'none';
            }).catch(err => {
                console.warn("Stop camera error:", err);
            });
        }
    }

    function toggleTorch() {
        if (html5QrCode && isScanning) {
            isTorchActive = !isTorchActive;
            try {
                html5QrCode.applyVideoConstraints({
                    advanced: [{ torch: isTorchActive }]
                }).then(() => {
                    const btn = document.getElementById('torchBtn');
                    if (btn) {
                        btn.className = isTorchActive ? 'btn btn-warning btn-mobile-touch text-dark fw-bold' : 'btn btn-outline-warning btn-mobile-touch text-dark';
                        btn.innerHTML = isTorchActive ? '<i class="bi bi-lightbulb-fill me-1"></i> Senter ON' : '<i class="bi bi-lightbulb me-1"></i> <?= lang('Aset.torch_toggle') ?>';
                    }
                }).catch(e => {
                    console.warn("Torch constraint not supported:", e);
                });
            } catch (e) {
                console.warn("Torch failed:", e);
            }
        }
    }

    function switchCamera() {
        if (availableDevices.length > 1) {
            currentDeviceIndex = (currentDeviceIndex + 1) % availableDevices.length;
            stopCamera();
            setTimeout(() => {
                startCamera();
            }, 300);
        }
    }

    function submitManualCode() {
        const input = document.getElementById('manualAssetCodeInput');
        if (!input) return;
        const code = input.value.trim().toUpperCase();
        if (!code) {
            alert("<?= current_locale() === 'en' ? 'Please enter a valid Asset Code (e.g., AST-ENG-001).' : 'Silakan masukkan Kode Aset yang valid (contoh: AST-ENG-001).' ?>");
            input.focus();
            return;
        }
        processScanCode(code);
    }

    function processScanCode(code) {
        // Fetch asset from API endpoint
        fetch(`<?= base_url('scan/lookup') ?>?code=${encodeURIComponent(code)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const ast = data.asset;
                    document.getElementById('scannedKodeAset').innerText = ast.kode_aset;
                    document.getElementById('scannedNamaAset').innerText = ast.nama_aset;
                    document.getElementById('scannedLokasi').innerText = (<?= current_locale() === 'en' ? "'Location: '" : "'Lokasi: '" ?>) + ast.lokasi_spesifik;
                    document.getElementById('scannedDept').innerText = ast.nama_departemen;
                    document.getElementById('scannedKondisi').innerText = ast.kondisi;

                    document.getElementById('scannedReportBtn').href = data.reportUrl;
                    document.getElementById('scannedDetailBtn').href = data.detailUrl;

                    assetModal.show();
                } else {
                    alert(data.message || "<?= current_locale() === 'en' ? 'Asset not found with code: ' : 'Aset tidak ditemukan dengan kode: ' ?>" + code);
                }
            })
            .catch(err => {
                console.error(err);
                alert("<?= current_locale() === 'en' ? 'Failed to process QR Code. Please try again.' : 'Gagal memproses QR Code. Silakan coba kembali.' ?>");
            });
    }
</script>
<?= $this->endSection() ?>
