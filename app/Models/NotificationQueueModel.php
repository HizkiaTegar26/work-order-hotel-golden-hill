<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationQueueModel extends Model
{
    protected $table = 'notification_queue';
    protected $primaryKey = 'id_queue';
    protected $allowedFields = [
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'notification_type',
        'subject',
        'message',
        'channel',
        'status',
        'attempts',
        'max_attempts',
        'failed_reason',
        'dispatched_channel',
        'created_at',
        'processed_at',
        'sent_at',
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Enqueue a new notification for asynchronous dispatch
     */
    public function enqueue(array $data): int|string
    {
        $payload = [
            'recipient_name'    => $data['recipient_name'] ?? 'User',
            'recipient_phone'   => $data['recipient_phone'] ?? null,
            'recipient_email'   => $data['recipient_email'] ?? null,
            'notification_type' => $data['notification_type'] ?? 'general',
            'subject'           => $data['subject'] ?? 'Pemberitahuan Sistem Hotel Golden Hill',
            'message'           => $data['message'] ?? '',
            'channel'           => $data['channel'] ?? 'whatsapp',
            'status'            => 'pending',
            'attempts'          => 0,
            'max_attempts'      => $data['max_attempts'] ?? 3,
            'failed_reason'     => null,
            'dispatched_channel'=> null,
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        return $this->insert($payload);
    }

    /**
     * Get pending items to be processed
     */
    public function getPendingQueue(int $limit = 25): array
    {
        return $this->where('status', 'pending')
                    ->orGroupStart()
                        ->where('status', 'failed')
                        ->where('attempts <', 3)
                    ->groupEnd()
                    ->orderBy('id_queue', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }
}
