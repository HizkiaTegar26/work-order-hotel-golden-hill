<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusWoModel extends Model
{
    protected $table = 'status_wo';
    protected $primaryKey = 'id_status';
    protected $allowedFields = ['nama_status'];
    protected $returnType = 'array';
    protected $afterInsert = ['clearCache'];
    protected $afterUpdate = ['clearCache'];
    protected $afterDelete = ['clearCache'];

    /**
     * Get all statuses from cache or database (TTL: 1 hour)
     */
    public function getAllCached(int $ttl = 3600): array
    {
        $cached = cache('master_status_wo');
        if ($cached !== null) {
            return $cached;
        }

        $data = $this->orderBy('id_status', 'ASC')->findAll();
        cache()->save('master_status_wo', $data, $ttl);
        return $data;
    }

    public function clearCache(array $data)
    {
        cache()->delete('master_status_wo');
        return $data;
    }
}
