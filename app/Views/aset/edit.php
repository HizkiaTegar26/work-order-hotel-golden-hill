<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-pencil-square text-warning me-2"></i><?= lang('Aset.title_edit', [esc($asset['nama_aset'])]) ?></h4>
                <p class="text-muted small mb-0"><?= lang('Aset.col_code') ?>: <strong><?= esc($asset['kode_aset']) ?></strong></p>
            </div>
            <a href="<?= base_url('aset/detail/' . $asset['id_aset']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> <?= current_locale() === 'en' ? 'Back to Details' : 'Kembali ke Detail' ?>
            </a>
        </div>

        <div class="card card-custom p-4">
            <form action="<?= base_url('aset/update/' . $asset['id_aset']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary"><?= lang('Aset.col_code') ?></label>
                        <input type="text" class="form-control bg-light" value="<?= esc($asset['kode_aset']) ?>" disabled>
                        <div class="form-text small"><?= lang('Aset.field_code_locked') ?></div>
                    </div>

                    <div class="col-md-6">
                        <label for="nama_aset" class="form-label small fw-semibold text-secondary"><?= lang('Aset.field_name') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_aset" name="nama_aset" required value="<?= esc(old('nama_aset', $asset['nama_aset'])) ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="id_departemen" class="form-label small fw-semibold text-secondary"><?= lang('Aset.field_dept') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_departemen" name="id_departemen" required>
                            <?php foreach ($departemen as $d): ?>
                                <option value="<?= $d['id_departemen'] ?>" <?= old('id_departemen', $asset['id_departemen']) == $d['id_departemen'] ? 'selected' : '' ?>>
                                    <?= esc($d['nama_departemen']) ?> (<?= esc($d['kode_dept']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="kondisi" class="form-label small fw-semibold text-secondary"><?= lang('Aset.field_condition') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="kondisi" name="kondisi" required>
                            <option value="Baik" <?= old('kondisi', $asset['kondisi']) === 'Baik' ? 'selected' : '' ?>><?= lang('Aset.cond_good') ?></option>
                            <option value="Rusak Ringan" <?= old('kondisi', $asset['kondisi']) === 'Rusak Ringan' ? 'selected' : '' ?>><?= lang('Aset.cond_light_damage') ?></option>
                            <option value="Rusak Berat" <?= old('kondisi', $asset['kondisi']) === 'Rusak Berat' ? 'selected' : '' ?>><?= lang('Aset.cond_heavy_damage') ?></option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="lokasi_spesifik" class="form-label small fw-semibold text-secondary"><?= lang('Aset.field_location') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lokasi_spesifik" name="lokasi_spesifik" required value="<?= esc(old('lokasi_spesifik', $asset['lokasi_spesifik'])) ?>">
                    </div>

                    <div class="col-12 text-end pt-3 border-top">
                        <a href="<?= base_url('aset/detail/' . $asset['id_aset']) ?>" class="btn btn-secondary me-2"><?= current_locale() === 'en' ? 'Cancel' : 'Batal' ?></a>
                        <button type="submit" class="btn btn-gold px-4">
                            <i class="bi bi-save me-1"></i> <?= lang('Aset.btn_save_changes') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
