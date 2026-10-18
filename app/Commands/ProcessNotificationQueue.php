<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\NotificationService;

class ProcessNotificationQueue extends BaseCommand
{
    protected $group       = 'Notification';
    protected $name        = 'queue:work';
    protected $description = 'Proses antrean notifikasi asynchronous dengan failover otomatis WhatsApp ke Email SMTP.';

    public function run(array $params)
    {
        $isDaemon = array_key_exists('daemon', $params) || in_array('--daemon', $params, true);
        $limit    = (int) ($params['limit'] ?? 25);
        $service  = new NotificationService();

        CLI::write("=====================================================", 'cyan');
        CLI::write(" HOTEL GOLDEN HILL - NOTIFICATION QUEUE WORKER ", 'yellow');
        CLI::write("=====================================================", 'cyan');

        if ($isDaemon) {
            CLI::write("Worker berjalan dalam mode daemon (Ctrl+C untuk berhenti)...", 'green');
            while (true) {
                $res = $service->processQueue($limit);
                if ($res['total'] > 0) {
                    $timestamp = date('H:i:s');
                    CLI::write("[{$timestamp}] Diproses: {$res['total']} | WA: {$res['whatsapp']} | Email Failover: {$res['failover']} | Gagal: {$res['failed']}", 'white');
                }
                sleep(5);
            }
        } else {
            CLI::write("Memproses antrean notifikasi (Batch limit: {$limit})...", 'yellow');
            $res = $service->processQueue($limit);

            if ($res['total'] === 0) {
                CLI::write("Tidak ada antrean notifikasi yang perlu diproses.", 'green');
            } else {
                CLI::write("Hasil Eksekusi Antrean:", 'cyan');
                CLI::write(" - Total Antrean : {$res['total']}", 'white');
                CLI::write(" - Terkirim WA   : {$res['whatsapp']}", 'green');
                CLI::write(" - Failover Email: {$res['failover']}", 'yellow');
                CLI::write(" - Gagal         : {$res['failed']}", $res['failed'] > 0 ? 'red' : 'light_gray');
            }
        }
    }
}
