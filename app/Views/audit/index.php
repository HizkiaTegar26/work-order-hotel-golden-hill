<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-shield-check text-warning me-2"></i><?= lang('General.audit_title') ?></h4>
        <p class="text-muted small mb-0"><?= lang('General.audit_sub') ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('audit') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-clockwise me-1"></i> <?= lang('General.refresh_log') ?>
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable mb-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 160px;"><?= lang('General.time_date') ?></th>
                    <th><?= lang('General.user') ?></th>
                    <th><?= lang('General.role') ?></th>
                    <th><?= lang('General.system_module') ?></th>
                    <th><?= lang('General.activity_performed') ?></th>
                    <th><?= lang('General.ip_address') ?></th>
                </tr>
            </thead>
            <tbody class="small">
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td>
                            <span class="fw-semibold text-dark"><?= date('d/m/Y H:i:s', strtotime($l['timestamp'])) ?></span>
                        </td>
                        <td>
                            <?php if (!empty($l['nama_lengkap'])): ?>
                                <span class="fw-bold text-dark"><?= esc($l['nama_lengkap']) ?></span>
                                <small class="text-muted d-block"><?= esc($l['username']) ?></small>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= lang('General.system_automation') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($l['nama_role'])): ?>
                                <span class="badge bg-light text-dark border"><?= esc($l['nama_role']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-dark">System</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                <?= esc($l['modul']) ?>
                            </span>
                        </td>
                        <td class="text-secondary" style="max-width: 380px;">
                            <?= esc($l['aktivitas']) ?>
                        </td>
                        <td>
                            <code><?= esc($l['ip_address'] ?? '127.0.0.1') ?></code>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
