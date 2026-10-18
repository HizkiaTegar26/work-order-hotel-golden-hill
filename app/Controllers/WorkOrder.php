<?php

namespace App\Controllers;

use App\Models\WorkOrderModel;
use App\Models\KategoriWoModel;
use App\Models\StatusWoModel;
use App\Models\PrioritasSlaModel;
use App\Models\DepartemenModel;
use App\Models\AsetHotelModel;
use App\Models\InventarisBarangModel;
use App\Models\WoSparepartsModel;
use App\Models\UserModel;
use App\Libraries\AuditService;
use App\Libraries\WhatsAppService;
use App\Libraries\NotificationService;
use App\Libraries\SlaService;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use Dompdf\Dompdf;
use Dompdf\Options;

class WorkOrder extends BaseController
{
    protected WorkOrderModel $woModel;
    protected KategoriWoModel $kategoriModel;
    protected StatusWoModel $statusModel;
    protected PrioritasSlaModel $prioritasModel;
    protected DepartemenModel $deptModel;
    protected AsetHotelModel $asetModel;
    protected InventarisBarangModel $barangModel;
    protected WoSparepartsModel $sparepartModel;
    protected UserModel $userModel;
    protected AuditService $audit;
    protected WhatsAppService $wa;
    protected NotificationService $notification;
    protected SlaService $sla;

    public function __construct()
    {
        $this->woModel = new WorkOrderModel();
        $this->kategoriModel = new KategoriWoModel();
        $this->statusModel = new StatusWoModel();
        $this->prioritasModel = new PrioritasSlaModel();
        $this->deptModel = new DepartemenModel();
        $this->asetModel = new AsetHotelModel();
        $this->barangModel = new InventarisBarangModel();
        $this->sparepartModel = new WoSparepartsModel();
        $this->userModel = new UserModel();
        $this->audit = new AuditService();
        $this->wa = new WhatsAppService();
        $this->notification = new NotificationService();
        $this->sla = new SlaService();
    }

    public function index()
    {
        // Run SLA check
        $this->sla->checkAndEscalateOverdue();

        $roleId = (int) session()->get('id_role');
        $userId = (int) session()->get('user_id');
        $deptId = (int) session()->get('id_departemen');

        $filterStatus = $this->request->getGet('status');
        $filterDept   = $this->request->getGet('dept');
        $filterPrio   = $this->request->getGet('prio');
        $filterUser   = $this->request->getGet('user');

        $filters = [];
        if (!empty($filterStatus)) $filters['status'] = $filterStatus;
        if (!empty($filterDept))   $filters['departemen'] = $filterDept;
        if (!empty($filterPrio))   $filters['prioritas'] = $filterPrio;

        if ($filterUser === 'me') {
            $filters['user_id'] = $userId;
        }

        // Apply role boundaries
        if ($roleId === 3 && $filterUser !== 'all') { // Staf view
            // If specific user not requested, show their department or their own tasks
            // Keep filter open if they want to view their department list
        } elseif ($roleId === 2 && empty($filterDept)) { // Kadep view
            // Kadep defaults to seeing work orders directed to their department OR initiated by them
            $filters['departemen'] = $deptId;
        }

        $workOrders = $this->woModel->getWorkOrdersWithRelations(null, $filters);

        // Attach SLA info
        foreach ($workOrders as &$wo) {
            $wo['sla_info'] = $this->sla->getSlaInfo($wo);
        }
        unset($wo);

        return view('work_order/index', [
            'title'       => 'Daftar Work Order',
            'workOrders'  => $workOrders,
            'kategori'    => $this->kategoriModel->getAllCached(),
            'statusList'  => $this->statusModel->getAllCached(),
            'prioritas'   => $this->prioritasModel->getAllCached(),
            'departemen'  => $this->deptModel->getAllCached(),
            'currentFilters' => [
                'status' => $filterStatus,
                'dept'   => $filterDept,
                'prio'   => $filterPrio,
                'user'   => $filterUser,
            ],
        ]);
    }

