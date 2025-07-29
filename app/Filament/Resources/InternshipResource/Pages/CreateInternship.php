<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendInternshipNotificationJob; // <-- GANTI INI
use App\Jobs\SendAdminEmailJob;             // <-- TAMBAHKAN INI

class CreateInternship extends CreateRecord
{
    protected static string $resource = InternshipResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
        return $data;
    }

    protected function afterCreate(): void
    {
        $freshRecord = $this->record->fresh();

        // Lemparkan tugas ke "Koki" di dapur (Queue)
        // Proses ini sangat cepat, hanya memasukkan data ke tabel 'jobs'

        // 1. Lemparkan Job untuk kirim email
        SendAdminEmailJob::dispatch($freshRecord);

        // 2. Lemparkan Job untuk kirim WhatsApp
        SendInternshipNotificationJob::dispatch($freshRecord->id);

        // Selesai! Halaman akan langsung refresh dan terasa instan.
    }
}
