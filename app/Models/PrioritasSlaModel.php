<?php

namespace App\Models;

use CodeIgniter\Model;

class PrioritasSlaModel extends Model
{
    protected $table = 'prioritas_sla';
    protected $primaryKey = 'id_prioritas';
    protected $allowedFields = ['tingkat_prioritas', 'target_jam'];
    protected $returnType = 'array';
    protected $afterInsert = ['clearCache'];
    protected $afterUpdate = ['clearCache'];
    protected $afterDelete = ['clearCache'];

    /**
     * Get all SLA priorities from cache or database (TTL: 1 hour)
     */
    public function getAllCached(int $ttl = 3600): array
    {
        $cached = cache('master_prioritas_sla');
        if ($cached !== null) {
            return $cached;
        }

        $data = $this->orderBy('target_jam', 'ASC')->findAll();
        cache()->save('master_prioritas_sla', $data, $ttl);
        return $data;
    }

    public function clearCache(array $data)
    {
        cache()->delete('master_prioritas_sla');
        return $data;
    }
}
