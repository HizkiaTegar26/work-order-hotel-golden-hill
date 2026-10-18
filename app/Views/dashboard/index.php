<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0 text-dark"><?= lang('Dashboard.page_title') ?></h4>
            <span class="badge bg-primary"><?= lang('Dashboard.realtime_badge') ?></span>
        </div>
        <p class="text-muted small mb-0"><?= lang('Dashboard.subtitle') ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('scan') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="bi bi-camera me-1"></i> <?= lang('Dashboard.btn_scan_qr') ?>
        </a>
        <a href="<?= base_url('workorder/create') ?>" class="btn btn-gold btn-sm rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> <?= lang('Dashboard.btn_create_wo') ?>
        </a>
    </div>
</div>

<!-- Primary Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 h-100 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase"><?= lang('Dashboard.total_wo') ?></span>
                    <h3 class="fw-bold text-dark mt-1 mb-0"><?= number_format($kpi['total_wo']) ?></h3>
                </div>
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="bi bi-collection-fill fs-5"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block"><?= lang('Dashboard.all_depts') ?></small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 h-100 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase"><?= lang('Dashboard.in_progress') ?></span>
                    <h3 class="fw-bold text-warning mt-1 mb-0"><?= number_format($kpi['total_proses']) ?></h3>
                </div>
                <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3">
                    <i class="bi bi-gear-wide-connected fs-5"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block"><?= lang('Dashboard.waiting_review', [number_format($kpi['total_baru'])]) ?></small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 h-100 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase"><?= lang('Dashboard.completed') ?></span>
                    <h3 class="fw-bold text-success mt-1 mb-0"><?= number_format($kpi['total_selesai']) ?></h3>
                </div>
                <div class="p-2 bg-success bg-opacity-10 text-success rounded-3">
                    <i class="bi bi-check2-circle fs-5"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block"><?= lang('Dashboard.compliance', [$kpi['sla_compliance_rate']]) ?></small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 h-100 border-start border-4 border-danger <?= $kpi['total_escalated'] > 0 ? 'bg-danger bg-opacity-10' : '' ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase"><?= lang('Dashboard.escalated_overdue') ?></span>
                    <h3 class="fw-bold text-danger mt-1 mb-0"><?= number_format($kpi['total_escalated']) ?></h3>
                </div>
                <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-3">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
            </div>
            <small class="text-danger mt-2 d-block fw-semibold">
                <?= $kpi['total_escalated'] > 0 ? lang('Dashboard.urgent_attention') : lang('Dashboard.no_delays') ?>
            </small>
        </div>
    </div>
</div>

