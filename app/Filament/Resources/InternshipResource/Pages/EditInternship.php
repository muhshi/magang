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
        // if (in_array($this->record->status, ['accepted', 'rejected'])) {
        //     Mail::to($this->record->email)->send(new NotifikasiMagang($this->record));
        // }
    }
}
