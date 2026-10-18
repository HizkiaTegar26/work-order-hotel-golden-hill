<?php

namespace App\Libraries;

use App\Models\WorkOrderModel;
use App\Models\UserModel;

class SlaService
{
    protected WorkOrderModel $woModel;
    protected WhatsAppService $waService;
    protected NotificationService $notification;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->woModel = new WorkOrderModel();
        $this->waService = new WhatsAppService();
        $this->notification = new NotificationService();
        $this->auditService = new AuditService();
    }

    public function calculateDeadline(string $tanggalAjuan, int $targetJam): string
    {
        return date('Y-m-d H:i:s', strtotime("{$tanggalAjuan} + {$targetJam} hours"));
    }

    public function getSlaInfo(array $wo): array
    {
        // Status 6: Selesai, Status 3: Ditolak
        if ((int)$wo['id_status'] === 6) {
            return [
                'status' => 'completed',
                'badge_class' => 'bg-success',
                'badge_text' => 'Tuntas / Selesai',
                'is_overdue' => false,
                'remaining_seconds' => 0,
                'progress_percent' => 100,
            ];
        }

        if ((int)$wo['id_status'] === 3) {
            return [
                'status' => 'rejected',
                'badge_class' => 'bg-secondary',
                'badge_text' => 'Ditolak',
                'is_overdue' => false,
                'remaining_seconds' => 0,
                'progress_percent' => 0,
            ];
        }

        if (empty($wo['deadline'])) {
            return [
                'status' => 'no_deadline',
                'badge_class' => 'bg-secondary',
                'badge_text' => 'Menunggu Review',
                'is_overdue' => false,
                'remaining_seconds' => 0,
                'progress_percent' => 0,
            ];
        }

        $now = time();
        $deadlineTs = strtotime($wo['deadline']);
        $startTs = strtotime($wo['tanggal_ajuan']);
        $totalSeconds = max(1, $deadlineTs - $startTs);
        $remainingSeconds = $deadlineTs - $now;

        if ($remainingSeconds <= 0 || (int)$wo['id_status'] === 8) {
            $overdueSecs = abs($remainingSeconds);
            $hours = floor($overdueSecs / 3600);
            $mins = floor(($overdueSecs % 3600) / 60);
            $overdueStr = $hours > 0 ? "{$hours}j {$mins}m" : "{$mins}m";

            return [
                'status' => 'overdue',
                'badge_class' => 'bg-danger animate-pulse',
                'badge_text' => "OVERDUE +{$overdueStr}",
                'is_overdue' => true,
                'remaining_seconds' => $remainingSeconds,
                'progress_percent' => 100,
            ];
        }

        $hours = floor($remainingSeconds / 3600);
        $mins = floor(($remainingSeconds % 3600) / 60);
        $timeStr = $hours > 0 ? "{$hours}j {$mins}m" : "{$mins}m";
        $ratio = ($remainingSeconds / $totalSeconds) * 100;

        if ($ratio <= 30) {
            return [
                'status' => 'warning',
                'badge_class' => 'bg-warning text-dark',
                'badge_text' => "Sisa {$timeStr} (Kritis)",
                'is_overdue' => false,
                'remaining_seconds' => $remainingSeconds,
                'progress_percent' => round(100 - $ratio),
            ];
        }

        return [
            'status' => 'safe',
            'badge_class' => 'bg-success',
            'badge_text' => "Sisa {$timeStr}",
            'is_overdue' => false,
            'remaining_seconds' => $remainingSeconds,
            'progress_percent' => round(100 - $ratio),
        ];
    }

    /**
     * Run Auto Escalation Routine:
     * Check active non-completed work orders that exceeded deadline.
     * Escalate to id_status = 8, record audit log, send WhatsApp alerts.
     */
    public function checkAndEscalateOverdue(): array
    {
        $overdueOrders = $this->woModel->getOverdueWorkOrders();
        $userModel = new UserModel();
        $admins = $userModel->where('id_role', 1)->where('status_aktif', 1)->findAll();
        $escalatedList = [];

        foreach ($overdueOrders as $wo) {
            // Update to Escalated (id_status = 8)
            $this->woModel->update($wo['id_wo'], [
                'id_status' => 8
            ]);

            // Audit log
            $this->auditService->log(
                "Auto Escalation: Work Order {$wo['kode_wo']} telah melewati SLA deadline ({$wo['deadline']}) dan otomatis dieskalasi ke status Escalated.",
                'SLA Auto Escalation',
                1 // System/Admin user
            );

            // Fetch Kadep
            $kadep = $userModel->getHeadOfDepartment($wo['id_departemen']);
            if ($kadep) {
                $this->waService->notifyOverdueEscalated($wo, $kadep['no_whatsapp'], $kadep['nama_lengkap']);
                $this->notification->notifyOverdueEscalated($wo, $kadep['no_whatsapp'] ?? '', $kadep['email'] ?? '', $kadep['nama_lengkap'] ?? 'Kadep');
            }

            // Alert Admins
            foreach ($admins as $adm) {
                $this->waService->notifyOverdueEscalated($wo, $adm['no_whatsapp'], $adm['nama_lengkap']);
                $this->notification->notifyOverdueEscalated($wo, $adm['no_whatsapp'] ?? '', $adm['email'] ?? '', $adm['nama_lengkap'] ?? 'Admin');
            }

            $escalatedList[] = $wo['kode_wo'];
        }

        return $escalatedList;
    }
}
