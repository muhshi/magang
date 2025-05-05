<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use App\Mail\NotifikasiMagang;
use App\Mail\NotifikasiMasukKeKantor;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        $freshRecord = $this->record->fresh(); // pastikan sudah tersimpan

        // Pastikan file sudah ada
        if (
            $freshRecord->letter_file &&
            $freshRecord->photo_file &&
            file_exists(public_path("storage/{$freshRecord->letter_file}")) &&
            file_exists(public_path("storage/{$freshRecord->photo_file}"))
        ) {
            Mail::to('bpsdemak3321@gmail.com')->send(new NotifikasiMasukKeKantor($freshRecord));
        } else {
            // Optional: log jika file belum ada
            \Log::warning('File belum tersedia untuk email magang', [
                'letter' => $freshRecord->letter_file,
                'photo' => $freshRecord->photo_file,
            ]);
        }
    }


}
