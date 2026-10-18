<!DOCTYPE html>
<html lang="<?= current_locale() ?>">
<head>
    <meta charset="UTF-8">
    <title><?= lang('Aset.title_print_qr') ?> - Hotel Golden Hill</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 20px;
        }
        .no-print-bar {
            background: #0f172a;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .btn-print {
            background: #d97706;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        .labels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
        }
        .qr-card {
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            page-break-inside: avoid;
        }
        .hotel-header {
            font-size: 10px;
            font-weight: bold;
            color: #d97706;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .asset-code {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 6px;
            letter-spacing: 0.5px;
        }
        .asset-name {
            font-size: 11px;
            color: #334155;
            margin-top: 2px;
            height: 28px;
            overflow: hidden;
        }
        .asset-loc {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none;
            }
            .labels-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }
            .qr-card {
                border: 1px solid #94a3b8;
            }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <div>
            <strong><?= lang('Aset.print_sheet_title') ?></strong>
            <div style="font-size: 11px; color: #94a3b8;"><?= lang('Aset.print_sheet_ready', [count($assets)]) ?></div>
        </div>
        <button class="btn-print" onclick="window.print()">🖨️ <?= lang('Aset.print_sheet_btn') ?></button>
    </div>

    <div class="labels-grid">
        <?php foreach ($assets as $ast): ?>
            <div class="qr-card">
                <div class="hotel-header">★ Hotel Golden Hill Batu ★</div>
                <?php if (!empty($ast['qr_code_path']) && file_exists(FCPATH . $ast['qr_code_path'])): ?>
                    <img src="<?= base_url($ast['qr_code_path']) ?>" alt="QR" style="width: 140px; height: 140px; margin: 4px 0;">
                <?php else: ?>
                    <div style="height: 140px; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #94a3b8;">No QR</div>
                <?php endif; ?>
                <div class="asset-code"><?= esc($ast['kode_aset']) ?></div>
                <div class="asset-name"><?= esc($ast['nama_aset']) ?></div>
                <div class="asset-loc"><?= esc($ast['lokasi_spesifik']) ?> (<?= esc($ast['nama_departemen']) ?>)</div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
