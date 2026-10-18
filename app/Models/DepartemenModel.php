<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartemenModel extends Model
{
    protected $table = 'departemen';
    protected $primaryKey = 'id_departemen';
    protected $allowedFields = ['nama_departemen', 'kode_dept'];
    protected $returnType = 'array';
    protected $afterInsert = ['clearCache'];
    protected $afterUpdate = ['clearCache'];
    protected $afterDelete = ['clearCache'];

    /**
     * Get all departments from cache or database (TTL: 1 hour)
     */
    public function getAllCached(int $ttl = 3600): array
    {
        $cached = cache('master_departemen');
        if ($cached !== null) {
            return $cached;
        }

        $data = $this->orderBy('nama_departemen', 'ASC')->findAll();
        cache()->save('master_departemen', $data, $ttl);
        return $data;
    }

    /**
     * Invalidate cached departments
     */
    public function clearCache(array $data)
    {
        cache()->delete('master_departemen');
        return $data;
    }
}
