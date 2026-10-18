<?php

namespace App\Controllers;

use App\Models\LogAuditModel;

class Audit extends BaseController
{
    protected LogAuditModel $auditModel;

    public function __construct()
    {
        $this->auditModel = new LogAuditModel();
    }

    public function index()
    {
        $logs = $this->auditModel->getLogsWithUser(1000);

        return view('audit/index', [
            'title' => 'Audit Trail & Activity Log',
            'logs'  => $logs,
        ]);
    }
}
