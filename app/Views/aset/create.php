<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-plus-circle text-warning me-2"></i><?= lang('Aset.title_create') ?></h4>
                <p class="text-muted small mb-0"><?= lang('Aset.subtitle_create') ?></p>
            </div>
            <a href="<?= base_url('aset') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> <?= current_locale() === 'en' ? 'Back to Assets List' : 'Kembali ke Daftar Aset' ?>
            </a>
        </div>

        <div class="card card-custom p-4">
            <form action="<?= base_url('aset/store') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="kode_aset" class="form-label small fw-semibold text-secondary"><?= lang('Aset.field_code') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kode_aset" name="kode_aset" placeholder="<?= current_locale() === 'en' ? 'e.g. AST-ENG-004 or AST-FO-001' : 'Contoh: AST-ENG-004 atau AST-FO-001' ?>" required value="<?= old('kode_aset') ?>">
                        <div class="form-text small"><?= lang('Aset.field_code_help') ?></div>
                    </div>

                    <div class="col-md-6">
                        <label for="nama_aset" class="form-label small fw-semibold text-secondary"><?= lang('Aset.field_name') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_aset" name="nama_aset" placeholder="<?= current_locale() === 'en' ? 'e.g. AC Split Daikin Inverter 2PK' : 'Contoh: AC Split Daikin Inverter 2PK' ?>" required value="<?= old('nama_aset') ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="id_departemen" class="form-label small fw-semibold text-secondary"><?= lang('Aset.field_dept') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_departemen" name="id_departemen" required>
                            <option value=""><?= lang('Aset.field_select_dept') ?></option>
                            <?php foreach ($departemen as $d): ?>
                                <option value="<?= $d['id_departemen'] ?>" <?= old('id_departemen') == $d['id_departemen'] ? 'selected' : '' ?>>
                                    <?= esc($d['nama_departemen']) ?> (<?= esc($d['kode_dept']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="kondisi" class="form-label small fw-semibold text-secondary"><?= lang('Aset.field_condition') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="kondisi" name="kondisi" required>
                            <option value="Baik" <?= old('kondisi') === 'Baik' ? 'selected' : '' ?>><?= lang('Aset.cond_good') ?> (<?= current_locale() === 'en' ? 'Normal Operational' : 'Normal Operasional' ?>)</option>
                            <option value="Rusak Ringan" <?= old('kondisi') === 'Rusak Ringan' ? 'selected' : '' ?>><?= lang('Aset.cond_light_damage') ?> (<?= current_locale() === 'en' ? 'Minor Repair Needed' : 'Perlu Servis Minor' ?>)</option>
                            <option value="Rusak Berat" <?= old('kondisi') === 'Rusak Berat' ? 'selected' : '' ?>><?= lang('Aset.cond_heavy_damage') ?> (<?= current_locale() === 'en' ? 'Non-functional / Critical' : 'Tidak Berfungsi / Kritis' ?>)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="lokasi_spesifik" class="form-label small fw-semibold text-secondary"><?= lang('Aset.field_location') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lokasi_spesifik" name="lokasi_spesifik" placeholder="<?= current_locale() === 'en' ? 'e.g. Deluxe Room 204, Server Room 2nd Fl, West Lobby' : 'Contoh: Kamar Deluxe 204, Ruang Server Lt. 2, Lobby Barat' ?>" required value="<?= old('lokasi_spesifik') ?>">
                    </div>

                    <div class="col-12 text-end pt-3 border-top">
                        <a href="<?= base_url('aset') ?>" class="btn btn-secondary me-2"><?= current_locale() === 'en' ? 'Cancel' : 'Batal' ?></a>
                        <button type="submit" class="btn btn-gold px-4">
                            <i class="bi bi-qr-code me-1"></i> <?= lang('Aset.btn_save_asset') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
