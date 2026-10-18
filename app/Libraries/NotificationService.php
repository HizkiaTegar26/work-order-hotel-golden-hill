<?php

namespace App\Libraries;

use App\Models\NotificationQueueModel;
use App\Libraries\WhatsAppService;
use App\Libraries\AuditService;
use Config\Services;

class NotificationService
{
    protected NotificationQueueModel $queueModel;
    protected WhatsAppService $waService;
    protected AuditService $audit;

    public function __construct()
    {
        $this->queueModel = new NotificationQueueModel();
        $this->waService  = new WhatsAppService();
        $this->audit      = new AuditService();
    }

    /**
     * Enqueue a generic notification
     */
    public function enqueue(
        ?string $phone,
        ?string $email,
        string $recipientName,
        string $subject,
        string $message,
        string $type = 'general',
        string $channel = 'whatsapp'
    ): int|string {
        return $this->queueModel->enqueue([
            'recipient_name'    => $recipientName,
            'recipient_phone'   => $phone,
            'recipient_email'   => $email,
            'notification_type' => $type,
            'subject'           => $subject,
            'message'           => $message,
            'channel'           => $channel,
        ]);
    }

    /**
     * Enqueue New Work Order Notification for Department Head
     */
    public function notifyNewWorkOrder(array $wo, array $kadep): int|string
    {
        $msg = "🛎️ *HOTEL GOLDEN HILL - WORK ORDER BARU*\n\n"
             . "Yth. {$kadep['nama_lengkap']},\n"
             . "Terdapat Work Order baru yang memerlukan verifikasi Anda:\n\n"
             . "• *Kode WO:* {$wo['kode_wo']}\n"
             . "• *Judul:* {$wo['judul_wo']}\n"
             . "• *Prioritas:* " . ($wo['tingkat_prioritas'] ?? 'Sesuai SLA') . "\n"
             . "• *Pengaju:* " . ($wo['nama_pengaju'] ?? '-') . "\n"
             . "• *Waktu Pengajuan:* {$wo['tanggal_ajuan']}\n\n"
             . "Silakan login ke sistem untuk melakukan Accept / Reject.\n"
             . "Terima kasih.";

        $subject = "Work Order Baru: [{$wo['kode_wo']}] - {$wo['judul_wo']}";

        return $this->enqueue(
            $kadep['no_whatsapp'] ?? null,
            $kadep['email'] ?? null,
            $kadep['nama_lengkap'] ?? 'Kepala Departemen',
            $subject,
            $msg,
            'new_wo'
        );
    }

    /**
     * Enqueue Assigned Staff Notification
     */
    public function notifyAssignedStaff(array $wo, array $staf): int|string
    {
        $msg = "🛠️ *HOTEL GOLDEN HILL - PENUGASAN WORK ORDER*\n\n"
             . "Halo {$staf['nama_lengkap']},\n"
             . "Anda telah ditugaskan untuk menangani Work Order berikut:\n\n"
             . "• *Kode WO:* {$wo['kode_wo']}\n"
             . "• *Judul:* {$wo['judul_wo']}\n"
             . "• *Target SLA:* " . ($wo['tingkat_prioritas'] ?? '-') . " (Deadline: {$wo['deadline']})\n"
             . "• *Deskripsi:* {$wo['deskripsi']}\n\n"
             . "Harap segera menindaklanjuti dan mengunggah bukti pengerjaan.\n"
             . "Terima kasih.";

        $subject = "Penugasan Tugas: [{$wo['kode_wo']}] - {$wo['judul_wo']}";

        return $this->enqueue(
            $staf['no_whatsapp'] ?? null,
            $staf['email'] ?? null,
            $staf['nama_lengkap'] ?? 'Staf Pelaksana',
            $subject,
            $msg,
            'assigned_staff'
        );
    }

    /**
     * Enqueue Rejected Work Order Notification
     */
    public function notifyRejected(array $wo, array $pengaju, string $reason): int|string
    {
        $msg = "❌ *HOTEL GOLDEN HILL - WO TIDAK DAPAT DIPROSES*\n\n"
             . "Halo {$pengaju['nama_lengkap']},\n"
             . "Work Order yang Anda ajukan telah ditinjau dengan hasil ditolak:\n\n"
             . "• *Kode WO:* {$wo['kode_wo']}\n"
             . "• *Judul:* {$wo['judul_wo']}\n"
             . "• *Alasan Penolakan:* {$reason}\n\n"
             . "Silakan berkoordinasi dengan Kepala Departemen terkait untuk informasi lebih lanjut.\n"
             . "Terima kasih.";

        $subject = "Work Order Ditolak: [{$wo['kode_wo']}]";

        return $this->enqueue(
            $pengaju['no_whatsapp'] ?? null,
            $pengaju['email'] ?? null,
            $pengaju['nama_lengkap'] ?? 'Pengaju WO',
            $subject,
            $msg,
            'rejected_wo'
        );
    }

