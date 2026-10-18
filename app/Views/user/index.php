<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-people text-warning me-2"></i><?= lang('General.user_mgmt_title') ?></h4>
        <p class="text-muted small mb-0"><?= lang('General.user_mgmt_sub') ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-gold btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> <?= lang('General.add_user') ?>
        </button>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable mb-0">
            <thead class="table-light small">
                <tr>
                    <th><?= lang('General.fullname') ?></th>
                    <th><?= lang('General.username') ?></th>
                    <th><?= lang('General.email_wa') ?></th>
                    <th><?= lang('General.department') ?></th>
                    <th><?= lang('General.role') ?></th>
                    <th><?= lang('General.account_status') ?></th>
                    <th class="text-center" style="width: 140px;"><?= lang('General.actions') ?></th>
                </tr>
            </thead>
            <tbody class="small">
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="fw-bold text-dark">
                            <?= esc($u['nama_lengkap']) ?>
                            <?php if ($u['id_user'] == session()->get('user_id')): ?>
                                <span class="badge bg-primary ms-1" style="font-size: 0.65rem;"><?= current_locale() === 'en' ? 'Your Account' : 'Akun Anda' ?></span>
                            <?php endif; ?>
                        </td>
                        <td><code><?= esc($u['username']) ?></code></td>
                        <td>
                            <div><?= esc($u['email']) ?></div>
                            <small class="text-muted"><i class="bi bi-whatsapp text-success me-1"></i><?= esc($u['no_whatsapp']) ?></small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= esc($u['nama_departemen']) ?> (<?= esc($u['kode_dept']) ?>)</span>
                        </td>
                        <td>
                            <?php
                                $rClass = match($u['nama_role']) {
                                    'Admin' => 'bg-danger',
                                    'Kepala Departemen' => 'bg-primary',
                                    default => 'bg-secondary',
                                };
                            ?>
                            <span class="badge <?= $rClass ?>"><?= esc($u['nama_role']) ?></span>
                        </td>
                        <td>
                            <?php if ((int)$u['status_aktif'] === 1): ?>
                                <span class="badge bg-success"><?= lang('General.active') ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger"><?= lang('General.inactive') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $u['id_user'] ?>" title="<?= current_locale() === 'en' ? 'Edit User / Switch Role' : 'Edit User / Switch Role' ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="<?= base_url('users/toggle/' . $u['id_user']) ?>" class="btn btn-outline-warning text-dark" title="<?= current_locale() === 'en' ? 'Toggle Active Status' : 'Toggle Status Aktif' ?>">
                                    <i class="bi bi-power"></i>
                                </a>
                                <?php if ($u['id_user'] != session()->get('user_id')): ?>
                                    <a href="<?= base_url('users/delete/' . $u['id_user']) ?>" class="btn btn-outline-danger" title="<?= lang('General.delete') ?>" onclick="return confirm('<?= current_locale() === 'en' ? 'Delete this account permanently?' : 'Hapus akun ini secara permanen?' ?>');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal<?= $u['id_user'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title fs-6"><?= lang('General.edit_user', [esc($u['username'])]) ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="<?= base_url('users/update/' . $u['id_user']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold"><?= lang('General.fullname') ?> <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_lengkap" class="form-control" required value="<?= esc($u['nama_lengkap']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" required value="<?= esc($u['email']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold"><?= current_locale() === 'en' ? 'WhatsApp Number' : 'Nomor WhatsApp' ?></label>
                                            <input type="text" name="no_whatsapp" class="form-control" value="<?= esc($u['no_whatsapp']) ?>">
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold"><?= lang('General.role') ?> <span class="text-danger">*</span></label>
                                                <select name="id_role" class="form-select" required>
                                                    <?php foreach ($roles as $r): ?>
                                                        <option value="<?= $r['id_role'] ?>" <?= $u['id_role'] == $r['id_role'] ? 'selected' : '' ?>>
                                                            <?= esc($r['nama_role']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold"><?= lang('General.department') ?> <span class="text-danger">*</span></label>
                                                <select name="id_departemen" class="form-select" required>
                                                    <?php foreach ($departemen as $d): ?>
                                                        <option value="<?= $d['id_departemen'] ?>" <?= $u['id_departemen'] == $d['id_departemen'] ? 'selected' : '' ?>>
                                                            <?= esc($d['nama_departemen']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold"><?= lang('General.new_password_optional') ?></label>
                                            <input type="password" name="password" class="form-control" placeholder="••••••••">
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6"><i class="bi bi-person-plus-fill me-1 text-warning"></i> <?= lang('General.add_user') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('users/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?= lang('General.fullname') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="<?= current_locale() === 'en' ? 'e.g. Achmad Fauzi' : 'Contoh: Achmad Fauzi' ?>" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold"><?= lang('General.username') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="fauzi_eng" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold"><?= lang('Auth.password') ?> <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="<?= lang('Auth.pass_min_length') ?>" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="fauzi@goldenhill.com" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold"><?= current_locale() === 'en' ? 'WhatsApp No.' : 'No. WhatsApp' ?></label>
                            <input type="text" name="no_whatsapp" class="form-control" placeholder="08123456789">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold"><?= lang('General.role') ?> <span class="text-danger">*</span></label>
                            <select name="id_role" class="form-select" required>
                                <option value=""><?= current_locale() === 'en' ? '-- Select Role --' : '-- Pilih Role --' ?></option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id_role'] ?>"><?= esc($r['nama_role']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold"><?= lang('General.department') ?> <span class="text-danger">*</span></label>
                            <select name="id_departemen" class="form-select" required>
                                <option value=""><?= current_locale() === 'en' ? '-- Select Department --' : '-- Pilih Departemen --' ?></option>
                                <?php foreach ($departemen as $d): ?>
                                    <option value="<?= $d['id_departemen'] ?>"><?= esc($d['nama_departemen']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><?= lang('General.cancel') ?></button>
                    <button type="submit" class="btn btn-sm btn-gold"><?= current_locale() === 'en' ? 'Create User Account' : 'Buat Akun Pengguna' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
