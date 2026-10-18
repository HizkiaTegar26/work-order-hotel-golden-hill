<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\SlaService;

class EscalateOverdueWorkOrders extends BaseCommand
{
    protected $group       = 'WorkOrder';
    protected $name        = 'wo:escalate';
    protected $description = 'Pindai dan eskalasikan secara otomatis Work Order yang melewati batas SLA.';

    public function run(array $params)
    {
        CLI::write("Memeriksa status SLA Work Order...", 'yellow');
        $slaService = new SlaService();
        $escalated = $slaService->checkAndEscalateOverdue();

        if (empty($escalated)) {
            CLI::write("Tidak ada Work Order overdue baru yang perlu dieskalasi.", 'green');
        } else {
            CLI::write("Berhasil mengeskalasi " . count($escalated) . " Work Order:", 'red');
            foreach ($escalated as $item) {
                CLI::write(" - " . $item, 'light_red');
            }
        }
    }
}
