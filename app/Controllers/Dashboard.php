<?php

namespace App\Controllers;

use App\Models\WorkOrderModel;
use App\Models\AsetHotelModel;
use App\Models\DepartemenModel;
use App\Libraries\SlaService;

class Dashboard extends BaseController
{
    protected WorkOrderModel $woModel;
    protected AsetHotelModel $asetModel;
    protected DepartemenModel $deptModel;
    protected SlaService $slaService;

    public function __construct()
    {
        $this->woModel = new WorkOrderModel();
        $this->asetModel = new AsetHotelModel();
        $this->deptModel = new DepartemenModel();
        $this->slaService = new SlaService();
    }

    public function index()
    {
        // Automatically check for any overdue orders in background
        $this->slaService->checkAndEscalateOverdue();

        $kpi = $this->woModel->getKpiSummary();
        $expensesByDept = $this->woModel->getExpensesByDepartment();
        $topBrokenAssets = $this->woModel->getTopBrokenAssets(5);
        $monthlyTrend = $this->woModel->getMonthlyPerformanceTrend();

        // Recent Work Orders
        $userRole = session()->get('nama_role');
        $userId = session()->get('user_id');
        $deptId = session()->get('id_departemen');

        $filters = [];
        if ($userRole === 'Staf') {
            // Staff sees their own submissions or assigned to them
            $recentWos = $this->woModel->getWorkOrdersWithRelations(null, ['user_id' => $userId]);
            $assignedWos = $this->woModel->getWorkOrdersWithRelations(null, ['assigned_to' => $userId]);
            $recentWos = array_merge($recentWos, $assignedWos);
            // Deduplicate by id_wo
            $seen = [];
            $recentWos = array_filter($recentWos, function($item) use (&$seen) {
                if (isset($seen[$item['id_wo']])) return false;
                $seen[$item['id_wo']] = true;
                return true;
            });
        } elseif ($userRole === 'Kepala Departemen') {
            $recentWos = $this->woModel->getWorkOrdersWithRelations(null, ['departemen' => $deptId]);
        } else {
            // Admin sees all
            $recentWos = $this->woModel->getWorkOrdersWithRelations(null, []);
        }

        // Slice to latest 10
        $recentWos = array_slice($recentWos, 0, 10);

        // Attach SLA info to each WO
        foreach ($recentWos as &$wo) {
            $wo['sla_info'] = $this->slaService->getSlaInfo($wo);
        }
        unset($wo);

        return view('dashboard/index', [
            'title'            => 'Executive KPI & Analytics Dashboard',
            'kpi'              => $kpi,
            'expensesByDept'   => $expensesByDept,
            'topBrokenAssets'  => $topBrokenAssets,
            'monthlyTrend'     => $monthlyTrend,
            'recentWos'        => $recentWos,
        ]);
    }
}
