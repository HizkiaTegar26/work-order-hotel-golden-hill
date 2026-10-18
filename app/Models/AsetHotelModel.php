<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetHotelModel extends Model
{
    protected $table = 'aset_hotel';
    protected $primaryKey = 'id_aset';
    protected $allowedFields = [
        'kode_aset',
        'nama_aset',
        'id_departemen',
        'lokasi_spesifik',
        'qr_code_path',
        'kondisi',
        'created_at',
    ];
    protected $returnType = 'array';

    public function getAssetsWithDept($id = null)
    {
        $builder = $this->select('aset_hotel.*, departemen.nama_departemen, departemen.kode_dept')
                        ->join('departemen', 'departemen.id_departemen = aset_hotel.id_departemen', 'left');

        if ($id !== null) {
            return $builder->where('aset_hotel.id_aset', $id)->first();
        }

        return $builder->orderBy('aset_hotel.id_aset', 'DESC')->findAll();
    }

    public function getAssetByCode($code)
    {
        return $this->select('aset_hotel.*, departemen.nama_departemen, departemen.kode_dept')
                    ->join('departemen', 'departemen.id_departemen = aset_hotel.id_departemen', 'left')
                    ->where('aset_hotel.kode_aset', $code)
                    ->first();
    }

    public function getRepairHistory($id_aset)
    {
        $db = \Config\Database::connect();
        return $db->table('work_order')
                  ->select('work_order.*, status_wo.nama_status, prioritas_sla.tingkat_prioritas, users.nama_lengkap as pengaju_nama, handler.nama_lengkap as handler_nama')
                  ->join('status_wo', 'status_wo.id_status = work_order.id_status', 'left')
                  ->join('prioritas_sla', 'prioritas_sla.id_prioritas = work_order.id_prioritas', 'left')
                  ->join('users', 'users.id_user = work_order.id_user', 'left')
                  ->join('users as handler', 'handler.id_user = work_order.assigned_to', 'left')
                  ->where('work_order.id_aset', $id_aset)
                  ->orderBy('work_order.tanggal_ajuan', 'DESC')
                  ->get()
                  ->getResultArray();
    }
}
