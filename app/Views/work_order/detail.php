<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header & Back Button -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0 text-dark"><?= esc($wo['kode_wo']) ?></h4>
            <?php
                $sClass = match($wo['nama_status']) {
                    'Baru' => 'bg-primary',
                    'Accepted', 'Ditugaskan' => 'bg-info text-dark',
                    'Proses' => 'bg-warning text-dark',
                    'Selesai' => 'bg-success',
                    'Ditolak' => 'bg-danger',
                    'Escalated' => 'bg-danger animate-pulse',
                    default => 'bg-secondary',
                };
            ?>
            <span class="badge <?= $sClass ?> px-3 py-1"><?= translate_status($wo['nama_status']) ?></span>
            <span class="badge bg-light text-dark border"><?= translate_category($wo['nama_kategori']) ?></span>
        </div>
        <small class="text-muted"><?= current_locale() === 'en' ? 'Submitted on ' : 'Diajukan pada ' ?><?= date('d F Y, H:i', strtotime($wo['tanggal_ajuan'])) ?><?= current_locale() === 'en' ? ' by ' : ' oleh ' ?><strong><?= esc($wo['nama_pengaju']) ?></strong></small>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('workorder/export-pdf/' . $wo['id_wo']) ?>" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3">
            <i class="bi bi-file-earmark-pdf me-1"></i> <?= lang('WorkOrder.btn_print_pdf') ?>
        </a>
        <a href="<?= base_url('workorder') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> <?= current_locale() === 'en' ? 'Back' : 'Kembali' ?>
        </a>
    </div>
</div>

<!-- SLA Countdown Banner -->
<?php $sla = $wo['sla_info']; ?>
<div class="card card-custom p-3 mb-4 border-start border-4 <?= $sla['status'] === 'overdue' ? 'border-danger bg-danger bg-opacity-10' : ($sla['status'] === 'warning' ? 'border-warning bg-warning bg-opacity-10' : 'border-success bg-white') ?>">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-2 <?= $sla['status'] === 'overdue' ? 'text-danger' : ($sla['status'] === 'warning' ? 'text-warning' : 'text-success') ?>">
                <i class="bi bi-stopwatch-fill"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark"><?= lang('WorkOrder.sla_indicator') ?></span>
                    <span class="badge <?= $sla['badge_class'] ?> fs-6"><?= $sla['badge_text'] ?></span>
                </div>
                <small class="text-muted">
                    <?= lang('WorkOrder.col_priority') ?>: <strong><?= translate_priority($wo['tingkat_prioritas']) ?> (<?= lang('WorkOrder.sla_hours', [$wo['target_jam']]) ?>)</strong>
                    <?php if ($wo['deadline']): ?>
                        • <?= current_locale() === 'en' ? 'Deadline: ' : 'Batas Waktu: ' ?><strong><?= date('d M Y, H:i', strtotime($wo['deadline'])) ?> WIB</strong>
                    <?php endif; ?>
                </small>
            </div>
        </div>

        <?php if ($wo['nama_status'] === 'Escalated'): ?>
            <span class="badge bg-danger p-2 fs-6 animate-pulse">
                <i class="bi bi-bell-fill me-1"></i> <?= lang('WorkOrder.overdue_escalated_msg') ?>
            </span>
        <?php endif; ?>
    </div>
</div>

