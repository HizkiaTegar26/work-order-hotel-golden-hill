<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderModel extends Model
{
    protected $table = 'work_order';
    protected $primaryKey = 'id_wo';
    protected $allowedFields = [
        'kode_wo',
        'judul_wo',
        'deskripsi',
        'id_user',
        'id_kategori',
        'id_status',
        'id_prioritas',
        'id_departemen',
        'id_aset',
        'foto_trouble',
        'foto_bukti_selesai',
        'tanggal_ajuan',
        'deadline',
        'assigned_to',
        'verified_by',
        'verified_at',
        'reject_note',
        'total_biaya_sparepart',
        'durasi_menit_pengerjaan',
    ];
    protected $returnType = 'array';

    public function generateKodeWo()
    {
        $prefix = 'WO-' . date('Ymd') . '-';
        $last = $this->like('kode_wo', $prefix, 'after')
                     ->orderBy('id_wo', 'DESC')
                     ->first();

        if ($last) {
            $lastNumber = (int) substr($last['kode_wo'], -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    public function getWorkOrdersWithRelations($id = null, $filters = [])
    {
        $builder = $this->select('
                work_order.*,
                kategori_wo.nama_kategori,
                status_wo.nama_status,
                prioritas_sla.tingkat_prioritas,
                prioritas_sla.target_jam,
                departemen.nama_departemen,
                departemen.kode_dept,
                aset_hotel.nama_aset,
                aset_hotel.kode_aset,
                aset_hotel.lokasi_spesifik as aset_lokasi,
                pengaju.nama_lengkap as nama_pengaju,
                pengaju.email as email_pengaju,
                pengaju.no_whatsapp as wa_pengaju,
                handler.nama_lengkap as nama_handler,
                handler.no_whatsapp as wa_handler,
                kadep.nama_lengkap as nama_kadep
            ')
            ->join('kategori_wo', 'kategori_wo.id_kategori = work_order.id_kategori', 'left')
            ->join('status_wo', 'status_wo.id_status = work_order.id_status', 'left')
            ->join('prioritas_sla', 'prioritas_sla.id_prioritas = work_order.id_prioritas', 'left')
            ->join('departemen', 'departemen.id_departemen = work_order.id_departemen', 'left')
            ->join('aset_hotel', 'aset_hotel.id_aset = work_order.id_aset', 'left')
            ->join('users as pengaju', 'pengaju.id_user = work_order.id_user', 'left')
            ->join('users as handler', 'handler.id_user = work_order.assigned_to', 'left')
            ->join('users as kadep', 'kadep.id_user = work_order.verified_by', 'left');

        if ($id !== null) {
            return $builder->where('work_order.id_wo', $id)->first();
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('work_order.id_status', $filters['status']);
        }
        if (!empty($filters['departemen'])) {
            $builder->where('work_order.id_departemen', $filters['departemen']);
        }
        if (!empty($filters['prioritas'])) {
            $builder->where('work_order.id_prioritas', $filters['prioritas']);
        }
        if (!empty($filters['kategori'])) {
            $builder->where('work_order.id_kategori', $filters['kategori']);
        }
        if (!empty($filters['user_id'])) {
            $builder->where('work_order.id_user', $filters['user_id']);
        }
        if (!empty($filters['assigned_to'])) {
            $builder->where('work_order.assigned_to', $filters['assigned_to']);
        }
        if (!empty($filters['start_date'])) {
            $builder->where('work_order.tanggal_ajuan >=', $filters['start_date'] . ' 00:00:00');
        }
        if (!empty($filters['end_date'])) {
            $builder->where('work_order.tanggal_ajuan <=', $filters['end_date'] . ' 23:59:59');
        }

        return $builder->orderBy('work_order.id_wo', 'DESC')->findAll();
    }

    /**
     * Get Work Orders needing auto escalation:
     * - Status is not 'Selesai' (6) and not 'Ditolak' (3) and not already 'Escalated' (8)
     * - deadline is past now
     */
    public function getOverdueWorkOrders()
    {
        $now = date('Y-m-d H:i:s');
        return $this->select('work_order.*, departemen.nama_departemen, prioritas_sla.tingkat_prioritas, pengaju.nama_lengkap as nama_pengaju')
                    ->join('departemen', 'departemen.id_departemen = work_order.id_departemen', 'left')
                    ->join('prioritas_sla', 'prioritas_sla.id_prioritas = work_order.id_prioritas', 'left')
                    ->join('users as pengaju', 'pengaju.id_user = work_order.id_user', 'left')
                    ->whereNotIn('work_order.id_status', [3, 6, 8])
                    ->where('work_order.deadline IS NOT NULL')
                    ->where('work_order.deadline <', $now)
                    ->findAll();
    }

    // Analytics Methods
    public function getKpiSummary()
    {
        $db = \Config\Database::connect();

        // Total WO counts
        $totalWo = $this->countAllResults();
        $totalSelesai = $this->where('id_status', 6)->countAllResults();
        $totalProses = $this->whereIn('id_status', [4, 5])->countAllResults();
        $totalBaru = $this->where('id_status', 1)->countAllResults();
        $totalEscalated = $this->where('id_status', 8)->countAllResults();

        // Average Response Time (minutes from tanggal_ajuan to verified_at)
        $avgResponseQuery = $db->query("
            SELECT AVG(TIMESTAMPDIFF(MINUTE, tanggal_ajuan, verified_at)) as avg_response_min
            FROM work_order
            WHERE verified_at IS NOT NULL
        ")->getRowArray();
        $avgResponseMin = round((float) ($avgResponseQuery['avg_response_min'] ?? 0), 1);

        // Average Resolution Time (minutes of durasi_menit_pengerjaan for completed WOs)
        $avgResolutionQuery = $db->query("
            SELECT AVG(durasi_menit_pengerjaan) as avg_resolution_min
            FROM work_order
            WHERE id_status = 6 AND durasi_menit_pengerjaan IS NOT NULL
        ")->getRowArray();
        $avgResolutionMin = round((float) ($avgResolutionQuery['avg_resolution_min'] ?? 0), 1);

        // SLA Compliance Rate
        // Completed within deadline: id_status = 6 AND (verified_at + durasi <= deadline OR verified_at <= deadline)
        // Or simply: total finished where completion did not exceed deadline
        $totalCompleted = $db->query("SELECT COUNT(*) as cnt FROM work_order WHERE id_status = 6")->getRowArray()['cnt'] ?? 0;
        $onTimeCompleted = $db->query("
            SELECT COUNT(*) as cnt FROM work_order 
            WHERE id_status = 6 AND (deadline IS NULL OR (verified_at IS NOT NULL AND DATE_ADD(verified_at, INTERVAL IFNULL(durasi_menit_pengerjaan, 0) MINUTE) <= deadline))
        ")->getRowArray()['cnt'] ?? 0;

        $slaComplianceRate = $totalCompleted > 0 ? round(($onTimeCompleted / $totalCompleted) * 100, 1) : 100;

        // Total Sparepart Expenses
        $totalSparepartQuery = $db->query("SELECT SUM(total_biaya_sparepart) as total_biaya FROM work_order")->getRowArray();
        $totalSparepartCost = (float) ($totalSparepartQuery['total_biaya'] ?? 0);

        return [
            'total_wo' => $totalWo,
            'total_selesai' => $totalSelesai,
            'total_proses' => $totalProses,
            'total_baru' => $totalBaru,
            'total_escalated' => $totalEscalated,
            'avg_response_min' => $avgResponseMin,
            'avg_resolution_min' => $avgResolutionMin,
            'sla_compliance_rate' => $slaComplianceRate,
            'total_sparepart_cost' => $totalSparepartCost,
        ];
    }

    public function getExpensesByDepartment()
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT departemen.nama_departemen, departemen.kode_dept, IFNULL(SUM(work_order.total_biaya_sparepart), 0) as total_biaya
            FROM departemen
            LEFT JOIN work_order ON work_order.id_departemen = departemen.id_departemen
            GROUP BY departemen.id_departemen, departemen.nama_departemen, departemen.kode_dept
            ORDER BY total_biaya DESC
        ")->getResultArray();
    }

    public function getTopBrokenAssets($limit = 5)
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT aset_hotel.id_aset, aset_hotel.kode_aset, aset_hotel.nama_aset, aset_hotel.lokasi_spesifik, departemen.nama_departemen,
                   COUNT(work_order.id_wo) as jumlah_rusak,
                   IFNULL(SUM(work_order.total_biaya_sparepart), 0) as total_biaya
            FROM aset_hotel
            JOIN work_order ON work_order.id_aset = aset_hotel.id_aset
            LEFT JOIN departemen ON departemen.id_departemen = aset_hotel.id_departemen
            GROUP BY aset_hotel.id_aset, aset_hotel.kode_aset, aset_hotel.nama_aset, aset_hotel.lokasi_spesifik, departemen.nama_departemen
            ORDER BY jumlah_rusak DESC, total_biaya DESC
            LIMIT ?
        ", [$limit])->getResultArray();
    }

    public function getMonthlyPerformanceTrend()
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT DATE_FORMAT(tanggal_ajuan, '%Y-%m') as bulan,
                   COUNT(id_wo) as total_wo,
                   SUM(CASE WHEN id_status = 6 THEN 1 ELSE 0 END) as selesai,
                   SUM(CASE WHEN id_status = 8 THEN 1 ELSE 0 END) as escalated
            FROM work_order
            GROUP BY DATE_FORMAT(tanggal_ajuan, '%Y-%m')
            ORDER BY bulan ASC
            LIMIT 12
        ")->getResultArray();
    }
}
