<!DOCTYPE html>
<html lang="<?= current_locale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="theme-color" content="#0f172a">
    <title><?= esc($title ?? lang('Navigation.dashboard')) ?> - <?= lang('Navigation.app_name') ?> | <?= lang('Navigation.hotel_name') ?></title>
    
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">

    <style>
        /* Mobile Field Staff Touch Target Optimization (Specification 6.1: Minimum 48px) */
        .btn-mobile-touch, .btn-touch-48 { min-height: 48px !important; min-width: 48px !important; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="brand-box">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="brand-badge">Golden Tulip</span>
                <span class="badge bg-dark text-white-50 border border-secondary" style="font-size: 0.65rem;">V2.0</span>
            </div>
            <h5 class="fw-bold mb-0 text-white"><?= lang('Navigation.hotel_name') ?></h5>
            <small class="text-white-50" style="font-size: 0.75rem;"><?= lang('Navigation.location') ?></small>
        </div>

        <ul class="sidebar-nav">
            <li class="nav-section-title"><?= lang('Navigation.main_menu') ?></li>
            <li>
                <a href="<?= base_url('dashboard') ?>" class="nav-link <?= (url_is('dashboard*') || current_url() == base_url('/')) ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span><?= lang('Navigation.dashboard') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('workorder') ?>" class="nav-link <?= url_is('workorder*') && !url_is('workorder/monitoring*') ? 'active' : '' ?>">
                    <i class="bi bi-tools"></i>
                    <span><?= lang('Navigation.work_orders') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('scan') ?>" class="nav-link <?= url_is('scan*') ? 'active' : '' ?>">
                    <i class="bi bi-qr-code-scan"></i>
                    <span><?= lang('Navigation.scan_qr') ?></span>
                </a>
            </li>

            <li class="nav-section-title"><?= lang('Navigation.assets_inventory') ?></li>
            <li>
                <a href="<?= base_url('aset') ?>" class="nav-link <?= url_is('aset*') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    <span><?= lang('Navigation.asset_management') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('inventaris') ?>" class="nav-link <?= url_is('inventaris*') ? 'active' : '' ?>">
                    <i class="bi bi-archive"></i>
                    <span><?= lang('Navigation.spare_parts') ?></span>
                </a>
            </li>

            <?php 
                $userRole = session()->get('nama_role');
                $roleId = (int) session()->get('id_role');
            ?>

            <?php if ($roleId === 1 || $roleId === 2): // Admin or Kadep ?>
            <li class="nav-section-title"><?= lang('Navigation.supervisory') ?></li>
            <li>
                <a href="<?= base_url('workorder/monitoring') ?>" class="nav-link <?= url_is('workorder/monitoring*') ? 'active' : '' ?>">
                    <i class="bi bi-clock-history"></i>
                    <span><?= lang('Navigation.sla_monitoring') ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($roleId === 1): // Admin only ?>
            <li class="nav-section-title"><?= lang('Navigation.administrator') ?></li>
            <li>
                <a href="<?= base_url('users') ?>" class="nav-link <?= url_is('users*') ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span><?= lang('Navigation.user_management') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('departemen') ?>" class="nav-link <?= url_is('departemen*') ? 'active' : '' ?>">
                    <i class="bi bi-diagram-3"></i>
                    <span><?= lang('Navigation.departments') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('audit') ?>" class="nav-link <?= url_is('audit*') ? 'active' : '' ?>">
                    <i class="bi bi-shield-check"></i>
                    <span><?= lang('Navigation.audit_trail') ?></span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- User Info & Role Switcher in Sidebar -->
        <div class="user-card-sidebar">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                    <?= strtoupper(substr(session()->get('nama_lengkap') ?? 'U', 0, 1)) ?>
                </div>
                <div class="overflow-hidden">
                    <div class="text-white text-truncate fw-semibold" style="font-size: 0.85rem;"><?= esc(session()->get('nama_lengkap')) ?></div>
                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;"><?= esc(session()->get('nama_role')) ?></span>
                    <span class="text-white-50" style="font-size: 0.7rem;">• <?= esc(session()->get('kode_dept') ?? 'ALL') ?></span>
                </div>
            </div>

            <!-- Quick Role Switcher for Test/Demo -->
            <button type="button" class="btn btn-sm btn-outline-light w-100 py-1" style="font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#switchRoleModal">
                <?= lang('Navigation.switch_role_demo') ?>
            </button>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div id="content-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" id="sidebarToggle" aria-label="Menu" title="Menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-sm-block">
                    <h6 class="mb-0 fw-bold text-dark"><?= esc($title ?? lang('Navigation.dashboard')) ?></h6>
                    <small class="text-muted" style="font-size: 0.75rem;"><?= lang('Navigation.hotel_name') ?> • <?= lang('Navigation.app_name') ?></small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Language Switcher Dropdown (Specification 3.1) -->
                <?php $currentLocale = current_locale(); ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle rounded-pill px-3 py-1 fw-semibold small" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="<?= lang('Navigation.language') ?>">
                        <?= $currentLocale === 'en' ? 'EN' : 'ID' ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size: 0.85rem;">
                        <li class="dropdown-header small text-muted fw-bold"><?= lang('Navigation.language') ?></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 <?= $currentLocale === 'id' ? 'active fw-bold' : '' ?>" href="<?= base_url('lang/id') ?>">
                                <span style="font-size: 1.15rem;">🇮🇩</span>
                                <div>
                                    <div>Bahasa Indonesia</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Default (ID)</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 <?= $currentLocale === 'en' ? 'active fw-bold' : '' ?>" href="<?= base_url('lang/en') ?>">
                                <span style="font-size: 1.15rem;">🇬🇧</span>
                                <div>
                                    <div>English</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">International (EN)</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons & Utilities -->
                <a href="<?= base_url('scan') ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <?= lang('Navigation.quick_scan') ?>
                </a>
                <button type="button" class="btn btn-sm btn-link text-secondary text-decoration-none px-2" onclick="openOnboardingModal(true)" title="<?= current_locale() === 'en' ? 'Interactive Operations Guide' : 'Panduan Operasional Interaktif' ?>">
                    <?= current_locale() === 'en' ? 'Guide' : 'Panduan' ?>
                </button>

                <!-- SLA Overdue Check Notification -->
                <?php 
                    $overdueCount = (new \App\Models\WorkOrderModel())->where('id_status', 8)->countAllResults();
                ?>
                <a href="<?= base_url('workorder?status=8') ?>" class="btn btn-sm btn-light border position-relative rounded-pill px-2" title="Work Order Overdue / Escalated" aria-label="Work Order Overdue / Escalated">
                    <i class="bi bi-bell text-secondary"></i>
                    <?php if ($overdueCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                        <?= $overdueCount ?>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- User Dropdown -->
                <div class="dropdown ms-1">
                    <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2 rounded-pill ps-1 pe-3 py-1" type="button" data-bs-toggle="dropdown">
                        <span class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center fw-semibold" style="width: 26px; height: 26px; font-size: 0.75rem;"><?= strtoupper(substr(session()->get('username') ?? 'U', 0, 1)) ?></span>
                        <span class="d-none d-md-inline" style="font-size: 0.85rem;"><?= esc(session()->get('username')) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li class="dropdown-header">
                            <span class="fw-bold"><?= esc(session()->get('nama_lengkap')) ?></span><br>
                            <small class="text-muted"><?= esc(session()->get('email')) ?></small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('workorder?user=me') ?>">
                                <?= lang('Navigation.my_wo') ?>
                            </a>
                        </li>
                        <li>
                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#switchRoleModal">
                                <?= lang('Navigation.switch_role') ?>
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                <?= lang('Navigation.logout') ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="p-3 p-md-4 flex-grow-1">
            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <?= session()->getFlashdata('warning') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-muted small text-center text-md-start d-md-flex justify-content-between align-items-center">
            <div>
                <strong><?= lang('Navigation.hotel_name') ?> by Golden Tulip Batu</strong> • <?= lang('Navigation.app_name') ?>
            </div>
            <div class="mt-1 mt-md-0">
                <?= lang('Navigation.thesis_by') ?>
            </div>
        </footer>
    </div>

    <!-- Modal Role Switcher -->
    <div class="modal fade" id="switchRoleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fs-6"><?= lang('Navigation.switch_role_demo') ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('auth/switch-role') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <p class="small text-muted mb-3"><?= lang('Navigation.switch_role_desc') ?></p>
                        
                        <div class="list-group">
                            <label class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold">1. Administrator</div>
                                    <small class="text-muted">Username: admin (Full Control, Master Data, Executive KPI)</small>
                                </div>
                                <input class="form-check-input me-1" type="radio" name="switch_user_id" value="1" <?= session()->get('user_id') == 1 ? 'checked' : '' ?>>
                            </label>
                            <label class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold">2. Hizkia Tegar Kurniawan (Admin)</div>
                                    <small class="text-muted">Username: hizkia (Full Control System)</small>
                                </div>
                                <input class="form-check-input me-1" type="radio" name="switch_user_id" value="2" <?= session()->get('user_id') == 2 ? 'checked' : '' ?>>
                            </label>
                            <label class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold">3. Kepala Departemen Engineering</div>
                                    <small class="text-muted">Username: kadep_eng (Verifikasi, Set SLA, Penugasan Staf)</small>
                                </div>
                                <input class="form-check-input me-1" type="radio" name="switch_user_id" value="3" <?= session()->get('user_id') == 3 ? 'checked' : '' ?>>
                            </label>
                            <label class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold">4. Staf Engineering</div>
                                    <small class="text-muted">Username: staf_eng (Eksekusi WO, Pemotongan Sparepart, Foto Bukti)</small>
                                </div>
                                <input class="form-check-input me-1" type="radio" name="switch_user_id" value="4" <?= session()->get('user_id') == 4 ? 'checked' : '' ?>>
                            </label>
                            <label class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold">5. Staf Housekeeping</div>
                                    <small class="text-muted">Username: staf_hk (Ajukan WO, Scan QR Aset)</small>
                                </div>
                                <input class="form-check-input me-1" type="radio" name="switch_user_id" value="8" <?= session()->get('user_id') == 8 ? 'checked' : '' ?>>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><?= lang('Navigation.cancel') ?></button>
                        <button type="submit" class="btn btn-sm btn-gold"><?= lang('Navigation.apply_role') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Active Locale
        const currentLocale = '<?= current_locale() ?>';

        // Sidebar Toggle for Mobile
        $('#sidebarToggle').on('click', function() {
            $('#sidebar').toggleClass('show');
        });

        // DataTables.net i18n Configuration (Specification 3.3)
        const dtLanguageConfig = currentLocale === 'en' ? {
            search: "<?= lang('General.dt_search') ?>",
            lengthMenu: "<?= lang('General.dt_lengthMenu') ?>",
            info: "<?= lang('General.dt_info') ?>",
            infoEmpty: "<?= lang('General.dt_infoEmpty') ?>",
            zeroRecords: "<?= lang('General.dt_zeroRecords') ?>",
            paginate: {
                first: "<?= lang('General.dt_paginate_first') ?>",
                last: "<?= lang('General.dt_paginate_last') ?>",
                next: "<?= lang('General.dt_paginate_next') ?>",
                previous: "<?= lang('General.dt_paginate_previous') ?>"
            }
        } : {
            search: "<?= lang('General.dt_search') ?>",
            lengthMenu: "<?= lang('General.dt_lengthMenu') ?>",
            info: "<?= lang('General.dt_info') ?>",
            infoEmpty: "<?= lang('General.dt_infoEmpty') ?>",
            zeroRecords: "<?= lang('General.dt_zeroRecords') ?>",
            paginate: {
                first: "<?= lang('General.dt_paginate_first') ?>",
                last: "<?= lang('General.dt_paginate_last') ?>",
                next: "<?= lang('General.dt_paginate_next') ?>",
                previous: "<?= lang('General.dt_paginate_previous') ?>"
            }
        };

        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                language: dtLanguageConfig
            });
        });

        // Intl.DateTimeFormat helper in JS (Specification 3.3)
        window.formatDateLocale = function(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr;
            return new Intl.DateTimeFormat(currentLocale === 'en' ? 'en-US' : 'id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        };

        // SweetAlert2 Confirmation Helper
        window.confirmAction = function(message, onConfirm) {
            Swal.fire({
                title: "<?= lang('General.confirm_title') ?>",
                text: message || "<?= lang('General.confirm_delete') ?>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#64748b",
                confirmButtonText: "<?= lang('General.yes') ?>",
                cancelButtonText: "<?= lang('General.cancel') ?>"
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
            });
        };

        $(document).on('submit', '.js-confirm-delete', function(e) {
            const form = this;
            if (form.dataset.confirmed === '1') return true;
            e.preventDefault();
            window.confirmAction(form.dataset.confirm, function() {
                form.dataset.confirmed = '1';
                form.submit();
            });
        });
    </script>

    <!-- Interactive Onboarding Modal -->
    <?= $this->include('partials/onboarding_modal') ?>

    <!-- PWA Offline & Background Sync Manager -->
    <script src="<?= base_url('js/offline-manager.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
