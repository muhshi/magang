<?php

namespace App\Jobs;

use App\Models\Internship; // Pastikan use ini ada
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WhatsAppService;

class SendInternshipNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $internshipId;

    /**
     * Terima ID pendaftar
     */
    public function __construct(int $internshipId)
    {
        $this->internshipId = $internshipId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Cari pendaftar di database menggunakan ID
        $internship = Internship::find($this->internshipId);

        if (!$internship) {
            return; // Hentikan jika data tidak ditemukan
        }

        $adminNumbers = explode(',', env('ADMIN_WHATSAPP_NUMBER'));

        $notificationData = [
            'name' => $internship->full_name,
            'email' => $internship->email,
            'instansi' => $internship->school_name,
            'durasi' => $internship->start_date . ' - ' . $internship->end_date,
            'motivasi' => $internship->motivation,
            'keterampilan' => $internship->skills,
        ];

        foreach ($adminNumbers as $number) {
            // Logika try-catch bisa dihapus dari sini karena Job sudah menanganinya
            WhatsAppService::sendInternshipNotification(trim($number), $notificationData);
        }
    }
}
