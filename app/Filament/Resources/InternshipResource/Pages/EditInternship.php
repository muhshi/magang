<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use App\Mail\NotifikasiMagang;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditInternship extends EditRecord
{
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $pendaftar = $this->record;

        // Kirim email jika statusnya accepted atau rejected
        if ($pendaftar->status === 'accepted' || $pendaftar->status === 'rejected') {
            Mail::to($pendaftar->email)->send(new NotifikasiMagang($pendaftar));
        }

        // 2. LOGIKA BARU: Update role pengguna jika diterima
        if ($pendaftar->status === 'accepted') {
            // Ambil user yang terkait dengan pendaftaran ini
            $user = $pendaftar->user;

            if ($user) {
                // Tambahkan role baru 'Magang BPS'
                $user->assignRole('Magang BPS');
            }
        }

    }
}