<!-- 4 KPI Performance Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-stopwatch fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small"><?= lang('Dashboard.avg_response') ?></div>
                    <div class="fs-5 fw-bold text-dark">
                        <?php 
                            $respMin = $kpi['avg_response_min'];
                            echo $respMin >= 60 ? lang('Dashboard.hours', [round($respMin / 60, 1)]) : lang('Dashboard.minutes', [$respMin]);
                        ?>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;"><?= lang('Dashboard.avg_response_sub') ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-wrench-adjustable-circle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small"><?= lang('Dashboard.avg_resolution') ?></div>
                    <div class="fs-5 fw-bold text-dark">
                        <?php 
                            $resMin = $kpi['avg_resolution_min'];
                            echo $resMin >= 60 ? lang('Dashboard.hours', [round($resMin / 60, 1)]) : lang('Dashboard.minutes', [$resMin]);
                        ?>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;"><?= lang('Dashboard.avg_resolution_sub') ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-shield-check fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small"><?= lang('Dashboard.sla_compliance') ?></div>
                    <div class="fs-5 fw-bold text-success"><?= $kpi['sla_compliance_rate'] ?>%</div>
                    <small class="text-muted" style="font-size: 0.72rem;"><?= lang('Dashboard.sla_compliance_sub') ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small"><?= lang('Dashboard.sparepart_cost') ?></div>
                    <div class="fs-5 fw-bold text-dark">Rp <?= number_format($kpi['total_sparepart_cost'], 0, ',', '.') ?></div>
                    <small class="text-muted" style="font-size: 0.72rem;"><?= lang('Dashboard.sparepart_cost_sub') ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row g-4 mb-4">
    <!-- Chart 1: SLA Compliance & Status Distribution -->
    <div class="col-lg-4">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart text-primary me-2"></i><?= lang('Dashboard.chart_sla_title') ?></h6>
                <span class="badge bg-light text-dark border"><?= lang('Dashboard.chart_sla_badge') ?></span>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="slaStatusChart"></canvas>
            </div>
            <div class="d-flex justify-content-around text-center mt-3 pt-2 border-top">
                <div>
                    <small class="text-muted d-block"><?= lang('WorkOrder.status_completed') ?></small>
                    <span class="fw-bold text-success"><?= $kpi['total_selesai'] ?></span>
                </div>
                <div>
                    <small class="text-muted d-block"><?= lang('WorkOrder.status_process') ?></small>
                    <span class="fw-bold text-warning"><?= $kpi['total_proses'] ?></span>
                </div>
                <div>
                    <small class="text-muted d-block"><?= lang('WorkOrder.status_new') ?></small>
                    <span class="fw-bold text-primary"><?= $kpi['total_baru'] ?></span>
                </div>
                <div>
                    <small class="text-muted d-block"><?= lang('WorkOrder.status_escalated') ?></small>
                    <span class="fw-bold text-danger"><?= $kpi['total_escalated'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Total Biaya Sparepart per Departemen -->
    <div class="col-lg-8">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-fill text-warning me-2"></i><?= lang('Dashboard.chart_dept_title') ?></h6>
                <span class="badge bg-light text-dark border"><?= lang('Dashboard.chart_dept_badge') ?></span>
            </div>
            <div style="height: 280px; position: relative;">
                <canvas id="deptExpenseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2: Top 5 Broken Assets -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tools text-danger me-2"></i><?= lang('Dashboard.chart_assets_title') ?></h6>
                <span class="badge bg-light text-dark border"><?= lang('Dashboard.chart_assets_badge') ?></span>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="brokenAssetsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 5 Breakdown Table -->
    <div class="col-lg-6">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-stars text-primary me-2"></i><?= lang('Dashboard.breakdown_title') ?></h6>
                <a href="<?= base_url('aset') ?>" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem;"><?= lang('Dashboard.all_assets') ?></a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th><?= lang('Aset.col_code') ?></th>
                            <th><?= lang('Aset.col_name') ?></th>
                            <th><?= lang('WorkOrder.col_dept') ?></th>
                            <th class="text-center"><?= current_locale() === 'en' ? 'Incidents' : 'Insiden' ?></th>
                            <th class="text-end"><?= lang('Dashboard.total_cost_label') ?></th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if (empty($topBrokenAssets)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3"><?= lang('Dashboard.no_damage_history') ?></td></tr>
                        <?php else: ?>
                            <?php foreach ($topBrokenAssets as $asset): ?>
                                <tr>
                                    <td><span class="badge bg-dark"><?= esc($asset['kode_aset']) ?></span></td>
                                    <td class="fw-semibold"><?= esc($asset['nama_aset']) ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($asset['nama_departemen']) ?></span></td>
                                    <td class="text-center"><span class="badge bg-danger rounded-pill"><?= lang('Dashboard.damage_incidents', [$asset['jumlah_rusak']]) ?></span></td>
                                    <td class="text-end fw-bold text-success">Rp <?= number_format($asset['total_biaya'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Active Work Orders Table with SLA Countdown -->
<div class="card card-custom p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i><?= lang('Dashboard.recent_wos_title') ?></h5>
            <small class="text-muted"><?= lang('Dashboard.recent_wos_sub') ?></small>
        </div>
        <a href="<?= base_url('workorder') ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
            <?= lang('Dashboard.view_all_wo') ?> <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th><?= lang('WorkOrder.col_code') ?></th>
                    <th><?= lang('WorkOrder.col_title') ?></th>
                    <th><?= lang('WorkOrder.col_dept') ?></th>
                    <th><?= lang('WorkOrder.col_priority') ?></th>
                    <th><?= lang('WorkOrder.col_status') ?></th>
                    <th><?= lang('WorkOrder.col_sla') ?></th>
                    <th><?= lang('WorkOrder.col_handler') ?></th>
                    <th class="text-center"><?= lang('WorkOrder.col_actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentWos)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4"><?= lang('Dashboard.no_recent_wos') ?></td></tr>
                <?php else: ?>
                    <?php foreach ($recentWos as $wo): ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('workorder/detail/' . $wo['id_wo']) ?>" class="fw-bold text-decoration-none text-dark">
                                    <?= esc($wo['kode_wo']) ?>
                                </a>
                                <div class="text-muted small" style="font-size: 0.72rem;"><?= date('d/m/Y H:i', strtotime($wo['tanggal_ajuan'])) ?></div>
                            </td>
                            <td style="max-width: 250px;">
                                <div class="fw-semibold text-truncate"><?= esc($wo['judul_wo']) ?></div>
                                <div class="text-muted small text-truncate" style="font-size: 0.75rem;"><?= esc($wo['deskripsi']) ?></div>
                                <?php if (!empty($wo['nama_aset'])): ?>
                                    <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.68rem;">
                                        <i class="bi bi-tag me-1"></i> <?= esc($wo['nama_aset']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= esc($wo['nama_departemen']) ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                    $pClass = match($wo['tingkat_prioritas']) {
                                        'Emergency' => 'bg-danger',
                                        'High' => 'bg-warning text-dark',
                                        'Medium' => 'bg-info text-dark',
                                        default => 'bg-secondary',
                                    };
                                ?>
                                <span class="badge <?= $pClass ?>"><?= translate_priority($wo['tingkat_prioritas']) ?> (<?= lang('WorkOrder.sla_hours', [$wo['target_jam']]) ?>)</span>
                            </td>
                            <td>
                                <?php
                                    $sClass = match($wo['nama_status']) {
                                        'Baru' => 'bg-primary',
                                        'Accepted', 'Ditugaskan' => 'bg-info text-dark',
                                        'Proses' => 'bg-warning text-dark',
                                        'Selesai' => 'bg-success',
                                        'Ditolak' => 'bg-danger',
                                        'Escalated' => 'bg-danger animate-pulse',
                                        default => 'bg-secondary',
                                    };
                                ?>
                                <span class="badge <?= $sClass ?>"><?= translate_status($wo['nama_status']) ?></span>
                            </td>
                            <td>
                                <?php $sla = $wo['sla_info']; ?>
                                <span class="badge <?= $sla['badge_class'] ?>">
                                    <i class="bi bi-stopwatch me-1"></i> <?= $sla['badge_text'] ?>
                                </span>
                                <?php if ($wo['deadline']): ?>
                                    <div class="text-muted" style="font-size: 0.7rem;"><?= lang('Dashboard.target_label', [date('d/m H:i', strtotime($wo['deadline']))]) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($wo['nama_handler'])): ?>
                                    <span class="badge bg-dark"><?= esc($wo['nama_handler']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic"><?= lang('WorkOrder.unassigned') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('workorder/detail/' . $wo['id_wo']) ?>" class="btn btn-sm btn-outline-primary py-1 px-2" title="<?= lang('Dashboard.detail_btn') ?>">
                                    <i class="bi bi-eye"></i> <?= lang('Dashboard.detail_btn') ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Chart 1: SLA Status Distribution (Doughnut)
        const ctxSla = document.getElementById('slaStatusChart').getContext('2d');
        new Chart(ctxSla, {
            type: 'doughnut',
            data: {
                labels: ['<?= lang('WorkOrder.status_completed') ?>', '<?= lang('WorkOrder.status_process') ?>', '<?= lang('WorkOrder.status_new') ?>', '<?= lang('WorkOrder.status_escalated') ?>'],
                datasets: [{
                    data: [
                        <?= $kpi['total_selesai'] ?>,
                        <?= $kpi['total_proses'] ?>,
                        <?= $kpi['total_baru'] ?>,
                        <?= $kpi['total_escalated'] ?>
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11, family: 'Plus Jakarta Sans' } }
                    }
                },
                cutout: '70%'
            }
        });

        // Chart 2: Total Biaya Sparepart per Departemen (Bar Chart)
        const ctxDept = document.getElementById('deptExpenseChart').getContext('2d');
        const deptLabels = <?= json_encode(array_column($expensesByDept, 'nama_departemen')) ?>;
        const deptExpenses = <?= json_encode(array_map('floatval', array_column($expensesByDept, 'total_biaya'))) ?>;

        new Chart(ctxDept, {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{
                    label: '<?= lang('Dashboard.sparepart_cost') ?> (IDR)',
                    data: deptExpenses,
                    backgroundColor: 'rgba(217, 119, 6, 0.85)',
                    borderColor: '#b45309',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000).toLocaleString() + 'k';
                            },
                            font: { family: 'Plus Jakarta Sans' }
                        }
                    },
                    x: {
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '<?= current_locale() === 'en' ? 'Cost: Rp ' : 'Biaya: Rp ' ?>' + context.parsed.y.toLocaleString('<?= current_locale() === 'en' ? 'en-US' : 'id-ID' ?>');
                            }
                        }
                    }
                }
            }
        });

        // Chart 3: Top 5 Broken Assets (Horizontal Bar)
        const ctxAssets = document.getElementById('brokenAssetsChart').getContext('2d');
        const assetLabels = <?= json_encode(array_column($topBrokenAssets, 'nama_aset')) ?>;
        const assetCounts = <?= json_encode(array_map('intval', array_column($topBrokenAssets, 'jumlah_rusak'))) ?>;

        new Chart(ctxAssets, {
            type: 'bar',
            indexAxis: 'y',
            data: {
                labels: assetLabels.length ? assetLabels : ['<?= current_locale() === 'en' ? 'No Data Yet' : 'Belum Ada Data' ?>'],
                datasets: [{
                    label: '<?= current_locale() === 'en' ? 'Breakdown Frequency' : 'Frekuensi Kerusakan' ?>',
                    data: assetCounts.length ? assetCounts : [0],
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: '#dc2626',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { family: 'Plus Jakarta Sans' } }
                    },
                    y: {
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 10 } }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
