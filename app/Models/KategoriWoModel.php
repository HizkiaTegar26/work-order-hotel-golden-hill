<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriWoModel extends Model
{
    protected $table = 'kategori_wo';
    protected $primaryKey = 'id_kategori';
    protected $allowedFields = ['nama_kategori'];
    protected $returnType = 'array';
    protected $afterInsert = ['clearCache'];
    protected $afterUpdate = ['clearCache'];
    protected $afterDelete = ['clearCache'];

    /**
     * Get all categories from cache or database (TTL: 1 hour)
     */
    public function getAllCached(int $ttl = 3600): array
    {
        $cached = cache('master_kategori_wo');
        if ($cached !== null) {
            return $cached;
        }

        $data = $this->orderBy('id_kategori', 'ASC')->findAll();
        cache()->save('master_kategori_wo', $data, $ttl);
        return $data;
    }

    public function clearCache(array $data)
    {
        cache()->delete('master_kategori_wo');
        return $data;
    }
}
