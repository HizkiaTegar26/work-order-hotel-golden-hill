<?php

namespace App\Libraries;

class WhatsAppService
{
    protected string $provider;
    protected string $apiKey;
    protected string $sender;
    protected string $endpoint;
    protected string $logFile;

    public function __construct()
    {
        $this->provider = getenv('wa.provider') ?: 'simulation';
        $this->apiKey   = getenv('wa.apiKey') ?: '';
        $this->sender   = getenv('wa.sender') ?: '';
        $this->endpoint = getenv('wa.endpoint') ?: '';
        $this->logFile  = WRITEPATH . 'logs/whatsapp_gateway.log';
    }

    public function sendMessage(string $targetPhone, string $message): array
    {
        // Standardize phone number for Indonesian prefix
        $target = preg_replace('/[^0-9]/', '', $targetPhone);
        if (str_starts_with($target, '08')) {
            $target = '628' . substr($target, 2);
        }

        if (empty($target) || strlen($target) < 8) {
            return [
                'status' => false,
                'error'  => 'Nomor WhatsApp tidak valid atau tidak lengkap (minimal 8 digit)',
            ];
        }

        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$this->provider}] To: {$target} | Message: " . str_replace("\n", " ", $message) . PHP_EOL;
        @file_put_contents($this->logFile, $logEntry, FILE_APPEND);

        if ($this->provider === 'fonnte' && !empty($this->apiKey)) {
            return $this->sendViaFonnte($target, $message);
        } elseif ($this->provider === 'wablas' && !empty($this->apiKey)) {
            return $this->sendViaWablas($target, $message);
        }

        // Default: Simulation / Sandbox mode
        return [
            'status'  => true,
            'mode'    => 'simulation',
            'target'  => $target,
            'message' => 'Simulated WhatsApp notification dispatched successfully',
        ];
    }

    protected function sendViaFonnte(string $target, string $message): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => $message,
            ],
            CURLOPT_HTTPHEADER => [
                "Authorization: {$this->apiKey}",
            ],
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ['status' => false, 'error' => $err];
        }

        return ['status' => true, 'response' => json_decode($response, true)];
    }

    protected function sendViaWablas(string $target, string $message): array
    {
        $endpoint = !empty($this->endpoint) ? $this->endpoint : 'https://solo.wablas.com/api/send-message';
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'phone' => $target,
                'message' => $message,
            ]),
            CURLOPT_HTTPHEADER => [
                "Authorization: {$this->apiKey}",
                "Content-Type: application/x-www-form-urlencoded"
            ],
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ['status' => false, 'error' => $err];
        }

        return ['status' => true, 'response' => json_decode($response, true)];
    }

    public function notifyNewWorkOrder(array $wo, array $kadep): array
    {
        $phone = $kadep['no_whatsapp'] ?? '';
        if (empty($phone)) return ['status' => false, 'message' => 'No phone'];

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

        return $this->sendMessage($phone, $msg);
    }

    public function notifyAssignedStaff(array $wo, array $staf): array
    {
        $phone = $staf['no_whatsapp'] ?? '';
        if (empty($phone)) return ['status' => false, 'message' => 'No phone'];

        $msg = "🛠️ *HOTEL GOLDEN HILL - PENUGASAN WORK ORDER*\n\n"
             . "Halo {$staf['nama_lengkap']},\n"
             . "Anda telah ditugaskan untuk menangani Work Order berikut:\n\n"
             . "• *Kode WO:* {$wo['kode_wo']}\n"
             . "• *Judul:* {$wo['judul_wo']}\n"
             . "• *Target SLA:* " . ($wo['tingkat_prioritas'] ?? '-') . " (Deadline: {$wo['deadline']})\n"
             . "• *Deskripsi:* {$wo['deskripsi']}\n\n"
             . "Harap segera menindaklanjuti dan mengunggah bukti pengerjaan.\n"
             . "Terima kasih.";

        return $this->sendMessage($phone, $msg);
    }

    public function notifyRejected(array $wo, array $pengaju, string $reason): array
    {
        $phone = $pengaju['no_whatsapp'] ?? '';
        if (empty($phone)) return ['status' => false, 'message' => 'No phone'];

        $msg = "❌ *HOTEL GOLDEN HILL - WO TIDAK DAPAT DIPROSES*\n\n"
             . "Halo {$pengaju['nama_lengkap']},\n"
             . "Work Order yang Anda ajukan telah ditinjau dengan hasil ditolak:\n\n"
             . "• *Kode WO:* {$wo['kode_wo']}\n"
             . "• *Judul:* {$wo['judul_wo']}\n"
             . "• *Alasan Penolakan:* {$reason}\n\n"
             . "Silakan berkoordinasi dengan Kepala Departemen terkait untuk informasi lebih lanjut.\n"
             . "Terima kasih.";

        return $this->sendMessage($phone, $msg);
    }

    public function notifyOverdueEscalated(array $wo, string $phone, string $recipientName): array
    {
        if (empty($phone)) return ['status' => false, 'message' => 'No phone'];

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

        return $this->sendMessage($phone, $msg);
    }

    public function notifyResetPassword(string $phone, string $nama, string $resetUrl): array
    {
        if (empty($phone)) return ['status' => false, 'message' => 'No phone'];

        $msg = "🔐 *HOTEL GOLDEN HILL - PERMINTAAN RESET PASSWORD*\n\n"
             . "Halo {$nama},\n"
             . "Kami menerima permintaan reset password untuk akun Anda. Silakan klik tautan di bawah ini untuk mengatur kata sandi baru (berlaku 1 jam):\n\n"
             . "{$resetUrl}\n\n"
             . "Jika Anda tidak meminta reset ini, abaikan pesan ini dengan aman.";

        return $this->sendMessage($phone, $msg);
    }
}