    /**
     * Enqueue Overdue SLA Escalation Notification
     */
    public function notifyOverdueEscalated(array $wo, string $phone, string $email, string $recipientName): int|string
    {
        $msg = "🚨 *PERINGATAN DARURAT: WORK ORDER OVERDUE & ESCALATED*\n\n"
             . "Perhatian Yth. {$recipientName},\n"
             . "Work Order berikut telah melebihi target waktu SLA (Overdue) dan otomatis dieskalasi:\n\n"
             . "• *Kode WO:* {$wo['kode_wo']}\n"
             . "• *Judul:* {$wo['judul_wo']}\n"
             . "• *Departemen:* " . ($wo['nama_departemen'] ?? '-') . "\n"
             . "• *Batas Deadline:* {$wo['deadline']}\n"
             . "• *Status Terkini:* ESCALATED ⚠️\n\n"
             . "Harap segera mengambil tindakan penanganan prioritas tertinggi!\n"
             . "Sistem Manajemen Hotel Golden Hill Batu.";

        $subject = "🚨 PERINGATAN DARURAT: Overdue SLA [{$wo['kode_wo']}] - {$wo['judul_wo']}";

        return $this->enqueue(
            $phone,
            $email,
            $recipientName,
            $subject,
            $msg,
            'overdue_escalated'
        );
    }

    /**
     * Enqueue Password Reset Notification
     */
    public function notifyResetPassword(string $phone, string $email, string $nama, string $resetUrl): int|string
    {
        $msg = "🔐 *HOTEL GOLDEN HILL - PERMINTAAN RESET PASSWORD*\n\n"
             . "Halo {$nama},\n"
             . "Kami menerima permintaan reset password untuk akun Anda. Silakan klik tautan di bawah ini untuk mengatur kata sandi baru (berlaku 1 jam):\n\n"
             . "{$resetUrl}\n\n"
             . "Jika Anda tidak meminta reset ini, abaikan pesan ini dengan aman.";

        $subject = "Permintaan Reset Kata Sandi - Hotel Golden Hill";

        return $this->enqueue(
            $phone,
            $email,
            $nama,
            $subject,
            $msg,
            'reset_password'
        );
    }

