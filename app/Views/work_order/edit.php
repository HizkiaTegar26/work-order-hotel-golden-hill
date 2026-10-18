<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-pencil-square text-warning me-2"></i><?= lang('WorkOrder.title_edit', [esc($wo['kode_wo'])]) ?></h4>
                <p class="text-muted small mb-0"><?= current_locale() === 'en' ? 'Can only be modified while the Work Order status is "New".' : 'Hanya dapat diubah selama status Work Order masih "Baru".' ?></p>
            </div>
            <a href="<?= base_url('workorder/detail/' . $wo['id_wo']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> <?= current_locale() === 'en' ? 'Back to Details' : 'Kembali ke Detail' ?>
            </a>
        </div>

        <div class="card card-custom p-4">
            <form action="<?= base_url('workorder/update/' . $wo['id_wo']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label for="judul_wo" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_title') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="judul_wo" name="judul_wo" required value="<?= esc(old('judul_wo', $wo['judul_wo'])) ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="id_kategori" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_category') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_kategori" name="id_kategori" required>
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id_kategori'] ?>" <?= old('id_kategori', $wo['id_kategori']) == $k['id_kategori'] ? 'selected' : '' ?>>
                                    <?= esc(translate_category($k['nama_kategori'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="id_departemen" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_dept') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_departemen" name="id_departemen" required>
                            <?php foreach ($departemen as $d): ?>
                                <option value="<?= $d['id_departemen'] ?>" <?= old('id_departemen', $wo['id_departemen']) == $d['id_departemen'] ? 'selected' : '' ?>>
                                    <?= esc($d['nama_departemen']) ?> (<?= esc($d['kode_dept']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="id_aset" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_asset') ?></label>
                        <select class="form-select" id="id_aset" name="id_aset">
                            <option value=""><?= lang('WorkOrder.field_no_asset') ?></option>
                            <?php foreach ($assets as $a): ?>
                                <option value="<?= $a['id_aset'] ?>" <?= old('id_aset', $wo['id_aset']) == $a['id_aset'] ? 'selected' : '' ?>>
                                    [<?= esc($a['kode_aset']) ?>] <?= esc($a['nama_aset']) ?> (<?= esc($a['lokasi_spesifik']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="deskripsi" class="form-label small fw-semibold text-secondary"><?= lang('WorkOrder.field_description') ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?= esc(old('deskripsi', $wo['deskripsi'])) ?></textarea>
                    </div>

                    <div class="col-12">
                        <label for="foto_trouble" class="form-label small fw-semibold text-secondary"><?= current_locale() === 'en' ? 'Replace Problem Photo (Optional)' : 'Ganti Foto Kendala (Opsional)' ?></label>
                        <input type="file" class="form-control" id="foto_trouble" name="foto_trouble" accept="image/*">
                        <?php if (!empty($wo['foto_trouble'])): ?>
                            <div class="mt-2 small text-muted">
                                <?= lang('WorkOrder.field_current_photo') ?> <a href="<?= base_url($wo['foto_trouble']) ?>" target="_blank"><?= current_locale() === 'en' ? 'View Photo' : 'Lihat Foto' ?></a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 text-end pt-3 border-top">
                        <a href="<?= base_url('workorder/detail/' . $wo['id_wo']) ?>" class="btn btn-secondary me-2"><?= current_locale() === 'en' ? 'Cancel' : 'Batal' ?></a>
                        <button type="submit" class="btn btn-gold px-4">
                            <i class="bi bi-save me-1"></i> <?= lang('WorkOrder.btn_save_changes') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