    public function create()
    {
        $idAset = $this->request->getGet('id_aset');
        $selectedAsset = null;
        if (!empty($idAset)) {
            $selectedAsset = $this->asetModel->getAssetsWithDept($idAset);
        }

        return view('work_order/create', [
            'title'         => 'Ajukan Work Order Baru',
            'kategori'      => $this->kategoriModel->getAllCached(),
            'prioritas'     => $this->prioritasModel->getAllCached(),
            'departemen'    => $this->deptModel->getAllCached(),
            'assets'        => $this->asetModel->getAssetsWithDept(),
            'selectedAsset' => $selectedAsset,
        ]);
    }

    public function store()
    {
        $validationRule = [
            'judul_wo'      => 'required|min_length[5]|max_length[150]',
            'deskripsi'     => 'required|min_length[10]',
            'id_kategori'   => 'required|is_not_unique[kategori_wo.id_kategori]',
            'id_prioritas'  => 'required|is_not_unique[prioritas_sla.id_prioritas]',
            'id_departemen' => 'required|is_not_unique[departemen.id_departemen]',
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->withInput()->with('error', 'Harap lengkapi semua kolom dengan benar.');
        }

        $userId = (int) session()->get('user_id');
        $kodeWo = $this->woModel->generateKodeWo();
        $idPrioritas = (int) $this->request->getPost('id_prioritas');
        $idDept = (int) $this->request->getPost('id_departemen');
        $idAset = $this->request->getPost('id_aset');
        if (empty($idAset)) $idAset = null;

        // SLA calculation
        $prio = $this->prioritasModel->find($idPrioritas);
        $targetJam = (int) ($prio['target_jam'] ?? 24);
        $now = date('Y-m-d H:i:s');
        $deadline = $this->sla->calculateDeadline($now, $targetJam);

        // Handle Photo Upload
        $fotoTroubleName = null;
        $file = $this->request->getFile('foto_trouble');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/trouble', $newName);
            $fotoTroubleName = 'uploads/trouble/' . $newName;
        }

        $woData = [
            'kode_wo'                 => $kodeWo,
            'judul_wo'                => trim($this->request->getPost('judul_wo')),
            'deskripsi'               => trim($this->request->getPost('deskripsi')),
            'id_user'                 => $userId,
            'id_kategori'             => (int) $this->request->getPost('id_kategori'),
            'id_status'               => 1, // Baru
            'id_prioritas'            => $idPrioritas,
            'id_departemen'           => $idDept,
            'id_aset'                 => $idAset,
            'foto_trouble'            => $fotoTroubleName,
            'foto_bukti_selesai'      => null,
            'tanggal_ajuan'           => $now,
            'deadline'                => $deadline,
            'assigned_to'             => null,
            'verified_by'             => null,
            'verified_at'             => null,
            'reject_note'             => null,
            'total_biaya_sparepart'   => 0.00,
            'durasi_menit_pengerjaan' => null,
        ];

        $insertedId = $this->woModel->insert($woData);

        // Audit Log
        $this->audit->log("Mengajukan Work Order baru: {$kodeWo} - {$woData['judul_wo']}", 'Work Order', $userId);

        // Asynchronous Notification Queue to Kadep of target department
        $kadep = $this->userModel->getHeadOfDepartment($idDept);
        if ($kadep) {
            $woData['nama_pengaju'] = session()->get('nama_lengkap');
            $woData['tingkat_prioritas'] = $prio['tingkat_prioritas'] ?? 'SLA';
            $this->notification->notifyNewWorkOrder($woData, $kadep);
        }

