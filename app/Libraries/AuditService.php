<?php

namespace App\Libraries;

use App\Models\LogAuditModel;

class AuditService
{
    protected LogAuditModel $logModel;

    public function __construct()
    {
        $this->logModel = new LogAuditModel();
    }

    public function log(string $aktivitas, string $modul, ?int $userId = null): bool
    {
        $session = session();
        if ($userId === null && $session && $session->has('user_id')) {
            $userId = (int) $session->get('user_id');
        }

        $request = service('request');
        $ip = $request->getIPAddress();
        if (empty($ip)) {
            $ip = '127.0.0.1';
        }

        return (bool) $this->logModel->insert([
            'id_user'    => $userId,
            'aktivitas'  => $aktivitas,
            'modul'      => $modul,
            'ip_address' => $ip,
            'timestamp'  => date('Y-m-d H:i:s'),
        ]);
    }
}
