<?php

namespace App\Jobs;

use App\Models\Internship; // Pastikan model Internship di-import
use App\Mail\NotifikasiMasukKeKantor; // Import class Mail Anda
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail; // Import Mail Facade

class SendAdminEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Internship $internship;

    /**
     * Create a new job instance.
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
        // Pindahkan logika pengiriman email ke sini
        if (
            $this->internship->letter_file &&
            $this->internship->photo_file &&
            file_exists(public_path("storage/{$this->internship->letter_file}")) &&
            file_exists(public_path("storage/{$this->internship->photo_file}"))
        ) {
            Mail::to('bps3321@bps.go.id')->send(new NotifikasiMasukKeKantor($this->internship));
        } else {
            \Log::warning('File belum tersedia untuk email magang (dari Job)', [
                'internship_id' => $this->internship->id,
            ]);
        }
    }
}
