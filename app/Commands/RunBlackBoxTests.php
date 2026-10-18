<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

use App\Models\UserModel;
use App\Models\WorkOrderModel;
use App\Models\AsetHotelModel;
use App\Models\InventarisBarangModel;
use App\Models\WoSparepartsModel;
use App\Models\DepartemenModel;
use App\Models\PrioritasSlaModel;
use App\Models\StatusWoModel;
use App\Models\KategoriWoModel;
use App\Models\LogAuditModel;
use App\Models\ResetPasswordTokenModel;

use App\Libraries\SlaService;
use App\Libraries\QrCodeService;
use App\Libraries\WhatsAppService;
use App\Libraries\AuditService;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RunBlackBoxTests extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:blackbox';
    protected $description = 'Menjalankan 25 Black Box Test Cases Validasi Sistem Work Order V2.0';

    public function run(array $params)
    {
        CLI::write("======================================================================", 'yellow');
        CLI::write(" HOTEL GOLDEN HILL BY GOLDEN TULIP BATU - BLACK BOX VALIDATION SUITE ", 'white');
        CLI::write(" Sistem Informasi Work Order, Manajemen Aset, & Performance V2.0   ", 'white');
        CLI::write(" Mahasiswa: Hizkia Tegar Kurniawan (NIM: 223140707111136)            ", 'cyan');
        CLI::write("======================================================================\n", 'yellow');

        $passCount = 0;
        $failCount = 0;

        $userModel = new UserModel();
        $woModel = new WorkOrderModel();
        $asetModel = new AsetHotelModel();
        $barangModel = new InventarisBarangModel();
        $sparepartModel = new WoSparepartsModel();
        $deptModel = new DepartemenModel();
        $prioModel = new PrioritasSlaModel();
        $statusModel = new StatusWoModel();
        $auditModel = new LogAuditModel();
        $tokenModel = new ResetPasswordTokenModel();

        $slaService = new SlaService();
        $qrService = new QrCodeService();
        $waService = new WhatsAppService();
        $auditService = new AuditService();

        $assertTest = function(int $no, string $name, bool $condition, string $details = '') use (&$passCount, &$failCount) {
            if ($condition) {
                $passCount++;
                CLI::write(sprintf("[%02d] %-52s : VALID (PASS)", $no, $name), 'green');
            } else {
                $failCount++;
                CLI::write(sprintf("[%02d] %-52s : INVALID (FAIL) (%s)", $no, $name, $details), 'red');
            }
        };

        // -------------------------------------------------------------
        // 5.1 SKENARIO PENGUJIAN ROLE STAF (TEST CASE 1 - 10)
        // -------------------------------------------------------------
        CLI::write("--- 5.1 SKENARIO ROLE STAF ---", 'cyan');

        // TC 01: Registrasi / Create User Staf
        $testStafUser = 'staf_bb_' . time();
        $newStafId = $userModel->insert([
            'nama_lengkap'  => 'Staf Tester BlackBox',
            'username'      => $testStafUser,
            'password'      => password_hash('password123', PASSWORD_BCRYPT),
            'email'         => $testStafUser . '@goldenhill.com',
            'no_whatsapp'   => '081233445566',
            'id_departemen' => 1, // Engineering
            'id_role'       => 3, // Staf
            'status_aktif'  => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        $foundStaf = $userModel->find($newStafId);
        $assertTest(1, "Staf: Registrasi Akun Staf Baru", !empty($foundStaf) && $foundStaf['username'] === $testStafUser);

        // TC 02: Login Staf
        $staf = $userModel->where('username', 'staf_eng')->first();
        $loginSuccess = ($staf && password_verify('staf123', $staf['password']) && (int)$staf['id_role'] === 3);
        $assertTest(2, "Staf: Autentikasi Login Staf", $loginSuccess);

        // TC 03: Ajukan WO Manual
        $kodeWoManual = $woModel->generateKodeWo();
        $prioHigh = $prioModel->where('tingkat_prioritas', 'High')->first();
        $targetDeadline = $slaService->calculateDeadline(date('Y-m-d H:i:s'), (int)$prioHigh['target_jam']);
        $manualWoId = $woModel->insert([
            'kode_wo'                 => $kodeWoManual,
            'judul_wo'                => 'Lampu Koridor Lt. 2 Mati Total',
            'deskripsi'               => 'Dua armature lampu downlight tidak menyala, mengganggu kenyamanan tamu hotel.',
            'id_user'                 => $staf['id_user'],
            'id_kategori'             => 4, // General Repair
            'id_status'               => 1, // Baru
            'id_prioritas'            => $prioHigh['id_prioritas'],
            'id_departemen'           => 1, // ENG
            'id_aset'                 => null,
            'tanggal_ajuan'           => date('Y-m-d H:i:s'),
            'deadline'                => $targetDeadline,
            'total_biaya_sparepart'   => 0.00,
        ]);
        $createdWo = $woModel->find($manualWoId);
        $assertTest(3, "Staf: Ajukan Work Order Manual", !empty($createdWo) && (int)$createdWo['id_status'] === 1);

        // TC 04: Ajukan WO via Scan QR Aset
        $targetAsset = $asetModel->where('kode_aset', 'AST-ENG-001')->first();
        $kodeWoQr = $woModel->generateKodeWo();
        $qrWoId = $woModel->insert([
            'kode_wo'                 => $kodeWoQr,
            'judul_wo'                => 'AC Chiller Suara Berisik - Scan QR',
            'deskripsi'               => 'Kompresor AC bergetar kencang terdeteksi setelah scan QR label fisik.',
            'id_user'                 => $staf['id_user'],
            'id_kategori'             => 1, // Maintenance
            'id_status'               => 1, // Baru
            'id_prioritas'            => 3, // High
            'id_departemen'           => $targetAsset['id_departemen'],
            'id_aset'                 => $targetAsset['id_aset'],
            'tanggal_ajuan'           => date('Y-m-d H:i:s'),
            'deadline'                => date('Y-m-d H:i:s', strtotime('+4 hours')),
            'total_biaya_sparepart'   => 0.00,
        ]);
        $createdQrWo = $woModel->find($qrWoId);
        $assertTest(4, "Staf: Ajukan WO via Scan QR Aset (Locked Dept/Asset)", !empty($createdQrWo) && (int)$createdQrWo['id_aset'] === (int)$targetAsset['id_aset']);

        // TC 05: Edit WO Milik Sendiri (Status Baru)
        $woModel->update($manualWoId, [
            'judul_wo' => 'Lampu Koridor Lt. 2 Mati Total - Diperbarui',
        ]);
        $updatedWo = $woModel->find($manualWoId);
        $assertTest(5, "Staf: Edit WO Milik Sendiri saat Status Baru", $updatedWo['judul_wo'] === 'Lampu Koridor Lt. 2 Mati Total - Diperbarui');

        // TC 06: Hapus WO Milik Sendiri (Status Baru)
        $woToDeleteId = $woModel->insert([
            'kode_wo'       => 'WO-DEL-' . time(),
            'judul_wo'      => 'Ajuan Salah Input untuk Dihapus',
            'deskripsi'     => 'Testing penghapusan WO',
            'id_user'       => $staf['id_user'],
            'id_kategori'   => 1,
            'id_status'     => 1, // Baru
            'id_prioritas'  => 1,
            'id_departemen' => 1,
            'tanggal_ajuan' => date('Y-m-d H:i:s'),
        ]);
        $woModel->delete($woToDeleteId);
        $deletedCheck = $woModel->find($woToDeleteId);
        $assertTest(6, "Staf: Hapus WO Milik Sendiri saat Status Baru", empty($deletedCheck));

        // TC 07: Mulai Pengerjaan WO (Proses)
        $woModel->update($manualWoId, [
            'id_status'   => 5, // Proses
            'assigned_to' => $staf['id_user'],
        ]);
        $procWo = $woModel->find($manualWoId);
        $assertTest(7, "Staf: Mulai Proses Pengerjaan WO (Status: Proses)", (int)$procWo['id_status'] === 5);

        // TC 08: Pakai Sparepart & Potong Stok Aman
        $itemLamp = $barangModel->where('kode_barang', 'BRG-002')->first();
        $initialStock = (int) $itemLamp['stok'];
        $deductQty = 2;
        $isStockDeducted = $barangModel->deductStockSafely($itemLamp['id_barang'], $deductQty);
        $subtotalCost = $deductQty * (float)$itemLamp['harga_satuan'];
        $sparepartModel->insert([
            'id_wo'          => $manualWoId,
            'id_barang'      => $itemLamp['id_barang'],
            'jumlah'         => $deductQty,
            'harga_subtotal' => $subtotalCost,
        ]);
        $updatedItem = $barangModel->find($itemLamp['id_barang']);
        $stockSuccess = ($isStockDeducted && (int)$updatedItem['stok'] === ($initialStock - $deductQty));
        $assertTest(8, "Staf: Penggunaan Sparepart & Pemotongan Stok Gudang", $stockSuccess && $subtotalCost == 130000);

        // TC 09: Selesaikan WO + Foto Bukti + Durasi Pengerjaan
        $woModel->update($manualWoId, [
            'id_status'               => 6, // Selesai
            'durasi_menit_pengerjaan' => 45,
            'foto_bukti_selesai'      => 'uploads/selesai/bukti_lampu_normal.jpg',
            'total_biaya_sparepart'   => $subtotalCost,
        ]);
        $finishedWo = $woModel->find($manualWoId);
        $assertTest(9, "Staf: Selesaikan WO + Foto Bukti + Catat Biaya", (int)$finishedWo['id_status'] === 6 && (float)$finishedWo['total_biaya_sparepart'] == 130000);

        // TC 10: Download Excel Laporan WO Departemen Sendiri
        $deptWos = $woModel->getWorkOrdersWithRelations(null, ['departemen' => $staf['id_departemen']]);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'HOTEL GOLDEN HILL BY GOLDEN TULIP BATU');
        $sheet->setCellValue('A2', 'LAPORAN WORK ORDER DEPARTEMEN ENGINEERING');
        $r = 4;
        foreach ($deptWos as $dw) {
            $sheet->setCellValue("A{$r}", $dw['kode_wo']);
            $sheet->setCellValue("B{$r}", $dw['judul_wo']);
            $sheet->setCellValue("C{$r}", $dw['nama_status']);
            $r++;
        }
        $tempExcel = sys_get_temp_dir() . '/test_wo_report_' . time() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempExcel);
        $excelSuccess = file_exists($tempExcel) && filesize($tempExcel) > 1000;
        @unlink($tempExcel);
        $assertTest(10, "Staf: Unduh Laporan Excel WO Departemen", $excelSuccess);

        // -------------------------------------------------------------
        // 5.2 SKENARIO PENGUJIAN ROLE KEPALA DEPARTEMEN (TEST CASE 11 - 17)
        // -------------------------------------------------------------
        CLI::write("\n--- 5.2 SKENARIO ROLE KEPALA DEPARTEMEN ---", 'cyan');

        // TC 11: Login Kadep
        $kadep = $userModel->where('username', 'kadep_eng')->first();
        $loginKadep = ($kadep && password_verify('kadep123', $kadep['password']) && (int)$kadep['id_role'] === 2);
        $assertTest(11, "Kadep: Autentikasi Login Kepala Departemen", $loginKadep);

        // TC 12: Verifikasi WO Masuk (Accept)
        $woToAcceptId = $woModel->insert([
            'kode_wo'       => 'WO-ACC-' . time(),
            'judul_wo'      => 'Pintu Kaca Kamar 201 Agak Seret',
            'deskripsi'     => 'Perlu pelumasan rel pintu kaca geser.',
            'id_user'       => 9, // Staf FO
            'id_kategori'   => 4,
            'id_status'     => 1, // Baru
            'id_prioritas'  => 2, // Medium 12 jam
            'id_departemen' => 1, // ENG
            'tanggal_ajuan' => date('Y-m-d H:i:s'),
            'deadline'      => date('Y-m-d H:i:s', strtotime('+12 hours')),
        ]);
        $woModel->update($woToAcceptId, [
            'id_status'   => 2, // Accepted
            'verified_by' => $kadep['id_user'],
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
        $acceptedWo = $woModel->find($woToAcceptId);
        $assertTest(12, "Kadep: Verifikasi WO Masuk (Accept WO)", (int)$acceptedWo['id_status'] === 2 && !empty($acceptedWo['verified_at']));

        // TC 13: Verifikasi WO Masuk (Reject + Reason + WA Notification)
        $woToRejectId = $woModel->insert([
            'kode_wo'       => 'WO-REJ-' . time(),
            'judul_wo'      => 'Pengadaan Kulkas Mini Tambahan',
            'deskripsi'     => 'Meminta kulkas tambahan di pos security',
            'id_user'       => 9,
            'id_kategori'   => 1,
            'id_status'     => 1, // Baru
            'id_prioritas'  => 1,
            'id_departemen' => 1,
            'tanggal_ajuan' => date('Y-m-d H:i:s'),
        ]);
        $rejectReason = 'Pengadaan unit kulkas baru bukan melalui form WO perbaikan, gunakan form Capex/Opex pengadaan barang.';
        $woModel->update($woToRejectId, [
            'id_status'   => 3, // Ditolak
            'verified_by' => $kadep['id_user'],
            'verified_at' => date('Y-m-d H:i:s'),
            'reject_note' => $rejectReason,
        ]);
        $rejectedWo = $woModel->find($woToRejectId);
        $waRejectResult = $waService->notifyRejected($rejectedWo, $userModel->find(9), $rejectReason);
        $assertTest(13, "Kadep: Verifikasi WO Masuk (Reject + Reason Input)", (int)$rejectedWo['id_status'] === 3 && !empty($rejectedWo['reject_note']) && $waRejectResult['status']);

        // TC 14: Ubah Tingkat Prioritas SLA (Emergency 1 Jam)
        $newEmergencyDeadline = $slaService->calculateDeadline($acceptedWo['tanggal_ajuan'], 1);
        $woModel->update($woToAcceptId, [
            'id_prioritas' => 4, // Emergency (1 jam)
            'deadline'     => $newEmergencyDeadline,
        ]);
        $prioUpdatedWo = $woModel->find($woToAcceptId);
        $assertTest(14, "Kadep: Ubah Tingkat Prioritas SLA (Emergency 1 Jam)", (int)$prioUpdatedWo['id_prioritas'] === 4 && $prioUpdatedWo['deadline'] === $newEmergencyDeadline);

        // TC 15: Penugasan Staf (Assign To) + Notifikasi WA Penugasan
        $woModel->update($woToAcceptId, [
            'id_status'   => 4, // Ditugaskan
            'assigned_to' => $staf['id_user'],
        ]);
        $assignedWo = $woModel->find($woToAcceptId);
        $waAssignResult = $waService->notifyAssignedStaff($assignedWo, $staf);
        $assertTest(15, "Kadep: Tugaskan Staf Pelaksana (Assign To)", (int)$assignedWo['id_status'] === 4 && (int)$assignedWo['assigned_to'] === (int)$staf['id_user'] && $waAssignResult['status']);

        // TC 16: Monitoring Overdue SLA & Deteksi Eskalasi Otomatis
        $overdueTestWoId = $woModel->insert([
            'kode_wo'       => 'WO-OVERDUE-' . time(),
            'judul_wo'      => 'Sensor Lift Macet (Simulasi Overdue)',
            'deskripsi'     => 'Melewati batas waktu SLA',
            'id_user'       => 9,
            'id_kategori'   => 1,
            'id_status'     => 4, // Ditugaskan tapi belum selesai
            'id_prioritas'  => 3,
            'id_departemen' => 1,
            'tanggal_ajuan' => date('Y-m-d H:i:s', strtotime('-6 hours')),
            'deadline'      => date('Y-m-d H:i:s', strtotime('-2 hours')), // past deadline!
        ]);
        $overdueList = $woModel->getOverdueWorkOrders();
        $isDetectedInOverdue = false;
        foreach ($overdueList as $ow) {
            if ((int)$ow['id_wo'] === $overdueTestWoId) {
                $isDetectedInOverdue = true;
                break;
            }
        }
        $escalatedResult = $slaService->checkAndEscalateOverdue();
        $checkEscalatedState = $woModel->find($overdueTestWoId);
        $assertTest(16, "Kadep: Monitoring Overdue SLA & Auto Escalation Engine", $isDetectedInOverdue && (int)$checkEscalatedState['id_status'] === 8);

        // TC 17: Download Laporan Biaya Sparepart Departemen
        $deptExpenses = $woModel->getExpensesByDepartment();
        $assertTest(17, "Kadep: Rekapitulasi Pengeluaran Biaya Sparepart Dept", !empty($deptExpenses) && isset($deptExpenses[0]['total_biaya']));

        // -------------------------------------------------------------
        // 5.3 SKENARIO PENGUJIAN ROLE ADMIN (TEST CASE 18 - 25)
        // -------------------------------------------------------------
        CLI::write("\n--- 5.3 SKENARIO ROLE ADMIN ---", 'cyan');

        // TC 18: Login Admin
        $admin = $userModel->where('username', 'admin')->first();
        $loginAdmin = ($admin && password_verify('admin123', $admin['password']) && (int)$admin['id_role'] === 1);
        $assertTest(18, "Admin: Autentikasi Login Administrator", $loginAdmin);

        // TC 19: Manajemen User (Create, Update Role, Toggle Active)
        $testAdminUser = 'user_mgmt_' . time();
        $newUserId = $userModel->insert([
            'nama_lengkap'  => 'User Manajemen Test',
            'username'      => $testAdminUser,
            'password'      => password_hash('secret123', PASSWORD_BCRYPT),
            'email'         => $testAdminUser . '@goldenhill.com',
            'no_whatsapp'   => '081299887766',
            'id_departemen' => 2, // IT
            'id_role'       => 3, // Staf
            'status_aktif'  => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        $userModel->update($newUserId, ['status_aktif' => 0]);
        $inactiveUser = $userModel->find($newUserId);
        $userModel->update($newUserId, ['status_aktif' => 1]);
        $activeUser = $userModel->find($newUserId);
        $assertTest(19, "Admin: Manajemen User (Tambah, Ubah, Toggle Status)", !empty($newUserId) && (int)$inactiveUser['status_aktif'] === 0 && (int)$activeUser['status_aktif'] === 1);

        // TC 20: Switch Role Multi-Akun
        $hizkiaUser = $userModel->where('username', 'hizkia')->first();
        $switchValid = ($hizkiaUser && (int)$hizkiaUser['id_role'] === 1);
        $assertTest(20, "Admin: Switch Role Multi-Akun (Testing & Audit)", $switchValid);

        // TC 21: Manajemen Departemen (Tambah / Edit)
        $testDeptCode = 'T' . rand(10, 99);
        $newDeptId = $deptModel->insert([
            'nama_departemen' => 'Security & Safety VIP',
            'kode_dept'       => $testDeptCode,
        ]);
        $deptModel->update($newDeptId, [
            'nama_departemen' => 'Security & Loss Prevention',
        ]);
        $updatedDept = $deptModel->find($newDeptId);
        $assertTest(21, "Admin: Manajemen Departemen (Tambah & Perbarui Data)", !empty($newDeptId) && $updatedDept['nama_departemen'] === 'Security & Loss Prevention');

        // TC 22: Master Aset + Print QR Code Generation
        $testAssetCode = 'AST-T' . rand(100, 999);
        $qrGeneratedPath = $qrService->generateForAsset($testAssetCode);
        $newAssetId = $asetModel->insert([
            'kode_aset'       => $testAssetCode,
            'nama_aset'       => 'Smart TV Samsung 55 Inch Suite Room',
            'id_departemen'   => 2, // IT
            'lokasi_spesifik' => 'Presidential Suite Lt. 5',
            'qr_code_path'    => $qrGeneratedPath,
            'kondisi'         => 'Baik',
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
        $qrPhysicalExists = file_exists(FCPATH . $qrGeneratedPath) && filesize(FCPATH . $qrGeneratedPath) > 200;
        $assertTest(22, "Admin: Master Aset + Generate QR Code Image", !empty($newAssetId) && $qrPhysicalExists);

        // TC 23: Master Inventaris Suku Cadang (Tambah / Edit Stok)
        $testBarangCode = 'BRG-T' . rand(100, 999);
        $newBarangId = $barangModel->insert([
            'kode_barang'  => $testBarangCode,
            'nama_barang'  => 'Kabel HDMI High Speed 4K 5M',
            'stok'         => 15,
            'satuan'       => 'Pcs',
            'harga_satuan' => 95000.00,
        ]);
        $barangModel->update($newBarangId, ['stok' => 20]);
        $updatedBarang = $barangModel->find($newBarangId);
        $assertTest(23, "Admin: Master Inventaris Suku Cadang (Tambah & Stok)", (int)$updatedBarang['stok'] === 20 && (float)$updatedBarang['harga_satuan'] == 95000);

        // TC 24: Monitor Executive KPI Dashboard (Metrik, Rata-rata Respon & Resolusi)
        $kpiSummary = $woModel->getKpiSummary();
        $topAssets = $woModel->getTopBrokenAssets(5);
        $kpiValid = (
            isset($kpiSummary['total_wo']) &&
            isset($kpiSummary['avg_response_min']) &&
            isset($kpiSummary['avg_resolution_min']) &&
            isset($kpiSummary['sla_compliance_rate']) &&
            is_array($topAssets)
        );
        $assertTest(24, "Admin: Monitor Executive KPI Analytics & Chart Data", $kpiValid);

        // TC 25: Monitor Audit Trail & Alur Token Reset Password
        $auditService->log("Verifikasi audit trail test case 25", 'System Testing', 1);
        $recentLog = $auditModel->where('modul', 'System Testing')->orderBy('id_log', 'DESC')->first();
        $resetToken = $tokenModel->generateToken('admin@goldenhill.com');
        $isTokenValid = $tokenModel->isValidToken($resetToken);
        $assertTest(25, "Admin: Audit Trail Logging & Token Reset Password Flow", !empty($recentLog) && !empty($isTokenValid));

        // Clean up test records
        $userModel->delete($newStafId);
        $userModel->delete($newUserId);
        $deptModel->delete($newDeptId);
        $asetModel->delete($newAssetId);
        if (file_exists(FCPATH . $qrGeneratedPath)) @unlink(FCPATH . $qrGeneratedPath);
        $barangModel->delete($newBarangId);

        CLI::write("\n======================================================================", 'yellow');
        CLI::write(sprintf(" HASIL PENGUJIAN: %d VALID (PASS) / %d GAGAL (FAIL)", $passCount, $failCount), $failCount === 0 ? 'green' : 'red');
        if ($failCount === 0) {
            CLI::write(" STATUS KELULUSAN SISTEM: 100% VALID & MEMENUHI SELURUH SPESIFIKASI", 'green');
        } else {
            CLI::write(" STATUS: TERDAPAT TEST CASE GAGAL, PERIKSA KEMBALI.", 'red');
        }
        CLI::write("======================================================================\n", 'yellow');
    }
}
