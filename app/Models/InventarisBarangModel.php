<?php

namespace App\Models;

use CodeIgniter\Model;

class InventarisBarangModel extends Model
{
    protected $table = 'inventaris_barang';
    protected $primaryKey = 'id_barang';
    protected $allowedFields = [
        'kode_barang',
        'nama_barang',
        'stok',
        'satuan',
        'harga_satuan',
    ];
    protected $returnType = 'array';

    /**
     * Concurrency-safe stock deduction using Pessimistic Locking (SELECT ... FOR UPDATE)
     */
    public function deductStockSafely($id_barang, $qty)
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        $query = $db->query("SELECT * FROM inventaris_barang WHERE id_barang = ? FOR UPDATE", [(int)$id_barang]);
        $item = $query->getRowArray();

        if (!$item || (int)$item['stok'] < (int)$qty) {
            $db->transRollback();
            return false;
        }

        $newStock = (int)$item['stok'] - (int)$qty;
        $db->query("UPDATE inventaris_barang SET stok = ? WHERE id_barang = ?", [$newStock, (int)$id_barang]);

        $db->transCommit();
        return true;
    }

    /**
     * Concurrency-safe stock deduction with detailed validation and error info
     *
     * @param int $id_barang
     * @param int $qty
     * @return array
     */
    public function deductStockPessimistic(int $id_barang, int $qty): array
    {
        $db = \Config\Database::connect();
        
        // Execute SELECT ... FOR UPDATE to lock the row exclusively against concurrent transactions
        $query = $db->query("SELECT * FROM inventaris_barang WHERE id_barang = ? FOR UPDATE", [$id_barang]);
        $item = $query->getRowArray();

        if (!$item) {
            return [
                'success'   => false,
                'item'      => null,
                'remaining' => 0,
                'error'     => 'Barang inventaris tidak ditemukan dalam database.',
            ];
        }

        if ((int)$item['stok'] < $qty) {
            return [
                'success'   => false,
                'item'      => $item,
                'remaining' => (int)$item['stok'],
                'error'     => "Stok barang '{$item['nama_barang']}' ({$item['kode_barang']}) tidak mencukupi. Sisa stok tersedia: {$item['stok']} {$item['satuan']}, namun permintaan pengerjaan: {$qty} {$item['satuan']}.",
            ];
        }

        $newStock = (int)$item['stok'] - $qty;
        $db->query("UPDATE inventaris_barang SET stok = ? WHERE id_barang = ?", [$newStock, $id_barang]);

        return [
            'success'   => true,
            'item'      => $item,
            'remaining' => $newStock,
            'error'     => null,
        ];
    }
}
