<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb & Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-tools text-warning me-2"></i><?= lang('WorkOrder.title_list') ?></h4>
        <p class="text-muted small mb-0"><?= lang('WorkOrder.subtitle_list') ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('workorder/export-excel') ?><?= !empty($currentFilters['dept']) ? '?dept=' . $currentFilters['dept'] : '' ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-file-earmark-excel me-1"></i> <?= lang('WorkOrder.btn_export_excel') ?>
        </a>
        <a href="<?= base_url('workorder/create') ?>" class="btn btn-gold btn-sm rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> <?= lang('WorkOrder.btn_create') ?>
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-custom p-3 mb-4">
    <form action="<?= base_url('workorder') ?>" method="GET" class="row g-2 align-items-end">
        <div class="col-md-3 col-sm-6">
            <label class="form-label small fw-semibold text-secondary mb-1"><?= lang('WorkOrder.filter_status') ?></label>
            <select name="status" class="form-select form-select-sm">
                <option value=""><?= lang('WorkOrder.filter_all_status') ?></option>
                <?php foreach ($statusList as $st): ?>
                    <option value="<?= $st['id_status'] ?>" <?= ($currentFilters['status'] == $st['id_status']) ? 'selected' : '' ?>>
                        <?= esc(translate_status($st['nama_status'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3 col-sm-6">
            <label class="form-label small fw-semibold text-secondary mb-1"><?= lang('WorkOrder.filter_dept') ?></label>
            <select name="dept" class="form-select form-select-sm">
                <option value=""><?= lang('WorkOrder.filter_all_dept') ?></option>
                <?php foreach ($departemen as $d): ?>
                    <option value="<?= $d['id_departemen'] ?>" <?= ($currentFilters['dept'] == $d['id_departemen']) ? 'selected' : '' ?>>
                        <?= esc($d['nama_departemen']) ?> (<?= esc($d['kode_dept']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 col-sm-6">
            <label class="form-label small fw-semibold text-secondary mb-1"><?= lang('WorkOrder.filter_priority') ?></label>
            <select name="prio" class="form-select form-select-sm">
                <option value=""><?= lang('WorkOrder.filter_all_priority') ?></option>
                <?php foreach ($prioritas as $p): ?>
                    <option value="<?= $p['id_prioritas'] ?>" <?= ($currentFilters['prio'] == $p['id_prioritas']) ? 'selected' : '' ?>>
                        <?= esc(translate_priority($p['tingkat_prioritas'])) ?> (<?= $p['target_jam'] ?>H)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 col-sm-6">
            <label class="form-label small fw-semibold text-secondary mb-1"><?= lang('WorkOrder.filter_ownership') ?></label>
            <select name="user" class="form-select form-select-sm">
                <option value=""><?= lang('WorkOrder.filter_all_data') ?></option>
                <option value="me" <?= ($currentFilters['user'] === 'me') ? 'selected' : '' ?>><?= lang('WorkOrder.filter_my_wo') ?></option>
            </select>
        </div>

        <div class="col-md-2 col-sm-12 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-dark w-100">
                <i class="bi bi-funnel me-1"></i> <?= lang('WorkOrder.btn_filter') ?>
            </button>
            <a href="<?= base_url('workorder') ?>" class="btn btn-sm btn-light border" title="<?= lang('WorkOrder.btn_reset') ?>">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- Main Table Card -->
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
                    <th><?= lang('WorkOrder.col_handler') ?></th>
                    <th><?= lang('WorkOrder.col_cost') ?></th>
                    <th class="text-center" style="width: 120px;"><?= lang('WorkOrder.col_actions') ?></th>
                </tr>
            </thead>
            <tbody class="small">
                <?php foreach ($workOrders as $wo): ?>
                    <tr>
                        <td>
                            <a href="<?= base_url('workorder/detail/' . $wo['id_wo']) ?>" class="fw-bold text-dark text-decoration-none">
                                <?= esc($wo['kode_wo']) ?>
                            </a>
                            <div class="text-muted" style="font-size: 0.7rem;"><?= date('d/m/Y H:i', strtotime($wo['tanggal_ajuan'])) ?></div>
                            <div class="text-muted" style="font-size: 0.7rem;"><?= current_locale() === 'en' ? 'By: ' : 'Oleh: ' ?><?= esc($wo['nama_pengaju']) ?></div>
                        </td>
                        <td style="max-width: 240px;">
                            <div class="fw-semibold text-truncate"><?= esc($wo['judul_wo']) ?></div>
                            <div class="text-muted text-truncate" style="font-size: 0.75rem;"><?= esc($wo['deskripsi']) ?></div>
                            <?php if (!empty($wo['nama_aset'])): ?>
                                <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.68rem;">
                                    <i class="bi bi-tag me-1"></i> <?= esc($wo['nama_aset']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= esc($wo['nama_departemen']) ?></span>
                            <div class="text-muted" style="font-size: 0.7rem;"><?= translate_category($wo['nama_kategori']) ?></div>
                        </td>
                        <td>
                            <?php
                                $pClass = match($wo['tingkat_prioritas']) {
                                    'Emergency' => 'bg-danger',
                                    'High' => 'bg-warning text-dark',
                                    'Medium' => 'bg-info text-dark',
                                    default => 'bg-secondary',
                                };
                            ?>
                            <span class="badge <?= $pClass ?>"><?= translate_priority($wo['tingkat_prioritas']) ?></span>
                            <div class="text-muted" style="font-size: 0.7rem;"><?= lang('Dashboard.target_label', [lang('WorkOrder.sla_hours', [$wo['target_jam']])]) ?></div>
                        </td>
                        <td>
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
                            <span class="badge <?= $sClass ?>"><?= translate_status($wo['nama_status']) ?></span>
                        </td>
                        <td>
                            <?php $sla = $wo['sla_info']; ?>
                            <span class="badge <?= $sla['badge_class'] ?>">
                                <i class="bi bi-stopwatch me-1"></i> <?= $sla['badge_text'] ?>
                            </span>
                            <?php if ($wo['deadline']): ?>
                                <div class="text-muted" style="font-size: 0.7rem;">Deadline: <?= date('d/m H:i', strtotime($wo['deadline'])) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($wo['nama_handler'])): ?>
                                <span class="badge bg-dark"><?= esc($wo['nama_handler']) ?></span>
                            <?php else: ?>
                                <span class="text-muted fst-italic"><?= lang('WorkOrder.unassigned') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold text-end">
                            Rp <?= number_format($wo['total_biaya_sparepart'], 0, ',', '.') ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('workorder/detail/' . $wo['id_wo']) ?>" class="btn btn-outline-primary" title="<?= lang('Dashboard.detail_btn') ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php 
                                    $currentUserId = (int) session()->get('user_id');
                                    $currentRoleId = (int) session()->get('id_role');
                                    $isOwnerOrAdmin = ($currentUserId === (int)$wo['id_user'] || $currentRoleId === 1);
                                ?>
                                <?php if ($isOwnerOrAdmin && (int)$wo['id_status'] === 1): ?>
                                    <a href="<?= base_url('workorder/edit/' . $wo['id_wo']) ?>" class="btn btn-outline-secondary" title="Edit WO">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= base_url('workorder/delete/' . $wo['id_wo']) ?>" class="btn btn-outline-danger" title="Hapus WO" onclick="return confirm('<?= current_locale() === 'en' ? 'Delete this Work Order?' : 'Hapus Work Order ini?' ?>');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