        return redirect()->to(base_url('workorder/detail/' . $insertedId))->with('success', "Work Order {$kodeWo} berhasil diajukan dan sedang menunggu verifikasi Kepala Departemen.");
    }

    public function detail($id)
    {
        $wo = $this->woModel->getWorkOrdersWithRelations($id);
        if (!$wo) {
            return redirect()->to(base_url('workorder'))->with('error', 'Work Order tidak ditemukan.');
        }

        $wo['sla_info'] = $this->sla->getSlaInfo($wo);

        // Spare parts used
        $sparepartsUsed = $this->sparepartModel->getPartsByWo($id);

        // Available spare parts for completion modal
        $availableItems = $this->barangModel->where('stok >', 0)->findAll();

        // Staff members for assignment modal (from responsible department)
        $staffMembers = $this->userModel->getStaffByDepartment($wo['id_departemen']);

        // Priorities list for SLA change modal
        $priorities = $this->prioritasModel->findAll();

        return view('work_order/detail', [
            'title'          => 'Detail Work Order: ' . $wo['kode_wo'],
            'wo'             => $wo,
            'sparepartsUsed' => $sparepartsUsed,
            'availableItems' => $availableItems,
            'staffMembers'   => $staffMembers,
            'priorities'     => $priorities,
        ]);
    }

    public function edit($id)
    {
        $wo = $this->woModel->find($id);
        if (!$wo) {
            return redirect()->to(base_url('workorder'))->with('error', 'Work Order tidak ditemukan.');
        }

        $roleId = (int) session()->get('id_role');
        $userId = (int) session()->get('user_id');

        // Only allow edit if status is 'Baru' (1) and user is pengaju or Admin
        if ((int)$wo['id_status'] !== 1 && $roleId !== 1) {
            return redirect()->to(base_url('workorder/detail/' . $id))->with('error', 'Work Order yang sudah diproses atau disetujui tidak dapat diedit.');
        }

        if ((int)$wo['id_user'] !== $userId && $roleId !== 1) {
            return redirect()->to(base_url('workorder/detail/' . $id))->with('error', 'Anda hanya dapat mengedit Work Order milik Anda sendiri.');
        }

        return view('work_order/edit', [
            'title'      => 'Edit Work Order: ' . $wo['kode_wo'],
            'wo'         => $wo,
            'kategori'   => $this->kategoriModel->getAllCached(),
            'prioritas'  => $this->prioritasModel->getAllCached(),
            'departemen' => $this->deptModel->getAllCached(),
            'assets'     => $this->asetModel->getAssetsWithDept(),
        ]);
    }

    public function update($id)
    {
        $wo = $this->woModel->find($id);
        if (!$wo) {
            return redirect()->to(base_url('workorder'))->with('error', 'Work Order tidak ditemukan.');
        }

        $roleId = (int) session()->get('id_role');
        $userId = (int) session()->get('user_id');

        if ((int)$wo['id_status'] !== 1 && $roleId !== 1) {
            return redirect()->to(base_url('workorder/detail/' . $id))->with('error', 'Work Order tidak dapat diedit karena status bukan Baru.');
        }

        if ((int)$wo['id_user'] !== $userId && $roleId !== 1) {
            return redirect()->to(base_url('workorder/detail/' . $id))->with('error', 'Anda tidak berhak mengedit WO ini.');
        }

        $updateData = [
            'judul_wo'      => trim($this->request->getPost('judul_wo')),
            'deskripsi'     => trim($this->request->getPost('deskripsi')),
            'id_kategori'   => (int) $this->request->getPost('id_kategori'),
            'id_departemen' => (int) $this->request->getPost('id_departemen'),
            'id_aset'       => !empty($this->request->getPost('id_aset')) ? (int) $this->request->getPost('id_aset') : null,
        ];

        // Handle replacement photo
        $file = $this->request->getFile('foto_trouble');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/trouble', $newName);
            $updateData['foto_trouble'] = 'uploads/trouble/' . $newName;
        }

        $this->woModel->update($id, $updateData);
        $this->audit->log("Memperbarui data Work Order {$wo['kode_wo']}", 'Work Order', $userId);

        return redirect()->to(base_url('workorder/detail/' . $id))->with('success', 'Data Work Order berhasil diperbarui.');
    }

    public function delete($id)
    {
        $wo = $this->woModel->find($id);
        if (!$wo) {
            return redirect()->to(base_url('workorder'))->with('error', 'Work Order tidak ditemukan.');
        }

        $roleId = (int) session()->get('id_role');
        $userId = (int) session()->get('user_id');

        if ((int)$wo['id_status'] !== 1 && $roleId !== 1) {
            return redirect()->to(base_url('workorder/detail/' . $id))->with('error', 'Work Order yang sudah diverifikasi tidak dapat dihapus.');
        }

        if ((int)$wo['id_user'] !== $userId && $roleId !== 1) {
            return redirect()->to(base_url('workorder/detail/' . $id))->with('error', 'Anda hanya dapat menghapus WO milik Anda sendiri.');
        }

        $this->woModel->delete($id);
        $this->audit->log("Menghapus Work Order {$wo['kode_wo']}", 'Work Order', $userId);

        return redirect()->to(base_url('workorder'))->with('success', "Work Order {$wo['kode_wo']} berhasil dihapus.");
    }

    /**
     * Kadep / Admin Verification: Accept or Reject
     */
    public function verify($id)
    {
        $wo = $this->woModel->find($id);
        if (!$wo) return redirect()->back()->with('error', 'WO tidak ditemukan.');

        $action = $this->request->getPost('action'); // 'accept' or 'reject'
        $userId = (int) session()->get('user_id');
        $now = date('Y-m-d H:i:s');

        if ($action === 'accept') {
            $this->woModel->update($id, [
                'id_status'   => 2, // Accepted
                'verified_by' => $userId,
                'verified_at' => $now,
            ]);

            $this->audit->log("Verifikasi Accept untuk Work Order {$wo['kode_wo']}", 'Work Order', $userId);
            return redirect()->to(base_url('workorder/detail/' . $id))->with('success', 'Work Order disetujui (Accepted). Silakan tugaskan staf penanggung jawab.');
        } elseif ($action === 'reject') {
            $rejectNote = trim($this->request->getPost('reject_note') ?? '');
            if (empty($rejectNote)) {
                return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
            }

            $this->woModel->update($id, [
                'id_status'   => 3, // Ditolak
                'verified_by' => $userId,
                'verified_at' => $now,
                'reject_note' => $rejectNote,
            ]);

            // Notify pengaju via Asynchronous Notification Queue
            $pengaju = $this->userModel->find($wo['id_user']);
            if ($pengaju) {
                $this->notification->notifyRejected($wo, $pengaju, $rejectNote);
            }

            $this->audit->log("Verifikasi Reject untuk Work Order {$wo['kode_wo']}: {$rejectNote}", 'Work Order', $userId);
            return redirect()->to(base_url('workorder/detail/' . $id))->with('warning', 'Work Order telah ditolak dan alasan telah diteruskan ke pemohon.');
        }

        return redirect()->back();
    }

    /**
     * Kadep / Admin Assignment: Assign to Staff Member
     */
    public function assign($id)
    {
        $wo = $this->woModel->getWorkOrdersWithRelations($id);
        if (!$wo) return redirect()->back()->with('error', 'WO tidak ditemukan.');

        $assignedTo = (int) $this->request->getPost('assigned_to');
        $staf = $this->userModel->find($assignedTo);
        if (!$staf) return redirect()->back()->with('error', 'Staf tidak ditemukan.');

        $userId = (int) session()->get('user_id');
        $now = date('Y-m-d H:i:s');

        $this->woModel->update($id, [
            'id_status'   => 4, // Ditugaskan
            'assigned_to' => $assignedTo,
            'verified_by' => $userId,
            'verified_at' => $wo['verified_at'] ?? $now,
        ]);

        // Send Asynchronous Notification to assigned staff (WA with Email failover)
        $this->notification->notifyAssignedStaff($wo, $staf);
        $this->audit->log("Menugaskan Work Order {$wo['kode_wo']} kepada {$staf['nama_lengkap']}", 'Work Order', $userId);

        return redirect()->to(base_url('workorder/detail/' . $id))->with('success', "Work Order berhasil ditugaskan kepada {$staf['nama_lengkap']}. Notifikasi WhatsApp telah dikirim.");
    }

    /**
     * Kadep / Admin: Change SLA Priority & Recalculate Deadline
     */
    public function changePriority($id)
    {
        $wo = $this->woModel->find($id);
        if (!$wo) return redirect()->back()->with('error', 'WO tidak ditemukan.');

        $newPrioId = (int) $this->request->getPost('id_prioritas');
        $prio = $this->prioritasModel->find($newPrioId);
        if (!$prio) return redirect()->back()->with('error', 'Prioritas tidak valid.');

        $newDeadline = $this->sla->calculateDeadline($wo['tanggal_ajuan'], (int)$prio['target_jam']);
        $userId = (int) session()->get('user_id');

        $this->woModel->update($id, [
            'id_prioritas' => $newPrioId,
            'deadline'     => $newDeadline,
        ]);

        $this->audit->log("Mengubah tingkat prioritas SLA WO {$wo['kode_wo']} menjadi {$prio['tingkat_prioritas']} ({$prio['target_jam']} Jam)", 'Work Order', $userId);
        return redirect()->to(base_url('workorder/detail/' . $id))->with('success', "Tingkat prioritas diperbarui ke {$prio['tingkat_prioritas']}. Deadline disesuaikan.");
    }

    /**
     * Staf: Start Processing Work Order
     */
    public function startProcess($id)
    {
        $wo = $this->woModel->find($id);
        if (!$wo) return redirect()->back()->with('error', 'WO tidak ditemukan.');

        $userId = (int) session()->get('user_id');

        $this->woModel->update($id, [
            'id_status' => 5, // Proses
        ]);

        $this->audit->log("Memulai pengerjaan Work Order {$wo['kode_wo']}", 'Work Order', $userId);
        return redirect()->to(base_url('workorder/detail/' . $id))->with('success', 'Status pengerjaan diubah ke "Dalam Proses". Segera tuntaskan tugas Anda!');
    }

    /**
     * Staf / Admin: Complete Work Order
     * - Uploads foto bukti selesai
     * - Records duration
     * - Optional spare parts deduction (with concurrency safe stock reduction)
     */
    public function finish($id)
    {
        $wo = $this->woModel->find($id);
        if (!$wo) return redirect()->back()->with('error', 'WO tidak ditemukan.');

        $userId = (int) session()->get('user_id');
        $durasiMenit = (int) $this->request->getPost('durasi_menit_pengerjaan');

        // Handle Photo Selesai Upload
        $fotoBuktiName = null;
        $file = $this->request->getFile('foto_bukti_selesai');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/selesai', $newName);
            $fotoBuktiName = 'uploads/selesai/' . $newName;
        }

        // Handle Spareparts Usage with Database Transaction & Pessimistic Locking (SELECT ... FOR UPDATE)
        $useSpareparts = $this->request->getPost('use_spareparts');
        $sparepartItems = $this->request->getPost('spareparts'); // array of ['id_barang' => X, 'qty' => Y]
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
                            // Rollback transaction immediately on insufficient stock
                            $db->transRollback();
                            return redirect()->back()->withInput()->with('error', "Stok barang tidak mencukupi: " . $lockResult['error']);
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

            // Update Work Order to Selesai (6)
            $this->woModel->update($id, [
                'id_status'               => 6, // Selesai
                'foto_bukti_selesai'      => $fotoBuktiName,
                'durasi_menit_pengerjaan' => $durasiMenit > 0 ? $durasiMenit : 30,
                'total_biaya_sparepart'   => $totalCost,
            ]);

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', "Gagal menyelesaikan Work Order: " . $e->getMessage());
        }

        $this->audit->log("Menyelesaikan Work Order {$wo['kode_wo']} dengan durasi {$durasiMenit} menit dan total biaya sparepart Rp " . number_format($totalCost, 0, ',', '.'), 'Work Order', $userId);

        return redirect()->to(base_url('workorder/detail/' . $id))->with('success', "Selamat! Work Order {$wo['kode_wo']} telah selesai dan tercatat di laporan.");
    }

    /**
     * Export Department Work Orders to Excel using PhpSpreadsheet
     */
    public function exportExcel()
    {
        $roleId = (int) session()->get('id_role');
        $deptId = (int) session()->get('id_departemen');

        $filters = [];
        if ($roleId === 3 || $roleId === 2) {
            // Staff and Kadep download their own department's report
            $filters['departemen'] = $deptId;
        } else {
            $reqDept = $this->request->getGet('dept');
            if (!empty($reqDept)) $filters['departemen'] = $reqDept;
        }

        $workOrders = $this->woModel->getWorkOrdersWithRelations(null, $filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(current_locale() === 'en' ? 'Work Order Report' : 'Laporan Work Order');

        // Hotel Header
        $sheet->setCellValue('A1', lang('Report.hotel_title'));
        $sheet->setCellValue('A2', lang('Report.excel_main_header'));
        $sheet->setCellValue('A3', lang('Report.download_date', [date('d F Y H:i:s'), session()->get('nama_lengkap')]));

        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);

        // Table Headers
        $headers = [
            'A5' => lang('Report.col_no'),
            'B5' => lang('Report.col_wo_code'),
            'C5' => lang('Report.col_title'),
            'D5' => lang('Report.col_dept'),
            'E5' => lang('Report.col_category'),
            'F5' => lang('Report.col_sla_priority'),
            'G5' => lang('Report.col_status'),
            'H5' => lang('Report.col_requester'),
            'I5' => lang('Report.col_handler'),
            'J5' => lang('Report.col_date'),
            'K5' => lang('Report.col_duration_min'),
            'L5' => lang('Report.col_cost_idr'),
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A5:L5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $row = 6;
        $no = 1;
        $totalCostAll = 0;

        foreach ($workOrders as $item) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $item['kode_wo']);
            $sheet->setCellValue("C{$row}", $item['judul_wo']);
            $sheet->setCellValue("D{$row}", $item['nama_departemen']);
            $sheet->setCellValue("E{$row}", translate_category($item['nama_kategori']));
            $sheet->setCellValue("F{$row}", translate_priority($item['tingkat_prioritas']) . ' (' . lang('WorkOrder.sla_hours', [$item['target_jam']]) . ')');
            $sheet->setCellValue("G{$row}", translate_status($item['nama_status']));
            $sheet->setCellValue("H{$row}", $item['nama_pengaju']);
            $sheet->setCellValue("I{$row}", !empty($item['nama_handler']) ? $item['nama_handler'] : lang('WorkOrder.unassigned'));
            $sheet->setCellValue("J{$row}", $item['tanggal_ajuan']);
            $sheet->setCellValue("K{$row}", $item['durasi_menit_pengerjaan'] ?? 0);
            $sheet->setCellValue("L{$row}", (float)$item['total_biaya_sparepart']);

            $totalCostAll += (float)$item['total_biaya_sparepart'];
            $row++;
        }

        // Summary Row
        $sheet->setCellValue("A{$row}", lang('Report.total_expense_label'));
        $sheet->mergeCells("A{$row}:K{$row}");
        $sheet->setCellValue("L{$row}", $totalCostAll);

        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
        ]);

        // Currency format for column L
        $sheet->getStyle("L6:L{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Auto width
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Work_Order_GoldenHill_' . date('Ymd_His') . '.xlsx';

        $this->audit->log("Mengunduh Laporan Work Order dalam format Excel ({$fileName})", 'Laporan & Export');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    /**
     * Export Single Work Order to PDF using DomPDF
     */
    public function exportPdf($id)
    {
        $wo = $this->woModel->getWorkOrdersWithRelations($id);
        if (!$wo) return redirect()->to(base_url('workorder'))->with('error', 'WO tidak ditemukan.');

        $spareparts = $this->sparepartModel->getPartsByWo($id);
        $slaInfo = $this->sla->getSlaInfo($wo);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);

        $html = view('work_order/export_pdf', [
            'wo'         => $wo,
            'spareparts' => $spareparts,
            'slaInfo'    => $slaInfo,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $this->audit->log("Mengunduh Surat Perintah Kerja (PDF) untuk {$wo['kode_wo']}", 'Laporan & Export');
        $dompdf->stream('Work_Order_' . $wo['kode_wo'] . '.pdf', ['Attachment' => 0]);
        exit();
    }

    /**
     * SLA Monitoring & Escalation Dashboard
     */
    public function monitoring()
    {
        $this->sla->checkAndEscalateOverdue();

        $workOrders = $this->woModel->getWorkOrdersWithRelations();
        foreach ($workOrders as &$wo) {
            $wo['sla_info'] = $this->sla->getSlaInfo($wo);
        }
        unset($wo);

        return view('work_order/monitoring', [
            'title'      => 'Monitoring Kinerja SLA & Escalation',
            'workOrders' => $workOrders,
        ]);
    }

    /**
     * Offline PWA Fallback View
     */
    public function offlineView()
    {
        return view('offline/index', [
            'title' => 'Mode Offline - Hotel Golden Hill'
        ]);
    }

    /**
     * Background Sync API for Offline Work Orders Drafts
     */
    public function syncOfflineDraft()
    {
        $json = $this->request->getJSON(true);
        $drafts = $json['drafts'] ?? [];
        $syncedCount = 0;
        $createdList = [];

        $userId = (int) session()->get('user_id');
        if (!$userId) {
            $userId = 1; // System / default fallback
        }

        foreach ($drafts as $draft) {
            $judul = trim($draft['judul_wo'] ?? '');
            $deskripsi = trim($draft['deskripsi'] ?? '');
            $idKategori = (int) ($draft['id_kategori'] ?? 1);
            $idPrioritas = (int) ($draft['id_prioritas'] ?? 2);
            $idDept = (int) ($draft['id_departemen'] ?? 1);
            $idAset = !empty($draft['id_aset']) ? (int) $draft['id_aset'] : null;

            if (empty($judul) || empty($deskripsi)) {
                continue;
            }

            $kodeWo = $this->woModel->generateKodeWo();
            $prio = $this->prioritasModel->find($idPrioritas);
            $targetJam = (int) ($prio['target_jam'] ?? 12);
            $now = date('Y-m-d H:i:s');
            $deadline = $this->sla->calculateDeadline($now, $targetJam);

            $insertData = [
                'kode_wo'                 => $kodeWo,
                'judul_wo'                => $judul,
                'deskripsi'               => $deskripsi . "\n\n[Dibuat dalam Mode Offline & Disinkronkan Otomatis]",
                'id_user'                 => $userId,
                'id_kategori'             => $idKategori,
                'id_status'               => 1, // Baru
                'id_prioritas'            => $idPrioritas,
                'id_departemen'           => $idDept,
                'id_aset'                 => $idAset,
                'foto_trouble'            => null,
                'tanggal_ajuan'           => $now,
                'deadline'                => $deadline,
                'total_biaya_sparepart'   => 0.00,
            ];

            $insertedId = $this->woModel->insert($insertData);
            if ($insertedId) {
                $syncedCount++;
                $createdList[] = $kodeWo;
                $this->audit->log("Sinkronisasi Work Order Offline: {$kodeWo} - {$judul}", 'Work Order Offline Sync', $userId);

                // Enqueue notification to Kadep
                $kadep = $this->userModel->getHeadOfDepartment($idDept);
                if ($kadep) {
                    $insertData['nama_pengaju'] = session()->get('nama_lengkap') ?: 'Staf Lapangan';
                    $insertData['tingkat_prioritas'] = $prio['tingkat_prioritas'] ?? 'SLA';
                    $this->notification->notifyNewWorkOrder($insertData, $kadep);
                }
            }
        }

        return $this->response->setJSON([
            'success'     => true,
            'count'       => $syncedCount,
            'created_wos' => $createdList,
            'message'     => "Berhasil menyinkronkan {$syncedCount} draft Work Order offline ke server."
        ]);
    }
}
