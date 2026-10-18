<!DOCTYPE html>
<html lang="<?= current_locale() ?>">
<head>
    <meta charset="UTF-8">
    <title><?= lang('Report.spk_title') ?> - <?= esc($wo['kode_wo']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1e293b;
            margin: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .hotel-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .hotel-subtitle {
            font-size: 10px;
            color: #64748b;
        }
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: #f1f5f9;
            padding: 6px;
            border: 1px solid #cbd5e1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table-info td {
            padding: 5px;
            vertical-align: top;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #94a3b8;
            padding: 6px;
        }
        .table-bordered th {
            background-color: #e2e8f0;
            text-align: left;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
            border: 1px solid #94a3b8;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
        }
        .signatures td {
            width: 33.3%;
            text-align: center;
            vertical-align: top;
        }
        .sig-box {
            height: 60px;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="margin-bottom: 0;">
            <tr>
                <td style="width: 70%;">
                    <div class="hotel-title"><?= lang('Report.hotel_title') ?></div>
                    <div class="hotel-subtitle"><?= lang('Report.hotel_address') ?></div>
                    <div class="hotel-subtitle"><?= lang('Report.hotel_phone') ?></div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="font-size: 14px; font-weight: bold; color: #d97706;"><?= esc($wo['kode_wo']) ?></div>
                    <div style="font-size: 10px; color: #64748b;"><?= date('d F Y, H:i', strtotime($wo['tanggal_ajuan'])) ?></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title"><?= lang('Report.spk_title') ?></div>

    <table class="table-info">
        <tr>
            <td style="width: 20%; font-weight: bold;"><?= lang('Report.job_title') ?></td>
            <td style="width: 45%;"><?= esc($wo['judul_wo']) ?></td>
            <td style="width: 15%; font-weight: bold;"><?= lang('Report.status_label') ?></td>
            <td style="width: 20%;"><span class="badge"><?= translate_status($wo['nama_status']) ?></span></td>
        </tr>
        <tr>
            <td style="font-weight: bold;"><?= lang('Report.category_label') ?></td>
            <td><?= translate_category($wo['nama_kategori']) ?></td>
            <td style="font-weight: bold;"><?= lang('Report.sla_priority_label') ?></td>
            <td><strong><?= translate_priority($wo['tingkat_prioritas']) ?> (<?= lang('WorkOrder.sla_hours', [$wo['target_jam']]) ?>)</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;"><?= lang('Report.target_dept_label') ?></td>
            <td><?= esc($wo['nama_departemen']) ?> (<?= esc($wo['kode_dept']) ?>)</td>
            <td style="font-weight: bold;"><?= lang('Report.deadline_label') ?></td>
            <td><?= $wo['deadline'] ? date('d/m/Y H:i', strtotime($wo['deadline'])) : '-' ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;"><?= lang('Report.requester_label') ?></td>
            <td><?= esc($wo['nama_pengaju']) ?></td>
            <td style="font-weight: bold;"><?= lang('Report.handler_label') ?></td>
            <td><strong><?= esc($wo['nama_handler'] ?? lang('WorkOrder.unassigned')) ?></strong></td>
        </tr>
        <?php if (!empty($wo['nama_aset'])): ?>
        <tr>
            <td style="font-weight: bold;"><?= lang('Report.linked_asset_label') ?></td>
            <td colspan="3"><?= esc($wo['nama_aset']) ?> [<?= esc($wo['kode_aset']) ?>] — <?= current_locale() === 'en' ? 'Location: ' : 'Lokasi: ' ?><?= esc($wo['aset_lokasi']) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div style="font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?= lang('Report.desc_instruction') ?></div>
    <div style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 10px; margin-bottom: 15px; min-height: 40px;">
        <?= nl2br(esc($wo['deskripsi'])) ?>
    </div>

    <div style="font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?= lang('Report.spareparts_breakdown') ?></div>
    <table class="table-bordered">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;"><?= lang('Report.col_no') ?></th>
                <th style="width: 20%;"><?= lang('Report.part_item_code') ?></th>
                <th style="width: 40%;"><?= lang('Report.part_item_name') ?></th>
                <th style="width: 15%; text-align: center;"><?= lang('Report.part_item_qty') ?></th>
                <th style="width: 20%; text-align: right;"><?= lang('Report.part_item_subtotal') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($spareparts)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b;"><?= lang('Report.no_spareparts_used') ?></td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($spareparts as $sp): ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td><?= esc($sp['kode_barang']) ?></td>
                        <td><?= esc($sp['nama_barang']) ?></td>
                        <td style="text-align: center;"><?= $sp['jumlah'] ?> <?= esc($sp['satuan']) ?></td>
                        <td style="text-align: right;">Rp <?= number_format($sp['harga_subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;"><?= lang('Report.total_material_cost') ?></th>
                <th style="text-align: right;">Rp <?= number_format($wo['total_biaya_sparepart'], 0, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div><?= lang('Report.sig_requester') ?></div>
                <div class="sig-box"></div>
                <div style="font-weight: bold;"><?= esc($wo['nama_pengaju']) ?></div>
            </td>
            <td>
                <div><?= lang('Report.sig_handler') ?></div>
                <div class="sig-box"></div>
                <div style="font-weight: bold;"><?= esc($wo['nama_handler'] ?? '...........................') ?></div>
            </td>
            <td>
                <div><?= lang('Report.sig_kadep') ?></div>
                <div class="sig-box"></div>
                <div style="font-weight: bold;"><?= esc($wo['nama_kadep'] ?? '...........................') ?></div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <?= lang('Report.pdf_footer', [date('d/m/Y H:i:s')]) ?>
    </div>
</body>
</html>
