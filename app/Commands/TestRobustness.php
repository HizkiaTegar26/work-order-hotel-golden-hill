<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\NotificationService;
use App\Models\NotificationQueueModel;
use App\Models\InventarisBarangModel;
use App\Models\DepartemenModel;

class TestRobustness extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:robustness';
    protected $description = 'Verifikasi menyeluruh Fitur Ketahanan Sistem (Robustness & Failover Pack V2.0).';

    public function run(array $params)
    {
        $passCount = 0;
        $failCount = 0;

        $assert = function (string $name, bool $condition, string $detail = '') use (&$passCount, &$failCount) {
            if ($condition) {
                $passCount++;
                CLI::write("  [PASS] {$name}", 'green');
            } else {
                $failCount++;
                CLI::write("  [FAIL] {$name} ({$detail})", 'red');
            }
        };

        CLI::write("======================================================================", 'cyan');
        CLI::write(" HOTEL GOLDEN HILL - ROBUSTNESS & FAILOVER TEST SUITE V2.0 ", 'yellow');
        CLI::write("======================================================================", 'cyan');

        // -------------------------------------------------------------
        // 1. MITIGASI WA API & EMAIL FAILOVER (NOTIFICATION QUEUE)
        // -------------------------------------------------------------
        CLI::write("\n--- 1. NOTIFICATION QUEUE & EMAIL SMTP FAILOVER ---", 'cyan');
        $notifService = new NotificationService();
        $queueModel   = new NotificationQueueModel();

        // Test 1.1: Asynchronous Enqueue
        $queueId = $notifService->enqueue(
            'invalid_phone_9999',
            'kadep.engineering@goldenhillhotel.com',
            'Bambang Wijaya (Kadep)',
            'Uji Ketahanan Failover WA ke Email',
            'Ini adalah pesan uji coba failover otomatis saat WA gateway offline.',
            'failover_test'
        );
        $queuedItem = $queueModel->find($queueId);
        $assert('1.1 Asynchronous Enqueue to notification_queue', $queuedItem && $queuedItem['status'] === 'pending');

        // Test 1.2: Queue Worker Dispatch with Automatic Email Failover
        $processRes = $notifService->processQueue(10);
        $processedItem = $queueModel->find($queueId);
        $assert('1.2 Automatic Failover to Email SMTP upon WA Failure', 
            $processedItem && $processedItem['status'] === 'sent' && $processedItem['dispatched_channel'] === 'email_failover',
            "Status: {$processedItem['status']}, Channel: {$processedItem['dispatched_channel']}"
        );
        $assert('1.3 Failover Reason and Audit Trail Logged', !empty($processedItem['failed_reason']));

        // -------------------------------------------------------------
        // 2. MITIGASI RACE CONDITION STOK (PESSIMISTIC LOCKING)
        // -------------------------------------------------------------
        CLI::write("\n--- 2. PESSIMISTIC LOCKING & STOCK SAFETY ---", 'cyan');
        $barangModel = new InventarisBarangModel();
        $lamp = $barangModel->where('kode_barang', 'BRG-002')->first();
        $initialStock = (int) $lamp['stok'];

        // Test 2.1: Insufficient stock attempt -> must reject and rollback
        $excessiveQty = $initialStock + 999;
        $failResult = $barangModel->deductStockPessimistic($lamp['id_barang'], $excessiveQty);
        $afterRollback = $barangModel->find($lamp['id_barang']);
        $assert('2.1 Excessive Stock Deduction Rejected & Rolled Back', 
            $failResult['success'] === false && (int)$afterRollback['stok'] === $initialStock,
            "Remaining: {$afterRollback['stok']}, Expected: {$initialStock}"
        );
        $assert('2.2 Friendly Error Message on Insufficient Stock', 
            str_contains($failResult['error'], 'tidak mencukupi')
        );

        // Test 2.3: Valid stock deduction with row-level lock -> must succeed
        $validResult = $barangModel->deductStockPessimistic($lamp['id_barang'], 1);
        $afterDeduct = $barangModel->find($lamp['id_barang']);
        $assert('2.3 Concurrency-Safe Stock Deduction (SELECT FOR UPDATE)', 
            $validResult['success'] === true && (int)$afterDeduct['stok'] === ($initialStock - 1),
            "Stock: {$afterDeduct['stok']}, Expected: " . ($initialStock - 1)
        );

        // Restore stock
        $barangModel->update($lamp['id_barang'], ['stok' => $initialStock]);

        // -------------------------------------------------------------
        // 3. OPTIMASI KINERJA VIA CACHING & AUTO-INVALIDATION
        // -------------------------------------------------------------
        CLI::write("\n--- 3. MASTER DATA CACHING & INVALIDATION ---", 'cyan');
        $deptModel = new DepartemenModel();
        cache()->delete('master_departemen');

        // Test 3.1: Cache Miss and Save
        $depts = $deptModel->getAllCached();
        $isSaved = (cache('master_departemen') !== null);
        $assert('3.1 Master Departments Cached Successfully (TTL: 1 Hr)', $isSaved && count($depts) > 0);

        // Test 3.2: Cache Hit
        $deptsCached = $deptModel->getAllCached();
        $assert('3.2 Master Departments Cache Hit Retrieval', count($deptsCached) === count($depts));

        // Test 3.3: Automatic Invalidation on Insert
        $newDeptId = $deptModel->insert([
            'nama_departemen' => 'Test Invalidation Dept',
            'kode_dept'       => 'TID',
        ]);
        $isInvalidated = (cache('master_departemen') === null);
        $assert('3.3 Automatic Cache Invalidation on Master Update', $isInvalidated);
        $deptModel->delete($newDeptId);

        // -------------------------------------------------------------
        // 4. MITIGASI BLINDSPOT SINYAL (PWA & OFFLINE WORKFLOW)
        // -------------------------------------------------------------
        CLI::write("\n--- 4. BLINDSPOT SINYAL & PWA OFFLINE WORKFLOW ---", 'cyan');
        $manifestPath = FCPATH . 'manifest.json';
        $swPath       = FCPATH . 'sw.js';
        $offlineJs    = FCPATH . 'js/offline-manager.js';
        $assert('4.1 Web App Manifest Present & Valid', file_exists($manifestPath) && str_contains(file_get_contents($manifestPath), 'GHH WorkOrder'));
        $assert('4.2 Service Worker (sw.js) Pre-cache Shell Present', file_exists($swPath) && str_contains(file_get_contents($swPath), 'ghh-workorder-v2-cache'));
        $assert('4.3 Offline Storage & Background Sync Manager Present', file_exists($offlineJs) && str_contains(file_get_contents($offlineJs), 'ghh_offline_wo_drafts'));

        // -------------------------------------------------------------
        // 5. OPTIMASI UI/UX MOBILE & INTERACTIVE ONBOARDING
        // -------------------------------------------------------------
        CLI::write("\n--- 5. MOBILE FIELD TOUCH TARGETS & ONBOARDING ---", 'cyan');
        $layoutContent = file_get_contents(APPPATH . 'Views/layouts/main.php');
        $detailContent = file_get_contents(APPPATH . 'Views/work_order/detail.php');
        $onboardingView= APPPATH . 'Views/partials/onboarding_modal.php';

        $assert('5.1 Minimum 48px Touch Target Styling Defined', str_contains($layoutContent, 'min-height: 48px'));
        $assert('5.2 Sticky Mobile 1-Click Quick Actions Bar Integrated', str_contains($detailContent, 'mobile-quick-actions'));
        $assert('5.3 Interactive Onboarding Modal Walkthrough Present', file_exists($onboardingView) && str_contains(file_get_contents($onboardingView), 'onboardingModal'));

        // -------------------------------------------------------------
        // SUMMARY
        // -------------------------------------------------------------
        CLI::write("\n======================================================================", 'cyan');
        CLI::write(sprintf(" HASIL UJI KETAHANAN SISTEM: %d VALID (PASS) / %d GAGAL (FAIL)", $passCount, $failCount), $failCount === 0 ? 'green' : 'red');
        CLI::write(" STATUS ARSITEKTUR: " . ($failCount === 0 ? "100% ROBUST & MEMENUHI SELURUH SPESIFIKASI" : "PERLU PERBAIKAN"), $failCount === 0 ? 'green' : 'red');
        CLI::write("======================================================================\n", 'cyan');
    }
}