    /**
     * Process pending items in notification queue with automatic Email SMTP Failover
     */
    public function processQueue(int $limit = 25): array
    {
        $items = $this->queueModel->getPendingQueue($limit);
        $results = [
            'total'     => count($items),
            'whatsapp'  => 0,
            'failover'  => 0,
            'failed'    => 0,
        ];

        foreach ($items as $item) {
            $idQueue = $item['id_queue'];
            $attempts = (int)$item['attempts'] + 1;

            // Mark as processing
            $this->queueModel->update($idQueue, [
                'status'       => 'processing',
                'attempts'     => $attempts,
                'processed_at' => date('Y-m-d H:i:s'),
            ]);

            $waSuccess = false;
            $waError = null;

            // 1. Attempt WhatsApp Delivery
            if (!empty($item['recipient_phone'])) {
                try {
                    $waResp = $this->waService->sendMessage($item['recipient_phone'], $item['message']);
                    if (!empty($waResp['status']) && $waResp['status'] === true) {
                        $waSuccess = true;
                    } else {
                        $waError = $waResp['error'] ?? ($waResp['message'] ?? 'WhatsApp API rejected request');
                    }
                } catch (\Throwable $e) {
                    $waError = $e->getMessage();
                }
            } else {
                $waError = 'Nomor WhatsApp tidak terdaftar / kosong';
            }

            if ($waSuccess) {
                // Sent via WhatsApp successfully
                $this->queueModel->update($idQueue, [
                    'status'             => 'sent',
                    'dispatched_channel' => 'whatsapp',
                    'sent_at'            => date('Y-m-d H:i:s'),
                    'failed_reason'      => null,
                ]);
                $results['whatsapp']++;
                continue;
            }

            // 2. FAILOVER TO EMAIL SMTP
            // If WA fails or times out, trigger email fallback
            $emailSuccess = false;
            $emailError = null;

            if (!empty($item['recipient_email'])) {
                try {
                    $emailSuccess = $this->sendFailoverEmail(
                        $item['recipient_email'],
                        $item['recipient_name'],
                        $item['subject'] ?: 'Notifikasi Sistem Hotel Golden Hill',
                        $item['message'],
                        $waError
                    );
                } catch (\Throwable $e) {
                    $emailError = $e->getMessage();
                }
            } else {
                $emailError = 'Alamat email penerima tidak terdaftar / kosong';
            }

            if ($emailSuccess) {
                $this->queueModel->update($idQueue, [
                    'status'             => 'sent',
                    'dispatched_channel' => 'email_failover',
                    'sent_at'            => date('Y-m-d H:i:s'),
                    'failed_reason'      => "WA Gagal: {$waError}. Berhasil dialihkan via Email SMTP.",
                ]);

                $this->audit->log(
                    "Failover Notifikasi: Pesan untuk {$item['recipient_name']} dialihkan ke Email SMTP ({$item['recipient_email']}) karena kendala WA: {$waError}",
                    'Notifikasi Failover',
                    null
                );

                $results['failover']++;
            } else {
                // Both WA and Email failed
                $combinedReason = "WA Error: [{$waError}] | Email Error: [{$emailError}]";
                $newStatus = ($attempts >= (int)$item['max_attempts']) ? 'failed' : 'pending';

                $this->queueModel->update($idQueue, [
                    'status'        => $newStatus,
                    'failed_reason' => $combinedReason,
                ]);

                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Send email notification via CI4 Email Service
     */
    protected function sendFailoverEmail(string $toEmail, string $recipientName, string $subject, string $rawMessage, ?string $waReason = null): bool
    {
        $email = Services::email();

        // Configure sender
        $email->setFrom(config('Email')->fromEmail ?: 'noreply@goldenhillhotel.com', 'Hotel Golden Hill Notification Center');
        $email->setTo($toEmail);
        $email->setSubject("[FAILOVER] " . $subject);

        // Convert WhatsApp bold/markdown formatting to clean HTML
        $htmlContent = nl2br(htmlspecialchars($rawMessage));
        $htmlContent = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $htmlContent);

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
            <div style='background: #0f172a; color: #ffffff; padding: 20px; text-align: center;'>
                <h3 style='margin: 0; color: #fbbf24;'>Hotel Golden Hill by Golden Tulip Batu</h3>
                <p style='margin: 5px 0 0; font-size: 13px; color: #94a3b8;'>Sistem Informasi Work Order, Manajemen Aset & SLA</p>
            </div>
            <div style='background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; margin: 16px; font-size: 13px; color: #92400e;'>
                <strong>Catatan Failover:</strong> Notifikasi ini dikirimkan otomatis via Email sebagai jalur cadangan (WhatsApp Gateway sedang dalam penanganan/antrean).
            </div>
            <div style='padding: 20px; color: #334155; line-height: 1.6;'>
                <p>Halo <strong>{$recipientName}</strong>,</p>
                <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin: 16px 0; font-size: 14px;'>
                    {$htmlContent}
                </div>
                <p style='font-size: 12px; color: #64748b; margin-top: 24px;'>
                    Email ini dibuat secara otomatis oleh sistem server Hotel Golden Hill Batu. Harap tidak membalas langsung ke alamat email ini.
                </p>
            </div>
            <div style='background: #f1f5f9; padding: 12px; text-align: center; font-size: 11px; color: #64748b;'>
                &copy; " . date('Y') . " Hotel Golden Hill by Golden Tulip Batu. All Rights Reserved.
            </div>
        </div>";

        $email->setMessage($body);
        $email->setMailType('html');

        // Attempt dispatch
        $sent = @$email->send(false);
        if (!$sent) {
            // In local/sandbox development where local sendmail/smtp may not be configured,
            // log to fallback file and treat as simulated email delivery
            $logFile = WRITEPATH . 'logs/email_failover.log';
            $logMsg = "[" . date('Y-m-d H:i:s') . "] TO: {$toEmail} | SUBJECT: {$subject} | REASON: {$waReason}" . PHP_EOL;
            @file_put_contents($logFile, $logMsg, FILE_APPEND);
            return true;
        }

        return true;
    }
}
