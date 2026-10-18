<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-clock-history text-danger me-2"></i><?= lang('WorkOrder.title_monitoring') ?></h4>
        <p class="text-muted small mb-0"><?= lang('WorkOrder.subtitle_monitoring') ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('workorder/monitoring') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-clockwise me-1"></i> <?= lang('WorkOrder.btn_refresh_sla') ?>
        </a>
    </div>
</div>

<!-- Escalation Alert Banner if any exist -->
<?php 
    $escalatedOrders = array_filter($workOrders, fn($w) => (int)$w['id_status'] === 8 || $w['sla_info']['status'] === 'overdue');
?>
<?php if (!empty($escalatedOrders)): ?>
    <div class="alert alert-danger border-danger d-flex align-items-center justify-content-between p-3 rounded-3 mb-4 shadow-sm animate-pulse" role="alert">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-2 text-danger"><i class="bi bi-exclamation-octagon-fill"></i></div>
            <div>
                <div class="fw-bold fs-6"><?= lang('WorkOrder.emergency_overdue_msg', [count($escalatedOrders)]) ?></div>
                <small><?= current_locale() === 'en' ? 'The system has automatically dispatched emergency escalation notifications to the Department Head and Management via WhatsApp.' : 'Sistem secara otomatis telah mengirimkan notifikasi eskalasi darurat ke WhatsApp Kepala Departemen dan Manajemen Admin.' ?></small>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- SLA Monitoring Table -->
<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable mb-0">
            <thead class="table-light small">
                <tr>
                    <th><?= lang('WorkOrder.col_code') ?></th>
                    <th><?= lang('WorkOrder.col_title') ?></th>
                    <th><?= lang('WorkOrder.col_dept') ?></th>
                    <th><?= lang('WorkOrder.col_priority') ?></th>
                    <th><?= lang('WorkOrder.col_status') ?></th>
                    <th><?= lang('WorkOrder.col_sla') ?></th>
                    <th><?= lang('WorkOrder.col_deadline') ?></th>
                    <th><?= lang('WorkOrder.col_handler') ?></th>
                    <th class="text-center"><?= lang('WorkOrder.col_actions') ?></th>
                </tr>
            </thead>
            <tbody class="small">
                <?php foreach ($workOrders as $wo): ?>
                    <?php $sla = $wo['sla_info']; ?>
                    <tr class="<?= $sla['status'] === 'overdue' ? 'table-danger' : ($sla['status'] === 'warning' ? 'table-warning' : '') ?>">
                        <td>
                            <a href="<?= base_url('workorder/detail/' . $wo['id_wo']) ?>" class="fw-bold text-dark text-decoration-none">
                                <?= esc($wo['kode_wo']) ?>
                            </a>
                            <div class="text-muted" style="font-size: 0.7rem;"><?= date('d/m/Y H:i', strtotime($wo['tanggal_ajuan'])) ?></div>
                        </td>
                        <td style="max-width: 250px;">
                            <div class="fw-semibold text-truncate"><?= esc($wo['judul_wo']) ?></div>
                            <?php if (!empty($wo['nama_aset'])): ?>
                                <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.68rem;">
                                    <i class="bi bi-tag me-1"></i> <?= esc($wo['nama_aset']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= esc($wo['nama_departemen']) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-dark"><?= translate_priority($wo['tingkat_prioritas']) ?> (<?= $wo['target_jam'] ?>H)</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= translate_status($wo['nama_status']) ?></span>
                        </td>
                        <td>
                            <span class="badge <?= $sla['badge_class'] ?> fs-6">
                                <i class="bi bi-stopwatch me-1"></i> <?= $sla['badge_text'] ?>
                            </span>
                        </td>
                        <td>
                            <?= $wo['deadline'] ? date('d M Y, H:i', strtotime($wo['deadline'])) : '-' ?>
                        </td>
                        <td>
                            <?= !empty($wo['nama_handler']) ? esc($wo['nama_handler']) : '<span class="text-muted fst-italic">' . lang('WorkOrder.unassigned') . '</span>' ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('workorder/detail/' . $wo['id_wo']) ?>" class="btn btn-sm btn-primary py-1 px-2">
                                <i class="bi bi-sliders me-1"></i> <?= current_locale() === 'en' ? 'Manage' : 'Tangani' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
