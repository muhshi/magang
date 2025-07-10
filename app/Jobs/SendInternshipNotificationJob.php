<?php

namespace App\Jobs;

use App\Models\Internship; // Pastikan model Internship di-import
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WhatsAppService;

class SendInternshipNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Kita simpan seluruh data pendaftar
    public Internship $internship;

    /**
     * Terima model Internship, bukan array
     */
    public function __construct(Internship $internship)
    {
        $this->internship = $internship;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminNumbers = explode(',', env('ADMIN_WHATSAPP_NUMBER'));

        // Siapkan data dari model yang kita terima
        $notificationData = [
            'name' => $this->internship->full_name,
            'email' => $this->internship->email,
            'instansi' => $this->internship->school_name,
            'durasi' => $this->internship->start_date . ' - ' . $this->internship->end_date,
            'motivasi' => $this->internship->motivation,
            'keterampilan' => $this->internship->skills,
        ];

        foreach ($adminNumbers as $number) {
            try {
                // Panggil service WA dengan data yang sudah disiapkan
                WhatsAppService::sendInternshipNotification(trim($number), $notificationData);
            } catch (\Exception $e) {
                \Log::error('Job Gagal Kirim WhatsApp: ' . $e->getMessage());
            }
        }
    }
}
