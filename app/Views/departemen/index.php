<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-diagram-3 text-warning me-2"></i><?= lang('General.dept_mgmt_title') ?></h4>
        <p class="text-muted small mb-0"><?= lang('General.dept_mgmt_sub') ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-gold btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addDeptModal">
            <i class="bi bi-plus-lg me-1"></i> <?= lang('General.add_dept') ?>
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th style="width: 80px;"><?= current_locale() === 'en' ? 'Code' : 'Kode' ?></th>
                            <th><?= lang('General.dept_name') ?></th>
                            <th class="text-center" style="width: 120px;"><?= lang('General.actions') ?></th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php foreach ($departemen as $d): ?>
                            <tr>
                                <td><span class="badge bg-dark fs-6"><?= esc($d['kode_dept']) ?></span></td>
                                <td class="fw-semibold text-dark"><?= esc($d['nama_departemen']) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" data-bs-toggle="modal" data-bs-target="#editDeptModal<?= $d['id_departemen'] ?>" title="<?= lang('General.edit') ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="<?= base_url('departemen/delete/' . $d['id_departemen']) ?>" class="btn btn-sm btn-outline-danger py-1 px-2" title="<?= lang('General.delete') ?>" onclick="return confirm('<?= lang('General.confirm_delete') ?>');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editDeptModal<?= $d['id_departemen'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title fs-6"><?= lang('General.edit_dept') ?>: <?= esc($d['kode_dept']) ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= base_url('departemen/update/' . $d['id_departemen']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold"><?= lang('General.dept_code') ?> <span class="text-danger">*</span></label>
                                                    <input type="text" name="kode_dept" class="form-control" required value="<?= esc($d['kode_dept']) ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold"><?= lang('General.dept_name') ?> <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_departemen" class="form-control" required value="<?= esc($d['nama_departemen']) ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><?= lang('General.cancel') ?></button>
                                                <button type="submit" class="btn btn-sm btn-gold"><?= lang('WorkOrder.btn_save_changes') ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Dept Modal -->
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6"><i class="bi bi-plus-circle me-1 text-warning"></i> <?= lang('General.add_dept') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('departemen/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?= lang('General.dept_code') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="kode_dept" class="form-control" placeholder="<?= current_locale() === 'en' ? 'e.g. SEC, ACC, HRD' : 'Contoh: SEC, ACC, HRD' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?= lang('General.dept_name') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="nama_departemen" class="form-control" placeholder="<?= current_locale() === 'en' ? 'e.g. Security & Safety' : 'Contoh: Security & Safety' ?>" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><?= lang('General.cancel') ?></button>
                    <button type="submit" class="btn btn-sm btn-gold"><?= current_locale() === 'en' ? 'Save Department' : 'Simpan Departemen' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
