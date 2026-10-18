<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-plus-circle text-warning me-2"></i><?= lang('WorkOrder.title_create') ?></h4>
                <p class="text-muted small mb-0"><?= lang('WorkOrder.subtitle_create') ?></p>
            </div>
            <a href="<?= base_url('workorder') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> <?= current_locale() === 'en' ? 'Back to List' : 'Kembali ke Daftar' ?>
            </a>
        </div>

        <?php if (!empty($selectedAsset)): ?>
            <div class="alert alert-warning border-warning d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-3">
                    <div class="fs-2 text-warning"><i class="bi bi-qr-code-scan"></i></div>
                    <div>
                        <div class="fw-bold text-dark"><?= lang('WorkOrder.asset_locked_notice', [esc($selectedAsset['nama_aset']), esc($selectedAsset['kode_aset'])]) ?></div>
                        <small class="text-muted"><?= current_locale() === 'en' ? 'Location: ' : 'Lokasi: ' ?><?= esc($selectedAsset['lokasi_spesifik']) ?> • <?= lang('Aset.col_condition') ?>: <span class="badge bg-secondary"><?= translate_condition($selectedAsset['kondisi']) ?></span></small>
                    </div>
                </div>
                <span class="badge bg-warning text-dark px-3 py-2"><?= lang('WorkOrder.auto_locked_badge') ?></span>
            </div>
        <?php endif; ?>

        <div class="card card-custom p-4">
            <form action="<?= base_url('workorder/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <!-- Judul Work Order -->
                    <div class="col-12">
                        <label for="judul_wo" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_title') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="judul_wo" name="judul_wo" placeholder="<?= current_locale() === 'en' ? 'e.g. Main Lobby AC Not Cooling / Corridor Light Broken' : 'Contoh: AC Lobby Utama Kurang Dingin / Lampu Koridor Mati' ?>" required value="<?= old('judul_wo') ?>">
                        <div class="form-text small"><?= lang('WorkOrder.field_title_help') ?></div>
                    </div>

                    <!-- Kategori WO -->
                    <div class="col-md-6">
                        <label for="id_kategori" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_category') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_kategori" name="id_kategori" required>
                            <option value=""><?= lang('WorkOrder.field_select_cat') ?></option>
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id_kategori'] ?>" <?= old('id_kategori') == $k['id_kategori'] ? 'selected' : '' ?>>
                                    <?= esc(translate_category($k['nama_kategori'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Prioritas SLA -->
                    <div class="col-md-6">
                        <label for="id_prioritas" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_priority') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_prioritas" name="id_prioritas" required>
                            <option value=""><?= lang('WorkOrder.field_select_prio') ?></option>
                            <?php foreach ($prioritas as $p): ?>
                                <option value="<?= $p['id_prioritas'] ?>" <?= (old('id_prioritas') == $p['id_prioritas'] || $p['tingkat_prioritas'] === 'High') ? 'selected' : '' ?>>
                                    <?= esc(translate_priority($p['tingkat_prioritas'])) ?> — Target: <?= lang('WorkOrder.sla_hours', [$p['target_jam']]) ?> (SLA)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small text-danger"><?= lang('WorkOrder.field_prio_help') ?></div>
                    </div>

                    <!-- Departemen Penanggung Jawab -->
                    <div class="col-md-6">
                        <label for="id_departemen" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_dept') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_departemen" name="id_departemen" required>
                            <option value=""><?= lang('WorkOrder.field_select_dept') ?></option>
                            <?php foreach ($departemen as $d): ?>
                                <option value="<?= $d['id_departemen'] ?>" <?= (!empty($selectedAsset) && $selectedAsset['id_departemen'] == $d['id_departemen']) ? 'selected' : (old('id_departemen') == $d['id_departemen'] ? 'selected' : '') ?>>
                                    <?= esc($d['nama_departemen']) ?> (<?= esc($d['kode_dept']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Kaitan Aset Hotel (Opsional / Terisi dari Scan) -->
                    <div class="col-md-6">
                        <label for="id_aset" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_asset') ?></label>
                        <select class="form-select" id="id_aset" name="id_aset">
                            <option value=""><?= lang('WorkOrder.field_no_asset') ?></option>
                            <?php foreach ($assets as $a): ?>
                                <option value="<?= $a['id_aset'] ?>" <?= (!empty($selectedAsset) && $selectedAsset['id_aset'] == $a['id_aset']) ? 'selected' : (old('id_aset') == $a['id_aset'] ? 'selected' : '') ?>>
                                    [<?= esc($a['kode_aset']) ?>] <?= esc($a['nama_aset']) ?> (<?= esc($a['lokasi_spesifik']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small"><?= current_locale() === 'en' ? 'Or use <a href="' . base_url('scan') . '" class="text-decoration-none">QR Camera Scanner</a> for instant selection.' : 'Atau gunakan <a href="' . base_url('scan') . '" class="text-decoration-none">Scanner Kamera QR</a> untuk memilih instan.' ?></div>
                    </div>

                    <!-- Deskripsi Lengkap -->
                    <div class="col-12">
                        <label for="deskripsi" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_description') ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="<?= lang('WorkOrder.field_desc_help') ?>" required><?= old('deskripsi') ?></textarea>
                    </div>

                    <!-- Unggah Foto Kendala -->
                    <div class="col-12">
                        <label for="foto_trouble" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_photo_trouble') ?></label>
                        <input type="file" class="form-control" id="foto_trouble" name="foto_trouble" accept="image/*">
                        <div class="form-text small"><?= lang('WorkOrder.field_photo_help') ?></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 text-end pt-3 border-top">
                        <a href="<?= base_url('workorder') ?>" class="btn btn-secondary me-2"><?= current_locale() === 'en' ? 'Cancel' : 'Batal' ?></a>
                        <button type="submit" class="btn btn-gold px-4">
                            <i class="bi bi-send-check me-1"></i> <?= lang('WorkOrder.btn_submit_wo') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
