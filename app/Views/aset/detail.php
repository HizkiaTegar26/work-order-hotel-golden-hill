<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0 text-dark"><?= esc($asset['nama_aset']) ?></h4>
            <span class="badge bg-dark"><?= esc($asset['kode_aset']) ?></span>
            <?php
                $cClass = match($asset['kondisi']) {
                    'Baik' => 'bg-success',
                    'Rusak Ringan' => 'bg-warning text-dark',
                    'Rusak Berat' => 'bg-danger',
                    default => 'bg-secondary',
                };
            ?>
            <span class="badge <?= $cClass ?>"><?= translate_condition($asset['kondisi']) ?></span>
        </div>
        <p class="text-muted small mb-0"><?= current_locale() === 'en' ? 'Location: ' : 'Lokasi: ' ?><strong><?= esc($asset['lokasi_spesifik']) ?></strong> • <?= lang('Aset.col_dept') ?>: <strong><?= esc($asset['nama_departemen']) ?> (<?= esc($asset['kode_dept']) ?>)</strong></p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <!-- Shortcut Button to Report Work Order with Locked Asset -->
        <a href="<?= base_url('workorder/create?id_aset=' . $asset['id_aset']) ?>" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-3 shadow-sm">
            <i class="bi bi-tools me-1"></i> <?= lang('Aset.report_damage_this') ?>
        </a>
        <a href="<?= base_url('aset/print-qr/' . $asset['id_aset']) ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="bi bi-printer me-1"></i> <?= lang('Aset.print_qr_btn') ?>
        </a>
        <a href="<?= base_url('aset') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> <?= current_locale() === 'en' ? 'Back' : 'Kembali' ?>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- QR Code Profile Card -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 text-center mb-4">
            <h6 class="fw-bold text-dark mb-3"><?= lang('Aset.qr_label_title') ?></h6>
            
            <div class="p-3 bg-white border rounded-3 d-inline-block shadow-sm mb-3">
                <?php if (!empty($asset['qr_code_path']) && file_exists(FCPATH . $asset['qr_code_path'])): ?>
                    <img src="<?= base_url($asset['qr_code_path']) ?>" alt="QR Code" class="img-fluid" style="width: 180px; height: 180px;">
                <?php else: ?>
                    <div class="p-4 text-muted small"><?= current_locale() === 'en' ? 'QR Code not yet generated' : 'QR Code belum digenerate' ?></div>
                <?php endif; ?>
                <div class="fw-bold text-dark mt-2" style="letter-spacing: 1px;"><?= esc($asset['kode_aset']) ?></div>
                <small class="text-muted d-block" style="font-size: 0.72rem;"><?= current_locale() === 'en' ? 'Golden Hill Hotel Batu' : 'Hotel Golden Hill Batu' ?></small>
            </div>

            <div class="d-grid gap-2">
                <a href="<?= base_url($asset['qr_code_path']) ?>" download="<?= esc($asset['kode_aset']) ?>_QR.png" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download me-1"></i> <?= lang('Aset.download_qr_image') ?>
                </a>
            </div>
        </div>

        <!-- Asset Metadata -->
        <div class="card card-custom p-3">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><?= lang('Aset.facility_specs') ?></h6>
            <table class="table table-sm table-borderless small mb-0">
                <tr>
                    <td class="text-muted" style="width: 45%;"><?= lang('Aset.col_code') ?>:</td>
                    <td class="fw-bold"><?= esc($asset['kode_aset']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('Aset.col_dept') ?>:</td>
                    <td class="fw-semibold"><?= esc($asset['nama_departemen']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('Aset.physical_condition') ?></td>
                    <td><span class="badge <?= $cClass ?>"><?= translate_condition($asset['kondisi']) ?></span></td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('Aset.physical_location') ?></td>
                    <td class="fw-semibold"><?= esc($asset['lokasi_spesifik']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('Aset.registration_date') ?></td>
                    <td><?= $asset['created_at'] ? date('d/m/Y', strtotime($asset['created_at'])) : '-' ?></td>
                </tr>
                <tr>
                    <td class="text-muted"><?= lang('Aset.total_incidents') ?></td>
                    <td class="fw-bold text-danger"><?= lang('Aset.incident_count', [count($history)]) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Right: Complete Repair History Timeline -->
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i><?= lang('Aset.repair_history_title') ?></h5>
                    <small class="text-muted"><?= lang('Aset.repair_history_sub') ?></small>
                </div>
                <a href="<?= base_url('workorder/create?id_aset=' . $asset['id_aset']) ?>" class="btn btn-sm btn-warning text-dark fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> <?= lang('Aset.report_new_damage') ?>
                </a>
            </div>

            <?php if (empty($history)): ?>
                <div class="text-center py-5 text-muted bg-light rounded-3">
                    <i class="bi bi-shield-check fs-1 text-success d-block mb-2"></i>
                    <h6 class="fw-bold text-dark"><?= lang('Aset.prime_condition') ?></h6>
                    <p class="small mb-0"><?= lang('Aset.no_damage_recorded') ?></p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th><?= lang('WorkOrder.col_code') ?></th>
                                <th><?= current_locale() === 'en' ? 'Repair Description' : 'Deskripsi Perbaikan' ?></th>
                                <th><?= lang('WorkOrder.col_priority') ?></th>
                                <th><?= lang('WorkOrder.col_status') ?></th>
                                <th><?= current_locale() === 'en' ? 'Technician' : 'Teknisi' ?></th>
                                <th class="text-end"><?= lang('Dashboard.sparepart_cost') ?></th>
                                <th class="text-center"><?= lang('WorkOrder.col_actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php foreach ($history as $h): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('workorder/detail/' . $h['id_wo']) ?>" class="fw-bold text-dark text-decoration-none">
                                            <?= esc($h['kode_wo']) ?>
                                        </a>
                                        <div class="text-muted" style="font-size: 0.7rem;"><?= date('d/m/Y', strtotime($h['tanggal_ajuan'])) ?></div>
                                    </td>
                                    <td style="max-width: 250px;">
                                        <div class="fw-semibold text-truncate"><?= esc($h['judul_wo']) ?></div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;"><?= esc($h['deskripsi']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= translate_priority($h['tingkat_prioritas']) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                            $hStatusClass = match($h['nama_status']) {
                                                'Selesai' => 'bg-success',
                                                'Proses' => 'bg-warning text-dark',
                                                'Ditolak' => 'bg-danger',
                                                'Escalated' => 'bg-danger animate-pulse',
                                                default => 'bg-info text-dark',
                                            };
                                        ?>
                                        <span class="badge <?= $hStatusClass ?>"><?= translate_status($h['nama_status']) ?></span>
                                    </td>
                                    <td><?= !empty($h['handler_nama']) ? esc($h['handler_nama']) : '-' ?></td>
                                    <td class="text-end fw-bold text-success">
                                        Rp <?= number_format($h['total_biaya_sparepart'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('workorder/detail/' . $h['id_wo']) ?>" class="btn btn-sm btn-outline-primary py-1 px-2">
                                            <i class="bi bi-eye"></i> <?= lang('Dashboard.detail_btn') ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
