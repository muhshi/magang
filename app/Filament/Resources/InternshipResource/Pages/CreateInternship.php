<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use App\Mail\NotifikasiMagang;
use App\Mail\NotifikasiMasukKeKantor;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\WhatsAppService;


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
            Mail::to('bps3321@bps.go.id')->send(new NotifikasiMasukKeKantor($freshRecord));
        } else {
            // Optional: log jika file belum ada
            // \Log::warning('File belum tersedia untuk email magang', [
            //     'letter' => $freshRecord->letter_file,
            //     'photo' => $freshRecord->photo_file,
            // ]);
        }

        // Kirim WhatsApp ke admin
        // try {
        //     $adminNumber = env('ADMIN_WHATSAPP_NUMBER', '6285399590905'); // pastikan pakai format internasional

        //     WhatsappService::sendInternshipNotification($adminNumber, [
        //         'name' => $freshRecord->full_name,
        //         'email' => $freshRecord->email,
        //         'instansi' => $freshRecord->school_name,
        //         'durasi' => $freshRecord->start_date . ' - ' . $freshRecord->end_date,
        //         'motivasi' => $freshRecord->motivation,
        //         'keterampilan' => $freshRecord->skills,
        //         'foto_url' => $freshRecord->photo_file ? public_path("storage/{$freshRecord->photo_file}") : null,
        //         'dokumen_url' => $freshRecord->letter_file ? public_path("storage/{$freshRecord->letter_file}") : null,
        //         'dokumen_nama' => $freshRecord->letter_file ? basename($freshRecord->letter_file) : null,
        //     ]);
        // } catch (\Exception $e) {
        //     \log::error('Gagal kirim WhatsApp magang: ' . $e->getMessage());
        // }
    }


}
