<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $allowedFields = [
        'nama_lengkap',
        'username',
        'password',
        'email',
        'no_whatsapp',
        'id_departemen',
        'id_role',
        'status_aktif',
        'preferred_language',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    public function getUserWithRelations($id = null)
    {
        $builder = $this->select('users.*, roles.nama_role, departemen.nama_departemen, departemen.kode_dept')
                        ->join('roles', 'roles.id_role = users.id_role', 'left')
                        ->join('departemen', 'departemen.id_departemen = users.id_departemen', 'left');

        if ($id !== null) {
            return $builder->where('users.id_user', $id)->first();
        }

        return $builder->orderBy('users.id_user', 'ASC')->findAll();
    }

    public function getStaffByDepartment($id_dept)
    {
        return $this->select('users.*, roles.nama_role')
                    ->join('roles', 'roles.id_role = users.id_role')
                    ->where('users.id_departemen', $id_dept)
                    ->where('users.status_aktif', 1)
                    ->where('roles.nama_role', 'Staf')
                    ->findAll();
    }

    public function getHeadOfDepartment($id_dept)
    {
        return $this->select('users.*, roles.nama_role')
                    ->join('roles', 'roles.id_role = users.id_role')
                    ->where('users.id_departemen', $id_dept)
                    ->where('users.status_aktif', 1)
                    ->where('roles.nama_role', 'Kepala Departemen')
                    ->first();
    }
}
