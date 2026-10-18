<?php

namespace App\Models;

use CodeIgniter\Model;

class LogAuditModel extends Model
{
    protected $table = 'log_audit';
    protected $primaryKey = 'id_log';
    protected $allowedFields = [
        'id_user',
        'aktivitas',
        'modul',
        'ip_address',
        'timestamp',
    ];
    protected $returnType = 'array';

    public function getLogsWithUser($limit = 500)
    {
        return $this->select('log_audit.*, users.nama_lengkap, users.username, roles.nama_role')
                    ->join('users', 'users.id_user = log_audit.id_user', 'left')
                    ->join('roles', 'roles.id_role = users.id_role', 'left')
                    ->orderBy('log_audit.timestamp', 'DESC')
                    ->findAll($limit);
    }
}
