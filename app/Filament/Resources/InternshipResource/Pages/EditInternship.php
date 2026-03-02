<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use App\Mail\NotifikasiMagang;
use App\Models\Schedule;
use App\Settings\SystemSettings;
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
                // Hapus role lama dan assign role baru
                $user->removeRole('Calon Magang');
                $user->assignRole('Magang BPS');

                // 3. Auto-create Schedule (jadwal presensi) jika belum ada
                if (!Schedule::where('user_id', $user->id)->exists()) {
                    $settings = app(SystemSettings::class);

                    Schedule::create([
                        'user_id'    => $user->id,
                        'is_wfa'     => false,
                        'is_banned'  => false,
                    ]);
                }
            }
        }

    }
}

