<?php

namespace App\Models;

use CodeIgniter\Model;

class WoSparepartsModel extends Model
{
    protected $table = 'wo_spareparts';
    protected $primaryKey = 'id_usage';
    protected $allowedFields = [
        'id_wo',
        'id_barang',
        'jumlah',
        'harga_subtotal',
    ];
    protected $returnType = 'array';

    public function getPartsByWo($id_wo)
    {
        return $this->select('wo_spareparts.*, inventaris_barang.nama_barang, inventaris_barang.kode_barang, inventaris_barang.satuan, inventaris_barang.harga_satuan')
                    ->join('inventaris_barang', 'inventaris_barang.id_barang = wo_spareparts.id_barang', 'left')
                    ->where('wo_spareparts.id_wo', $id_wo)
                    ->findAll();
    }
}
