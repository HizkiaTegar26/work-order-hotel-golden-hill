<?php

namespace App\Services;

use App\Models\WorkOrderModel;
use App\Models\InventarisBarangModel;
use App\Models\WoSparepartsModel;
use App\Libraries\AuditService;
use CodeIgniter\HTTP\Files\UploadedFile;

class WorkOrderCompletionService
{
    protected WorkOrderModel $woModel;
    protected InventarisBarangModel $barangModel;
    protected WoSparepartsModel $sparepartModel;
    protected AuditService $audit;

    public function __construct()
    {
        $this->woModel = new WorkOrderModel();
        $this->barangModel = new InventarisBarangModel();
        $this->sparepartModel = new WoSparepartsModel();
        $this->audit = new AuditService();
    }

    /**
     * Complete a Work Order with pessimistic stock lock deduction and rollback hygiene
     *
     * @param array $wo Work order record
     * @param int $userId Current executing user ID
     * @param int $durasiMenit Working duration in minutes
     * @param string|null $useSpareparts Flag ('1' or '0')
     * @param array|null $sparepartItems Array of ['id_barang' => int, 'qty' => int]
     * @param UploadedFile|null $file Optional uploaded proof photo
     * @return array Result array ['success' => bool, 'error' => ?string, 'total_cost' => float, 'durasi' => int]
     */
    public function completeWorkOrder(
        array $wo,
        int $userId,
        int $durasiMenit,
        ?string $useSpareparts = null,
        ?array $sparepartItems = null,
        ?UploadedFile $file = null
    ): array {
        $id = (int)$wo['id_wo'];

        // 1. Handle file upload if present
        $fotoBuktiName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/selesai', $newName);
            $fotoBuktiName = 'uploads/selesai/' . $newName;
        }

        // 2. Transactional execution with pessimistic stock locking
        $totalCost = 0.00;
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            if ($useSpareparts === '1' && !empty($sparepartItems) && is_array($sparepartItems)) {
                foreach ($sparepartItems as $part) {
                    $idBarang = (int) ($part['id_barang'] ?? 0);
                    $qty = (int) ($part['qty'] ?? 0);

                    if ($idBarang > 0 && $qty > 0) {
                        // Pessimistic Lock Row via SELECT ... FOR UPDATE
                        $lockResult = $this->barangModel->deductStockPessimistic($idBarang, $qty);
                        if (!$lockResult['success']) {
                            // Rollback transaction immediately on insufficient stock & clean up uploaded photo
                            $db->transRollback();
                            if ($fotoBuktiName && file_exists(FCPATH . $fotoBuktiName)) {
                                @unlink(FCPATH . $fotoBuktiName);
                            }
                            return [
                                'success' => false,
                                'error'   => "Stok barang tidak mencukupi: " . $lockResult['error'],
                            ];
                        }

                        $item = $lockResult['item'];
                        $subtotal = $qty * (float)$item['harga_satuan'];
                        $totalCost += $subtotal;

                        $this->sparepartModel->insert([
                            'id_wo'          => $id,
                            'id_barang'      => $idBarang,
                            'jumlah'         => $qty,
                            'harga_subtotal' => $subtotal,
                        ]);

                        $this->audit->log("Pengurangan stok inventaris {$item['nama_barang']} sebanyak {$qty} {$item['satuan']} (Pessimistic Lock) untuk WO {$wo['kode_wo']}", 'Inventaris', $userId);
                    }
                }
            }

            // 3. Update Work Order status to Selesai (6)
            $this->woModel->update($id, [
                'id_status'               => 6, // Selesai
                'foto_bukti_selesai'      => $fotoBuktiName,
                'durasi_menit_pengerjaan' => $durasiMenit > 0 ? $durasiMenit : 30,
                'total_biaya_sparepart'   => $totalCost,
            ]);

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            if ($fotoBuktiName && file_exists(FCPATH . $fotoBuktiName)) {
                @unlink(FCPATH . $fotoBuktiName);
            }
            return [
                'success' => false,
                'error'   => "Gagal menyelesaikan Work Order: " . $e->getMessage(),
            ];
        }

        $durasiFinal = $durasiMenit > 0 ? $durasiMenit : 30;
        $this->audit->log("Menyelesaikan Work Order {$wo['kode_wo']} dengan durasi {$durasiFinal} menit dan total biaya sparepart Rp " . number_format($totalCost, 0, ',', '.'), 'Work Order', $userId);

        return [
            'success'    => true,
            'error'      => null,
            'total_cost' => $totalCost,
            'durasi'     => $durasiFinal,
        ];
    }
}
