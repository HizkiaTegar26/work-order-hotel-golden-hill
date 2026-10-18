<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-archive text-warning me-2"></i><?= lang('General.inventory_title') ?></h4>
        <p class="text-muted small mb-0"><?= lang('General.inventory_sub') ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-gold btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus-lg me-1"></i> <?= lang('General.add_spare_part') ?>
        </button>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable mb-0">
            <thead class="table-light small">
                <tr>
                    <th><?= current_locale() === 'en' ? 'Item Code' : 'Kode Barang' ?></th>
                    <th><?= lang('WorkOrder.part_item_name') ?></th>
                    <th class="text-center"><?= lang('General.stock_available') ?></th>
                    <th><?= lang('General.unit') ?></th>
                    <th class="text-end"><?= lang('General.unit_price') ?></th>
                    <th class="text-center" style="width: 120px;"><?= lang('General.actions') ?></th>
                </tr>
            </thead>
            <tbody class="small">
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><span class="badge bg-dark"><?= esc($item['kode_barang']) ?></span></td>
                        <td class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></td>
                        <td class="text-center">
                            <?php if ($item['stok'] <= 10): ?>
                                <span class="badge bg-danger rounded-pill px-2 py-1">
                                    <i class="bi bi-exclamation-circle me-1"></i> <?= lang('General.stock_critical', [$item['stok']]) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success rounded-pill px-2 py-1">
                                    <?= $item['stok'] ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($item['satuan']) ?></td>
                        <td class="text-end fw-bold text-success">
                            Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" data-bs-toggle="modal" data-bs-target="#editModal<?= $item['id_barang'] ?>" title="<?= lang('General.edit') ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php if (session()->get('id_role') == 1): ?>
                                <a href="<?= base_url('inventaris/delete/' . $item['id_barang']) ?>" class="btn btn-sm btn-outline-danger py-1 px-2" title="<?= lang('General.delete') ?>" onclick="return confirm('<?= lang('General.confirm_delete') ?>');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Edit Item Modal -->
                    <div class="modal fade" id="editModal<?= $item['id_barang'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title fs-6"><?= lang('General.edit_spare_part', [esc($item['kode_barang'])]) ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="<?= base_url('inventaris/update/' . $item['id_barang']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold"><?= lang('WorkOrder.part_item_name') ?> <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_barang" class="form-control" required value="<?= esc($item['nama_barang']) ?>">
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold"><?= current_locale() === 'en' ? 'Stock Quantity' : 'Jumlah Stok' ?> <span class="text-danger">*</span></label>
                                                <input type="number" name="stok" class="form-control" min="0" required value="<?= $item['stok'] ?>">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold"><?= lang('General.unit') ?> <span class="text-danger">*</span></label>
                                                <input type="text" name="satuan" class="form-control" required value="<?= esc($item['satuan']) ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold"><?= lang('General.unit_price') ?> <span class="text-danger">*</span></label>
                                            <input type="number" name="harga_satuan" class="form-control" min="0" step="1000" required value="<?= $item['harga_satuan'] ?>">
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

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6"><i class="bi bi-plus-circle me-1 text-warning"></i> <?= lang('General.add_spare_part') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('inventaris/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?= current_locale() === 'en' ? 'Item Code' : 'Kode Barang' ?> <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" class="form-control" placeholder="<?= current_locale() === 'en' ? 'e.g. BRG-009' : 'Contoh: BRG-009' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?= lang('WorkOrder.part_item_name') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="<?= current_locale() === 'en' ? 'e.g. Single Switch Broco' : 'Contoh: Saklar Tunggal Broco' ?>" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold"><?= lang('General.initial_stock') ?> <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" min="0" value="10" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold"><?= lang('General.unit') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" placeholder="Pcs, Meter, Can" value="Pcs" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?= lang('General.unit_price') ?> <span class="text-danger">*</span></label>
                        <input type="number" name="harga_satuan" class="form-control" min="0" step="500" placeholder="50000" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><?= lang('General.cancel') ?></button>
                    <button type="submit" class="btn btn-sm btn-gold"><?= lang('General.save_to_inventory') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
