<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-box-seam text-warning me-2"></i><?= lang('Aset.title_list') ?></h4>
        <p class="text-muted small mb-0"><?= lang('Aset.subtitle_list') ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('aset/print-qr') ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="bi bi-printer me-1"></i> <?= lang('Aset.btn_print_all_qr') ?>
        </a>
        <a href="<?= base_url('aset/create') ?>" class="btn btn-gold btn-sm rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> <?= lang('Aset.btn_add_asset') ?>
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable mb-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 70px;"><?= lang('Aset.col_qr') ?></th>
                    <th><?= lang('Aset.col_code') ?></th>
                    <th><?= lang('Aset.col_name') ?></th>
                    <th><?= lang('Aset.col_dept') ?></th>
                    <th><?= lang('Aset.col_location') ?></th>
                    <th><?= lang('Aset.col_condition') ?></th>
                    <th class="text-center" style="width: 140px;"><?= lang('Aset.col_actions') ?></th>
                </tr>
            </thead>
            <tbody class="small">
                <?php foreach ($assets as $ast): ?>
                    <tr>
                        <td class="text-center">
                            <?php if (!empty($ast['qr_code_path']) && file_exists(FCPATH . $ast['qr_code_path'])): ?>
                                <img src="<?= base_url($ast['qr_code_path']) ?>" alt="QR" class="rounded border p-1" style="width: 50px; height: 50px;">
                            <?php else: ?>
                                <span class="badge bg-secondary">No QR</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('aset/detail/' . $ast['id_aset']) ?>" class="fw-bold text-dark text-decoration-none">
                                <?= esc($ast['kode_aset']) ?>
                            </a>
                        </td>
                        <td class="fw-semibold"><?= esc($ast['nama_aset']) ?></td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= esc($ast['nama_departemen']) ?></span>
                        </td>
                        <td><i class="bi bi-geo-alt text-muted me-1"></i><?= esc($ast['lokasi_spesifik']) ?></td>
                        <td>
                            <?php
                                $cClass = match($ast['kondisi']) {
                                    'Baik' => 'bg-success',
                                    'Rusak Ringan' => 'bg-warning text-dark',
                                    'Rusak Berat' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            ?>
                            <span class="badge <?= $cClass ?>"><?= translate_condition($ast['kondisi']) ?></span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('aset/detail/' . $ast['id_aset']) ?>" class="btn btn-outline-primary" title="<?= current_locale() === 'en' ? 'History & Details' : 'Riwayat & Rincian' ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= base_url('aset/print-qr/' . $ast['id_aset']) ?>" target="_blank" class="btn btn-outline-dark" title="<?= lang('Aset.print_qr_btn') ?>">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                                <a href="<?= base_url('workorder/create?id_aset=' . $ast['id_aset']) ?>" class="btn btn-outline-warning text-dark" title="<?= lang('Aset.report_damage_this') ?>">
                                    <i class="bi bi-tools"></i>
                                </a>
                                <?php if (session()->get('id_role') == 1): ?>
                                    <a href="<?= base_url('aset/edit/' . $ast['id_aset']) ?>" class="btn btn-outline-secondary" title="<?= current_locale() === 'en' ? 'Edit Asset' : 'Edit Aset' ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= base_url('aset/delete/' . $ast['id_aset']) ?>" class="btn btn-outline-danger" title="<?= current_locale() === 'en' ? 'Delete Asset' : 'Hapus Aset' ?>" onclick="return confirm('<?= current_locale() === 'en' ? 'Delete this asset and its QR code?' : 'Hapus aset ini beserta QR code terkait?' ?>');">
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