<!-- Main Details Grid -->
<div class="row g-4">
    <!-- Left Column: Problem Details & Spareparts -->
    <div class="col-lg-8">
        <!-- Problem Detail Card -->
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3"><?= esc($wo['judul_wo']) ?></h5>
            
            <label class="small fw-semibold text-muted text-uppercase mb-1"><?= lang('WorkOrder.section_problem_desc') ?></label>
            <div class="p-3 bg-light rounded-3 text-secondary mb-4" style="white-space: pre-line; line-height: 1.6;">
                <?= esc($wo['deskripsi']) ?>
            </div>

            <?php if (!empty($wo['reject_note'])): ?>
                <div class="alert alert-danger border-danger p-3 rounded-3 mb-4">
                    <div class="fw-bold"><i class="bi bi-x-circle-fill me-1"></i> <?= lang('WorkOrder.section_rejection') ?></div>
                    <div class="small mt-1"><?= esc($wo['reject_note']) ?></div>
                    <div class="text-muted small mt-2" style="font-size: 0.72rem;"><?= lang('WorkOrder.section_verified_by', [esc($wo['nama_kadep'] ?? (current_locale() === 'en' ? 'Department Head' : 'Kepala Departemen')), date('d/m/Y H:i', strtotime($wo['verified_at']))]) ?></div>
                </div>
            <?php endif; ?>

            <!-- Photos Section -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small fw-semibold text-muted text-uppercase mb-2 d-block"><?= lang('WorkOrder.photo_trouble') ?></label>
                    <?php if (!empty($wo['foto_trouble'])): ?>
                        <div class="border rounded-3 p-2 bg-light text-center">
                            <a href="<?= base_url($wo['foto_trouble']) ?>" target="_blank">
                                <img src="<?= base_url($wo['foto_trouble']) ?>" class="img-fluid rounded" style="max-height: 220px; object-fit: cover;" alt="Foto Trouble">
                            </a>
                            <div class="small text-muted mt-1"><i class="bi bi-zoom-in"></i> <?= lang('WorkOrder.photo_click_zoom') ?></div>
                        </div>
                    <?php else: ?>
                        <div class="border rounded-3 p-4 bg-light text-center text-muted small">
                            <i class="bi bi-image fs-3 d-block mb-1"></i> <?= lang('WorkOrder.photo_not_attached') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="small fw-semibold text-muted text-uppercase mb-2 d-block"><?= lang('WorkOrder.photo_finished') ?></label>
                    <?php if (!empty($wo['foto_bukti_selesai'])): ?>
                        <div class="border border-success rounded-3 p-2 bg-success bg-opacity-10 text-center">
                            <a href="<?= base_url($wo['foto_bukti_selesai']) ?>" target="_blank">
                                <img src="<?= base_url($wo['foto_bukti_selesai']) ?>" class="img-fluid rounded" style="max-height: 220px; object-fit: cover;" alt="Foto Bukti Selesai">
                            </a>
                            <div class="small text-success fw-semibold mt-1"><i class="bi bi-check-circle"></i> <?= lang('WorkOrder.verified_complete') ?></div>
                        </div>
                    <?php else: ?>
                        <div class="border rounded-3 p-4 bg-light text-center text-muted small">
                            <i class="bi bi-hourglass-split fs-3 d-block mb-1"></i> <?= lang('WorkOrder.photo_not_finished') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Suku Cadang Digunakan Card -->
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-archive-fill text-warning me-2"></i><?= lang('WorkOrder.section_spareparts') ?></h6>
                    <small class="text-muted"><?= lang('WorkOrder.spareparts_desc') ?></small>
                </div>
                <span class="badge bg-light text-dark border">
                    Total: Rp <?= number_format($wo['total_biaya_sparepart'], 0, ',', '.') ?>
                </span>
            </div>

            <?php if (empty($sparepartsUsed)): ?>
                <div class="text-center py-4 text-muted bg-light rounded-3 small">
                    <i class="bi bi-box2 fs-3 d-block mb-1"></i>
                    <?= lang('WorkOrder.spareparts_empty') ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th><?= current_locale() === 'en' ? 'Item Code' : 'Kode Barang' ?></th>
                                <th><?= lang('WorkOrder.part_item_name') ?></th>
                                <th class="text-center"><?= lang('WorkOrder.part_qty') ?></th>
                                <th class="text-end"><?= lang('WorkOrder.part_price') ?></th>
                                <th class="text-end"><?= lang('WorkOrder.part_subtotal') ?></th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php foreach ($sparepartsUsed as $sp): ?>
                                <tr>
                                    <td><span class="badge bg-dark"><?= esc($sp['kode_barang']) ?></span></td>
                                    <td class="fw-semibold"><?= esc($sp['nama_barang']) ?></td>
                                    <td class="text-center"><?= $sp['jumlah'] ?> <?= esc($sp['satuan']) ?></td>
                                    <td class="text-end">Rp <?= number_format($sp['harga_satuan'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold text-success">Rp <?= number_format($sp['harga_subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold small">
                            <tr>
                                <td colspan="4" class="text-end"><?= lang('WorkOrder.part_total_material') ?></td>
                                <td class="text-end text-success">Rp <?= number_format($wo['total_biaya_sparepart'], 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Meta Info & Actions -->
    <div class="col-lg-4">
        <!-- Operational Details Card -->
        <div class="card card-custom p-3 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-info-circle me-1 text-primary"></i> <?= lang('WorkOrder.operational_details') ?></h6>

            <table class="table table-sm table-borderless small mb-0">
                <tr>
                    <td class="text-muted" style="width: 40%;"><?= lang('WorkOrder.col_dept') ?>:</td>
                    <td class="fw-bold"><?= esc($wo['nama_departemen']) ?> (<?= esc($wo['kode_dept']) ?>)</td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('WorkOrder.col_priority') ?>:</td>
                    <td>
                        <span class="badge bg-warning text-dark"><?= translate_priority($wo['tingkat_prioritas']) ?> (<?= lang('WorkOrder.sla_hours', [$wo['target_jam']]) ?>)</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('WorkOrder.target_completion') ?></td>
                    <td class="fw-semibold"><?= $wo['deadline'] ? date('d/m/Y H:i', strtotime($wo['deadline'])) : '-' ?></td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('WorkOrder.duration_work') ?></td>
                    <td class="fw-bold text-primary"><?= $wo['durasi_menit_pengerjaan'] ? lang('WorkOrder.minutes', [$wo['durasi_menit_pengerjaan']]) : '-' ?></td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('WorkOrder.col_requester') ?></td>
                    <td>
                        <div class="fw-semibold"><?= esc($wo['nama_pengaju']) ?></div>
                        <?php if (!empty($wo['wa_pengaju'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $wo['wa_pengaju']) ?>" target="_blank" class="badge bg-success text-decoration-none">
                                <i class="bi bi-whatsapp me-1"></i> <?= esc($wo['wa_pengaju']) ?>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('WorkOrder.col_handler') ?></td>
                    <td>
                        <?php if (!empty($wo['nama_handler'])): ?>
                            <div class="fw-bold text-dark"><?= esc($wo['nama_handler']) ?></div>
                            <?php if (!empty($wo['wa_handler'])): ?>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $wo['wa_handler']) ?>" target="_blank" class="badge bg-success text-decoration-none">
                                    <i class="bi bi-whatsapp me-1"></i> Chat WA
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted fst-italic"><?= lang('WorkOrder.unassigned') ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('WorkOrder.verified_by_label') ?></td>
                    <td><?= !empty($wo['nama_kadep']) ? esc($wo['nama_kadep']) . ' (' . date('d/m H:i', strtotime($wo['verified_at'])) . ')' : '-' ?></td>
                </tr>
            </table>
        </div>

        <!-- Linked Asset Card -->
        <?php if (!empty($wo['nama_aset'])): ?>
            <div class="card card-custom p-3 mb-4 border-warning">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-box-seam me-1 text-warning"></i> <?= lang('WorkOrder.linked_asset') ?></h6>
                    <span class="badge bg-dark"><?= esc($wo['kode_aset']) ?></span>
                </div>
                <div class="fw-bold text-dark mb-1"><?= esc($wo['nama_aset']) ?></div>
                <small class="text-muted d-block mb-2"><i class="bi bi-geo-alt me-1"></i> <?= current_locale() === 'en' ? 'Location: ' : 'Lokasi: ' ?><?= esc($wo['aset_lokasi']) ?></small>
                <a href="<?= base_url('aset/detail/' . $wo['id_aset']) ?>" class="btn btn-sm btn-outline-dark w-100">
                    <i class="bi bi-clock-history me-1"></i> <?= lang('WorkOrder.view_repair_history') ?>
                </a>
            </div>
        <?php endif; ?>

        <!-- Action Control Center (Role Based) -->
        <?php 
            $roleId = (int) session()->get('id_role');
            $userId = (int) session()->get('user_id');
            $deptId = (int) session()->get('id_departemen');
            $isKadepOrAdmin = ($roleId === 1 || ($roleId === 2 && $deptId === (int)$wo['id_departemen']));
            $isHandlerOrAdmin = ($roleId === 1 || $userId === (int)$wo['assigned_to']);
        ?>

        <div class="card card-custom p-3 bg-light border-0">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-sliders me-1"></i> <?= lang('WorkOrder.mgmt_actions') ?></h6>

            <!-- Kadep / Admin Verification Actions -->
            <?php if ($isKadepOrAdmin && (int)$wo['id_status'] === 1): // Status: Baru ?>
                <div class="d-grid gap-2 mb-3">
                    <form action="<?= base_url('workorder/verify/' . $wo['id_wo']) ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="accept">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i> <?= lang('WorkOrder.btn_accept') ?>
                        </button>
                    </form>

                    <button type="button" class="btn btn-outline-danger w-100 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle me-1"></i> <?= lang('WorkOrder.btn_reject') ?>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Kadep / Admin Assignment Action -->
            <?php if ($isKadepOrAdmin && in_array((int)$wo['id_status'], [1, 2, 4, 8])): ?>
                <div class="d-grid gap-2 mb-2">
                    <button type="button" class="btn btn-primary w-100 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="bi bi-person-plus me-1"></i> <?= !empty($wo['assigned_to']) ? lang('WorkOrder.btn_reassign') : lang('WorkOrder.btn_assign') ?>
                    </button>

                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 py-1" data-bs-toggle="modal" data-bs-target="#changePrioModal">
                        <i class="bi bi-speedometer2 me-1"></i> <?= lang('WorkOrder.btn_adjust_sla') ?>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Staf Handler Actions -->
            <?php if ($isHandlerOrAdmin): ?>
                <?php if (in_array((int)$wo['id_status'], [2, 4])): // Accepted or Ditugaskan ?>
                    <form action="<?= base_url('workorder/start-process/' . $wo['id_wo']) ?>" method="POST" class="mb-2">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-warning btn-mobile-touch w-100 fw-bold text-dark shadow-sm">
                            <i class="bi bi-play-circle-fill me-2"></i> <?= lang('WorkOrder.btn_start_process') ?>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (in_array((int)$wo['id_status'], [4, 5, 8])): // In Progress / Escalated ?>
                    <button type="button" class="btn btn-success btn-mobile-touch w-100 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#finishModal">
                        <i class="bi bi-check2-all me-2"></i> <?= lang('WorkOrder.btn_finish_wo') ?>
                    </button>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ((int)$wo['id_status'] === 6): ?>
                <div class="alert alert-success border-0 mb-0 py-2 text-center small fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> <?= lang('WorkOrder.wo_already_completed') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Mobile Quick Actions Floating Bar (Field Staff Optimization: 1-Click Operations) -->
<?php if ($isHandlerOrAdmin || $isKadepOrAdmin): ?>
    <div class="mobile-quick-actions d-md-none">
        <div class="d-flex gap-2">
            <?php if ($isKadepOrAdmin && (int)$wo['id_status'] === 1): ?>
                <form action="<?= base_url('workorder/verify/' . $wo['id_wo']) ?>" method="POST" class="flex-grow-1">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="accept">
                    <button type="submit" class="btn btn-success btn-mobile-touch w-100 shadow">
                        <i class="bi bi-check-circle me-1"></i> <?= lang('WorkOrder.btn_accept') ?>
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger btn-mobile-touch px-3" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle"></i>
                </button>
            <?php elseif ($isHandlerOrAdmin && in_array((int)$wo['id_status'], [2, 4])): ?>
                <form action="<?= base_url('workorder/start-process/' . $wo['id_wo']) ?>" method="POST" class="w-100">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-warning btn-mobile-touch w-100 fw-bold text-dark shadow">
                        <i class="bi bi-play-circle-fill me-2 fs-5"></i> <?= lang('WorkOrder.btn_start_process') ?> (1-Klik)
                    </button>
                </form>
            <?php elseif ($isHandlerOrAdmin && in_array((int)$wo['id_status'], [5, 8])): ?>
                <button type="button" class="btn btn-success btn-mobile-touch w-100 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#finishModal">
                    <i class="bi bi-check2-all me-2 fs-5"></i> <?= lang('WorkOrder.btn_finish_wo') ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- MODAL 1: Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fs-6"><i class="bi bi-x-circle me-1"></i> <?= lang('WorkOrder.modal_reject_title') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('workorder/verify/' . $wo['id_wo']) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="reject">
                <div class="modal-body">
                    <label class="form-label small fw-semibold"><?= lang('WorkOrder.modal_reject_reason') ?> <span class="text-danger">*</span></label>
                    <textarea name="reject_note" class="form-control" rows="4" placeholder="<?= current_locale() === 'en' ? 'Explain rejection reason (e.g. duplicate submission, outside department scope, or capex form)...' : 'Jelaskan alasan penolakan (misal: duplikasi ajuan, bukan ranah departemen, atau form capex)...' ?>" required></textarea>
                    <div class="form-text small"><?= lang('WorkOrder.modal_reject_help') ?></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><?= current_locale() === 'en' ? 'Cancel' : 'Batal' ?></button>
                    <button type="submit" class="btn btn-sm btn-danger"><?= lang('WorkOrder.btn_confirm_reject') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 2: Assign To Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6"><i class="bi bi-person-check me-1"></i> <?= lang('WorkOrder.modal_assign_title') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('workorder/assign/' . $wo['id_wo']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <label class="form-label small fw-semibold"><?= lang('WorkOrder.modal_assign_staff') ?> (<?= lang('WorkOrder.col_dept') ?>: <?= esc($wo['nama_departemen']) ?>) <span class="text-danger">*</span></label>
                    <select name="assigned_to" class="form-select" required>
                        <option value=""><?= current_locale() === 'en' ? '-- Select Staff Member --' : '-- Pilih Anggota Staf --' ?></option>
                        <?php foreach ($staffMembers as $staf): ?>
                            <option value="<?= $staf['id_user'] ?>" <?= $wo['assigned_to'] == $staf['id_user'] ? 'selected' : '' ?>>
                                <?= esc($staf['nama_lengkap']) ?> (WA: <?= esc($staf['no_whatsapp']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text small text-muted mt-2">
                        <i class="bi bi-whatsapp text-success me-1"></i> <?= lang('WorkOrder.modal_assign_help') ?>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><?= current_locale() === 'en' ? 'Cancel' : 'Batal' ?></button>
                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('WorkOrder.btn_confirm_assign') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 3: Change SLA Priority Modal -->
<div class="modal fade" id="changePrioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6"><i class="bi bi-speedometer2 me-1"></i> <?= lang('WorkOrder.modal_prio_title') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('workorder/change-priority/' . $wo['id_wo']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <label class="form-label small fw-semibold"><?= lang('WorkOrder.modal_prio_label') ?> <span class="text-danger">*</span></label>
                    <select name="id_prioritas" class="form-select" required>
                        <?php foreach ($priorities as $pr): ?>
                            <option value="<?= $pr['id_prioritas'] ?>" <?= $wo['id_prioritas'] == $pr['id_prioritas'] ? 'selected' : '' ?>>
                                <?= esc(translate_priority($pr['tingkat_prioritas'])) ?> — Target: <?= lang('WorkOrder.sla_hours', [$pr['target_jam']]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text small mt-2"><?= lang('WorkOrder.modal_prio_help') ?></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><?= current_locale() === 'en' ? 'Cancel' : 'Batal' ?></button>
                    <button type="submit" class="btn btn-sm btn-gold"><?= current_locale() === 'en' ? 'Save New Priority' : 'Simpan Prioritas Baru' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 4: Finish Work Order & Spareparts Deduction -->
<div class="modal fade" id="finishModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fs-6"><i class="bi bi-check-circle me-1"></i> <?= lang('WorkOrder.modal_finish_title') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('workorder/finish/' . $wo['id_wo']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold"><?= lang('WorkOrder.finish_duration') ?> <span class="text-danger">*</span></label>
                            <input type="number" name="durasi_menit_pengerjaan" class="form-control" placeholder="<?= current_locale() === 'en' ? 'e.g. 60' : 'Contoh: 60' ?>" min="5" max="1440" required value="45">
                            <div class="form-text small"><?= lang('WorkOrder.finish_duration_help') ?></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold"><?= lang('WorkOrder.finish_photo') ?></label>
                            <input type="file" name="foto_bukti_selesai" class="form-control" accept="image/*">
                            <div class="form-text small"><?= current_locale() === 'en' ? 'Photo of repair result / asset normal condition.' : 'Foto hasil perbaikan / kondisi normal aset.' ?></div>
                        </div>

                        <div class="col-12 border-top pt-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="use_spareparts" name="use_spareparts" value="1" onchange="toggleSpareparts(this)">
                                <label class="form-check-label fw-bold text-dark" for="use_spareparts">
                                    <?= lang('WorkOrder.finish_use_parts') ?>
                                </label>
                            </div>
                            <small class="text-muted d-block mb-3"><?= lang('WorkOrder.finish_parts_help') ?></small>

                            <div id="sparepartsContainer" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle" id="sparepartsTable">
                                        <thead class="table-light small">
                                            <tr>
                                                <th><?= current_locale() === 'en' ? 'Select Spare Part Item' : 'Pilih Item Suku Cadang' ?></th>
                                                <th style="width: 120px;"><?= lang('WorkOrder.part_qty') ?></th>
                                                <th style="width: 50px;"><?= lang('WorkOrder.col_actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select name="spareparts[0][id_barang]" class="form-select form-select-sm">
                                                        <option value=""><?= current_locale() === 'en' ? '-- Select Spare Part --' : '-- Pilih Suku Cadang --' ?></option>
                                                        <?php foreach ($availableItems as $item): ?>
                                                            <option value="<?= $item['id_barang'] ?>">
                                                                [<?= esc($item['kode_barang']) ?>] <?= esc($item['nama_barang']) ?> (<?= current_locale() === 'en' ? 'Stock: ' : 'Stok: ' ?><?= $item['stok'] ?> <?= esc($item['satuan']) ?> - Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="spareparts[0][qty]" class="form-control form-control-sm" min="1" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
                                    <i class="bi bi-plus-lg me-1"></i> <?= lang('WorkOrder.btn_add_part_row') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal"><?= current_locale() === 'en' ? 'Cancel' : 'Batal' ?></button>
                    <button type="submit" class="btn btn-success btn-mobile-touch fw-bold px-4 shadow">
                        <i class="bi bi-check-lg me-1"></i> <?= lang('WorkOrder.btn_finish_confirm') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let rowIndex = 1;

    function toggleSpareparts(checkbox) {
        const container = document.getElementById('sparepartsContainer');
        container.style.display = checkbox.checked ? 'block' : 'none';
    }

    function addRow() {
        const table = document.getElementById('sparepartsTable').getElementsByTagName('tbody')[0];
        const newRow = table.insertRow();
        newRow.innerHTML = `
            <td>
                <select name="spareparts[${rowIndex}][id_barang]" class="form-select form-select-sm" required>
                    <option value=""><?= current_locale() === 'en' ? '-- Select Spare Part --' : '-- Pilih Suku Cadang --' ?></option>
                    <?php foreach ($availableItems as $item): ?>
                        <option value="<?= $item['id_barang'] ?>">
                            [<?= esc($item['kode_barang']) ?>] <?= esc($item['nama_barang']) ?> (<?= current_locale() === 'en' ? 'Stock: ' : 'Stok: ' ?><?= $item['stok'] ?> <?= esc($item['satuan']) ?> - Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="number" name="spareparts[${rowIndex}][qty]" class="form-control form-control-sm" min="1" value="1" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
            </td>
        `;
        rowIndex++;
    }

    function removeRow(btn) {
        const row = btn.closest('tr');
        const tbody = row.closest('tbody');
        if (tbody.rows.length > 1) {
            row.remove();
        } else {
            alert('<?= current_locale() === 'en' ? 'At least 1 item row is required when spareparts feature is enabled.' : 'Minimal harus ada 1 baris item jika fitur sparepart diaktifkan.' ?>');
        }
    }
</script>
<?= $this->endSection() ?>
