<!-- Interactive Onboarding Modal for New Users / Field Staff -->
<div class="modal fade" id="onboardingModal" tabindex="-1" aria-labelledby="onboardingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header with Gradient & Close Button -->
            <div class="modal-header bg-dark text-white border-0 py-3 px-4 position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2 py-1 fw-bold"><i class="bi bi-compass-fill me-1"></i> Interactive Guide</span>
                    <h5 class="modal-title fw-bold mb-0 text-white fs-6" id="onboardingModalLabel">
                        <?= current_locale() === 'en' ? 'Quick Field Operations Guide V2.0' : 'Panduan Ringkas Operasional Lapangan V2.0' ?>
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body with Interactive Carousel / Steps -->
            <div class="modal-body p-4 p-md-5">
                <!-- Step Navigation Indicators -->
                <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                    <button type="button" class="btn btn-sm rounded-pill px-3 onboarding-step-btn active bg-warning text-dark fw-bold" onclick="goToOnboardingStep(0)">1. <?= current_locale() === 'en' ? 'Overview' : 'Alur WO' ?></button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 onboarding-step-btn btn-outline-secondary" onclick="goToOnboardingStep(1)">2. <?= current_locale() === 'en' ? 'QR Scanner' : 'Scan QR' ?></button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 onboarding-step-btn btn-outline-secondary" onclick="goToOnboardingStep(2)">3. <?= current_locale() === 'en' ? 'SLA & Parts' : 'SLA & Sparepart' ?></button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 onboarding-step-btn btn-outline-secondary" onclick="goToOnboardingStep(3)">4. <?= current_locale() === 'en' ? 'Offline Mode' : 'Mode Offline' ?></button>
                </div>

                <!-- Slide 1: System Workflow -->
                <div class="onboarding-slide text-center" id="onboardingSlide0">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 76px; height: 76px;">
                            <i class="bi bi-diagram-3-fill fs-2"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2"><?= current_locale() === 'en' ? 'Streamlined 3-Step Service Workflow' : 'Alur Cepat Pengerjaan Work Order (3 Langkah Ringkas)' ?></h5>
                    <p class="text-muted small mb-4" style="max-width: 520px; margin: 0 auto;">
                        <?= current_locale() === 'en' 
                            ? 'Golden Hill Hotel Work Order System minimizes field clicks from task assignment to resolution.' 
                            : 'Dirancang khusus agar staf lapangan dan teknisi hotel dapat mengeksekusi tugas secara cepat tanpa birokrasi berbelit.' ?>
                    </p>
                    <div class="row g-3 text-start small">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <div class="fw-bold text-dark mb-1"><span class="badge bg-warning text-dark me-1">1</span> <?= current_locale() === 'en' ? 'Accept / Report' : 'Ajukan / Verifikasi' ?></div>
                                <span class="text-muted"><?= current_locale() === 'en' ? 'Staff submits ticket via scan or manual, Kadep verifies & assigns in 1 click.' : 'Staf melapor via QR aset, Kepala Departemen menyetujui & menugaskan staf.' ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <div class="fw-bold text-dark mb-1"><span class="badge bg-primary me-1">2</span> <?= current_locale() === 'en' ? 'Start Progress' : 'Mulai Pengerjaan' ?></div>
                                <span class="text-muted"><?= current_locale() === 'en' ? '1-touch status change on mobile screen to Start Progress.' : 'Tekan tombol 1-klik di layar HP untuk memulai status "Dalam Proses".' ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <div class="fw-bold text-dark mb-1"><span class="badge bg-success me-1">3</span> <?= current_locale() === 'en' ? 'Resolve & Finish' : 'Selesaikan & Bukti' ?></div>
                                <span class="text-muted"><?= current_locale() === 'en' ? 'Upload photo proof and record spare parts deducted directly from inventory.' : 'Unggah foto bukti selesai dan catat sparepart dengan pemotongan stok otomatis.' ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: QR Scanner & Fallbacks -->
                <div class="onboarding-slide text-center d-none" id="onboardingSlide1">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle" style="width: 76px; height: 76px;">
                            <i class="bi bi-qr-code-scan fs-2"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2"><?= current_locale() === 'en' ? 'Instant Asset QR Scanning & Dark Mode Torch' : 'Pemindaian QR Code Aset & Fitur Senter Ruang Gelap' ?></h5>
                    <p class="text-muted small mb-4" style="max-width: 520px; margin: 0 auto;">
                        <?= current_locale() === 'en'
                            ? 'Every hotel asset has a distinct QR label. Scan to auto-fill ticket details. In boiler rooms or basements, toggle the flashlight or use manual asset input.'
                            : 'Setiap aset hotel dilengkapi stiker QR Code. Pindai untuk otomatis mengunci lokasi & departemen. Jika di ruang mesin yang gelap, nyalakan tombol senter atau gunakan input manual.' ?>
                    </p>
                    <div class="row g-2 text-start small">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <i class="bi bi-lightbulb text-warning fs-4 d-block mb-1"></i>
                                <strong><?= current_locale() === 'en' ? 'Flashlight / Torch' : 'Lampu Senter' ?></strong>
                                <p class="text-muted mb-0 mt-1"><?= current_locale() === 'en' ? 'Active on supported mobile devices for low-light scans.' : 'Dapat diaktifkan langsung dari layar scanner tanpa aplikasi tambahan.' ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <i class="bi bi-keyboard text-primary fs-4 d-block mb-1"></i>
                                <strong><?= current_locale() === 'en' ? 'Direct Code Fallback' : 'Input Manual Cadangan' ?></strong>
                                <p class="text-muted mb-0 mt-1"><?= current_locale() === 'en' ? 'Enter asset code or select from searchable list if camera is unavailable.' : 'Ketik kode aset atau pilih dari daftar jika izin kamera ditolak peramban.' ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: SLA Countdown & Stock Safety -->
                <div class="onboarding-slide text-center d-none" id="onboardingSlide2">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle" style="width: 76px; height: 76px;">
                            <i class="bi bi-stopwatch fs-2"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2"><?= current_locale() === 'en' ? 'Live SLA Countdown & Stock Safety' : 'Hitung Mundur SLA & Perlindungan Stok Inventaris' ?></h5>
                    <p class="text-muted small mb-4" style="max-width: 520px; margin: 0 auto;">
                        <?= current_locale() === 'en'
                            ? 'Monitor countdown badges on your assigned tasks. High-concurrency pessimistic locking protects spare parts from race condition deductions.'
                            : 'Pantau badge hitung mundur SLA agar tugas tidak terlewat. Saat menggunakan suku cadang, sistem mengunci baris inventaris secara aman agar stok tidak minus.' ?>
                    </p>
                    <div class="p-3 bg-light rounded-3 text-start small border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span><span class="badge bg-danger">Emergency</span> <?= current_locale() === 'en' ? '1 Hour SLA Target' : 'Target SLA 1 Jam' ?></span>
                            <span><span class="badge bg-warning text-dark">High</span> <?= current_locale() === 'en' ? '4 Hours SLA Target' : 'Target SLA 4 Jam' ?></span>
                        </div>
                        <div class="text-muted">
                            <?= current_locale() === 'en'
                                ? 'Auto-escalation worker automatically warns Department Heads via WhatsApp / Email failover when deadlines pass.'
                                : 'Jika terlewat dari target jam, sistem otomatis mengeskalasi tiket dan mengirimkan peringatan darurat ke Kepala Departemen.' ?>
                        </div>
                    </div>
                </div>

                <!-- Slide 4: Offline Mode in Basements -->
                <div class="onboarding-slide text-center d-none" id="onboardingSlide3">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style="width: 76px; height: 76px;">
                            <i class="bi bi-wifi-off fs-2"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2"><?= current_locale() === 'en' ? 'Basement Blindspot & Offline Auto-Sync' : 'Mitigasi Blindspot Sinyal (Offline Auto-Sync)' ?></h5>
                    <p class="text-muted small mb-4" style="max-width: 520px; margin: 0 auto;">
                        <?= current_locale() === 'en'
                            ? 'When working in basements or elevator shafts without Wi-Fi/4G, tickets are saved safely to local storage and automatically sync upon returning to signal.'
                            : 'Ketika bekerja di ruang mesin genset atau basement tanpa sinyal, formulir tetap dapat diisi dan disimpan secara lokal. Begitu kembali terhubung, sistem otomatis mengunggahnya.' ?>
                    </p>
                    <div class="alert alert-info border-info border-opacity-25 p-3 rounded-3 text-start small mb-0">
                        <i class="bi bi-cloud-arrow-up-fill text-info me-2 fs-5"></i>
                        <strong><?= current_locale() === 'en' ? 'Zero Data Loss Guarantee' : 'Jaminan Tanpa Kehilangan Data' ?>:</strong>
                        <?= current_locale() === 'en'
                            ? 'The bottom connectivity banner informs you of offline status and confirms successful background sync.'
                            : 'Banner status di pojok layar akan memberitahukan saat perangkat offline dan mengonfirmasi ketika sinkronisasi berhasil.' ?>
                    </div>
                </div>
            </div>

            <!-- Footer with Don't Show Again & Nav Buttons -->
            <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="form-check small">
                    <input class="form-check-input" type="checkbox" id="dontShowOnboardingCheck">
                    <label class="form-check-label text-muted" for="dontShowOnboardingCheck">
                        <?= current_locale() === 'en' ? "Don't show this guide again automatically" : 'Jangan tampilkan panduan ini lagi secara otomatis' ?>
                    </label>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="prevOnboardingBtn" onclick="prevOnboardingStep()" style="display: none;">
                        <i class="bi bi-chevron-left me-1"></i> <?= current_locale() === 'en' ? 'Back' : 'Sebelumnya' ?>
                    </button>
                    <button type="button" class="btn btn-gold btn-sm rounded-pill px-4 fw-bold" id="nextOnboardingBtn" onclick="nextOnboardingStep()">
                        <?= current_locale() === 'en' ? 'Next' : 'Selanjutnya' ?> <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-4 fw-bold" id="finishOnboardingBtn" onclick="finishOnboarding()" style="display: none;">
                        <i class="bi bi-check-lg me-1"></i> <?= current_locale() === 'en' ? 'Got It!' : 'Saya Mengerti!' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentOnboardingStep = 0;
    const totalOnboardingSteps = 4;

    function goToOnboardingStep(step) {
        currentOnboardingStep = step;
        for (let i = 0; i < totalOnboardingSteps; i++) {
            const slide = document.getElementById('onboardingSlide' + i);
            if (slide) {
                if (i === step) {
                    slide.classList.remove('d-none');
                } else {
                    slide.classList.add('d-none');
                }
            }
        }

        // Update nav buttons
        const stepBtns = document.querySelectorAll('.onboarding-step-btn');
        stepBtns.forEach((btn, idx) => {
            if (idx === step) {
                btn.className = 'btn btn-sm rounded-pill px-3 onboarding-step-btn active bg-warning text-dark fw-bold';
            } else {
                btn.className = 'btn btn-sm rounded-pill px-3 onboarding-step-btn btn-outline-secondary';
            }
        });

        const prevBtn = document.getElementById('prevOnboardingBtn');
        const nextBtn = document.getElementById('nextOnboardingBtn');
        const finishBtn = document.getElementById('finishOnboardingBtn');

        if (prevBtn) prevBtn.style.display = step > 0 ? 'inline-block' : 'none';
        if (nextBtn) nextBtn.style.display = step < totalOnboardingSteps - 1 ? 'inline-block' : 'none';
        if (finishBtn) finishBtn.style.display = step === totalOnboardingSteps - 1 ? 'inline-block' : 'none';
    }

    function nextOnboardingStep() {
        if (currentOnboardingStep < totalOnboardingSteps - 1) {
            goToOnboardingStep(currentOnboardingStep + 1);
        }
    }

    function prevOnboardingStep() {
        if (currentOnboardingStep > 0) {
            goToOnboardingStep(currentOnboardingStep - 1);
        }
    }

    function finishOnboarding() {
        const check = document.getElementById('dontShowOnboardingCheck');
        if (check && check.checked) {
            localStorage.setItem('ghh_dismiss_onboarding', 'true');
        }
        const modalEl = document.getElementById('onboardingModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    window.openOnboardingModal = function(force = false) {
        if (!force && localStorage.getItem('ghh_dismiss_onboarding') === 'true') {
            return;
        }
        const modalEl = document.getElementById('onboardingModal');
        if (modalEl) {
            goToOnboardingStep(0);
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    };

    // Auto-open on initial load if not dismissed
    document.addEventListener("DOMContentLoaded", function() {
        if (localStorage.getItem('ghh_dismiss_onboarding') !== 'true') {
            setTimeout(function() {
                openOnboardingModal(false);
            }, 1200);
        }
    });
</script>
